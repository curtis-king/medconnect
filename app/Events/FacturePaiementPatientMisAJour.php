<?php

namespace App\Events;

use App\Models\FactureProfessionnelle;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FacturePaiementPatientMisAJour implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public FactureProfessionnelle $facture) {}

    public function broadcastOn(): array
    {
        $channels = [new Channel('admin')];

        if ($this->facture->patient_user_id) {
            $channels[] = new PrivateChannel('patient.'.$this->facture->patient_user_id);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'facture.paiement-patient.mis-a-jour';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->facture->id,
            'reference' => $this->facture->reference,
            'statut_paiement_patient' => $this->facture->statut_paiement_patient,
            'mode_paiement' => $this->facture->mode_paiement,
        ];
    }
}
