<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\CheckoutController;
use MyFatoorah\Library\PaymentMyfatoorahApiV2;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\SellerPackageController;
use App\Models\CombinedOrder;
use App\Models\CustomerPackage;
use App\Models\SellerPackage;
use Illuminate\Http\Request;
use Session;
use Redirect;

class MyfatoorahController extends Controller
{
    public $mfObj;
    /**
     * create MyFatoorah object
     */
    public function __construct() {
            // If you want to set the credentials and the mode manually.
            if (get_setting('myfatoorah_sandbox') == 1) {
                config(['myfatoorah.test_mode' => true]);
            } else {
                config(['myfatoorah.test_mode' => false]);
            }
        $this->mfObj = new PaymentMyfatoorahApiV2(config('myfatoorah.api_key'), config('myfatoorah.country_iso'), config('myfatoorah.test_mode'));
    }

    /**
     * Create MyFatoorah invoice
     *
     * @return \Illuminate\Http\Response
     */

    public function pay(Request $request)
    {
        if (Session::has('payment_type')) {
            $combined_order = CombinedOrder::findOrFail($request->session()->get('combined_order_id'));
            $amount = $combined_order->grand_total;
            $name = json_decode($combined_order->shipping_address)->name;
            $phone = preg_replace("/-/", "", json_decode($combined_order->shipping_address)->phone);
            $email = json_decode($combined_order->shipping_address)->email;

            if (Session::get('payment_type') == 'cart_payment') {
                $amount = $combined_order->grand_total;
            } elseif (Session::get('payment_type') == 'wallet_payment') {
                $amount = Session::get('payment_data')['amount'];
            } elseif (Session::get('payment_type') == 'customer_package_payment') {
                $customer_package = CustomerPackage::findOrFail(Session::get('payment_data')['customer_package_id']);
                $amount = $customer_package->amount;
            } elseif (Session::get('payment_type') == 'seller_package_payment') {
                $seller_package = SellerPackage::findOrFail(Session::get('payment_data')['seller_package_id']);
                $amount = $seller_package->amount;
            }
        }

        $currency_code = "USD";
        if (Session::has('currency_code')) {
            $currency_code = Session::get('currency_code');
        }
        $callbackURL = route('myfatoorah.callback');
        $data = [
            'CustomerName'       => $name,
            'InvoiceValue'       => $amount,
            'DisplayCurrencyIso' => $currency_code,
            'CustomerEmail'      => $email,
            'CallBackUrl'        => $callbackURL,
            'ErrorUrl'           => $callbackURL,
            'CustomerReference'  => $combined_order->id,
        ];
        try {
            $paymentMethodId = 0;
            $data            = $this->mfObj->getInvoiceURL($data, $paymentMethodId);
            if ($data['invoiceId']) {
                $checkoutUrl = $data['invoiceURL'];
                return Redirect::to($checkoutUrl);
            }
            flash(translate('Payment was failed'))->error();
            return back();
        } catch (\Exception $e) {
            // return  response()->json(['IsSuccess' => 'false', 'Message' => $e->getMessage()]);
            flash(translate('Payment was failed'))->error();
            return redirect()->route('home');
        }
    }

    /**
     * Get MyFatoorah payment information
     * 
     * @return \Illuminate\Http\Response
     */

    public function callback()
    {
        try {
            $data = $this->mfObj->getPaymentStatus(request('paymentId'), 'PaymentId');

            if ($data->InvoiceStatus == 'Paid') {

                $payment_type = Session::get('payment_type');
                if ($payment_type == 'cart_payment') {
                    $checkoutController = new CheckoutController;
                    return $checkoutController->checkout_done(session()->get('combined_order_id'), null);
                }
                if ($payment_type == 'wallet_payment') {
                    $walletController = new WalletController;
                    return $walletController->wallet_payment_done(session()->get('payment_data'), null);
                }
                if ($payment_type == 'customer_package_payment') {
                    $customer_package_controller = new CustomerPackageController;
                    return $customer_package_controller->purchase_payment_done(session()->get('payment_data'), null);
                }
                if ($payment_type == 'seller_package_payment') {
                    $seller_package_controller = new SellerPackageController;
                    return $seller_package_controller->purchase_payment_done(session()->get('payment_data'), null);
                }
            } else {
                flash(translate('Payment was failed'))->error();
                return redirect()->route('home');
            }
            return response()->json(['IsSuccess' => 'true', 'Message' => $msg, 'Data' => $data]);
        } catch (\Exception $e) {
            Session::forget('payment_data');
            flash(translate('Payment was failed'))->error();
            return redirect()->route('home');
        }
    }
}
