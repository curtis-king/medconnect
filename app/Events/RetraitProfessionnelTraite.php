<?php

namespace App\Events;

use App\Models\RetraitProfessionnel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RetraitProfessionnelTraite implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public RetraitProfessionnel $retrait) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('professionnel.'.$this->retrait->dossier_professionnel_id),
            new Channel('admin'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'retrait-professionnel.traite';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->retrait->id,
            'reference' => $this->retrait->reference,
            'statut' => $this->retrait->statut,
            'montant_approuve' => $this->retrait->montant_approuve,
        ];
    }
}
