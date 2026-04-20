<?php

namespace App\Notifications;

use App\Models\ExamenProfessionnel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ExamenDemandeNotification extends Notification implements ShouldQueue
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
            'type' => 'examen_demande',
            'examen_id' => $this->examen->id,
            'libelle' => $this->examen->libelle,
            'message' => 'Un examen a ete demande.',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
