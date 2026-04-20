<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDossierProfessionnelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'raison_sociale' => ['nullable', 'string', 'max:255'],
            'type_structure' => ['required', 'in:individuel,clinique,hopital,dispensaire,autre'],
            'specialite' => [
                'required',
                'string',
                Rule::in([
                    'Médecine générale',
                    'Cardiologie',
                    'Pédiatrie',
                    'Gynécologie',
                    'Dermatologie',
                    'Dentisterie',
                    'Laboratoire',
                    'Radiologie',
                    'Kinésithérapie',
                    'Pharmacie',
                    'Ophtalmologie',
                    'ORL',
                    'Psychologie',
                    'Autre',
                ]),
            ],
            'image_identite' => ['required', 'image', 'max:4096'],
            'NIU' => ['nullable', 'string', 'max:50'],
            'forme_juridique' => ['nullable', 'string', 'max:100'],
            'attestation_professionnelle' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'document_prise_de_fonction' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'frais_id' => ['required', 'exists:frais,id'],
            'mode_paiement_inscription' => ['nullable', 'in:cash,mobile_money,virement,carte'],
            'reference_paiement_inscription' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'type_structure.required' => 'Le type de structure est obligatoire.',
            'type_structure.in' => 'Le type de structure n\'est pas valide.',
            'specialite.required' => 'La spécialité est obligatoire.',
            'specialite.in' => 'La spécialité sélectionnée n\'est pas valide.',
            'image_identite.required' => 'La photo de profil ou le logo est obligatoire.',
            'image_identite.image' => 'Le fichier visuel doit être une image valide.',
            'image_identite.max' => 'Le fichier visuel ne doit pas dépasser 4 Mo.',
            'frais_id.required' => 'Veuillez sélectionner un tarif d\'inscription.',
            'frais_id.exists' => 'Le tarif sélectionné n\'existe pas.',
        ];
    }
}
