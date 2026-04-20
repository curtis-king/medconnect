<?php

namespace App\Events;

use App\Models\OrdonnanceProfessionnelle;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrdonnanceFinalisee implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public OrdonnanceProfessionnelle $ordonnance) {}

    public function broadcastOn(): array
    {
        $channels = [new Channel('admin')];

        $patientUserId = $this->ordonnance->consultation?->patient_user_id;

        if ($patientUserId) {
            $channels[] = new PrivateChannel('patient.'.$patientUserId);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'ordonnance.finalisee';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->ordonnance->id,
            'consultation_id' => $this->ordonnance->consultation_professionnelle_id,
            'statut' => $this->ordonnance->statut,
        ];
    }
}
