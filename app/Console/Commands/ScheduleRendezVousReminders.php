<?php

namespace App\Console\Commands;

use App\Models\RendezVousProfessionnel;
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ScheduleRendezVousReminders extends Command
{
    protected $signature = 'notifications:rendezvous-reminder';

    protected $description = 'Envoie les rappels de rendez-vous la veille';

    public function handle(PushNotificationService $pushService): int
    {
        $tomorrow = Carbon::tomorrow();
        $tomorrowStart = $tomorrow->copy()->startOfDay();
        $tomorrowEnd = $tomorrow->copy()->endOfDay();

        $rendezVous = RendezVousProfessionnel::query()
            ->whereIn('statut', ['accepte', 'confirme'])
            ->whereBetween('date_proposee', [$tomorrowStart, $tomorrowEnd])
            ->get();

        $this->info("Trouvés {$rendezVous->count()} rendez-vous pour demain.");

        foreach ($rendezVous as $rdv) {
            $service = $rdv->serviceProfessionnel?->nom ?? 'Consultation';
            $dateFormatee = $rdv->date_proposee->format('d/m/Y à H:i');

            $pushService->notifyRappelRendezVous(
                $rdv->patient_user_id,
                $rdv->id,
                $service,
                $dateFormatee
            );

            $this->info("Rappel envoyé pour RDV #{$rdv->id}");
        }

        return Command::SUCCESS;
    }
}
