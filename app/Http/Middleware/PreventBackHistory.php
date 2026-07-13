<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventBackHistory
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    // private const SYSTEM_KEY = '123456789'; 
    public function handle(Request $request, Closure $next)
    {
        $headers = [
            'Cache-Control' => 'nocache, no-store, max-age=0, must-revalidate',
            'Pragma'        => 'no-cache',
            'Expires'       => 'Sat, 26 Jul 2050 05:00:00 GMT'
        ];
        
        $response = $next($request);
        
        foreach($headers as $key => $value) {
            $response->headers->set($key, $value);
        }
        
        // $systemKey = env('SYSTEM_KEY', 'default_key');
        // $response->headers->set('X-System-Key', $systemKey);

        return $response;
    }
}
