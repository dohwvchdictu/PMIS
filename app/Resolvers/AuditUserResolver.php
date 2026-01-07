<?php

namespace App\Resolvers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditUserResolver
{
    public function __invoke()
    {
        try {
            // Try web guard first (Livewire end users)
            if (Auth::guard('web')->check()) {
                return Auth::guard('web')->user();
            }

            // Try Filament guard (admin panel)
            if (class_exists(\Filament\Facades\Filament::class)) {
                if (\Filament\Facades\Filament::auth()->check()) {
                    return \Filament\Facades\Filament::auth()->user();
                }
            }

            // Try sanctum guard (if used for API)
            if (Auth::guard('sanctum')->check()) {
                return Auth::guard('sanctum')->user();
            }

        } catch (\Throwable $e) {
            Log::error('Audit user resolver error: ' . $e->getMessage());
        }

        return null; // Allow operations without authenticated user
    }
}
