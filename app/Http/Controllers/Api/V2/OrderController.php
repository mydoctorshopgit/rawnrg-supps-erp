<?php

namespace App\Http\Controllers\Api\V2;

use DB;
use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Models\CombinedOrder;
use App\Models\CreditDelivery;
use App\Models\Accounts;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Mail\NewOrder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $isGuest = !$request->filled('temp_user_id') ? false : !auth()->check();

        if (!auth()->check()) {
            $registeredUserExists = User::where('email', $request->input('email'))
                ->where('user_type', 'customer')
                ->exists();

            if ($registeredUserExists) {
                return response()->json([
                    'result' => false,
                    'message' => 'An account with this email already exists. Please log in to continue.'
                ], 422);
            }
        }

        $guestId = User::where('email', $request->input('email'))
            ->where('user_type', 'customer_guest')
            ->value('id');

        // ── Auth: always use the authenticated user, never trust user_id from body ──
        $user = auth()->user();
        if (!$user && !$request->filled('temp_user_id')) {
            return response()->json(['result' => false, 'message' => 'Unauthenticated'], 401);
        }

        $rules = ['payment_type' => 'required|string'];

        if (!$user) {
            // Guest
            $rules = array_merge($rules, [
                'temp_user_id'   => 'required|string',
                'first_name'     => 'required|string|max:100',
                'last_name'      => 'required|string|max:100',
                'email'          => 'required|email|max:200',
                'phone'          => 'required|string|max:30',
                'address_line_1' => 'required|string|max:255',
                'city'           => 'required|string|max:100',
                'post_code'      => 'required|string|max:20',
                'country'        => 'required|string|max:100',
            ]);
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['result' => false, 'message' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();

        try {
            // ── Cart ──────────────────────────────────────────────────────────────
            $cartQuery = Cart::with(['product.stocks', 'product.taxes', 'product.user.seller']);

            if ($user) {
                $cartQuery->where('user_id', $user->id);
            } else {
                $cartQuery->where('temp_user_id', $request->temp_user_id);
            }

            $cartItems = $cartQuery->get();

            if ($cartItems->isEmpty()) {
                DB::rollBack();
                return response()->json(['result' => false, 'message' => 'Cart is empty'], 422);
            }

            // ── Validate stock availability before creating anything ───────────────
            foreach ($cartItems as $cartItem) {
                $stock = $cartItem->product->stocks
                    ->where('sku', $cartItem->sku)
                    ->first()
                    ?? $cartItem->product->stocks
                    ->where('pip_code', $cartItem->sku)
                    ->first();

                if (!$stock || $stock->qty < $cartItem->quantity) {
                    DB::rollBack();
                    return response()->json([
                        'result'  => false,
                        'message' => "Insufficient stock for: {$cartItem->product->name} (SKU: {$cartItem->sku})"
                    ], 422);
                }
            }

            // ── Shipping address ──────────────────────────────────────────────────
            if (!$user) {
                // Guest — capture all address fields from the form (matching the UI)
                $shippingAddress = [
                    'name'        => trim($request->first_name . ' ' . $request->last_name),
                    'email'       => $request->email,
                    'phone'       => $request->phone,
                    'address1'    => $request->address_line_1,
                    'address2'    => $request->address_line_2 ?? null,
                    'address3'    => $request->address_line_3 ?? null,
                    'town'        => $request->town         ?? null,
                    'city'        => $request->city,
                    'county'      => $request->county        ?? null,
                    'post_code'   => $request->post_code,
                    'country'     => $request->country,
                ];

                if (!$guestId) {
                    $guestStore = $this->guestStore($request);

                    if ($guestStore['status'] === false) {
                        DB::rollBack();
                        return response()->json([
                            'result'  => false,
                            'message' => "Invalid Guest Store"
                        ], 400);
                    } else {
                        $guestId = $guestStore['user']->id;
                    }
                }
            } else {
                // Use the specific address_id from the request if provided,
                // otherwise fall back to the user's first address
                $addressId = $request->input('address_id');
                $address = $addressId
                    ? CreditDelivery::where('id', $addressId)
                    ->where('credit_id', $user->id) // security: ensure it belongs to this user
                    ->first()
                    : null;

                // Final fallback: first address on file
                if (!$address) {
                    $address = CreditDelivery::where('credit_id', $user->id)->first();
                }

                $shippingAddress = $this->prepareShippingAddress($user, $address);
            }

            // ── Combined order ────────────────────────────────────────────────────
            $combinedOrder = CombinedOrder::create([
                'user_id'          => $user?->id,
                'guest_id'         => $user ? null : $guestId,
                'shipping_address' => json_encode($shippingAddress),
                'grand_total'      => 0,
                'payment_status'   => 'pending',
            ]);

            $groupedItems       = $cartItems->groupBy(fn($item) => $item->product->user_id);
            $totalCombinedGrand = 0;

            foreach ($groupedItems as $sellerId => $items) {

                $order = $this->createOrder($combinedOrder, $user, $guestId, $request);

                $totals = ['subtotal' => 0, 'tax' => 0, 'discount' => 0, 'shipping' => 0];

                foreach ($items as $cartItem) {
                    $product  = $cartItem->product;
                    $qty      = (int) $cartItem->quantity;

                    // Unit price and tax (without quantity)
                    $unitPrice = (float) cart_product_price($cartItem, $product, false, false);
                    $unitTax   = (float) cart_product_tax($cartItem, $product, false);

                    $linePrice    = $unitPrice * $qty;
                    $lineTax      = $unitTax * $qty;
                    $lineDiscount = (float) $cartItem->discount;
                    $lineShipping = (float) $cartItem->shipping_cost;

                    $totals['subtotal'] += $linePrice;
                    $totals['tax']      += $lineTax;
                    $totals['discount'] += $lineDiscount;
                    $totals['shipping'] += $lineShipping;

                    OrderDetail::create([
                        'order_id'      => $order->id,
                        'seller_id'     => $sellerId,
                        'product_id'    => $product->id,
                        'variation'     => $cartItem->variation,
                        'price'         => $linePrice,   // net line price, no deductions
                        'tax'           => $lineTax,
                        'sku'           => $cartItem->sku,
                        'shipping_type' => $cartItem->shipping_type,
                        'shipping_cost' => $lineShipping,
                        'quantity'      => $qty,
                        'discount'      => $lineDiscount,
                    ]);

                    // ── Decrement stock qty ───────────────────────────────────────
                    ProductStock::where('product_id', $product->id)
                        ->where(function ($q) use ($cartItem) {
                            $q->where('sku', $cartItem->sku)
                                ->orWhere('pip_code', $cartItem->sku);
                        })
                        ->decrement('qty', $qty);

                    $product->increment('num_of_sale', $qty);
                }

                // ── Shipping cost: free over £49, else £7.99 ──────────────────────
                $freeShippingThreshold = (float) (config('business.free_shipping_threshold', 49.00));
                $shippingCost = ($totals['subtotal'] >= $freeShippingThreshold) ? 0.00 : 7.99;

                $grandTotal = ($totals['subtotal'] + $shippingCost + $totals['tax']) - $totals['discount'];

                $order->update([
                    'grand_total'     => $grandTotal,
                    'total_tax'       => $totals['tax'],
                    'coupon_discount' => $totals['discount'],
                    'seller_id'       => $sellerId,
                    'payment_status'  => 'unpaid',
                    'shipping_cost'   => $shippingCost,
                ]);

                $totalCombinedGrand += $grandTotal;

                // ── Accounts ledger entry (registered users only) ─────────────────
                if ($user) {
                    Accounts::create([
                        'order_id'           => $order->id,
                        'customer_detail_id' => $user->id,
                        'debit'              => $grandTotal,
                        'credit'             => 0,
                        'due_date'           => now()->addDays(40),
                    ]);
                }
            }

            $combinedOrder->update(['grand_total' => $totalCombinedGrand]);

            DB::commit();

            // ── Clear cart ────────────────────────────────────────────────────────
            if ($user) {
                Cart::where('user_id', $user->id)->delete();
            } else {
                Cart::where('temp_user_id', $request->temp_user_id)->delete();
            }

            // ── Determine country for routing logic ───────────────────────────────
            $country      = $shippingAddress['country'] ?? '';
            $isNonUk      = $country !== 'United Kingdom';          // applies to both guests and users
            $isCreditUser = $user && $user->user_type === 'credit_customer';
            $skipPayment  = $isNonUk || $isCreditUser;

            // ── Set status = 20 for all non-UK orders ─────────────────────────────
            if ($isNonUk) {
                Order::where('combined_order_id', $combinedOrder->id)
                    ->update(['status' => 20]);
            }

            // ── Resolve the email recipient ───────────────────────────────────────
            $recipientEmail = $user ? $user->email : ($shippingAddress['email'] ?? null);
            $recipientName  = $user ? $user->name  : ($shippingAddress['name']  ?? 'Customer');

            if ($skipPayment) {
                // Non-UK or credit customer: send "order received" email
                if (env('MAIL_USERNAME') != null && $recipientEmail) {
                    try {
                        $emailOrder = $combinedOrder->orders()
                            ->with(['orderDetails.product.thumbnail'])
                            ->first();

                        if ($emailOrder) {
                            Mail::to($recipientEmail)->send(new NewOrder([
                                'view'    => 'emails.order_process_mail',
                                'subject' => 'Order Received - ' . $combinedOrder->id,
                                'from'    => env('MAIL_USERNAME'),
                                'order'   => $emailOrder,
                            ]));
                        }
                    } catch (\Exception $e) {
                        Log::error('Order Confirmation Email Failed: ' . $e->getMessage());
                    }
                }

                return response()->json([
                    'combined_order_id'     => $combinedOrder->id,
                    'grand_total'           => single_price($totalCombinedGrand),
                    'grand_total_formatted' => single_price($totalCombinedGrand),
                    'result'                => true,
                    'payment_required'      => false,
                    'message'               => 'Order received. A confirmation email has been sent. Payment will be processed after ERP confirmation.',
                ]);
            }

            // ── UK order: send confirmation email and proceed to payment ──────────
            if (env('MAIL_USERNAME') != null && $recipientEmail) {
                try {
                    $emailOrder = $combinedOrder->orders()
                        ->with([
                            'orderDetails.product.thumbnail',
                            'customer.creditDelivery.countries',
                            'customer.creditDelivery',
                        ])
                        ->first();

                    if ($emailOrder) {
                        Mail::to($recipientEmail)->send(new NewOrder([
                            'view'    => 'emails.order_confirmation',
                            'subject' => 'Your order has been placed - ' . $combinedOrder->id,
                            'from'    => env('MAIL_USERNAME'),
                            'order'   => $emailOrder,
                        ]));
                    }
                } catch (\Exception $e) {
                    Log::error('Order Email Failed: ' . $e->getMessage());
                }
            }

            return response()->json([
                'combined_order_id'     => $combinedOrder->id,
                'grand_total'           => single_price($totalCombinedGrand),
                'grand_total_formatted' => single_price($totalCombinedGrand),
                'result'                => true,
                'payment_required'      => true,
                'message'               => 'Order created successfully. Proceed to payment.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Store Failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['result' => false, 'message' => 'Something went wrong'], 500);
        }
    }

    private function guestStore(Request $request)
    {
        try {
            $password = 'guest_' . $request->input('email');

            $user = User::create([
                'name'           => $request->input('first_name'),
                'last_name'      => $request->input('last_name'),
                'email'          => $request->input('email'),
                'password'       => Hash::make($password),
                'user_from'      => 'website',
                'user_type'      => 'customer_guest',
                'mobile_number'  => $request->input('phone'),
            ]);

            return [
                'status' => true,
                'user' => $user
            ];
        } catch (\Throwable $e) {
            Log::error('Guest Store Error: ' . $e->getMessage());
            return [
                'status' => false,
            ];
        }
    }


    private function prepareShippingAddress($user, $address): array
    {
        return [
            'name'        => $user->name . ($user->last_name ? ' ' . $user->last_name : ''),
            'email'       => $user->email,
            'phone'       => $user->phone ?? null,
            'address1'    => $address->address1   ?? null,
            'address2'    => $address->address2   ?? null,
            'address3'    => $address->address3   ?? null,
            'town'        => $address->town        ?? null,
            'city'        => $address->city        ?? null,
            'county'      => $address->county      ?? null,
            'post_code'   => $address->post_code   ?? null,
            'country'     => $address->country     ?? null,
            'lat_lang'    => ($address && $address->latitude && $address->longitude)
                ? $address->latitude . ',' . $address->longitude
                : null,
        ];
    }

    private function createOrder($combinedOrder, $user, $guestId, $request): Order
    {
        // Use combined_order id for uniqueness — no race condition
        $invoiceNumber = 'MDS-' . str_pad($combinedOrder->id, 6, '0', STR_PAD_LEFT);

        return Order::create([
            'combined_order_id' => $combinedOrder->id,
            'user_id'           => $user?->id,
            'guest_id'          => $user ? null : $guestId,
            'shipping_address'  => $combinedOrder->shipping_address,
            'order_from'        => 'app',
            'payment_type'      => $request->payment_type,
            'code'              => now()->format('Ymd-His') . '-' . $combinedOrder->id,
            'date'              => now()->timestamp,
            'payment_status'    => 'unpaid',
            'invoice_number'    => $invoiceNumber,
        ]);
    }

    public function getOrders(Request $request, $type)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['result' => false, 'message' => 'Unauthenticated'], 401);
        }

        $statuses = $this->getStatusesByType($type);
        if (!$statuses) {
            return response()->json(['result' => false, 'message' => 'Invalid order type'], 400);
        }

        $orders = Order::where('user_id', $user->id)
            ->whereIn('status', $statuses)
            ->select('id', 'invoice_number', 'grand_total', 'status', 'created_at', 'payment_status')
            ->latest()
            ->paginate(10);

        return response()->json([
            'result' => true,
            'orders' => $orders->through(fn($order) => [
                'id'             => $order->id,
                'date'           => $order->created_at->toDateTimeString(),
                'invoice_number' => $order->invoice_number,
                'price'          => single_price($order->grand_total),
                'payment_status' => $order->payment_status,
                'status'         => $this->getStatusLabel($order->status),
            ]),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
                'total'        => $orders->total(),
            ],
        ]);
    }

    public function viewOrder(Request $request, $id)
    {
        $user = $request->user();

        $order = Order::with([
            'orderDetails.product.thumbnail',
            'orderDetails.product.taxes',
            'orderDetails.product.stocks',
            'user',
            'combined_order',
        ])->find($id);

        if (!$order) {
            return response()->json(['result' => false, 'message' => 'Order not found'], 404);
        }

        if ($user && $order->user_id !== $user->id) {
            return response()->json(['result' => false, 'message' => 'Forbidden'], 403);
        }

        $shipping = $order->shipping_address
            ? (is_array(json_decode($order->shipping_address)) ? (object)[] : json_decode($order->shipping_address))
            : (object)[];

        $subtotal = $order->orderDetails->sum('price');

        return response()->json([
            'result' => true,
            'order'  => [
                'id'             => $order->id,
                'code'           => $order->code,
                'invoice_number' => $order->invoice_number,
                'date'           => date('d M Y', $order->date),
                'status'         => $this->getStatusLabel($order->status),
                'payment_status' => $order->payment_status,
                'payment_type'   => $order->payment_type,

                'shipping_address' => [
                    'name'        => $shipping->name      ?? null,
                    'email'       => $shipping->email     ?? null,
                    'phone'       => $shipping->phone     ?? null,
                    'address1'    => $shipping->address1  ?? null,
                    'address2'    => $shipping->address2  ?? null,
                    'address3'    => $shipping->address3  ?? null,
                    'town'        => $shipping->town      ?? null,
                    'city'        => $shipping->city      ?? null,
                    'county'      => $shipping->county    ?? null,
                    'post_code'   => $shipping->post_code ?? null,
                    'country'     => $shipping->country   ?? null,
                ],

                'pricing' => [
                    'subtotal'        => single_price($subtotal),
                    'shipping_cost'   => single_price($order->shipping_cost ?? 0),
                    'total_tax'       => single_price($order->total_tax ?? 0),
                    'coupon_discount' => single_price($order->coupon_discount ?? 0),
                    'grand_total'     => single_price($order->grand_total),
                ],

                'items' => $order->orderDetails->map(function ($detail) {
                    $product   = $detail->product;
                    $unitPrice = $detail->quantity > 0
                        ? round($detail->price / $detail->quantity, 2)
                        : $detail->price;

                    // Find the matching stock by SKU or pip_code to get size/color/flavour
                    $stock = $product
                        ? ($product->stocks->firstWhere('sku', $detail->sku)
                            ?? $product->stocks->firstWhere('pip_code', $detail->sku))
                        : null;

                    // Strip inch/quote marks from variant display values
                    $cleanVal = fn(?string $v): ?string => $v === null ? null
                        : trim(str_replace(['"', "'", "\u{201C}", "\u{201D}", "\u{2018}", "\u{2019}"], '', $v));

                    return [
                        'product_id'   => $detail->product_id,
                        'product_name' => $product->name ?? 'N/A',
                        'product_code' => $detail->sku ?? $product->product_code ?? 'N/A',
                        'pip_code'     => $stock->pip_code ?? $product->pip_code ?? 'N/A',
                        'variation'    => $detail->variation,
                        'size'         => $cleanVal($stock->variant ?? null),
                        'color'        => $cleanVal($stock->color   ?? null),
                        'flavour'      => $cleanVal($stock->flavour ?? null),
                        'pack_qty'     => $stock->pack_qty ?? null,
                        'image'        => $product ? uploaded_asset($product->thumbnail_img) : null,
                        'unit_price'   => single_price($unitPrice),
                        'quantity'     => $detail->quantity,
                        'tax'          => single_price($detail->tax),
                        'line_total'   => single_price($detail->price),
                    ];
                }),

                'invoice_download_url' => route('api.invoice.download', $order->id),
            ],
        ]);
    }

    public function invoiceDownload(Request $request, $id)
    {
        $user  = $request->user();
        $order = Order::with([
            'orderDetails.product',
            'user',
        ])->find($id);

        if (!$order) {
            return response()->json(['result' => false, 'message' => 'Order not found'], 404);
        }

        if ($user && $order->user_id !== $user->id) {
            return response()->json(['result' => false, 'message' => 'Forbidden'], 403);
        }

        try {
            $pdf = \PDF::loadView('backend.invoices.invoice', [
                'order'          => $order,
                'font_family'    => "'Roboto','sans-serif'",
                'direction'      => 'ltr',
                'text_align'     => 'left',
                'not_text_align' => 'right',
            ]);

            return $pdf->download('invoice-' . $order->invoice_number . '.pdf');
        } catch (\Exception $e) {
            Log::error('Invoice Download Failed: ' . $e->getMessage());
            return response()->json(['result' => false, 'message' => 'Could not generate invoice'], 500);
        }
    }

    private function getStatusesByType($type): ?array
    {
        return match ($type) {
            'new'       => ['0', '1', '2', '3', '20'],
            'delivered' => ['4'],
            'cancel'    => ['10'],
            default     => null,
        };
    }

    private function getStatusLabel($status): string
    {
        return match ((int) $status) {
            0  => 'Pending',
            1  => 'New Order',
            2  => 'Fulfillment',
            3  => 'Shipment Confirmation',
            4  => 'Delivered',
            10 => 'Cancelled',
            20 => 'International Order',
            default => 'Unknown',
        };
    }
}
