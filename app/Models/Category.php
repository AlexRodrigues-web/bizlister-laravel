<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'cat_id';

    // public $timestamps = true;
    // protected $fillable = ['name','slug','description','parent_id','is_active'];
}