<?php

namespace App\Notifications;

use App\Models\FactureProfessionnelle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class FactureBackofficeMiseAJourNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public FactureProfessionnelle $facture) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        $backofficeMessage = match ((string) $this->facture->statut_backoffice) {
            'valide' => 'Validation backoffice effectuee pour la prise en charge du patient.',
            'paye' => 'Paiement backoffice effectue pour cette facture.',
            'rejete' => 'Demande backoffice rejetee pour cette facture.',
            default => 'Le statut backoffice de la facture a ete mis a jour.',
        };

        return [
            'type' => 'facture_backoffice_mise_a_jour',
            'facture_id' => $this->facture->id,
            'reference' => $this->facture->reference,
            'statut_mutuelle' => $this->facture->statut_mutuelle,
            'statut_backoffice' => $this->facture->statut_backoffice,
            'montant_total' => (float) $this->facture->montant_total,
            'montant_couvert_mutuelle' => (float) $this->facture->montant_couvert_mutuelle,
            'message' => $backofficeMessage,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
