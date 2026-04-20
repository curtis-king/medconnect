<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => class_basename($this->subscribable_type),
            'date_debut' => $this->date_debut,
            'date_fin' => $this->date_fin,
            'jours_restants' => max(0, $this->date_fin->diffInDays(now())),
            'montant' => (float) $this->montant,
            'statut_paiement' => $this->statut_paiement,
            'actif' => $this->actif,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
