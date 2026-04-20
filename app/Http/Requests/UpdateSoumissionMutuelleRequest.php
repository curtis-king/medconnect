<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateSoumissionMutuelleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', 'in:processing,approve,reject,pay'],
            'montant_pris_en_charge' => ['nullable', 'numeric', 'min:0'],
            'motif_rejet' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $action = (string) $this->input('action');
            $soumission = $this->route('soumissionMutuelle');

            if ($action === 'approve' && ! $this->filled('montant_pris_en_charge')) {
                $validator->errors()->add('montant_pris_en_charge', 'Le montant pris en charge est obligatoire pour valider la demande.');
            }

            if ($action === 'approve' && $soumission && (float) $this->input('montant_pris_en_charge', 0) > (float) $soumission->montant_soumis) {
                $validator->errors()->add('montant_pris_en_charge', 'Le montant pris en charge ne peut pas depasser le montant soumis.');
            }

            if ($action === 'reject' && ! $this->filled('motif_rejet')) {
                $validator->errors()->add('motif_rejet', 'Le motif de rejet est obligatoire.');
            }

            if ($action === 'pay' && $soumission && ! in_array($soumission->statut, ['approuve', 'partiel'], true)) {
                $validator->errors()->add('action', 'Seules les demandes approuvees ou partielles peuvent etre payees.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'action.required' => 'L action a executer est obligatoire.',
            'action.in' => 'L action selectionnee est invalide.',
            'montant_pris_en_charge.numeric' => 'Le montant pris en charge doit etre numerique.',
            'montant_pris_en_charge.min' => 'Le montant pris en charge doit etre positif.',
            'motif_rejet.max' => 'Le motif de rejet est trop long.',
            'notes.max' => 'Les notes sont trop longues.',
        ];
    }
}
