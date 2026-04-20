<?php

namespace App\Notifications;

use App\Models\ExamenProfessionnel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ExamenResultatDisponibleNotification extends Notification implements ShouldQueue
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
            'type' => 'examen_resultat_disponible',
            'examen_id' => $this->examen->id,
            'libelle' => $this->examen->libelle,
            'has_resultat_fichier' => ! empty($this->examen->resultat_fichier_path),
            'message' => 'Le resultat de votre examen est disponible.',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
