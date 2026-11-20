<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('utilizadores.atualizar');
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $userId],
            'mobile' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function withValidator(\Illuminate\Validation\Validator $validator)
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $user = $this->route('user');
            if ($user->id === $this->user()->id && $this->input('is_active') === false) {
                $validator->errors()->add('is_active', 'Não pode desativar a sua própria conta de utilizador.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O campo email deve ser um endereço de email válido.',
            'email.unique' => 'Este email já se encontra registado.',
            'role.required' => 'O grupo de permissões é obrigatório.',
            'role.exists' => 'O grupo de permissões selecionado é inválido.',
            'is_active.required' => 'O estado é obrigatório.',
        ];
    }
}