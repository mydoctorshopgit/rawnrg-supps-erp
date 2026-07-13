<?php

namespace App\Http\Controllers\Api\V2;

use App\Notifications\AppEmailVerificationNotification;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordReset;
use App\Notifications\PasswordResetRequest;
use Illuminate\Support\Str;
use App\Http\Controllers\OTPVerificationController;

use Hash;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{

    public function forgetRequest(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'result' => false,
                'message' => translate('User is not found')
            ], 404);
        }

        $code = random_int(100000, 999999);

        $user->update([
            'verification_code' => $code
        ]);

        try {
            $user->notify(new AppEmailVerificationNotification($user));
        } catch (\Exception $e) {
            Log::error('Email notification failed: ' . $e->getMessage());
        }

        return response()->json([
            'result' => true,
            'message' => translate('A code is sent')
        ]);
    }
    public function verifyCode(Request $request)
    {
        // 1. Validate input
        $request->validate([
            'email' => 'required|email',
            'verification_code' => 'required|numeric',
        ]);

        // 2. Find user with matching email + code
        $user = User::where('email', $request->email)
            ->where('verification_code', $request->verification_code)
            ->first();

        if (!$user) {
            return response()->json([
                'result' => false,
                'message' => translate('Invalid verification code or email'),
            ], 404);
        }

        // 3. Optional: expiry check
        if ($user->verification_code_created_at) {
            $expiresAt = now()->subMinutes(10);

            if ($user->verification_code_created_at < $expiresAt) {
                return response()->json([
                    'result' => false,
                    'message' => translate('Verification code has expired'),
                ], 410);
            }
        }

        // 4. Mark code as verified (temporary flag or token)
        // You can either generate a reset token or just confirm step
        $resetToken = \Str::random(60);

        $user->update([
            'verification_code' => null, // prevent reuse
            'verification_code_created_at' => null,
            'password_reset_token' => $resetToken, // optional column
        ]);

        return response()->json([
            'result' => true,
            'message' => translate('Code verified successfully'),
            'reset_token' => $resetToken, // used in next step to reset password
        ]);
    }

    public function confirmReset(Request $request)
    {
        // 1. Validate input
        $request->validate([
            'reset_token' => 'required|string',
            'password' => 'required|min:6|confirmed',
        ]);

        // 2. Find user by reset token
        $user = User::where('password_reset_token', $request->reset_token)->first();

        if (!$user) {
            return response()->json([
                'result' => false,
                'message' => translate('Invalid or expired reset token'),
            ], 404);
        }

        // 3. Update password and clear token
        $user->update([
            'password' => Hash::make($request->password),
            'password_reset_token' => null, // VERY IMPORTANT
            'verification_code' => null,
            'verification_code_created_at' => null,
        ]);

        return response()->json([
            'result' => true,
            'message' => translate('Your password has been reset successfully'),
        ], 200);
    }

    public function resendCode(Request $request)
    {
        // 1. Validate input
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = strtolower(trim($request->email));

        // 2. Find user
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'result' => false,
                'message' => translate('User is not found')
            ], 404);
        }

        // 3. Generate verification code
        $code = random_int(100000, 999999);

        // 4. Update user with code + timestamp
        $user->update([
            'verification_code' => $code,
            'verification_code_created_at' => now(),
        ]);

        // 5. Send email notification safely
        try {
            $user->notify(new AppEmailVerificationNotification($user));
        } catch (\Exception $e) {
            \Log::error('Resend code email failed: ' . $e->getMessage());

            return response()->json([
                'result' => false,
                'message' => translate('Failed to send verification code'),
            ], 500);
        }

        return response()->json([
            'result' => true,
            'message' => translate('A code is sent again'),
        ], 200);
    }
}
