<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    /** =========================
     *  TABELA E PK (LEGADO)
     *  ========================= */
    protected $table = 'reviews';
    protected $primaryKey = 'rev_id';
    public $timestamps = true; // sua tabela tem created_at / updated_at

    /** =========================
     *  ATRIBUTOS
     *  ========================= */
    protected $fillable = [
        'biz_id',      // FK -> business.biz_id
        'user_id',     // FK -> users.user_id
        'rating',
        'title',
        'body',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'bool',
        'rating'      => 'int',
    ];

    /** =========================
     *  RELACIONAMENTOS
     *  ========================= */
    public function business(): BelongsTo
    {
        // reviews.biz_id -> business.biz_id
        return $this->belongsTo(Business::class, 'biz_id', 'biz_id');
    }

    public function user(): BelongsTo
    {
        // reviews.user_id -> users.user_id  (seu esquema!)
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /** =========================
     *  SCOPES ÚTEIS
     *  ========================= */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('is_approved')->orWhere('is_approved', false);
        });
    }

    public function scopeRecent($query)
    {
        // sem depender de timestamps, ordena pela PK do legado
        return $query->orderByDesc($this->getKeyName()); // rev_id
    }

    /** =========================
     *  HELPERS
     *  ========================= */
    public function getSafeRatingAttribute(): int
    {
        $r = (int) ($this->rating ?? 0);
        return max(0, min(5, $r));
    }

    public function getStarsAttribute(): array
    {
        $filled = $this->safe_rating;
        return array_map(fn ($i) => $i < $filled, range(0, 4));
    }

    public function getDisplayTitleAttribute(): string
    {
        return trim((string) ($this->title ?? ''));
    }

    public function getDisplayBodyAttribute(): string
    {
        return trim((string) ($this->body ?? ''));
    }
}
