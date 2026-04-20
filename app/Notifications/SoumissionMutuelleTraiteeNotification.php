<?php

namespace App\Notifications;

use App\Models\SoumissionMutuelle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class SoumissionMutuelleTraiteeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public SoumissionMutuelle $soumission) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'soumission_mutuelle_traitee',
            'soumission_id' => $this->soumission->id,
            'reference' => $this->soumission->reference,
            'statut' => $this->soumission->statut,
            'montant_pris_en_charge' => $this->soumission->montant_pris_en_charge,
            'montant_rejete' => $this->soumission->montant_rejete,
            'message' => 'Votre soumission mutuelle a ete traitee.',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
