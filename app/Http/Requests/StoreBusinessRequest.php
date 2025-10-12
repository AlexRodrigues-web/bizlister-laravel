<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            "business_name" => ["required","string","max:255"],
            "description"   => ["required","string","max:1000"],
            "cid"           => ["required","integer","min:1"],
            "sid"           => ["required","integer","min:1"],
            "city"          => ["required","string","max:255"],
            "image"         => ["nullable","file","mimetypes:image/jpeg,image/png,image/webp","max:2048"], // 2MB
            "email"         => ["nullable","email","max:255"],
            "phone"         => ["nullable","string","max:255"],
            "website"       => ["nullable","url","max:255"],
            "tags"          => ["nullable","string","max:255"],
        ];
    }

    public function messages(): array
    {
        return [
            "business_name.required" => "Informe o nome do negócio.",
            "description.required"   => "Informe a descrição.",
            "cid.required"           => "Selecione a categoria.",
            "sid.required"           => "Selecione a cidade.",
            "city.required"          => "Informe o nome da cidade.",
            "image.mimetypes"        => "A imagem deve ser JPG, PNG ou WEBP.",
            "image.max"              => "A imagem deve ter no máximo 2MB.",
        ];
    }
}