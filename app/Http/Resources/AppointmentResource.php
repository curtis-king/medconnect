<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date_consultation,
            'heure' => $this->heure_consultation,
            'professional' => new UserResource($this->whenLoaded('professionnel')),
            'status' => $this->statut,
            'type' => $this->type_consultation,
            'created_at' => $this->created_at,
        ];
    }
}
