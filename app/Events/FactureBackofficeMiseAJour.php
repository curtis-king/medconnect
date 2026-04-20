<?php

namespace App\Events;

use App\Models\FactureProfessionnelle;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FactureBackofficeMiseAJour implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public FactureProfessionnelle $facture) {}

    public function broadcastOn(): array
    {
        $channels = [new Channel('admin')];

        if ($this->facture->patient_user_id) {
            $channels[] = new PrivateChannel('patient.'.$this->facture->patient_user_id);
        }

        if ($this->facture->dossier_professionnel_id) {
            $channels[] = new PrivateChannel('professionnel.'.$this->facture->dossier_professionnel_id);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'facture.backoffice.mise-a-jour';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->facture->id,
            'reference' => $this->facture->reference,
            'statut_backoffice' => $this->facture->statut_backoffice,
            'statut_mutuelle' => $this->facture->statut_mutuelle,
        ];
    }
}
