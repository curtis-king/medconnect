<?php

namespace App\Listeners;

use App\Events\RendezVousSoumis;
use App\Listeners\Concerns\ResolvesAdministrativeRecipients;
use App\Notifications\RendezVousSoumisNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyOnRendezVousSoumis implements ShouldQueue
{
    use ResolvesAdministrativeRecipients;

    public function handle(RendezVousSoumis $event): void
    {
        $rendezVous = $event->rendezVous->loadMissing('dossierProfessionnel.user', 'patient');

        if ($rendezVous->dossierProfessionnel?->user) {
            $rendezVous->dossierProfessionnel->user->notify(new RendezVousSoumisNotification($rendezVous));
        }

        if ($rendezVous->patient) {
            $rendezVous->patient->notify(new RendezVousSoumisNotification($rendezVous));
        }

        $this->administrativeRecipients()->each(
            fn ($admin) => $admin->notify(new RendezVousSoumisNotification($rendezVous))
        );
    }
}
