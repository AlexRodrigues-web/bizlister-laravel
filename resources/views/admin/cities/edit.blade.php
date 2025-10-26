<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CityAdminController extends Controller
{
    // GET /admin/cities
    public function index()
    {
        // Tabela legada: city (city_id, city, name, slug, state/uf, is_active, timestamps)
        $cities = DB::table('city')
            ->select('city_id', 'city', 'name', 'slug', 'state', 'is_active', 'created_at', 'updated_at')
            ->orderBy('city_id')
            ->paginate(15);

        // Flags úteis para a view
        $hasUf = DB::getSchemaBuilder()->hasColumn('city', 'state') || DB::getSchemaBuilder()->hasColumn('city', 'uf');

        return view('admin.cities.index', [
            'items' => $cities,
            'pk'    => 'city_id',
            'hasUf' => $hasUf,
        ]);
    }

    // GET /admin/cities/create
    public function create()
    {
        return view('admin.cities.create');
    }

    // POST /admin/cities
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'city'      => ['required', 'string', 'max:190'], // nome visível
            'uf'        => ['nullable', 'string', 'max:2'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $now    = Carbon::now();
        $name   = trim($data['city']); // preenche ambos
        $slug   = Str::slug($name) ?: null;
        $uf     = strtoupper($data['uf'] ?? '');
        $active = isset($data['is_active']) ? (int) !!$data['is_active'] : 1;

        // Coluna pode ser 'state' (mais comum) ou 'uf' (alguns dumps)
        $stateColumn = DB::getSchemaBuilder()->hasColumn('city', 'state') ? 'state'
                      : (DB::getSchemaBuilder()->hasColumn('city', 'uf') ? 'uf' : null);

        $insert = [
            'city'       => $name,
            'name'       => $name, // evita o erro "Field 'name' doesn't have a default value"
            'slug'       => $slug,
            'is_active'  => $active,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        if ($stateColumn) {
            $insert[$stateColumn] = $uf ?: null;
        }

        DB::table('city')->insert($insert);

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'Cidade criada com sucesso.');
    }

    // GET /admin/cities/{id}/edit
    public function edit($id)
    {
        $city = DB::table('city')->where('city_id', $id)->first();

        if (!$city) {
            abort(404, 'Cidade não encontrada');
        }

        return view('admin.cities.edit', ['city' => $city]);
    }

    // PUT/PATCH /admin/cities/{id}
    public function update(Request $request, $id): RedirectResponse
    {
        $data = $request->validate([
            'city'      => ['required', 'string', 'max:190'], // nome visível
            'uf'        => ['nullable', 'string', 'max:2'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $exists = DB::table('city')->where('city_id', $id)->exists();
        if (!$exists) {
            abort(404, 'Cidade não encontrada');
        }

        $now    = Carbon::now();
        $name   = trim($data['city']);
        $slug   = Str::slug($name) ?: null;
        $uf     = strtoupper($data['uf'] ?? '');
        $active = isset($data['is_active']) ? (int) !!$data['is_active'] : 1;

        $stateColumn = DB::getSchemaBuilder()->hasColumn('city', 'state') ? 'state'
                      : (DB::getSchemaBuilder()->hasColumn('city', 'uf') ? 'uf' : null);

        $update = [
            'city'       => $name,
            'name'       => $name, // mantém consistente
            'slug'       => $slug,
            'is_active'  => $active,
            'updated_at' => $now,
        ];

        if ($stateColumn) {
            $update[$stateColumn] = $uf ?: null;
        }

        DB::table('city')->where('city_id', $id)->update($update);

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'Cidade atualizada com sucesso.');
    }

    // DELETE /admin/cities/{id}
    public function destroy($id): RedirectResponse
    {
        // Evita excluir cidade com negócios vinculados
        $hasDeps = DB::table('business')->where('city', $id)->exists();
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
            return back()->with('error', 'Não foi possível excluir a cidade. Verifique vínculos e tente novamente.');
        }
    }
}
