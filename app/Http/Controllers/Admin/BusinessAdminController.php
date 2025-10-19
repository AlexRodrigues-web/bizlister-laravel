<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;

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
        ]);

        if (!isset($data['menu'])) {
            $data['menu'] = 0; // default legado
        }

        $exists = DB::table('business')->where('biz_id', $id)->exists();
        abort_unless($exists, 404, 'Negócio não encontrado.');

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
            // Apaga arquivos (colunas guardam caminhos relativos ao disk 'public')
            $paths = array_filter([
                $row->image    ?? null,
                $row->image_lg ?? null,
                $row->image_sm ?? null,
            ]);

            foreach ($paths as $p) {
                // Evita tentar deletar URLs absolutas por engano
                if (is_string($p) && !preg_match('~^https?://~i', $p)) {
                    Storage::disk('public')->delete($p);
                }
            }

            // Apaga a pasta /businesses/{biz_id} (se seguir o padrão do backfill/upload)
            $dir = "businesses/{$id}";
            if (Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->deleteDirectory($dir);
            }

            // Remove o registro
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
