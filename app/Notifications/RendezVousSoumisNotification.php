<?php

namespace App\Notifications;

use App\Models\RendezVousProfessionnel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class RendezVousSoumisNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public RendezVousProfessionnel $rendezVous) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        $message = ((int) $notifiable->id === (int) $this->rendezVous->patient_user_id)
            ? 'Votre demande de rendez-vous a ete soumise avec succes.'
            : 'Nouvelle demande de rendez-vous soumise.';

        return [
            'type' => 'rendez_vous_soumis',
            'rendez_vous_id' => $this->rendezVous->id,
            'reference' => $this->rendezVous->reference,
            'type_demande' => $this->rendezVous->type_demande,
            'patient_user_id' => $this->rendezVous->patient_user_id,
            'date_proposee' => $this->rendezVous->date_proposee?->toISOString(),
            'message' => $message,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
