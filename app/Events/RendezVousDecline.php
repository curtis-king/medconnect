<?php

namespace App\Events;

use App\Models\RendezVousProfessionnel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RendezVousDecline implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public RendezVousProfessionnel $rendezVous) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('patient.'.$this->rendezVous->patient_user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'rendez-vous.decline';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->rendezVous->id,
            'reference' => $this->rendezVous->reference,
        ];
    }
}
