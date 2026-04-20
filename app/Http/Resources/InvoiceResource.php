<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'appointment_id' => $this->rendez_vous_professionnel_id,
            'professional' => new UserResource($this->whenLoaded('professionnel')),
            'type_service' => $this->type_service,
            'type_facture' => $this->type_facture,
            'montant_total' => (float) $this->montant_total,
            'montant_couvert_mutuelle' => (float) $this->montant_couvert_mutuelle,
            'montant_a_charge_patient' => (float) $this->montant_a_charge_patient,
            'statut' => $this->statut,
            'statut_paiement_patient' => $this->statut_paiement_patient,
            'statut_mutuelle' => $this->statut_mutuelle,
            'statut_backoffice' => $this->statut_backoffice,
            'mode_paiement' => $this->mode_paiement,
            'reference_paiement' => $this->reference_paiement,
            'envoyee_backoffice' => $this->envoyee_backoffice,
            'soumise_backoffice_le' => $this->soumise_backoffice_le,
            'prise_en_charge_confirmee_le' => $this->prise_en_charge_confirmee_le,
            'payee_le' => $this->payee_le,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
