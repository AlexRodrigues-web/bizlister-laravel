<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function authorize()
    {
        // rota já passa por middleware is_admin; liberar aqui
        return auth()->check();
    }

    public function rules()
    {
        return [
            'site_title'        => ['nullable','string','max:500'],
            'site_link'         => ['nullable','string','max:999'],
            'meta_keywords'     => ['nullable','string','max:999'],
            'meta_description'  => ['nullable','string','max:999'],
            'home_text'         => ['nullable','string','max:999'],
            'site_email'        => ['nullable','email','max:500'],
            'county'            => ['nullable','string','max:255'],
            'zip'               => ['nullable','string','max:255'],
            'fb_app_id'         => ['nullable','string','max:999'],
            'fb_secret_key'     => ['nullable','string','max:500'],
            'fb_page'           => ['nullable','string','max:999'],
            'twitter_link'      => ['nullable','string','max:999'],
            'pinterest_link'    => ['nullable','string','max:999'],
            'google_pluse_link' => ['nullable','string','max:999'],
            'active'            => ['nullable','boolean'],
            'rev_active'        => ['nullable','boolean'],
            'template'          => ['nullable','string','max:256'],
            'site_views'        => ['nullable','integer','min:0'],
            'vertion'           => ['nullable','string','max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'active'     => (int) (bool) $this->input('active'),
            'rev_active' => (int) (bool) $this->input('rev_active'),
        ]);
    }
}
