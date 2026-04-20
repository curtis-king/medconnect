<?php

namespace App\Notifications;

use App\Models\RetraitProfessionnel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class RetraitProfessionnelTraiteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public RetraitProfessionnel $retrait) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'retrait_professionnel_traite',
            'retrait_id' => $this->retrait->id,
            'reference' => $this->retrait->reference,
            'statut' => $this->retrait->statut,
            'montant_demande' => $this->retrait->montant_demande,
            'montant_approuve' => $this->retrait->montant_approuve,
            'message' => 'Votre demande de retrait a ete traitee.',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
