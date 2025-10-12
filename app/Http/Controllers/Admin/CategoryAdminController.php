<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CategoryAdminController extends Controller
{
    private function table(): string { return 'category'; }

    private function pk(string $table): string
    {
        $rows = DB::select("SHOW KEYS FROM `$table` WHERE Key_name='PRIMARY'");
        return $rows[0]->Column_name ?? 'id';
    }

    public function index()
    {
        $table = $this->table();
        $pk    = $this->pk($table);
        $items = DB::table($table)->orderBy($pk)->paginate(15);
        $cols  = Schema::getColumnListing($table);

        return view('admin.categories.index', [
            'items' => $items,
            'pk'    => $pk,
            'cols'  => $cols,
        ]);
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $table = $this->table();

        // Campos prováveis na tabela legacy
        $data = $request->only(['category','name','title','slug','descricao','description']);
        $data = array_filter($data, fn($v) => $v !== null && $v !== '');

        if (empty($data)) {
            return redirect()->back()->withErrors(['category' => 'Informe um nome/título.']);
        }

        // Preferir gravar em 'category' se existir essa coluna
        $cols = Schema::getColumnListing($table);
        if (in_array('category', $cols, true) && !isset($data['category'])) {
            $data['category'] = $data['name'] ?? $data['title'] ?? null;
        }

        DB::table($table)->insert($data);
        return redirect()->route('admin.categories.index')->with('status', 'Categoria criada.');
    }

    public function edit($id)
    {
        $table = $this->table();
        $pk    = $this->pk($table);
        $item  = DB::table($table)->where($pk, $id)->first();
        abort_if(!$item, 404);

        return view('admin.categories.edit', compact('item','pk'));
    }

    public function update(Request $request, $id)
    {
        $table = $this->table();
        $pk    = $this->pk($table);

        $data  = $request->only(['category','name','title','slug','descricao','description']);
        $data  = array_filter($data, fn($v) => $v !== null && $v !== '');

        if (empty($data)) {
            return redirect()->back()->withErrors(['category' => 'Nada para atualizar.']);
        }

        $cols = Schema::getColumnListing($table);
        if (in_array('category', $cols, true) && !isset($data['category'])) {
            $data['category'] = $data['name'] ?? $data['title'] ?? null;
        }

        DB::table($table)->where($pk, $id)->update($data);
        return redirect()->route('admin.categories.index')->with('status', 'Categoria atualizada.');
    }

    public function destroy($id)
    {
        $table = $this->table();
        $pk    = $this->pk($table);
        DB::table($table)->where($pk, $id)->delete();
        return redirect()->route('admin.categories.index')->with('status', 'Categoria removida.');
    }
}