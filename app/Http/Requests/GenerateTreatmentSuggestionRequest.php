<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateTreatmentSuggestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'symptomes' => ['nullable', 'string', 'max:2000'],
            'diagnostic_medecin' => ['nullable', 'string', 'max:4000'],
            'diagnostic' => ['nullable', 'string', 'max:4000'],
            'conclusion' => ['nullable', 'string', 'max:4000'],
            'recommandations' => ['nullable', 'string', 'max:5000'],
            'ordonnance_produits' => ['nullable', 'string', 'max:5000'],
            'ordonnance_prescription' => ['nullable', 'string', 'max:5000'],
            'ordonnance_recommandations' => ['nullable', 'string', 'max:5000'],
            'ordonnance_instructions' => ['nullable', 'string', 'max:5000'],
            '_active_tab' => ['nullable', 'string', 'in:consultation,ordonnances,examens,suivi,had,documents'],
        ];
    }
}
