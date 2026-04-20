<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsultationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'professional' => new UserResource($this->whenLoaded('professionnel')),
            'type_consultation' => $this->type_consultation,
            'mode_deroulement' => $this->mode_deroulement,
            'lien_teleconsultation' => $this->lien_teleconsultation_patient,
            'date_consultation' => $this->date_consultation,
            'heure_consultation' => $this->heure_consultation,
            'raison' => $this->raison,
            'resume' => $this->resume_consultation,
            'diagnostic_preomaire' => $this->diagnostic_preliminaire,
            'ordonnances' => OrdonnanceResource::collection($this->whenLoaded('ordonnances')),
            'examens' => ExamenResource::collection($this->whenLoaded('examens')),
            'statut' => $this->statut,
            'notes' => $this->notes_professionnel,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
