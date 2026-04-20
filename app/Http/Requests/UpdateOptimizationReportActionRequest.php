<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOptimizationReportActionRequest extends FormRequest
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
            'admin_response' => ['nullable', 'string', 'max:5000'],
            'action_plan' => ['nullable', 'string', 'max:5000'],
            'action_status' => ['required', 'in:pending,in_progress,done,blocked'],
            'action_due_date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'action_status.required' => 'Le statut d\'action est obligatoire.',
            'action_status.in' => 'Le statut d\'action sélectionné est invalide.',
            'action_due_date.date' => 'La date d\'échéance doit être une date valide.',
        ];
    }
}
