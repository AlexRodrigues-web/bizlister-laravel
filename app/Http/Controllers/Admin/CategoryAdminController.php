<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CategoryAdminController extends Controller
{
    // GET /admin/categories
    public function index()
    {
        // Tabela legada: category (cat_id, category)
        $categories = DB::table('category')
            ->select('cat_id', 'category')
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

        DB::table('category')->insert([
            'category' => $data['category'],
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Categoria criada com sucesso.');
    }

    // GET /admin/categories/{id}/edit
    public function edit($id)
    {
        $category = DB::table('category')->where('cat_id', $id)->first();
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

        $exists = DB::table('category')->where('cat_id', $id)->exists();
        if (!$exists) {
            abort(404, 'Categoria não encontrada');
        }

        DB::table('category')->where('cat_id', $id)->update([
            'category' => $data['category'],
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Categoria atualizada com sucesso.');
    }

    // DELETE /admin/categories/{id}
    public function destroy($id): RedirectResponse
    {
        // BLOQUEIO: não permitir excluir se houver negócios vinculados
        $hasDeps = DB::table('business')->where('cid', $id)->exists();
        if ($hasDeps) {
            return back()->with('error', 'Não é possível excluir: há negócios vinculados a esta categoria.');
        }

        try {
            $deleted = DB::table('category')->where('cat_id', $id)->delete();
            if (!$deleted) {
                abort(404, 'Categoria não encontrada');
            }

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Categoria excluída com sucesso.');
        } catch (\Throwable $e) {
            // Caso exista FK no banco (ON DELETE RESTRICT) ou outro erro
            return back()->with('error', 'Não foi possível excluir a categoria. Verifique vínculos e tente novamente.');
        }
    }
}
