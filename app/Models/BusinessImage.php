<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BusinessImage extends Model
{
    protected $table = 'business_images';

    protected $fillable = [
        'business_id',
        'path',        // ex.: businesses/{biz_id}/gallery/arquivo.jpg
        'title',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relação: FK local business_id -> business.biz_id
     */
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id', 'biz_id');
    }

    /**
     * Atributo dinâmico: URL pública da imagem.
     * Ex.: $image->url
     */
    public function getUrlAttribute(): string
    {
        // Se veio uma URL absoluta, retorna como está
        if (is_string($this->path) && preg_match('~^https?://~i', $this->path)) {
            return $this->path;
        }

        // Se existir no disco 'public', usa Storage::url
        if ($this->path && Storage::disk('public')->exists($this->path)) {
            return Storage::url($this->path);
        }

        // Fallback: tenta acessar via /storage/{path}
        return $this->path ? asset('storage/'.$this->path) : '';
    }

    /**
     * Normaliza o 'path' antes de salvar (remove barras duplicadas, espaços etc.)
     */
    public function setPathAttribute($value): void
    {
        $v = is_string($value) ? trim($value) : $value;

        // remove domínio se passar URL completa do próprio storage
        if (is_string($v) && preg_match('~^https?://[^/]+/storage/(.+)$~i', $v, $m)) {
            $v = $m[1];
        }

        // normaliza barras
        if (is_string($v)) {
            $v = preg_replace('~/+~', '/', ltrim($v, '/'));
        }

        $this->attributes['path'] = $v;
    }

    /**
     * Scopes úteis
     */
    public function scopeOrdered($q)
    {
        return $q->orderByDesc('is_primary')
                 ->orderBy('sort_order')
                 ->orderBy('id');
    }

    public function scopeForBusiness($q, int $bizId)
    {
        return $q->where('business_id', $bizId);
    }

    public function scopePrimary($q)
    {
        return $q->where('is_primary', true);
    }

    /**
     * Ao excluir o registro, tenta remover o arquivo do storage público.
     * (Silencioso se não existir.)
     */
    protected static function booted(): void
    {
        static::deleting(function (self $img) {
            if ($img->path && Storage::disk('public')->exists($img->path)) {
                try {
                    Storage::disk('public')->delete($img->path);
                } catch (\Throwable $e) {
                    // silencioso para não travar a exclusão do registro
                }
            }
        });
    }
}
