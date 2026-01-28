<?php

return [

    /*
    |--------------------------------------------------------------------------
    | JWT Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for JWT token management in the application.
    | These values control token validity and refresh behavior.
    |
    */

    'secret' => env('JWT_SECRET', env('APP_KEY')),

    'ttl' => env('JWT_TTL', 28800), // Token validity in seconds (default: 8 hours)

    'refresh_threshold' => env('JWT_REFRESH_THRESHOLD', 300), // Refresh after N seconds (default: 5 minutes)

    /*
    |--------------------------------------------------------------------------
    | External API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for external API integration
    |
    */

    'api' => [
        'base_url' => env('API_BASE_URL', 'http://192.168.100.162:8081/'),
        'timeout' => env('API_TIMEOUT', 10),
    ],

];
