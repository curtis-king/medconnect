<?php

namespace App\Providers;

use App\Models\ConsultationProfessionnelle;
use App\Models\ExamenProfessionnel;
use App\Models\FactureProfessionnelle;
use App\Models\OrdonnanceProfessionnelle;
use App\Models\RendezVousProfessionnel;
use App\Models\RetraitProfessionnel;
use App\Models\SoumissionMutuelle;
use App\Observers\ConsultationProfessionnelleObserver;
use App\Observers\ExamenProfessionnelObserver;
use App\Observers\FactureProfessionnelleObserver;
use App\Observers\OrdonnanceProfessionnelleObserver;
use App\Observers\RendezVousProfessionnelObserver;
use App\Observers\RetraitProfessionnelObserver;
use App\Observers\SoumissionMutuelleObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RendezVousProfessionnel::observe(RendezVousProfessionnelObserver::class);
        ConsultationProfessionnelle::observe(ConsultationProfessionnelleObserver::class);
        FactureProfessionnelle::observe(FactureProfessionnelleObserver::class);
        OrdonnanceProfessionnelle::observe(OrdonnanceProfessionnelleObserver::class);
        ExamenProfessionnel::observe(ExamenProfessionnelObserver::class);
        SoumissionMutuelle::observe(SoumissionMutuelleObserver::class);
        RetraitProfessionnel::observe(RetraitProfessionnelObserver::class);
    }
}
