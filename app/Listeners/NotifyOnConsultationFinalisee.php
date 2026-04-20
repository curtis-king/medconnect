<?php

namespace App\Listeners;

use App\Events\ConsultationFinalisee;
use App\Listeners\Concerns\ResolvesAdministrativeRecipients;
use App\Notifications\ConsultationFinaliseeNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyOnConsultationFinalisee implements ShouldQueue
{
    use ResolvesAdministrativeRecipients;

    public function handle(ConsultationFinalisee $event): void
    {
        $consultation = $event->consultation->loadMissing('patient');

        if ($consultation->patient) {
            $consultation->patient->notify(new ConsultationFinaliseeNotification($consultation));
        }

        $this->administrativeRecipients()->each(
            fn ($admin) => $admin->notify(new ConsultationFinaliseeNotification($consultation))
        );
    }
}
