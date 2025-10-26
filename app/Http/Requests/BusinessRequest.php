<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use App\Models\Subcategory;

class BusinessRequest extends FormRequest
{
    public function authorize(): bool
    {
        // As rotas já estão protegidas por auth; aqui liberamos.
        return true;
    }

    /**
     * Normalizações leves antes da validação.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'business_name'  => is_string($this->business_name) ? trim($this->business_name) : $this->business_name,
            'description'    => is_string($this->description) ? trim($this->description) : $this->description,
            'cid'            => $this->cid !== null ? (string) $this->cid : null,
            'sid'            => $this->sid !== null ? (string) $this->sid : null,
            'subcategory_id' => $this->subcategory_id !== null ? (string) $this->subcategory_id : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'business_name'  => ['required','string','max:120'],
            'description'    => ['nullable','string','max:2000'],

            // Categoria (legado usa "cid"); aceita categories.id OU categories.cat_id
            'cid' => [
                'required',
                function (string $attribute, $value, \Closure $fail) {
                    if (!$this->existsInCategories($value)) {
                        $fail('A categoria selecionada é inválida.');
                    }
                },
            ],

            // Subcategoria opcional: precisa existir e pertencer à categoria selecionada
            'subcategory_id' => [
                'nullable',
                'integer',
                Rule::exists('subcategories', 'id'),
                function (string $attribute, $value, \Closure $fail) {
                    if (!$value) return;
                    $catId = $this->resolveCategoryId($this->cid);
                    if (!$catId) return; // a regra de cid já acusa erro
                    $ok = Subcategory::where('id', $value)
                        ->where('category_id', $catId)
                        ->exists();
                    if (!$ok) {
                        $fail('A subcategoria selecionada não pertence à categoria escolhida.');
                    }
                },
            ],

            // Cidade (legado usa "sid"); aceita city/cities .id OU .city_id
            'sid' => [
                'required',
                function (string $attribute, $value, \Closure $fail) {
                    if (!$this->existsInCities($value)) {
                        $fail('A cidade selecionada é inválida.');
                    }
                },
            ],

            // Imagem opcional: 2MB, formatos comuns
            'image' => ['nullable','file','mimes:jpeg,jpg,png,webp','max:2048'],
        ];
    }

    /**
     * Mapeia nomes de atributos para mensagens mais legíveis.
     */
    public function attributes(): array
    {
        return [
            'business_name'  => 'nome do negócio',
            'description'    => 'descrição',
            'cid'            => 'categoria',
            'subcategory_id' => 'subcategoria',
            'sid'            => 'cidade',
            'image'          => 'imagem',
        ];
    }

    /**
     * Mensagens customizadas essenciais.
     */
    public function messages(): array
    {
        return [
            'business_name.required' => 'Informe o nome do negócio.',
            'business_name.max'      => 'O nome do negócio pode ter no máximo :max caracteres.',
            'description.max'        => 'A descrição pode ter no máximo :max caracteres.',
            'subcategory_id.exists'  => 'A subcategoria selecionada não foi encontrada.',
            'image.mimes'            => 'A imagem deve ser JPEG, JPG, PNG ou WEBP.',
            'image.max'              => 'A imagem pode ter no máximo :max kilobytes.',
        ];
    }

    /* =========================================================================
     |  Helpers de validação (compat com legado)
     * ========================================================================= */

    /**
     * Verifica se o valor existe em categories.id OU categories.cat_id.
     */
    protected function existsInCategories($value): bool
    {
        if (!Schema::hasTable('categories')) return false;

        // Tenta PK moderna
        if (Schema::hasColumn('categories', 'id')) {
            if (DB::table('categories')->where('id', $value)->exists()) {
                return true;
            }
        }

        // Tenta legado (cat_id)
        if (Schema::hasColumn('categories', 'cat_id')) {
            if (DB::table('categories')->where('cat_id', $value)->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resolve o ID "moderno" (categories.id) a partir do valor recebido (id ou cat_id).
     * Retorna null se não conseguir resolver.
     */
    protected function resolveCategoryId($value): ?int
    {
        if (!Schema::hasTable('categories')) return null;

        if (Schema::hasColumn('categories', 'id') &&
            DB::table('categories')->where('id', $value)->exists()) {
            return (int) $value;
        }

        if (Schema::hasColumn('categories', 'cat_id')) {
            $row = DB::table('categories')->select('id')->where('cat_id', $value)->first();
            if ($row && isset($row->id)) {
                return (int) $row->id;
            }
        }

        // Se a tabela ainda não tiver a coluna 'id', não há como resolver de forma confiável
        return null;
    }

    /**
     * Verifica se o valor existe em city/cities (id OU city_id).
     */
    protected function existsInCities($value): bool
    {
        // city
        if (Schema::hasTable('city')) {
            if (Schema::hasColumn('city', 'id') && DB::table('city')->where('id', $value)->exists()) {
                return true;
            }
            if (Schema::hasColumn('city', 'city_id') && DB::table('city')->where('city_id', $value)->exists()) {
                return true;
            }
        }

        // cities
        if (Schema::hasTable('cities')) {
            if (Schema::hasColumn('cities', 'id') && DB::table('cities')->where('id', $value)->exists()) {
                return true;
            }
            if (Schema::hasColumn('cities', 'city_id') && DB::table('cities')->where('city_id', $value)->exists()) {
                return true;
            }
        }

        return false;
    }
}
