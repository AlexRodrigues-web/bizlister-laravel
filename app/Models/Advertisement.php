<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $table = 'advertisements';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = false; // id do legado não é auto_increment
    protected $keyType = 'int';
    protected $guarded = [];
}