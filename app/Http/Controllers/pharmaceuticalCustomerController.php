<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\PharmaConfirmMail;
use App\Mail\PharmaUnconfirmMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\PharmaceuticalAccount;

class pharmaceuticalCustomerController extends Controller
{
    public function pharmaceuticalAccount( $id = null){
      
  
         $data = [
           'user' => null, // Initialize as null
           // 'customer'=>DeliveryAddress ::where('customer_detail_id', $customerDetailId)->get(),
         ];
     
         if (!empty($id)) {
           $data['user'] = user::where('id', $id)->first();
         }
        return view('backend.customer.pharmaceutical_account.pharmaceutical_account')->with($data);
    }
     public function pharmaceutical_account_delete($id)
{
    // Get the specific user
    $user = User::where('id', $id)->first();

    if (!$user) {
        return response()->json(['error' => 'User not found or not a credit customer.'], 404);
    }

    // Get the related RegisterCredit record
    $registerCredit = PharmaceuticalAccount::where('user_id', $user->id)->first();

    
        $registerCredit->delete();
    

    $user->delete();

     return redirect()->route('pharmaceutical_account.list')->with('success', 'Customer deleted successfully.');
}
    public function pharmaceuticalAccountList( ){
      
  
        $data = [
            'user' => User::where('is_pharmaceutical', '1')
            ->orderByRaw("FIELD(is_pharma_approved, 0,1)")
            ->latest()
            ->get()
    
        ];
     
      
        return view('backend.customer.pharmaceutical_account.pharmaceutical_account_index')->with($data);
    }
    public function pharmaceuticalAccountView($id)
    {
      $User = User::where('id', $id)->first();
      return view('backend.customer.pharmaceutical_account.pharmaceutical_account_view', compact('User'));
    }
    public function pharmaceuticalStore(Request $request)
    {
        $firstName = $request->input('first_name');
        $lastName = $request->input('last_name');
        $email = $request->input('holder_email');
        $password = $request->input('password');
        $confirm_password = $request->input('confirm_password');
        $license_number = $request->input('license_number');
        $license_name = $request->input('license_name');
        $company_name = $request->input('company_name');
        $account_number = $request->input('account_number');
        $license_type = $request->input('license_type');
        $registration_date = $request->input('registration_date');
        $signature = $request->input('Signature');
        $user_id = $request->input('user_id');
        if ($password !== $confirm_password) {
            return response()->json(['message' => 'Passwords do not match'], 400);
        }
        if ($user_id) {
            // Update existing user
            $user = User::find($user_id);
            if (!$user) {
                return response()->json(['alert' => 'error', 'message' => 'User not found'], 404);
            }
    
            $user->name = $firstName;
            $user->last_name = $lastName;
            $email = $request->input('holder_email');
            $user->email = $email;
            $user->user_from = 'erp';
            if (!empty($password)) { 
                $user->password = Hash::make($password);
            }
      
            $user->user_type = "customer_pharmaceutical";
            $user->save();
    
            // Check if PharmaceuticalAccount exists for this user
            $pharmaAccount = PharmaceuticalAccount::where('user_id', $user->id)->first();
    
            if ($pharmaAccount) {
                // Update existing pharmaceutical account
                $pharmaAccount->company_name = $company_name;
                $pharmaAccount->account_number = $account_number;
                $pharmaAccount->license_type = $license_type;
                $pharmaAccount->license_name = $license_name;
                $pharmaAccount->license_number = $license_number;
                $pharmaAccount->registration_date = $registration_date;
                $pharmaAccount->Signature = $signature;
                $pharmaAccount->update();
    
                return redirect()->route('pharmaceutical_account.list')->with('success', 'Pharmaceutical account successfully updated');

            }
        } else {
            // Create new user
            $user = new User();
            $user->name = $firstName;
            $user->last_name = $lastName;
            $email = $request->input('holder_email');
            $existingContact = User::where('email', $email)->first();
      
            if ($existingContact) {
              return redirect()->route('pharmaceutical_account')->with('error', 'Email already exists. Please use a different email address.');      }
            $user->email = $email;
            $user->user_from = 'erp';
            if (!empty($password)) { 
                $user->password = Hash::make($password);
            }
            $user->user_type = "customer_pharmaceutical";
            $user->save();
    
            // Create new pharmaceutical account
            $pharmaAccount = new PharmaceuticalAccount();
            $pharmaAccount->user_id = $user->id;
            $pharmaAccount->holder_first_name = $firstName;
            $pharmaAccount->holder_last_name = $lastName;
            $pharmaAccount->holder_email = $email;
            $pharmaAccount->company_name = $company_name;
            $pharmaAccount->account_number = $account_number;
            $pharmaAccount->license_type = $license_type;
            $pharmaAccount->license_name = $license_name;
            $pharmaAccount->license_number = $license_number;
            $pharmaAccount->registration_date = $registration_date;
            $pharmaAccount->Signature = $signature;
            $pharmaAccount->save();
    
            return redirect()->route('pharmaceutical_account.list')->with('success', 'pharmaceutical account successfully created');
        }
    
     
    }
    public function reject_account_pharma(Request $request)
    {
    //     $user_id = $request->input('user_id');
    //     $user = User::where('id', operator: $user_id)->first();
    //     $user->is_approved = "1";
    //     $user->save(); 
        
    // $message = 'Account Rejected successfully!';
    // return response()->json(['message' => $message]);
      // dd( $request->input('comment'));
    $user_id = $request->input('user_id');
    $user = User::where('id', $user_id)->first(); 

    if ($user) {
        $user->is_pharma_approved = "2";
        $randomPassword = Str::random(10);

        $user->password = Hash::make($randomPassword);
        // $user->save();
        $user->save(); 

        
        $creditCustomer = PharmaceuticalAccount::where('user_id', $user->id)->first();

        if ($creditCustomer) {
            $creditCustomer->url = $request->input('url');
            $creditCustomer->comment = $request->comment;
            $creditCustomer->save(); 
        }
         Mail::to($user->email)->send(new PharmaUnconfirmMail(
            $user,
            $randomPassword,
            $creditCustomer->account_number ?? '',
            $creditCustomer->company_name ?? ''
        ));

        return response()->json(['message' => 'Account Reject!']);
    }

    return response()->json(['message' => 'User not found'], 404);
}
  

    
    public function approved_account_pharma(Request $request)
{
    // dd( $request->input('comment'));
    $user_id = $request->input('user_id');
    $user = User::where('id', $user_id)->first(); 

    if ($user) {
        $user->is_pharma_approved = "1";
        $randomPassword = Str::random(10);

        $user->password = Hash::make($randomPassword);
        // $user->save();
        $user->save(); 

        
        $creditCustomer = PharmaceuticalAccount::where('user_id', $user->id)->first();

        if ($creditCustomer) {
            $creditCustomer->url = $request->input('url');
            $creditCustomer->comment = $request->comment;
            $creditCustomer->save(); 
        }
         Mail::to($user->email)->send(new PharmaConfirmMail(
            $user,
            $randomPassword,
            $creditCustomer->account_number ?? '',
            $creditCustomer->company_name ?? ''
        ));

        return response()->json(['message' => 'Account Approved!']);
    }

    return response()->json(['message' => 'User not found'], 404);
}
    
}
