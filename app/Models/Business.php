<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $table = "business";
    protected $primaryKey = "biz_id";
    public $timestamps = false;

    // Liberar mass assignment para preencher colunas obrigatórias herdadas (legacy)
    protected $guarded = [];
}