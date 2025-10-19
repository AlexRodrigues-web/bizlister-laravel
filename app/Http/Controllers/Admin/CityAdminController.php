<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\RedirectResponse;

class CityAdminController extends Controller
{
    // GET /admin/cities
    public function index()
    {
        // Tabela legada: city (city_id, city[, uf])
        $hasUf = Schema::hasColumn('city', 'uf');

        $query = DB::table('city')->select('city_id', 'city');
        if ($hasUf) {
            $query->addSelect('uf');
        }

        $items = $query->orderBy('city_id')->paginate(15);
        $pk    = 'city_id';

        return view('admin.cities.index', compact('items', 'pk', 'hasUf'));
    }

    // GET /admin/cities/create
    public function create()
    {
        $hasUf = Schema::hasColumn('city', 'uf');
        return view('admin.cities.create', compact('hasUf'));
    }

    // POST /admin/cities
    public function store(Request $request): RedirectResponse
    {
        $hasUf = Schema::hasColumn('city', 'uf');

        $rules = [
            'city' => ['required', 'string', 'max:190'],
        ];
        if ($hasUf) {
            $rules['uf'] = ['required', 'string', 'size:2'];
        }

        $data = $request->validate($rules);

        $insert = ['city' => $data['city']];
        if ($hasUf) {
            $insert['uf'] = strtoupper($data['uf']);
        }

        DB::table('city')->insert($insert);

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'Cidade criada com sucesso.');
    }

    // GET /admin/cities/{id}/edit
    public function edit($id)
    {
        $hasUf = Schema::hasColumn('city', 'uf');

        $city = DB::table('city')->where('city_id', $id)->first();
        abort_unless($city, 404, 'Cidade não encontrada');

        return view('admin.cities.edit', compact('city', 'hasUf'));
    }

    // PUT /admin/cities/{id}
    public function update(Request $request, $id): RedirectResponse
    {
        $hasUf = Schema::hasColumn('city', 'uf');

        $rules = [
            'city' => ['required', 'string', 'max:190'],
        ];
        if ($hasUf) {
            $rules['uf'] = ['required', 'string', 'size:2'];
        }

        $data = $request->validate($rules);

        $exists = DB::table('city')->where('city_id', $id)->exists();
        abort_unless($exists, 404, 'Cidade não encontrada');

        $update = ['city' => $data['city']];
        if ($hasUf) {
            $update['uf'] = strtoupper($data['uf']);
        }

        DB::table('city')->where('city_id', $id)->update($update);

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'Cidade atualizada com sucesso.');
    }

   // DELETE /admin/cities/{id}
public function destroy($id): RedirectResponse
{
    $id = (int) $id;

    // Detecta colunas legadas na tabela business
    $hasSid  = Schema::hasColumn('business', 'sid');
    $hasCity = Schema::hasColumn('business', 'city');

    // WHERE (sid = ? OR city = ?)
    $hasDeps = false;
    if ($hasSid || $hasCity) {
        $hasDeps = DB::table('business')
            ->where(function ($q) use ($hasSid, $hasCity, $id) {
                $q->whereRaw('1=0');
                if ($hasSid)  { $q->orWhere('sid',  $id); }
                if ($hasCity) { $q->orWhere('city', $id); }
            })
            ->exists();
    }

    if ($hasDeps) {
        return back()->with('error', 'Não é possível excluir: há negócios vinculados a esta cidade.');
    }

    try {
        $deleted = DB::table('city')->where('city_id', $id)->delete();
        if (!$deleted) {
            abort(404, 'Cidade não encontrada');
        }

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'Cidade excluída com sucesso.');
    } catch (\Throwable $e) {
        // Padroniza a mensagem — também cobre FK RESTRICT
        return back()->with('error', 'Não é possível excluir: há negócios vinculados a esta cidade.');
    }
}
}
