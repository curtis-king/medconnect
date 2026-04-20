<?php

namespace App\Notifications;

use App\Models\ExamenProfessionnel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ExamenAccepteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ExamenProfessionnel $examen) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'examen_accepte',
            'examen_id' => $this->examen->id,
            'libelle' => $this->examen->libelle,
            'message' => 'Votre demande d\'examen a ete acceptee. La facture est disponible pour paiement.',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
