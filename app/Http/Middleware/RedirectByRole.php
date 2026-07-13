<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectByRole
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        if ($user->user_type === 'admin' || $user->user_type === 'staff') {
            return redirect('/admin');
        }

        return redirect('/dashboard');
    }
}