<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderDetail;
use App\Models\ProductStock;
use App\Models\Product;
use App\Models\Order;
use App\Models\City;
use App\Models\User;
use App\Models\Address;
use App\Models\Addon;
use App\Models\Accounts;
use App\Models\CustomerDetail;
use App\Models\CustomerProductsAssign;
use App\Models\ContactInformation;
use App\Models\DeliveryAddress;
use App\Models\CreditDelivery;

use Session;
use Auth;
use Mail;
use App\Mail\order_place;
use App\Mail\Order_verification;
use App\Http\Resources\PosProductCollection;
use App\Models\CombinedOrder;
use App\Models\Country;
use App\Models\State;
use App\Models\ShippingTerms;
use App\Utility\CategoryUtility;
use Illuminate\Support\Facades\Crypt;
use Log;

class PosController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:pos_manager'])->only('index');
        $this->middleware(['permission:pos_configuration'])->only('pos_activation');
    }

    public function index()
    {
        // $customers = CustomerDetail::all();
        // dd($customers);
        Session::forget('pos.shipping_info');
        Session::forget('pos.discount');
        Session::forget('pos.shipping');
        Session::forget('pos.discoun-t');
        Session::forget('pos.cart');
        Session::forget('customerId');
        Session::forget('pos.tax');
        Session::forget('custom_vat');
        Session::forget('is_custom_shipping_charges');
        $customers = User::whereIn('user_type', ['customer', 'customer_credit'])

            ->get();

        return view('pos.index', compact('customers'));
    }

    public function seller_index()
    {
        // $customers = User::where('user_type', 'customer')->where('email_verified_at', '!=', null)->orderBy('created_at', 'desc')->get();

        $customers = CustomerDetail::all();

        if (get_setting('pos_activation_for_seller') == 1) {
            return view('pos.frontend.seller.pos.index', compact('customers'));
        } else {
            flash(translate('POS is disable for Sellers!!!'))->error();
            return back();
        }
    }

    public function searchDeliveryAddress(Request $request)
    {
        $query = $request->input('query');
        $customer_id = $request->input('customer_id');

        $addresses = \App\Models\CreditDelivery::select('customer_delivery_address.*', 'countries.name as country_name')
            ->leftJoin('countries', 'countries.id', '=', 'customer_delivery_address.country')
            ->where('credit_id', $customer_id)
            ->where(function ($q) use ($query) {
                $q->where('delivery_name', 'LIKE', "%{$query}%")
                    ->orWhere('address1', 'LIKE', "%{$query}%")
                    ->orWhere('address2', 'LIKE', "%{$query}%")
                    ->orWhere('address3', 'LIKE', "%{$query}%")
                    ->orWhere('postcode', 'LIKE', "%{$query}%")
                    ->orWhere('city', 'LIKE', "%{$query}%")
                    ->orWhere('town', 'LIKE', "%{$query}%");
            })
            ->get();

        return response()->json($addresses);
    }

    public function searchShow(Request $request)
    {
        $keyword     = trim((string) $request->input('search', ''));
        $customer_id = $request->input('customer_id');

        if ($keyword === '') {
            return response()->json(['html' => '']);
        }

        // Base: published admin products with their stocks
        $query = ProductStock::query()
            ->select(
                'products.id',
                'products.name',
                'products.product_code',
                'product_stocks.id as stock_id',
                'product_stocks.variant',
                'product_stocks.sku',
                'product_stocks.pip_code',
                'product_stocks.qty as stock_qty',
                'product_stocks.image as stock_image',
                'product_stocks.price as stock_price'
            )
            ->join('products', 'product_stocks.product_id', '=', 'products.id')
            ->where('products.added_by', 'admin')
            ->where('products.published', 1)
            ->where(function ($q) use ($keyword) {
                $term = strtolower($keyword);
                $q->whereRaw('LOWER(products.name) like ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(products.product_code) like ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(product_stocks.sku) like ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(product_stocks.pip_code) like ?', ["%{$term}%"]);
            });

        // If a customer is selected, prefer their assigned price via LEFT JOIN
        if ($customer_id) {
            $query->addSelect('cpa.pack_price as customer_price')
                ->leftJoin('customers_products_assign as cpa', function ($join) use ($customer_id) {
                    $join->on('cpa.products_id', '=', 'products.id')
                        ->where('cpa.customer_detail_id', '=', $customer_id);
                });

            session()->put('customerId', $customer_id);
        }

        $results = $query->orderBy('products.name')->limit(20)->get();

        if ($results->isEmpty()) {
            return response()->json(['html' => '<tr><td colspan="3">No products found.</td></tr>']);
        }

        $output = '';
        foreach ($results as $product) {
            // Use customer-specific price if available, otherwise stock price
            $price = (!empty($product->customer_price) && $customer_id)
                ? $product->customer_price
                : $product->stock_price;

            $output .= '<tr>';
            $output .= '<td>' . htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8');
            if ($product->variant) {
                $output .= ' <small class="text-muted">(' . htmlspecialchars($product->variant, ENT_QUOTES, 'UTF-8') . ')</small>';
            }
            $output .= '</td>';
            $output .= '<td>' . single_price($price) . '</td>';
            $output .= '<td>';
            $output .= '<button class="btn btn-primary btn-sm add-product-btn"'
                . ' data-id="'       . (int) $product->id . '"'
                . ' data-stock-id="' . (int) $product->stock_id . '"'
                . ' data-name="'     . htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') . '"'
                . ' data-sku="'      . htmlspecialchars($product->sku ?? '', ENT_QUOTES, 'UTF-8') . '"'
                . ' data-variant="'  . htmlspecialchars($product->variant ?? '', ENT_QUOTES, 'UTF-8') . '"'
                . ' data-price="'    . (float) $price . '">'
                . 'Add</button>';
            $output .= '</td>';
            $output .= '</tr>';
        }

        return response()->json(['html' => $output]);
    }

    public function search(Request $request)
    {

        $products = ProductStock::select('products.*', 'product_stocks.id as stock_id', 'product_stocks.variant', 'product_stocks.price as stock_price', 'product_stocks.qty as stock_qty', 'product_stocks.image as stock_image')
            ->leftjoin('products', 'product_stocks.product_id', '=', 'products.id')
            ->where('products.added_by', 'admin')
            ->orderBy('products.created_at', 'desc');




        if ($request->keyword != null) {

            $products = $products->where(function ($query) use ($request) {
                $query->whereRaw('LOWER(products.name) like ?', ['%' . strtolower($request->keyword) . '%'])
                    ->orWhereRaw('LOWER(products.product_code) like ?', ['%' . strtolower($request->keyword) . '%'])
                    ->orWhereRaw('LOWER(product_stocks.sku) like ?', ['%' . strtolower($request->keyword) . '%'])
                    ->orWhereRaw('LOWER(product_stocks.pip_code) like ?', ['%' . strtolower($request->keyword) . '%']);
            });
        }

        // $p = $products->get();

        // dd($p);

        $stocks = new PosProductCollection($products->paginate(16));
        $stocks->appends(['keyword' =>  $request->keyword, 'category' => $request->category, 'brand' => $request->brand]);
        return $stocks;
    }

    public function addToCart(Request $request)
    {
        $stock = ProductStock::find($request->stock_id);
        $product = $stock->product;

        $data = array();
        $data['stock_id'] = $request->stock_id;
        $data['id'] = $product->id;
        $data['variant'] = $stock->variant;
        $data['quantity'] = $product->min_qty;

        if ($stock->qty < $product->min_qty) {
            return array('success' => 0, 'message' => translate("This product doesn't have enough stock for minimum purchase quantity ") . $product->min_qty, 'view' => view('pos.cart')->render());
        }

        $tax = 0;
        $cusotmer_price = CustomerProductsAssign::where('products_id', '=', $product->id)->where('customer_detail_id', '=', session()->get("customerId"))->first();

        $price = $stock->price;
        // $price = $cusotmer_price->stock_price;

        // discount calculation
        $discount_applicable = false;
        if ($product->discount_start_date == null) {
            $discount_applicable = true;
        } elseif (
            strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }
        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }
        }
        $customers = CustomerDetail::where('id', session()->get("customerId"))->where('status', '=', 1)->first();
        // $customers = CustomerDetail::where('id',session()->get("customerId"))->where('status','=',1)->first();

        //tax calculation
        foreach ($product->taxes as $product_tax) {

            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }
        // if ($customers->vat_rate == 1) {
        //         $tax += ($price * 20) / 100;

        //     }else{
        //         $tax += ($price * 0) / 100;
        //     }

        $data['price'] = $price;
        $data['tax'] = $tax;

        if ($request->session()->has('pos.cart')) {
            $foundInCart = false;
            $cart = collect();

            foreach ($request->session()->get('pos.cart') as $key => $cartItem) {
                if ($cartItem['id'] == $product->id && $cartItem['stock_id'] == $stock->id) {
                    $foundInCart = true;
                    $loop_product = Product::find($cartItem['id']);
                    $product_stock = $loop_product->stocks->where('variant', $cartItem['variant'])->first();

                    if ($product_stock->qty >= ($cartItem['quantity'] + 1)) {
                        $cartItem['quantity'] += 1;
                    } else {
                        return array('success' => 0, 'message' => translate("This product doesn't have more stock."), 'view' => view('pos.cart')->render());
                    }
                }
                $cart->push($cartItem);
            }

            if (!$foundInCart) {
                $cart->push($data);
            }
            $request->session()->put('pos.cart', $cart);
        } else {
            $cart = collect([$data]);
            $request->session()->put('pos.cart', $cart);
        }

        $request->session()->put('pos.cart', $cart);

        return array('success' => 1, 'message' => '', 'view' => view('pos.cart', compact('tax'))->render());
    }

    //updated the quantity for a cart item
    public function updateQuantity(Request $request)
    {
        $cart = $request->session()->get('pos.cart', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if ($key == $request->key) {
                $product = Product::find($object['id']);
                $product_stock = $product->stocks->where('id', $object['stock_id'])->first();

                if ($product_stock->qty >= $request->quantity) {
                    $object['quantity'] = $request->quantity;
                } else {
                    return array('success' => 0, 'message' => translate("This product doesn't have more stock."), 'view' => view('pos.cart')->render());
                }
            }
            return $object;
        });
        $request->session()->put('pos.cart', $cart);
        return array('success' => 1, 'message' => '', 'view' => view('pos.cart')->render());
    }

    //removes from Cart
    public function removeFromCart(Request $request)
    {
        if (Session::has('pos.cart')) {
            $cart = Session::get('pos.cart', collect([]));
            $cart->forget($request->key);
            Session::put('pos.cart', $cart);

            $request->session()->put('pos.cart', $cart);
        }

        return view('pos.cart');
    }

    //Shipping Address for admin
    public function getShippingAddress(Request $request)
    {
        $user_id = $request->customer_id;
        session()->put('customerId', $user_id);
        // dd($user_id);
        return view('pos.shipping_address', compact('user_id'));
    }

    //Shipping Address for seller
    public function getShippingAddressForSeller(Request $request)
    {
        $user_id = $request->id;
        if ($user_id == '') {
            return view('pos.frontend.seller.pos.guest_shipping_address');
        } else {
            return view('pos.frontend.seller.pos.shipping_address', compact('user_id'));
        }
    }

    public function set_shipping_address(Request $request)
    {


        if (session()->has('customerId')) {
            $contact = User::where("id", "=", session()->get("customerId"))->first();
            // $company = RegisterCredit::where("user_id","=",session()->get("customerId"))->first();
            $address = CreditDelivery::where("credit_id", "=", $contact->id)->first();
            $data['name'] = $contact->name;
            $data['email'] = $contact?->email;
            $data['address'] = $address->address1 . "\n" . $address->address2 . "\n" . $address->address3;
            $data['country'] = $address->country;
            $data['state'] = $address->county;
            $data['city'] = $address->city . ' - ' . $address->town;
            $data['postal_code'] = $address->post_code;
            // $data['delivery_name'] = $address->delivery_name;
            $data['phone'] = $contact?->phone_number;
        } else {
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['address'] = $request->address;
            $data['country'] = $request->country;
            $data['state'] = $request->county;
            $data['city'] = $request->city;
            // $data['country'] = Country::find($request->country_id)->name;
            // $data['state'] = State::find($request->state_id)->name;
            // $data['city'] = City::find($request->city_id)->name;
            $data['postal_code'] = $request->post_code;
            $data['phone'] = $request->phone_number;
        }
        //  dd($data);
        $shipping_info = $data;
        $request->session()->put('pos.shipping_info', $shipping_info);
    }

    //set Discount
    public function setDiscount(Request $request)
    {
        if ($request->discount >= 0) {
            Session::put('pos.discount', $request->discount);
            $shipping = Session::get('pos.shipping');
            $vat = Session::get('pos.tax');
        }

        return view('pos.cart', compact('shipping', 'vat'));
    }

    //set Shipping Cost
    public function setShipping(Request $request)
    {
        if ($request->shipping != null) {
            Session::forget('pos.shipping');
            Session::put('pos.shipping', $request->shipping);
        }

        if ($request->is_custom_shipping_charges == true) {
            Session::put('is_custom_shipping_charges', true);
        }
        return view('pos.cart');
    }
    public function setVat(Request $request)
    {
        if ($request->vat != null) {
            Session::forget('pos.tax');
            Session::put('pos.tax', $request->vat);
        }

        $vat = Session::get('pos.tax');
        $shipping = Session::get('pos.shipping');

        return view('pos.cart', compact('vat', 'shipping'));
    }
    //order summary
    public function get_order_summary(Request $request)
    {
        return view('pos.order_summary');
    }

    //order place
    public function order_store(Request $request)
    {
        if (Session::get('pos.shipping_info') == null) {
            return array('success' => 0, 'message' => translate("Please Add Shipping Information."));
        }
        if (empty($request->purchase_number)) {
            return array('success' => 0, 'message' => "Purchase Number is Required!");
        }
        if (Session::has('pos.cart') && count(Session::get('pos.cart')) > 0) {
            $order = new Order;

            $shipping_info = Session::get('pos.shipping_info');
            if ($request->user_id == null) {
                $order->guest_id    = mt_rand(100000, 999999);
            } else {
                $order->user_id = $request->user_id;
            }
            $data['name']           = $shipping_info['name'];
            $data['email']          = $shipping_info['email'];
            $data['address']        = $shipping_info['address'];
            $data['country']        = $shipping_info['country'];
            $data['city']           = $shipping_info['city'];
            $data['postal_code']    = $shipping_info['postal_code'];
            $data['phone']          = $shipping_info['phone'];
            //$order->delivery_name = $shipping_info['delivery_name'];
            $order->shipping_address = json_encode($data);

            $order->user_id = session()->get("customerId");
            $customerDetail = User::find($order->user_id);


            $order->post_code = $shipping_info['postal_code'];

            $order->payment_type = $request->payment_type;
            $order->delivery_viewed = '0';
            $order->payment_status_viewed = '0';
            $combined_order = new CombinedOrder();
            $combined_order->user_id = $customerDetail->id;
            $combined_order->shipping_address = json_encode($data);
            $combined_order->save();


            // Get the last order's code to determine the counter
            $lastOrder = Order::latest('id')->first();
            $lastOrderCount = Order::latest('id')->count();
            $lastCounter = $lastOrderCount == 0 ? 0 : $lastOrder->id;

            // Increment the counter
            $newCounter = str_pad($lastCounter + 99, 5, '0', STR_PAD_LEFT); // Ensures counter has leading zeros, e.g. 00001, 00002, ...

            // Get the current month and year
            $month = date('m'); // Month as two digits
            $year = date('y');  // Last two digits of the year

            // Construct the order code
            $orderCode = "{$newCounter}M{$month}Y{$year}";

            // Set the order code and invoice number
            $order->code = $request->order_id; // Assuming you're getting the order_id from the request
            $order->invoice_number = "MDS-" . $newCounter; // This will give the format "GXM-00001"


            // Get the last order's code to determine the counter
            // $lastOrder = Order::latest('id')->first();
            // $lastCounter = $lastOrder == null?1:$lastOrder->id;


            // dd($lastCounter);

            // Increment the counter
            // $newCounter = str_pad($lastCounter + 1, 5, '0', STR_PAD_LEFT);

            // Get the current month and year
            // $month = date('m');
            // $year = date('y');

            // Construct the new order code
            // $orderCode = "{$newCounter}M{$month}Y{$year}";


            // Combine to create the order code
            // $order->code = $request->order_id;
            // $order->invoice_number = "GXM-".$orderCode;

            // Example: 00001M01Y25


            // $order->code = date('Ymd-His').rand(10,99);
            $order->date = strtotime('now');
            $order->payment_status = Session::get('pos.shipping_info')['country'] === 'United Kingdom' ? 'paid' : 'unpaid';
            $order->payment_details = $request->payment_type;
            $order->purchase_order_number = $request->purchase_number;
            $order->combined_order_id = $combined_order->id;
            // dd($request->pharmaceutical_checkbox);

            $order->is_pharmaceutical = $request->pharmaceutical_checkbox == 1 ? '1' : '0';
            // dd($order->is_pharmaceutical);


            if ($request->payment_type == 'offline_payment') {
                if ($request->offline_trx_id == null) {
                    return array('success' => 0, 'message' => translate("Transaction ID can not be null."));
                }
                $data['name']   = $request->offline_payment_method;
                $data['amount'] = $request->offline_payment_amount;
                $data['trx_id'] = $request->offline_trx_id;
                $data['photo']  = $request->offline_payment_proof;
                $order->manual_payment_data = json_encode($data);
                $order->manual_payment = 1;
            }


            $shipping_info = Session::get('pos.shipping_info');


            if ($order->save()) {
                // if ($customerDetail->user_type == 'customer') {
                //     $customerIdEncoded = base64_encode($customerDetail->id);
                //     $orderIdEncoded = base64_encode($order->id);
                //     Log::info(("Customer Id : " . $customerDetail->id . " Order Id : " . $order->id));
                //     $verificationUrl = "https://mydoctorshop.com/payment-order?user_id=" . $customerIdEncoded . "&order_id=" . $orderIdEncoded;


                //     try {
                //         Mail::to($customerDetail->email)->send(new Order_verification($verificationUrl));
                //     } catch (\Exception $e) {
                //         \Log::error("Failed to send verification email", [
                //             'email' => $customerDetail->email,
                //             'error' => $e->getMessage(),
                //         ]);
                //     }
                // }
                $subtotal = 0;
                $tax = 0;

                $session_shipping_cost = Session::get('pos.shipping', 0);
                $session_cart = Session::get('pos.cart');
                $session_total_cart = count($session_cart);
                $per_shipping_cost = floor(($session_shipping_cost / $session_total_cart) * 100) / 100;
                $remaining_shipping_cost = $session_shipping_cost;

                foreach (Session::get('pos.cart') as $key => $cartItem) {
                    $product_stock = ProductStock::find($cartItem['stock_id']);
                    $product = $product_stock->product;
                    $product_variation = $product_stock->variant;

                    $subtotal += $cartItem['price'] * $cartItem['quantity'];
                    $tax += $cartItem['tax'] * $cartItem['quantity'];


                    if ($cartItem['quantity'] > $product_stock->qty) {
                        $order->delete();
                        return array('success' => 0, 'message' => $product->name . ' (' . $product_variation . ') ' . translate(" just stock outs."));
                    } else {
                        $product_stock->qty -= $cartItem['quantity'];
                        $product_stock->save();
                    }

                    $order_detail = new OrderDetail;
                    $order_detail->order_id  = $order->id;
                    $order_detail->seller_id = $product->user_id;
                    $order_detail->product_id = $product->id;
                    $order_detail->sku = $product_stock->sku;

                    // $order_detail->payment_status = $request->payment_type != 'cash_on_delivery' ? 'paid' : 'unpaid';
                    $order_detail->payment_status = Session::get('pos.shipping_info')['country'] === 'United Kingdom' ? 'paid' : 'unpaid';
                    $order_detail->variation = $product_variation;
                    $order_detail->price = $cartItem['price'] * $cartItem['quantity'];
                    // $order_detail->tax = $cartItem['tax'] * $cartItem['quantity'];
                    // $order_detail->tax = Session::get('pos.tax', 0) * $cartItem['quantity'];
                    $order_detail->tax = Session::get('pos.tax', 0);
                    $order_detail->quantity = $cartItem['quantity'];
                    $order_detail->picked_qty = 0;
                    $order_detail->shipping_type = null;

                    // if (Session::get('pos.shipping', 0) >= 0) {
                    //     $order_detail->shipping_cost = Session::get('pos.shipping', 0) / count();
                    // } else {
                    //     $order_detail->shipping_cost = 0;
                    // }

                    //Handle shipping cost
                    if ($key == $session_total_cart - 1) {
                        $order_detail->shipping_cost = round($remaining_shipping_cost, 2);
                    } else {
                        $order_detail->shipping_cost = $per_shipping_cost;
                        $remaining_shipping_cost -= $per_shipping_cost;
                    }

                    $order_detail->save();

                    $product->num_of_sale++;
                    $product->save();
                }


                // if (!empty($order->tax)) {
                // $total_tax = (($subtotal + (Session::get('pos.shipping', 0) ?? 0) - (Session::get('pos.discount') ?? 0)) * 20 / 100);
                // $total_tax = Session::get('pos.tax', 0) * $cartItem['quantity'];
                $total_tax = Session::get('pos.tax', 0);
                $order->total_tax =  $total_tax;
                // }
                $order->grand_total = $subtotal + $total_tax + Session::get('pos.shipping', 0);
                if ($request->offline_payment_amount > $order->grand_total) {
                    return array('success' => 0, 'message' => "Amount should be equal or less than total amounts.");
                }
                $shipping = ShippingTerms::where('customer_detail_id', '=', session()->get("customerId"))->first();
                $order_value = $shipping->order_value ?? 0;
                if ((int) $order_value >= $order->grand_total) {
                    return array('success' => 0, 'message' => "Order Value should be equal or greater than minimum order " . $shipping->order_value . " amounts.");
                }

                if (Session::has('pos.discount')) {
                    $order->grand_total -= Session::get('pos.discount');
                    $order->coupon_discount = Session::get('pos.discount');
                }
                $order->seller_id = $product->user_id;
                $order->save();

                // Save grand total in combined order
                $combined_order->grand_total = $order->grand_total;
                $combined_order->payment_status = Session::get('pos.shipping_info')['country'] === 'United Kingdom' ? 'paid' : 'unpaid';
                $combined_order->save();

                $account = new Accounts();
                $account->order_id = $order->id;
                $account->customer_detail_id = session()->get("customerId");
                $account->debit = $order->grand_total;
                $account->credit = 0;
                $account->due_date = $order->user->user_type == 'customer_credit' ?  date("Y-m-d", strtotime($order->created_at->addDays(40))) : $order->created_at;
                $account->save();

                $array['view'] = 'emails.order_confirmation';
                $array['subject'] = 'Your order has been placed - ' . $order->code;
                $array['from'] = env('MAIL_USERNAME');
                $array['order'] = $order;

                $admin_products = array();
                $seller_products = array();

                foreach ($order->orderDetails as $key => $orderDetail) {
                    if ($orderDetail->product->added_by == 'admin') {
                        array_push($admin_products, $orderDetail->product->id);
                    } else {
                        $product_ids = array();
                        if (array_key_exists($orderDetail->product->user_id, $seller_products)) {
                            $product_ids = $seller_products[$orderDetail->product->user_id];
                        }
                        array_push($product_ids, $orderDetail->product->id);
                        $seller_products[$orderDetail->product->user_id] = $product_ids;
                    }
                }

                foreach ($seller_products as $key => $seller_product) {
                    try {
                    } catch (\Exception $e) {
                    }
                }


                // $email = $request->email ?? ($shipping_info['email'] ?? null);

                // if (!empty($email)) {
                //     try {
                //         Mail::to($email)->queue(new order_place($array));
                //     } catch (\Exception $e) {
                //         \Log::error("Failed to send order confirmation email", [
                //             'email' => $email,
                //             'error' => $e->getMessage(),
                //         ]);
                //     }
                // }

                if ($customerDetail->user_type === 'customer') {
                    $isInternational = Session::get('pos.shipping_info')['country'] !== 'United Kingdom';
                    $verificationUrl = '';

                    if ($isInternational) {
                        $payload = [
                            'user_id' => $customerDetail->id,
                            'order_id' => $order->id,
                        ];

                        $token = Crypt::encryptString(json_encode($payload));
                        $verificationUrl = rtrim(env('FRONTEND_URL', config('app.url')), '/') . '/payment-order?token=' . $token;
                    }

                    try {
                        Mail::to($customerDetail->email)->send(new Order_verification($verificationUrl, $order));
                    } catch (\Exception $e) {
                        \Log::error("Failed to send verification email", [
                            'email' => $customerDetail->email,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }


                Session::forget('pos.shipping_info');
                Session::forget('pos.shipping');
                Session::forget('pos.discount');
                Session::forget('pos.cart');
                Session::forget('pos.tax');
                Session::forget('customerId');
                Session::forget('is_custom_shipping_charges');
                return array('success' => 1, 'message' => translate('Order Completed Successfully.'));
            } else {
                return array('success' => 0, 'message' => translate('Please input customer information.'));
            }
        }
        return array('success' => 0, 'message' => translate("Please select a product."));
    }

    public function pos_activation()
    {
        return view('pos.pos_activation');
    }
}
