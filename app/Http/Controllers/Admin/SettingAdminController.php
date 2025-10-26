<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequest;
use App\Models\Setting;

class SettingAdminController extends Controller
{
    /**
     * Exibe o formulário de configurações.
     * Garante que haja um registro (id=1) com valores padrão.
     */
    public function edit()
    {
        $setting = Setting::query()->first();

        if (!$setting) {
            // Defaults seguros p/ sua tabela (conforme SHOW COLUMNS)
            $setting = Setting::query()->firstOrCreate(
                ['id' => 1],
                [
                    'site_title'        => config('app.name', 'BizLister'),
                    'site_link'         => config('app.url', url('/')),
                    'meta_keywords'     => '',
                    'meta_description'  => '',
                    'home_text'         => '',
                    'site_email'        => '',
                    'county'            => '',
                    'zip'               => '',
                    'fb_app_id'         => '',
                    'fb_secret_key'     => '',
                    'fb_page'           => '',
                    'twitter_link'      => '',
                    'pinterest_link'    => '',
                    'google_pluse_link' => '',
                    'active'            => 1,
                    'rev_active'        => 1,
                    'template'          => 'default',
                    'site_views'        => 0,
                    'vertion'           => '1.0.0',
                ]
            );
        }

        return view('admin.settings.edit', compact('setting'));
    }

    /**
     * Salva as configurações.
     */
    public function update(SettingRequest $request)
    {
        $setting = Setting::query()->firstOrCreate(['id' => 1]);

        $data = $request->validated();

        // Lista de campos realmente existentes na sua tabela
        $fillable = [
            'site_title','site_link','meta_keywords','meta_description','home_text','site_email',
            'county','zip','fb_app_id','fb_secret_key','fb_page','twitter_link','pinterest_link',
            'google_pluse_link','active','rev_active','template','site_views','vertion',
        ];

        // 1) Filtra apenas os campos permitidos e aplica trim em strings
        $payload = [];
        foreach ($fillable as $key) {
            if (array_key_exists($key, $data)) {
                $value = $data[$key];
                $payload[$key] = is_string($value) ? trim($value) : $value;
            }
        }

        // 2) Normalizações específicas

        // site_link -> garante esquema (http/https)
        if (isset($payload['site_link']) && is_string($payload['site_link']) && $payload['site_link'] !== '') {
            if (!preg_match('~^https?://~i', $payload['site_link'])) {
                $payload['site_link'] = 'http://' . ltrim($payload['site_link'], '/');
            }
        }

        // Flags -> 0/1
        $payload['active']     = isset($data['active']) ? 1 : 0;
        $payload['rev_active'] = isset($data['rev_active']) ? 1 : 0;

        // site_views -> inteiro não negativo (se enviado)
        if (array_key_exists('site_views', $payload)) {
            $views = (int) $payload['site_views'];
            $payload['site_views'] = max(0, $views);
        }

        // 3) Persiste
        $setting->fill($payload);
        $setting->save();

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', 'Configurações salvas!');
    }
}
