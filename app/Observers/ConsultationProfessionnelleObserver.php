<?php

namespace App\Observers;

use App\Events\ConsultationFinalisee;
use App\Models\ConsultationProfessionnelle;

class ConsultationProfessionnelleObserver
{
    public function updated(ConsultationProfessionnelle $consultation): void
    {
        if (! $consultation->wasChanged('statut')) {
            return;
        }

        if ($consultation->statut === 'finalise') {
            event(new ConsultationFinalisee($consultation));
        }
    }
}
