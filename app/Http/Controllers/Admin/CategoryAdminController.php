<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryAdminController extends Controller
{
    public function index()
    {
        $items = Category::orderBy('category')->paginate(15);
        return view('admin.categories.index', compact('items'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        // Aceita nomes legado: category | label | cat_name
        $name = trim((string) ($request->input('category') ?? $request->input('label') ?? $request->input('cat_name') ?? ''));
        if ($name === '') {
            return back()->withErrors(['category' => 'Informe o nome da categoria.'])->withInput();
        }
        Category::create(['category' => $name]);
        return redirect()->route('admin.categories.index')->with('success', 'Categoria criada.');
    }

    public function edit($cat)
    {
        $item = Category::where('cat_id', $cat)->firstOrFail();
        return view('admin.categories.edit', compact('item'));
    }

    public function update(UpdateCategoryRequest $request, $cat): RedirectResponse
    {
        $item = Category::where('cat_id', $cat)->firstOrFail();
        $name = trim((string) ($request->input('category') ?? $request->input('label') ?? $request->input('cat_name') ?? ''));
        if ($name === '') {
            return back()->withErrors(['category' => 'Informe o nome da categoria.'])->withInput();
        }
        $item->update(['category' => $name]);
        return redirect()->route('admin.categories.index')->with('success', 'Categoria atualizada.');
    }

    public function destroy($cat): RedirectResponse
    {
        $item = Category::where('cat_id', $cat)->firstOrFail();
        $item->delete();
        return back()->with('success', 'Categoria removida.');
    }
}