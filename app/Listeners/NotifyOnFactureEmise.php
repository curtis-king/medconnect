<?php

namespace App\Listeners;

use App\Events\FactureEmise;
use App\Listeners\Concerns\ResolvesAdministrativeRecipients;
use App\Notifications\FactureEmiseNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyOnFactureEmise implements ShouldQueue
{
    use ResolvesAdministrativeRecipients;

    public function handle(FactureEmise $event): void
    {
        $facture = $event->facture->loadMissing('patient');

        if ($facture->patient) {
            $facture->patient->notify(new FactureEmiseNotification($facture));
        }

        $this->administrativeRecipients()->each(
            fn ($admin) => $admin->notify(new FactureEmiseNotification($facture))
        );
    }
}
