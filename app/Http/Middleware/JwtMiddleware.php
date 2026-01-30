<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;
use App\Services\ApiService;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Validates JWT token from session and automatically refreshes it before expiration.
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

        // Validate JWT token exists
        $token = Session::get('jwt_token');
        $tokenCreatedAt = Session::get('token_created_at', 0);
        $tokenTTL = config('jwt.ttl', 300); // 5 minutes (API token lifetime)
        $refreshThreshold = config('jwt.refresh_threshold', 240); // 4 minutes (refresh before expiry)
        $tokenAge = time() - $tokenCreatedAt;

        if (!$token) {
            Session::forget(['jwt_token', 'token_created_at', 'login_credentials']);
            auth()->logout();
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        // Check if token has completely expired (8 hours)
        if ($tokenAge >= $tokenTTL) {
            // Try to refresh the token one last time
            if ($this->attemptTokenRefresh()) {
                return $next($request);
            }

            // If refresh failed, log out
            Session::forget(['jwt_token', 'token_created_at', 'login_credentials']);
            auth()->logout();
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        // Automatically refresh token before it expires
        if ($tokenAge >= $refreshThreshold) {
            if (!$this->attemptTokenRefresh()) {
                Session::forget(['jwt_token', 'token_created_at', 'login_credentials']);
                auth()->logout();
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
        }

        return $next($request);
    }

    /**
     * Attempt to refresh the JWT token using stored credentials
     *
     * @return bool True if refresh was successful, false otherwise
     */
    protected function attemptTokenRefresh(): bool
    {
        $credentials = Session::get('login_credentials');

        // Skip refresh for local users (they have mock tokens)
        if ($credentials && isset($credentials['email'])) {
            $token = Session::get('jwt_token');
            if ($token && str_starts_with($token, 'local_token_')) {
                // For local users, just update the timestamp
                Session::put('token_created_at', time());
                return true;
            }
        }

        if ($credentials) {
            try {
                $apiService = app(ApiService::class);
                $response = $apiService->login($credentials);

                if (isset($response['statusCode']) && $response['statusCode'] == 200) {
                    // Token was refreshed successfully by ApiService
                    return true;
                }
            } catch (\Exception $e) {
                // Log the error but don't expose it to the user
                \Log::error('JWT Token Refresh Failed: ' . $e->getMessage());
            }
        }

        return false;
    }
}
