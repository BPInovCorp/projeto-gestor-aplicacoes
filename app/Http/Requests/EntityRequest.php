<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EntityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('entidades.criar') || $this->user()->can('entidades.atualizar');
    }

    public function rules(): array
    {
        $id = $this->route('entity') ? $this->route('entity')->id : 'NULL';

        return [
            'name' => ['required', 'string', 'max:255'],
            'is_client' => ['boolean'],
            'is_supplier' => ['boolean'],
            'nif' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:100'],
            'country_id' => ['required', 'exists:countries,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    protected function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            if (!$this->boolean('is_client') && !$this->boolean('is_supplier')) {
                $validator->errors()->add('is_client', 'A entidade deve ser pelo menos Cliente ou Fornecedor.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'nif.unique' => 'Este NIF já está registado.',
            'country_id.required' => 'O país é obrigatório.',
            'country_id.exists' => 'O país selecionado é inválido.',
        ];
    }
}