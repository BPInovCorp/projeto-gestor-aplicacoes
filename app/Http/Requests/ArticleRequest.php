<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $article = $this->route('article');
        $articleId = is_object($article) ? $article->id : $article;

        return [
            'reference' => [
                'required',
                'string',
                'max:100',
                Rule::unique('articles', 'reference')->ignore($articleId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'vat_rate_id' => ['required', 'exists:vat_rates,id'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'reference.required' => 'A referência é obrigatória.',
            'reference.string' => 'A referência tem de ser texto.',
            'reference.max' => 'A referência não pode exceder 100 caracteres.',
            'reference.unique' => 'Esta referência já se encontra registada.',
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome tem de ser texto.',
            'name.max' => 'O nome não pode exceder 255 caracteres.',
            'price.required' => 'O preço é obrigatório.',
            'price.numeric' => 'O preço tem de ser um valor numérico.',
            'price.min' => 'O preço não pode ser inferior a zero.',
            'vat_rate_id.required' => 'A seleção da taxa de IVA é obrigatória.',
            'vat_rate_id.exists' => 'A taxa de IVA selecionada não é válida.',
            'photo.image' => 'O ficheiro tem de ser uma imagem.',
            'photo.mimes' => 'A imagem tem de ser do formato JPEG, PNG, JPG ou WEBP.',
            'photo.max' => 'A imagem não pode ter mais de 2MB.',
            'is_active.boolean' => 'O estado tem de ser verdadeiro ou falso.',
        ];
    }
}