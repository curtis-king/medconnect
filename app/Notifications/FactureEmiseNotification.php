<?php

namespace App\Notifications;

use App\Models\FactureProfessionnelle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class FactureEmiseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public FactureProfessionnelle $facture) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'facture_emise',
            'facture_id' => $this->facture->id,
            'reference' => $this->facture->reference,
            'montant_total' => $this->facture->montant_total,
            'montant_couvert_mutuelle' => $this->facture->montant_couvert_mutuelle,
            'montant_a_charge_patient' => $this->facture->montant_a_charge_patient,
            'message' => 'Une nouvelle facture a ete emise.',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
