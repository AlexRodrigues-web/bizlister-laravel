<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class, 'user_id', 'user_id');
    }

    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $timestamps = false;

    // mantÃƒÂ©m os fillables jÃƒÂ¡ definidos (legado)
    protected $fillable = [
        'username',
        'email',
        'password',
        'registered_date',
        'country',
        'gender',
        'birthday',
        'about',
        'avatar',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        // Se algum dia existir uma flag booleana (ex.: is_admin/active), basta descomentar/adicionar:
        // 'is_admin' => 'boolean',
        // 'active'   => 'boolean',
    ];
}
