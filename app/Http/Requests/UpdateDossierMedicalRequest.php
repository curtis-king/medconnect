<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDossierMedicalRequest extends FormRequest
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
        return [
            'nom' => ['sometimes', 'required', 'string', 'max:255'],
            'prenom' => ['sometimes', 'required', 'string', 'max:255'],
            'date_naissance' => ['sometimes', 'nullable', 'date'],
            'sexe' => ['sometimes', 'nullable', 'in:M,F,Autre'],
            'telephone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'adresse' => ['sometimes', 'nullable', 'string', 'max:255'],

            'groupe_sanguin' => ['sometimes', 'nullable', 'string', 'max:10'],
            'allergies' => ['sometimes', 'nullable', 'string'],
            'maladies_chroniques' => ['sometimes', 'nullable', 'string'],
            'traitements_en_cours' => ['sometimes', 'nullable', 'string'],
            'antecedents_familiaux' => ['sometimes', 'nullable', 'string'],
            'antecedents_personnels' => ['sometimes', 'nullable', 'string'],

            'contact_urgence_nom' => ['sometimes', 'nullable', 'string', 'max:255'],
            'contact_urgence_telephone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'contact_urgence_relation' => ['sometimes', 'nullable', 'string', 'max:100'],

            'type_piece_identite' => ['sometimes', 'nullable', 'in:cni,passeport,permis,autre'],
            'numero_piece_identite' => ['sometimes', 'nullable', 'string', 'max:100'],
            'date_expiration_piece_identite' => ['sometimes', 'nullable', 'date'],

            'photo_profil' => ['sometimes', 'nullable', 'image', 'max:2048'],
            'piece_identite_recto' => ['sometimes', 'nullable', 'image', 'max:4096'],
            'piece_identite_verso' => ['sometimes', 'nullable', 'image', 'max:4096'],

            'actif' => ['sometimes', 'boolean'],
            'partage_actif' => ['sometimes', 'boolean'],
        ];
    }
}
