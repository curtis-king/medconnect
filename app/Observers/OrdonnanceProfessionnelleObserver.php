<?php

namespace App\Observers;

use App\Events\OrdonnanceFinalisee;
use App\Models\OrdonnanceProfessionnelle;

class OrdonnanceProfessionnelleObserver
{
    public function created(OrdonnanceProfessionnelle $ordonnance): void
    {
        if ($ordonnance->statut === 'finalisee') {
            event(new OrdonnanceFinalisee($ordonnance));
        }
    }

    public function updated(OrdonnanceProfessionnelle $ordonnance): void
    {
        if (! $ordonnance->wasChanged('statut')) {
            return;
        }

        if ($ordonnance->statut === 'finalisee') {
            event(new OrdonnanceFinalisee($ordonnance));
        }
    }
}
