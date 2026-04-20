<?php

namespace App\Events;

use App\Models\FactureProfessionnelle;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FactureEmise implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public FactureProfessionnelle $facture) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('patient.'.$this->facture->patient_user_id),
            new Channel('admin'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'facture.emise';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->facture->id,
            'reference' => $this->facture->reference,
            'montant_total' => $this->facture->montant_total,
            'montant_a_charge_patient' => $this->facture->montant_a_charge_patient,
        ];
    }
}
