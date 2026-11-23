<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class ContactRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('configuracoes.gerir');
    }

    public function rules(): array
    {
        $id = $this->route('contact_role') ? $this->route('contact_role')->id : 'NULL';

        return [
            'name' => ['required', 'string', 'max:255', 'unique:contact_roles,name,' . $id],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da função é obrigatório.',
            'name.unique' => 'Já existe uma função com este nome.',
            'is_active.required' => 'O estado é obrigatório.',
        ];
    }
}