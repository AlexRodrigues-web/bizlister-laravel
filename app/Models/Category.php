<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = "categories";
    public $timestamps = false;

    // PK correta do legado
    protected $primaryKey = "cat_id";

    public function businesses()
    {
        // FK em business -> cid | ownerKey = cat_id
        return $this->hasMany(Business::class, "cid", "cat_id");
    }
}
