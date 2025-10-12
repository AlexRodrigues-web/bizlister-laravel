<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades.DB;
use Illuminate\Support\Facades\Schema;

class CityAdminController extends Controller
{
    private function table(): string { return 'city'; }

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

        return view('admin.cities.index', [
            'items' => $items,
            'pk'    => $pk,
            'cols'  => $cols,
        ]);
    }

    public function create()
    {
        return view('admin.cities.create');
    }

    public function store(Request $request)
    {
        $table = $this->table();

        // Campos prováveis no legado
        $data = $request->only(['city','name','title','uf','state','slug']);
        $data = array_filter($data, fn($v) => $v !== null && $v !== '');

        if (empty($data)) {
            return redirect()->back()->withErrors(['city' => 'Informe o nome da cidade.']);
        }

        $cols = Schema::getColumnListing($table);
        if (in_array('city', $cols, true) && !isset($data['city'])) {
            $data['city'] = $data['name'] ?? $data['title'] ?? null;
        }

        DB::table($table)->insert($data);
        return redirect()->route('admin.cities.index')->with('status', 'Cidade criada.');
    }

    public function edit($id)
    {
        $table = $this->table();
        $pk    = $this->pk($table);
        $item  = DB::table($table)->where($pk, $id)->first();
        abort_if(!$item, 404);

        return view('admin.cities.edit', compact('item','pk'));
    }

    public function update(Request $request, $id)
    {
        $table = $this->table();
        $pk    = $this->pk($table);

        $data  = $request->only(['city','name','title','uf','state','slug']);
        $data  = array_filter($data, fn($v) => $v !== null && $v !== '');

        if (empty($data)) {
            return redirect()->back()->withErrors(['city' => 'Nada para atualizar.']);
        }

        $cols = Schema::getColumnListing($table);
        if (in_array('city', $cols, true) && !isset($data['city'])) {
            $data['city'] = $data['name'] ?? $data['title'] ?? null;
        }

        DB::table($table)->where($pk, $id)->update($data);
        return redirect()->route('admin.cities.index')->with('status', 'Cidade atualizada.');
    }

    public function destroy($id)
    {
        $table = $this->table();
        $pk    = $this->pk($table);
        DB::table($table)->where($pk, $id)->delete();
        return redirect()->route('admin.cities.index')->with('status', 'Cidade removida.');
    }
}