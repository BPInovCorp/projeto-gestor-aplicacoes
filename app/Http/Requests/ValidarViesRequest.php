<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarViesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo_pais' => ['required', 'string', 'size:2'],
            'nif' => ['required', 'string', 'min:2', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_pais.required' => 'O código do país é obrigatório.',
            'codigo_pais.string' => 'O código do país tem de ser texto.',
            'codigo_pais.size' => 'O código do país deve ter exatamente 2 caracteres.',
            'nif.required' => 'O NIF é obrigatório.',
            'nif.string' => 'O NIF tem de ser texto.',
            'nif.min' => 'O NIF deve conter pelo menos 2 caracteres.',
            'nif.max' => 'O NIF não pode ter mais de 20 caracteres.',
        ];
    }
}