<?php

namespace App\Notifications;

use App\Models\ConsultationProfessionnelle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ConsultationFinaliseeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ConsultationProfessionnelle $consultation) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'consultation_finalisee',
            'consultation_id' => $this->consultation->id,
            'rendez_vous_id' => $this->consultation->rendez_vous_professionnel_id,
            'type_service' => $this->consultation->type_service,
            'message' => 'Votre consultation/examen a ete finalise(e).',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
