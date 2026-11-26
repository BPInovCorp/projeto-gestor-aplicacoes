<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'entity_id' => ['required', 'exists:entities,id'],
            'contact_role_id' => ['nullable', 'exists:contact_roles,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'mobile' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'rgpd_consent' => ['boolean'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'entity_id.required' => 'A seleção da entidade é obrigatória.',
            'entity_id.exists' => 'A entidade selecionada não é válida.',
            'contact_role_id.exists' => 'A função selecionada não é válida.',
            'first_name.required' => 'O nome é obrigatório.',
            'first_name.string' => 'O nome tem de ser texto.',
            'first_name.max' => 'O nome não pode exceder 255 caracteres.',
            'last_name.required' => 'O apelido é obrigatório.',
            'last_name.string' => 'O apelido tem de ser texto.',
            'last_name.max' => 'O apelido não pode exceder 255 caracteres.',
            'phone.max' => 'O telefone não pode exceder 50 caracteres.',
            'mobile.max' => 'O telemóvel não pode exceder 50 caracteres.',
            'email.email' => 'O email introduzido não é válido.',
            'email.max' => 'O email não pode exceder 255 caracteres.',
            'rgpd_consent.boolean' => 'O consentimento RGPD tem de ser verdadeiro ou falso.',
            'is_active.boolean' => 'O estado tem de ser verdadeiro ou falso.',
        ];
    }
}