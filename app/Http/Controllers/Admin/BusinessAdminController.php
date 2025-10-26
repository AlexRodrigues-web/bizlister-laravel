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

        // === Listagem com nomes de categoria/cidade ===
        $rows = DB::table('business as b')
            ->leftJoin('categories as c', 'c.cat_id', '=', 'b.cid')
            ->leftJoin('city as ci',        'ci.city_id', '=', 'b.sid')
            ->select(
                'b.*',
                DB::raw('c.category as category_name'),
                DB::raw('ci.city as city_name')
            )
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

        // === Filtros (views esperam $cats e $cities) ===
        $cats = DB::table('categories')
            ->select('cat_id', 'category')
            ->orderBy('category')
            ->get();

        $cities = DB::table('city')
            ->select('city_id', 'city')
            ->orderBy('city')
            ->get();

        return view('admin.businesses.index', compact('rows', 'q', 'cid', 'sid', 'cats', 'cities'));
    }

    /** Form de criação — views esperam $cats, $cities e (às vezes) $item */
    public function create()
    {
        $item = (object)[]; // mantém a view feliz

        $cats = DB::table('categories')
            ->select('cat_id', 'category')
            ->orderBy('category')
            ->get();

        $cities = DB::table('city')
            ->select('city_id', 'city')
            ->orderBy('city')
            ->get();

        // Use a view admin/businesses/create.blade.php
        return view('admin.businesses.create', compact('item', 'cats', 'cities'));
    }

    /** Salvar novo — aceita 'sid' ou 'city_id' vindos da view */
    public function store(Request $request): RedirectResponse
    {
        $sid = $request->input('sid', $request->input('city_id'));

        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'cid'           => ['required', 'integer', 'exists:categories,cat_id'], // <-- categories
            'menu'          => ['nullable', 'integer'],
            'phone'         => ['nullable', 'string', 'max:50'],
            'address'       => ['nullable', 'string', 'max:255'],
            'status'        => ['nullable', 'string', 'max:50'],
            'image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // valida cidade
        if (!is_numeric($sid) || !DB::table('city')->where('city_id', (int)$sid)->exists()) {
            return back()->withErrors(['sid' => 'Cidade inválida.'])->withInput();
        }

        $cols = Schema::getColumnListing('business');
        $has  = fn(string $c) => in_array($c, $cols, true);

        // normalizações
        if ($has('menu'))   { $data['menu']   = isset($data['menu']) ? (int)$data['menu'] : 0; }
        if ($has('phone'))  { $data['phone']  = $data['phone']  ?? ''; }
        if ($has('status')) { $data['status'] = $data['status'] ?? ''; }
        if ($has('address')){ $data['address']= $data['address']?? ''; }

        // cidade (ID)
        $data['sid'] = (int)$sid;
        if ($has('city')) { $data['city'] = (int)$sid; } // legado

        // upload opcional
        if ($request->hasFile('image') && $request->file('image')->isValid() && $has('image')) {
            $ext     = strtolower($request->file('image')->getClientOriginalExtension() ?: 'jpg');
            $baseDir = 'businesses';
            $name    = Str::random(40).'.'.$ext;
            $request->file('image')->storeAs($baseDir, $name, 'public');
            $data['image'] = $baseDir.'/'.$name;
        }

        // insert só com colunas existentes
        $insert = [];
        if ($has('business_name')) $insert['business_name'] = $data['business_name'];
        if ($has('description'))   $insert['description']   = $data['description'] ?? null;
        if ($has('cid'))           $insert['cid']           = (int)$data['cid'];
        if ($has('sid'))           $insert['sid']           = (int)$data['sid'];
        if ($has('menu'))          $insert['menu']          = $data['menu'] ?? 0;
        if ($has('phone'))         $insert['phone']         = $data['phone'] ?? '';
        if ($has('address'))       $insert['address']       = $data['address'] ?? '';
        if ($has('status'))        $insert['status']        = $data['status'] ?? '';
        if ($has('city'))          $insert['city']          = $data['city'] ?? null;
        if ($has('image'))         $insert['image']         = $data['image'] ?? null;
        if ($has('created_at') && !isset($insert['created_at'])) $insert['created_at'] = now();
        if ($has('updated_at') && !isset($insert['updated_at'])) $insert['updated_at'] = now();

        $id = DB::table('business')->insertGetId($insert, 'biz_id');

        return redirect()
            ->route('admin.businesses.edit', ['business' => $id])
            ->with('success', 'Negócio cadastrado com sucesso.');
    }

    /** Editar — passa nomes padronizados para a view */
    public function edit(int $id)
    {
        $item = DB::table('business')->where('biz_id', $id)->first();
        abort_if(!$item, 404, 'Negócio não encontrado.');

        $cats = DB::table('categories')
            ->select('cat_id', 'category')
            ->orderBy('category')
            ->get();

        $cities = DB::table('city')
            ->select('city_id', 'city')
            ->orderBy('city')
            ->get();

        return view('admin.businesses.edit', compact('item', 'cats', 'cities'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $sid = $request->input('sid', $request->input('city_id'));

        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'cid'           => ['required', 'integer', 'exists:categories,cat_id'], // <-- categories
            'menu'          => ['nullable', 'integer'],
            'phone'         => ['nullable', 'string', 'max:50'],
            'address'       => ['nullable', 'string', 'max:255'],
            'status'        => ['nullable', 'string', 'max:50'],
            'image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if (!is_numeric($sid) || !DB::table('city')->where('city_id', (int)$sid)->exists()) {
            return back()->withErrors(['sid' => 'Cidade inválida.'])->withInput();
        }

        $exists = DB::table('business')->where('biz_id', $id)->exists();
        abort_unless($exists, 404, 'Negócio não encontrado.');

        $cols = Schema::getColumnListing('business');
        $has  = fn(string $c) => in_array($c, $cols, true);

        // normalizações
        if ($has('menu'))   { $data['menu']   = isset($data['menu']) ? (int)$data['menu'] : 0; }
        if ($has('phone'))  { $data['phone']  = $data['phone']  ?? ''; }
        if ($has('status')) { $data['status'] = $data['status'] ?? ''; }
        if ($has('address')){ $data['address']= $data['address']?? ''; }

        $data['sid'] = (int)$sid;
        if ($has('city')) { $data['city'] = (int)$sid; }

        // upload
        if ($request->hasFile('image') && $request->file('image')->isValid() && $has('image')) {
            $ext     = strtolower($request->file('image')->getClientOriginalExtension() ?: 'jpg');
            $baseDir = 'businesses';
            $name    = Str::random(40).'.'.$ext;
            $request->file('image')->storeAs($baseDir, $name, 'public');
            $data['image'] = $baseDir.'/'.$name;
        } else {
            unset($data['image']);
        }

        $update = [];
        if ($has('business_name')) $update['business_name'] = $data['business_name'];
        if ($has('description'))   $update['description']   = $data['description'] ?? null;
        if ($has('cid'))           $update['cid']           = (int)$data['cid'];
        if ($has('sid'))           $update['sid']           = (int)$data['sid'];
        if ($has('menu'))          $update['menu']          = $data['menu'] ?? 0;
        if ($has('phone'))         $update['phone']         = $data['phone'] ?? '';
        if ($has('address'))       $update['address']       = $data['address'] ?? '';
        if ($has('status'))        $update['status']        = $data['status'] ?? '';
        if ($has('city'))          $update['city']          = $data['city'] ?? null;
        if ($has('image') && isset($data['image'])) $update['image'] = $data['image'];
        if ($has('updated_at'))    $update['updated_at']    = now();

        DB::table('business')->where('biz_id', $id)->update($update);

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
