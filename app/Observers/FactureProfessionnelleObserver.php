<?php

namespace App\Observers;

use App\Events\FactureBackofficeMiseAJour;
use App\Events\FactureEmise;
use App\Events\FacturePaiementPatientMisAJour;
use App\Models\FactureProfessionnelle;

class FactureProfessionnelleObserver
{
    public function created(FactureProfessionnelle $facture): void
    {
        event(new FactureEmise($facture));
    }

    public function updated(FactureProfessionnelle $facture): void
    {
        if ($facture->wasChanged('statut') && $facture->statut === 'emise') {
            event(new FactureEmise($facture));
        }

        if ($facture->wasChanged('statut_backoffice') || $facture->wasChanged('statut_mutuelle')) {
            event(new FactureBackofficeMiseAJour($facture));
        }

        if ($facture->wasChanged('statut_paiement_patient') || $facture->wasChanged('mode_paiement')) {
            event(new FacturePaiementPatientMisAJour($facture));
        }
    }
}
