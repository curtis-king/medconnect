<?php

namespace App\Listeners;

use App\Events\SoumissionMutuelleTraitee;
use App\Listeners\Concerns\ResolvesAdministrativeRecipients;
use App\Notifications\SoumissionMutuelleTraiteeNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyOnSoumissionMutuelleTraitee implements ShouldQueue
{
    use ResolvesAdministrativeRecipients;

    public function handle(SoumissionMutuelleTraitee $event): void
    {
        $soumission = $event->soumission->loadMissing('dossierMedical.user');

        if ($soumission->dossierMedical?->user) {
            $soumission->dossierMedical->user->notify(new SoumissionMutuelleTraiteeNotification($soumission));
        }

        $this->administrativeRecipients()->each(
            fn ($admin) => $admin->notify(new SoumissionMutuelleTraiteeNotification($soumission))
        );
    }
}
