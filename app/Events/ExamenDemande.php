<?php

namespace App\Events;

use App\Models\ExamenProfessionnel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExamenDemande implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ExamenProfessionnel $examen) {}

    public function broadcastOn(): array
    {
        $channels = [new Channel('admin')];

        if ($this->examen->patient_user_id) {
            $channels[] = new PrivateChannel('patient.'.$this->examen->patient_user_id);
        }

        if ($this->examen->dossier_professionnel_id) {
            $channels[] = new PrivateChannel('professionnel.'.$this->examen->dossier_professionnel_id);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'examen.demande';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->examen->id,
            'libelle' => $this->examen->libelle,
            'statut' => $this->examen->statut,
        ];
    }
}
