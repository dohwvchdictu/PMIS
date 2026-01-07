<?php

return [
    'enabled' => env('AUDITING_ENABLED', true),

    'implementation' => OwenIt\Auditing\Models\Audit::class,

    'user' => [
        'morph_prefix' => 'user',
        'guards' => [
            'web',
            'sanctum',
        ],
        'resolver' => function () {
            try {
                // Try web guard first (Livewire end users)
                if (auth()->guard('web')->check()) {
                    return auth()->guard('web')->user();
                }

                // Try Filament guard (admin panel)
                if (class_exists(\Filament\Facades\Filament::class)) {
                    if (\Filament\Facades\Filament::auth()->check()) {
                        return \Filament\Facades\Filament::auth()->user();
                    }
                }

                // Try sanctum guard (if used for API)
                if (auth()->guard('sanctum')->check()) {
                    return auth()->guard('sanctum')->user();
                }

            } catch (\Throwable $e) {
                \Log::error('Audit user resolver error: ' . $e->getMessage());
            }

            return null; // Allow operations without authenticated user
        },
    ],

    'resolver' => [
        'ip_address' => null,
        'user_agent' => null,
        'url' => null,
    ],

    'events' => [
        'created',
        'updated',
        'deleted',
        'restored',
    ],

    'strict' => false,
    'timestamps' => true,
    'threshold' => 0,
    'driver' => 'database',

    'drivers' => [
        'database' => [
            'table' => 'audits',
            'connection' => null,
        ],
    ],

    'console' => false,
];
