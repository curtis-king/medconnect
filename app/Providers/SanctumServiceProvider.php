<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use Illuminate\Auth\TokenGuard;
use Illuminate\Support\ServiceProvider;

class SanctumServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerGuard();
    }

    /**
     * Register guard for Sanctum authentication.
     */
    private function registerGuard(): void
    {
        $this->app['auth']->extend('sanctum', function ($app, $name, array $config) {
            return new TokenGuard(
                $app['auth']->createUserProvider($config['provider']),
                $app['request'],
                $config
            );
        });

        // Add a custom token retrieval callback
        $this->app['auth']->viaRequest('sanctum', function ($request) {
            // Get the Bearer token from the Authorization header
            $header = $request->header('Authorization', '');

            if (! preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
                return null;
            }

            $plainTextToken = $matches[1];

            // Hash the token to match what's stored in the database
            $hashedToken = hash('sha256', $plainTextToken);

            // Find the token in the database
            $token = PersonalAccessToken::where('token', $hashedToken)->first();

            if (! $token) {
                return null;
            }

            // Check if token has expired
            if ($token->expires_at && now()->isAfter($token->expires_at)) {
                $token->delete();

                return null;
            }

            // Get and return the user associated with the token
            $user = $token->tokenable()->first();

            if ($user) {
                $user->_token = $token;
            }

            return $user;
        });
    }
}
