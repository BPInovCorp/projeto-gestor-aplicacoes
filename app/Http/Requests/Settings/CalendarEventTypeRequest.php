<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class CalendarEventTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('configuracoes.gerir');
    }

    public function rules(): array
    {
        $id = $this->route('calendar_event_type') ? $this->route('calendar_event_type')->id : 'NULL';

        return [
            'name' => ['required', 'string', 'max:255', 'unique:calendar_event_types,name,' . $id],
            'color' => ['nullable', 'string', 'size:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do tipo de evento é obrigatório.',
            'name.unique' => 'Já existe um tipo com este nome.',
            'color.size' => 'A cor deve ter exatamente 7 caracteres (formato HEX).',
            'color.regex' => 'A cor deve ser um código hexadecimal válido (ex: #3B82F6).',
            'is_active.required' => 'O estado é obrigatório.',
        ];
    }
}