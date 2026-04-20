<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DossierMedicalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'numero_unique' => $this->numero_unique,
            'nom_complet' => "{$this->prenom} {$this->nom}",
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'date_naissance' => $this->date_naissance,
            'sexe' => $this->sexe,
            'telephone' => $this->telephone,
            'adresse' => $this->adresse,
            'groupe_sanguin' => $this->groupe_sanguin,
            'allergies' => $this->allergies,
            'maladies_chroniques' => $this->maladies_chroniques,
            'traitements_en_cours' => $this->traitements_en_cours,
            'antecedents_familiaux' => $this->antecedents_familiaux,
            'antecedents_personnels' => $this->antecedents_personnels,
            'contact_urgence' => [
                'nom' => $this->contact_urgence_nom,
                'telephone' => $this->contact_urgence_telephone,
                'relation' => $this->contact_urgence_relation,
            ],
            'est_personne_a_charge' => $this->est_personne_a_charge,
            'lien_avec_responsable' => $this->lien_avec_responsable,
            'actif' => $this->actif,
            'statut_paiement_inscription' => $this->statut_paiement_inscription,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
