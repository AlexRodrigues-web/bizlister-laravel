<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $table = 'business';
    protected $primaryKey = 'biz_id';
    public $timestamps = false; // legado nÃ£o tem created_at/updated_at

    protected $guarded = [];

    /**
     * =========================
     * RELACIONAMENTOS EXISTENTES
     * =========================
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'cid', 'cat_id');
    }

    // Mantido como estava (se no seu schema "sid" for cidade, vocÃª pode manter este
    // e tambÃ©m usar o city() abaixo â€” nÃ£o vou remover nada do seu legado).
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'sid', 'sid');
    }

    public function hours()
    {
        return $this->hasMany(Hour::class, 'business_id', 'biz_id');
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class, 'bizid', 'biz_id');
    }

    /**
     * =========================
     * NOVO: RELAÃ‡ÃƒO DE REVIEWS
     * =========================
     *
     * Ajuste a FK se no seu banco a coluna for diferente (ex.: 'biz_id').
     * O mais comum no seu padrÃ£o Ã© 'business_id' referenciando 'biz_id'.
     */
        /** RELAÇÃO CORRETA COM REVIEWS (FK = reviews.biz_id → business.biz_id) */
    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Review::class, 'biz_id', 'biz_id');
    }

    /**
     * (Opcional) Se "sid" for a cidade, esta relaÃ§Ã£o ajuda nas views novas,
     * sem quebrar nada do que jÃ¡ existe.
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'sid', 'city_id');
    }

    /**
     * =========================
     * HELPERS (NÃƒO INTRUSIVOS)
     * =========================
     * Ãšteis para as views: mÃ©dia e contagem sÃ³ de reviews aprovados.
     */
    public function approvedReviews()
    {
        return $this->reviews()->where('is_approved', true);
    }

    public function averageRating(): float
    {
        $avg = $this->approvedReviews()->avg('rating');
        return (float) round($avg ?: 0, 1);
    }

    public function reviewsCount(): int
    {
        return (int) $this->approvedReviews()->count();
    }
}


