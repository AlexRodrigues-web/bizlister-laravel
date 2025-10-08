<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'category'; // TABELA LEGADO
    protected $primaryKey = 'cat_id';
    public $timestamps = false;

    protected $fillable = ['cat_name'];
}