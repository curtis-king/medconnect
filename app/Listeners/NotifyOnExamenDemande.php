<?php

namespace App\Listeners;

use App\Events\ExamenDemande;
use App\Listeners\Concerns\ResolvesAdministrativeRecipients;
use App\Notifications\ExamenDemandeNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyOnExamenDemande implements ShouldQueue
{
    use ResolvesAdministrativeRecipients;

    public function handle(ExamenDemande $event): void
    {
        $examen = $event->examen->loadMissing('patient', 'professionnel');

        if ($examen->patient) {
            $examen->patient->notify(new ExamenDemandeNotification($examen));
        }

        if ($examen->professionnel) {
            $examen->professionnel->notify(new ExamenDemandeNotification($examen));
        }

        $this->administrativeRecipients()->each(
            fn ($admin) => $admin->notify(new ExamenDemandeNotification($examen))
        );
    }
}
