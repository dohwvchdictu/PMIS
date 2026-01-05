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
        return \App\Models\User::resolveAuditUser();
    },
],

    'resolver' => [
        'ip_address' => function () {
            return request()->ip();
        },
        'user_agent' => function () {
            return request()->userAgent();
        },
        'url' => function () {
            return request()->fullUrl();
        },
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