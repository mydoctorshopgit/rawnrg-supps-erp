<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Order;
use App\Utility\CartUtility;
use Illuminate\Http\Request;
use App\Models\CreditDelivery;
use App\Models\ProductStock;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{

    public function payment_order(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string'
        ]);

        try {
            $payload = json_decode(Crypt::decryptString($validated['token']), true);

            if (
                !is_array($payload) ||
                !isset($payload['user_id'], $payload['order_id'])
            ) {
                return response()->json([
                    'success'  => false,
                    'message' => 'Invalid token.',
                ], 400);
            }

            $user_id = $payload['user_id'] ?? null;
            $order_id = $payload['order_id'] ?? null;

            // ✅ Decode user & order IDs
            // $user_id = base64_decode($request->input('user_id'));
            // $order_id = base64_decode($request->input('order_id'));

            // ✅ Fetch order with relationships
            $order = Order::where('id', $order_id)
                ->where(function ($query) use ($user_id) {
                    $query->where('user_id', $user_id)
                        ->orWhere('guest_id', $user_id);
                })
                ->with(['orderDetails.product', 'combined_order'])
                ->first();

            // ❌ Return if order not found
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found.'
                ]);
            }

            // ✅ Decode payment and check if already paid
            $payment = json_decode($order->payment_details);
            if (
                $payment
                && isset($payment->status, $payment->transactionId, $payment->chargeId)
                && $payment->status === 'Success'
                && !empty($payment->transactionId)
                && !empty($payment->chargeId)
            ) {
                return response()->json([
                    'success' => false,
                    'is_paid' => true,
                    'message' => 'Your order is already paid.'
                ]);
            }

            // ✅ Prepare products array and subtotal
            $subtotal = 0;
            $products = $order->orderDetails->map(function ($detail) use (&$subtotal) {
                $product = $detail->product;

                $subtotal += $detail->price;

                $productStock = ProductStock::where('product_id', $product->id)
                    ->where('sku', $detail->sku)
                    ->select(['id', 'flavour', 'color', 'sku', 'pack_qty'])
                    ->first();

                return [
                    'product_name' => $product->name ?? '',
                    'product_code' => $product->code ?? '',
                    'pip_code'     => $product->pip_code ?? '',
                    'image'        => uploaded_asset($product->thumbnail_img),
                    'price'        => ($detail->price / $detail->quantity),
                    'total_price'  => $detail->price,
                    'quantity'     => $detail->quantity,
                    'flavour'      => $productStock?->flavour,
                    'color'        => $productStock?->color,
                    'sku'          => $productStock?->sku,
                    'pack_qty'     => $productStock?->pack_qty,
                    'varient'     => $productStock?->variant,
                ];
            })->toArray();

            // ✅ Calculate shipping cost
            $shipping_cost = $order->orderDetails->sum('shipping_cost');

            // ✅ Return JSON response
            return response()->json([
                'success'             => true,
                'user_id'             => $user_id,
                'combined_order_id'   => $order->combined_order->id ?? '',
                'order_id'            => $order_id,
                'products'            => $products,
                'grand_total'         => $order->grand_total,
                'total_tax'           => $order->total_tax,
                'coupon_discount'     => $order->coupon_discount,
                'subtotal'            => $subtotal,
                'shipping_cost'       => $shipping_cost,
            ]);
        } catch (DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token'
            ], 400);
        }
    }


    public function summary(Request $request)
    {
        $user_id = $request->input('user_id');
        $temp_user_id = $request->input('temp_user_id');

        // ✅ Resolve column + value once
        $column = $user_id ? 'user_id' : 'temp_user_id';
        $value  = $user_id ?? $temp_user_id;

        // ✅ Single query
        $items = Cart::where($column, $value)->get();

        // ✅ Early return if empty
        if ($items->isEmpty()) {
            return response()->json([
                'sub_total' => format_price(0.00),
                'tax' => format_price(0.00),
                'shipping_cost' => format_price(0.00),
                'discount' => format_price(0.00),
                'grand_total' => format_price(0.00),
                'grand_total_value' => 0.00,
                'coupon_code' => "",
                'coupon_applied' => false,
            ]);
        }

        $subtotal = 0.00;
        $tax = 0.00;
        $discount = 0;

        // ✅ Avoid repeated Product::find() calls (performance improvement)
        $products = Product::whereIn('id', $items->pluck('product_id'))
            ->with('stocks')
            ->get()
            ->keyBy('id');

        foreach ($items as $cartItem) {
            $product = $products[$cartItem->product_id] ?? null;

            if ($product) {
                $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem->quantity;
                $tax += cart_product_tax($cartItem, $product, false) * $cartItem->quantity;
                $discount += ($product->discount_type === 'amount') ? ($product->discount * $cartItem->quantity) : (($product->unit_price * $product->discount) / 100 * $cartItem->quantity);
            }
        }

        // ✅ Shipping logic (unchanged)
        $shipping_cost = $subtotal > 49.00 ? 0 : 7.99;

        // ✅ Address lookup (optimized)
        $address = CreditDelivery::where('credit_id', $value)->first();

        if ($address && $address->country != 'United Kingdom') {
            $tax_cost = 0;
            $shipping_cost = 0;
        } else {
            $tax_cost = $tax;
        }

        // ✅ Discount calculation
        // $discount = $items->sum('discount');

        // ✅ Final total
        // $sum = ($subtotal + $tax_cost + $shipping_cost) - $discount;
        $sum = ($subtotal + $tax_cost + $shipping_cost);

        // ✅ First item safely
        $firstItem = $items->first();

        return response()->json([
            'sub_total' => single_price($subtotal),
            'tax' => single_price($tax_cost),
            'shipping_cost' => single_price($shipping_cost),
            'discount' => single_price($discount),
            'grand_total' => single_price($sum),
            'grand_total_value' => single_price($sum),
            'coupon_code' => $firstItem->coupon_code ?? "",
            'coupon_applied' => ($firstItem->coupon_applied ?? 0) == 1,
        ]);
    }

    public function count(Request $request)
    {
        $user_id = $request->input('user_id');
        $temp_user_id = $request->input('temp_user_id');

        if ($user_id) {
            $items = Cart::where('user_id', $user_id)->get();
        } else {
            $items = Cart::where('temp_user_id', $temp_user_id)->get();
        }

        return response()->json([
            'count' => sizeof($items),
            'status' => true,
        ]);
    }

    public function getList(Request $request)
    {
        $user_id = $request->input('user_id');
        $temp_user_id = $request->input('temp_user_id');

        // ✅ Resolve condition once
        $column = $user_id ? 'user_id' : 'temp_user_id';
        $value  = $user_id ?? $temp_user_id;

        // ✅ Get all cart items in ONE query
        $cartItems = Cart::where($column, $value)->get();

        // ✅ Extract owner IDs
        $owner_ids = $cartItems->pluck('owner_id')->unique()->values()->toArray();

        $currency_symbol = currency_symbol();
        $shops = [];
        $sub_total = 0.00;
        $grand_total = 0.00;

        if (!empty($owner_ids)) {

            // ✅ Preload products
            $products = Product::whereIn('id', $cartItems->pluck('product_id'))
                ->with('stocks')
                ->get()
                ->keyBy('id');

            // ✅ Preload stocks — keyed by both sku and pip_code for flexible lookup
            $stocksRaw = \App\Models\ProductStock::whereIn('product_id', $cartItems->pluck('product_id'))
                ->get();

            // Build lookup map: key = "product_id_code" where code is sku OR pip_code
            $stocks = collect();
            foreach ($stocksRaw as $s) {
                $skuKey     = $s->product_id . '_' . $s->sku;
                $pipKey     = $s->product_id . '_' . $s->pip_code;
                if (!$stocks->has($skuKey))  $stocks->put($skuKey, $s);
                if (!$stocks->has($pipKey))  $stocks->put($pipKey, $s);
            }

            // ✅ Preload shops
            $shopsData = Shop::whereIn('user_id', $owner_ids)
                ->get()
                ->keyBy('user_id');

            foreach ($owner_ids as $owner_id) {

                $shop = [];
                $shop_items_data = [];

                // ✅ Filter items instead of querying again
                $shop_items = $cartItems->where('owner_id', $owner_id);

                foreach ($shop_items as $item) {

                    $product = $products[$item->product_id] ?? null;
                    $stock = $stocks->get($item->product_id . '_' . $item->sku)
                        ?? $stocks->get($item->product_id . '_' . $item->sku); // already covers pip_code keys

                    if (!$product) continue;

                    $price = cart_product_price($item, $product, false, false) * intval($item->quantity);
                    $tax = cart_product_tax($item, $product, false) * intval($item->quantity);
                    $unit_price = cart_product_price($item, $product, false, false);

                    $shop_items_data_item = [
                        "id" => (int) $item->id,
                        "owner_id" => (int) $item->owner_id,
                        "user_id" => (int) $item->user_id,
                        "product_id" => (int) $item->product_id,
                        "product_name" => $product->getTranslation('name'),
                        "pip_code" => $product->pip_code,
                        "pharmaceutical_product" => $product->pharmaceutical_product == "1" ? 'true' : 'false',
                        "auction_product" => $product->auction_product,
                        "product_thumbnail_image" => uploaded_asset($product->thumbnail_img),
                        "variation" => $item->variation,
                        "color" => $stock?->color,
                        "flavour" => $stock?->flavour,
                        "size" => $stock?->variant,
                        "pack_qty" => $stock->pack_qty,
                        "product_code" => !empty($item->sku) ? $item->sku : $product->product_code,
                        "price" => single_price($price),
                        "single_product_price" => single_price($unit_price),
                        "currency_symbol" => $currency_symbol,
                        "tax" => single_price($tax),
                        "shipping_cost" => (float) $item->shipping_cost,
                        "quantity" => (int) $item->quantity,
                        "lower_limit" => (int) $product->min_qty,
                        "upper_limit" =>
                        (int) optional(
                            $product->stocks->where('variant', $item->variation)->first()
                        )->qty
                            ?? (int) optional(
                                $product->stocks->first()
                            )->qty,
                    ];

                    $sub_total += $price;
                    $shop_items_data[] = $shop_items_data_item;
                }

                $grand_total += $sub_total;

                $shop_data = $shopsData[$owner_id] ?? null;

                $shop = [
                    'name' => $shop_data ? translate($shop_data->name) : translate("Inhouse"),
                    'owner_id' => (int) $owner_id,
                    'sub_total' => single_price($sub_total),
                    'cart_items' => $shop_items_data
                ];

                $shops[] = $shop;

                // ✅ Reset subtotal per shop (same as your logic)
                $sub_total = 0.00;
            }
        }

        return response()->json([
            // "grand_total" => single_price($sub_total), // ⚠️ kept same as your logic
            "summary" => $this->summary($request),
            "data" => $shops
        ]);
    }

    public function user_id_update(Request $request)
    {
        // ✅ Validate request
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'temp_user_id' => 'required|string'
        ]);

        try {
            // ✅ Update directly and get affected rows
            $updated = Cart::where('temp_user_id', $validated['temp_user_id'])
                ->update(['user_id' => $validated['user_id']]);

            // ✅ Check if any row was updated
            if ($updated > 0) {
                return response()->json([
                    'result' => true,
                    'message' => 'User ID updated successfully'
                ], 200);
            }

            // ❌ No records found
            return response()->json([
                'result' => false,
                'message' => 'No cart found for the given temp_user_id'
            ], 404);
        } catch (\Exception $e) {
            // ❌ Handle unexpected errors
            return response()->json([
                'result' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage() // remove in production
            ], 500);
        }
    }

    public function add(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id'              => 'nullable',
                'temp_user_id'         => 'nullable',
                'products'             => 'required|array|min:1',
                'products.*.id'        => 'required|integer',
                'products.*.quantity'  => 'nullable|integer|min:1',
                'products.*.sku'       => 'required|string',
                'products.*.size'      => 'nullable|string',
                'products.*.color'     => 'nullable|string',
                'products.*.flavour'   => 'nullable|string',
            ]);

            // dd($request->all());

            $user_id      = $request->input('user_id');
            $temp_user_id = $request->input('temp_user_id');
            $products     = $request->input('products');

            // Identify cart owner
            $cartKey   = $user_id ? 'user_id' : 'temp_user_id';
            $cartValue = $user_id ?? $temp_user_id;

            $carts = Cart::where($cartKey, $cartValue)->get();
            $check_auction_in_cart = CartUtility::check_auction_in_cart($carts);

            foreach ($products as $item) {

                $product = Product::find($item['id']);

                if (!$product) {
                    return response()->json([
                        'result'  => false,
                        'message' => translate('This product is no longer available. Please remove it from your cart and try again.'),
                        'product_id' => $item['id'],
                    ], 404);
                }

                $quantity = isset($item['quantity']) ? max(1, (int) $item['quantity']) : 1;
                $rawCode  = $item['sku'];    // frontend sends either sku or pip_code here
                $size     = $item['size']    ?? null;
                $color    = $item['color']   ?? null;
                $flavour  = $item['flavour'] ?? null;

                // ── Step 1: try matching as SKU ───────────────────────────────
                $product_stock = ProductStock::where('product_id', $product->id)
                    ->where('sku', $rawCode)
                    ->first();

                // ── Step 2: try matching as pip_code ──────────────────────────
                if (!$product_stock) {
                    $product_stock = ProductStock::where('product_id', $product->id)
                        ->where('pip_code', $rawCode)
                        ->first();
                }

                // ── Step 3: fall back to variant/color/flavour match ──────────
                if (!$product_stock) {
                    $product_stock = ProductStock::where('product_id', $product->id)
                        ->when($size,    fn($q) => $q->where('variant', $size))
                        ->when($color,   fn($q) => $q->where('color', $color))
                        ->when($flavour, fn($q) => $q->where('flavour', $flavour))
                        ->first();
                }

                if (!$product_stock) {
                    return response()->json([
                        'result'  => false,
                        'message' => translate('Selected variation is not available.')
                    ], 404);
                }

                // ── Normalize: always store the actual SKU from the stock record.
                // If the stock has a real sku use it; otherwise fall back to pip_code.
                $sku = (!empty($product_stock->sku) && $product_stock->sku != '0')
                    ? $product_stock->sku
                    : ($product_stock->pip_code ?? $rawCode);

                // Use the actual variant (size) stored on the stock record
                $variant = $product_stock->variant ?? '';

                // ✅ Auction rules (unchanged)
                if ($check_auction_in_cart && $product->auction_product == 0) {
                    return response()->json([
                        'result' => false,
                        'message' => translate('Remove auction product from cart to add this product.')
                    ], 200);
                }

                if (!$check_auction_in_cart && $carts->count() > 0 && $product->auction_product == 1) {
                    return response()->json([
                        'result' => false,
                        'message' => translate('Remove other products from cart to add this auction product.')
                    ], 200);
                }

                // ✅ Minimum quantity check
                if ($product->min_qty > $quantity) {
                    return response()->json([
                        'result' => false,
                        'message' => translate("Minimum") . " {$product->min_qty} " . translate("item(s) should be ordered")
                    ], 200);
                }

                // ✅ Prepare cart
                $cart = Cart::firstOrNew([
                    'variation' => $variant,
                    'user_id' => $user_id,
                    'sku' => $sku,
                    'temp_user_id' => $temp_user_id,
                    'product_id' => $product->id
                ]);

                // ✅ If already exists
                if ($cart->exists) {

                    if ($product->auction_product == 1 && ($cart->product_id == $product->id)) {
                        return response()->json([
                            'result' => false,
                            'message' => translate('This auction product is already added to your cart.')
                        ], 200);
                    }

                    if ($product->digital == 1) {
                        return response()->json([
                            'result' => false,
                            'message' => translate('Already added this product')
                        ], 200);
                    }

                    $newQuantity = $cart->quantity + $quantity;

                    // ✅ Stock validation with existing quantity
                    if ($product_stock->qty < $newQuantity) {
                        return response()->json([
                            'result' => false,
                            'message' => $product_stock->qty == 0
                                ? translate("Stock out")
                                : translate("Only") . " {$product_stock->qty} " . translate("item(s) are available")
                        ], 200);
                    }

                    $quantity = $newQuantity;
                } else {

                    // ✅ Stock validation for new cart item
                    if ($product_stock->qty < $quantity) {
                        return response()->json([
                            'result' => false,
                            'message' => $product_stock->qty == 0
                                ? translate("Stock out")
                                : translate("Only") . " {$product_stock->qty} " . translate("item(s) are available")
                        ], 200);
                    }
                }

                // ✅ Price calculation
                $price = CartUtility::get_price($product, $product_stock, $quantity);

                // ✅ Address
                $address = CreditDelivery::where('credit_id', $cartValue)->first();

                // ✅ Tax logic
                $tax = (isset($address) && $address->country != 'United Kingdom')
                    ? 0
                    : CartUtility::tax_calculation($product, $price);

                // ✅ Save cart
                CartUtility::save_cart_data($cart, $product, $price, $tax, $quantity);
            }

            // ✅ Summary
            $summary = $this->summary($request);
            $count = $this->count($request);

            return response()->json([
                'result' => true,
                'summary' => $summary,
                'count' => $count,
                'message' => translate('Product added to cart successfully')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'result' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            \Log::error('Cart Add Error: ' . $e->getMessage());

            return response()->json([
                'result' => false,
                'message' => translate('Something went wrong. Please try again later.')
            ], 500);
        }
    }


    public function delete(Request $request)
    {
        $id = $request->input('id');

        // ✅ Validate cart ID
        if (!$id) {
            return response()->json([
                'result' => false,
                'message' => translate('Cart ID is required.')
            ], 400);
        }

        // ✅ Find cart item
        $cart = Cart::find($id);

        // ✅ Check if cart exists
        if (!$cart) {
            return response()->json([
                'result' => false,
                'message' => translate('Cart item not found.')
            ], 404);
        }

        // ✅ Delete the cart item
        $cart->delete();

        return response()->json([
            'result' => true,
            'message' => translate('Cart item removed successfully.')
        ], 200);
    }

    public function changeQuantity(Request $request)
    {
        $cart = Cart::with('product.stocks')->find($request->id);

        if (!$cart) {
            return response()->json([
                'result' => false,
                'message' => translate('Something went wrong')
            ], 200);
        }

        $product = $cart->product;

        // ✅ Auction products cannot change quantity
        if ($product->auction_product == 1) {
            return response()->json([
                'result' => false,
                'message' => translate('Maximum available quantity reached')
            ], 200);
        }

        // ✅ Find stock for the cart SKU
        $stock = $product->stocks->where('sku', $cart->sku)->first();

        if ($stock && $stock->qty >= $request->quantity) {
            $cart->update(['quantity' => $request->quantity]);

            return response()->json([
                'result' => true,
                'message' => translate('Cart updated'),
                'price'   => $cart->price, // 🔥 Send updated price
            ], 200);
        }

        // ✅ Quantity exceeds stock
        return response()->json([
            'result' => false,
            'message' => translate('Maximum available quantity reached')
        ], 200);
    }

    public function process(Request $request)
    {
        // Log the request data for debugging
        \Log::info('Cart Process Request:', $request->all());

        $cart_ids = explode(",", $request->cart_ids);
        $cart_quantities = explode(",", $request->cart_quantities);

        if (empty($cart_ids) || empty($cart_quantities)) {
            return response()->json(['result' => false, 'message' => translate('Cart is empty')], 200);
        }

        $i = 0;
        foreach ($cart_ids as $cart_id) {
            $cart_item = Cart::where('id', $cart_id)->first();

            // ✅ Check if cart item exists
            if (!$cart_item) {
                return response()->json(['result' => false, 'message' => translate("Cart item not found")], 404);
            }

            $product = Product::where('id', $cart_item->product_id)->first();

            // ✅ Check if product exists
            if (!$product) {
                return response()->json(['result' => false, 'message' => translate("Product not found for cart item")], 404);
            }

            // ✅ Validate minimum quantity
            if ($product->min_qty > $cart_quantities[$i]) {
                return response()->json([
                    'result' => false,
                    'message' => translate("Minimum") . " {$product->min_qty} " . translate("item(s) should be ordered for") . " {$product->name}"
                ], 200);
            }

            // ✅ Fetch stock safely (use `optional()` to prevent null errors)
            $stock = optional($cart_item->product->stocks->where('variant', $cart_item->variation)->first())->qty ?? 0;
            $variant_string = !empty($cart_item->variation) ? " ($cart_item->variation)" : "";

            // ✅ Check stock availability
            if ($stock >= $cart_quantities[$i] || $product->digital == 1) {
                $cart_item->update(['quantity' => $cart_quantities[$i]]);
            } else {
                if ($stock == 0) {
                    return response()->json([
                        'result' => false,
                        'message' => translate("No item is available for") . " {$product->name}{$variant_string}," . translate("remove this from cart")
                    ], 200);
                } else {
                    return response()->json([
                        'result' => false,
                        'message' => translate("Only") . " {$stock} " . translate("item(s) are available for") . " {$product->name}{$variant_string}"
                    ], 200);
                }
            }

            $i++;
        }

        return response()->json(['result' => true, 'message' => translate('Cart updated')], 200);
    }


    public function destroy($id)
    {
        Cart::destroy($id);
        return response()->json(['result' => true, 'message' => translate('Product is successfully removed from your cart')], 200);
    }
}
