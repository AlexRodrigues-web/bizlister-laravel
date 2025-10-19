<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'biz_id', 'user_id', 'rating', 'title', 'body', 'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'bool',
        'rating' => 'int',
    ];

    public function business(): BelongsTo
    {
        // business.biz_id
        return $this->belongsTo(Business::class, 'biz_id', 'biz_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}