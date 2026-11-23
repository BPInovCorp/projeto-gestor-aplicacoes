<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class VatRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('configuracoes.gerir');
    }

    public function rules(): array
    {
        $id = $this->route('vat_rate') ? $this->route('vat_rate')->id : 'NULL';

        return [
            'name' => ['required', 'string', 'max:255', 'unique:vat_rates,name,' . $id],
            'value' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da taxa é obrigatório.',
            'name.unique' => 'Já existe uma taxa com este nome.',
            'value.required' => 'O valor percentual é obrigatório.',
            'value.numeric' => 'O valor deve ser numérico.',
            'value.min' => 'O valor não pode ser negativo.',
            'value.max' => 'O valor não pode exceder 100%.',
            'is_active.required' => 'O estado é obrigatório.',
        ];
    }
}