<?php

namespace App\Notifications;

use App\Models\RendezVousProfessionnel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class RendezVousDeclineNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public RendezVousProfessionnel $rendezVous) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'rendez_vous_decline',
            'rendez_vous_id' => $this->rendezVous->id,
            'reference' => $this->rendezVous->reference,
            'message' => 'Votre demande de rendez-vous a ete refusee.',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
