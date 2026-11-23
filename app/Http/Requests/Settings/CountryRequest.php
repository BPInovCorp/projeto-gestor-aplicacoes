<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class CountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('configuracoes.gerir');
    }

    public function rules(): array
    {
        $id = $this->route('country') ? $this->route('country')->id : 'NULL';

        return [
            'name' => ['required', 'string', 'max:255', 'unique:countries,name,' . $id],
            'code' => ['required', 'string', 'size:2', 'unique:countries,code,' . $id],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do país é obrigatório.',
            'name.unique' => 'Já existe um país com este nome.',
            'code.required' => 'O código do país é obrigatório.',
            'code.size' => 'O código do país deve ter exatamente 2 caracteres.',
            'code.unique' => 'Já existe um país com este código.',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper($this->code),
            ]);
        }
    }
}