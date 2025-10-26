<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    protected $table = 'bookmarks';
    protected $primaryKey = 'bm_id';
    public $timestamps = false;
    protected $guarded = [];
}