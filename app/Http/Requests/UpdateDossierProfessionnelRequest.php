<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDossierProfessionnelRequest extends FormRequest
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
            'image_identite' => [
                Rule::requiredIf(fn () => empty($this->route('dossierProfessionnel')?->image_identite_path)),
                'nullable',
                'image',
                'max:4096',
            ],
            'NIU' => ['nullable', 'string', 'max:50'],
            'forme_juridique' => ['nullable', 'string', 'max:100'],
            'attestation_professionnelle' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'document_prise_de_fonction' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'image_identite.required' => 'La photo de profil ou le logo est obligatoire.',
            'image_identite.image' => 'Le fichier visuel doit être une image valide.',
            'image_identite.max' => 'Le fichier visuel ne doit pas dépasser 4 Mo.',
        ];
    }
}
