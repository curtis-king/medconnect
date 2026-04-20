<?php

namespace App\Listeners;

use App\Events\RetraitProfessionnelTraite;
use App\Listeners\Concerns\ResolvesAdministrativeRecipients;
use App\Notifications\RetraitProfessionnelTraiteNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyOnRetraitProfessionnelTraite implements ShouldQueue
{
    use ResolvesAdministrativeRecipients;

    public function handle(RetraitProfessionnelTraite $event): void
    {
        $retrait = $event->retrait->loadMissing('dossierProfessionnel.user');

        if ($retrait->dossierProfessionnel?->user) {
            $retrait->dossierProfessionnel->user->notify(new RetraitProfessionnelTraiteNotification($retrait));
        }

        $this->administrativeRecipients()->each(
            fn ($admin) => $admin->notify(new RetraitProfessionnelTraiteNotification($retrait))
        );
    }
}
