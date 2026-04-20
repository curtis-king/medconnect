<?php

namespace App\Listeners;

use App\Events\RendezVousDecline;
use App\Notifications\RendezVousDeclineNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyOnRendezVousDecline implements ShouldQueue
{
    public function handle(RendezVousDecline $event): void
    {
        $rendezVous = $event->rendezVous->loadMissing('patient');

        if ($rendezVous->patient) {
            $rendezVous->patient->notify(new RendezVousDeclineNotification($rendezVous));
        }
    }
}
