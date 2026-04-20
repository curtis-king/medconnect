<?php

namespace App\Events;

use App\Models\RendezVousProfessionnel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RendezVousSoumis implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public RendezVousProfessionnel $rendezVous) {}

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('professionnel.'.$this->rendezVous->dossier_professionnel_id),
            new Channel('admin'),
        ];

        if ($this->rendezVous->patient_user_id) {
            $channels[] = new PrivateChannel('patient.'.$this->rendezVous->patient_user_id);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'rendez-vous.soumis';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->rendezVous->id,
            'reference' => $this->rendezVous->reference,
            'type_demande' => $this->rendezVous->type_demande,
            'patient' => $this->rendezVous->patient?->name,
            'date_proposee' => $this->rendezVous->date_proposee?->toDateTimeString(),
        ];
    }
}
