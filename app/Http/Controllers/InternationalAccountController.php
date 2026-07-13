<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Country;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CreditDelivery;
use App\Models\RegisterCredit;
use App\Mail\credit_account_confirmation;
use App\Mail\credit_account_unconfirmation;
use App\Mail\register;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class InternationalAccountController extends Controller
{

    public function customerCreditList()
    {
        $customers = RegisterCredit::where('is_completed', '0')->latest()->get();

        foreach ($customers as $customer) {
            $customer->user()->delete();
            $customer->creditDelivery()->delete();
            $customer->delete();
        }

        $users = User::join('customer_register_credit', 'customer_register_credit.user_id', '=', 'users.id')
            ->join('credit_delivery_address', 'credit_delivery_address.credit_id', '=', 'users.id')
            ->where('customer_register_credit.is_completed', '1')
            ->where('credit_delivery_address.country', '!=', 'United Kingdom')
            ->orderByRaw("FIELD(users.is_approved, 0, 2, 1)")
            ->select('users.*', 'customer_register_credit.is_completed', 'users.is_approved', 'credit_delivery_address.country')
            ->latest()
            ->get();

        return view('backend.customer.international_account.international_index', compact('users'));
    }

    public function customerRegisterList()
    {

        $customers = RegisterCredit::where('is_completed', '0')->latest()->get();

        foreach ($customers as $customer) {

            $customer->user()->delete();
            $customer->creditDelivery()->delete();

            $customer->delete();
        }

        $users = User::where('users.user_type', ['customer', 'customer_pharmaceuti'])
            ->join('customer_register_credit', 'customer_register_credit.user_id', '=', 'users.id')
            ->where('customer_register_credit.is_completed', '1')
            ->orderByRaw("FIELD(users.is_approved, 0, 2, 1)")
            ->select('users.*', 'customer_register_credit.is_completed', 'users.is_approved')
            ->latest()
            ->get();

        return view('backend.customer.international_account.register_index', compact('users'));
    }

    public function credit_customer_delete($id)
    {
        // Get the specific user
        $user = User::where('id', $id)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found or not a credit customer.'], 404);
        }

        // Get the related RegisterCredit record
        $registerCredit = RegisterCredit::where('user_id', $user->id)->first();

        if ($registerCredit) {

            creditDelivery::where('credit_id', $registerCredit->id)->delete();

            $registerCredit->delete();
        }


        $user->delete();

        return redirect()->route('international.customer_credit.list')->with('success', 'Customer deleted successfully.');
    }
    public function register_customer_delete($id)
    {
        // dd('hello');
        $user = User::where('id', $id)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found or not a credit customer.'], 404);
        }

        $registerCredit = RegisterCredit::where('user_id', $user->id)->first();

        if ($registerCredit) {

            creditDelivery::where('credit_id', $registerCredit->id)->delete();

            $registerCredit->delete();
        }


        $user->delete();
        return redirect()->route('international.customer_credit.list')->with('success', 'Customer deleted successfully.');
    }

    public function register_customer($id = null)
    {
        session()->forget('credit_id');

        $data = [
            'user' => null,
            'country' => Country::all(),
            // 'customer'=>DeliveryAddress ::where('customer_detail_id', $customerDetailId)->get(),
        ];

        if (!empty($id)) {
            $data['user'] = user::where('id', $id)->first();
        }
        return view('backend.customer.international_account.register')->with($data);
    }
    public function credit_customer($id = null)
    {
        session()->forget('credit_id');

        $data = [
            'user' => null,
            'country' => Country::all(),
            // 'customer'=>DeliveryAddress ::where('customer_detail_id', $customerDetailId)->get(),
        ];

        if (!empty($id)) {
            $data['user'] = user::where('id', $id)->first();
        }
        return view('backend.customer.customer_register_credit.register_credit')->with($data);
    }


    public function credit_customer_view($id)
    {
        $User = User::where('id', $id)->first();
        //   $registerCredit = registerCredit::where('user_id', $User->id)->first();
        $creditDelivery = creditDelivery::where('credit_id', $User->id)->get();


        return view('backend.customer.customer_register_credit.register_credit_view', compact('User', 'creditDelivery'));
    }
    public function register_customer_view($id)
    {
        $User = User::where('id', $id)->first();
        //   $registerCredit = registerCredit::where('user_id', $User->id)->first();
        $creditDelivery = creditDelivery::where('credit_id', $User->id)->get();


        return view('backend.customer.register_account.register_view', compact('User', 'creditDelivery'));
    }

    public function customerCreditStore(Request $request)
    {
        $Name = $request->input('first_name');
        $LName =  $request->input('last_name');
        $email = $request->input('email');
        // dd($request->input('email'));
        $password = $request->input('password');
        $confirm_password = $request->input('confirm_password');
        $organization_type = $request->input('organization_type');
        $company_name = $request->input('company_name');
        $department_name = $request->input('department_name');
        $statement_email = $request->input('statement_email');
        $phone_number = $request->input('phone_number');
        $mobile_number = $request->input('mobile_number');
        $organization_name = $request->input('organization_name');
        if ($password !== $confirm_password) {
            return response()->json(['message' => 'Passwords do not match'], 400);
        }

        $user_id = $request->input('user_id') ?? session()->get('credit_id');

        if ($user_id) {
            $user = User::find($user_id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $user->name = $Name;
            $user->last_name = $LName;
            $user->email = $email;
            $user->user_from = 'erp';
            // $user->is_approved = '1';
            if (!empty($password)) {
                $user->password = Hash::make($password);
            }
            $user->save();

            $customerCredit = RegisterCredit::where('user_id', $user->id)->first();
            if ($customerCredit) {
                $customerCredit->organization_type = $organization_type;
                $customerCredit->company_name = $company_name;
                $customerCredit->department_name = $department_name;
                $customerCredit->statement_email = $statement_email;
                $customerCredit->phone_number = $phone_number;
                $customerCredit->mobile_number = $mobile_number;
                $customerCredit->organization_name = $organization_name;
                $customerCredit->update();
                $message = 'personal details update successfully!';
            }
        } else {



            $user = new User();
            $user->name = $Name;
            $user->last_name = $LName;

            $existingContact = User::where('email', $email)->first();

            if ($existingContact) {
                return response()->json(['message' => 'Email already exists. Please use a different email address.'], 400);
            }
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->user_type = "customer_credit";
            $user->user_from = 'erp';
            // $user->is_approved = '1';
            $user->save();

            $customerCredit = new RegisterCredit();
            $customerCredit->user_id = $user->id;
            $customerCredit->organization_type = $organization_type;
            $customerCredit->company_name = $company_name;
            $customerCredit->department_name = $department_name;
            $customerCredit->statement_email = $statement_email;
            $customerCredit->phone_number = $phone_number;
            $customerCredit->mobile_number = $mobile_number;
            $customerCredit->organization_name = $organization_name;
            $customerCredit->save();

            session()->put("credit_id", $user->id);
            $message = 'personal details save successfully!';
        }
        return response()->json(['message' => $message]);
    }
    public function customerRegisterStore(Request $request)
    {
        $Name = $request->input('first_name');
        $LName =  $request->input('last_name');
        $email = $request->input('email');
        // dd($request->input('email'));
        $password = $request->input('password');
        $confirm_password = $request->input('confirm_password');
        $organization_type = $request->input('organization_type');
        $company_name = $request->input('company_name');
        $department_name = $request->input('department_name');
        $statement_email = $request->input('statement_email');
        $phone_number = $request->input('phone_number');
        $mobile_number = $request->input('mobile_number');
        $organization_name = $request->input('organization_name');
        if ($password !== $confirm_password) {
            return response()->json(['message' => 'Passwords do not match'], 400);
        }

        $user_id = $request->input('user_id') ?? session()->get('credit_id');

        if ($user_id) {
            $user = User::find($user_id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $user->name = $Name;
            $user->last_name = $LName;
            $user->email = $email;

            $user->user_from = 'erp';
            // $user->is_approved = '1';
            if (!empty($password)) {
                $user->password = Hash::make($password);
            }
            $user->save();

            $customerCredit = RegisterCredit::where('user_id', $user->id)->first();
            if ($customerCredit) {
                $customerCredit->organization_type = $organization_type;
                $customerCredit->company_name = $company_name;
                $customerCredit->department_name = $department_name;
                $customerCredit->statement_email = $statement_email;
                $customerCredit->phone_number = $phone_number;
                $customerCredit->mobile_number = $mobile_number;
                $customerCredit->organization_name = $organization_name;
                $customerCredit->update();
                $message = 'personal details update successfully!';
            }
        } else {



            $user = new User();
            $user->name = $Name;
            $user->last_name = $LName;

            $existingContact = User::where('email', $email)->first();

            if ($existingContact) {
                return response()->json(['message' => 'Email already exists. Please use a different email address.'], 400);
            }
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->user_type = "customer";
            $user->user_from = 'erp';
            // $user->is_approved = '1';
            $user->save();

            $customerCredit = new RegisterCredit();
            $customerCredit->user_id = $user->id;
            $customerCredit->organization_type = $organization_type;
            $customerCredit->company_name = $company_name;
            $customerCredit->department_name = $department_name;
            $customerCredit->statement_email = $statement_email;
            $customerCredit->phone_number = $phone_number;
            $customerCredit->mobile_number = $mobile_number;
            $customerCredit->organization_name = $organization_name;
            $customerCredit->save();

            session()->put("credit_id", $user->id);
            $message = 'personal details save successfully!';
        }
        return response()->json(['message' => $message]);
    }

    public function deliveryFormStore(Request $request)
    {
        $user_id = $request->input('user_id') ?? session()->get('credit_id');

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

    public function changeStatusCredit(Request $request)
    {
        $session_user_id = session()->get('credit_id');
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
            if (!$UserGet) {
                return response()->json(['message' => 'User not found in session!'], 404);
            }
            $userCredit = RegisterCredit::where('user_id', $UserGet->id)->first();

            $userCredit->is_completed = '1';
            $userCredit->save();
            session()->forget('credit_id');
            $message = 'Data saved successfully!';
        }
        // If no user_id is found, return an error
        else {
            return response()->json(['message' => 'User ID not found!'], 400);
        }

        return response()->json(['message' => $message]);
    }
    public function changeStatusCredit1(Request $request)
    {
        $session_user_id = session()->get('credit_id');
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
            if (!$UserGet) {
                return response()->json(['message' => 'User not found in session!'], 404);
            }
            $userCredit = RegisterCredit::where('user_id', $UserGet->id)->first();

            $userCredit->is_completed = '1';
            $userCredit->save();
            session()->forget('credit_id');
            $message = 'Data saved successfully!';
        }
        // If no user_id is found, return an error
        else {
            return response()->json(['message' => 'User ID not found!'], 400);
        }

        return response()->json(['message' => $message]);
    }
    public function changeStatusRegister(Request $request)
    {
        $session_user_id = session()->get('credit_id');
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
            if (!$UserGet) {
                return response()->json(['message' => 'User not found in session!'], 404);
            }
            $userCredit = RegisterCredit::where('user_id', $UserGet->id)->first();

            $userCredit->is_completed = '1';
            $userCredit->save();
            session()->forget('credit_id');
            $message = 'Data saved successfully!';
        }
        // If no user_id is found, return an error
        else {
            return response()->json(['message' => 'User ID not found!'], 400);
        }

        return response()->json(['message' => $message]);
    }

    public function deliveryCreditEdit($id)
    {
        // dd($id);
        $delivery = creditDelivery::find($id);
        if (!$delivery) {
            return response()->json(['success' => false, 'message' => 'Dejhlivery address not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $delivery]);
    }

    public function updateCredit(Request $request, $id)
    {
        //   dd($id);
        //   dd($request->post_code);
        $deliveryAddress = creditDelivery::find($id);
        $deliveryAddress->update($request->all());
        return response()->json(['success' => true, 'message' => 'Address updated successfully']);
    }
    public function reject_account(Request $request)
    {
        //     $user_id = $request->input('user_id');
        //     $user = User::where('id', operator: $user_id)->first();
        //     $user->is_approved = "1";
        //     $user->save(); 
        // $message = 'Account Rejected successfully!';
        // return response()->json(['message' => $message]);
        $user_id = $request->input('user_id');
        $user = User::where('id', $user_id)->first();

        if ($user) {
            $user->is_approved = "1";
            $user->user_type = "customer";

            $randomPassword = Str::random(10);

            $user->password = Hash::make($randomPassword);
            $user->save();

            $creditCustomer = RegisterCredit::where('user_id', $user->id)->first();
            if ($creditCustomer) {
                $creditCustomer->url = $request->input('url');
                $creditCustomer->comment = $request->input('comment');
                $creditCustomer->save();
            }


            Mail::to($user->email)->send(new register(
                $user,
                $randomPassword,
                $creditCustomer->account_number ?? '',
                $creditCustomer->company_name ?? '',

            ));

            return response()->json(['message' => 'Account Reject!']);
        }

        return response()->json(['message' => 'User not found'], 404);
    }
    public function register_reject_account(Request $request)
    {
        //     $user_id = $request->input('user_id');
        //     $user = User::where('id', operator: $user_id)->first();
        //     $user->is_approved = "1";
        //     $user->save(); 
        // $message = 'Account Rejected successfully!';
        // return response()->json(['message' => $message]);
        $user_id = $request->input('user_id');
        $user = User::where('id', $user_id)->first();

        if ($user) {
            $user->is_approved = "1";

            $randomPassword = Str::random(10);

            $user->password = Hash::make($randomPassword);
            $user->save();

            $creditCustomer = RegisterCredit::where('user_id', $user->id)->first();
            if ($creditCustomer) {
                $creditCustomer->url = $request->input('url');
                $creditCustomer->comment = $request->input('comment');
                $creditCustomer->save();
            }


            Mail::to($user->email)->send(new credit_account_unconfirmation(
                $user,
                $randomPassword,
                $creditCustomer->account_number ?? '',
                $creditCustomer->company_name ?? ''
            ));

            return response()->json(['message' => 'Account Reject!']);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    public function approved_account(Request $request)
    {
        $user_id = $request->input('user_id');
        $user = User::where('id', $user_id)->first();

        if ($user) {
            $user->is_approved = "2";

            $randomPassword = Str::random(10);

            $user->password = Hash::make($randomPassword);
            $user->password_text = $randomPassword;
            $user->save();

            $creditCustomer = RegisterCredit::where('user_id', $user->id)->first();
            if ($creditCustomer) {
                $creditCustomer->url = $request->input('url');
                $creditCustomer->comment = $request->input('comment');
                $creditCustomer->save();
            }


            Mail::to($user->email)->send(new credit_account_confirmation(
                $user,
                $randomPassword,
                $creditCustomer->account_number ?? '',
                $creditCustomer->company_name ?? ''
            ));

            return response()->json(['message' => 'Account Approved!']);
        }

        return response()->json(['message' => 'User not found'], 404);
    }


    public function deliveryDestroy(Request $request)
    {
        $deliveryAddressId = $request->input('deliveryAddressId');
        $deliveryAddress = CreditDelivery::find($deliveryAddressId);

        if (!$deliveryAddress) {
            return response()->json(['message' => 'Delivery address not found.'], 404);
        }

        $deliveryAddress->delete();

        return response()->json(['message' => 'Delivery address deleted successfully.'], 200);
    }
}
