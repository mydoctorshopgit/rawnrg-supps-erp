<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\Cart;
use App\Models\CombinedOrder;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentIntent;
use Stripe\Webhook;

class StripeController extends Controller
{
    public function stripe(Request $request)
    {
        $data['payment_type'] = $request->payment_type;
        $data['combined_order_id'] = $request->combined_order_id;
        $data['amount'] = $request->amount;
        $data['user_id'] = $request->user_id;
        $data['package_id'] = 0;

        if (isset($request->package_id)) {
            $data['package_id'] = $request->package_id;
        }

        return view('frontend.payment.stripe_app', $data);
    }

    public function create_payment_intent(Request $request)
    {
        $request->validate([
            'combined_order_id' => 'required|exists:combined_orders,id',
        ]);

        try {
            $combinedOrder = CombinedOrder::findOrFail($request->combined_order_id);

            // ── Ownership check ───────────────────────────────────────────────
            $user = auth()->user();
            if ($user && $combinedOrder->user_id !== $user->id) {
                return response()->json(['status' => false, 'message' => 'Forbidden'], 403);
            }

            if ($combinedOrder->payment_status === 'paid') {
                return response()->json(['status' => false, 'message' => 'Order already paid'], 400);
            }

            if ($combinedOrder->grand_total < 0.50) {
                $combinedOrder->update(['payment_status' => 'paid']);
                Order::where('combined_order_id', $combinedOrder->id)
                    ->update(['payment_status' => 'paid']);
                Cart::where('user_id', $combinedOrder->user_id)->delete();
                return response()->json(['status' => true, 'client_secret' => null, 'message' => 'Order is free, marked as paid']);
            }

            $amount = (int) round($combinedOrder->grand_total * 100);

            Stripe::setApiKey(config('services.stripe.secret'));

            // ── Reuse existing PaymentIntent if not yet paid ──────────────────
            if ($combinedOrder->stripe_payment_intent_id) {
                try {
                    $existing = PaymentIntent::retrieve($combinedOrder->stripe_payment_intent_id);
                    if (in_array($existing->status, ['requires_payment_method', 'requires_confirmation', 'requires_action'])) {
                        return response()->json(['status' => true, 'client_secret' => $existing->client_secret]);
                    }
                } catch (\Exception $e) {
                    // Could not retrieve existing, fall through to create new one
                }
            }

            $paymentIntent = PaymentIntent::create([
                'amount'   => $amount,
                'currency' => 'GBP',
                'automatic_payment_methods' => ['enabled' => true],
                'metadata' => [
                    'combined_order_id' => $combinedOrder->id,
                    'user_id'           => $combinedOrder->user_id,
                ],
            ]);

            $combinedOrder->update(['stripe_payment_intent_id' => $paymentIntent->id]);

            return response()->json(['status' => true, 'client_secret' => $paymentIntent->client_secret]);

        } catch (\Exception $e) {
            Log::error('PaymentIntent Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to create payment intent'], 500);
        }
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // ── PAYMENT SUCCESS ───────────────────────────────────────────────────────
        if ($event->type === 'payment_intent.succeeded') {
            $intent        = $event->data->object;
            $combinedOrder = CombinedOrder::where('stripe_payment_intent_id', $intent->id)->first();

            if ($combinedOrder && $combinedOrder->payment_status !== 'paid') {
                DB::beginTransaction();
                try {
                    $payment = [
                        'status'        => 'Success',
                        'transactionId' => $intent->id,
                        'chargeId'      => $intent->latest_charge ?? null,
                    ];

                    checkout_done($combinedOrder->id, json_encode($payment));

                    DB::table('combined_orders')
                        ->where('id', $combinedOrder->id)
                        ->update(['payment_status' => 'paid']);

                    if ($combinedOrder->user_id) {
                        Cart::where('user_id', $combinedOrder->user_id)->delete();
                    } elseif ($combinedOrder->guest_id) {
                        Cart::where('temp_user_id', $combinedOrder->guest_id)->delete();
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Webhook payment_intent.succeeded error: ' . $e->getMessage());
                    return response()->json(['error' => 'Webhook processing failed'], 500);
                }
            }
        }

        // ── PAYMENT FAILED / CANCELED ─────────────────────────────────────────────
        if (in_array($event->type, ['payment_intent.payment_failed', 'payment_intent.canceled'])) {
            $intent        = $event->data->object;
            $combinedOrder = CombinedOrder::where('stripe_payment_intent_id', $intent->id)->first();

            if ($combinedOrder && $combinedOrder->payment_status !== 'paid') {
                DB::beginTransaction();
                try {
                    Order::where('combined_order_id', $combinedOrder->id)
                        ->update(['payment_status' => 'failed']);

                    DB::table('combined_orders')
                        ->where('id', $combinedOrder->id)
                        ->update(['payment_status' => 'failed']);

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Webhook payment failed error: ' . $e->getMessage());
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    public function payment_success(Request $request)
    {
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        try {
            $input = trim((string) $request->payment_intent_id);

            // Client may send either the full client_secret (pi_xxx_secret_xxx)
            // or just the PaymentIntent ID (pi_xxx) — extract the ID either way
            $paymentIntentId = str_contains($input, '_secret_')
                ? explode('_secret_', $input)[0]
                : $input;

            $intent = $stripe->paymentIntents->retrieve($paymentIntentId);

            if ($intent->status !== 'succeeded') {
                return response()->json(['result' => false, 'message' => 'Payment not completed']);
            }

            $metadata = $intent->metadata;

            $chargeId = $intent->latest_charge
                ?? ($intent->charges->data[0]->id ?? null);

            $payment = [
                'status'        => 'Success',
                'transactionId' => $intent->id,
                'chargeId'      => $chargeId,
            ];

            // ── Handle cart payment via metadata.combined_order_id ────────────
            $combinedOrderId = $metadata->combined_order_id ?? null;

            if ($combinedOrderId) {
                $combinedOrder = CombinedOrder::find($combinedOrderId);

                if ($combinedOrder && $combinedOrder->payment_status !== 'paid') {
                    checkout_done($combinedOrderId, json_encode($payment));

                    DB::table('combined_orders')
                        ->where('id', $combinedOrder->id)
                        ->update(['payment_status' => 'paid']);

                    if ($combinedOrder->user_id) {
                        Cart::where('user_id', $combinedOrder->user_id)->delete();
                    } elseif ($combinedOrder->guest_id) {
                        Cart::where('temp_user_id', $combinedOrder->guest_id)->delete();
                    }
                }

                return response()->json(['result' => true, 'message' => 'Payment successful']);
            }

            // ── Fallback: legacy payment_type routing ─────────────────────────
            if (($metadata->payment_type ?? '') === 'cart_payment') {
                checkout_done($metadata->combined_order_id, json_encode($payment));
            } elseif (($metadata->payment_type ?? '') === 'wallet_payment') {
                wallet_payment_done($metadata->user_id, $metadata->amount, 'Stripe', json_encode($payment));
            } elseif (($metadata->payment_type ?? '') === 'seller_package_payment') {
                seller_purchase_payment_done($metadata->user_id, $metadata->package_id, $metadata->amount, 'Stripe', json_encode($payment));
            } elseif (($metadata->payment_type ?? '') === 'customer_package_payment') {
                customer_purchase_payment_done($metadata->user_id, $metadata->package_id);
            }

            return response()->json(['result' => true, 'message' => 'Payment successful']);

        } catch (\Exception $e) {
            Log::error('payment_success error: ' . $e->getMessage());
            return response()->json(['result' => false, 'message' => 'Error verifying payment'], 500);
        }
    }

    public function cancel(Request $request)
    {
        try {
            $input = trim((string) $request->payment_intent_id);

            $paymentIntentId = str_contains($input, '_secret_')
                ? explode('_secret_', $input)[0]
                : $input;

            if ($paymentIntentId) {
                $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
                $intent = $stripe->paymentIntents->retrieve($paymentIntentId);

                $metadata = $intent->metadata ?? [];

                if (isset($metadata['combined_order_id'])) {
                    $order = Order::where('combined_order_id', $metadata['combined_order_id'])->first();

                    if ($order) {
                        $order->payment_status = 'unpaid';
                        $order->save();
                    }
                }
            }

            return response()->json([
                'result'  => true,
                'message' => 'Payment was cancelled.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'Could not handle payment cancellation.',
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
