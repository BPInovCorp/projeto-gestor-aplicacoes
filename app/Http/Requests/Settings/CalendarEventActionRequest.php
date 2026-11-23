<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class CalendarEventActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('configuracoes.gerir');
    }

    public function rules(): array
    {
        $id = $this->route('calendar_event_action') ? $this->route('calendar_event_action')->id : 'NULL';

        return [
            'name' => ['required', 'string', 'max:255', 'unique:calendar_event_actions,name,' . $id],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da ação de evento é obrigatório.',
            'name.unique' => 'Já existe uma ação com este nome.',
            'is_active.required' => 'O estado é obrigatório.',
        ];
    }
}