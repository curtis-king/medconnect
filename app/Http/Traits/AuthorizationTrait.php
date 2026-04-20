<?php

namespace App\Http\Traits;

use App\Services\AuthorizationService;
use Illuminate\Http\Request;

trait AuthorizationTrait
{
    /**
     * Vérifier si l'utilisateur peut effectuer une action.
     */
    protected function authorize(Request $request, string $action): void
    {
        AuthorizationService::authorize($request->user(), $action);
    }

    /**
     * Vérifier si l'utilisateur a une permission.
     */
    protected function can(Request $request, string $action): bool
    {
        return AuthorizationService::can($request->user(), $action);
    }

    /**
     * Vérifier si l'utilisateur a un rôle.
     */
    protected function hasRole(Request $request, string ...$roles): bool
    {
        return AuthorizationService::hasRole($request->user(), ...$roles);
    }
}
