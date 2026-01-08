<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\UserResolver as UserResolverContract;

class CustomUserResolver implements UserResolverContract
{
    public static function resolve()
    {
        try {
            // Try web guard first
            if (Auth::guard('web')->check()) {
                return Auth::guard('web')->user();
            }

            // Try Filament auth
            if (class_exists(\Filament\Facades\Filament::class)) {
                $filamentAuth = \Filament\Facades\Filament::auth();
                if ($filamentAuth->check()) {
                    return $filamentAuth->user();
                }
            }

            // Try sanctum guard
            if (Auth::guard('sanctum')->check()) {
                return Auth::guard('sanctum')->user();
            }

        } catch (\Throwable $e) {
            \Log::error('Audit user resolver error: ' . $e->getMessage());
        }

        return null;
    }
}
