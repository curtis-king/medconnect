<?php

namespace App\Http\Middleware;

use App\Models\AuthToken;
use Closure;
use Illuminate\Http\Request;

class SanctumMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the Bearer token from the Authorization header
        $header = $request->header('Authorization', '');

        if (! preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $plainTextToken = $matches[1];

        // Find the token in the database
        $token = AuthToken::where('token', $plainTextToken)->first();

        if (! $token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Check if token has expired
        if ($token->isExpired()) {
            $token->delete();

            return response()->json(['message' => 'Token expired'], 401);
        }

        // Get the user associated with the token
        $user = $token->user;

        if (! $user) {
            return response()->json(['message' => 'User not found'], 401);
        }

        // Set the authenticated user on the request
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
