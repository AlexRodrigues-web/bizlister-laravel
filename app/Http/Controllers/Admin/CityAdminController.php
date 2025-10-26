<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CityAdminController extends Controller
{
    // GET /admin/cities
    public function index()
    {
        // Tabela legada: city (city_id, city[, name, uf/state, slug, is_active, timestamps])
        $hasName   = Schema::hasColumn('city', 'name');
        $hasUf     = Schema::hasColumn('city', 'uf');
        $hasState  = Schema::hasColumn('city', 'state');

        $query = DB::table('city')->select('city_id', 'city');

        if ($hasName)   $query->addSelect('name');
        if ($hasUf)     $query->addSelect('uf');
        if ($hasState)  $query->addSelect('state');

        $items = $query->orderBy('city_id')->paginate(15);
        $pk    = 'city_id';
        // Para a view: se existir pelo menos uma das colunas de UF, mostramos a coluna
        $hasUfAny = $hasUf || $hasState;

        return view('admin.cities.index', [
            'items'  => $items,
            'pk'     => $pk,
            'hasUf'  => $hasUfAny,
        ]);
    }

    // GET /admin/cities/create
    public function create()
    {
        $hasUf     = Schema::hasColumn('city', 'uf');
        $hasState  = Schema::hasColumn('city', 'state');

        return view('admin.cities.create', [
            'hasUf' => ($hasUf || $hasState),
        ]);
    }

    // POST /admin/cities
    public function store(Request $request): RedirectResponse
    {
        $hasName     = Schema::hasColumn('city', 'name');
        $hasSlug     = Schema::hasColumn('city', 'slug');
        $hasActive   = Schema::hasColumn('city', 'is_active');
        $hasUf       = Schema::hasColumn('city', 'uf');
        $hasState    = Schema::hasColumn('city', 'state');
        $hasCreated  = Schema::hasColumn('city', 'created_at');
        $hasUpdated  = Schema::hasColumn('city', 'updated_at');

        $rules = [
            'city'       => ['required', 'string', 'max:190'], // nome visível
            'is_active'  => ['nullable', 'boolean'],
        ];
        // UF é opcional; valida se vier
        if ($hasUf || $hasState) {
            $rules['uf'] = ['nullable', 'string', 'size:2'];
        }

        $data   = $request->validate($rules);
        $name   = trim($data['city']);
        $slug   = Str::slug($name) ?: null;
        $active = isset($data['is_active']) ? (int) !!$data['is_active'] : 1;
        $ufVal  = strtoupper($data['uf'] ?? '');

        $insert = [
            'city' => $name,
        ];

        if ($hasName)   $insert['name']      = $name;      // evita NOT NULL em 'name'
        if ($hasSlug)   $insert['slug']      = $slug;
        if ($hasActive) $insert['is_active'] = $active;

        if ($hasUf)        $insert['uf']    = $ufVal ?: null;
        elseif ($hasState) $insert['state'] = $ufVal ?: null;

        $now = Carbon::now();
        if ($hasCreated) $insert['created_at'] = $now;
        if ($hasUpdated) $insert['updated_at'] = $now;

        DB::table('city')->insert($insert);

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'Cidade criada com sucesso.');
    }

    // GET /admin/cities/{id}/edit
    public function edit($id)
    {
        $hasUf     = Schema::hasColumn('city', 'uf');
        $hasState  = Schema::hasColumn('city', 'state');

        $city = DB::table('city')
            ->where('city_id', $id)
            ->first();

        abort_unless($city, 404, 'Cidade não encontrada');

        return view('admin.cities.edit', [
            'city'  => $city,
            'hasUf' => ($hasUf || $hasState),
        ]);
    }

    // PUT/PATCH /admin/cities/{id}
    public function update(Request $request, $id): RedirectResponse
    {
        $hasName     = Schema::hasColumn('city', 'name');
        $hasSlug     = Schema::hasColumn('city', 'slug');
        $hasActive   = Schema::hasColumn('city', 'is_active');
        $hasUf       = Schema::hasColumn('city', 'uf');
        $hasState    = Schema::hasColumn('city', 'state');
        $hasUpdated  = Schema::hasColumn('city', 'updated_at');

        $rules = [
            'city'       => ['required', 'string', 'max:190'],
            'is_active'  => ['nullable', 'boolean'],
        ];
        if ($hasUf || $hasState) {
            $rules['uf'] = ['nullable', 'string', 'size:2'];
        }

        $data = $request->validate($rules);

        $exists = DB::table('city')->where('city_id', $id)->exists();
        abort_unless($exists, 404, 'Cidade não encontrada');

        $name   = trim($data['city']);
        $slug   = Str::slug($name) ?: null;
        $active = isset($data['is_active']) ? (int) !!$data['is_active'] : 1;
        $ufVal  = strtoupper($data['uf'] ?? '');

        $update = [
            'city' => $name,
        ];

        if ($hasName)   $update['name']      = $name;   // mantém consistente
        if ($hasSlug)   $update['slug']      = $slug;
        if ($hasActive) $update['is_active'] = $active;

        if ($hasUf)        $update['uf']    = $ufVal ?: null;
        elseif ($hasState) $update['state'] = $ufVal ?: null;

        if ($hasUpdated)   $update['updated_at'] = Carbon::now();

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
