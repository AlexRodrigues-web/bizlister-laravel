<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        $id = $this->route('page')?->id;

        return [
            'slug'         => ['nullable','max:255', Rule::unique('pages','slug')->ignore($id)],
            'title'        => ['required','max:255'],
            'content'      => ['nullable','string'],
            'is_active'    => ['required','boolean'],
            'published_at' => ['nullable','date'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => (int) (bool) $this->input('is_active', 0),
        ]);
    }
}
