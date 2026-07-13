<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Country;
use App\Models\LoginForm;
use App\Models\HeadOffice;
use Illuminate\Http\Request;
use App\Models\ShippingTerms;
use App\Models\AccountPayable;
use App\Models\CustomerDetail;
use App\Models\RegisterOffice;
use App\Models\DeliveryAddress;
use App\Models\ContactInformation;
use Illuminate\Support\Facades\Hash;

class commercialCustomerController extends Controller
{
  public function commercial_account_index()
  {
    $customer = CustomerDetail::where("status",'=',1)->get();
    
    $customerDelete=CustomerDetail::where('status','=','0' )
    ->with('headOffice')
    ->with('registerOffice')
    ->with('deliveryAddress')
    ->with('contactInformation')
    ->with('shippingTerms')
    ->with('accountPayable')->delete();

    return view('backend.customer.customer_commercial_account.commercial_account_index', compact('customer'));
  }
  public function commercialDestroy($id)
  {
      $customer = CustomerDetail::with([
          'headOffice',
          'registerOffice',
          'deliveryAddress',
          'contactInformation',
          'shippingTerms',
          'accountPayable'
      ])->find($id);
  
      if ($customer) {
          // Delete related records first
          $customer->headOffice()->delete();
          $customer->registerOffice()->delete();
          $customer->deliveryAddress()->delete();
          $customer->contactInformation()->delete();
          $customer->shippingTerms()->delete();
          $customer->accountPayable()->delete();
  
          // Now delete the customer record
          $customer->delete();
      }
  
      return redirect()->route('commercial_account_index');
  }
  

  public function commercial_account_view($id)
  {
    $customerDetail = CustomerDetail::where('id', $id)->first();
    $customerDelivaryDetail = DeliveryAddress::where('customer_detail_id', $id)->get();
    return view('backend.customer.customer_commercial_account.customer_commercial_view', compact('customerDetail', 'customerDelivaryDetail'));
  }
  public function commercial_account($id = null)
  {
    // $customerDetailId = session()->get('customer_detail_id');
      session()->forget('customer_detail_id');

    $data = [
      'customerDetail' => null, // Initialize as null
      'country' => Country::all(),
      // 'customer'=>DeliveryAddress ::where('customer_detail_id', $customerDetailId)->get(),
    ];

    if (!empty($id)) {
      $data['customerDetail'] = CustomerDetail::where('id', $id)->first();
    }
    return view('backend.customer.customer_commercial_account.customer_commercial_account')->with($data);
  }

  public function storeCustomerDetails(Request $request)
  {
    $customerDetailId = $request->input('customer_id');
    $sessionCustomerDetailId = session()->get('customer_detail_id');
    if ($customerDetailId) {
      $customerDetail = CustomerDetail::find($customerDetailId);
    } elseif ($sessionCustomerDetailId) {
      $customerDetail = CustomerDetail::find($sessionCustomerDetailId);
    }
    if (isset($customerDetail)) {
      $customerDetail->company_name = $request->input('Cname');
      $customerDetail->company_type = $request->input('company_type');
      $customerDetail->account_type = $request->input('account_type');
      $customerDetail->business_structure = $request->input('business_structure');
      $customerDetail->currency = $request->input('currency');
      $customerDetail->vat_rate = $request->input('vat_rate');
      $customerDetail->update();
      $message = 'Customer details updated successfully!';
    } else {
      $customerDetail1 = new CustomerDetail();
       $customerDetail1->company_name = $request->input('Cname');
      $customerDetail1->company_type = $request->input('company_type');
      $customerDetail1->account_type = $request->input('account_type');
      $customerDetail1->business_structure = $request->input('business_structure');
      $customerDetail1->currency = $request->input('currency');
      $customerDetail1->vat_rate = $request->input('vat_rate');
      $customerDetail1->status = '0'; // Default status is 0
      $customerDetail1->save();
      session()->put("customer_detail_id", $customerDetail1->id);
      $message = 'Customer details saved successfully!';
    }
    // You can store the customer_detail_id in session or return it
    return response()->json([
      'message' => $message,
    ]);
  }

  public function storeHeadOffice(Request $request)
  {

    $customerDetailId = $request->input('customer_id');

    $sessionHeadOffice = session()->get('customer_detail_id');
    if ($customerDetailId) {
      $headOffice = HeadOffice::where('customer_detail_id', $customerDetailId)->first();
    } elseif ($sessionHeadOffice) {
      $headOffice = HeadOffice::where('customer_detail_id', $sessionHeadOffice)->first();
    }


    if (isset($headOffice)) {

      $headOffice->postcode = $request->input('postcode');
      $headOffice->address1 = $request->input('address1');
      $headOffice->address2 = $request->input('address2');
      $headOffice->address3 = $request->input('address3');
      $headOffice->town = $request->input('town');
      $headOffice->city = $request->input('city');
      $headOffice->county = $request->input('county');
      $headOffice->country = $request->input('country');
      $headOffice->save();
      $message = 'Head Office updated successfully!';
    } else {
      // dd($request->input('postcode'));
      $headOffice = new HeadOffice();
      $headOffice->customer_detail_id = session()->get("customer_detail_id");
      $headOffice->postcode = $request->input('postcode');
      $headOffice->address1 = $request->input('address1');
      $headOffice->address2 = $request->input('address2');
      $headOffice->address3 = $request->input('address3');
      $headOffice->town = $request->input('town');
      $headOffice->city = $request->input('city');
      $headOffice->county = $request->input('county');
      $headOffice->country = $request->input('country');
      $headOffice->save();
      $message = 'Head Office   saved successfully!';
    }
    // session()->put("customer_detail_id", $headOffice->id);

    return response()->json([
      'id' => $headOffice->id,
      'message' => $message,
    ]);
    // Return success response
  }
  public function storeRegisterOffice(Request $request)
  {

    $customerDetailId = $request->input('customer_id');

    $registerSessionOffice = session()->get('customer_detail_id');
    if ($customerDetailId) {
      $RegisterOffice = RegisterOffice::where('customer_detail_id', $customerDetailId)->first();
    } elseif ($registerSessionOffice) {
      $RegisterOffice = RegisterOffice::where('customer_detail_id', $registerSessionOffice)->first();
    }
    if (isset($RegisterOffice)) {
      $RegisterOffice->postcode = $request->input('postcode');
      $RegisterOffice->address1 = $request->input('address1');
      $RegisterOffice->address2 = $request->input('address2');
      $RegisterOffice->address3 = $request->input('address3');
      $RegisterOffice->town = $request->input('town');
      $RegisterOffice->city = $request->input('city');
      $RegisterOffice->county = $request->input('county');
      $RegisterOffice->country = $request->input('country');
      $RegisterOffice->update();
      $message = ' Office updated successfully!';
    } else {


      $RegisterOffice = new RegisterOffice();
      $RegisterOffice->customer_detail_id = session()->get("customer_detail_id");
      $RegisterOffice->postcode = $request->input('postcode');
      $RegisterOffice->address1 = $request->input('address1');
      $RegisterOffice->address2 = $request->input('address2');
      $RegisterOffice->address3 = $request->input('address3');
      $RegisterOffice->town = $request->input('town');
      $RegisterOffice->city = $request->input('city');
      $RegisterOffice->county = $request->input('county');
      $RegisterOffice->country = $request->input('country');
      $RegisterOffice->save();
    }

    // session()->put("customer_detail_id", $RegisterOffice->id);

    // Return success response
    return response()->json(['id' => $RegisterOffice->id, 'message' => 'Register office saved successfully!']);
  }
  public function storeDeliveryAddress(Request $request,)
  {
    $customerDetailId = $request->input('customer_id');

    // $deliverySessionOffice = session()->get('customer_detail_id');
    if ($customerDetailId) {
      $DeliveryAddress = DeliveryAddress::where('customer_detail_id', $customerDetailId)->first();
     } //elseif ($deliverySessionOffice) {
    //   $DeliveryAddress = DeliveryAddress::where('customer_detail_id', $deliverySessionOffice)->first();
    // }
    if (!session()->has("customer_detail_id")) {
      // dd("update");
      $DeliveryAddress = new DeliveryAddress();
      $DeliveryAddress->customer_detail_id = $customerDetailId;
      $DeliveryAddress->delivery_name = $request->input('delivery_name');
      $DeliveryAddress->postcode = $request->input('postcode');
      $DeliveryAddress->address1 = $request->input('address1');
      $DeliveryAddress->address2 = $request->input('address2');
      $DeliveryAddress->address3 = $request->input('address3');
      $DeliveryAddress->town = $request->input('town');
      $DeliveryAddress->city = $request->input('city');
      $DeliveryAddress->county = $request->input('county');
      $DeliveryAddress->country = $request->input('country');
      $DeliveryAddress->save();
    } else {

      // dd("save");
      $DeliveryAddress = new DeliveryAddress();
      $DeliveryAddress->customer_detail_id = session()->get("customer_detail_id");
      $DeliveryAddress->delivery_name = $request->input('delivery_name');
      $DeliveryAddress->postcode = $request->input('postcode');
      $DeliveryAddress->address1 = $request->input('address1');
      $DeliveryAddress->address2 = $request->input('address2');
      $DeliveryAddress->address3 = $request->input('address3');
      $DeliveryAddress->town = $request->input('town');
      $DeliveryAddress->city = $request->input('city');
      $DeliveryAddress->county = $request->input('county');
      $DeliveryAddress->country = $request->input('country');
      $DeliveryAddress->save();
    }
  
    
    // $id = session()->get("customer_detail_id");
    // session()->put("customer_detail_id", $DeliveryAddress->id);




    // Return success response
    return response()->json([
      'id' =>  $DeliveryAddress->id,
      'name' =>  $DeliveryAddress->delivery_name,
      'postcode' => $DeliveryAddress->postcode,
      'address1' => $DeliveryAddress->address1,
      'town' => $DeliveryAddress->town,
      'city' => $DeliveryAddress->city,
      'county' => $DeliveryAddress->county,
      'country' => $DeliveryAddress->country,
      'message' => 'Delivery Address saved successfully!'
    ]);
  }
  public function deliveryDestroy(Request $request)
  {
    $deliveryAddressId = $request->input('deliveryAddressId');
    $deliveryAddress = DeliveryAddress::find($deliveryAddressId);

    if (!$deliveryAddress) {
      return response()->json(['message' => 'Delivery address not found.'], 404);
    }

    $deliveryAddress->delete();

    return response()->json(['message' => 'Delivery address deleted successfully.'], 200);
  }


  public function storeContactInformation(Request $request)
  {
    $customerDetailId = $request->input('customer_id');

    $contactSessionOffice = session()->get('customer_detail_id');

    if ($customerDetailId) {
      $ContactInformation = ContactInformation::where('customer_detail_id', $customerDetailId)->first();
    } elseif ($contactSessionOffice) {
      $ContactInformation = ContactInformation::where('customer_detail_id', $contactSessionOffice)->first();
    }
    if (isset($ContactInformation)) {
      $ContactInformation->first_name = $request->input('firstName');
      $ContactInformation->last_name = $request->input('lastName');
      $ContactInformation->email = $request->input('contact_email');
      $ContactInformation->office_number = $request->input('officeNumber');
      $ContactInformation->mobile_number = $request->input('mobileNumber');

      $ContactInformation->save();
    } else {
      $ContactInformation = new ContactInformation();
      $ContactInformation->customer_detail_id = session()->get("customer_detail_id");
      $ContactInformation->first_name = $request->input('firstName');
      $ContactInformation->last_name = $request->input('lastName');
      $email = $request->input('contact_email');
      $existingContact = ContactInformation::where('email', $email)->first();

      if ($existingContact) {
        return response()->json(['message' => 'Email already exists. Please use a different email address.'], 400);
      }
      $ContactInformation->email = $email;

      $ContactInformation->office_number = $request->input('officeNumber');
      $ContactInformation->mobile_number = $request->input('mobileNumber');

      $ContactInformation->save();
    }


    // Return success response
    return response()->json(['id' => $ContactInformation->id, 'message' => 'Contact Information saved successfully!']);
  }
  public function storeLogin(Request $request)
  {


    $contactSessionOffice = session()->get('customer_detail_id');


    if ($contactSessionOffice) {
      $LoginCustomer = CustomerDetail::where('user_id', $contactSessionOffice)->get();
      $Loginid = user::where('uid', $LoginCustomer->id)->get();
    }
    if ($Loginid) {
      $Loginid->name = $request->input('name');
      $Loginid->email = $request->input('email');
      $Loginid->user_type = 'commercialcustomer';
      $Loginid->password = Hash::make($request->input('password'));
      $Loginid->save();
    } else {
      $LoginCustomer = new User();
      $LoginCustomer->name = $request->input('name');
      $email = $request->input('email');
      $existingContact = User::where('email', $email)->first();

      if ($existingContact) {
        return response()->json(['message' => 'Email already exists. Please use a different email address.'], 400);
      }
      $LoginCustomer->email = $email;
      $LoginCustomer->user_type = 'commercialcustomer';
      $LoginCustomer->password = Hash::make($request->input('password'));


      $LoginCustomer->save();


      $id = $LoginCustomer->id;

      $customerDetail =  CustomerDetail::find(session()->get("customer_detail_id"));
      $customerDetail->user_id = $id;

      $customerDetail->save();
    }

    // Return success response
    return response()->json([$id => $LoginCustomer->id, 'message' => 'Customer Login saved successfully!']);
  }

  public function storeShippingTerm(Request $request)
  {
    $customerDetailId = $request->input('customer_id');

    $shippingSessionOffice = session()->get('customer_detail_id');

    if ($customerDetailId) {
      $shippingTerms = ShippingTerms::where('customer_detail_id', $customerDetailId)->first();
    } elseif ($shippingSessionOffice) {
      $shippingTerms = ShippingTerms::where('customer_detail_id', $shippingSessionOffice)->first();
    }
    if (isset($shippingTerms)) {
      $shippingTerms->order_value = $request->input('order_value');
      $shippingTerms->delivary_charges = $request->input('delivary_charges');
      $shippingTerms->international_shipping_term = $request->input('internationalShipping');
      $shippingTerms->save();
    } else {
      $shippingTerms = new ShippingTerms();
      $shippingTerms->customer_detail_id = session()->get("customer_detail_id");
      $shippingTerms->order_value = $request->input('order_value');
      $shippingTerms->delivary_charges = $request->input('delivary_charges');
      $shippingTerms->international_shipping_term = $request->input('internationalShipping');



      $shippingTerms->save();
    }





    // $id = session()->get("customer_detail_id");




    // Return success response
    return response()->json(['id' => $shippingTerms->id, 'message' => 'Shipping Terms saved successfully!']);
  }
  public function storeAccountPayable(Request $request)
  {
    $customerDetailId = $request->input('customer_id');

    $payableSessionOffice = session()->get('customer_detail_id');


    if ($customerDetailId) {
      $account_Payable = AccountPayable::where('customer_detail_id', $customerDetailId)->first();
    } elseif ($payableSessionOffice) {
      $account_Payable = AccountPayable::where('customer_detail_id', $payableSessionOffice)->first();
    }
    if (isset($account_Payable)) {

      $account_Payable->first_name = $request->input('firstName');
      $account_Payable->last_name = $request->input('lastName');
      $account_Payable->email = $request->input('contact_email');
      $account_Payable->office_number = $request->input('officeNumber');
      $account_Payable->mobile_number = $request->input('mobileNumber');
      $account_Payable->confirmation_email = $request->input("confirmationEmail");
      $account_Payable->statement_email = $request->input('statementEmail');

      // Save address details
      $account_Payable->post_code = $request->input('postcode');
      $account_Payable->address1 = $request->input('address1');
      $account_Payable->address2 = $request->input('address2');
      $account_Payable->town = $request->input('town');
      $account_Payable->city = $request->input('city');
      $account_Payable->country = $request->input('country');
      // 


      $account_Payable->account_name = $request->input('accountName');
      $account_Payable->bank_name = $request->input('bankName');
      $account_Payable->short_code = $request->input('shortCode');
      $account_Payable->account_number = $request->input('accountNumber');
      $account_Payable->iban = $request->input('iban');
      $account_Payable->swift_code = $request->input('swiftCode');

      $account_Payable->save();
      $message = ' Completed Data Update successfully!';
    } else {


      $account_Payable = new AccountPayable();
      $account_Payable->customer_detail_id = session()->get("customer_detail_id");
      $account_Payable->first_name = $request->input('firstName');
      $account_Payable->last_name = $request->input('lastName');
      $email = $request->input('contact_email');

      
      $account_Payable->email = $email;
      $account_Payable->office_number = $request->input('officeNumber');
      $account_Payable->mobile_number = $request->input('mobileNumber');
      $account_Payable->confirmation_email = $request->input("confirmationEmail");
      $account_Payable->statement_email = $request->input('statementEmail');

      // Save address details
      $account_Payable->post_code = $request->input('postcode');
      $account_Payable->address1 = $request->input('address1');
      $account_Payable->address2 = $request->input('address2');
      $account_Payable->town = $request->input('town');
      $account_Payable->city = $request->input('city');
      $account_Payable->country = $request->input('country');
      // 


      $account_Payable->account_name = $request->input('accountName');
      $account_Payable->bank_name = $request->input('bankName');
      $account_Payable->short_code = $request->input('shortCode');
      $account_Payable->account_number = $request->input('accountNumber');
      $account_Payable->iban = $request->input('iban');
      $account_Payable->swift_code = $request->input('swiftCode');

      $account_Payable->save();
      if ($account_Payable->save()) {
        $customerDetail = CustomerDetail::find(session()->get('customer_detail_id'));

        $customerDetail->status = '1';
        $customerDetail->save();
      }
      $message = ' Completed Data Save successfully!';


      session()->forget('customer_detail_id');
    }






    return response()->json([
      'alert' => 'success',
      'message' => $message,

    ]);
  }


  public function deliveryEdit($id)
  {
      $delivery = DeliveryAddress::find($id);
      if (!$delivery) {
          return response()->json(['success' => false, 'message' => 'Delivery address not found'], 404);
      }
      return response()->json(['success' => true, 'data' => $delivery]);
  }

  public function update(Request $request, $id)
  {
    // dd($id);
    // dd($request->DeliveryName);
      $deliveryAddress = DeliveryAddress::find($id);
      $deliveryAddress->update($request->all());
      return response()->json(['success' => true, 'message' => 'Address updated successfully']);
  }
}
