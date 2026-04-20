<?php

namespace App\Listeners;

use App\Events\FactureBackofficeMiseAJour;
use App\Listeners\Concerns\ResolvesAdministrativeRecipients;
use App\Notifications\FactureBackofficeMiseAJourNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyOnFactureBackofficeMiseAJour implements ShouldQueue
{
    use ResolvesAdministrativeRecipients;

    public function handle(FactureBackofficeMiseAJour $event): void
    {
        $facture = $event->facture->loadMissing('patient', 'professionnel');

        if ($facture->patient) {
            $facture->patient->notify(new FactureBackofficeMiseAJourNotification($facture));
        }

        if ($facture->professionnel) {
            $facture->professionnel->notify(new FactureBackofficeMiseAJourNotification($facture));
        }

        $this->administrativeRecipients()->each(
            fn ($admin) => $admin->notify(new FactureBackofficeMiseAJourNotification($facture))
        );
    }
}
