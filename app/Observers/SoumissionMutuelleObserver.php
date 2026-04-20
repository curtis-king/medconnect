<?php

namespace App\Observers;

use App\Events\SoumissionMutuelleTraitee;
use App\Models\SoumissionMutuelle;

class SoumissionMutuelleObserver
{
    public function updated(SoumissionMutuelle $soumission): void
    {
        if (! $soumission->wasChanged('statut')) {
            return;
        }

        if (in_array($soumission->statut, ['approuve', 'rejete', 'partiel'], true)) {
            event(new SoumissionMutuelleTraitee($soumission));
        }
    }
}
