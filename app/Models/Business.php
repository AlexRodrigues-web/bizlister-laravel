<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Business extends Model
{
    /**
     * Legado:
     * - tabela: business (sem 's')
     * - PK: biz_id
     */
    protected $table = 'business';
    protected $primaryKey = 'biz_id';
    public $timestamps = false;

    // Ajuste conforme sua política; deixo liberado para compatibilidade
    protected $guarded = [];

    /* =========================
     |  RELAÇÕES (com fallback)
     * ========================= */

    /**
     * Categoria:
     * - moderno: business.category_id → categories.id
     * - legado : business.cid         → categories.cat_id
     */
    public function category()
    {
        if (Schema::hasColumn($this->getTable(), 'category_id') &&
            Schema::hasColumn('categories', 'id')) {
            return $this->belongsTo(Category::class, 'category_id', 'id');
        }

        // Legado (mais comum no teu BD)
        return $this->belongsTo(Category::class, 'cid', 'cat_id');
    }

    /**
     * Subcategoria (FOCO DA MIGRAÇÃO):
     * - moderno: business.subcategory_id → subcategories.id
     * - legado : business.sid            → subcategories.sid
     */
    public function subcategory()
    {
        if (Schema::hasColumn($this->getTable(), 'subcategory_id') &&
            Schema::hasColumn('subcategories', 'id')) {
            return $this->belongsTo(Subcategory::class, 'subcategory_id', 'id');
        }

        // Fallback legado
        return $this->belongsTo(Subcategory::class, 'sid', 'sid');
    }

    /**
     * Cidade:
     * - se existir city_id → city.id (ou cities.id)
     * - fallback legado: sid (business) → city.city_id
     */
    public function city()
    {
        if (Schema::hasColumn($this->getTable(), 'city_id')) {
            // tenta 'city' depois 'cities'
            $cityTable = Schema::hasTable('city') ? 'city' : (Schema::hasTable('cities') ? 'cities' : null);
            if ($cityTable && Schema::hasColumn($cityTable, 'id')) {
                return $this->belongsTo(City::class, 'city_id', 'id');
            }
        }

        // Legado mais provável
        return $this->belongsTo(City::class, 'sid', 'city_id');
    }

    /**
     * Hours: 1:N
     * FK legado: hours.business_id → business.biz_id
     */
    public function hours()
    {
        return $this->hasMany(Hour::class, 'business_id', 'biz_id');
    }

    /**
     * Bookmarks: 1:N
     * FK legado: bookmarks.bizid → business.biz_id
     */
    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class, 'bizid', 'biz_id');
    }

    /**
     * Reviews: 1:N
     * Preferência moderna (reviews.business_id), fallback legado (reviews.biz_id)
     */
    public function reviews()
    {
        if (Schema::hasColumn('reviews', 'business_id')) {
            return $this->hasMany(Review::class, 'business_id', 'biz_id');
        }

        return $this->hasMany(Review::class, 'biz_id', 'biz_id');
    }

    /**
     * Imagens do negócio: 1:N
     * Tabela moderna: business_images.business_id → business.biz_id
     * (Mantém ordem por capa, depois sort e id)
     */
    public function images()
    {
        return $this->hasMany(\App\Models\BusinessImage::class, 'business_id', 'biz_id')
                    ->orderBy('is_primary', 'desc')
                    ->orderBy('sort_order', 'asc')
                    ->orderBy('id', 'asc');
    }

    /* =========================
     |  HELPERS
     * ========================= */

    public function approvedReviews()
    {
        $q = $this->reviews();
        if (Schema::hasColumn('reviews', 'is_approved')) {
            $q->where('is_approved', true);
        }
        return $q;
    }

    public function averageRating(): float
    {
        if (!Schema::hasColumn('reviews', 'rating')) {
            return 0.0;
        }
        $avg = $this->approvedReviews()->avg('rating');
        return (float) round($avg ?: 0, 1);
    }

    public function reviewsCount(): int
    {
        return (int) $this->approvedReviews()->count();
    }
}
