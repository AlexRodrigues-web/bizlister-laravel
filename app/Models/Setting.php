<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // ajuste a tabela se necessário
    protected $table = 'settings';

    // ajuste os fillables/guardeds se necessário
    protected $guarded = [];

    // ajuste timestamps conforme seu schema
    public $timestamps = false;
}