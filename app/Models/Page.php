<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['title','slug','content','is_active'];
    // Se o nome da tabela for diferente, especifique:
    // protected $table = 'pages';
}
