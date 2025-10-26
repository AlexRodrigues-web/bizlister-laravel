<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Hour extends Model
{
    protected $table = 'hours';
    protected $primaryKey = 'hour_id';
    public $timestamps = false;
    protected $guarded = [];
}