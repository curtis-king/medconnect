<?php

namespace App\Observers;

use App\Events\RendezVousAccepte;
use App\Events\RendezVousDecline;
use App\Events\RendezVousSoumis;
use App\Models\RendezVousProfessionnel;

class RendezVousProfessionnelObserver
{
    public function created(RendezVousProfessionnel $rendezVous): void
    {
        event(new RendezVousSoumis($rendezVous));
    }

    public function updated(RendezVousProfessionnel $rendezVous): void
    {
        if (! $rendezVous->wasChanged('statut')) {
            return;
        }

        if ($rendezVous->statut === 'accepte') {
            event(new RendezVousAccepte($rendezVous));
        }

        if ($rendezVous->statut === 'decline') {
            event(new RendezVousDecline($rendezVous));
        }
    }
}
