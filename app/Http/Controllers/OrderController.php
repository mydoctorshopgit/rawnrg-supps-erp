<?php

namespace App\Http\Controllers;

use Auth;
use Mail;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Address;
use App\Models\Product;
use App\Models\Accounts;
use App\Models\CouponUsage;
use App\Models\OrderDetail;
use App\Models\SmsTemplate;
use App\Utility\SmsUtility;
use Mpdf\Mpdf;
use App\Models\ProductStock;
use CoreComponentRepository;
use Illuminate\Http\Request;
use App\Models\CombinedOrder;
use App\Mail\InvoiceEmailManager;
use App\Mail\Invoice2Mail;
use App\Mail\DispatchedOrder;
use App\Mail\Order_verification;
use App\Mail\order_place;
use App\Utility\NotificationUtility;
// use Twilio\Rest\Accounts;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AffiliateController;
use Illuminate\Support\Facades\Crypt;
use Log;

class OrderController extends Controller
{

    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:view_all_orders|view_inhouse_orders|view_seller_orders|view_pickup_point_orders'])->only('all_orders');
        $this->middleware(['permission:view_order_details'])->only('show');
        $this->middleware(['permission:delete_order'])->only('destroy', 'bulk_order_delete');
    }


    // All Orders
    public function all_orders(Request $request)
    {
        // CoreComponentRepository::instantiateShopRepository();
        $search = $request->search;

        $date = $request->date;
        $sort_search = null;
        $delivery_status = null;
        $payment_status = '';
        // $orders = DB::table('orders')
        // ->select('post_code', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(grand_total) as total_sum'))
        // ->groupBy('post_code')
        // ->get();
        $orders = Order::select('orders.*', 'customer_register_credit.company_name', 'users.name', 'users.last_name')
        ->leftjoin('customer_register_credit', 'orders.user_id', '=', 'customer_register_credit.user_id')
        ->leftjoin('users', 'orders.user_id', '=', 'users.id')
        ->orderBy('orders.created_at', 'desc');
        $orders = $orders->where('orders.status', '=', '1');
        $admin_user_id = User::where('user_type', 'admin')->first()->id;


        if (
            Route::currentRouteName() == 'inhouse_orders.index' &&
            Auth::user()->can('view_inhouse_orders')
        ) {
            $orders = $orders->where('orders.seller_id', '=', $admin_user_id);
        } else if (
            Route::currentRouteName() == 'seller_orders.index' &&
            Auth::user()->can('view_seller_orders')
        ) {
            $orders = $orders->where('orders.seller_id', '!=', $admin_user_id);
        } else if (
            Route::currentRouteName() == 'pick_up_point.index' &&
            Auth::user()->can('view_pickup_point_orders')
        ) {
            if (get_setting('vendor_system_activation') != 1) {
                $orders = $orders->where('orders.seller_id', '=', $admin_user_id);
            }
            $orders->where('shipping_type', 'pickup_point')->orderBy('code', 'desc');
            if (
                Auth::user()->user_type == 'staff' &&
                Auth::user()->staff->pick_up_point != null
            ) {
                $orders->where('shipping_type', 'pickup_point')
                    ->where('pickup_point_id', Auth::user()->staff->pick_up_point->id);
            }
        } else if (
            Route::currentRouteName() == 'all_orders.index' &&
            Auth::user()->can('view_all_orders')
        ) {
            if (get_setting('vendor_system_activation') != 1) {
                $orders = $orders->where('orders.seller_id', '=', $admin_user_id);
            }
        } else {
            abort(403);
        }

        if ($search) {
            $orders->where(function ($query) use ($search) {
                $query->where('post_code', 'like', '%' . $search . '%')
                    ->orWhere('delivery_name', 'like', '%' . $search . '%')
                    ->orWhere('purchase_order_number', 'like', '%' . $search . '%')
                    ->orWhere('invoice_number', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%')
                    ->orWhere('carrier_name', 'like', '%' . $search . '%')
                    ->orWhere('tracking_code', 'like', '%' . $search . '%')
                    ->orWhere('company_name', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhereHas('customer_details', function ($subQuery) use ($search) {
                        $subQuery->where('company_name', 'like', '%' . $search . '%');
                    });
            });
        }
        if ($request->payment_status != null) {
            $orders = $orders->where('payment_status', $request->payment_status);
            $payment_status = $request->payment_status;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($date != null) {
            $orders = $orders->where('created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])) . '  00:00:00')
                ->where('created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])) . '  23:59:59');
        }
        $orders = $orders->latest()->paginate(15);

        return view('backend.sales.index', compact('orders', 'sort_search', 'payment_status', 'delivery_status', 'date'));
    }


    public function show($id)
    {
        $order = Order::with(['user', 'user.registerCredit'])->findOrFail(decrypt($id));

        $order_shipping_address = json_decode($order->shipping_address);

        // $similar_orders = Order::where('post_code', $order->post_code)
        //     ->whereDate('created_at', Carbon::parse($order->created_at)->format('Y-m-d'))
        //     ->where('id', '!=', $order->id) 
        //     ->orderBy('post_code')
        //     ->orderBy('created_at', 'asc')
        //     ->get();

        $delivery_boys = User::where('city', $order_shipping_address->city ?? '')
            ->where('user_type', 'delivery_boy')
            ->get();

        $order->viewed = 1;
        $order->save();

        return view('backend.sales.show', compact('order',  'delivery_boys'));
    }

    //  public function dispatch_orders_show($id)
    // {
    //     $order = Order::findOrFail(decrypt($id));
    //     $order_shipping_address = json_decode($order->shipping_address);
    //     $delivery_boys = User::where('city', $order_shipping_address->city)
    //         ->where('user_type', 'delivery_boy')
    //         ->get();

    //     $order->viewed = 1;
    //     $order->save();
    //     return view('backend.sales.dispatch_orders_show', compact('order', 'delivery_boys'));
    // }


    // fullfilment
    public function fullfillment_orders(Request $request)
    {
        // Get request filters
        $date = $request->date;
        $search = $request->search;
        $payment_status = $request->payment_status;
        $delivery_status = $request->delivery_status;

        // Fetch orders with basic conditions
        $orders = Order::select('orders.*', 'customer_register_credit.company_name', 'users.name', 'users.last_name')
        ->leftjoin('customer_register_credit', 'orders.user_id', '=', 'customer_register_credit.user_id')
        ->leftjoin('users', 'orders.user_id', '=', 'users.id')->where('orders.status', 2)
        ->orderBy('orders.created_at', 'desc');

        // Apply filters if provided
        if ($search) {
            $orders->where(function ($query) use ($search) {
                $query->where('post_code', 'like', '%' . $search . '%')
                    ->orWhere('delivery_name', 'like', '%' . $search . '%')
                    ->orWhere('purchase_order_number', 'like', '%' . $search . '%')
                    ->orWhere('invoice_number', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%')
                    ->orWhere('carrier_name', 'like', '%' . $search . '%')
                    ->orWhere('tracking_code', 'like', '%' . $search . '%')
                    ->orWhereHas('customer_details', function ($subQuery) use ($search) {
                        $subQuery->where('company_name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($payment_status) {
            $orders->where('payment_status', $payment_status);
        }

        if ($delivery_status) {
            $orders->where('delivery_status', $delivery_status);
        }

        if ($date) {
            $dates = explode(" to ", $date);
            $orders->whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dates[0])),
                date('Y-m-d 23:59:59', strtotime($dates[1]))
            ]);
        }

        // Order by post_code and code in descending order
        // $orders->orderBy('created_at', 'desc')
        //     ->orderByRaw('COALESCE(id, "") DESC');



        // Paginate the results
        $orders = $orders->latest()->paginate(15);

        // Return the view with data
        return view('backend.sales.fullfillment_order', compact('orders', 'search', 'payment_status', 'delivery_status', 'date'));
    }
    public function fullfillment_orders_show($id)
    {
        $order = Order::findOrFail($id);
        $order_shipping_address = json_decode($order->shipping_address);
        $similar_orders = Order::where('post_code', $order->post_code)
            ->whereDate('created_at', Carbon::parse($order->created_at)->format('Y-m-d'))
            ->where('id', '!=', $order->id)
            ->orderBy('post_code')
            ->orderBy('created_at', 'asc')
            ->get();

        $delivery_boys = User::where('city', $order_shipping_address->city ?? '')
            ->where('user_type', 'delivery_boy')
            ->get();

        $order->viewed = 1;
        $order->save();
        return view('backend.sales.fullfillment_show', compact('order', 'similar_orders', 'delivery_boys'));
    }



    // shipment
    public function shipment_order(Request $request)
    {
        // Get request filters
        $date = $request->date;
        $search = $request->search;
        $payment_status = $request->payment_status;
        $delivery_status = $request->delivery_status;

        // Fetch orders with basic conditions
        $orders = Order::select('orders.*', 'customer_register_credit.company_name', 'users.name', 'users.last_name')
        ->leftjoin('customer_register_credit', 'orders.user_id', '=', 'customer_register_credit.user_id')
        ->leftjoin('users', 'orders.user_id', '=', 'users.id')->where('status', 3)->orderBy('created_at', 'desc');

        // Apply filters if provided
        if ($search) {
            $orders->where(function ($query) use ($search) {
                $query->where('post_code', 'like', '%' . $search . '%')
                    ->orWhere('delivery_name', 'like', '%' . $search . '%')
                    ->orWhere('purchase_order_number', 'like', '%' . $search . '%')
                    ->orWhere('invoice_number', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%')
                    ->orWhere('carrier_name', 'like', '%' . $search . '%')
                    ->orWhere('tracking_code', 'like', '%' . $search . '%')
                    ->orWhereHas('customer_details', function ($subQuery) use ($search) {
                        $subQuery->where('company_name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($payment_status) {
            $orders->where('payment_status', $payment_status);
        }

        if ($delivery_status) {
            $orders->where('delivery_status', $delivery_status);
        }

        if ($date) {
            $dates = explode(" to ", $date);
            $orders->whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dates[0])),
                date('Y-m-d 23:59:59', strtotime($dates[1]))
            ]);
        }

        // Paginate the results
        $orders = $orders->latest()->paginate(15);

        // Return the view with data
        return view('backend.sales.shipment_confirmation', compact('orders', 'search', 'payment_status', 'delivery_status', 'date'));
    }
    public function shipment_orders_show($id)
    {
        $order = Order::findOrFail($id);
        $order_shipping_address = json_decode($order->shipping_address);
        $delivery_boys = User::where('city', $order_shipping_address->city ?? '')
            ->where('user_type', 'delivery_boy')
            ->get();

        $order->viewed = 1;
        $order->save();
        return view('backend.sales.shipment_show', compact('order', 'delivery_boys'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $carts = Cart::where('user_id', Auth::user()->id)
            ->get();

        if ($carts->isEmpty()) {
            flash(translate('Your cart is empty'))->warning();
            return redirect()->route('home');
        }

        $address = Address::where('id', $carts[0]['address_id'])->first();

        $shippingAddress = [];
        if ($address != null) {
            $shippingAddress['name']        = Auth::user()->name;
            $shippingAddress['email']       = Auth::user()->email;
            $shippingAddress['address']     = $address->address;
            $shippingAddress['country']     = $address->country->name;
            $shippingAddress['state']       = $address->state->name;
            $shippingAddress['city']        = $address->city->name;
            $shippingAddress['postal_code'] = $address->postal_code;
            $shippingAddress['phone']       = $address->phone;
            if ($address->latitude || $address->longitude) {
                $shippingAddress['lat_lang'] = $address->latitude . ',' . $address->longitude;
            }
        }

        $combined_order = new CombinedOrder;
        $combined_order->user_id = Auth::user()->id;
        $combined_order->shipping_address = json_encode($shippingAddress);
        $combined_order->save();

        $seller_products = array();
        foreach ($carts as $cartItem) {
            $product_ids = array();
            $product = Product::find($cartItem['product_id']);
            if (isset($seller_products[$product->user_id])) {
                $product_ids = $seller_products[$product->user_id];
            }
            array_push($product_ids, $cartItem);
            $seller_products[$product->user_id] = $product_ids;
        }

        foreach ($seller_products as $seller_product) {
            $order = new Order;
            $order->combined_order_id = $combined_order->id;
            $order->user_id = Auth::user()->id;
            $order->shipping_address = $combined_order->shipping_address;

            $order->additional_info = $request->additional_info;

            // $order->shipping_type = $carts[0]['shipping_type'];
            // if ($carts[0]['shipping_type'] == 'pickup_point') {
            //     $order->pickup_point_id = $cartItem['pickup_point'];
            // }
            // if ($carts[0]['shipping_type'] == 'carrier') {
            //     $order->carrier_id = $cartItem['carrier_id'];
            // }

            $order->payment_type = $request->payment_option;
            $order->delivery_viewed = '0';
            $order->payment_status_viewed = '0';
            $order->code = date('Ymd-His') . rand(10, 99);
            $order->date = strtotime('now');
            $order->save();

            $subtotal = 0;
            $tax = 0;
            $shipping = 0;
            $coupon_discount = 0;

            //Order Details Storing
            foreach ($seller_product as $cartItem) {
                $product = Product::find($cartItem['product_id']);

                $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
                $tax +=  cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
                $coupon_discount += $cartItem['discount'];

                $product_variation = $cartItem['variation'];

                $product_stock = $product->stocks->where('variant', $product_variation)->first();
                if ($product->digital != 1 && $cartItem['quantity'] > $product_stock->qty) {
                    flash(translate('The requested quantity is not available for ') . $product->getTranslation('name'))->warning();
                    $order->delete();
                    return redirect()->route('cart')->send();
                } elseif ($product->digital != 1) {
                    $product_stock->qty -= $cartItem['quantity'];
                    $product_stock->save();
                }

                $order_detail = new OrderDetail;
                $order_detail->order_id = $order->id;
                $order_detail->seller_id = $product->user_id;
                $order_detail->product_id = $product->id;
                $order_detail->variation = $product_variation;
                $order_detail->price = cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
                $order_detail->tax = cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
                $order_detail->shipping_type = $cartItem['shipping_type'];
                $order_detail->product_referral_code = $cartItem['product_referral_code'];
                $order_detail->shipping_cost = $cartItem['shipping_cost'];

                $shipping += $order_detail->shipping_cost;
                //End of storing shipping cost

                $order_detail->quantity = $cartItem['quantity'];

                if (addon_is_activated('club_point')) {
                    $order_detail->earn_point = $product->earn_point;
                }

                $order_detail->save();

                $product->num_of_sale += $cartItem['quantity'];
                $product->save();

                $order->seller_id = $product->user_id;
                $order->shipping_type = $cartItem['shipping_type'];

                if ($cartItem['shipping_type'] == 'pickup_point') {
                    $order->pickup_point_id = $cartItem['pickup_point'];
                }
                if ($cartItem['shipping_type'] == 'carrier') {
                    $order->carrier_id = $cartItem['carrier_id'];
                }

                if ($product->added_by == 'seller' && $product->user->seller != null) {
                    $seller = $product->user->seller;
                    $seller->num_of_sale += $cartItem['quantity'];
                    $seller->save();
                }

                if (addon_is_activated('affiliate_system')) {
                    if ($order_detail->product_referral_code) {
                        $referred_by_user = User::where('referral_code', $order_detail->product_referral_code)->first();

                        $affiliateController = new AffiliateController;
                        $affiliateController->processAffiliateStats($referred_by_user->id, 0, $order_detail->quantity, 0, 0);
                    }
                }
            }

            $order->grand_total = $subtotal + $tax + $shipping;

            if ($seller_product[0]->coupon_code != null) {
                $order->coupon_discount = $coupon_discount;
                $order->grand_total -= $coupon_discount;

                $coupon_usage = new CouponUsage;
                $coupon_usage->user_id = Auth::user()->id;
                $coupon_usage->coupon_id = Coupon::where('code', $seller_product[0]->coupon_code)->first()->id;
                $coupon_usage->save();
            }

            $combined_order->grand_total += $order->grand_total;

            $order->save();
        }

        $combined_order->save();

        foreach ($combined_order->orders as $order) {
            NotificationUtility::sendOrderPlacedNotification($order);
        }

        $request->session()->put('combined_order_id', $combined_order->id);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        if ($order != null) {
            foreach ($order->orderDetails as $key => $orderDetail) {
                try {

                    $product_stock = ProductStock::where('product_id', $orderDetail->product_id)->where('variant', $orderDetail->variation)->first();
                    if ($product_stock != null) {
                        $product_stock->qty += $orderDetail->quantity;
                        $product_stock->save();
                    }
                } catch (\Exception $e) {
                }

                $orderDetail->delete();
            }
            $order->delete();
            flash(translate('Order has been deleted successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }
        return back();
    }

    public function product_destory($id, $prod_id)
    {

        OrderDetail::where('order_id', '=', $id)->where('product_id', '=', $prod_id)->delete();

        $order = OrderDetail::where('order_id', '=', $id)->get();
        $total = 0;
        $tax = 0;
        $shipping_cost = 0;
        if ($order != null) {
            foreach ($order as $key => $orderDetail) {
                try {

                    $product_stock = ProductStock::where('product_id', $orderDetail->product_id)->where('variant', $orderDetail->variation)->first();
                    if ($product_stock != null) {
                        $product_stock->qty += $orderDetail->quantity;
                        $product_stock->save();
                    }
                    $total += $orderDetail->price;
                    $tax += $orderDetail->tax;
                    $shipping_cost += $orderDetail->shipping_cost;
                } catch (\Exception $e) {
                }
            }

            $grand_total = $total + $tax + $shipping_cost;
            Order::where('id', $id)->update(["grand_total" => $grand_total]);
            flash(translate('Product has been deleted successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }
        return back();
    }

    public function bulk_order_delete(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $order_id) {
                $this->destroy($order_id);
            }
        }

        return 1;
    }

    public function order_details(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->save();
        return view('seller.order_details_seller', compact('order'));
    }




    public function sendInvoice2(Request $request)
    {
        // dd('hello');
        $invoiceData = [
            'id' => 1234,
            'amount' => '$250.00'
        ];

        Mail::to('haseeb@tech9et.com ')->send(new Invoice2Mail($invoiceData));

        return back()->with('success', 'Invoice sent successfully!');
    }


    public function update_delivery_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->delivery_viewed = '0';
        $order->delivery_status = $request->status;
        $order->save();

        if ($request->status == 'cancelled' && $order->payment_type == 'wallet') {
            $user = User::where('id', $order->user_id)->first();
            $user->balance += $order->grand_total;
            $user->save();
        }

        if (Auth::user()->user_type == 'seller') {
            foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail) {
                $orderDetail->delivery_status = $request->status;
                $orderDetail->save();

                if ($request->status == 'cancelled') {
                    $variant = $orderDetail->variation;
                    if ($orderDetail->variation == null) {
                        $variant = '';
                    }

                    $product_stock = ProductStock::where('product_id', $orderDetail->product_id)
                        ->where('variant', $variant)
                        ->first();

                    if ($product_stock != null) {
                        $product_stock->qty += $orderDetail->quantity;
                        $product_stock->save();
                    }
                }
            }
        } else {
            foreach ($order->orderDetails as $key => $orderDetail) {

                $orderDetail->delivery_status = $request->status;
                $orderDetail->save();

                if ($request->status == 'cancelled') {
                    $variant = $orderDetail->variation;
                    if ($orderDetail->variation == null) {
                        $variant = '';
                    }

                    $product_stock = ProductStock::where('product_id', $orderDetail->product_id)
                        ->where('variant', $variant)
                        ->first();

                    if ($product_stock != null) {
                        $product_stock->qty += $orderDetail->quantity;
                        $product_stock->save();
                    }
                }

                if (addon_is_activated('affiliate_system')) {
                    if (($request->status == 'delivered' || $request->status == 'cancelled') &&
                        $orderDetail->product_referral_code
                    ) {

                        $no_of_delivered = 0;
                        $no_of_canceled = 0;

                        if ($request->status == 'delivered') {
                            $no_of_delivered = $orderDetail->quantity;
                        }
                        if ($request->status == 'cancelled') {
                            $no_of_canceled = $orderDetail->quantity;
                        }

                        $referred_by_user = User::where('referral_code', $orderDetail->product_referral_code)->first();

                        $affiliateController = new AffiliateController;
                        $affiliateController->processAffiliateStats($referred_by_user->id, 0, 0, $no_of_delivered, $no_of_canceled);
                    }
                }
            }
        }
        if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'delivery_status_change')->first()->status == 1) {
            try {
                SmsUtility::delivery_status_change(json_decode($order->shipping_address)->phone, $order);
            } catch (\Exception $e) {
            }
        }

        //sends Notifications to user
        // NotificationUtility::sendNotification($order, $request->status);
        // if (get_setting('google_firebase') == 1 && $order->user->device_token != null) {
        //     $request->device_token = $order->user->device_token;
        //     $request->title = "Order updated !";
        //     $status = str_replace("_", "", $order->delivery_status);
        //     $request->text = " Your order {$order->code} has been {$status}";

        //     $request->type = "order";
        //     $request->id = $order->id;
        //     $request->user_id = $order->user->id;

        //     NotificationUtility::sendFirebaseNotification($request);
        // }


        if (addon_is_activated('delivery_boy')) {
            if (Auth::user()->user_type == 'delivery_boy') {
                $deliveryBoyController = new DeliveryBoyController;
                $deliveryBoyController->store_delivery_history($order);
            }
        }

        return 1;
    }

    public function update_tracking_code(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->tracking_code = $request->tracking_code;
        $order->save();

        return 1;
    }

    public function purchase_order_number(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->carrier_name = $request->carrier_name;
        $order->save();

        return 1;
    }
    public function notes_update(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->notes = $request->notes;
        $order->save();

        return 1;
    }
    public function order_status_old(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->status = $request->status;
        $order->save();
        // if ($request->status == 3) {
        //     Accounts::where('order_id', '=', $request->order_id)->update(['status' => 1]);
        // }
        return 1;
    }
    
    public function order_status(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'status'   => 'required|integer',
        ]);

        $order = Order::findOrFail($request->order_id);
        $order->status = $request->status;
        $order->save();

        $userId = $order->user_id ?? $order->guest_id;

        // Status 1 = ERP confirmed — send payment link to customer
        if ($request->status == 1 && $userId) {
            try {
                $user = User::findOrFail($userId);

                $payload = [
                    'user_id' => $user->id,
                    'order_id' => $order->id
                ];

                $token = Crypt::encryptString(json_encode($payload));

                $verificationUrl = rtrim(env('FRONTEND_URL', config('app.url')), '/'). '/payment-order?token='. $token;

                Log::info('Order confirmed — sending payment email', [
                    'customer_id' => $user->id,
                    'order_id'    => $order->id,
                    'verify_url' => $verificationUrl,
                ]);


                // Mail::to($user->email)->send(new order_place(
                //     [
                //         'view'    => 'emails.order_confirmation',
                //         'subject' => 'Your order has been placed - ' . $order->code,
                //         'from'    => env('MAIL_USERNAME'),
                //         'order'   => $order,
                //     ],
                //     '',              // filePath
                //     '',              // excelFilePath
                //     $verificationUrl // payment link
                // ));

                Mail::to($user->email)->send(new Order_verification($verificationUrl, $order));

            } catch (\Throwable $e) {
                Log::error('Failed to send order confirmation email', [
                    'order_id' => $order->id,
                    'user_id'  => $order->user_id,
                    'error'    => $e->getMessage(),
                ]);
            }
        }

        return 1;
    }


    public function update_payment_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->payment_status_viewed = '0';
        $order->save();

        if (Auth::user()->user_type == 'seller') {
            foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail) {
                $orderDetail->payment_status = $request->status;
                $orderDetail->save();
            }
        } else {
            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->payment_status = $request->status;
                $orderDetail->save();
            }
        }

        $status = 'paid';
        foreach ($order->orderDetails as $key => $orderDetail) {
            if ($orderDetail->payment_status != 'paid') {
                $status = 'unpaid';
            }
        }
        $order->payment_status = $status;
        $order->save();


        if (
            $order->payment_status == 'paid' &&
            $order->commission_calculated == 0
        ) {
            calculateCommissionAffilationClubPoint($order);
        }

        //sends Notifications to user
        // NotificationUtility::sendNotification($order, $request->status);
        // if (get_setting('google_firebase') == 1 && $order->user->device_token != null) {
        //     $request->device_token = $order->user->device_token;
        //     $request->title = "Order updated !";
        //     $status = str_replace("_", "", $order->payment_status);
        //     $request->text = " Your order {$order->code} has been {$status}";

        //     $request->type = "order";
        //     $request->id = $order->id;
        //     $request->user_id = $order->user->id;

        //     NotificationUtility::sendFirebaseNotification($request);
        // }


        if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'payment_status_change')->first()->status == 1) {
            try {
                SmsUtility::payment_status_change(json_decode($order->shipping_address)->phone, $order);
            } catch (\Exception $e) {
            }
        }
        return 1;
    }

    public function assign_delivery_boy(Request $request)
    {
        if (addon_is_activated('delivery_boy')) {

            $order = Order::findOrFail($request->order_id);
            $order->assign_delivery_boy = $request->delivery_boy;
            $order->delivery_history_date = date("Y-m-d H:i:s");
            $order->save();

            $delivery_history = \App\Models\DeliveryHistory::where('order_id', $order->id)
                ->where('delivery_status', $order->delivery_status)
                ->first();

            if (empty($delivery_history)) {
                $delivery_history = new \App\Models\DeliveryHistory;

                $delivery_history->order_id = $order->id;
                $delivery_history->delivery_status = $order->delivery_status;
                $delivery_history->payment_type = $order->payment_type;
            }
            $delivery_history->delivery_boy_id = $request->delivery_boy;

            $delivery_history->save();

            if (env('MAIL_USERNAME') != null && get_setting('delivery_boy_mail_notification') == '1') {
                $array['view'] = 'emails.invoice';
                $array['subject'] = translate('You are assigned to delivery an order. Order code') . ' - ' . $order->code;
                $array['from'] = env('MAIL_FROM_ADDRESS');
                $array['order'] = $order;

                try {
                    Mail::to($order->delivery_boy->email)->queue(new InvoiceEmailManager($array));
                } catch (\Exception $e) {
                }
            }

            if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'assign_delivery_boy')->first()->status == 1) {
                try {
                    SmsUtility::assign_delivery_boy($order->delivery_boy->phone, $order->code);
                } catch (\Exception $e) {
                }
            }
        }

        return 1;
    }
    public function updateOrderDetail(Request $request)
    {
        $orderDetail = OrderDetail::findOrFail($request->id);
        $orderDetail->quantity = $request->quantity;
        $orderDetail->picked_qty = $request->picked_qty;
        $orderDetail->price = $request->price * $request->quantity;
        $orderDetail->save();
        $ordersDetails = OrderDetail::where('order_id', $request->order_id)->get();
        // Initialize totals
        $price = 0;
        $tax = 0;
        $shipping_cost = 0;
        foreach ($ordersDetails as $value) {
            $price += $value->price;
            $tax += $value->tax;
            $shipping_cost += $value->shipping_cost;
        }
        $order = Order::findOrFail($request->order_id);
        $coupon_discount = $order->coupon_discount ?? 0;
        $order->total_tax = ($price + $shipping_cost - $coupon_discount) * 20 / 100;
        $order->grand_total = $price + $order->total_tax + $shipping_cost;
        // dd( $order->grand_total);
        $order->save();
        return response()->json(['success' => true]);
    }

    // public function updateOrderDetail(Request $request)
    // {

    //     $orderDetail = OrderDetail::findOrFail($request->id);
    //     $orderDetail->quantity = $request->quantity;
    //     $orderDetail->picked_qty = $request->picked_qty;
    //     $orderDetail->price = $request->price * $request->quantity;
    //     $orderDetail->save();

    //     $order = OrderDetail::where('id','=',$request->order_id)->get();
    //     $ordersDetails = OrderDetail::where('order_id','=',$request->order_id)->get();
    //     $price = 0;
    //     $tax = 0;
    //     $shipping_cost = 0;

    //     foreach ($ordersDetails as $key => $value) {
    //         $price += $value->price;
    //         $tax += $value->tax;
    //         $shipping_cost += $value->shipping_cost;
    //     }

    //     $order = Order::findOrFail($request->order_id);
    //     // if (!empty($order->tax)) {
    //         $order->total_tax =   ($price + $shipping_cost - $order->coupon_discount) * 20/100;
    //     // }
    //     $order->grand_total =   $price+ $order->total_tax + $shipping_cost;
    //     $order->save();

    //     return response()->json(['success' => true]);
    // }

    public function updateStatusAndSendInvoice(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->status = $request->status;
        $order->save();
        if ($request->status == 4) {
            Accounts::where('order_id', '=', $request->order_id)->update(['status' => 1]);
        }
        $array['view'] = 'emails.order_dispatched'; // Email body me invoice2 ka layout use hoga
        $array['subject'] = 'PO - ' . $order->code;
        $array['from'] = env('MAIL_USERNAME');
        $array['order'] = $order;

        // ✅ PDF: invoice.blade.php se banta hai
        $pdfView = view('emails.invoice', compact('order'))->render();
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($pdfView);

        // ✅ Save the PDF
        $invoiceNumber = $order->invoice_number;
        $pdfFilePath = storage_path('app/public/order-invoices.pdf');
        $mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);

        Mail::to($request->email)->queue(new InvoiceEmailManager($array, $pdfFilePath, '', $invoiceNumber));


        return response()->json([
            'id' => 1,
            'message' => 'Status updated and invoice sent successfully',
        ]);
    }


    public function saveProducts(Request $request)
    {
        $products = $request->input('products', []);
        $order_id = 0;

        foreach ($products as $product) {
            // Resolve the exact stock row so we save the correct variation/sku
            $stock = null;
            if (!empty($product['stock_id'])) {
                $stock = \App\Models\ProductStock::find($product['stock_id']);
            }

            // Fallback: match by product + variant string if no stock_id provided
            if (!$stock && !empty($product['product_id'])) {
                $stockQuery = \App\Models\ProductStock::where('product_id', $product['product_id']);
                if (!empty($product['variant'])) {
                    $stockQuery->where('variant', $product['variant']);
                }
                if (!empty($product['sku'])) {
                    $stockQuery->where('sku', $product['sku']);
                }
                $stock = $stockQuery->first();
            }

            $sku       = $stock?->sku ?? $product['sku'] ?? null;
            $variation = $stock?->variant ?? $product['variant'] ?? null;
            $linePrice = !empty($product['subtotal'])
                ? (float) $product['subtotal']
                : ((float) $product['price'] * (int) $product['quantity']);

            // If a detail with the same order + product + sku already exists, update it
            $existing = OrderDetail::where('order_id', $product['order_id'])
                ->where('product_id', $product['product_id'])
                ->when($sku, fn($q) => $q->where('sku', $sku))
                ->first();

            if ($existing) {
                $existing->quantity  += (int) $product['quantity'];
                $existing->price      = $existing->price + $linePrice;
                $existing->updated_at = now();
                $existing->save();
            } else {
                OrderDetail::insert([
                    'order_id'       => $product['order_id'],
                    'seller_id'      => 9,
                    'product_id'     => $product['product_id'],
                    'variation'      => $variation,
                    'sku'            => $sku,
                    'price'          => $linePrice,
                    'quantity'       => $product['quantity'],
                    'payment_status' => 'paid',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }

            $order_id = $product['order_id'];
        }

        $ordersDetails = OrderDetail::where('order_id', $order_id)->get();

        $price         = 0;
        $tax           = 0;
        $shipping_cost = 0;

        foreach ($ordersDetails as $value) {
            $price         += $value->price;
            $tax           += $value->tax;
            $shipping_cost += $value->shipping_cost;
        }

        $order                = Order::findOrFail($order_id);
        $coupon_discount      = $order->coupon_discount ?? 0;
        $order->total_tax     = ($price + $shipping_cost - $coupon_discount) * 20 / 100;
        $order->grand_total   = $price + $order->total_tax + $shipping_cost;
        $order->save();

        return response()->json(['success' => true]);
    }
    public function pending_orders(Request $request)
    {
        $date = $request->date;
        $search = $request->search;
        $payment_status = $request->payment_status;
        $delivery_status = $request->delivery_status;

        // Fetch orders with basic conditions
        $orders = Order::select('orders.*', 'customer_register_credit.company_name', 'users.name', 'users.last_name')
        ->leftjoin('customer_register_credit', 'orders.user_id', '=', 'customer_register_credit.user_id')
        ->leftjoin('users', 'orders.user_id', '=', 'users.id')->where('status', 0)->orderBy('created_at', 'desc');

        // Apply filters if provided
        if ($search) {
            $orders->where(function ($query) use ($search) {
                $query->where('post_code', 'like', '%' . $search . '%')
                    ->orWhere('delivery_name', 'like', '%' . $search . '%')
                    ->orWhere('purchase_order_number', 'like', '%' . $search . '%')
                    ->orWhere('invoice_number', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%')
                    ->orWhere('carrier_name', 'like', '%' . $search . '%')
                    ->orWhere('tracking_code', 'like', '%' . $search . '%')
                    ->orWhereHas('customer_details', function ($subQuery) use ($search) {
                        $subQuery->where('company_name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($payment_status) {
            $orders->where('payment_status', $payment_status);
        }

        if ($delivery_status) {
            $orders->where('delivery_status', $delivery_status);
        }

        if ($date) {
            $dates = explode(" to ", $date);
            $orders->whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dates[0])),
                date('Y-m-d 23:59:59', strtotime($dates[1]))
            ]);
        }

        // Order by post_code and code in descending order
        // $orders->orderBy('created_at', 'desc')
        //     ->orderByRaw('COALESCE(id, "") DESC');



        // Paginate the results
        $orders = $orders->latest()->paginate(15);

        // Return the view with data
        return view('backend.sales.pending_orders', compact('orders', 'search', 'payment_status', 'delivery_status', 'date'));
    }


    public function pending_orders_show($id)
    {
        $order = Order::findOrFail($id);
        $order_shipping_address = json_decode($order->shipping_address);
        $similar_orders = Order::where('post_code', $order->post_code)
            ->whereDate('created_at', Carbon::parse($order->created_at)->format('Y-m-d'))
            ->where('id', '!=', $order->id)
            ->orderBy('post_code')
            ->orderBy('created_at', 'asc')
            ->get();

        $delivery_boys = User::where('city', $order_shipping_address->city ?? '')
            ->where('user_type', 'delivery_boy')
            ->get();

        $order->viewed = 1;
        $order->save();
        return view('backend.sales.pending_show', compact('order',  'delivery_boys'));
    }

    //cancelled
    public function cancelled_orders(Request $request)
    {
        $date = $request->date;
        $search = $request->search;
        $payment_status = $request->payment_status;
        $delivery_status = $request->delivery_status;

        // Fetch orders with basic conditions
        // $orders = Order::where('status', operator: 10);

        $orders = Order::select('orders.*', 'customer_register_credit.company_name', 'users.name', 'users.last_name')
            ->leftjoin('customer_register_credit', 'orders.user_id', '=', 'customer_register_credit.user_id')
            ->leftjoin('users', 'orders.user_id', '=', 'users.id')
            ->orderBy('orders.created_at', 'desc')
            ->where('orders.status', 10);

        // Apply filters if provided
        if ($search) {
            $orders->where(function ($query) use ($search) {
                $query->where('orders.post_code', 'like', '%' . $search . '%')
                    ->orWhere('orders.delivery_name', 'like', '%' . $search . '%')
                    ->orWhere('orders.purchase_order_number', 'like', '%' . $search . '%')
                    ->orWhere('orders.invoice_number', 'like', '%' . $search . '%')
                    ->orWhere('orders.code', 'like', '%' . $search . '%')
                    ->orWhere('orders.carrier_name', 'like', '%' . $search . '%')
                    ->orWhere('orders.tracking_code', 'like', '%' . $search . '%')
                    ->orWhere('customer_register_credit.company_name', 'like', '%' . $search . '%')
                    ->orWhere('users.name', 'like', '%' . $search . '%')
                    ->orWhere('users.last_name', 'like', '%' . $search . '%');
            });
        }

        if ($payment_status) {
            $orders->where('payment_status', $payment_status);
        }

        if ($delivery_status) {
            $orders->where('delivery_status', $delivery_status);
        }

        if ($date) {
            $dates = explode(" to ", $date);
            $orders->whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dates[0])),
                date('Y-m-d 23:59:59', strtotime($dates[1]))
            ]);
        }

        // Order by post_code and code in descending order
        // $orders->orderBy('created_at', 'desc')
        //     ->orderByRaw('COALESCE(id, "") DESC');



        // Paginate the results
        $orders = $orders->latest()->paginate(15);

        // Return the view with data
        return view('backend.sales.cancelled_orders', compact('orders', 'search', 'payment_status', 'delivery_status', 'date'));
    }



    public function cancelled_orders_show($id)
    {
        $order = Order::findOrFail($id);
        $order_shipping_address = json_decode($order->shipping_address);
        $similar_orders = Order::where('post_code', $order->post_code)
            ->whereDate('created_at', Carbon::parse($order->created_at)->format('Y-m-d'))
            ->where('id', '!=', $order->id)
            ->orderBy('post_code')
            ->orderBy('created_at', 'asc')
            ->get();

        $delivery_boys = User::where('city', $order_shipping_address->city ?? '')
            ->where('user_type', 'delivery_boy')
            ->get();

        $order->viewed = 1;
        $order->save();
        return view('backend.sales.cancelled_show', compact('order',  'delivery_boys'));
    }
    
    
    

    // intenational order

    public function international_orders(Request $request)
    {
        $date = $request->date;
        $search = $request->search;
        $payment_status = $request->payment_status;
        $delivery_status = $request->delivery_status;

        $orders = Order::select('orders.*', 'customer_register_credit.company_name', 'users.name', 'users.last_name')
            ->leftjoin('customer_register_credit', 'orders.user_id', '=', 'customer_register_credit.user_id')
            ->leftjoin('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.status', 20)
            ->orderBy('orders.created_at', 'desc');


        if ($search) {
            $orders->where(function ($query) use ($search) {
                $query->where('post_code', 'like', '%' . $search . '%')
                    ->orWhere('delivery_name', 'like', '%' . $search . '%')
                    ->orWhere('purchase_order_number', 'like', '%' . $search . '%')
                    ->orWhere('invoice_number', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%')
                    ->orWhere('carrier_name', 'like', '%' . $search . '%')
                    ->orWhere('tracking_code', 'like', '%' . $search . '%')
                    ->orWhereHas('customer_details', function ($subQuery) use ($search) {
                        $subQuery->where('company_name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($payment_status) {
            $orders->where('payment_status', $payment_status);
        }

        if ($delivery_status) {
            $orders->where('delivery_status', $delivery_status);
        }

        if ($date) {
            $dates = explode(" to ", $date);
            $orders->whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dates[0])),
                date('Y-m-d 23:59:59', strtotime($dates[1]))
            ]);
        }

        $orders = $orders->latest()->paginate(15);

        return view('backend.sales.international_orders', compact('orders', 'search', 'payment_status', 'delivery_status', 'date'));
    }

    public function international_orders_show($id)
    {
        $order = Order::findOrFail($id);
        $order_shipping_address = json_decode($order->shipping_address);
        $similar_orders = Order::where('post_code', $order->post_code)
            ->whereDate('created_at', Carbon::parse($order->created_at)->format('Y-m-d'))
            ->where('id', '!=', $order->id)
            ->orderBy('post_code')
            ->orderBy('created_at', 'asc')
            ->get();

        $delivery_boys = User::where('city', $order_shipping_address->city ?? '')
            ->where('user_type', 'delivery_boy')
            ->get();

        $order->viewed = 1;
        $order->save();
        return view('backend.sales.international_order_show', compact('order',  'delivery_boys'));
    }


    public function updateChargePopup(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'type' => 'required|in:delivery,vat,discount',
                'value' => 'required|numeric|min:0',
            ]);

            $order = Order::findOrFail($request->order_id);

            if ($request->type === 'delivery') {
                foreach ($order->orderDetails as $orderDetail) {
                    $orderDetail->shipping_cost = $request->value / $order->orderDetails->count();
                    $orderDetail->save();
                }
            } elseif ($request->type === 'vat') {
                $order->total_tax = $request->value;
            } elseif ($request->type === 'discount') {
                $order->coupon_discount = $request->value;
            }

            $order->grand_total = $order->orderDetails->sum('price')
                + $order->orderDetails->sum("shipping_cost")
                + $order->total_tax
                - ($order->coupon_discount ?? 0);

            $order->save();

            return redirect()->back()->with('success', 'Order charges updated successfully.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating order charges. Please try again.');
        }
    }
}
