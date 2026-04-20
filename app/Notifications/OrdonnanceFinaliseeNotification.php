<?php

namespace App\Notifications;

use App\Models\OrdonnanceProfessionnelle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class OrdonnanceFinaliseeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public OrdonnanceProfessionnelle $ordonnance) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'ordonnance_finalisee',
            'ordonnance_id' => $this->ordonnance->id,
            'consultation_id' => $this->ordonnance->consultation_professionnelle_id,
            'message' => 'Une ordonnance a ete finalisee.',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
