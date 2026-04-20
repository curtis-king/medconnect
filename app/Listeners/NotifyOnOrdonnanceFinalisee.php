<?php

namespace App\Listeners;

use App\Events\OrdonnanceFinalisee;
use App\Listeners\Concerns\ResolvesAdministrativeRecipients;
use App\Notifications\OrdonnanceFinaliseeNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyOnOrdonnanceFinalisee implements ShouldQueue
{
    use ResolvesAdministrativeRecipients;

    public function handle(OrdonnanceFinalisee $event): void
    {
        $ordonnance = $event->ordonnance->loadMissing('consultation.patient');

        if ($ordonnance->consultation?->patient) {
            $ordonnance->consultation->patient->notify(new OrdonnanceFinaliseeNotification($ordonnance));
        }

        $this->administrativeRecipients()->each(
            fn ($admin) => $admin->notify(new OrdonnanceFinaliseeNotification($ordonnance))
        );
    }
}
