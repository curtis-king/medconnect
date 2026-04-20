<?php

namespace App\Observers;

use App\Events\RetraitProfessionnelTraite;
use App\Models\RetraitProfessionnel;

class RetraitProfessionnelObserver
{
    public function updated(RetraitProfessionnel $retrait): void
    {
        if (! $retrait->wasChanged('statut')) {
            return;
        }

        if (in_array($retrait->statut, ['approuve', 'rejete', 'paye'], true)) {
            event(new RetraitProfessionnelTraite($retrait));
        }
    }
}
