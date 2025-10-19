<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "nome"     => ["required","string","max:120"],
            "email"    => ["required","email","max:160"],
            "assunto"  => ["required","string","max:160"],
            "mensagem" => ["required","string","max:5000"],
        ];
    }

    public function attributes(): array
    {
        return [
            "nome"     => "nome",
            "email"    => "e-mail",
            "assunto"  => "assunto",
            "mensagem" => "mensagem",
        ];
    }
}
