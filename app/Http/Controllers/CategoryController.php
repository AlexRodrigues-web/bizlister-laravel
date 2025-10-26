<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * GET /categorias
     * Mantém a listagem simples; com paginação se possível.
     */
    public function index()
    {
        try {
            $categories = Category::orderBy('cat_id')
                ->paginate(24)
                ->withQueryString();
        } catch (\Throwable $e) {
            $categories = Category::orderBy('cat_id')->get();
        }

        return view('categories.index', compact('categories'));
    }

    /**
     * GET /categoria/{id}-{slug?}
     * Mostra os negócios da categoria com busca e ordenação usadas pela sua view.
     */
    public function show($id, ?string $slug = null, Request $request)
    {
        // Busca pela PK real (legado)
        $category = Category::where('cat_id', $id)->firstOrFail();

        // Slug canônico (evita páginas “cruas” por URL inconsistente)
        $rawName = $category->category
            ?? $category->name
            ?? $category->label
            ?? $category->cat_name
            ?? $category->category_name
            ?? "categoria-{$category->cat_id}";

        $expectedSlug = Str::slug((string) $rawName);
        if ($expectedSlug === '') {
            $expectedSlug = (string) $category->cat_id;
        }

        if ($slug !== $expectedSlug) {
            return redirect()
                ->route('categories.show', ['id' => $category->cat_id, 'slug' => $expectedSlug])
                ->setStatusCode(301);
        }

        // Filtros vindos da UI (sua view já manda ?q=&ord=)
        $q   = trim((string) $request->query('q', ''));
        $ord = trim((string) $request->query('ord', ''));

        // Base: pela FK cid
        $query = Business::where('cid', $category->cat_id);

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('business_name', 'LIKE', "%{$q}%")
                  ->orWhere('description', 'LIKE', "%{$q}%");
            });
        }

        // Ordenação compatível com a sua view
        switch ($ord) {
            case 'recentes':
                // Se não há created_at no legado, usar biz_id desc como proxy de "mais recente"
                $query->orderByDesc('biz_id');
                break;

            case 'nome_az':
                $query->orderBy('business_name', 'asc');
                break;

            case 'nome_za':
                $query->orderBy('business_name', 'desc');
                break;

            default:
                // Ordenação padrão estável
                $query->orderBy('business_name', 'asc');
                break;
        }

        // Paginação com preservação da query-string
        try {
            $businesses = $query->paginate(12)->withQueryString();
        } catch (\Throwable $e) {
            $businesses = $query->get();
        }

        $pageTitle = "Categoria: " . (string) $rawName;

        return view('categories.show', compact(
            'category',
            'businesses',
            'q',
            'ord',
            'pageTitle'
        ));
    }
}
