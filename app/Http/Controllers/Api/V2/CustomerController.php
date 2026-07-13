<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\CreditDelivery;
use App\Models\RegisterCredit;
use App\Models\BusinessSetting;
// use App\Models\User;
use Laravel\Sanctum\HasApiTokens;
use App\Mail\EmailVerificationMail;
use App\Mail\register;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

use App\Models\PharmaceuticalAccount;
use Illuminate\Support\Carbon; // optional if using Carbon directly

use Illuminate\Support\Facades\Session;
use App\Http\Resources\V2\CustomerCollection;
use App\Http\Controllers\OTPVerificationController;
use App\Notifications\AppEmailVerificationNotification;
use Illuminate\Support\Facades\Validator;
use Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    // public function show()
    // {
    //     $user = User::where("id",auth()->user()->id)->where("user_type","customer_credit")->get();

    //     return new CustomerCollection($user);
    // }
    // public function pharma_show()
    // {
    //     if (!auth()->check()) {
    //         return response()->json(['error' => 'Unauthorized'], 401);
    //     }

    //     $user = User::where("user_type", "admin")
    //                 ->get();

    //     return new CustomerCollection($user);
    // }

    public function customerCreate(Request $request)
    {
	Log::info($request->all());
        switch ($request->input('user_type')) {
            case 'register_customer':
                return $this->customerStore($request);
                break;
            case 'credit_customer':
                return $this->customerCreditStore($request);
                break;
            case 'pharma_customer':
                return $this->pharmaceuticalStore($request);
                break;
            case 'guest_customer':
                return $this->guestStore($request);
                break;
            default:
                return response()->json(['error' => 'Invalid user type'], 500);
        }
    }

    public function allCustomerUpdate(Request $request)
    {
        switch ($request->input('user_type')) {
            case 'register_customer':
                return $this->customerUpdate($request);
                break;
            case 'credit_customer':
                return $this->customerCreditUpdate($request);
                break;
            case 'pharma_customer':
                return $this->pharmaceuticalUpdate($request);
                break;
            case 'guest_customer':
                return $this->guestUpdate($request);
                break;
            default:
                return response()->json(['error' => 'Invalid user type'], 400);
        }
    }

    public function show(Request $request)
    {
        switch ($request->input('user_type')) {
            case 'register_customer':
                return $this->customerShow($request);
                break;
            case 'credit_customer':
                return $this->credit_show($request);
                break;
            case 'pharma_customer':
                return $this->pharma_show($request);
                break;
            case 'guest_customer':
                return $this->guestShow($request);
                break;
            default:
                return response()->json(['error' => 'Invalid user type'], 400);
        }
    }

    public function customerCreditStore(Request $request)
    {
        $validated = $request->validate([
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'organization_type' => 'nullable',
            'bussiness_name'    => 'nullable|string|max:255',
            'department_name'   => 'nullable|string|max:255',
            'statement_email'   => 'required_if:organization_type,1,2|nullable|email',
            'phone_number'      => 'required_if:organization_type,1,2|nullable|string|max:20',
            'mobile_number'     => 'required|string|max:20',
            'organization_name' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Create user
            $user = User::create([
                'name'       => $validated['first_name'],
                'last_name'  => $validated['last_name'],
                'email'      => $validated['email'],
                'password'   => !empty($validated['password'])
                    ? Hash::make($validated['password'])
                    : null,
                'user_type'  => 'customer_credit',
            ]);

            // Create credit record
            $customerCredit = RegisterCredit::create([
                'user_id'           => $user->id,
                'organization_type' => $validated['organization_type'] ?? null,
                'company_name'      => $validated['bussiness_name'] ?? null,
                'department_name'   => $validated['department_name'] ?? null,
                'statement_email'   => $validated['statement_email'] ?? null,
                'phone_number'      => $validated['phone_number'] ?? null,
                'mobile_number'     => $validated['mobile_number'],
                'organization_name' => $validated['organization_name'] ?? null,
                'is_completed'      => 1,
            ]);

            DB::commit();

            // Send email AFTER commit
            try {
                $token = Str::random(64);
                $user->email_verification_token = $token;
                $user->save();

                $verificationUrl = rtrim(env('FRONTEND_URL', config('app.url')), '/')
                    . '/auth/email-verification'
                    . '?token=' . $token;

                Mail::to($user->email)->send(new EmailVerificationMail($verificationUrl, $user->name));
            } catch (\Throwable $mailError) {
                \Log::warning('Email failed: ' . $mailError->getMessage());
            }

            return response()->json([
                'status' => true,
                'message' => 'Personal details saved successfully!',

                'data' => [
                    'user' => [
                        'user_id'   => $user->id,
                        'first_name' => $user->name,
                        'last_name' => $user->last_name,
                        'email'     => $user->email,
                        'user_type' => $user->user_type,
                    ],

                    'credit' => [
                        'credit_id'        => $customerCredit->id,
                        'organization_type' => $customerCredit->organization_type,
                        'company_name'     => $customerCredit->company_name,
                        'department_name'  => $customerCredit->department_name,
                        'statement_email'  => $customerCredit->statement_email,
                        'phone_number'     => $customerCredit->phone_number,
                        'mobile_number'    => $customerCredit->mobile_number,
                        'organization_name' => $customerCredit->organization_name,
                    ]
                ]
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('Customer Credit Store Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }


    public function customerStore(Request $request)
    {
        $validated = $request->validate([
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'bussiness_name'    => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->where(function ($query) {
                    return $query->where('user_type', 'customer');
                }),
            ],
            'mobile_number'     => 'required|string|max:20',
            'password'          => 'required|min:6|same:confirm_password',
            'confirm_password'  => 'required|min:6',
        ]);

        DB::beginTransaction();

        try {

            $existCustomer = User::where('email', $validated['email'])
                ->whereIn('user_type', ['customer_guest', 'customer_credit'])
                ->first();

            if ($existCustomer) {
                $existCustomer->name = $validated['first_name'];
                $existCustomer->last_name = $validated['last_name'];
                $existCustomer->password = Hash::make($validated['password']);
                $existCustomer->user_type = 'customer';
                $existCustomer->save();

                $user = $existCustomer;
            } else {
                // Create user
                $user = User::create([
                    'name'       => $validated['first_name'],
                    'last_name'  => $validated['last_name'],
                    'email'      => $validated['email'],
                    'password'   => Hash::make($validated['password']),
                    'user_type'  => 'customer',
                ]);
            }

            // Create credit record
            $customerCredit = RegisterCredit::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'organization_type' => $validated['organization_type'] ?? null,
                    'company_name'      => $validated['bussiness_name'] ?? null,
                    'department_name'   => $validated['department_name'] ?? null,
                    'statement_email'   => $validated['statement_email'] ?? null,
                    'phone_number'      => $validated['phone_number'] ?? null,
                    'mobile_number'     => $validated['mobile_number'],
                    'organization_name' => $validated['organization_name'] ?? null,
                    'is_completed'      => 1,
                ]
            );

            DB::commit();

            // Send email AFTER commit (important)
            try {
                $token = Str::random(64);
                $user->email_verification_token = $token;
                $user->save();

                $verificationUrl = rtrim(env('FRONTEND_URL', config('app.url')), '/')
                    . '/auth/email-verification'
                    . '?token=' . $token;

                Mail::to($user->email)->send(new EmailVerificationMail($verificationUrl, $user->name));
            } catch (\Throwable $mailError) {
                \Log::warning('Email sending failed: ' . $mailError->getMessage());
            }

            return response()->json([
                'status' => true,
                'message' => 'Personal details saved successfully!',

                'data' => [
                    'user' => [
                        'user_id'   => $user->id,
                        'first_name' => $user->name,
                        'last_name' => $user->last_name,
                        'email'     => $user->email,
                        'user_type' => $user->user_type,
                    ],

                    'register' => [
                        'register_id'        => $customerCredit->id,
                        'mobile_number'    => $customerCredit->mobile_number,
                        'bussiness_name' => $customerCredit->company_name,
                    ]
                ]
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('Customer Store Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function deliveryFormStore(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'credit_id' => 'required',
            'post_code' => 'required',
            'address1' => 'required',
            'town' => 'nullable',
            'city' => 'required',
            'country' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['message' => 'Validation failed', 'status' => false, 'errors' => $validate->errors()], 422);
        }

        $creditDelivery = CreditDelivery::create([
            'credit_id' => $request->input('credit_id'),
            'post_code' => $request->input('post_code'),
            'address1' => $request->input('address1'),
            'address2' => $request->input('address2'),
            'address3' => $request->input('address3'),
            'town' => $request->input('town'),
            'city' => $request->input('city'),
            'county' => $request->input('county'),
            'country' => $request->input('country'),
        ]);

        return response()->json([
            'status' => true,
            'id' =>  $creditDelivery->id,
            'credit_id' =>  $creditDelivery->credit_id,
            'postcode' => $creditDelivery->post_code,
            'address1' => $creditDelivery->address1,
            'town' => $creditDelivery->town,
            'city' => $creditDelivery->city,
            'county' => $creditDelivery->county,
            'country' => $creditDelivery->country,
            'message' => 'Delivery Address saved successfully!'
        ]);
    }

    public function customerCreditEdit($id)
    {

        $user = User::find($id);
        // dd($user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $customerCredit = RegisterCredit::where('user_id', $user->id)->first();
        // dd($customerCredit);

        return response()->json([
            "id" => $user->id,
            "user_type" => $user->user_type,
            "name" => $user->name,
            "last_name" => $user->last_name,
            "email" => $user->email,
            "user_from" => $user->user_from,
            'is_pharmaceutical' => $user->is_pharmaceutical,
            "organization_name" => $customerCredit->organization_name,
            "organization_type" => $customerCredit->organization_type,
            "company_name" => $customerCredit->company_name,
            "department_name" => $customerCredit->department_name,
            "statement_email" => $customerCredit->statement_email,
            "phone_number" => $customerCredit->phone_number,
            "mobile_number" => $customerCredit->mobile_number
        ]);
    }
    public function pharmaceuticalEdit($id)
    {

        $user = User::find($id);
        // dd($user);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $PharmaceuticalAccount = PharmaceuticalAccount::where('user_id', $user->id)->first();
        // dd($PharmaceuticalAccount);

        return response()->json([
            "id" => $user->id,
            "user_type" => $user->user_type,
            "name" => $user->name,
            "last_name" => $user->last_name,
            "email" => $user->email,
            "user_from" => $user->user_from,
            'is_pharmaceutical' => $user->is_pharmaceutical,
            "license_type" => $PharmaceuticalAccount->license_type,
            "license_number" => $PharmaceuticalAccount->license_number,
            "license_name" => $PharmaceuticalAccount->license_name,
            "account_number" => $PharmaceuticalAccount->account_number,
            "company_name" => $PharmaceuticalAccount->company_name,
            "registration_date" => $PharmaceuticalAccount->registration_date,
            "Signature" => $PharmaceuticalAccount->Signature,

        ]);
    }


    public function customerCreditUpdate(Request $request)
    {
        $validated = $request->validate([
            'user_id'           => 'required|exists:users,id',
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'email'             => 'required|email',

            'password'          => 'nullable|min:6|same:confirm_password',
            'confirm_password'  => 'nullable|min:6',

            'organization_type' => 'nullable|in:1,2',
            'bussiness_name'    => 'nullable|string|max:255',
            'department_name'   => 'nullable|string|max:255',
            'statement_email'   => 'nullable|email',
            'phone_number'      => 'nullable|string|max:20',
            'mobile_number'     => 'nullable|string|max:20',
            'organization_name' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($validated['user_id']);

            // Update user
            $user->update([
                'name'      => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email'     => $validated['email'],
                'user_from' => 'erp',
                'password'  => !empty($validated['password'])
                    ? Hash::make($validated['password'])
                    : $user->password,
            ]);

            // Update or create credit record
            $customerCredit = RegisterCredit::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'organization_type' => $validated['organization_type'] ?? null,
                    'company_name'      => $validated['bussiness_name'] ?? null,
                    'department_name'   => $validated['department_name'] ?? null,
                    'statement_email'   => $validated['statement_email'] ?? null,
                    'phone_number'      => $validated['phone_number'] ?? null,
                    'mobile_number'     => $validated['mobile_number'] ?? null,
                    'organization_name' => $validated['organization_name'] ?? null,
                ]
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Customer credit details updated successfully',
                'data' => [
                    'user' => [
                        'id'         => $user->id,
                        'first_name' => $user->name,
                        'last_name'  => $user->last_name,
                        'email'      => $user->email,
                    ],
                    'credit' => [
                        'organization_type' => $customerCredit->organization_type,
                        'company_name'      => $customerCredit->company_name,
                        'department_name'   => $customerCredit->department_name,
                        'statement_email'   => $customerCredit->statement_email,
                        'phone_number'      => $customerCredit->phone_number,
                        'mobile_number'     => $customerCredit->mobile_number,
                        'organization_name' => $customerCredit->organization_name,
                    ]
                ]
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('Customer Credit Update Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function customerUpdate(Request $request)
    {
        $validated = $request->validate([
            'user_id'           => 'required|exists:users,id',
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'email'             => 'required|email',

            'password'          => 'nullable|min:6|same:confirm_password',
            'confirm_password'  => 'nullable|min:6',

            'bussiness_name'    => 'nullable|string|max:255',
            'mobile_number'     => 'nullable|string|max:20',
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($validated['user_id']);

            // Update user
            $user->update([
                'name'      => $validated['first_name'],
                'last_name' => $validated['last_name'],
                // 'email'     => $validated['email'],
                'user_from' => 'erp',
                'password'  => !empty($validated['password'])
                    ? Hash::make($validated['password'])
                    : $user->password,
            ]);

            // Update or create credit record
            $customerCredit = RegisterCredit::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'organization_type' => $validated['organization_type'] ?? null,
                    'company_name'      => $validated['bussiness_name'] ?? null,
                    'department_name'   => $validated['department_name'] ?? null,
                    'statement_email'   => $validated['statement_email'] ?? null,
                    'phone_number'      => $validated['phone_number'] ?? null,
                    'mobile_number'     => $validated['mobile_number'] ?? null,
                    'organization_name' => $validated['organization_name'] ?? null,
                ]
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Customer details updated successfully',
                'data' => [
                    'user' => [
                        'id'         => $user->id,
                        'first_name' => $user->name,
                        'last_name'  => $user->last_name,
                        'email'      => $user->email,
                    ],
                    'register' => [
                        "credit_id" => $customerCredit->id,
                        'bussiness_name'      => $customerCredit->company_name,
                        'mobile_number'     => $customerCredit->mobile_number,
                    ]
                ]
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('Customer Update Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function pharmaceuticalStore(Request $request)
    {
        $validated = $request->validate([
            'user_id'           => 'required|exists:users,id',
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'holder_email'      => 'required|email',
            'license_number'    => 'required|string|max:255',
            'license_name'      => 'required|string|max:255',
            'bussiness_name'    => 'required|string|max:255',
            'account_number'    => 'required|string|max:255',
            'license_type'      => 'required|string|max:255',
            'registration_date' => 'required|date',
            'Signature'         => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($validated['user_id']);

            $user->update([
                'is_pharmaceutical' => 1,
            ]);

            $pharmaAccount = PharmaceuticalAccount::create([
                'user_id'            => $user->id,
                'company_name'       => $validated['bussiness_name'],
                'holder_first_name'  => $validated['first_name'],
                'holder_last_name'   => $validated['last_name'],
                'holder_email'       => $validated['holder_email'],
                'account_number'     => $validated['account_number'],
                'license_type'       => $validated['license_type'],
                'license_name'       => $validated['license_name'],
                'license_number'     => $validated['license_number'],
                'registration_date'  => $validated['registration_date'],
                'signature'          => $validated['Signature'] ?? null,
            ]);

            $token = $user->createToken('PharmaToken')->plainTextToken;

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Pharmaceutical account created successfully',

                'data' => [
                    'user' => [
                        'id'                  => $user->id,
                        'first_name'          => $user->name,
                        'last_name'           => $user->last_name,
                        'email'               => $user->email,
                        'is_pharmaceutical'   => $user->is_pharmaceutical,
                        'is_pharma_approved'  => $user->is_pharma_approved,
                    ],

                    'pharma_account' => [
                        'account_number'     => $pharmaAccount->account_number,
                        'company_name'       => $pharmaAccount->company_name,
                        'license_type'       => $pharmaAccount->license_type,
                        'license_name'       => $pharmaAccount->license_name,
                        'license_number'     => $pharmaAccount->license_number,
                        'registration_date'  => $pharmaAccount->registration_date,
                        'signature'          => $pharmaAccount->signature,
                    ],

                    'token' => $token,
                ]
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('Pharma Store Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }



    public function guestStore(Request $request)
    {
        $validated = $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'mobile_number'  => 'required|string|max:20',
        ]);

        DB::beginTransaction();

        try {
            $password = Str::random(12);

            $user = User::create([
                'name'           => $validated['first_name'],
                'last_name'      => $validated['last_name'],
                'email'          => $validated['email'],
                'password'       => Hash::make($password),
                'user_from'      => 'website',
                'user_type'      => 'customer_guest',
                'mobile_number'  => $validated['mobile_number'],
            ]);

            $token = $user->createToken('GuestToken')->plainTextToken;

            DB::commit();

            return response()->json([
                'status' => true,
                'data' => [
                    'user_id'        => $user->id,
                    'first_name'     => $user->name,
                    'last_name'      => $user->last_name,
                    'email'          => $user->email,
                    'user_type'      => $user->user_type,
                    'mobile_number'  => $user->mobile_number,
                    'token'          => $token,
                ],
                'message' => 'Guest account created successfully',
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Guest Store Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }



    public function verify(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            return response()->json([
                'message' => 'Verification token is missing.'
            ], 400);
        }

        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid or expired verification link.'
            ], 404);
        }

        if ($user->is_verified == 0) {
            $user->email_verified_at          = now();
            $user->is_verified                = 1;
            $user->email_verification_token   = null; // consume the token — one-time use
            $user->save();

            $message = 'Email verified successfully!';
        } else {
            $message = 'Email already verified.';
        }

        // Only generate a token for regular customers
        $token = null;
        if ($user->user_type === 'customer') {
            $token = $user->createToken('CustomerToken')->plainTextToken;
        }

        return response()->json([
            'message'        => $message,
            'user'           => $user,
            'token'          => $token,
            'email_verified' => true,
            'success'        => true,
        ]);
    }

    public function updateDelivery(Request $request)
    {

        $addresses = CreditDelivery::where('id', $request->address_id)->first();

        if ($addresses) {
            // // Ensure address data is properly formatted
            // $addressData = $addresses->count() === 1 ? $addresses->first()->address : $addresses->pluck('address');
            // $addresses->credit_id = $request->input('credit_id');  // Add this if 'credit_id' is a column
            $addresses->post_code = $request->input('post_code');
            $addresses->address1 = $request->input('address1');
            $addresses->address2 = $request->input('address2');
            $addresses->address3 = $request->input('address3');
            $addresses->town = $request->input('town');
            $addresses->city = $request->input('city');
            $addresses->county = $request->input('county');
            $addresses->country = $request->input('country');

            // Save the record in the database
            $addresses->update();
            $message = "Delivery Addresse Update";
        }
        return response()->json([
            'message'    => $message

        ]);
    }

    public function contactStore(Request $request)
    {
        $firstName = $request->input('name');
        $lastName = $request->input('last_name');
        $email = $request->input('email');
        $phone_number = $request->input('phone_number');
        $message = $request->input('message');




        $user = new User();
        $user->name = $firstName;
        $user->last_name = $lastName;
        $existingContact = User::where('email', $email)->first();

        if ($existingContact) {
            return response()->json(['message' => 'Email already exists. Please use a different email address.'], 400);
        }
        $user->email = $email;

        $user->user_from = 'website';
        $user->user_type = 'contact_customer';
        $user->phone = $phone_number;
        $user->message = $message;
        $user->save();

        return response()->json([
            'user_id' => $user->id,
            'first_name' => $user->name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'user_type' => $user->user_type,
            'message' => $user->message,

        ]);
    }

    public function pharmaceuticalUpdate(Request $request)
    {
        $validated = $request->validate([
            'user_id'           => 'required|exists:users,id',
            'first_name'        => 'nullable|string|max:255',
            'last_name'         => 'nullable|string|max:255',

            'bussiness_name'    => 'nullable|string|max:255',
            'account_number'    => 'nullable|string|max:255',
            'license_type'      => 'nullable|string|max:255',
            'license_name'      => 'nullable|string|max:255',
            'license_number'    => 'nullable|string|max:255',
            'registration_date' => 'nullable|date',
            'signature'         => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($validated['user_id']);

            // Update user (only if provided)
            $user->update([
                'name'               => $validated['first_name'] ?? $user->name,
                'last_name'          => $validated['last_name'] ?? $user->last_name,
                'is_pharmaceutical'  => 1,
            ]);

            // Update or create pharma account
            $pharmaAccount = PharmaceuticalAccount::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name'      => $validated['bussiness_name'] ?? null,
                    'account_number'    => $validated['account_number'] ?? null,
                    'license_type'      => $validated['license_type'] ?? null,
                    'license_name'      => $validated['license_name'] ?? null,
                    'license_number'    => $validated['license_number'] ?? null,
                    'registration_date' => $validated['registration_date'] ?? null,
                    'signature'         => $validated['signature'] ?? null,
                ]
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Pharmaceutical account updated successfully',

                'data' => [
                    'user' => [
                        'id'                 => $user->id,
                        'first_name'         => $user->name,
                        'last_name'          => $user->last_name,
                        'email'              => $user->email,
                        'is_pharmaceutical'  => $user->is_pharmaceutical,
                    ],
                    'pharma_account' => [
                        'company_name'      => $pharmaAccount->company_name,
                        'account_number'    => $pharmaAccount->account_number,
                        'license_type'      => $pharmaAccount->license_type,
                        'license_name'      => $pharmaAccount->license_name,
                        'license_number'    => $pharmaAccount->license_number,
                        'registration_date' => $pharmaAccount->registration_date,
                        'signature'         => $pharmaAccount->signature,
                    ]
                ]
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('Pharma Update Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }


    public function credit_show(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $user = User::with([
                'registerCredit',
                'creditDelivery'
            ])->findOrFail($validated['user_id']);

            $credit = $user->registerCredit;
            $deliveries = $user->creditDelivery ?? collect();

            return response()->json([
                'status' => true,
                'data' => [
                    'user' => [
                        'id'         => $user->id,
                        'first_name' => $user->name,
                        'last_name'  => $user->last_name,
                        'email'      => $user->email,
                        'user_type'  => "credit_customer",
                    ],

                    'credit' => [
                        'company_name'      => $credit?->company_name,
                        'organization_type' => $credit?->organization_type,
                        'department_name'   => $credit?->department_name,
                        'statement_email'   => $credit?->statement_email,
                        'phone_number'      => $credit?->phone_number,
                        'mobile_number'     => $credit?->mobile_number,
                        'organization_name' => $credit?->organization_name,
                    ],

                    'delivery_addresses' => $deliveries->map(function ($delivery) {
                        return [
                            'address_id'  => $delivery->id,
                            'address1'    => $delivery->address1,
                            'address2'    => $delivery->address2,
                            'address3'    => $delivery->address3,
                            'country'     => $delivery->country,
                            'state'       => $delivery->state,
                            'city'        => $delivery->city,
                            'county'      => $delivery->county,
                            'town'        => $delivery->town,
                            'postal_code' => $delivery->post_code,
                        ];
                    }),
                ]
            ]);
        } catch (\Throwable $e) {
            \Log::error('Credit Show Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }


    public function customerShow(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $user = User::with([
                'registerCredit',
                'creditDelivery'
            ])->findOrFail($validated['user_id']);

            $credit = $user->registerCredit;
            $deliveries = $user?->creditDelivery ?? collect();

            return response()->json([
                'status' => true,
                'data' => [
                    'user' => [
                        'id'         => $user->id,
                        'first_name' => $user->name,
                        'last_name'  => $user->last_name,
                        'email'      => $user->email,
                        'user_type'  => "register_customer",
                    ],

                    'register' => [
                        'register_id'       => $credit?->id ?? null,
                        'business_name'      => $credit?->company_name ?? null,
                        'mobile_number'     => $credit?->mobile_number ?? null,
                    ],

                    'delivery_addresses' => $deliveries->map(function ($delivery) {
                        return [
                            'address_id'  => $delivery->id,
                            'address1'    => $delivery->address1 ?? null,
                            'address2'    => $delivery->address2 ?? null,
                            'address3'    => $delivery->address3 ?? null,
                            'country'     => $delivery->country ?? null,
                            'state'       => $delivery->state ?? null,
                            'city'        => $delivery->city ?? null,
                            'county'      => $delivery->county ?? null,
                            'town'        => $delivery->town ?? null,
                            'postal_code' => $delivery->post_code ?? null,
                        ];
                    }),
                ]
            ]);
        } catch (\Throwable $e) {
            \Log::error('Credit Show Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function pharma_show(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $user = User::with(['registerCredit.creditDelivery'])->findOrFail($validated['user_id']);

            $credit = $user->pharmaceuticalAccount;
            $deliveries = $user?->creditDelivery ?? collect();

            return response()->json([
                'status' => true,
                'data' => [
                    'user' => [
                        'id'                  => $user->id,
                        'first_name'          => $credit?->holder_first_name,
                        'last_name'           => $credit?->holder_last_name,
                        'email'               => $credit?->holder_email,
                        'user_type'           => 'pharma_customer',
                        'is_pharmaceutical'   => (bool) $user->is_pharmaceutical,
                        'is_pharma_approved'  => (bool) $user->is_pharma_approved,
                    ],

                    'pharma_account' => [
                        'company_name'      => $credit?->company_name,
                        'account_number'    => $credit?->account_number,
                        'registration_date' => $credit?->registration_date,
                        'license_number'    => $credit?->license_number,
                        'license_name'      => $credit?->license_name,
                        'license_type'      => $credit?->license_type,
                        'signature'         => $credit?->signature,
                    ],

                    'delivery_addresses' => $deliveries->map(function ($delivery) {
                        return [
                            'address1'     => $delivery->address1,
                            'address2'     => $delivery->address2,
                            'address3'     => $delivery->address3,
                            'country'      => $delivery->country,
                            'state'        => $delivery->state,
                            'city'         => $delivery->city,
                            'postal_code'  => $delivery->post_code,
                        ];
                    }),
                ]
            ]);
        } catch (\Throwable $e) {
            \Log::error('Pharma Show Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
