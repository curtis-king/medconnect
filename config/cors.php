<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:8081',
        'http://localhost:8082',
        'http://localhost:3000',
        'http://localhost:5173',
        'http://127.0.0.1:8081',
        'http://127.0.0.1:8082',
        'http://127.0.0.1:3000',
        'http://127.0.0.1:5173',
        'http://192.168.1.188:8081',
        'http://192.168.1.188:3000',
    ],

    'allowed_origins_patterns' => [
        '/localhost:\d+/',
        '/127\.0\.0\.1:\d+/',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
