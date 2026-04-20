<?php

namespace App\Listeners;

use App\Events\FacturePaiementPatientMisAJour;
use App\Listeners\Concerns\ResolvesAdministrativeRecipients;
use App\Notifications\FacturePaiementPatientMisAJourNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyOnFacturePaiementPatientMisAJour implements ShouldQueue
{
    use ResolvesAdministrativeRecipients;

    public function handle(FacturePaiementPatientMisAJour $event): void
    {
        $facture = $event->facture->loadMissing('patient');

        if ($facture->patient) {
            $facture->patient->notify(new FacturePaiementPatientMisAJourNotification($facture));
        }

        $this->administrativeRecipients()->each(
            fn ($admin) => $admin->notify(new FacturePaiementPatientMisAJourNotification($facture))
        );
    }
}
