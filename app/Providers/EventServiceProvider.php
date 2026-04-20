<?php

namespace App\Providers;

use App\Events\ConsultationFinalisee;
use App\Events\ExamenDemande;
use App\Events\ExamenResultatDisponible;
use App\Events\FactureBackofficeMiseAJour;
use App\Events\FactureEmise;
use App\Events\FacturePaiementPatientMisAJour;
use App\Events\OrdonnanceFinalisee;
use App\Events\RendezVousAccepte;
use App\Events\RendezVousDecline;
use App\Events\RendezVousSoumis;
use App\Events\RetraitProfessionnelTraite;
use App\Events\SoumissionMutuelleTraitee;
use App\Listeners\NotifyOnConsultationFinalisee;
use App\Listeners\NotifyOnExamenDemande;
use App\Listeners\NotifyOnExamenResultatDisponible;
use App\Listeners\NotifyOnFactureBackofficeMiseAJour;
use App\Listeners\NotifyOnFactureEmise;
use App\Listeners\NotifyOnFacturePaiementPatientMisAJour;
use App\Listeners\NotifyOnOrdonnanceFinalisee;
use App\Listeners\NotifyOnRendezVousAccepte;
use App\Listeners\NotifyOnRendezVousDecline;
use App\Listeners\NotifyOnRendezVousSoumis;
use App\Listeners\NotifyOnRetraitProfessionnelTraite;
use App\Listeners\NotifyOnSoumissionMutuelleTraitee;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        RendezVousSoumis::class => [
            NotifyOnRendezVousSoumis::class,
        ],
        RendezVousAccepte::class => [
            NotifyOnRendezVousAccepte::class,
        ],
        RendezVousDecline::class => [
            NotifyOnRendezVousDecline::class,
        ],
        ConsultationFinalisee::class => [
            NotifyOnConsultationFinalisee::class,
        ],
        FactureEmise::class => [
            NotifyOnFactureEmise::class,
        ],
        FactureBackofficeMiseAJour::class => [
            NotifyOnFactureBackofficeMiseAJour::class,
        ],
        FacturePaiementPatientMisAJour::class => [
            NotifyOnFacturePaiementPatientMisAJour::class,
        ],
        OrdonnanceFinalisee::class => [
            NotifyOnOrdonnanceFinalisee::class,
        ],
        ExamenDemande::class => [
            NotifyOnExamenDemande::class,
        ],
        ExamenResultatDisponible::class => [
            NotifyOnExamenResultatDisponible::class,
        ],
        SoumissionMutuelleTraitee::class => [
            NotifyOnSoumissionMutuelleTraitee::class,
        ],
        RetraitProfessionnelTraite::class => [
            NotifyOnRetraitProfessionnelTraite::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
