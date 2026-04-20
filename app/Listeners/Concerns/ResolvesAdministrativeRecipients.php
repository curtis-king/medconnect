<?php

namespace App\Listeners\Concerns;

use App\Models\User;
use Illuminate\Support\Collection;

trait ResolvesAdministrativeRecipients
{
    protected function administrativeRecipients(): Collection
    {
        return User::query()
            ->whereIn('role', [
                User::ROLE_ADMIN,
                User::ROLE_FINANCIERE,
                User::ROLE_SERVICE_CLIENT,
            ])
            ->get();
    }
}
