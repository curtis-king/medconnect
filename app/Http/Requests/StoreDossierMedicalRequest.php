<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDossierMedicalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isDependant = (string) $this->input('declaration_mode', 'personnel') === 'dependant';
        $isAdult = $this->isAdult((string) $this->input('date_naissance', ''));
        $isOnlineCreation = (string) $this->input('source_creation') === 'en_ligne';
        $requiresIdentity = $isOnlineCreation && ((! $isDependant) || $isAdult);

        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'date_naissance' => ['nullable', 'date'],
            'sexe' => ['nullable', 'in:M,F,Autre'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'adresse' => ['nullable', 'string', 'max:255'],

            'groupe_sanguin' => ['nullable', 'string', 'max:10'],
            'allergies' => ['nullable', 'string'],
            'maladies_chroniques' => ['nullable', 'string'],
            'traitements_en_cours' => ['nullable', 'string'],
            'antecedents_familiaux' => ['nullable', 'string'],
            'antecedents_personnels' => ['nullable', 'string'],

            'contact_urgence_nom' => ['nullable', 'string', 'max:255'],
            'contact_urgence_telephone' => ['nullable', 'string', 'max:50'],
            'contact_urgence_relation' => ['nullable', 'string', 'max:100'],

            'type_piece_identite' => [
                Rule::requiredIf($requiresIdentity),
                'nullable',
                'in:cni,passeport,permis,autre',
            ],
            'numero_piece_identite' => [
                Rule::requiredIf($requiresIdentity),
                'nullable',
                'string',
                'max:100',
                Rule::unique('dossiers_medicaux', 'numero_piece_identite')->where(fn ($query) => $query->whereNotNull('numero_piece_identite')),
            ],
            'date_expiration_piece_identite' => ['nullable', 'date'],

            'photo_profil' => [
                Rule::requiredIf(fn () => $this->input('source_creation') === 'en_ligne'),
                'nullable',
                'image',
                'max:2048',
            ],
            'piece_identite_recto' => [Rule::requiredIf($requiresIdentity), 'nullable', 'image', 'max:4096'],
            'piece_identite_verso' => [Rule::requiredIf($requiresIdentity), 'nullable', 'image', 'max:4096'],

            'source_creation' => ['required', 'in:guichet,en_ligne'],
            'declaration_mode' => ['nullable', 'in:personnel,dependant'],
            'lien_avec_responsable' => [
                Rule::requiredIf(fn () => $this->input('declaration_mode') === 'dependant'),
                'nullable',
                'in:enfant,conjoint,parent,frere_soeur,autre',
            ],
            'frais_id' => [
                'nullable',
                Rule::exists('frais', 'id')->where(fn ($query) => $query->where('type', 'inscription')),
            ],
            'statut_paiement_inscription' => ['nullable', 'in:en_attente,paye,exonere'],
            'mode_paiement_inscription' => ['nullable', 'in:cash,en_ligne,mobile_money,carte,virement'],
            'reference_paiement_inscription' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function isAdult(string $dateNaissance): bool
    {
        if ($dateNaissance === '') {
            return true;
        }

        try {
            return Carbon::parse($dateNaissance)->age >= 18;
        } catch (\Throwable) {
            return true;
        }
    }
}
