<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Já está atrás de middleware 'auth', mas deixamos explícito:
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'business_name' => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string', 'max:1000'],

            // legado: FKs para category/city
            'cid'           => ['required', 'integer', 'min:1', 'exists:category,cat_id'],
            'sid'           => ['required', 'integer', 'min:1', 'exists:city,city_id'],

            // campo de compatibilidade (não é usado para persistir no controller)
            'city'          => ['nullable', 'string', 'max:255'],

            // upload opcional, até 2MB, formatos comuns
            'image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            // campos adicionais opcionais
            'email'         => ['nullable', 'email', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:255'],
            'website'       => ['nullable', 'url', 'max:255'],
            'tags'          => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'business_name.required' => 'Informe o nome do negócio.',
            'cid.required'           => 'Selecione a categoria.',
            'sid.required'           => 'Selecione a cidade.',
            'image.mimes'            => 'A imagem deve ser JPG, PNG ou WEBP.',
            'image.max'              => 'A imagem deve ter no máximo 2MB.',
        ];
    }
}
