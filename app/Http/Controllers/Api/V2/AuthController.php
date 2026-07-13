<?php

/** @noinspection PhpUndefinedClassInspection */

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\OTPVerificationController;
use App\Models\BusinessSetting;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Notifications\AppEmailVerificationNotification;
use Hash;
use GeneaLabs\LaravelSocialiter\Facades\Socialiter;
use Socialite;
use App\Models\Cart;
use App\Rules\Recaptcha;
use App\Services\SocialRevoke;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{

    public function check_approved_pharma(Request $request)
    {
        $user_id = $request->user_id;

        $user = User::where('id', $user_id)->first();

        if (!$user) {
            return response()->json([
                'error' => 'User not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',  // Indicate success
            'is_pharma_approved' => $user->is_pharma_approved,  // Returning the approval status
            'message' => 'User found and pharma approval status fetched successfully'
        ]);
    }

    public function signup(Request $request)
    {
        $messages = array(
            'name.required' => translate('Name is required'),
            'email_or_phone.required' => $request->register_by == 'email' ? translate('Email is required') : translate('Phone is required'),
            'email_or_phone.email' => translate('Email must be a valid email address'),
            'email_or_phone.numeric' => translate('Phone must be a number.'),
            'email_or_phone.unique' => $request->register_by == 'email' ? translate('The email has already been taken') : translate('The phone has already been taken'),
            'password.required' => translate('Password is required'),
            'password.confirmed' => translate('Password confirmation does not match'),
            'password.min' => translate('Minimum 6 digits required for password')
        );
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required|min:6|confirmed',
            'email_or_phone' => [
                'required',
                Rule::when($request->register_by === 'email', ['email', 'unique:users,email']),
                Rule::when($request->register_by === 'phone', ['numeric', 'unique:users,phone']),
            ],
            'g-recaptcha-response' => [
                Rule::when(get_setting('google_recaptcha') == 1, ['required', new Recaptcha()], ['sometimes'])
            ]
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => $validator->errors()->all()
            ]);
        }

        $user = new User();
        $user->name = $request->name;
        if ($request->register_by == 'email') {

            $user->email = $request->email_or_phone;
        }
        if ($request->register_by == 'phone') {
            $user->phone = $request->email_or_phone;
        }
        $user->password = Hash::make($request->password);
        $user->verification_code = rand(100000, 999999);
        $user->save();


        $user->email_verified_at = null;
        if ($user->email != null) {
            if (BusinessSetting::where('type', 'email_verification')->first()->value != 1) {
                $user->email_verified_at = date('Y-m-d H:m:s');
            }
        }

        if ($user->email_verified_at == null) {
            if ($request->register_by == 'email') {
                try {
                    $user->notify(new AppEmailVerificationNotification());
                } catch (\Exception $e) {
                }
            } else {
                $otpController = new OTPVerificationController();
                $otpController->send_code($user);
            }
        }

        $user->save();
        //create token
        $user->createToken('tokens')->plainTextToken;

        return $this->loginSuccess($user);
    }

    public function resendCode()
    {
        $user = auth()->user();
        $user->verification_code = rand(100000, 999999);

        if ($user->email) {
            try {
                $user->notify(new AppEmailVerificationNotification());
            } catch (\Exception $e) {
            }
        } else {
            $otpController = new OTPVerificationController();
            $otpController->send_code($user);
        }

        $user->save();

        return response()->json([
            'result' => true,
            'message' => translate('Verification code is sent again'),
        ], 200);
    }

    public function confirmCode(Request $request)
    {
        $user = auth()->user();

        if ($user->verification_code == $request->verification_code) {
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->verification_code = null;
            $user->save();
            return response()->json([
                'result' => true,
                'message' => translate('Your account is now verified'),
            ], 200);
        } else {
            return response()->json([
                'result' => false,
                'message' => translate('Code does not match, you can request for resending the code'),
            ], 200);
        }
    }


    public function login(Request $request)
    {
        // Normalize inputs
        $loginBy = $request->input('login_by');
        $loginValue = trim($request->input('email'));
        $password = $request->input('password');

        if ($loginBy === 'email') {
            $loginValue = strtolower($loginValue);
        } elseif ($loginBy === 'phone') {
            $loginValue = preg_replace('/\D/', '', $loginValue);
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'login_by' => 'required|in:email,phone',
            'email'    => ['required', $loginBy === 'email' ? 'email' : 'numeric'],
            'password' => 'required|string',
        ], [
            'email.required' => $loginBy === 'email'
                ? translate('Email is required')
                : translate('Phone is required'),
            'email.email'   => translate('Email must be valid'),
            'email.numeric' => translate('Phone must be numeric'),
            'password.required' => translate('Password is required'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Find user
            $user = $loginBy === 'email'
                ? User::whereRaw('LOWER(email) = ?', [$loginValue])->first()
                : User::where('phone', $loginValue)->first();

            if (!$user) {
                Log::info("Login failed: User not found ({$loginBy}: {$loginValue})");

                return response()->json([
                    'status'  => false,
                    'message' => translate('User not found'),
                ], 401);
            }

            // Check banned
            if ($user->banned) {
                return response()->json([
                    'status'  => false,
                    'message' => translate('User is banned'),
                ], 403);
            }

            // Check password
            if (!Hash::check($password, $user->password)) {
                Log::info("Login failed: Incorrect password (user_id: {$user->id})");

                return response()->json([
                    'status'  => false,
                    'message' => translate('Incorrect password'),
                ], 401);
            }

            // Success
            return $this->loginSuccess($user);
        } catch (\Throwable $e) {
            Log::error('Login Error: ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }



    public function user(Request $request)
    {
        try {
            $user = User::with([
                'registerCredit.creditDelivery',
                'creditDelivery',
                'pharmaceuticalAccount.creditDelivery'
            ])->findOrFail(auth()->id());

            // ✅ Detect user type
            if ($user->pharmaceuticalAccount) {

                $pharma = $user->pharmaceuticalAccount;
                $deliveries = $pharma?->creditDelivery ?? collect();

                return response()->json([
                    'status' => true,
                    'data' => [
                        'user' => [
                            'id'                 => $user->id,
                            'first_name'         => $pharma?->holder_first_name,
                            'last_name'          => $pharma?->holder_last_name,
                            'email'              => $pharma?->holder_email,
                            'user_type'          => 'pharma_customer',
                            'is_pharmaceutical'  => (bool) $user->is_pharmaceutical,
                            'is_pharma_approved' => (bool) $user->is_pharma_approved,
                        ],

                        'pharma_account' => [
                            'company_name'      => $pharma?->company_name,
                            'account_number'    => $pharma?->account_number,
                            'registration_date' => $pharma?->registration_date,
                            'license_number'    => $pharma?->license_number,
                            'license_name'      => $pharma?->license_name,
                            'license_type'      => $pharma?->license_type,
                            'signature'         => $pharma?->signature,
                        ],

                        'delivery_addresses' => $deliveries->map(fn($d) => [
                            'address1'    => $d->address1,
                            'address2'    => $d->address2,
                            'address3'    => $d->address3,
                            'country'     => $d->country,
                            'state'       => $d->state,
                            'city'        => $d->city,
                            'postal_code' => $d->post_code,
                        ]),
                    ]
                ]);
            }

            // ✅ Credit Customer
            if ($user->registerCredit) {

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
                        'user_type'  => $user->user_type == "customer" ? "register_customer" : "credit_customer",
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

                        'delivery_addresses' => $deliveries->map(fn($d) => [
                            'address_id'  => $d->id,
                            'address1'    => $d->address1,
                            'address2'    => $d->address2,
                            'address3'    => $d->address3,
                            'country'     => $d->country,
                            'state'       => $d->state,
                            'city'        => $d->city,
                            'county'      => $d->county,
                            'town'        => $d->town,
                            'postal_code' => $d->post_code,
                        ]),
                    ]
                ]);
            }

            // ✅ Register Customer (default fallback)
            $credit = $user->registerCredit;
            $deliveries = $credit?->creditDelivery ?? collect();

            return response()->json([
                'status' => true,
                'data' => [
                    'user' => [
                        'id'         => $user->id,
                        'first_name' => $user->name,
                        'last_name'  => $user->last_name,
                        'email'      => $user->email,
                        'user_type'  => $user->user_type == "customer" ? "register_customer" : "credit_customer",
                    ],

                    'register' => [
                        'register_id'   => $credit?->id ?? null,
                        'business_name' => $credit?->company_name ?? null,
                        'mobile_number' => $credit?->mobile_number ?? null,
                    ],

                    'delivery_addresses' => $deliveries->map(fn($d) => [
                        'address_id'  => $d->id,
                        'address1'    => $d->address1 ?? null,
                        'address2'    => $d->address2 ?? null,
                        'address3'    => $d->address3 ?? null,
                        'country'     => $d->country ?? null,
                        'state'       => $d->state ?? null,
                        'city'        => $d->city ?? null,
                        'county'      => $d->county ?? null,
                        'town'        => $d->town ?? null,
                        'postal_code' => $d->post_code ?? null,
                    ]),
                ]
            ]);
        } catch (\Throwable $e) {
            \Log::error('User API Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function logout(Request $request)
    {

        $user = request()->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        return response()->json([
            'result' => true,
            'message' => translate('Successfully logged out')
        ]);
    }

    public function socialLogin(Request $request)
    {
        if (!$request->provider) {
            return response()->json([
                'result' => false,
                'message' => translate('User not found'),
                'user' => null
            ]);
        }

        switch ($request->social_provider) {
            case 'facebook':
                $social_user = Socialite::driver('facebook')->fields([
                    'name',
                    'first_name',
                    'last_name',
                    'email'
                ]);
                break;
            case 'google':
                $social_user = Socialite::driver('google')
                    ->scopes(['profile', 'email']);
                break;
            case 'twitter':
                $social_user = Socialite::driver('twitter');
                break;
            case 'apple':
                $social_user = Socialite::driver('sign-in-with-apple')
                    ->scopes(['name', 'email']);
                break;
            default:
                $social_user = null;
        }
        if ($social_user == null) {
            return response()->json(['result' => false, 'message' => translate('No social provider matches'), 'user' => null]);
        }

        if ($request->social_provider == 'twitter') {
            $social_user_details = $social_user->userFromTokenAndSecret($request->access_token, $request->secret_token);
        } else {
            $social_user_details = $social_user->userFromToken($request->access_token);
        }

        if ($social_user_details == null) {
            return response()->json(['result' => false, 'message' => translate('No social account matches'), 'user' => null]);
        }

        $existingUserByProviderId = User::where('provider_id', $request->provider)->first();

        if ($existingUserByProviderId) {
            $existingUserByProviderId->access_token = $social_user_details->token;
            if ($request->social_provider == 'apple') {
                $existingUserByProviderId->refresh_token = $social_user_details->refreshToken;
                if (!isset($social_user->user['is_private_email'])) {
                    $existingUserByProviderId->email = $social_user_details->email;
                }
            }
            $existingUserByProviderId->save();
            return $this->loginSuccess($existingUserByProviderId);
        } else {
            $existing_or_new_user = User::firstOrNew(
                [['email', '!=', null], 'email' => $social_user_details->email]
            );

            $existing_or_new_user->user_type = 'customer';
            $existing_or_new_user->provider_id = $social_user_details->id;

            if (!$existing_or_new_user->exists) {
                if ($request->social_provider == 'apple') {
                    if ($request->name) {
                        $existing_or_new_user->name = $request->name;
                    } else {
                        $existing_or_new_user->name = 'Apple User';
                    }
                } else {
                    $existing_or_new_user->name = $social_user_details->name;
                }
                $existing_or_new_user->email = $social_user_details->email;
                $existing_or_new_user->email_verified_at = date('Y-m-d H:m:s');
            }

            $existing_or_new_user->save();

            return $this->loginSuccess($existing_or_new_user);
        }
    }

    public function loginSuccess($user, $token = null)
    {
        $token = $token ?? $user->createToken('API Token')->plainTextToken;

        // Fix: no need for optional()->first()
        $organizationType = optional($user->registerCredit)->organization_type;

        return response()->json([
            'status' => true,
            'message' => translate('Successfully logged in'),

            'data' => [
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'expires_at'   => null,

                'user' => [
                    'id'                   => $user->id,
                    'name'                 => $user->name,
                    'email'                => $user->email,
                    'phone'                => $user->phone,
                    'user_type'            => $user->user_type == "customer" ? "register_customer" : ($user->user_type == "customer_credit" ? "credit_customer" : "pharma_customer"),
                    'organization_type'    => $organizationType,
                    'is_pharmaceutical'    => (bool) $user->is_pharmaceutical,
                    'is_pharma_approved'   => (bool) $user->is_pharma_approved,
                    'avatar'               => $user->avatar,
                    'avatar_original'      => uploaded_asset($user->avatar_original),
                    'email_verified'       => !is_null($user->email_verified_at),
                ]
            ]
        ]);
    }



    protected function loginFailed()
    {

        return response()->json([
            'result' => false,
            'message' => translate('Login Failed'),
            'access_token' => '',
            'token_type' => '',
            'expires_at' => null,
            'user' => [
                'id' => 0,
                'type' => '',
                'name' => '',
                'email' => '',
                'avatar' => '',
                'avatar_original' => '',
                'phone' => ''
            ]
        ]);
    }


    public function account_deletion()
    {
        if (auth()->user()) {
            Cart::where('user_id', auth()->user()->id)->delete();
        }

        // if (auth()->user()->provider && auth()->user()->provider != 'apple') {
        //     $social_revoke =  new SocialRevoke;
        //     $revoke_output = $social_revoke->apply(auth()->user()->provider);

        //     if ($revoke_output) {
        //     }
        // }

        $auth_user = auth()->user();
        $auth_user->tokens()->where('id', $auth_user->currentAccessToken()->id)->delete();
        $auth_user->customer_products()->delete();

        User::destroy(auth()->user()->id);

        return response()->json([
            "result" => true,
            "message" => translate('Your account deletion successfully done')
        ]);
    }

    public function getUserInfoByAccessToken(Request $request)
    {
        $token = PersonalAccessToken::findToken($request->access_token);
        if (!$token) {
            return $this->loginFailed();
        }
        $user = $token->tokenable;

        if ($user == null) {
            return $this->loginFailed();
        }

        return $this->loginSuccess($user, $request->access_token);
    }



    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        // Validation
        $validator = Validator::make($request->all(), [
            'current_password' => ['required'],
            'new_password'     => ['required', 'min:6', 'confirmed'],
            // 'confirmed' expects: new_password_confirmation
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => translate('Current password is incorrect')
            ], 400);
        }

        // Prevent same password reuse (optional but recommended)
        if (Hash::check($request->new_password, $user->password)) {
            return response()->json([
                'message' => translate('New password cannot be same as current password')
            ], 400);
        }

        try {
            DB::transaction(function () use ($user, $request) {

                $user->update([
                    'password' => Hash::make($request->new_password)
                ]);
            });

            return response()->json([
                'message' => translate('Password updated successfully')
            ], 200);
        } catch (\Throwable $e) {

            Log::error('Password Update Error: ' . $e->getMessage(), [
                'user_id' => $user->id
            ]);

            return response()->json([
                'message' => translate('Something went wrong')
            ], 500);
        }
    }
}
