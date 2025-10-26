<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;

class CategoryAdminController extends Controller
{
    /** Leitura via objeto legado (view/tabela) */
    protected string $readView = 'category';

    /** Verifica se uma tabela é VIEW (MySQL) */
    protected function isView(string $table): bool
    {
        try {
            $count = DB::scalar("
                SELECT COUNT(*) FROM INFORMATION_SCHEMA.VIEWS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
            ", [$table]);
            return (int) $count > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /** Define a tabela de ESCRITA (INSERT/UPDATE) */
    protected function resolveWriteTable(): string
    {
        if ($this->isView('category') && Schema::hasTable('categories')) {
            return 'categories';
        }
        return 'category';
    }

    /**
     * Monta payload com colunas existentes + defaults seguros
     * - Preenche variantes de nome (category/cat_name/name/title)
     * - Slug (slug/cat_slug)
     * - Flags e campos comuns exigidos sem default
     */
    protected function makePayload(string $table, string $nm, bool $forUpdate = false): array
    {
        $payload = [];

        // Nome visível (prioriza colunas se existirem)
        foreach (['category', 'cat_name', 'name', 'title'] as $col) {
            if (Schema::hasColumn($table, $col)) {
                $payload[$col] = $nm;
            }
        }

        // Slug
        $slugValue = Str::slug($nm);
        foreach (['slug', 'cat_slug'] as $col) {
            if (Schema::hasColumn($table, $col)) {
                $payload[$col] = $slugValue;
            }
        }

        // Colunas boolean/flag típicas (se existirem, define ativo)
        foreach (['is_active', 'active', 'enabled', 'visible', 'status'] as $col) {
            if (Schema::hasColumn($table, $col) && !array_key_exists($col, $payload)) {
                // Algumas bases usam 1/0, outras '1'/'0' — 1 funciona bem
                $payload[$col] = 1;
            }
        }

        // Campos string obrigatórios sem default em muitos dumps
        foreach ([
            'cat_description', 'description', 'icon', 'image',
            'meta_title', 'meta_description', 'meta_keywords'
        ] as $col) {
            if (Schema::hasColumn($table, $col) && !array_key_exists($col, $payload)) {
                $payload[$col] = '';
            }
        }

        // Inteiros comuns (parent/ordem)
        foreach (['cat_parent', 'parent_id', 'ordering', 'order', 'position'] as $col) {
            if (Schema::hasColumn($table, $col) && !array_key_exists($col, $payload)) {
                $payload[$col] = 0;
            }
        }

        // Timestamps
        if (Schema::hasColumn($table, 'updated_at')) {
            $payload['updated_at'] = now();
        }
        if (!$forUpdate && Schema::hasColumn($table, 'created_at')) {
            $payload['created_at'] = now();
        }

        return $payload;
    }

    // GET /admin/categories
    public function index()
    {
        // Lê do objeto legado "category" (view/tabela)
        $categories = DB::table($this->readView)
            ->select(['cat_id', 'category'])
            ->orderBy('cat_id')
            ->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    // GET /admin/categories/create
    public function create()
    {
        return view('admin.categories.create');
    }

    // POST /admin/categories
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category' => ['required', 'string', 'max:190'],
        ]);
        $nm = trim($data['category']);

        $writeTable = $this->resolveWriteTable();
        $payload    = $this->makePayload($writeTable, $nm, false);

        try {
            DB::table($writeTable)->insert($payload);
        } catch (QueryException $qe) {
            $errno = $qe->errorInfo[1] ?? null;

            // Se for VIEW com underlying NOT NULLs (1423), tenta 'categories'
            if ($errno == 1423 && $writeTable !== 'categories' && Schema::hasTable('categories')) {
                $altPayload = $this->makePayload('categories', $nm, false);
                DB::table('categories')->insert($altPayload);
            } else {
                // Se cair aqui por 1364 (mais um NOT NULL sem default),
                // é porque a tabela tem outra coluna obrigatória não mapeada acima.
                // Adicione no array de defaults em makePayload e tente novamente.
                throw $qe;
            }
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Categoria criada com sucesso.');
    }

    // GET /admin/categories/{id}/edit
    public function edit($id)
    {
        $category = DB::table($this->readView)->where('cat_id', $id)->first();
        if (!$category) {
            abort(404, 'Categoria não encontrada');
        }

        return view('admin.categories.edit', compact('category'));
    }

    // PUT/PATCH /admin/categories/{id}
    public function update(Request $request, $id): RedirectResponse
    {
        $data = $request->validate([
            'category' => ['required', 'string', 'max:190'],
        ]);
        $nm = trim($data['category']);

        $exists = DB::table($this->readView)->where('cat_id', $id)->exists();
        if (!$exists) {
            abort(404, 'Categoria não encontrada');
        }

        $writeTable = $this->resolveWriteTable();
        $payload    = $this->makePayload($writeTable, $nm, true);

        $updated = 0;
        try {
            if (Schema::hasColumn($writeTable, 'cat_id')) {
                $updated = DB::table($writeTable)->where('cat_id', $id)->update($payload);
            }
            if (!$updated && Schema::hasColumn($writeTable, 'id')) {
                $updated = DB::table($writeTable)->where('id', $id)->update($payload);
            }
        } catch (QueryException $qe) {
            $errno = $qe->errorInfo[1] ?? null;

            if ($errno == 1423 && $writeTable !== 'categories' && Schema::hasTable('categories')) {
                $payload = $this->makePayload('categories', $nm, true);

                // tenta mapear id vindo da view para a tabela 'categories'
                $mappedId = null;
                $row = DB::table($this->readView)->select('id')->where('cat_id', $id)->first();
                if ($row && isset($row->id)) {
                    $mappedId = $row->id;
                }

                if ($mappedId !== null && Schema::hasColumn('categories', 'id')) {
                    DB::table('categories')->where('id', $mappedId)->update($payload);
                } elseif (Schema::hasColumn('categories', 'cat_id')) {
                    DB::table('categories')->where('cat_id', $id)->update($payload);
                } elseif (Schema::hasColumn('categories', 'id')) {
                    DB::table('categories')->where('id', $id)->update($payload);
                }
            } else {
                throw $qe;
            }
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Categoria atualizada com sucesso.');
    }

    // DELETE /admin/categories/{id}
    public function destroy($id): RedirectResponse
    {
        if (DB::table('business')->where('cid', $id)->exists()) {
            return back()->with('error', 'Não é possível excluir: há negócios vinculados a esta categoria.');
        }

        $writeTable = $this->resolveWriteTable();

        try {
            $deleted = 0;
            if (Schema::hasColumn($writeTable, 'cat_id')) {
                $deleted = DB::table($writeTable)->where('cat_id', $id)->delete();
            }
            if (!$deleted && Schema::hasColumn($writeTable, 'id')) {
                $deleted = DB::table($writeTable)->where('id', $id)->delete();
            }

            if (!$deleted && $writeTable !== 'categories' && Schema::hasTable('categories')) {
                if (Schema::hasColumn('categories', 'cat_id')) {
                    $deleted = DB::table('categories')->where('cat_id', $id)->delete();
                } elseif (Schema::hasColumn('categories', 'id')) {
                    $deleted = DB::table('categories')->where('id', $id)->delete();
                }
            }

            if (!$deleted) {
                abort(404, 'Categoria não encontrada');
            }

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Categoria excluída com sucesso.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Não foi possível excluir a categoria. Verifique vínculos e tente novamente.');
        }
    }
}
