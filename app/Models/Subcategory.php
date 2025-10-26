<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Subcategory extends Model
{
    protected $table = 'subcategories';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
    ];

    /**
     * Route model binding por slug: /subcategories/{slug}
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Pertence a uma Category.
     * Dono pode ser 'id' (moderno) ou 'cat_id' (legado).
     */
    public function category()
    {
        $ownerKey = Schema::hasColumn('categories', 'id') ? 'id' : 'cat_id';
        return $this->belongsTo(Category::class, 'category_id', $ownerKey);
    }

    /**
     * Tem muitos Business.
     * FK em business = 'subcategory_id' → subcategories.id
     */
    public function businesses()
    {
        return $this->hasMany(Business::class, 'subcategory_id', 'id');
    }
}
