<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $table = "business";
    public $timestamps = false;

    // PK correta do legado
    protected $primaryKey = "biz_id";

    public function category()
    {
        // FK local = cid | ownerKey na Category = cat_id
        return $this->belongsTo(Category::class, "cid", "cat_id");
    }
}
