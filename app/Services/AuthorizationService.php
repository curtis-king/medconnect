<?php

namespace App\Services;

use App\Models\User;

class AuthorizationService
{
    /**
     * Vérifier si l'utilisateur peut accéder à une action.
     */
    public static function can(User $user, string $action): bool
    {
        $permissions = [
            // Actions pour tous les utilisateurs authentifiés
            'view-profile' => fn ($user) => true,
            'edit-profile' => fn ($user) => true,
            'delete-account' => fn ($user) => true,
            'manage-devices' => fn ($user) => true,

            // Actions pour rôles spécifiques
            'view-admin-dashboard' => fn ($user) => in_array($user->role, [User::ROLE_ADMIN]),
            'manage-users' => fn ($user) => in_array($user->role, [User::ROLE_ADMIN]),
            'manage-invoices' => fn ($user) => in_array($user->role, [User::ROLE_ADMIN, User::ROLE_FINANCIERE]),
            'view-clients' => fn ($user) => in_array($user->role, [User::ROLE_ADMIN, User::ROLE_PROFESSIONAL]),
            'create-appointment' => fn ($user) => in_array($user->role, [User::ROLE_USER, User::ROLE_MEMBRE]),
        ];

        if (! isset($permissions[$action])) {
            return false;
        }

        return $permissions[$action]($user);
    }

    /**
     * Vérifier si l'utilisateur a un rôle spécifique.
     */
    public static function hasRole(User $user, string ...$roles): bool
    {
        return in_array($user->role, $roles);
    }

    /**
     * Autoriser une action ou lever une exception 403.
     */
    public static function authorize(User $user, string $action): void
    {
        if (! self::can($user, $action)) {
            abort(403, "Vous n'êtes pas autorisé à effectuer cette action: {$action}");
        }
    }

    /**
     * Ajouter une permission personnalisée dynamiquement.
     */
    private static array $customPermissions = [];

    public static function definePermission(string $action, callable $callback): void
    {
        self::$customPermissions[$action] = $callback;
    }

    public static function canCustom(User $user, string $action): bool
    {
        if (isset(self::$customPermissions[$action])) {
            return self::$customPermissions[$action]($user);
        }

        return self::can($user, $action);
    }
}
