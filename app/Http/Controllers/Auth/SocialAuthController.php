<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Provedores suportados.
     */
    protected array $providers = ['google', 'facebook'];

    /**
     * Redireciona para o provedor OAuth.
     */
    public function redirect(string $provider)
    {
        $provider = strtolower($provider);
        abort_unless(in_array($provider, $this->providers, true), 404);

        // Em dev, usar stateless minimiza erro de "state mismatch".
        return Socialite::driver($provider)->stateless()->redirect();
    }

    /**
     * Callback do provedor: cria/vincula e autentica o usuário.
     */
    public function callback(string $provider)
    {
        $provider = strtolower($provider);
        abort_unless(in_array($provider, $this->providers, true), 404);

        try {
            $social = Socialite::driver($provider)->stateless()->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')
                ->with('error', 'Não foi possível autenticar com ' . $provider . '. Tente novamente.');
        }

        // Campos recebidos
        $providerId   = (string) ($social->getId() ?? '');
        $name         = trim((string) ($social->getName() ?? $social->getNickname() ?? ''));
        $email        = strtolower(trim((string) ($social->getEmail() ?? '')));
        $avatarUrl    = $social->getAvatar();
        $accessToken  = property_exists($social, 'token') ? ($social->token ?? null) : null;
        $refreshToken = property_exists($social, 'refreshToken') ? ($social->refreshToken ?? null) : null;

        if ($providerId === '') {
            return redirect()->route('login')->with('error', 'Retorno inválido do ' . $provider . '.');
        }

        // 1) Tenta por (provider, provider_id)
        $user = User::query()
            ->where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();

        // 2) Senão, tenta por e-mail
        if (!$user && $email) {
            $user = User::query()->where('email', $email)->first();
        }

        // 3) Cria novo usuário (schema legado)
        if (!$user) {
            $baseUsername = $name !== '' ? Str::slug($name, '_') : (Str::before($email, '@') ?: 'user');
            $username     = $this->uniqueUsername($baseUsername);

            $user = new User();

            // Campos principais
            $user->username = $username;
            $user->email    = $email ?: null;

            // Opcional: marcar e-mail como verificado quando o provedor informar e-mail
            if ($email && Schema::hasColumn('users', 'email_verified_at')) {
                $user->email_verified_at = now();
            }

            // Senha aleatória (não usada para login social)
            $user->password = bcrypt(Str::random(40));

            // Datas legadas
            if (Schema::hasColumn('users', 'registered_date')) {
                $user->registered_date = now();
            }

            // Avatar / foto
            if (Schema::hasColumn('users', 'avatar') && $avatarUrl) {
                $user->avatar = $avatarUrl;
            }
            if (Schema::hasColumn('users', 'profile_photo_url') && $avatarUrl) {
                $user->profile_photo_url = $avatarUrl;
            }

            // Provedor
            if (Schema::hasColumn('users', 'provider')) {
                $user->provider = $provider;
            }
            if (Schema::hasColumn('users', 'provider_id')) {
                $user->provider_id = $providerId;
            }
            if (Schema::hasColumn('users', 'provider_token') && $accessToken) {
                $user->provider_token = $accessToken;
            }
            if (Schema::hasColumn('users', 'provider_refresh_token') && $refreshToken) {
                $user->provider_refresh_token = $refreshToken;
            }

            $user->save();
        } else {
            // Atualizações leves
            $dirty = false;

            if (Schema::hasColumn('users', 'provider') && empty($user->provider)) {
                $user->provider = $provider; $dirty = true;
            }
            if (Schema::hasColumn('users', 'provider_id') && empty($user->provider_id)) {
                $user->provider_id = $providerId; $dirty = true;
            }

            if ($avatarUrl) {
                if (Schema::hasColumn('users', 'avatar') && empty($user->avatar)) {
                    $user->avatar = $avatarUrl; $dirty = true;
                }
                if (Schema::hasColumn('users', 'profile_photo_url') && empty($user->profile_photo_url)) {
                    $user->profile_photo_url = $avatarUrl; $dirty = true;
                }
            }

            if ($accessToken && Schema::hasColumn('users', 'provider_token')) {
                $user->provider_token = $accessToken; $dirty = true;
            }
            if ($refreshToken && Schema::hasColumn('users', 'provider_refresh_token')) {
                $user->provider_refresh_token = $refreshToken; $dirty = true;
            }

            // Se o provedor informou e-mail e não está verificado, opcionalmente verifique
            if ($email && Schema::hasColumn('users', 'email_verified_at') && empty($user->email_verified_at)) {
                $user->email_verified_at = now(); $dirty = true;
            }

            if ($dirty) {
                $user->save();
            }
        }

        // Autentica e redireciona
        Auth::login($user, true);

        return redirect()->intended(route('profile.show'))
            ->with('status', 'Login realizado com ' . $provider . '.');
    }

    /**
     * Gera um username único baseado no prefixo informado.
     */
    protected function uniqueUsername(string $base): string
    {
        $candidate = Str::limit(
            preg_replace('/[^a-z0-9_]+/i', '_', strtolower($base)) ?: 'user',
            30,
            ''
        );
        $original = $candidate;
        $suffix   = 1;

        while (
            User::query()->where('username', $candidate)->exists()
            || User::query()->where('email', $candidate)->exists()
        ) {
            $candidate = Str::limit($original . '_' . $suffix, 30, '');
            $suffix++;
        }

        return $candidate;
    }
}
