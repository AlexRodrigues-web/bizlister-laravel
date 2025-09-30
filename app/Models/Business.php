<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $table = "business";
    public $timestamps = false;
    protected $primaryKey = "biz_id";

    public function category()
    {
        return $this->belongsTo(Category::class, "cid", "cat_id");
    }

    public function city()
    {
        // FK business.sid -> City.city_id
        return $this->belongsTo(City::class, "sid", "city_id");
    }
}
