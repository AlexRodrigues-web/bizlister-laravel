<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Page extends Model
{
    protected $table = "pages";
    protected $primaryKey = "id";
    public $timestamps = true;

    protected $fillable = [
        "slug",
        "title",
        "content",
        "is_active",
        "published_at",
    ];

    protected $casts = [
        "is_active"    => "boolean",
        "published_at" => "datetime",
    ];

    /**
     * Escopo: somente publicadas (ativas e já publicadas)
     */
    public function scopePublished(Builder $q): Builder
    {
        return $q->where("is_active", 1)
                 ->where(function ($q) {
                     $q->whereNull("published_at")
                       ->orWhere("published_at", "<=", DB::raw("NOW()"));
                 });
    }
}
