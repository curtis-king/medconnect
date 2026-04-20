<?php

namespace App\Events;

use App\Models\SoumissionMutuelle;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SoumissionMutuelleTraitee implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public SoumissionMutuelle $soumission) {}

    public function broadcastOn(): array
    {
        $channels = [new Channel('admin')];

        $patientUserId = $this->soumission->dossierMedical?->user_id;

        if ($patientUserId) {
            $channels[] = new PrivateChannel('patient.'.$patientUserId);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'soumission-mutuelle.traitee';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->soumission->id,
            'reference' => $this->soumission->reference,
            'statut' => $this->soumission->statut,
            'montant_pris_en_charge' => $this->soumission->montant_pris_en_charge,
            'montant_rejete' => $this->soumission->montant_rejete,
        ];
    }
}
