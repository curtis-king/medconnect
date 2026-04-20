<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Sanctum allows you to specify which domains should be considered
    | stateful, meaning side-effects like updating user sessions
    | should take place. SPA applications should skip this feature.
    |
    */

    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,localhost:3001,localhost:5173,127.0.0.1,127.0.0.1:3000,127.0.0.1:3001,127.0.0.1:5173,127.0.0.1:8000',
        env('SANCTUM_STATEFUL_DOMAINS') ? ','.env('SANCTUM_STATEFUL_DOMAINS') : ''
    ))),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guards
    |--------------------------------------------------------------------------
    |
    | This array contains the authentication guards that will be checked when
    | Sanctum is trying to authenticate a request. If none of these guards
    | are able to authenticate the request, Sanctum will use the bearer
    | token to authenticate the request.
    |
    */

    'guard' => ['web', 'sanctum'],

    /*
    |--------------------------------------------------------------------------
    | Expiration Minutes
    |--------------------------------------------------------------------------
    |
    | This value controls the number of minutes until an issued token will be
    | considered expired. If this value is null, personal access tokens do
    | not expire. This won't tweak the lifetime of first-party sessions.
    |
    */

    'expiration' => null,

    /*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    |
    | Sanctum can prefix new tokens with a given value to help identify them
    | when debugging or working with your API. Set this to a value that makes
    | sense for your particular use case. A common prefix might be "laravel".
    |
    */

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | When authenticating your first-party SPA, Sanctum requires you to have
    | the following middleware enabled in your Http kernel. You may change
    | the middleware names. However, these may not be changed from this set.
    |
    */

    'middleware' => [
        'verify_csrf_token' => \App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => \App\Http\Middleware\EncryptCookies::class,
    ],

];
