<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'city';
    protected $primaryKey = 'city_id';

    // public $timestamps = true;
    // protected $fillable = ['name','slug','state','is_active'];
}