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
        'resolver' => \App\Resolvers\AuditUserResolver::class,
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
