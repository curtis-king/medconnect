<?php

namespace App\Observers;

use App\Events\ExamenDemande;
use App\Events\ExamenResultatDisponible;
use App\Models\ExamenProfessionnel;

class ExamenProfessionnelObserver
{
    public function created(ExamenProfessionnel $examen): void
    {
        event(new ExamenDemande($examen));

        if ($examen->statut === 'termine') {
            event(new ExamenResultatDisponible($examen));
        }
    }

    public function updated(ExamenProfessionnel $examen): void
    {
        if ($examen->wasChanged('statut') && $examen->statut === 'termine') {
            event(new ExamenResultatDisponible($examen));

            return;
        }

        if ($examen->statut !== 'termine') {
            return;
        }

        if ($examen->wasChanged('resultat_text') || $examen->wasChanged('resultat_fichier_path')) {
            event(new ExamenResultatDisponible($examen));
        }
    }
}
