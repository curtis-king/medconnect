<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceProfessionnelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'type' => ['required', 'in:consultation,examen,hospitalisation,chirurgie,urgence,autre'],
            'prix' => ['required', 'numeric', 'min:0'],
            'actif' => ['boolean'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom du service est obligatoire.',
            'type.required' => 'Le type de service est obligatoire.',
            'type.in' => 'Le type de service n\'est pas valide.',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.numeric' => 'Le prix doit être un nombre.',
            'prix.min' => 'Le prix ne peut pas être négatif.',
        ];
    }
}
