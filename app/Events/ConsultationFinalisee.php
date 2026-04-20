<?php

namespace App\Events;

use App\Models\ConsultationProfessionnelle;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConsultationFinalisee implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ConsultationProfessionnelle $consultation) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('patient.'.$this->consultation->patient_user_id),
            new Channel('admin'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'consultation.finalisee';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->consultation->id,
            'type_service' => $this->consultation->type_service,
            'rendez_vous_id' => $this->consultation->rendez_vous_professionnel_id,
        ];
    }
}
