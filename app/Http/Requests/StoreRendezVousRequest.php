<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRendezVousRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'dossier_professionnel_id' => ['required', 'integer', 'exists:dossiers_professionnels,id'],
            'service_professionnel_id' => ['nullable', 'integer', 'exists:services_professionnels,id'],
            'dossier_medical_id' => ['nullable', 'integer', 'exists:dossiers_medicaux,id'],
            'patient_dossier_reference' => ['nullable', 'string', 'max:120'],
            'mode_deroulement' => ['required', 'in:presentiel,teleconsultation'],
            'date_proposee_jour' => ['required', 'date', 'after_or_equal:today'],
            'heure_proposee' => ['required', 'date_format:H:i'],
            'motif' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'dossier_professionnel_id.required' => 'Veuillez sélectionner un professionnel.',
            'dossier_professionnel_id.exists' => 'Le professionnel sélectionné est invalide.',
            'dossier_medical_id.exists' => 'Le dossier medical selectionne est invalide.',
            'mode_deroulement.required' => 'Veuillez préciser le mode de déroulement.',
            'mode_deroulement.in' => 'Mode invalide (présentiel ou téléconsultation).',
            'date_proposee_jour.required' => 'Veuillez choisir une date.',
            'date_proposee_jour.after_or_equal' => "La date doit être aujourd'hui ou dans le futur.",
            'heure_proposee.required' => 'Veuillez choisir une heure.',
            'heure_proposee.date_format' => 'Format d\'heure invalide (HH:MM attendu).',
            'motif.required' => 'Veuillez renseigner le motif de votre visite.',
            'motif.min' => 'Le motif doit contenir au moins 10 caractères.',
        ];
    }
}
