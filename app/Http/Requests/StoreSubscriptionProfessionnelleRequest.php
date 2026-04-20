<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionProfessionnelleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'frais_id' => ['required', 'exists:frais,id'],
            'nombre_mois' => ['required', 'integer', 'min:1', 'max:12'],
            'mode_paiement' => ['required', 'in:cash,mobile_money,virement,carte'],
            'reference_paiement' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'frais_id.required' => 'Veuillez sélectionner un tarif.',
            'frais_id.exists' => 'Le tarif sélectionné n\'existe pas.',
            'nombre_mois.required' => 'Le nombre de mois est obligatoire.',
            'mode_paiement.required' => 'Le mode de paiement est obligatoire.',
        ];
    }
}
