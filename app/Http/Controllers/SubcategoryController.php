<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class SubcategoryController extends Controller
{
    public function __construct()
    {
        // Protege apenas os métodos de admin; 'show' é público
        $this->middleware(['auth', 'is_admin'])
            ->only(['index','create','store','edit','update','destroy']);
    }

    /* =========================================================
     |  PÚBLICO
     * ========================================================= */

    /**
     * Exibe a subcategoria e a lista de negócios relacionados.
     * Rota: GET /subcategories/{subcategory:slug}
     */
    public function show(Subcategory $subcategory)
    {
        $businesses = $subcategory->businesses()
            ->with(['category'])
            // ⚠️ Legado não tem created_at/updated_at
            ->orderByDesc('biz_id')
            ->paginate(12);

        return view('subcategories.show', compact('subcategory','businesses'));
    }

    /* =========================================================
     |  ADMIN
     * ========================================================= */

    /**
     * Lista de subcategorias (admin).
     */
    public function index()
    {
        $subs = Subcategory::with('category')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.subcategories.index', compact('subs'));
    }

    /**
     * Form de criação (admin).
     */
    public function create()
    {
        // Ordena pelo melhor campo disponível no legado/atual
        $orderCol = Schema::hasColumn('categories','name') ? 'name'
                 : (Schema::hasColumn('categories','category_name') ? 'category_name'
                 : (Schema::hasColumn('categories','category') ? 'category' : 'cat_id'));

        $categories = Category::orderBy($orderCol)->get();

        return view('admin.subcategories.create', compact('categories'));
    }

    /**
     * Cria uma nova subcategoria (admin).
     */
    public function store(Request $request)
    {
        // Escolhe dinamicamente a PK de categories
        $categoryKey = Schema::hasColumn('categories','id') ? 'id' : 'cat_id';

        $data = $request->validate([
            'category_id' => ['required',"exists:categories,{$categoryKey}"],
            'name'        => ['required','max:255'],
            'slug'        => ['nullable','max:255', Rule::unique('subcategories','slug')],
            'description' => ['nullable'],
        ]);

        // Gera slug se não vier
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Evita duplicado de nome na mesma categoria (reforça o índice único)
        $existsSameName = Subcategory::where('category_id',$data['category_id'])
            ->where('name',$data['name'])
            ->exists();
        if ($existsSameName) {
            return back()
                ->withErrors(['name' => 'Já existe uma subcategoria com este nome nesta categoria.'])
                ->withInput();
        }

        Subcategory::create($data);

        return redirect()
            ->route('admin.subcategories.index')
            ->with('status', 'Subcategoria criada com sucesso!');
    }

    /**
     * Form de edição (admin).
     */
    public function edit(Subcategory $subcategory)
    {
        $orderCol = Schema::hasColumn('categories','name') ? 'name'
                 : (Schema::hasColumn('categories','category_name') ? 'category_name'
                 : (Schema::hasColumn('categories','category') ? 'category' : 'cat_id'));

        $categories = Category::orderBy($orderCol)->get();

        return view('admin.subcategories.edit', compact('subcategory','categories'));
    }

    /**
     * Atualiza a subcategoria (admin).
     */
    public function update(Request $request, Subcategory $subcategory)
    {
        $categoryKey = Schema::hasColumn('categories','id') ? 'id' : 'cat_id';

        $data = $request->validate([
            'category_id' => ['required',"exists:categories,{$categoryKey}"],
            'name'        => ['required','max:255'],
            'slug'        => [
                'nullable','max:255',
                Rule::unique('subcategories','slug')->ignore($subcategory->id),
            ],
            'description' => ['nullable'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $existsSameName = Subcategory::where('category_id',$data['category_id'])
            ->where('name',$data['name'])
            ->where('id','!=',$subcategory->id)
            ->exists();
        if ($existsSameName) {
            return back()
                ->withErrors(['name' => 'Já existe uma subcategoria com este nome nesta categoria.'])
                ->withInput();
        }

        $subcategory->update($data);

        return redirect()
            ->route('admin.subcategories.index')
            ->with('status', 'Subcategoria atualizada com sucesso!');
    }

    /**
     * Remove a subcategoria (admin).
     */
    public function destroy(Subcategory $subcategory)
    {
        $subcategory->delete();

        return redirect()
            ->route('admin.subcategories.index')
            ->with('status', 'Subcategoria removida com sucesso!');
    }
}
