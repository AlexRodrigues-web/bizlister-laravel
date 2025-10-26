<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Category extends Model
{
    // A tabela é "categories"
    protected $table = 'categories';

    // Campos liberados para atribuição em massa (ajuste conforme seu schema)
    protected $fillable = [
        'name',
        'slug',
        'description',
        // ex.: 'image', 'is_active'
    ];

    public $timestamps = true; // mantém padrão do Laravel

    /**
     * Chave primária dinâmica:
     * - usa 'id' (moderno) se existir
     * - caso contrário, usa 'cat_id' (legado)
     */
    public function getKeyName()
    {
        return Schema::hasColumn($this->getTable(), 'id') ? 'id' : 'cat_id';
    }

    /**
     * Subcategorias: subcategories.category_id → categories.(id|cat_id)
     */
    public function subcategories()
    {
        $localKey = Schema::hasColumn($this->getTable(), 'id') ? 'id' : 'cat_id';
        return $this->hasMany(Subcategory::class, 'category_id', $localKey);
    }

    /**
     * Negócios: business.cid → categories.(id|cat_id)
     * (No teu legado a FK da categoria em business é 'cid'.)
     */
    public function businesses()
    {
        $localKey = Schema::hasColumn($this->getTable(), 'id') ? 'id' : 'cat_id';
        return $this->hasMany(Business::class, 'cid', $localKey);
    }
}
