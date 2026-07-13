<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class OptionalSanctumAuth
{
    /**
     * Attempt Sanctum token authentication without requiring it.
     * Guests pass through; authenticated users get auth()->user() resolved.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->bearerToken()) {
            try {
                $user = auth('sanctum')->authenticate();
                if ($user) {
                    auth()->setUser($user);
                }
            } catch (\Throwable $e) {
                // Invalid or expired token — treat as guest, don't block
            }
        }

        return $next($request);
    }
}
