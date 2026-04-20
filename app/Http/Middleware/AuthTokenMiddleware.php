<?php

namespace App\Http\Middleware;

use App\Models\AuthToken;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $this->extractToken($request);

        if (! $token) {
            return response()->json([
                'message' => 'Token requis',
            ], 401);
        }

        $authToken = AuthToken::where('token', $token)
            ->where(function ($query) {
                $query->where('expires_at', '>', now())
                    ->orWhereNull('expires_at');
            })
            ->first();

        if (! $authToken) {
            return response()->json([
                'message' => 'Token invalide ou expiré',
            ], 401);
        }

        $user = User::find($authToken->user_id);

        if (! $user) {
            return response()->json([
                'message' => 'Utilisateur non trouvé',
            ], 401);
        }

        Auth::login($user);

        return $next($request);
    }

    private function extractToken(Request $request): ?string
    {
        $header = $request->header('Authorization', '');

        if (preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
