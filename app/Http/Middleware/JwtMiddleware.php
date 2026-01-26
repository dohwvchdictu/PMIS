<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Validates JWT token from session and ensures it hasn't expired.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            Session::forget(['jwt_token', 'token_created_at', 'login_credentials']);
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Validate JWT token exists and is not expired
        $token = Session::get('jwt_token');
        $tokenCreatedAt = Session::get('token_created_at', 0);
        $tokenTTL = config('jwt.ttl', 300); // Default 5 minutes

        if (!$token) {
            Session::forget(['jwt_token', 'token_created_at', 'login_credentials']);
            auth()->logout();
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        // Check if token has expired
        if ((time() - $tokenCreatedAt) >= $tokenTTL) {
            Session::forget(['jwt_token', 'token_created_at', 'login_credentials']);
            auth()->logout();
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        return $next($request);
    }
}
