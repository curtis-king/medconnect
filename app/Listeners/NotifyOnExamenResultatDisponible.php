<?php

namespace App\Listeners;

use App\Events\ExamenResultatDisponible;
use App\Listeners\Concerns\ResolvesAdministrativeRecipients;
use App\Notifications\ExamenResultatDisponibleNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyOnExamenResultatDisponible implements ShouldQueue
{
    use ResolvesAdministrativeRecipients;

    public function handle(ExamenResultatDisponible $event): void
    {
        $examen = $event->examen->loadMissing('patient', 'professionnel');

        if ($examen->patient) {
            $examen->patient->notify(new ExamenResultatDisponibleNotification($examen));
        }

        if ($examen->professionnel) {
            $examen->professionnel->notify(new ExamenResultatDisponibleNotification($examen));
        }

        $this->administrativeRecipients()->each(
            fn ($admin) => $admin->notify(new ExamenResultatDisponibleNotification($examen))
        );
    }
}
