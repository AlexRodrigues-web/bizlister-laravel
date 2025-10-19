<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;              // upload
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image; // thumbs

class BusinessController extends Controller
{
    /**
     * Descobre dinamicamente tabela, coluna id e label de uma tabela simples.
     *
     * @return array{table:string|null,idCol:string|null,labelCol:string|null}
     */
    protected function detectSimpleTable(string $prefer, string $alt): array
    {
        $table = null;
        if (Schema::hasTable($prefer))      { $table = $prefer; }
        elseif (Schema::hasTable($alt))     { $table = $alt; }

        if (!$table) return ['table'=>null,'idCol'=>null,'labelCol'=>null];

        $cols = Schema::getColumnListing($table);

        // candidatos para id
        $idCandidates = ['city_id','sid','id'];
        $idCol = null;
        foreach ($idCandidates as $c) {
            if (in_array($c, $cols, true)) { $idCol = $c; break; }
        }

        // candidatos para label/nome
        $labelCandidates = ['city','name','title','label'];
        $labelCol = null;
        foreach ($labelCandidates as $c) {
            if (in_array($c, $cols, true)) { $labelCol = $c; break; }
        }

        return ['table'=>$table, 'idCol'=>$idCol, 'labelCol'=>$labelCol];
    }

    public function create()
    {
        // ==== Categorias ====
        $categories = collect();
        try {
            if (Schema::hasTable('category')) {
                $cols = Schema::getColumnListing('category');

                $labelCol = null;
                foreach (['cat_name','category','name','title','label'] as $c) {
                    if (in_array($c, $cols, true)) { $labelCol = $c; break; }
                }

                if ($labelCol && in_array('cat_id', $cols, true)) {
                    $categories = DB::table('category')
                        ->select('cat_id', DB::raw("$labelCol AS label"))
                        ->orderBy('label')
                        ->get();
                } elseif (in_array('cat_id', $cols, true)) {
                    $categories = DB::table('category')->select('cat_id')->get()
                        ->map(function ($r) { $r->label = 'Categoria #'.$r->cat_id; return $r; });
                }
            }
        } catch (\Throwable $e) {
            $categories = collect();
        }

        // ==== Cidades (dinâmico) ====
        $cities = collect();
        try {
            $meta = $this->detectSimpleTable('city','cities');
            if ($meta['table'] && $meta['idCol']) {
                $id   = $meta['idCol'];
                $name = $meta['labelCol']; // pode ser null

                if ($name) {
                    $cities = DB::table($meta['table'])
                        ->selectRaw("$id AS city_id, COALESCE($name, CONCAT('Cidade #', $id)) AS label")
                        ->orderBy('label')
                        ->get();
                } else {
                    $cities = DB::table($meta['table'])
                        ->selectRaw("$id AS city_id, CONCAT('Cidade #', $id) AS label")
                        ->orderBy('label')
                        ->get();
                }
            }
        } catch (\Throwable $e) {
            $cities = collect();
        }

        return view('business.create', compact('categories','cities'));
    }

    public function store(Request $request)
    {
        // A view envia: business_name, cid, sid, description, image
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'cid'           => 'required|integer',
            'sid'           => 'required|integer',
            'description'   => 'nullable|string',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'business_name' => $validated['business_name'],
            'cid'           => (int) $validated['cid'],
            'sid'           => (int) $validated['sid'],
            'description'   => $validated['description'] ?? null,
        ];

        // ===== Normalização: evita NULL em colunas NOT NULL quando o middleware zera "" => NULL =====
        foreach (["menu","phone","status","address"] as $f) {
            if (\Illuminate\Support\Facades\Schema::hasColumn("business", $f) && !array_key_exists($f, $data)) {
                $val = $request->input($f);
                if (is_null($val)) { $val = ""; } // força string vazia em vez de NULL
                $data[$f] = $val;
            }
        }
        // ==============================================================================================

        // Preenche 'city' textual se existir essa coluna no legado
        if (Schema::hasColumn('business', 'city')) {
            try {
                $meta = $this->detectSimpleTable('city','cities');
                if ($meta['table'] && $meta['idCol']) {
                    $id   = $meta['idCol'];
                    $name = $meta['labelCol'];
                    $row = DB::table($meta['table'])
                        ->where($id, $data['sid'])
                        ->selectRaw(
                            ($name ? "COALESCE($name, CONCAT('Cidade #', $id))" : "CONCAT('Cidade #', $id)")
                            . " AS label"
                        )
                        ->first();
                    if ($row) $data['city'] = $row->label;
                }
            } catch (\Throwable $e) {
                // silencioso
            }
        }

        // Campos extras comuns no legado
        foreach (['menu','status','phone','address'] as $extra) {
            if (Schema::hasColumn('business', $extra) && !array_key_exists($extra, $data)) {
                $data[$extra] = null;
            }
        }

        // ============ Upload + Thumbs (1200x800 e 360x240, WEBP) ============
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $baseDir  = 'businesses';
            $basename = Str::random(40);

            // extensão do original
            $ext = strtolower($request->file('image')->getClientOriginalExtension() ?: 'jpg');

            // 1) Original (mantém compat com colunas 'image' quando existem)
            $origRel = $baseDir.'/'.$basename.'.'.$ext;
            $request->file('image')->storeAs($baseDir, $basename.'.'.$ext, 'public');
            if (Schema::hasColumn('business', 'image')) {
                $data['image'] = $origRel;
            }

            try {
                $srcPath = $request->file('image')->getRealPath();

                // 2) LG 1200x800 (webp)
                $imgLg = Image::make($srcPath)
                    ->orientate()
                    ->fit(1200, 800, function($c){ $c->upsize(); });
                $lgRel = $baseDir.'/'.$basename.'_lg.webp';
                Storage::disk('public')->put($lgRel, (string) $imgLg->encode('webp', 85));

                // 3) SM 360x240 (webp)
                $imgSm = Image::make($srcPath)
                    ->orientate()
                    ->fit(360, 240, function($c){ $c->upsize(); });
                $smRel = $baseDir.'/'.$basename.'_sm.webp';
                Storage::disk('public')->put($smRel, (string) $imgSm->encode('webp', 85));

                // Persistência — usa novas colunas se existirem, senão mantém legado
                if (Schema::hasColumn('business', 'image_path_lg')) { $data['image_path_lg'] = $lgRel; }
                if (Schema::hasColumn('business', 'image_path_sm')) { $data['image_path_sm'] = $smRel; }
                if (Schema::hasColumn('business', 'image_lg'))      { $data['image_lg']      = $lgRel; }
                if (Schema::hasColumn('business', 'image_sm'))      { $data['image_sm']      = $smRel; }

            } catch (\Throwable $e) {
                // Se der erro na geração de thumbs, seguimos apenas com o original
                // (opcional: logar o erro)
            }
        }
        // ====================================================================

        $biz = Business::create($data);

        return redirect()
            ->route('business.show', [$biz->biz_id, Str::slug($biz->business_name)])
            ->with('success','Negócio cadastrado com sucesso.');
    }

    public function show(int $id, ?string $slug = null)
    {
        $biz = Business::findOrFail($id);

        // rótulos bonitos (opcional)
        $category = null;
        try {
            if (Schema::hasTable('category')) {
                $cols = Schema::getColumnListing('category');
                $labelCol = null;
                foreach (['category','cat_name','name','title','label'] as $c) {
                    if (in_array($c, $cols, true)) { $labelCol = $c; break; }
                }
                if ($labelCol) {
                    $category = DB::table('category')
                        ->where('cat_id', $biz->cid)
                        ->selectRaw("cat_id, $labelCol AS category")
                        ->first();
                }
            }
        } catch (\Throwable $e) {}

        $city = null;
        try {
            $meta = $this->detectSimpleTable('city','cities');
            if ($meta['table'] && $meta['idCol']) {
                $idCol   = $meta['idCol'];
                $nameCol = $meta['labelCol'];
                $city = DB::table($meta['table'])
                    ->where($idCol, $biz->sid)
                    ->selectRaw($idCol." AS city_id, ".
                        ($nameCol ? "COALESCE($nameCol, CONCAT('Cidade #', $idCol))" : "CONCAT('Cidade #', $idCol)")
                        ." AS city")
                    ->first();
            }
        } catch (\Throwable $e) {}

        return view('business.show', compact('biz','category','city'));
    }
}
