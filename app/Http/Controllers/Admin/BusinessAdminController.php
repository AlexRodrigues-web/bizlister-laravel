<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class BusinessAdminController extends Controller
{
    public function index(Request $request)
    {
        $q   = trim((string) $request->query('q', ''));
        $cid = $request->query('cid');
        $sid = $request->query('sid');

        $rows = DB::table('business as b')
            ->leftJoin('category as c', 'c.cat_id', '=', 'b.cid')
            ->leftJoin('city as ci',   'ci.city_id', '=', 'b.sid')
            ->select('b.*', 'c.category as category_name', 'ci.city as city_name')
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->where('b.business_name', 'like', "%$q%")
                      ->orWhere('b.description', 'like', "%$q%");
                });
            })
            ->when(!empty($cid), fn ($qb) => $qb->where('b.cid', $cid))
            ->when(!empty($sid), fn ($qb) => $qb->where('b.sid', $sid))
            ->orderBy('b.biz_id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $cats   = DB::table('category')->orderBy('category')->get();
        $cities = DB::table('city')->orderBy('city')->get();

        return view('admin.businesses.index', compact('rows', 'q', 'cid', 'sid', 'cats', 'cities'));
    }

    /**
     * Form de criação – reaproveita a view de edição com $row vazio.
     */
    public function create()
    {
        // $row vazio para a view detectar "modo criar"
        $row = (object)[];

        $cats   = DB::table('category')->orderBy('category')->get();
        $cities = DB::table('city')->orderBy('city')->get();

        return view('admin.businesses.edit', compact('row', 'cats', 'cities'));
    }

    /**
     * Salva um novo negócio (evita NULL em campos legados como 'menu').
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'cid'           => ['required', 'integer', 'exists:category,cat_id'],
            'sid'           => ['required', 'integer', 'exists:city,city_id'],
            'menu'          => ['nullable', 'integer'],
            'phone'         => ['nullable', 'string', 'max:50'],
            'address'       => ['nullable', 'string', 'max:255'],
            'status'        => ['nullable', 'string', 'max:50'],
            'image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Normalização – middleware ConvertEmptyStringsToNull joga "" => null.
        // Evitamos violar NOT NULL (menu, por ex) e mantemos legado feliz.
        if (Schema::hasColumn('business', 'menu')) {
            $data['menu'] = isset($data['menu']) ? (int)$data['menu'] : 0;
        }
        foreach (['phone','address','status'] as $f) {
            if (Schema::hasColumn('business', $f)) {
                $data[$f] = isset($data[$f]) ? (string)$data[$f] : '';
            }
        }

        // Preenche 'city' textual se a coluna existir no legado
        if (Schema::hasColumn('business', 'city')) {
            $cityRow = DB::table('city')
                ->where('city_id', $data['sid'])
                ->selectRaw("COALESCE(city, CONCAT('Cidade #', city_id)) AS label")
                ->first();
            if ($cityRow) {
                $data['city'] = $cityRow->label;
            }
        }

        // Upload simples (mantém compat com coluna 'image' caso exista)
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $ext     = strtolower($request->file('image')->getClientOriginalExtension() ?: 'jpg');
            $baseDir = 'businesses';
            $name    = Str::random(40).'.'.$ext;
            $request->file('image')->storeAs($baseDir, $name, 'public');
            if (Schema::hasColumn('business', 'image')) {
                $data['image'] = $baseDir.'/'.$name;
            }
        }

        // Insere e pega o ID (PK é biz_id)
        $id = DB::table('business')->insertGetId([
            'business_name' => $data['business_name'],
            'description'   => $data['description'] ?? null,
            'cid'           => (int)$data['cid'],
            'sid'           => (int)$data['sid'],
            // opcionais/legado
            'menu'          => $data['menu'] ?? 0,
            'phone'         => $data['phone'] ?? '',
            'address'       => $data['address'] ?? '',
            'status'        => $data['status'] ?? '',
            'city'          => $data['city'] ?? null,
            'image'         => $data['image'] ?? null,
        ], 'biz_id');

        return redirect()
            ->route('admin.businesses.edit', ['business' => $id])
            ->with('success', 'Negócio cadastrado com sucesso.');
    }

    public function edit(int $id)
    {
        $row = DB::table('business')->where('biz_id', $id)->first();
        abort_if(!$row, 404, 'Negócio não encontrado.');

        $cats   = DB::table('category')->orderBy('category')->get();
        $cities = DB::table('city')->orderBy('city')->get();

        return view('admin.businesses.edit', compact('row', 'cats', 'cities'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'cid'           => ['required', 'integer', 'exists:category,cat_id'],
            'sid'           => ['required', 'integer', 'exists:city,city_id'],
            'menu'          => ['nullable', 'integer'],
            'phone'         => ['nullable', 'string', 'max:50'],
            'address'       => ['nullable', 'string', 'max:255'],
            'status'        => ['nullable', 'string', 'max:50'],
            'image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if (Schema::hasColumn('business', 'menu')) {
            $data['menu'] = isset($data['menu']) ? (int)$data['menu'] : 0;
        }
        foreach (['phone','address','status'] as $f) {
            if (Schema::hasColumn('business', $f)) {
                $data[$f] = isset($data[$f]) ? (string)$data[$f] : '';
            }
        }

        $exists = DB::table('business')->where('biz_id', $id)->exists();
        abort_unless($exists, 404, 'Negócio não encontrado.');

        // Upload opcional durante edição
        if (!empty($data['image']) && $request->hasFile('image') && $request->file('image')->isValid()) {
            $ext     = strtolower($request->file('image')->getClientOriginalExtension() ?: 'jpg');
            $baseDir = 'businesses';
            $name    = Str::random(40).'.'.$ext;
            $request->file('image')->storeAs($baseDir, $name, 'public');
            if (Schema::hasColumn('business', 'image')) {
                $data['image'] = $baseDir.'/'.$name;
            } else {
                unset($data['image']);
            }
        } else {
            unset($data['image']); // não substituir se não enviar
        }

        DB::table('business')->where('biz_id', $id)->update($data);

        return redirect()
            ->route('admin.businesses.index')
            ->with('success', 'Negócio atualizado.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $row = DB::table('business')->where('biz_id', $id)->first();
        abort_if(!$row, 404, 'Negócio não encontrado.');

        DB::beginTransaction();
        try {
            $paths = array_filter([
                $row->image    ?? null,
                $row->image_lg ?? null,
                $row->image_sm ?? null,
            ]);

            foreach ($paths as $p) {
                if (is_string($p) && !preg_match('~^https?://~i', $p)) {
                    Storage::disk('public')->delete($p);
                }
            }

            $dir = "businesses/{$id}";
            if (Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->deleteDirectory($dir);
            }

            DB::table('business')->where('biz_id', $id)->delete();

            DB::commit();
            return redirect()
                ->route('admin.businesses.index')
                ->with('success', 'Negócio removido.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Falha ao remover o negócio. Tente novamente.');
        }
    }
}
