<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AppUser extends Authenticatable
{
    use Notifiable;

    protected $table = "app_users";

    protected $fillable = [
        "name", "email", "password", "is_admin", "email_verified_at",
    ];

    protected $hidden = [
        "password", "remember_token",
    ];

    protected $casts = [
        "email_verified_at" => "datetime",
        "is_admin" => "boolean",
    ];
}
