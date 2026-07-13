<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\CreditDelivery;
use App\Models\RegisterCredit;

class GuestAccountController extends Controller
{
    public function customerGuestList(){
        $user = User::where('user_type', 'customer_guest')->latest()->get();
        return view('backend.customer.guest_account.guest_account' ,compact('user'));
    }

    public function customerGuestView($id)
    {
      $User = User::where('id', $id)->first();
    
      $creditDelivery = creditDelivery::where('credit_id', $User->id)->get();
    

      return view('backend.customer.guest_account.guest_account_view', compact('User','creditDelivery'));
    }

    public function guest_customer($id = null)
    {
        session()->forget('guest_id');
  
      $data = [
        'user' => null, 
        'country' => Country::all(),
        // 'customer'=>DeliveryAddress ::where('customer_detail_id', $customerDetailId)->get(),
      ];
  
      if (!empty($id)) {
        $data['user'] = user::where('id', $id)->first();
      }
      return view('backend.customer.guest_account.guest_account_add')->with($data);
    }
    public function customerGuestStore(Request $request) {
      $Name = $request->input('first_name'); 
      $LName =  $request->input('last_name');
      $email = $request->input('email');
 
    
      $mobile_number = $request->input('mobile_number');
   
      
      $user_id = $request->input('user_id') ?? session()->get('guest_id');

      if ($user_id) {
          $user = User::find($user_id);
          if (!$user) {
              return response()->json(['message' => 'User not found'], 404);
          }
          $user->name = $Name;
          $user->last_name = $LName;
          $user->email = $email;
          $user->user_from = 'erp';
          $user->mobile_number = $mobile_number;
          // $user->is_approved = '1';
      
          $user->save();
  
          $message = 'personal details update successfully!';

      } else {
    
      
   
          $user = new User();
          $user->name = $Name;
          $user->last_name = $LName;
       
          $existingContact = User::where('email', $email)->first();
    
          if ($existingContact) {
            return response()->json(['message' => 'Email already exists. Please use a different email address.'], 400);
          }
          $user->email = $email;
          $user->user_type = "customer_guest";
          $user->user_from = 'erp';
          $user->mobile_number = $mobile_number;

          // $user->is_approved = '1';
          $user->save();
          

          session()->put("guest_id", $user->id);
          $message = 'personal details save successfully!';
      }
          return response()->json(['message' => $message]);
      
  }
  public function changeStatus3(Request $request)
  {
      $session_user_id = session()->get('guest_id');
      $user_id = $request->input('user_id');
  
      // If user_id is provided in request, update it
      if ($user_id) {
          $UserGet = User::find($user_id);
          if (!$UserGet) {
              return response()->json(['message' => 'User not found!'], 404);
          }
//            $userCredit = RegisterCredit::where('user_id',$UserGet->id)->first();
          
// $userCredit->is_completed = '1';
// $userCredit->save();
          // session()->forget('credit_id');
          $message = 'Data updated successfully!';
      }
      // If user_id is not in request but exists in session, save it
      elseif ($session_user_id) {
          $UserGet = User::find($session_user_id);
       
          $UserGet->guest_complete = '1';
          $UserGet->save();
          session()->forget('guest_id');
          $message = 'Data saved successfully!';
      }
      // If no user_id is found, return an error
      else {
          return response()->json(['message' => 'User ID not found!'], 400);
      }
  
      return response()->json(['message' => $message]);
  }


  public function delivery_guest_form(Request $request)
  { 
      $user_id = $request->input('user_id') ?? session()->get('guest_id');
  
      if (!$user_id) {
          return response()->json(['alert' => 'error', 'message' => 'User ID not provided'], 400);
      }
  
      $user = User::find($user_id);
      if (!$user) {
          return response()->json(['alert' => 'error', 'message' => 'User not found'], 404);
      }
  
  // $customerCredit = RegisterCredit::where('user_id', $user->id)->first();
    $creditDelivery = new creditDelivery();
    $creditDelivery->credit_id = $user->id;
    $creditDelivery->post_code = $request->input('post_code');
    $creditDelivery->address1 = $request->input('address1');
    $creditDelivery->address2 = $request->input('address2');
    $creditDelivery->address3 = $request->input('address3');
    $creditDelivery->town = $request->input('town');
    $creditDelivery->city = $request->input('city');
    $creditDelivery->county = $request->input('county');
    $creditDelivery->country = $request->input('country');
    $creditDelivery->save();      
         
  
      

  
      return response()->json([
          'id' =>  $creditDelivery->id,
          'credit_id' =>  $creditDelivery->credit_id,
          'name' =>  $creditDelivery->delivery_name,
          'postcode' => $creditDelivery->post_code,
          'address1' => $creditDelivery->address1,
          'town' => $creditDelivery->town,
          'city' => $creditDelivery->city,
          'county' => $creditDelivery->county,
          'country' => $creditDelivery->country,
          'message' => 'Delivery Address saved successfully!'
      ]);
  }



  public function customer_guest_delete($id)
{
    // dd('hello');
    $user = User::where('id', $id)->where('user_type', 'customer_guest')->first();

    if (!$user) {
        return response()->json(['error' => 'User not found or not a credit customer.'], 404);
    }

 

        creditDelivery::where('credit_id', $user->id)->delete();

        $user->delete();
    


    $user->delete();
//  return redirect()->route('pharmaceutical_account.list')->with('success', 'Customer deleted successfully.');
    return redirect()->route('customer_guest.list')->with('success', 'Customer deleted successfully.');
}

}
