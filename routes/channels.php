<?php

use App\Models\DossierProfessionnel;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('patient.{id}', function (User $user, int $id) {
    return $user->id === $id;
});

Broadcast::channel('professionnel.{dossierId}', function (User $user, int $dossierId) {
    if (! in_array($user->role, [User::ROLE_PROFESSIONAL, User::ROLE_SOIGNANT], true)) {
        return false;
    }

    return DossierProfessionnel::query()
        ->whereKey($dossierId)
        ->where('user_id', $user->id)
        ->exists();
});

Broadcast::channel('admin', function (User $user) {
    return in_array($user->role, [
        User::ROLE_ADMIN,
        User::ROLE_FINANCIERE,
        User::ROLE_SERVICE_CLIENT,
    ], true);
});
