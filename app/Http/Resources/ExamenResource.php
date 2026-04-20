<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type_examen' => $this->type_examen ?? 'examination',
            'professional' => new UserResource($this->whenLoaded('professionnel')),
            'description' => $this->description,
            'resultats' => $this->resultats,
            'notes' => $this->notes,
            'fichier_resultat_url' => $this->fichier_resultat_path ? asset('storage/'.ltrim($this->fichier_resultat_path, '/')) : null,
            'fichier_joint_url' => $this->fichier_joint_path ? asset('storage/'.ltrim($this->fichier_joint_path, '/')) : null,
            'statut' => $this->statut,
            'date_examen' => $this->date_examen,
            'created_at' => $this->created_at,
        ];
    }
}
