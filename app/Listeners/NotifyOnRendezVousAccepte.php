<?php

namespace App\Listeners;

use App\Events\RendezVousAccepte;
use App\Notifications\RendezVousAccepteNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyOnRendezVousAccepte implements ShouldQueue
{
    public function handle(RendezVousAccepte $event): void
    {
        $rendezVous = $event->rendezVous->loadMissing('patient');

        if ($rendezVous->patient) {
            $rendezVous->patient->notify(new RendezVousAccepteNotification($rendezVous));
        }
    }
}
