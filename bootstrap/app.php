<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        channels: __DIR__.'/../routes/channels.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // CORS for mobile apps
        $middleware->api(append: \Illuminate\Http\Middleware\HandleCors::class);

        $middleware->alias([
            'admin' => \App\Http\Middleware\CheckAdminRole::class,
            'admin_or_professional' => \App\Http\Middleware\CheckAdminOrProfessionalRole::class,
            'sanctum' => \App\Http\Middleware\SanctumMiddleware::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'auth.user' => \App\Http\Middleware\AuthenticatedUser::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'auth.token' => \App\Http\Middleware\AuthTokenMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
