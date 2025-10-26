<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * === Ajustes do schema legado ===
     * - PK: user_id
     * - Sem timestamps automáticos
     */
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $incrementing = true;
    public $timestamps = false;

    /**
     * Campos graváveis em massa.
     */
    protected $fillable = [
        // núcleo
        'username',
        'email',
        'password',

        // legado / opcionais
        'avatar',
        'about',
        'country',
        'gender',
        'birthday',
        'registered_date',

        // verificação
        'email_verified_at',

        // papéis
        'is_admin',

        // social login
        'provider',
        'provider_id',
        'provider_token',
        'provider_refresh_token',

        // se sua tabela tiver este campo
        'profile_photo_url',
    ];

    /**
     * Campos ocultos na serialização.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'provider_token',
        'provider_refresh_token',
    ];

    /**
     * Casts de tipos.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'registered_date'   => 'datetime',
        'is_admin'          => 'boolean',
        'birthday'          => 'date',
    ];

    /**
     * Acessor unificado da foto:
     * - Se existir coluna profile_photo_url e estiver preenchida, usa ela
     * - Senão, cai no avatar (URL absoluta) quando existir
     * - Senão, um placeholder
     */
    public function getProfilePhotoUrlAttribute()
    {
        // Se a coluna existir no banco e houver valor, retorna direto
        if (Schema::hasColumn($this->getTable(), 'profile_photo_url') && !empty($this->attributes['profile_photo_url'] ?? null)) {
            return $this->attributes['profile_photo_url'];
        }

        // Senão, usa avatar (muitos legados salvavam URL do provedor em avatar)
        $avatar = $this->attributes['avatar'] ?? null;
        if (is_string($avatar) && trim($avatar) !== '') {
            // se não for URL absoluta, tenta transformar em asset()
            if (preg_match('~^https?://~i', $avatar)) {
                return $avatar;
            }
            return url($avatar);
        }

        // Placeholder padrão
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->username ?? ($this->email ?? 'U')) . '&background=E5E7EB&color=374151';
    }

    /**
     * Mutator para garantir e-mail em minúsculas.
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = is_string($value) ? mb_strtolower(trim($value)) : $value;
    }
}
