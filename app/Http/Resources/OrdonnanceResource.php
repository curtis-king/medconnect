<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdonnanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'consultation_id' => $this->consultation_professionnelle_id,
            'professional' => new UserResource($this->whenLoaded('professionnel')),
            'produits' => $this->produits,
            'prescription' => $this->prescription,
            'recommandations' => $this->recommandations,
            'instructions_complementaires' => $this->instructions_complementaires,
            'fichier_joint_url' => $this->fichier_joint_path ? asset('storage/'.ltrim($this->fichier_joint_path, '/')) : null,
            'statut' => $this->statut,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
