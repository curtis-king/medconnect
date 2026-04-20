<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConsultationProfessionnelleRequest extends FormRequest
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
            'diagnostic' => ['nullable', 'string'],
            'diagnostic_medecin' => ['nullable', 'string'],
            'conclusion' => ['nullable', 'string'],
            'recommandations' => ['nullable', 'string'],
            'ordonnance' => ['nullable', 'string'],
            'type_consultation' => ['required', 'in:presentiel,visio_teleconsultation'],
            'lien_teleconsultation' => ['nullable', 'url', 'max:255'],
            'temperature' => ['nullable', 'numeric', 'between:30,45'],
            'tension_arterielle' => ['nullable', 'string', 'max:20'],
            'taux_glycemie' => ['nullable', 'numeric', 'between:0,1000'],
            'poids' => ['nullable', 'numeric', 'between:1,350'],
            'symptomes' => ['nullable', 'string', 'max:2000'],
            'fichier_resultat' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx'],
            'note_resultat' => ['nullable', 'string'],
            'observations' => ['nullable', 'string'],
            'ordonnance_produits' => ['nullable', 'string', 'max:5000'],
            'ordonnance_prescription' => ['nullable', 'string', 'max:5000'],
            'ordonnance_recommandations' => ['nullable', 'string', 'max:5000'],
            'ordonnance_instructions' => ['nullable', 'string', 'max:5000'],
            'imprimer_ordonnance' => ['nullable', 'boolean'],
            'creer_examen' => ['nullable', 'boolean'],
            'examen_mode_orientation' => ['nullable', 'in:interne,recommandation'],
            'examen_libelle' => ['nullable', 'string', 'max:255'],
            'examen_libelles' => ['nullable', 'string', 'max:5000'],
            'examen_service_id' => ['nullable', 'integer', 'exists:services_professionnels,id'],
            'examen_dossier_professionnel_cible_id' => ['nullable', 'integer', 'exists:dossiers_professionnels,id'],
            'examen_whatsapp' => ['nullable', 'string', 'max:50'],
            'examen_note_orientation' => ['nullable', 'string', 'max:2000'],
            'statut' => ['required', 'in:brouillon,finalise'],
        ];
    }
}
