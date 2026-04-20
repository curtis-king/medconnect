<?php

namespace App\Notifications;

use App\Models\FactureProfessionnelle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class FacturePaiementPatientMisAJourNotification extends Notification implements ShouldQueue
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
            'type' => 'facture_paiement_patient_mis_a_jour',
            'facture_id' => $this->facture->id,
            'reference' => $this->facture->reference,
            'statut_paiement_patient' => $this->facture->statut_paiement_patient,
            'mode_paiement' => $this->facture->mode_paiement,
            'message' => 'Le statut de paiement patient de la facture a ete mis a jour.',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
