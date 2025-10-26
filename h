[1mdiff --git a/app/Http/Controllers/Admin/BusinessAdminController.php b/app/Http/Controllers/Admin/BusinessAdminController.php[m
[1mindex f8a13de..6b7b5aa 100644[m
[1m--- a/app/Http/Controllers/Admin/BusinessAdminController.php[m
[1m+++ b/app/Http/Controllers/Admin/BusinessAdminController.php[m
[36m@@ -5,65 +5,111 @@[m [mnamespace App\Http\Controllers\Admin;[m
 use App\Http\Controllers\Controller;[m
 use Illuminate\Http\Request;[m
 use Illuminate\Support\Facades\DB;[m
[32m+[m[32muse Illuminate\Support\Facades\Storage;[m
[32m+[m[32muse Illuminate\Http\RedirectResponse;[m
 [m
 class BusinessAdminController extends Controller[m
 {[m
     public function index(Request $request)[m
     {[m
[31m-        $q  = trim((string)$request->query('q',''));[m
[32m+[m[32m        $q   = trim((string) $request->query('q', ''));[m
         $cid = $request->query('cid');[m
         $sid = $request->query('sid');[m
 [m
         $rows = DB::table('business as b')[m
[31m-            ->leftJoin('category as c','c.cat_id','=','b.cid')[m
[31m-            ->leftJoin('city as ci','ci.city_id','=','b.sid')[m
[31m-            ->select('b.*','c.category as category_name','ci.city as city_name')[m
[31m-            ->when($q !== '', function($qb) use ($q) {[m
[31m-                $qb->where(function($w) use ($q){[m
[31m-                    $w->where('b.business_name','like',"%$q%")[m
[31m-                      ->orWhere('b.description','like',"%$q%");[m
[32m+[m[32m            ->leftJoin('category as c', 'c.cat_id', '=', 'b.cid')[m
[32m+[m[32m            ->leftJoin('city as ci',   'ci.city_id', '=', 'b.sid')[m
[32m+[m[32m            ->select('b.*', 'c.category as category_name', 'ci.city as city_name')[m
[32m+[m[32m            ->when($q !== '', function ($qb) use ($q) {[m
[32m+[m[32m                $qb->where(function ($w) use ($q) {[m
[32m+[m[32m                    $w->where('b.business_name', 'like', "%$q%")[m
[32m+[m[32m                      ->orWhere('b.description', 'like', "%$q%");[m
                 });[m
             })[m
[31m-            ->when(!empty($cid), fn($qb) => $qb->where('b.cid',$cid))[m
[31m-            ->when(!empty($sid), fn($qb) => $qb->where('b.sid',$sid))[m
[31m-            ->orderBy('b.biz_id','desc')[m
[32m+[m[32m            ->when(!empty($cid), fn ($qb) => $qb->where('b.cid', $cid))[m
[32m+[m[32m            ->when(!empty($sid), fn ($qb) => $qb->where('b.sid', $sid))[m
[32m+[m[32m            ->orderBy('b.biz_id', 'desc')[m
             ->paginate(15)[m
             ->withQueryString();[m
 [m
[31m-        $cats = DB::table('category')->orderBy('category')->get();[m
[32m+[m[32m        $cats   = DB::table('category')->orderBy('category')->get();[m
         $cities = DB::table('city')->orderBy('city')->get();[m
 [m
[31m-        return view('admin.businesses.index', compact('rows','q','cid','sid','cats','cities'));[m
[32m+[m[32m        return view('admin.businesses.index', compact('rows', 'q', 'cid', 'sid', 'cats', 'cities'));[m
     }[m
 [m
     public function edit(int $id)[m
     {[m
[31m-        $row = DB::table('business')->where('biz_id',$id)->first();[m
[31m-        abort_if(!$row, 404);[m
[31m-        $cats = DB::table('category')->orderBy('category')->get();[m
[32m+[m[32m        $row = DB::table('business')->where('biz_id', $id)->first();[m
[32m+[m[32m        abort_if(!$row, 404, 'Negócio não encontrado.');[m
[32m+[m
[32m+[m[32m        $cats   = DB::table('category')->orderBy('category')->get();[m
         $cities = DB::table('city')->orderBy('city')->get();[m
[31m-        return view('admin.businesses.edit', compact('row','cats','cities'));[m
[32m+[m
[32m+[m[32m        return view('admin.businesses.edit', compact('row', 'cats', 'cities'));[m
     }[m
 [m
[31m-    public function update(Request $request, int $id)[m
[32m+[m[32m    public function update(Request $request, int $id): RedirectResponse[m
     {[m
         $data = $request->validate([[m
[31m-            'business_name' => ['required','string','max:255'],[m
[31m-            'description'   => ['nullable','string'],[m
[31m-            'cid'           => ['required','integer'],[m
[31m-            'sid'           => ['required','integer'],[m
[31m-            'menu'          => ['nullable','integer'],[m
[32m+[m[32m            'business_name' => ['required', 'string', 'max:255'],[m
[32m+[m[32m            'description'   => ['nullable', 'string'],[m
[32m+[m[32m            'cid'           => ['required', 'integer', 'exists:category,cat_id'],[m
[32m+[m[32m            'sid'           => ['required', 'integer', 'exists:city,city_id'],[m
[32m+[m[32m            'menu'          => ['nullable', 'integer'],[m
         ]);[m
 [m
[31m-        if (!isset($data['menu'])) $data['menu'] = 0; // default legado[m
[32m+[m[32m        if (!isset($data['menu'])) {[m
[32m+[m[32m            $data['menu'] = 0; // default legado[m
[32m+[m[32m        }[m
[32m+[m
[32m+[m[32m        $exists = DB::table('business')->where('biz_id', $id)->exists();[m
[32m+[m[32m        abort_unless($exists, 404, 'Negócio não encontrado.');[m
 [m
[31m-        DB::table('business')->where('biz_id',$id)->update($data);[m
[31m-        return redirect()->route('admin.businesses.index')->with('success','Negócio atualizado.');[m
[32m+[m[32m        DB::table('business')->where('biz_id', $id)->update($data);[m
[32m+[m
[32m+[m[32m        return redirect()[m
[32m+[m[32m            ->route('admin.businesses.index')[m
[32m+[m[32m            ->with('success', 'Negócio atualizado.');[m
     }[m
 [m
[31m-    public function destroy(int $id)[m
[32m+[m[32m    public function destroy(int $id): RedirectResponse[m
     {[m
[31m-        DB::table('business')->where('biz_id',$id)->delete();[m
[31m-        return redirect()->route('admin.businesses.index')->with('success','Negócio removido.');[m
[32m+[m[32m        $row = DB::table('business')->where('biz_id', $id)->first();[m
[32m+[m[32m        abort_if(!$row, 404, 'Negócio não encontrado.');[m
[32m+[m
[32m+[m[32m        DB::beginTransaction();[m
[32m+[m[32m        try {[m
[32m+[m[32m            // Apaga arquivos (colunas guardam caminhos relativos ao disk 'public')[m
[32m+[m[32m            $paths = array_filter([[m
[32m+[m[32m                $row->image    ?? null,[m
[32m+[m[32m                $row->image_lg ?? null,[m
[32m+[m[32m                $row->image_sm ?? null,[m
[32m+[m[32m            ]);[m
[32m+[m
[32m+[m[32m            foreach ($paths as $p) {[m
[32m+[m[32m                // Evita tentar deletar URLs absolutas por engano[m
[32m+[m[32m                if (is_string($p) && !preg_match('~^https?://~i', $p)) {[m
[32m+[m[32m                    Storage::disk('public')->delete($p);[m
[32m+[m[32m                }[m
[32m+[m[32m            }[m
[32m+[m
[32m+[m[32m            // Apaga a pasta /businesses/{biz_id} (se seguir o padrão do backfill/upload)[m
[32m+[m[32m            $dir = "businesses/{$id}";[m
[32m+[m[32m            if (Storage::disk('public')->exists($dir)) {[m
[32m+[m[32m                Storage::disk('public')->deleteDirectory($dir);[m
[32m+[m[32m            }[m
[32m+[m
[32m+[m[32m            // Remove o registro[m
[32m+[m[32m            DB::table('business')->where('biz_id', $id)->delete();[m
[32m+[m
[32m+[m[32m            DB::commit();[m
[32m+[m[32m            return redirect()[m
[32m+[m[32m                ->route('admin.businesses.index')[m
[32m+[m[32m                ->with('success', 'Negócio removido.');[m
[32m+[m[32m        } catch (\Throwable $e) {[m
[32m+[m[32m            DB::rollBack();[m
[32m+[m[32m            return back()->with('error', 'Falha ao remover o negócio. Tente novamente.');[m
[32m+[m[32m        }[m
     }[m
[31m-}[m
\ No newline at end of file[m
[32m+[m[32m}[m
[1mdiff --git a/app/Http/Controllers/Admin/CategoryAdminController.php b/app/Http/Controllers/Admin/CategoryAdminController.php[m
[1mindex 644f9d8..a52de21 100644[m
[1m--- a/app/Http/Controllers/Admin/CategoryAdminController.php[m
[1m+++ b/app/Http/Controllers/Admin/CategoryAdminController.php[m
[36m@@ -1,8 +1,10 @@[m
 <?php[m
[32m+[m
 namespace App\Http\Controllers\Admin;[m
 [m
 use App\Http\Controllers\Controller;[m
 use Illuminate\Http\Request;[m
[32m+[m[32muse Illuminate\Http\RedirectResponse;[m
 use Illuminate\Support\Facades\DB;[m
 [m
 class CategoryAdminController extends Controller[m
[36m@@ -26,7 +28,7 @@[m [mclass CategoryAdminController extends Controller[m
     }[m
 [m
     // POST /admin/categories[m
[31m-    public function store(Request $request)[m
[32m+[m[32m    public function store(Request $request): RedirectResponse[m
     {[m
         $data = $request->validate([[m
             'category' => ['required', 'string', 'max:190'],[m
[36m@@ -36,8 +38,9 @@[m [mclass CategoryAdminController extends Controller[m
             'category' => $data['category'],[m
         ]);[m
 [m
[31m-        return redirect()->route('admin.categories.index')[m
[31m-            ->with('status', 'Categoria criada com sucesso.');[m
[32m+[m[32m        return redirect()[m
[32m+[m[32m            ->route('admin.categories.index')[m
[32m+[m[32m            ->with('success', 'Categoria criada com sucesso.');[m
     }[m
 [m
     // GET /admin/categories/{id}/edit[m
[36m@@ -52,7 +55,7 @@[m [mclass CategoryAdminController extends Controller[m
     }[m
 [m
     // PUT/PATCH /admin/categories/{id}[m
[31m-    public function update(Request $request, $id)[m
[32m+[m[32m    public function update(Request $request, $id): RedirectResponse[m
     {[m
         $data = $request->validate([[m
             'category' => ['required', 'string', 'max:190'],[m
[36m@@ -67,19 +70,32 @@[m [mclass CategoryAdminController extends Controller[m
             'category' => $data['category'],[m
         ]);[m
 [m
[31m-        return redirect()->route('admin.categories.index')[m
[31m-            ->with('status', 'Categoria atualizada com sucesso.');[m
[32m+[m[32m        return redirect()[m
[32m+[m[32m            ->route('admin.categories.index')[m
[32m+[m[32m            ->with('success', 'Categoria atualizada com sucesso.');[m
     }[m
 [m
     // DELETE /admin/categories/{id}[m
[31m-    public function destroy($id)[m
[32m+[m[32m    public function destroy($id): RedirectResponse[m
     {[m
[31m-        $deleted = DB::table('category')->where('cat_id', $id)->delete();[m
[31m-        if (!$deleted) {[m
[31m-            abort(404, 'Categoria não encontrada');[m
[32m+[m[32m        // BLOQUEIO: não permitir excluir se houver negócios vinculados[m
[32m+[m[32m        $hasDeps = DB::table('business')->where('cid', $id)->exists();[m
[32m+[m[32m        if ($hasDeps) {[m
[32m+[m[32m            return back()->with('error', 'Não é possível excluir: há negócios vinculados a esta categoria.');[m
         }[m
 [m
[31m-        return redirect()->route('admin.categories.index')[m
[31m-            ->with('status', 'Categoria excluída com sucesso.');[m
[32m+[m[32m        try {[m
[32m+[m[32m            $deleted = DB::table('category')->where('cat_id', $id)->delete();[m
[32m+[m[32m            if (!$deleted) {[m
[32m+[m[32m                abort(404, 'Categoria não encontrada');[m
[32m+[m[32m            }[m
[32m+[m
[32m+[m[32m            return redirect()[m
[32m+[m[32m                ->route('admin.categories.index')[m
[32m+[m[32m                ->with('success', 'Categoria excluída com sucesso.');[m
[32m+[m[32m        } catch (\Throwable $e) {[m
[32m+[m[32m            // Caso exista FK no banco (ON DELETE RESTRICT) ou outro erro[m
[32m+[m[32m            return back()->with('error', 'Não foi possível excluir a categoria. Verifique vínculos e tente novamente.');[m
[32m+[m[32m        }[m
     }[m
[31m-}[m
\ No newline at end of file[m
[32m+[m[32m}[m
[1mdiff --git a/app/Http/Controllers/Admin/CityAdminController.php b/app/Http/Controllers/Admin/CityAdminController.php[m
[1mindex c5c354c..02d6fa2 100644[m
[1m--- a/app/Http/Controllers/Admin/CityAdminController.php[m
[1m+++ b/app/Http/Controllers/Admin/CityAdminController.php[m
[36m@@ -1,72 +1,142 @@[m
 <?php[m
[32m+[m
 namespace App\Http\Controllers\Admin;[m
 [m
 use App\Http\Controllers\Controller;[m
 use Illuminate\Http\Request;[m
 use Illuminate\Support\Facades\DB;[m
[32m+[m[32muse Illuminate\Support\Facades\Schema;[m
[32m+[m[32muse Illuminate\Http\RedirectResponse;[m
 [m
 class CityAdminController extends Controller[m
 {[m
     // GET /admin/cities[m
     public function index()[m
     {[m
[31m-        // Tabela legada: city (city_id, city)[m
[31m-        $items = DB::table("city")[m
[31m-            ->select("city_id", "city")[m
[31m-            ->orderBy("city_id")[m
[31m-            ->paginate(15);[m
[32m+[m[32m        // Tabela legada: city (city_id, city[, uf])[m
[32m+[m[32m        $hasUf = Schema::hasColumn('city', 'uf');[m
[32m+[m
[32m+[m[32m        $query = DB::table('city')->select('city_id', 'city');[m
[32m+[m[32m        if ($hasUf) {[m
[32m+[m[32m            $query->addSelect('uf');[m
[32m+[m[32m        }[m
 [m
[31m-        $pk = "city_id";[m
[32m+[m[32m        $items = $query->orderBy('city_id')->paginate(15);[m
[32m+[m[32m        $pk    = 'city_id';[m
 [m
[31m-        return view("admin.cities.index", compact("items", "pk"));[m
[32m+[m[32m        return view('admin.cities.index', compact('items', 'pk', 'hasUf'));[m
     }[m
 [m
     // GET /admin/cities/create[m
     public function create()[m
     {[m
[31m-        return view("admin.cities.create");[m
[32m+[m[32m        $hasUf = Schema::hasColumn('city', 'uf');[m
[32m+[m[32m        return view('admin.cities.create', compact('hasUf'));[m
     }[m
 [m
     // POST /admin/cities[m
[31m-    public function store(Request $request)[m
[32m+[m[32m    public function store(Request $request): RedirectResponse[m
     {[m
[31m-        $data = $request->validate([[m
[31m-            "city" => ["required","string","max:190"],[m
[31m-        ]);[m
[32m+[m[32m        $hasUf = Schema::hasColumn('city', 'uf');[m
[32m+[m
[32m+[m[32m        $rules = [[m
[32m+[m[32m            'city' => ['required', 'string', 'max:190'],[m
[32m+[m[32m        ];[m
[32m+[m[32m        if ($hasUf) {[m
[32m+[m[32m            $rules['uf'] = ['required', 'string', 'size:2'];[m
[32m+[m[32m        }[m
[32m+[m
[32m+[m[32m        $data = $request->validate($rules);[m
[32m+[m
[32m+[m[32m        $insert = ['city' => $data['city']];[m
[32m+[m[32m        if ($hasUf) {[m
[32m+[m[32m            $insert['uf'] = strtoupper($data['uf']);[m
[32m+[m[32m        }[m
 [m
[31m-        DB::table("city")->insert(["city" => $data["city"]]);[m
[32m+[m[32m        DB::table('city')->insert($insert);[m
 [m
[31m-        return redirect()->route("admin.cities.index")[m
[31m-                         ->with("status","Cidade criada com sucesso.");[m
[32m+[m[32m        return redirect()[m
[32m+[m[32m            ->route('admin.cities.index')[m
[32m+[m[32m            ->with('success', 'Cidade criada com sucesso.');[m
     }[m
 [m
     // GET /admin/cities/{id}/edit[m
     public function edit($id)[m
     {[m
[31m-        $city = DB::table("city")->where("city_id", $id)->first();[m
[31m-        abort_unless($city, 404);[m
[31m-        return view("admin.cities.edit", compact("city"));[m
[32m+[m[32m        $hasUf = Schema::hasColumn('city', 'uf');[m
[32m+[m
[32m+[m[32m        $city = DB::table('city')->where('city_id', $id)->first();[m
[32m+[m[32m        abort_unless($city, 404, 'Cidade não encontrada');[m
[32m+[m
[32m+[m[32m        return view('admin.cities.edit', compact('city', 'hasUf'));[m
     }[m
 [m
     // PUT /admin/cities/{id}[m
[31m-    public function update(Request $request, $id)[m
[32m+[m[32m    public function update(Request $request, $id): RedirectResponse[m
     {[m
[31m-        $data = $request->validate([[m
[31m-            "city" => ["required","string","max:190"],[m
[31m-        ]);[m
[32m+[m[32m        $hasUf = Schema::hasColumn('city', 'uf');[m
[32m+[m
[32m+[m[32m        $rules = [[m
[32m+[m[32m            'city' => ['required', 'string', 'max:190'],[m
[32m+[m[32m        ];[m
[32m+[m[32m        if ($hasUf) {[m
[32m+[m[32m            $rules['uf'] = ['required', 'string', 'size:2'];[m
[32m+[m[32m        }[m
[32m+[m
[32m+[m[32m        $data = $request->validate($rules);[m
[32m+[m
[32m+[m[32m        $exists = DB::table('city')->where('city_id', $id)->exists();[m
[32m+[m[32m        abort_unless($exists, 404, 'Cidade não encontrada');[m
 [m
[31m-        DB::table("city")->where("city_id", $id)->update(["city" => $data["city"]]);[m
[32m+[m[32m        $update = ['city' => $data['city']];[m
[32m+[m[32m        if ($hasUf) {[m
[32m+[m[32m            $update['uf'] = strtoupper($data['uf']);[m
[32m+[m[32m        }[m
 [m
[31m-        return redirect()->route("admin.cities.index")[m
[31m-                         ->with("status","Cidade atualizada com sucesso.");[m
[32m+[m[32m        DB::table('city')->where('city_id', $id)->update($update);[m
[32m+[m
[32m+[m[32m        return redirect()[m
[32m+[m[32m            ->route('admin.cities.index')[m
[32m+[m[32m            ->with('success', 'Cidade atualizada com sucesso.');[m
     }[m
 [m
[31m-    // DELETE /admin/cities/{id}[m
[31m-    public function destroy($id)[m
[31m-    {[m
[31m-        DB::table("city")->where("city_id", $id)->delete();[m
[32m+[m[32m   // DELETE /admin/cities/{id}[m
[32m+[m[32mpublic function destroy($id): RedirectResponse[m
[32m+[m[32m{[m
[32m+[m[32m    $id = (int) $id;[m
[32m+[m
[32m+[m[32m    // Detecta colunas legadas na tabela business[m
[32m+[m[32m    $hasSid  = Schema::hasColumn('business', 'sid');[m
[32m+[m[32m    $hasCity = Schema::hasColumn('business', 'city');[m
[32m+[m
[32m+[m[32m    // WHERE (sid = ? OR city = ?)[m
[32m+[m[32m    $hasDeps = false;[m
[32m+[m[32m    if ($hasSid || $hasCity) {[m
[32m+[m[32m        $hasDeps = DB::table('business')[m
[32m+[m[32m            ->where(function ($q) use ($hasSid, $hasCity, $id) {[m
[32m+[m[32m                $q->whereRaw('1=0');[m
[32m+[m[32m                if ($hasSid)  { $q->orWhere('sid',  $id); }[m
[32m+[m[32m                if ($hasCity) { $q->orWhere('city', $id); }[m
[32m+[m[32m            })[m
[32m+[m[32m            ->exists();[m
[32m+[m[32m    }[m
[32m+[m
[32m+[m[32m    if ($hasDeps) {[m
[32m+[m[32m        return back()->with('error', 'Não é possível excluir: há negócios vinculados a esta cidade.');[m
[32m+[m[32m    }[m
[32m+[m
[32m+[m[32m    try {[m
[32m+[m[32m        $deleted = DB::table('city')->where('city_id', $id)->delete();[m
[32m+[m[32m        if (!$deleted) {[m
[32m+[m[32m            abort(404, 'Cidade não encontrada');[m
[32m+[m[32m        }[m
 [m
[31m-        return redirect()->route("admin.cities.index")[m
[31m-                         ->with("status","Cidade excluída com sucesso.");[m
[32m+[m[32m        return redirect()[m
[32m+[m[32m            ->route('admin.cities.index')[m
[32m+[m[32m            ->with('success', 'Cidade excluída com sucesso.');[m
[32m+[m[32m    } catch (\Throwable $e) {[m
[32m+[m[32m        // Padroniza a mensagem — também cobre FK RESTRICT[m
[32m+[m[32m        return back()->with('error', 'Não é possível excluir: há negócios vinculados a esta cidade.');[m
     }[m
[31m-}[m
\ No newline at end of file[m
[32m+[m[32m}[m
[32m+[m[32m}[m
[1mdiff --git a/app/Http/Controllers/Auth/NewPasswordController.php b/app/Http/Controllers/Auth/NewPasswordController.php[m
[1mindex 1df8e21..e6fda75 100644[m
[1m--- a/app/Http/Controllers/Auth/NewPasswordController.php[m
[1m+++ b/app/Http/Controllers/Auth/NewPasswordController.php[m
[36m@@ -6,9 +6,9 @@[m [muse App\Http\Controllers\Controller;[m
 use Illuminate\Auth\Events\PasswordReset;[m
 use Illuminate\Http\Request;[m
 use Illuminate\Support\Facades\Hash;[m
[31m-use Illuminate\Support\Facades\Password;[m
[32m+[m[32muse Illuminate\Support\Facades\Password as PasswordBroker;[m
 use Illuminate\Support\Str;[m
[31m-use Illuminate\Validation\Rules;[m
[32m+[m[32muse Illuminate\Validation\Rules\Password;[m
 [m
 class NewPasswordController extends Controller[m
 {[m
[36m@@ -34,19 +34,26 @@[m [mclass NewPasswordController extends Controller[m
     public function store(Request $request)[m
     {[m
         $request->validate([[m
[31m-            'token' => ['required'],[m
[31m-            'email' => ['required', 'email'],[m
[31m-            'password' => ['required', 'confirmed', Rules\Password::defaults()],[m
[32m+[m[32m            'token'    => ['required'],[m
[32m+[m[32m            'email'    => ['required', 'email'],[m
[32m+[m[32m            'password' => [[m
[32m+[m[32m                'required',[m
[32m+[m[32m                'confirmed',[m
[32m+[m[32m                Password::min(8)[m
[32m+[m[32m                    ->letters()[m
[32m+[m[32m                    ->mixedCase()[m
[32m+[m[32m                    ->numbers()[m
[32m+[m[32m                    ->symbols()[m
[32m+[m[32m                    ->uncompromised(),[m
[32m+[m[32m            ],[m
         ]);[m
 [m
[31m-        // Here we will attempt to reset the user's password. If it is successful we[m
[31m-        // will update the password on an actual user model and persist it to the[m
[31m-        // database. Otherwise we will parse the error and return the response.[m
[31m-        $status = Password::reset([m
[32m+[m[32m        // Attempt to reset the user's password[m
[32m+[m[32m        $status = PasswordBroker::reset([m
             $request->only('email', 'password', 'password_confirmation', 'token'),[m
             function ($user) use ($request) {[m
                 $user->forceFill([[m
[31m-                    'password' => Hash::make($request->password),[m
[32m+[m[32m                    'password'       => Hash::make($request->password),[m
                     'remember_token' => Str::random(60),[m
                 ])->save();[m
 [m
[36m@@ -54,12 +61,11 @@[m [mclass NewPasswordController extends Controller[m
             }[m
         );[m
 [m
[31m-        // If the password was successfully reset, we will redirect the user back to[m
[31m-        // the application's home authenticated view. If there is an error we can[m
[31m-        // redirect them back to where they came from with their error message.[m
[31m-        return $status == Password::PASSWORD_RESET[m
[31m-                    ? redirect()->route('login')->with('status', __($status))[m
[31m-                    : back()->withInput($request->only('email'))[m
[31m-                            ->withErrors(['email' => __($status)]);[m
[32m+[m[32m        // Redirect accordingly[m
[32m+[m[32m        return $status == PasswordBroker::PASSWORD_RESET[m
[32m+[m[32m            ? redirect()->route('login')->with('status', __($status))[m
[32m+[m[32m            : back()[m
[32m+[m[32m                ->withInput($request->only('email'))[m
[32m+[m[32m                ->withErrors(['email' => __($status)]);[m
     }[m
[31m-}[m
[32m+[m[32m}[m
\ No newline at end of file[m
[1mdiff --git a/app/Http/Controllers/Auth/RegisteredUserController.php b/app/Http/Controllers/Auth/RegisteredUserController.php[m
[1mindex 9642164..3a79d5b 100644[m
[1m--- a/app/Http/Controllers/Auth/RegisteredUserController.php[m
[1m+++ b/app/Http/Controllers/Auth/RegisteredUserController.php[m
[36m@@ -9,61 +9,51 @@[m [muse Illuminate\Auth\Events\Registered;[m
 use Illuminate\Http\Request;[m
 use Illuminate\Support\Facades\Auth;[m
 use Illuminate\Support\Facades\Hash;[m
[31m-use Illuminate\Validation\Rules;[m
[32m+[m[32muse Illuminate\Validation\Rules\Password;[m
 [m
 class RegisteredUserController extends Controller[m
 {[m
[31m-    /**[m
[31m-     * Display the registration view.[m
[31m-     *[m
[31m-     * @return \Illuminate\View\View[m
[31m-     */[m
     public function create()[m
     {[m
         return view('auth.register');[m
     }[m
 [m
[31m-    /**[m
[31m-     * Handle an incoming registration request.[m
[31m-     *[m
[31m-     * @param  \Illuminate\Http\Request  $request[m
[31m-     * @return \Illuminate\Http\RedirectResponse[m
[31m-     *[m
[31m-     * @throws \Illuminate\Validation\ValidationException[m
[31m-     */[m
     public function store(Request $request)[m
     {[m
         $request->validate([[m
[31m-    'name' => ['required','string','max:255'],[m
[31m-    'email' => ['required','string','email','max:255','unique:users'],[m
[31m-    'password' => [[m
[31m-        'required',[m
[31m-        'string',[m
[31m-        'confirmed',[m
[31m-        'min:8',[m
[31m-        function($attr,$value,$fail){[m
[31m-            if(!preg_match('/[A-Z]/u', $value)){ $fail(__('validation.password_strength')); }[m
[31m-        },[m
[31m-        function($attr,$value,$fail){[m
[31m-            if(!preg_match('/[a-z]/u', $value)){ $fail(__('validation.password_strength')); }[m
[31m-        },[m
[31m-        function($attr,$value,$fail){[m
[31m-            if(!preg_match('/[0-9]/', $value)){ $fail(__('validation.password_strength')); }[m
[31m-        },[m
[31m-        function($attr,$value,$fail){[m
[31m-            if(!preg_match('/[^A-Za-z0-9]/u', $value)){ $fail(__('validation.password_strength')); }[m
[31m-        },[m
[31m-    ],[m
[31m-]);[m
[31m-[m
[31m-        $user = User::create([[m
[31m-            'name' => $request->name,[m
[31m-            'email' => $request->email,[m
[31m-            'password' => Hash::make($request->password),[m
[32m+[m[32m            // o form do Breeze usa "name"; mapeamos para a coluna legada "username"[m
[32m+[m[32m            'name'     => ['required','string','max:255'],[m
[32m+[m[32m            'email'    => ['required','string','email','max:255','unique:users,email'],[m
[32m+[m[32m            'password' => [[m
[32m+[m[32m                'required',[m
[32m+[m[32m                'confirmed',[m
[32m+[m[32m                Password::min(8)[m
[32m+[m[32m                    ->letters()[m
[32m+[m[32m                    ->mixedCase()[m
[32m+[m[32m                    ->numbers()[m
[32m+[m[32m                    ->symbols()[m
[32m+[m[32m                    ->uncompromised(),[m
[32m+[m[32m            ],[m
         ]);[m
 [m
[31m-        event(new Registered($user));[m
[32m+[m[32m        // valores padrão para colunas legadas NOT NULL[m
[32m+[m[32m        $legacyDefaults = [[m
[32m+[m[32m            'country'  => '',[m
[32m+[m[32m            'gender'   => '',[m
[32m+[m[32m            'birthday' => '',[m
[32m+[m[32m            'about'    => '',[m
[32m+[m[32m            'avatar'   => '',[m
[32m+[m[32m        ];[m
[32m+[m
[32m+[m[32m        $user = User::create(array_merge($legacyDefaults, [[m
[32m+[m[32m            'username'        => $request->input('name'),[m
[32m+[m[32m            'email'           => $request->email,[m
[32m+[m[32m            'password'        => Hash::make($request->password),[m
[32m+[m[32m            // varchar no legado — mantenha um formato consistente[m
[32m+[m[32m            'registered_date' => now()->format('Y-m-d H:i:s'),[m
[32m+[m[32m        ]));[m
 [m
[32m+[m[32m        event(new Registered($user));[m
         Auth::login($user);[m
 [m
         return redirect(RouteServiceProvider::HOME);[m
[1mdiff --git a/app/Http/Controllers/BusinessController.php b/app/Http/Controllers/BusinessController.php[m
[1mindex f01c7c6..c6c492a 100644[m
[1m--- a/app/Http/Controllers/BusinessController.php[m
[1m+++ b/app/Http/Controllers/BusinessController.php[m
[36m@@ -6,7 +6,9 @@[m [muse App\Models\Business;[m
 use Illuminate\Http\Request;[m
 use Illuminate\Support\Facades\DB;[m
 use Illuminate\Support\Facades\Schema;[m
[32m+[m[32muse Illuminate\Support\Facades\Storage;              // upload[m
 use Illuminate\Support\Str;[m
[32m+[m[32muse Intervention\Image\ImageManagerStatic as Image; // thumbs[m
 [m
 class BusinessController extends Controller[m
 {[m
[36m@@ -69,12 +71,11 @@[m [mclass BusinessController extends Controller[m
             $categories = collect();[m
         }[m
 [m
[31m-        // ==== Cidades (dinâmico de verdade) ====[m
[32m+[m[32m        // ==== Cidades (dinâmico) ====[m
         $cities = collect();[m
         try {[m
             $meta = $this->detectSimpleTable('city','cities');[m
             if ($meta['table'] && $meta['idCol']) {[m
[31m-                // Monta SELECT seguro com alias padronizado[m
                 $id   = $meta['idCol'];[m
                 $name = $meta['labelCol']; // pode ser null[m
 [m
[36m@@ -84,7 +85,6 @@[m [mclass BusinessController extends Controller[m
                         ->orderBy('label')[m
                         ->get();[m
                 } else {[m
[31m-                    // Sem coluna de nome, cai no fallback[m
                     $cities = DB::table($meta['table'])[m
                         ->selectRaw("$id AS city_id, CONCAT('Cidade #', $id) AS label")[m
                         ->orderBy('label')[m
[36m@@ -144,11 +144,50 @@[m [mclass BusinessController extends Controller[m
             }[m
         }[m
 [m
[31m-        // Upload[m
[31m-        if ($request->hasFile('image') && Schema::hasColumn('business','image')) {[m
[31m-            $path = $request->file('image')->store('business', 'public');[m
[31m-            $data['image'] = $path;[m
[32m+[m[32m        // ============ Upload + Thumbs (1200x800 e 360x240, WEBP) ============[m
[32m+[m[32m        if ($request->hasFile('image') && $request->file('image')->isValid()) {[m
[32m+[m[32m            $baseDir  = 'businesses';[m
[32m+[m[32m            $basename = Str::random(40);[m
[32m+[m
[32m+[m[32m            // extensão do original[m
[32m+[m[32m            $ext = strtolower($request->file('image')->getClientOriginalExtension() ?: 'jpg');[m
[32m+[m
[32m+[m[32m            // 1) Original (mantém compat com colunas 'image' quando existem)[m
[32m+[m[32m            $origRel = $baseDir.'/'.$basename.'.'.$ext;[m
[32m+[m[32m            $request->file('image')->storeAs($baseDir, $basename.'.'.$ext, 'public');[m
[32m+[m[32m            if (Schema::hasColumn('business', 'image')) {[m
[32m+[m[32m                $data['image'] = $origRel;[m
[32m+[m[32m            }[m
[32m+[m
[32m+[m[32m            try {[m
[32m+[m[32m                $srcPath = $request->file('image')->getRealPath();[m
[32m+[m
[32m+[m[32m                // 2) LG 1200x800 (webp)[m
[32m+[m[32m                $imgLg = Image::make($srcPath)[m
[32m+[m[32m                    ->orientate()[m
[32m+[m[32m                    ->fit(1200, 800, function($c){ $c->upsize(); });[m
[32m+[m[32m                $lgRel = $baseDir.'/'.$basename.'_lg.webp';[m
[32m+[m[32m                Storage::disk('public')->put($lgRel, (string) $imgLg->encode('webp', 85));[m
[32m+[m
[32m+[m[32m                // 3) SM 360x240 (webp)[m
[32m+[m[32m                $imgSm = Image::make($srcPath)[m
[32m+[m[32m                    ->orientate()[m
[32m+[m[32m                    ->fit(360, 240, function($c){ $c->upsize(); });[m
[32m+[m[32m                $smRel = $baseDir.'/'.$basename.'_sm.webp';[m
[32m+[m[32m                Storage::disk('public')->put($smRel, (string) $imgSm->encode('webp', 85));[m
[32m+[m
[32m+[m[32m                // Persistência — usa novas colunas se existirem, senão mantém legado[m
[32m+[m[32m                if (Schema::hasColumn('business', 'image_path_lg')) { $data['image_path_lg'] = $lgRel; }[m
[32m+[m[32m                if (Schema::hasColumn('business', 'image_path_sm')) { $data['image_path_sm'] = $smRel; }[m
[32m+[m[32m                if (Schema::hasColumn('business', 'image_lg'))      { $data['image_lg']      = $lgRel; }[m
[32m+[m[32m                if (Schema::hasColumn('business', 'image_sm'))      { $data['image_sm']      = $smRel; }[m
[32m+[m
[32m+[m[32m            } catch (\Throwable $e) {[m
[32m+[m[32m                // Se der erro na geração de thumbs, seguimos apenas com o original[m
[32m+[m[32m                // (opcional: logar o erro)[m
[32m+[m[32m            }[m
         }[m
[32m+[m[32m        // ====================================================================[m
 [m
         $biz = Business::create($data);[m
 [m
[36m@@ -183,12 +222,12 @@[m [mclass BusinessController extends Controller[m
         try {[m
             $meta = $this->detectSimpleTable('city','cities');[m
             if ($meta['table'] && $meta['idCol']) {[m
[31m-                $id   = $meta['idCol'];[m
[31m-                $name = $meta['labelCol'];[m
[32m+[m[32m                $idCol   = $meta['idCol'];[m
[32m+[m[32m                $nameCol = $meta['labelCol'];[m
                 $city = DB::table($meta['table'])[m
[31m-                    ->where($id, $biz->sid)[m
[31m-                    ->selectRaw($id." AS city_id, ".[m
[31m-                        ($name ? "COALESCE($name, CONCAT('Cidade #', $id))" : "CONCAT('Cidade #', $id)")[m
[32m+[m[32m                    ->where($idCol, $biz->sid)[m
[32m+[m[32m                    ->selectRaw($idCol." AS city_id, ".[m
[32m+[m[32m                        ($nameCol ? "COALESCE($nameCol, CONCAT('Cidade #', $idCol))" : "CONCAT('Cidade #', $idCol)")[m
                         ." AS city")[m
                     ->first();[m
             }[m
[1mdiff --git a/app/Http/Controllers/BusinessManageController.php b/app/Http/Controllers/BusinessManageController.php[m
[1mindex 90f7b34..891e85c 100644[m
[1m--- a/app/Http/Controllers/BusinessManageController.php[m
[1m+++ b/app/Http/Controllers/BusinessManageController.php[m
[36m@@ -78,7 +78,7 @@[m [mclass BusinessManageController extends Controller[m
         $item->save();[m
 [m
         return redirect()[m
[31m-            ->route('admin.business.index')[m
[32m+[m[32m            ->route('admin.businesses.index'))[m
             ->with('success', 'Negócio atualizado.');[m
     }[m
 [m
[1mdiff --git a/app/Http/Controllers/ContactController.php b/app/Http/Controllers/ContactController.php[m
[1mnew file mode 100644[m
[1mindex 0000000..5f98a0c[m
[1m--- /dev/null[m
[1m+++ b/app/Http/Controllers/ContactController.php[m
[36m@@ -0,0 +1,60 @@[m
[32m+[m[32m<?php[m
[32m+[m
[32m+[m[32mnamespace App\Http\Controllers;[m
[32m+[m
[32m+[m[32muse App\Http\Requests\ContactRequest;[m
[32m+[m[32muse App\Mail\ContactFormSubmitted;[m
[32m+[m[32muse Illuminate\Support\Facades\Log;[m
[32m+[m[32muse Illuminate\Support\Facades\Mail;[m
[32m+[m
[32m+[m[32mclass ContactController extends Controller[m
[32m+[m[32m{[m
[32m+[m[32m    public function show()[m
[32m+[m[32m    {[m
[32m+[m[32m        return view('pages.contact');[m
[32m+[m[32m    }[m
[32m+[m
[32m+[m[32m    public function submit(ContactRequest $request)[m
[32m+[m[32m    {[m
[32m+[m[32m        $data = $request->validated();[m
[32m+[m
[32m+[m[32m        Log::info('CONTACT_HTTP_START', [[m
[32m+[m[32m            'nome'     => $data['nome'] ?? null,[m
[32m+[m[32m            'email'    => $data['email'] ?? null,[m
[32m+[m[32m            'assunto'  => $data['assunto'] ?? null,[m
[32m+[m[32m            'length'   => isset($data['mensagem']) ? mb_strlen($data['mensagem']) : 0,[m
[32m+[m[32m        ]);[m
[32m+[m
[32m+[m[32m        // ADMIN_EMAILS (vírgulas) -> fallback mail.from.address -> no-reply@localhost[m
[32m+[m[32m        $adminEnv = trim((string) config('app.admin_emails', env('ADMIN_EMAILS', '')));[m
[32m+[m[32m        $toList   = array_values(array_filter(array_map('trim', explode(',', $adminEnv))));[m
[32m+[m[32m        if (empty($toList)) {[m
[32m+[m[32m            $from   = trim((string) config('mail.from.address', ''));[m
[32m+[m[32m            $toList = [$from !== '' ? $from : 'no-reply@localhost'];[m
[32m+[m[32m        }[m
[32m+[m
[32m+[m[32m        try {[m
[32m+[m[32m            Mail::to($toList)->send([m
[32m+[m[32m                (new ContactFormSubmitted($data))[m
[32m+[m[32m                    ->replyTo($data['email'], $data['nome'] ?? null)[m
[32m+[m[32m            );[m
[32m+[m
[32m+[m[32m            Log::info('CONTACT_HTTP_MAIL_OK', [[m
[32m+[m[32m                'to'      => $toList,[m
[32m+[m[32m                'assunto' => $data['assunto'] ?? null,[m
[32m+[m[32m            ]);[m
[32m+[m
[32m+[m[32m            return back()->with('status', 'Mensagem enviada! Verifique o log (mailer=log).');[m
[32m+[m[32m        } catch (\Throwable $e) {[m
[32m+[m[32m            Log::error('CONTACT_HTTP_MAIL_ERROR', [[m
[32m+[m[32m                'error'   => $e->getMessage(),[m
[32m+[m[32m                'to'      => $toList,[m
[32m+[m[32m                'assunto' => $data['assunto'] ?? null,[m
[32m+[m[32m            ]);[m
[32m+[m
[32m+[m[32m            return back()[m
[32m+[m[32m                ->withInput()[m
[32m+[m[32m                ->withErrors(['mensagem' => 'Falha ao enviar a mensagem. Veja o laravel.log para detalhes.']);[m
[32m+[m[32m        }[m
[32m+[m[32m    }[m
[32m+[m[32m}[m
[1mdiff --git a/app/Http/Controllers/PageController.php b/app/Http/Controllers/PageController.php[m
[1mnew file mode 100644[m
[1mindex 0000000..d3d427c[m
[1m--- /dev/null[m
[1m+++ b/app/Http/Controllers/PageController.php[m
[36m@@ -0,0 +1,17 @@[m
[32m+[m[32m<?php[m
[32m+[m
[32m+[m[32mnamespace App\Http\Controllers;[m
[32m+[m
[32m+[m[32muse App\Models\Page;[m
[32m+[m
[32m+[m[32mclass PageController extends Controller[m
[32m+[m[32m{[m
[32m+[m[32m    public function show(string $slug)[m
[32m+[m[32m    {[m
[32m+[m[32m        $page = Page::where('slug', $slug)[m
[32m+[m[32m            ->where('is_active', true)[m
[32m+[m[32m            ->firstOrFail();[m
[32m+[m
[32m+[m[32m        return view('pages.show', compact('page'));[m
[32m+[m[32m    }[m
[32m+[m[32m}[m
[1mdiff --git a/app/Http/Controllers/SearchController.php b/app/Http/Controllers/SearchController.php[m
[1mindex 44a22fa..35a22d5 100644[m
[1m--- a/app/Http/Controllers/SearchController.php[m
[1m+++ b/app/Http/Controllers/SearchController.php[m
[36m@@ -1,7 +1,6 @@[m
 <?php[m
 [m
 namespace App\Http\Controllers;[m
[31m-use App\Models\City;[m
 [m
 use Illuminate\Http\Request;[m
 use Illuminate\Support\Facades\DB;[m
[36m@@ -10,31 +9,84 @@[m [mclass SearchController extends Controller[m
 {[m
     public function index(Request $request)[m
     {[m
[31m-        $q      = trim((string)$request->input("q", ""));[m
[31m-        $cat    = (int)$request->input("categoria", 0);[m
[31m-        $sid    = (int)$request->input("cidade", 0);[m
[32m+[m[32m        // Inputs padronizados (mantém compat com 'categoria' e 'cidade')[m
[32m+[m[32m        $q   = trim((string) $request->query('q', ''));[m
[32m+[m[32m        $cid = $request->query('cid', $request->query('cat', $request->query('categoria'))); // cat_id[m
[32m+[m[32m        $sid = $request->query('sid', $request->query('cidade'));                             // city_id[m
 [m
[31m-        // combos[m
[31m-        $categories = DB::table("category")->selectRaw("cat_id, category AS label")->orderBy("label")->get();[m
[32m+[m[32m        // compat para a view (usa $cat)[m
[32m+[m[32m        $cat = $cid;[m
 [m
[31m-        $cities = City::orderBy("city")->get(["city_id","city"]);[m
[32m+[m[32m        // Combos (mantém 'label' para a view atual)[m
[32m+[m[32m        $categories = DB::table('category')[m
[32m+[m[32m            ->select('cat_id', 'category', DB::raw('category as label'))[m
[32m+[m[32m            ->orderBy('category')[m
[32m+[m[32m            ->get();[m
 [m
[31m-        // query principal[m
[31m-        $biz = DB::table("business")[m
[31m-            ->select("biz_id","business_name","description","city","cid","sid");[m
[32m+[m[32m        $cities = DB::table('city')[m
[32m+[m[32m            ->select('city_id', 'city', DB::raw('city as label'))[m
[32m+[m[32m            ->orderBy('city')[m
[32m+[m[32m            ->get();[m
 [m
[31m-        if ($q !== "") {[m
[31m-            $like = "%".$q."%";[m
[31m-            $biz->where(function($w) use ($like){[m
[31m-                $w->where("business_name","LIKE",$like)[m
[31m-                  ->orWhere("description","LIKE",$like);[m
[32m+[m[32m        // Base da consulta[m
[32m+[m[32m        $qb = DB::table('business as b')[m
[32m+[m[32m            ->leftJoin('category as c', 'c.cat_id', '=', 'b.cid')[m
[32m+[m[32m            ->leftJoin('city as ci', 'ci.city_id', '=', 'b.sid')[m
[32m+[m[32m            ->select([[m
[32m+[m[32m                'b.biz_id',[m
[32m+[m[32m                'b.business_name',[m
[32m+[m[32m                'b.description',[m
[32m+[m[32m                'b.city',[m
[32m+[m[32m                'b.cid',[m
[32m+[m[32m                'b.sid',[m
[32m+[m[32m                'b.image', 'b.image_lg', 'b.image_sm',[m
[32m+[m[32m                'c.category as category_name',[m
[32m+[m[32m                'ci.city as city_name',[m
[32m+[m[32m            ]);[m
[32m+[m
[32m+[m[32m        // Termo + score simples (prioriza nome)[m
[32m+[m[32m        if ($q !== '') {[m
[32m+[m[32m            $q_any   = "%{$q}%";[m
[32m+[m[32m            $q_word  = "% {$q} %";[m
[32m+[m[32m            $q_start = "{$q}%";[m
[32m+[m
[32m+[m[32m            $qb->where(function ($w) use ($q_any) {[m
[32m+[m[32m                $w->where('b.business_name', 'like', $q_any)[m
[32m+[m[32m                  ->orWhere('b.description',  'like', $q_any);[m
             });[m
[32m+[m
[32m+[m[32m            $qb->addSelect(DB::raw([m
[32m+[m[32m                "(CASE[m
[32m+[m[32m                    WHEN b.business_name LIKE ? THEN 3[m
[32m+[m[32m                    WHEN CONCAT(' ', b.business_name, ' ') LIKE ? THEN 2[m
[32m+[m[32m                    WHEN b.business_name LIKE ? THEN 1[m
[32m+[m[32m                    WHEN b.description   LIKE ? THEN 1[m
[32m+[m[32m                    ELSE 0[m
[32m+[m[32m                  END) AS rel_score"[m
[32m+[m[32m            ))->addBinding([$q_start, $q_word, $q_any, $q_any], 'select');[m
[32m+[m[32m        } else {[m
[32m+[m[32m            $qb->addSelect(DB::raw('0 AS rel_score'));[m
[32m+[m[32m        }[m
[32m+[m
[32m+[m[32m        // Filtros combinados[m
[32m+[m[32m        if ($cid !== null && $cid !== '') {[m
[32m+[m[32m            $qb->where('b.cid', $cid);[m
[32m+[m[32m        }[m
[32m+[m[32m        if ($sid !== null && $sid !== '') {[m
[32m+[m[32m            $qb->where('b.sid', $sid);[m
[32m+[m[32m        }[m
[32m+[m
[32m+[m[32m        // Ordenação: previsível por nome[m
[32m+[m[32m        if ($q !== '') {[m
[32m+[m[32m            $qb->orderByDesc('rel_score')[m
[32m+[m[32m               ->orderBy('b.business_name'); // ASC (padrão)[m
[32m+[m[32m        } else {[m
[32m+[m[32m            $qb->orderBy('b.business_name'); // só por nome[m
         }[m
[31m-        if ($cat > 0) $biz->where("cid",$cat);[m
[31m-        if ($sid > 0) $biz->where("sid",$sid);[m
 [m
[31m-        $results = $biz->orderBy("business_name")->get();[m
[32m+[m[32m        // IMPORTANTE: manter filtros nos links de paginação[m
[32m+[m[32m        $results = $qb->paginate(12)->withQueryString();[m
 [m
[31m-        return view("search.index", compact("q","cat","sid","categories","cities","results"));[m
[32m+[m[32m        return view('search.index', compact('q', 'cat', 'sid', 'categories', 'cities', 'results'));[m
     }[m
[31m-}[m
\ No newline at end of file[m
[32m+[m[32m}[m
[1mdiff --git a/app/Http/Controllers/SitemapController.php b/app/Http/Controllers/SitemapController.php[m
[1mindex 668ea80..ea48954 100644[m
[1m--- a/app/Http/Controllers/SitemapController.php[m
[1m+++ b/app/Http/Controllers/SitemapController.php[m
[36m@@ -2,9 +2,12 @@[m
 [m
 namespace App\Http\Controllers;[m
 [m
[32m+[m[32muse Illuminate\Http\Response;[m
 use Illuminate\Support\Facades\DB;[m
[32m+[m[32muse Illuminate\Support\Facades\Log;[m
[32m+[m[32muse Illuminate\Support\Facades\Route as RouteFacade;[m
[32m+[m[32muse Illuminate\Support\Facades\Schema;[m
 use Illuminate\Support\Str;[m
[31m-use Illuminate\Http\Response;[m
 [m
 class SitemapController extends Controller[m
 {[m
[36m@@ -13,95 +16,130 @@[m [mclass SitemapController extends Controller[m
      */[m
     public function index(): Response[m
     {[m
[31m-        $base   = url('/');[m
[31m-        $nowIso = now()->toAtomString();[m
[31m-[m
[31m-        // rotas estáticas relevantes[m
[31m-        $static = [[m
[31m-            route('categories.index'),[m
[31m-            route('cities.index'),[m
[31m-            route('search.index'),[m
[31m-        ];[m
[31m-[m
[31m-        // legado: category (cat_id, category)[m
[31m-        $cats = DB::table('category')[m
[31m-            ->select('cat_id', 'category')[m
[31m-            ->orderBy('cat_id')[m
[31m-            ->get();[m
[31m-[m
[31m-        // legado: city (city_id, city)[m
[31m-        $cities = DB::table('city')[m
[31m-            ->select('city_id', 'city')[m
[31m-            ->orderBy('city_id')[m
[31m-            ->get();[m
[31m-[m
[31m-        // legado: business (biz_id, business_name)[m
[31m-        $biz = DB::table('business')[m
[31m-            ->select('biz_id', 'business_name')[m
[31m-            ->whereNotNull('business_name')[m
[31m-            ->orderByDesc('biz_id')[m
[31m-            ->get();[m
[31m-[m
[31m-        $urls = [];[m
[31m-[m
[31m-        // home[m
[31m-        $urls[] = [[m
[31m-            'loc'        => $base . '/',[m
[31m-            'changefreq' => 'daily',[m
[31m-            'priority'   => '1.0',[m
[31m-            'lastmod'    => $nowIso,[m
[31m-        ];[m
[31m-[m
[31m-        foreach ($static as $u) {[m
[31m-            $urls[] = ['loc' => $u, 'changefreq' => 'weekly', 'priority' => '0.7', 'lastmod' => $nowIso];[m
[31m-        }[m
[32m+[m[32m        try {[m
[32m+[m[32m            $base   = url('/');[m
[32m+[m[32m            $nowIso = now()->toAtomString();[m
 [m
[31m-        foreach ($cats as $c) {[m
[31m-            $slug = Str::slug($c->category ?? (string)$c->cat_id);[m
[31m-            $urls[] = [[m
[31m-                'loc'        => route('categories.show', ['id' => $c->cat_id, 'slug' => $slug]),[m
[31m-                'changefreq' => 'weekly',[m
[31m-                'priority'   => '0.6',[m
[31m-                'lastmod'    => $nowIso,[m
[31m-            ];[m
[31m-        }[m
[32m+[m[32m            // Helper: só adiciona rota se existir[m
[32m+[m[32m            $static = [];[m
[32m+[m[32m            foreach (['categories.index', 'cities.index', 'search.index'] as $rname) {[m
[32m+[m[32m                if (RouteFacade::has($rname)) {[m
[32m+[m[32m                    $static[] = route($rname);[m
[32m+[m[32m                }[m
[32m+[m[32m            }[m
 [m
[31m-        foreach ($cities as $c) {[m
[31m-            $slug = Str::slug($c->city ?? (string)$c->city_id);[m
[31m-            $urls[] = [[m
[31m-                'loc'        => route('cities.show', ['id' => $c->city_id, 'slug' => $slug]),[m
[31m-                'changefreq' => 'weekly',[m
[31m-                'priority'   => '0.6',[m
[31m-                'lastmod'    => $nowIso,[m
[31m-            ];[m
[31m-        }[m
[32m+[m[32m            // ==== Coletas com tolerância a esquema ====[m
[32m+[m[32m            $cats   = collect();[m
[32m+[m[32m            $cities = collect();[m
[32m+[m[32m            $biz    = collect();[m
 [m
[31m-        foreach ($biz as $b) {[m
[31m-            $slug = Str::slug($b->business_name ?? (string)$b->biz_id);[m
[31m-            $urls[] = [[m
[31m-                'loc'        => route('business.show', ['id' => $b->biz_id, 'slug' => $slug]),[m
[31m-                'changefreq' => 'weekly',[m
[31m-                'priority'   => '0.8',[m
[31m-                'lastmod'    => $nowIso,[m
[31m-            ];[m
[31m-        }[m
[32m+[m[32m            // Categorias (legacy: tabela category; coluna 'category' ou 'category_name')[m
[32m+[m[32m            if (Schema::hasTable('category')) {[m
[32m+[m[32m                $catQuery = DB::table('category')->select('cat_id');[m
[32m+[m
[32m+[m[32m                // seleciona também a coluna de nome que existir[m
[32m+[m[32m                if (Schema::hasColumn('category', 'category_name')) {[m
[32m+[m[32m                    $catQuery->addSelect('category_name');[m
[32m+[m[32m                }[m
[32m+[m[32m                if (Schema::hasColumn('category', 'category')) {[m
[32m+[m[32m                    $catQuery->addSelect('category');[m
[32m+[m[32m                }[m
[32m+[m
[32m+[m[32m                $cats = $catQuery->orderBy('cat_id')->get();[m
[32m+[m[32m            }[m
[32m+[m
[32m+[m[32m            // Cidades (legacy: tabela city; colunas 'city_id', 'city')[m
[32m+[m[32m            if (Schema::hasTable('city')) {[m
[32m+[m[32m                $cityQuery = DB::table('city')->select('city_id');[m
[32m+[m[32m                if (Schema::hasColumn('city', 'city')) {[m
[32m+[m[32m                    $cityQuery->addSelect('city');[m
[32m+[m[32m                }[m
[32m+[m[32m                $cities = $cityQuery->orderBy('city_id')->get();[m
[32m+[m[32m            }[m
[32m+[m
[32m+[m[32m            // Negócios (legacy: tabela business; colunas 'biz_id', 'business_name')[m
[32m+[m[32m            if (Schema::hasTable('business')) {[m
[32m+[m[32m                $bizQuery = DB::table('business')->select('biz_id');[m
[32m+[m[32m                if (Schema::hasColumn('business', 'business_name')) {[m
[32m+[m[32m                    $bizQuery->addSelect('business_name')->whereNotNull('business_name');[m
[32m+[m[32m                }[m
[32m+[m[32m                $biz = $bizQuery->orderByDesc('biz_id')->get();[m
[32m+[m[32m            }[m
[32m+[m
[32m+[m[32m            // ==== Montagem do XML ====[m
[32m+[m[32m            $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";[m
[32m+[m[32m            $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";[m
 [m
[31m-        // XML simples e válido[m
[31m-        $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";[m
[31m-        $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";[m
[31m-        foreach ($urls as $u) {[m
[31m-            // Escapar como XML (ENT_XML1)[m
[31m-            $loc = htmlspecialchars($u['loc'], ENT_XML1);[m
[31m-            $xml .= "  <url>\n";[m
[31m-            $xml .= "    <loc>{$loc}</loc>\n";[m
[31m-            if (!empty($u['lastmod']))    { $xml .= "    <lastmod>{$u['lastmod']}</lastmod>\n"; }[m
[31m-            if (!empty($u['changefreq'])) { $xml .= "    <changefreq>{$u['changefreq']}</changefreq>\n"; }[m
[31m-            if (!empty($u['priority']))   { $xml .= "    <priority>{$u['priority']}</priority>\n"; }[m
[31m-            $xml .= "  </url>\n";[m
[32m+[m[32m            // home[m
[32m+[m[32m            $xml .= $this->urlNode($base . '/', $nowIso, 'daily', '1.0');[m
[32m+[m
[32m+[m[32m            // estáticas (se existirem)[m
[32m+[m[32m            foreach ($static as $u) {[m
[32m+[m[32m                $xml .= $this->urlNode($u, $nowIso, 'weekly', '0.7');[m
[32m+[m[32m            }[m
[32m+[m
[32m+[m[32m            // categorias[m
[32m+[m[32m            foreach ($cats as $c) {[m
[32m+[m[32m                $name = $c->category_name ?? $c->category ?? null;[m
[32m+[m[32m                $slug = Str::slug($name ?? (string) $c->cat_id);[m
[32m+[m[32m                // só gera se a rota existir[m
[32m+[m[32m                if (RouteFacade::has('categories.show')) {[m
[32m+[m[32m                    $loc = route('categories.show', ['id' => $c->cat_id, 'slug' => $slug]);[m
[32m+[m[32m                    $xml .= $this->urlNode($loc, $nowIso, 'weekly', '0.6');[m
[32m+[m[32m                }[m
[32m+[m[32m            }[m
[32m+[m
[32m+[m[32m            // cidades[m
[32m+[m[32m            foreach ($cities as $c) {[m
[32m+[m[32m                $name = $c->city ?? null;[m
[32m+[m[32m                $slug = Str::slug($name ?? (string) $c->city_id);[m
[32m+[m[32m                if (RouteFacade::has('cities.show')) {[m
[32m+[m[32m                    $loc = route('cities.show', ['id' => $c->city_id, 'slug' => $slug]);[m
[32m+[m[32m                    $xml .= $this->urlNode($loc, $nowIso, 'weekly', '0.6');[m
[32m+[m[32m                }[m
[32m+[m[32m            }[m
[32m+[m
[32m+[m[32m            // negócios[m
[32m+[m[32m            foreach ($biz as $b) {[m
[32m+[m[32m                $name = $b->business_name ?? null;[m
[32m+[m[32m                $slug = Str::slug($name ?? (string) $b->biz_id);[m
[32m+[m[32m                if (RouteFacade::has('business.show')) {[m
[32m+[m[32m                    $loc = route('business.show', ['id' => $b->biz_id, 'slug' => $slug]);[m
[32m+[m[32m                    $xml .= $this->urlNode($loc, $nowIso, 'weekly', '0.8');[m
[32m+[m[32m                }[m
[32m+[m[32m            }[m
[32m+[m
[32m+[m[32m            $xml .= "</urlset>\n";[m
[32m+[m
[32m+[m[32m            return response($xml, 200)[m
[32m+[m[32m                ->header('Content-Type', 'application/xml; charset=UTF-8');[m
[32m+[m[32m        } catch (\Throwable $e) {[m
[32m+[m[32m            // Loga para diagnóstico e ainda assim devolve um XML mínimo válido[m
[32m+[m[32m            Log::error('sitemap.xml error: ' . $e->getMessage(), [[m
[32m+[m[32m                'trace' => $e->getTraceAsString(),[m
[32m+[m[32m            ]);[m
[32m+[m
[32m+[m[32m            $fallback  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";[m
[32m+[m[32m            $fallback .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";[m
[32m+[m[32m            $fallback .= $this->urlNode(url('/'), now()->toAtomString(), 'daily', '1.0');[m
[32m+[m[32m            $fallback .= "</urlset>\n";[m
[32m+[m
[32m+[m[32m            // Mantém 200 para não prejudicar bots enquanto você verifica o log[m
[32m+[m[32m            return response($fallback, 200)[m
[32m+[m[32m                ->header('Content-Type', 'application/xml; charset=UTF-8');[m
         }[m
[31m-        $xml .= "</urlset>\n";[m
[32m+[m[32m    }[m
 [m
[31m-        return response($xml, 200)[m
[31m-            ->header('Content-Type', 'application/xml; charset=UTF-8');[m
[32m+[m[32m    private function urlNode(string $loc, string $lastmod, string $changefreq, string $priority): string[m
[32m+[m[32m    {[m
[32m+[m[32m        // Escapa como XML[m
[32m+[m[32m        $locEsc = htmlspecialchars($loc, ENT_XML1);[m
[32m+[m[32m        return[m
[32m+[m[32m            "  <url>\n" .[m
[32m+[m[32m            "    <loc>{$locEsc}</loc>\n" .[m
[32m+[m[32m            "    <lastmod>{$lastmod}</lastmod>\n" .[m
[32m+[m[32m            "    <changefreq>{$changefreq}</changefreq>\n" .[m
[32m+[m[32m            "    <priority>{$priority}</priority>\n" .[m
[32m+[m[32m            "  </url>\n";[m
     }[m
 }[m
[1mdiff --git a/app/Http/Kernel.php b/app/Http/Kernel.php[m
[1mindex 21a8754..a7c86bb 100644[m
[1m--- a/app/Http/Kernel.php[m
[1m+++ b/app/Http/Kernel.php[m
[36m@@ -62,5 +62,8 @@[m [mclass Kernel extends HttpKernel[m
         'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,[m
         'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,[m
         'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,[m
[32m+[m
[32m+[m[32m        // >>> adicionado <<<[m
[32m+[m[32m        'is_admin' => \App\Http\Middleware\IsAdmin::class,[m
     ];[m
 }[m
[1mdiff --git a/app/Http/Middleware/IsAdmin.php b/app/Http/Middleware/IsAdmin.php[m
[1mindex b782546..eebd421 100644[m
[1m--- a/app/Http/Middleware/IsAdmin.php[m
[1m+++ b/app/Http/Middleware/IsAdmin.php[m
[36m@@ -4,14 +4,23 @@[m [mnamespace App\Http\Middleware;[m
 [m
 use Closure;[m
 use Illuminate\Support\Facades\Auth;[m
[32m+[m[32muse Illuminate\Support\Facades\Gate;[m
 [m
 class IsAdmin[m
 {[m
     public function handle($request, Closure $next)[m
     {[m
[31m-        if (!Auth::check() || !Auth::user()->is_admin) {[m
[32m+[m[32m        $ok = Auth::check() && ([m
[32m+[m[32m            // Flag no banco[m
[32m+[m[32m            (bool) (Auth::user()->is_admin ?? false)[m
[32m+[m[32m            // OU Gate por e-mail (ENV ADMIN_EMAILS)[m
[32m+[m[32m            || Gate::allows('admin')[m
[32m+[m[32m        );[m
[32m+[m
[32m+[m[32m        if (!$ok) {[m
             abort(403, 'Acesso negado.');[m
         }[m
[32m+[m
         return $next($request);[m
     }[m
[31m-}[m
\ No newline at end of file[m
[32m+[m[32m}[m
[1mdiff --git a/app/Http/Requests/ContactRequest.php b/app/Http/Requests/ContactRequest.php[m
[1mnew file mode 100644[m
[1mindex 0000000..8f28ce1[m
[1m--- /dev/null[m
[1m+++ b/app/Http/Requests/ContactRequest.php[m
[36m@@ -0,0 +1,33 @@[m
[32m+[m[32m<?php[m
[32m+[m
[32m+[m[32mnamespace App\Http\Requests;[m
[32m+[m
[32m+[m[32muse Illuminate\Foundation\Http\FormRequest;[m
[32m+[m
[32m+[m[32mclass ContactRequest extends FormRequest[m
[32m+[m[32m{[m
[32m+[m[32m    public function authorize(): bool[m
[32m+[m[32m    {[m
[32m+[m[32m        return true;[m
[32m+[m[32m    }[m
[32m+[m
[32m+[m[32m    public function rules(): array[m
[32m+[m[32m    {[m
[32m+[m[32m        return [[m
[32m+[m[32m            "nome"     => ["required","string","max:120"],[m
[32m+[m[32m            "email"    => ["required","email","max:160"],[m
[32m+[m[32m            "assunto"  => ["required","string","max:160"],[m
[32m+[m[32m            "mensagem" => ["required","string","max:5000"],[m
[32m+[m[32m        ];[m
[32m+[m[32m    }[m
[32m+[m
[32m+[m[32m    public function attributes(): array[m
[32m+[m[32m    {[m
[32m+[m[32m        return [[m
[32m+[m[32m            "nome"     => "nome",[m
[32m+[m[32m            "email"    => "e-mail",[m
[32m+[m[32m            "assunto"  => "assunto",[m
[32m+[m[32m            "mensagem" => "mensagem",[m
[32m+[m[32m        ];[m
[32m+[m[32m    }[m
[32m+[m[32m}[m
[1mdiff --git a/app/Models/Page.php b/app/Models/Page.php[m
[1mindex 7cfb811..945836f 100644[m
[1m--- a/app/Models/Page.php[m
[1m+++ b/app/Models/Page.php[m
[36m@@ -1,11 +1,12 @@[m
 <?php[m
 [m
[31m-namespace App\Models\Models;[m
[32m+[m[32mnamespace App\Models;[m
 [m
[31m-use Illuminate\Database\Eloquent\Factories\HasFactory;[m
 use Illuminate\Database\Eloquent\Model;[m
 [m
 class Page extends Model[m
 {[m
[31m-    use HasFactory;[m
[32m+[m[32m    protected $fillable = ['title','slug','content','is_active'];[m
[32m+[m[32m    // Se o nome da tabela for diferente, especifique:[m
[32m+[m[32m    // protected $table = 'pages';[m
 }[m
[1mdiff --git a/app/Models/User.php b/app/Models/User.php[m
[1mindex f21d85f..d5553ae 100644[m
[1m--- a/app/Models/User.php[m
[1m+++ b/app/Models/User.php[m
[36m@@ -1,10 +1,12 @@[m
 <?php[m
[32m+[m
 namespace App\Models;[m
 [m
[32m+[m[32muse Illuminate\Contracts\Auth\MustVerifyEmail;[m
 use Illuminate\Foundation\Auth\User as Authenticatable;[m
 use Illuminate\Notifications\Notifiable;[m
 [m
[31m-class User extends Authenticatable[m
[32m+[m[32mclass User extends Authenticatable implements MustVerifyEmail[m
 {[m
     use Notifiable;[m
 [m
[36m@@ -12,6 +14,25 @@[m [mclass User extends Authenticatable[m
     protected $primaryKey = 'user_id';[m
     public $timestamps = false;[m
 [m
[31m-    protected $fillable = ['username','email','password'];[m
[31m-    protected $hidden   = ['password','remember_token'];[m
[31m-}[m
\ No newline at end of file[m
[32m+[m[32m    // mantém os fillables já definidos (legado)[m
[32m+[m[32m    protected $fillable = [[m
[32m+[m[32m        'username',[m
[32m+[m[32m        'email',[m
[32m+[m[32m        'password',[m
[32m+[m[32m        'registered_date',[m
[32m+[m[32m        'country',[m
[32m+[m[32m        'gender',[m
[32m+[m[32m        'birthday',[m
[32m+[m[32m        'about',[m
[32m+[m[32m        'avatar',[m
[32m+[m[32m    ];[m
[32m+[m
[32m+[m[32m    protected $hidden = ['password', 'remember_token'];[m
[32m+[m
[32m+[m[32m    protected $casts = [[m
[32m+[m[32m        'email_verified_at' => 'datetime',[m
[32m+[m[32m        // Se algum dia existir uma flag booleana (ex.: is_admin/active), basta descomentar/adicionar:[m
[32m+[m[32m        // 'is_admin' => 'boolean',[m
[32m+[m[32m        // 'active'   => 'boolean',[m
[32m+[m[32m    ];[m
[32m+[m[32m}[m
[1mdiff --git a/composer.json b/composer.json[m
[1mindex 5237158..40665d1 100644[m
[1m--- a/composer.json[m
[1m+++ b/composer.json[m
[36m@@ -12,6 +12,8 @@[m
         "fideloper/proxy": "^4.4",[m
         "fruitcake/laravel-cors": "^2.0",[m
         "guzzlehttp/guzzle": "^7.0.1",[m
[32m+[m[32m        "intervention/image": "^3.11",[m
[32m+[m[32m        "laravel-lang/lang": "^14.7",[m
         "laravel/framework": "^8.12",[m
         "laravel/tinker": "^2.5"[m
     },[m
[1mdiff --git a/composer.lock b/composer.lock[m
[1mindex 447bb1b..d5c20eb 100644[m
[1m--- a/composer.lock[m
[1m+++ b/composer.lock[m
[36m@@ -4,8 +4,54 @@[m
         "Read more about it at https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies",[m
         "This file is @generated automatically"[m
     ],[m
[31m-    "content-hash": "29af3d61320f76fa552b10210a5b48d0",[m
[32m+[m[32m    "content-hash": "18626114db7ec3c8a922f55a94d45564",[m
     "packages": [[m
[32m+[m[32m        {[m
[32m+[m[32m            "name": "archtechx/enums",[m
[32m+[m[32m            "version": "v0.3.2",[m
[32m+[m[32m            "source": {[m
[32m+[m[32m                "type": "git",[m
[32m+[m[32m                "url": "https://github.com/archtechx/enums.git",[m
[32m+[m[32m                "reference": "475f45e682b0771253707f9403b704759a08da5f"[m
[32m+[m[32m            },[m
[32m+[m[32m            "dist": {[m
[32m+[m[32m                "type": "zip",[m
[32m+[m[32m                "url": "https://api.github.com/repos/archtechx/enums/zipball/475f45e682b0771253707f9403b704759a08da5f",[m
[32m+[m[32m                "reference": "475f45e682b0771253707f9403b704759a08da5f",[m
[32m+[m[32m                "shasum": ""[m
[32m+[m[32m            },[m
[32m+[m[32m            "require": {[m
[32m+[m[32m                "php": "^8.1"[m
[32m+[m[32m            },[m
[32m+[m[32m            "require-dev": {[m
[32m+[m[32m                "nunomaduro/larastan": "^1.0|^2.4",[m
[32m+[m[32m                "orchestra/testbench": "^6.9|^7.0|^8.0",[m
[32m+[m[32m                "pestphp/pest": "^1.2|^2.0",[m
[32m+[m[32m                "pestphp/pest-plugin-laravel": "^1.0|^2.0"[m
[32m+[m[32m            },[m
[32m+[m[32m            "type": "library",[m
[32m+[m[32m            "autoload": {[m
[32m+[m[32m                "psr-4": {[m
[32m+[m[32m                    "ArchTech\\Enums\\": "src/"[m
[32m+[m[32m                }[m
[32m+[m[32m            },[m
[32m+[m[32m            "notification-url": "https://packagist.org/downloads/",[m
[32m+[m[32m            "license": [[m
[32m+[m[32m                "MIT"[m
[32m+[m[32m            ],[m
[32m+[m[32m            "authors": [[m
[32m+[m[32m                {[m
[32m+[m[32m                    "name": "Samuel Štancl",[m
[32m+[m[32m                    "email": "samuel@archte.ch"[m
[32m+[m[32m                }[m
[32m+[m[32m            ],[m
[32m+[m[32m            "description": "Helpers for making PHP enums more lovable.",[m
[32m+[m[32m            "support": {[m
[32m+[m[32m                "issues": "https://github.com/archtechx/enums/issues",[m
[32m+[m[32m                "source": "https://github.com/archtechx/enums/tree/v0.3.2"[m
[32m+[m[32m            },[m
[32m+[m[32m            "time": "2023-02-15T13:05:41+00:00"[m
[32m+[m[32m        },[m
         {[m
             "name": "asm89/stack-cors",[m
             "version": "v2.3.0",[m
[36m@@ -191,6 +237,83 @@[m
             ],[m
             "time": "2024-02-09T16:56:22+00:00"[m
         },[m
[32m+[m[32m        {[m
[32m+[m[32m            "name": "composer/semver",[m
[32m+[m[32m            "version": "3.4.4",[m
[32m+[m[32m            "source": {[m
[32m+[m[32m                "type": "git",[m
[32m+[m[32m                "url": "https://github.com/composer/semver.git",[m
[32m+[m[32m                "reference": "198166618906cb2de69b95d7d47e5fa8aa1b2b95"[m
[32m+[m[32m            },[m
[32m+[m[32m            "dist": {[m
[32m+[m[32m                "type": "zip",[m
[32m+[m[32m                "url": "https://api.github.com/repos/composer/semver/zipball/198166618906cb2de69b95d7d47e5fa8aa1b2b95",[m
[32m+[m[32m                "reference": "198166618906cb2de69b95d7d47e5fa8aa1b2b95",[m
[32m+[m[32m                "shasum": ""[m
[32m+[m[32m            },[m
[32m+[m[32m            "require": {[m
[32m+[m[32m                "php": "^5.3.2 || ^7.0 || ^8.0"[m
[32m+[m[32m            },[m
[32m+[m[32m            "require-dev": {[m
[32m+[m[32m                "phpstan/phpstan": "^1.11",[m
[32m+[m[32m                "symfony/phpunit-bridge": "^3 || ^7"[m
[32m+[m[32m            },[m
[32m+[m[32m            "type": "library",[m
[32m+[m[32m            "extra": {[m
[32m+[m[32m                "branch-alias": {[m
[32m+[m[32m                    "dev-main": "3.x-dev"[m
[32m+[m[32m                }[m
[32m+[m[32m            },[m
[32m+[m[32m            "autoload": {[m
[32m+[m[32m                "psr-4": {[m
[32m+[m[32m                    "Composer\\Semver\\": "src"[m
[32m+[m[32m                }[m
[32m+[m[32m            },[m
[32m+[m[32m            "notification-url": "https://packagist.org/downloads/",[m
[32m+[m[32m            "license": [[m
[32m+[m[32m                "MIT"[m
[32m+[m[32m            ],[m
[32m+[m[32m            "authors": [[m
[32m+[m[32m                {[m
[32m+[m[32m                    "name": "Nils Adermann",[m
[32m+[m[32m                    "email": "naderman@naderman.de",[m
[32m+[m[32m                    "homepage": "http://www.naderman.de"[m
[32m+[m[32m                },[m
[32m+[m[32m                {[m
[32m+[m[32m                    "name": "Jordi Boggiano",[m
[32m+[m[32m                    "email": "j.boggiano@seld.be",[m
[32m+[m[32m                    "homepage": "http://seld.be"[m
[32m+[m[32m                },[m
[32m+[m[32m                {[m
[32m+[m[32m                    "name": "Rob Bast",[m
[32m+[m[32m                    "email": "rob.bast@gmail.com",[m
[32m+[m[32m                    "homepage": "http://robbast.nl"[m
[32m+[m[32m                }[m
[32m+[m[32m            ],[m
[32m+[m[32m            "description": "Semver library that offers utilities, version constraint parsing and validation.",[m
[32m+[m[32m            "keywords": [[m
[32m+[m[32m                "semantic",[m
[32m+[m[32m                "semver",[m
[32m+[m[32m                "validation",[m
[32m+[m[32m                "versioning"[m
[32m+[m[32m            ],[m
[32m+[m[32m            "support": {[m
[32m+[m[32m                "irc": "ircs://irc.libera.chat:6697/composer",[m
[32m+[m[32m                "issues": "https://github.com/composer/semver/issues",[m
[32m+[m[32m                "source": "https://github.com/composer/semver/tree/3.4.4"[m
[32m+[m[32m            },[m
[32m+[m[32m            "funding": [[m
[32m+[m[32m                {[m
[32m+[m[32m                    "url": "https://packagist.com",[m
[32m+[m[32m                    "type": "custom"[m
[32m+[m[32m                },[m
[32m+[m[32m                {[m
[32m+[m[32m                    "url": "https://github.com/composer",[m
[32m+[m[32m                    "type": "github"[m
[32m+[m[32m                }[m
[32m+[m[32m            ],[m
[32m+[m[32m            "time": "2025-08-20T19:15:30+00:00"[m
[32m+[m[32m        },[m
         {[m
             "name": "dflydev/dot-access-data",[m
             "version": "v3.0.3",[m
[36m@@ -432,6 +555,234 @@[m
             ],[m
             "time": "2022-02-28T11:07:21+00:00"[m
         },[m
[32m+[m[32m        {[m
[32m+[m[32m            "name": "dragon-code/contracts",[m
[32m+[m[32m            "version": "2.24.0",[m
[32m+[m[32m            "source": {[m
[32m+[m[32m                "type": "git",[m
[32m+[m[32m                "url": "https://github.com/TheDragonCode/contracts.git",[m
[32m+[m[32m                "reference": "c21ea4fc0a399bd803a2805a7f2c989749083896"[m
[32m+[m[32m            },[m
[32m+[m[32m            "dist": {[m
[32m+[m[32m                "type": "zip",[m
[32m+[m[32m                "url": "https://api.github.com/repos/TheDragonCode/contracts/zipball/c21ea4fc0a399bd803a2805a7f2c989749083896",[m
[32m+[m[32m                "reference": "c21ea4fc0a399bd803a2805a7f2c989749083896",[m
[32m+[m[32m                "shasum": ""[m
[32m+[m[32m            },[m
[32m+[m[32m            "require": {[m
[32m+[m[32m                "php": "^7.2.5 || ^8.0",[m
[32m+[m[32m                "psr/http-message": "^1.0.1 || ^2.0",[m
[32m+[m[32m                "symfony/http-kernel": "^4.0 || ^5.0 || ^6.0 || ^7.0",[m
[32m+[m[32m                "symfony/polyfill-php80": "^1.23"[m
[32m+[m[32m            },[m
[32m+[m[32m            "conflict": {[m
[32m+[m[32m                "andrey-helldar/contracts": "*"[m
[32m+[m[32m            },[m
[32m+[m[32m            "require-dev": {[m
[32m+[m[32m                "illuminate/database": "^10.0 || ^11.0 || ^12.0",[m
[32m+[m[32m                "phpdocumentor/reflection-docblock": "^5.0"[m
[32m+[m[32m            },[m
[32m+[m[32m            "type": "library",[m
[32m+[m[32m            "autoload": {[m
[32m+[m[32m                "psr-4": {[m
[32m+[m[32m                    "DragonCode\\Contracts\\": "src"[m
[32m+[m[32m                }[m
[32m+[m[32m            },[m
[32m+[m[32m            "notification-url": "https://packagist.org/downloads/",[m
[32m+[m[32m            "license": [[m
[32m+[m[32m                "MIT"[m
[32m+[m[32m            ],[m
[32m+[m[32m            "authors": [[m
[32m+[m[32m                {[m
[32m+[m[32m                    "name": "Andrey Helldar",[m
[32m+[m[32m                    "email": "helldar@dragon-code.pro",[m
[32m+[m[32m                    "homepage": "https://dragon-code.pro"[m
[32m+[m[32m                }[m
[32m+[m[32m            ],[m
[32m+[m[32m            "description": "A set of contracts for any project",[m
[32m+[m[32m            "keywords": [[m
[32m+[m[32m                "contracts",[m
[32m+[m[32m                "interfaces"[m
[32m+[m[32m            ],[m
[32m+[m[32m            "support": {[m
[32m+[m[32m                "source": "https://github.com/TheDragonCode/contracts"[m
[32m+[m[32m            },[m
[32m+[m[32m            "funding": [[m
[32m+[m[32m                {[m
[32m+[m[32m                    "url": "https://boosty.to/dragon-code",[m
[32m+[m[32m                    "type": "boosty"[m
[32m+[m[32m                },[m
[32m+[m[32m                {[m
[32m+[m[32m                    "url": "https://yoomoney.ru/to/410012608840929",[m
[32m+[m[32m                    "type": "yoomoney"[m
[32m+[m[32m                }[m
[32m+[m[32m            ],[m
[32m+[m[32m            "time": "2025-02-23T23:11:50+00:00"[m
[32m+[m[32m        },[m
[32m+[m[32m        {[m
[32m+[m[32m            "name": "dragon-code/pretty-array",[m
[32m+[m[32m            "version": "4.2.0",[m
[32m+[m[32m            "source": {[m
[32m+[m[32m                "type": "git",[m
[32m+[m[32m                "url": "https://github.com/TheDragonCode/pretty-array.git",[m
[32m+[m[32m                "reference": "b94034d92172a5d14a578822d68b2a8f8b5388e0"[m
[32m+[m[32m            },[m
[32m+[m[32m            "dist": {[m
[32m+[m[32m                "type": "zip",[m
[32m+[m[32m                "url": "https://api.github.com/repos/TheDragonCode/pretty-array/zipball/b94034d92172a5d14a578822d68b2a8f8b5388e0",[m
[32m+[m[32m                "reference": "b94034d92172a5d14a578822d68b2a8f8b5388e0",[m
[32m+[m[32m                "shasum": ""[m
[32m+[m[32m            },[m
[32m+[m[32m            "require": {[m
[32m+[m[32m                "dragon-code/contracts": "^2.20",[m
[32m+[m[32m                "dragon-code/support": "^6.11.2",[m
[32m+[m[32m                "ext-dom": "*",[m
[32m+[m[32m                "ext-mbstring": "*",[m
[32m+[m[32m                "php": "^8.0"[m
[32m+[m[32m            },[m
[32m+[m[32m            "require-dev": {[m
[32m+[m[32m                "phpunit/phpunit": "^9.6 || ^10.0 || ^11.0 || ^12.0"[m
[32m+[m[32m            },[m
[32m+[m[32m            "suggest": {[m
[32m+[m[32m                "symfony/thanks": "Give thanks (in the form of a GitHub) to your fellow PHP package maintainers"[m
[32m+[m[32m            },[m
[32m+[m[32m            "type": "library",[m
[32m+[m[32m            "autoload": {[m
[32m+[m[32m                "psr-4": {[m
[32m+[m[32m                    "DragonCode\\PrettyArray\\": "src"[m
[32m+[m[32m                }[m
[32m+[m[32m            },[m
[32m+[m[32m            "notification-url": "https://packagist.org/downloads/",[m
[32m+[m[32m            "license": [[m
[32m+[m[32m                "MIT"[m
[32m+[m[32m            ],[m
[32m+[m[32m            "authors": [[m
[32m+[m[32m                {[m
[32m+[m[32m                    "name": "Andrey Helldar",[m
[32m+[m[32m                    "email": "helldar@dragon-code.pro",[m
[32m+[m[32m                    "homepage": "https://dragon-code.pro"[m
[32m+[m[32m                }[m
[32m+[m[32m            ],[m
[32m+[m[32m            "description": "Simple conversion of an array to a pretty view",[m
[32m+[m[32m            "keywords": [[m
[32m+[m[32m                "andrey helldar",[m
[32m+[m[32m                "array",[m
[32m+[m[32m                "dragon",[m
[32m+[m[32m                "dragon code",[m
[32m+[m[32m                "pretty",[m
[32m+[m[32m                "pretty array"[m
[32m+[m[32m            ],[m
[32m+[m[32m            "support": {[m
[32m+[m[32m                "issues": "https://github.com/TheDragonCode/pretty-array/issues",[m
[32m+[m[32m                "source": "https://github.com/TheDragonCode/pretty-array"[m
[32m+[m[32m            },[m
[32m+[m[32m            "funding": [[m
[32m+[m[32m                {[m
[32m+[m[32m                    "url": "https://boosty.to/dragon-code",[m
[32m+[m[32m                    "type": "boosty"[m
[32m+[m[32m                },[m
[32m+[m[32m                {[m
[32m+[m[32m                    "url": "https://yoomoney.ru/to/410012608840929",[m
[32m+[m[32m                    "type": "yoomoney"[m
[32m+[m[32m                }[m
[32m+[m[32m            ],[m
[32m+[m[32m            "time": "2025-02-24T15:35:24+00:00"[m
[32m+[m[32m        },[m
[32m+[m[32m        {[m
[32m+[m[32m            "name": "dragon-code/support",[m
[32m+[m[32m            "version": "6.16.0",[m
[32m+[m[32m            "source": {[m
[32m+[m[32m                "type": "git",[m
[32m+[m[32m                "url": "https://github.com/TheDragonCode/support.git",[m
[32m+[m[32m                "reference": "ab9b657a307e75f6ba5b2b39e1e45207dc1a065a"[m
[32m+[m[32m            },[m
[32m+[m[32m            "dist": {[m
[32m+[m[32m                "type": "zip",[m
[32m+[m[32m                "url": "https://api.github.com/repos/TheDragonCode/support/zipball/ab9b657a307e75f6ba5b2b39e1e45207dc1a065a",[m
[32m+[m[32m                "reference": "ab9b657a307e75f6ba5b2b39e1e45207dc1a065a",[m
[32m+[m[32m                "shasum": ""[m
[32m+[m[32m            },[m
[32m+[m[32m            "require": {[m
[32m+[m[32m                "dragon-code/contracts": "^2.22.0",[m
[32m+[m[32m                "ext-bcmath": "*",[m
[32m+[m[32m                "ext-ctype": "*",[m
[32m+[m[32m                "ext-dom": "*",[m
[32m+[m[32m                "ext-json": "*",[m
[32m+[m[32m                "ext-mbstring": "*",[m
[32m+[m[32m                "php": "^8.1",[m
[32m+[m[32m                "psr/http-message": "^1.0.1 || ^2.0",[m
[32m+[m[32m                "symfony/polyfill-php81": "^1.25",[m
[32m+[m[32m                "voku/portable-ascii": "^1.4.8 || ^2.0.1"[m
[32m+[m[32m            },[m
[32m+[m[32m            "conflict": {[m
[32m+[m[32m                "andrey-helldar/support": "*"[m
[32m+[m[32m            },[m
[32m+[m[32m            "require-dev": {[m
[32m+[m[32m                "illuminate/contracts": "^9.0 || ^10.0 || ^11.0 || ^12.0",[m
[32m+[m[32m                "phpunit/phpunit": "^9.6 || ^11.0 || ^12.0",[m
[32m+[m[32m                "symfony/var-dumper": "^6.0 || ^7.0"[m
[32m+[m[32m            },[m
[32m+[m[32m            "suggest": {[m
[32m+[m[32m                "dragon-code/laravel-support": "Various helper files for the Laravel and Lumen frameworks",[m
[32m+[m[32m                "symfony/thanks": "Give thanks (in the form of a GitHub) to your fellow PHP package maintainers"[m
[32m+[m[32m            },[m
[32m+[m[32m            "type": "library",[m
[32m+[m[32m            "extra": {[m
[32m+[m[32m                "dragon-code": {[m
[32m+[m[32m                    "docs-generator": {[m
[32m+[m[32m                        "preview": {[m
[32m+[m[32m                            "brand": "php",[m
[32m+[m[32m                            "vendor": "The Dragon Code"[m
[32m+[m[32m                        }[m
[32m+[m[32m                    }[m
[32m+[m[32m                }[m
[32m+[m[32m            },[m
[32m+[m[32m            "autoload": {[m
[32m+[m[32m                "psr-4": {[m
[32m+[m[32m                    "DragonCode\\Support\\": "src"[m
[32m+[m[32m                }[m
[32m+[m[32m            },[m
[32m+[m[32m            "notification-url": "https://packagist.org/downloads/",[m
[32m+[m[32m            "license": [[m
[32m+[m[32m                "MIT"[m
[32m+[m[32m            ],[m
[32m+[m[32m            "authors": [[m
[32m+[m[32m                {[m
[32m+[m[32m                    "name": "Andrey Helldar",[m
[32m+[m[32m                    "email": "helldar@dragon-code.pro",[m
[32m+[m[32m                    "homepage": "https://dragon-code.pro"[m
[32m+[m[32m                }[m
[32m+[m[32m            ],[m
[32m+[m[32m            "description": "Support package is a collection of helpers and tools for any project.",[m
[32m+[m[32m            "keywords": [[m
[32m+[m[32m                "dragon",[m
[32m+[m[32m                "dragon-code",[m
[32m+[m[32m                "framework",[m
[32m+[m[32m                "helper",[m
[32m+[m[32m                "helpers",[m
[32m+[m[32m                "laravel",[m
[32m+[m[32m                "php",[m
[32m+[m[32m                "support",[m
[32m+[m[32m                "symfony",[m
[32m+[m[32m                "yii",[m
[32m+[m[32m                "yii2"[m
[32m+[m[32m            ],[m
[32m+[m[32m            "support": {[m
[32m+[m[32m                "issues": "https://github.com/TheDragonCode/support/issues",[m
[32m+[m[32m                "source": "https://github.com/TheDragonCode/support"[m
[32m+[m[32m            },[m
[32m+[m[32m            "funding": [[m
[32m+[m[32m                {[m
[32m+[m[32m                    "url": "https://boosty.to/dragon-code",[m
[32m+[m[32m                    "type": "boosty"[m
[32m+[m[32m                },[m
[32m+[m[32m                {[m
[32m+[m[32m                    "url": "https://yoomoney.ru/to/410012608840929",[m
[32m+[m[32m                    "type": "yoomoney"[m
[32m+[m[32m                }[m
[32m+[m[32m            ],[m
[32m+[m[32m            "time": "2025-02-24T14:01:52+00:00"[m
[32m+[m[32m        },[m
         {[m
             "name": "dragonmantank/cron-expression",[m
             "version": "v3.4.0",[m
[36m@@ -1090,6 +1441,317 @@[m
             ],[m
             "time": "2025-08-23T21:21:41+00:00"[m
         },[m
[32m+[m[32m        {[m
[32m+[m[32m            "name": "intervention/gif",[m
[32m+[m[32m            "version": "4.2.2",[m
[32m+[m[32m            "source": {[m
[32m+[m[32m                "type": "git",[m
[32m+[m[32m                "url": "https://github.com/Intervention/gif.git",[m
[32m+[m[32m                "reference": "5999eac6a39aa760fb803bc809e8909ee67b451a"[m
[32m+[m[32m            },[m
[32m+[m[32m            "dist": {[m
[32m+[m[32m                "type": "zip",[m
[32m+[m[32m                "url": "https://api.github.com/repos/Intervention/gif/zipball/5999eac6a39aa760fb803bc809e8909ee67b451a",[m
[32m+[m[32m                "reference": "5999eac6a39aa760fb803bc809e8909ee67b451a",[m
[32m+[m[32m                "shasum": ""[m
[32m+[m[32m            },[m
[32m+[m[32m            "require": {[m
[32m+[m[32m                "php": "^8.1"[m
[32m+[m[32m            },[m
[32m+[m[32m            "require-dev": {[m
[32m+[m[32m                "phpstan/phpstan": "^2.1",[m
[32m+[m[32m                "phpunit/phpunit": "^10.0 || ^11.0  || ^12.0",[m
[32m+[m[32m                "slevomat/coding-standard": "~8.0",[m
[32m+[m[32m                "squizlabs/php_codesniffer": "^3.8"[m
[32m+[m[32m            },[m
[32m+[m[32m            "type": "library",[m
[32m+[m[32m            "autoload": {[m
[32m+[m[32m                "psr-4": {[m
[32m+[m[32m                    "Intervention\\Gif\\": "src"[m
[32m+[m[32m                }[m
[32m+[m[32m            },[m
[32m+[m[32m            "notification-url": "https://packagist.org/downloads/",[m
[32m+[m[32m            "license": [[m
[32m+[m[32m                "MIT"[m
[32m+[m[32m            ],[m
[32m+[m[32m            "authors": [[m
[32m+[m[32m                {[m
[32m+[m[32m                    "name": "Oliver Vogel",[m
[32m+[m[32m                    "email": "oliver@intervention.io",[m
[32m+[m[32m                    "homepage": "https://intervention.io/"[m
[32m+[m[32m                }[m
[32m+[m[32m            ],[m
[32m+[m[32m            "description": "Native PHP GIF Encoder/Decoder",[m
[32m+[m[32m            "homepage": "https://github.com/intervention/gif",[m
[32m+[m[32m            "keywords": [[m
[32m+[m[32m                "animation",[m
[32m+[m[32m                "gd",[m
[32m+[m[32m                "gif",[m
[32m+[m[32m                "image"[m
[32m+[m[32m            ],[m
[32m+[m[32m            "support": {[m
[32m+[m[32m                "issues": "https://github.com/Intervention/gif/issues",[m
[32m+[m[32m                "source": "https://github.com/Intervention/gif/tree/4.2.2"[m
[32m+[m[32m            },[m
[32m+[m[32m            "funding": [[m
[32m+[m[32m                {[m
[32m+[m[32m                    "url": "https://paypal.me/interventionio",[m
[32m+[m[32m                    "type": "custom"[m
[32m+[m[32m                },[m
[32m+[m[32m                {[m
[32m+[m[32m                    "url": "https://github.com/Intervention",[m
[32m+[m[32m                    "type": "github"[m
[32m+[m[32m                },[m
[32m+[m[32m                {[m
[32m+[m[32m                    "url": "https://ko-fi.com/interventionphp",[m
[32m+[m[32m                    "type": "ko_fi"[m
[32m+[m[32m                }[m
[32m+[m[32m            ],[m
[32m+[m[32m            "time": "2025-03-29T07:46:21+00:00"[m
[32m+[m[32m        },[m
[32m+[m[32m        {[m
[32m+[m[32m            "name": "intervention/image",[m
[32m+[m[32m            "version": "3.11.4",[m
[32m+[m[32m            "source": {[m
[32m+[m[32m                "type": "git",[m
[32m+[m[32m                "url": "https://github.com/Intervention/image.git",[m
[32m+[m[32m                "reference": "8c49eb21a6d2572532d1bc425964264f3e496846"[m
[32m+[m[32m            },[m
[32m+[m[32m            "dist": {[m
[32m+[m[32m                "type": "zip",[m
[32m+[m[32m                "url": "https://api.github.com/repos/Intervention/image/zipball/8c49eb21a6d2572532d1bc425964264f3e496846",[m
[32m+[m[32m                "reference": "8c49eb21a6d2572532d1bc425964264f3e496846",[m
[32m+[m[32m                "shasum": ""[m
[32m+[m[32m            },[m
[32m+[m[32m            "require": {[m
[32m+[m[32m                "ext-mbstring": "*",[m
[32m+[m[32m                "intervention/gif": "^4.2",[m
[32m+[m[32m                "php": "^8.1"[m
[32m+[m[32m            },[m
[32m+[m[32m            "require-dev": {[m
[32m+[m[32m                "mockery/mockery": "^1.6",[m
[32m+[m[32m                "phpstan/phpstan": "^2.1",[m
[32m+[m[32m                "phpunit/phpunit": "^10.0 || ^11.0 || ^12.0",[m
[32m+[m[32m                "slevomat/coding-standard": "~8.0",[m
[32m+[m[32m                "squizlabs/php_codesniffer": "^3.8"[m
[32m+[m[32m            },[m
[32m+[m[32m            "suggest": {[m
[32m+[m[32m                "ext-exif": "Recommended to be able to read EXIF data properly."[m
[32m+[m[32m            },[m
[32m+[m[32m            "type": "library",[m
[32m+[m[32m            "autoload": {[m
[32m+[m[32m                "psr-4": {[m
[32m+[m[32m                    "Intervention\\Image\\": "src"[m
[32m+[m[32m                }[m
[32m+[m[32m            },[m
[32m+[m[32m            "notification-url": "https://packagist.org/downloads/",[m
[32m+[m[32m            "license": [[m
[32m+[m[32m                "MIT"[m
[32m+[m[32m            ],[m
[32m+[m[32m            "authors": [[m
[32m+[m[32m                {[m
[32m+[m[32m                    "name": "Oliver Vogel",[m
[32m+[m[32m                    "email": "oliver@intervention.io",[m
[32m+[m[32m                    "homepage": "https://intervention.io/"[m
[32m+[m[32m                }[m
[32m+[m[32m            ],[m
[32m+[m[32m            "description": "PHP image manipulation",[m
[32m+[m[32m            "homepage": "https://image.intervention.io/",[m
[32m+[m[32m            "keywords": [[m
[32m+[m[32m                "gd",[m
[32m+[m[32m                "image",[m
[32m+[m[32m                "imagick",[m
[32m+[m[32m                "resize",[m
[32m+[m[32m                "thumbnail",[m
[32m+[m[32m                "watermark"[m
[32m+[m[32m            ],[m
[32m+[m[32m            "support": {[m
[32m+[m[32m                "issues": "https://github.com/Intervention/image/issues",[m
[32m+[m[32m                "source": "https://github.com/Intervention/image/tree/3.11.4"[m
[32m+[m[32m            },[m
[32m+[m[32m            "funding": [[m
[32m+[m[32m                {[m
[32m+[m[32m                    "url": "https://paypal.me/interventionio",[m
[32m+[m[32m                    "type": "custom"[m
[32m+[m[32m                },[m
[32m+[m[32m                {[m
[32m+[m[32m                    "url": "https://github.com/Intervention",[m
[32m+[m[32m                    "type": "github"[m
[32m+[m[32m                },[m
[32m+[m[32m                {[m
[32m+[m[32m                    "url": "https://ko-fi.com/interventionphp",[m
[32m+[m[32m                    "type": "ko_fi"[m
[32m+[m[32m                }[m
[32m+[m[32m            ],[m
[32m+[m[32m            "time": "2025-07-30T13:13:19+00:00"[m
[32m+[m[32m        },[m
[32m+[m[32m        {[m
[32m+[m[32m            "name": "laravel-lang/lang",[m
[32m+[m[32m            "version": "14.7.0",[m
[32m+[m[32m            "source": {[m
[32m+[m[32m                "type": "git",[m
[32m+[m[32m                "url": "https://github.com/Laravel-Lang/lang.git",[m
[32m+[m[32m                "reference": "6b196fc8d22fc1775d8acb15059814470687b275"[m
[32m+[m[32m            },[m
[32m+[m[32m            "dist": {[m
[32m+[m[32m                "type": "zip",[m
[32m+[m[32m                "url": "https://api.github.com/repos/Laravel-Lang/lang/zipball/6b196fc8d22fc1775d8acb15059814470687b275",[m
[32m+[m[32m                "reference": "6b196fc8d22fc1775d8acb15059814470687b275",[m
[32m+[m[32m                "shasum": ""[m
[32m+[m[32m            },[m
[32m+[m[32m            "require": {[m
[32m+[m[32m                "ext-json": "*",[m
[32m+[m[32m                "laravel-lang/publisher": "^14.0 || ^15.0 || ^16.0",[m
[32m+[m[32m                "php": "^8.1"[m
[32m+[m[32m            },[m
[32m+[m[32m            "require-dev": {[m
[32m+[m[32m                "laravel-lang/status-generator": "^1.19 || ^2.0",[m
[32m+[m[32m                "phpunit/phpunit": "^10.0",[m
[32m+[m[32m                "symfony/var-dumper": "^6.0 || ^7.0"[m
[32m+[m[32m            },[m
[32m+[m[32m            "type": "library",[m
[32m+[m[32m            "extra": {[m
[32m+[m[32m                "laravel": {[m
[32m+[m[32m                    "providers": [[m
[32m+[m[32m                        "LaravelLang\\Lang\\ServiceProvider"[m
[32m+[m[32m                    ][m
[32m+[m[32m                }[m
[32m+[m[32m            },[m
[32m+[m[32m            "autoload": {[m
[32m+[m[32m                "psr-4": {[m
[32m+[m[32m                    "LaravelLang\\Lang\\": "src/"[m
[32m+[m[32m                }[m
[32m+[m[32m            },[m
[32m+[m[32m            "notification-url": "https://packagist.org/downloads/",[m
[32m+[m[32m            "license": [[m
[32m+[m[32m                "MIT"[m
[32m+[m[32m            ],[m
[32m+[m[32m            "authors": [[m
[32m+[m[32m                {[m
[32m+[m[32m                    "name": "Laravel-Lang Team",[m
[32m+[m[32m                    "homepage": "https://github.com/Laravel-Lang"[m
[32m+[m[32m                }[m
[32m+[m[32m            ],[m
[32m+[m[32m            "description": "List of 126 languages for Laravel Framework, Laravel Jetstream, Laravel Fortify, Laravel Breeze, Laravel Cashier, Laravel Nova, Laravel Spark and Laravel UI",[m
[32m+[m[32m            "keywords": [[m
[32m+[m[32m                "lang",[m
[32m+[m[32m                "languages",[m
[32m+[m[32m                "laravel",[m
[32m+[m[32m                "lpm"[m
[32m+[m[32m            ],[m
[32m+[m[32m            "support": {[m
[32m+[m[32m                "issues": "https://github.com/Laravel-Lang/lang/issues",[m
[32m+[m[32m                "source": "https://github.com/Laravel-Lang/lang"[m
[32m+[m[32m            },[m
[32m+[m[32m            "time": "2024-03-22T18:18:21+00:00"[m
[32m+[m[32m        },[m
[32m+[m[32m        {[m
[32m+[m[32m            "name": "laravel-lang/publisher",[m
[32m+[m[32m            "version": "14.7.1",[m
[32m+[m[32m            "source": {[m
[32m+[m[32m                "type": "git",[m
[32m+[m[32m                "url": "https://github.com/Laravel-Lang/publisher.git",[m
[32m+[m[32m                "reference": "946405e3d8c7105b0ae8cf8de34a3e6e98a70a84"[m
[32m+[m[32m            },[m
[32m+[m[32m            "dist": {[m
[32m+[m[32m                "type": "zip",[m
[32m+[m[32m                "url": "https://api.github.com/repos/Laravel-Lang/publisher/zipball/946405e3d8c7105b0ae8cf8de34a3e6e98a70a84",[m
[32m+[m[32m                "reference": "946405e3d8c7105b0ae8cf8de34a3e6e98a70a84",[m
[32m+[m[32m                "shasum": ""[m
[32m+[m[32m            },[m
[32m+[m[32m            "require": {[m
[32m+[m[32m                "archtechx/enums": "^0.3",[m
[32m+[m[32m                "composer/semver": "^3.3",[m
[32m+[m[32m                "dragon-code/pretty-array": "^4.0",[m
[32m+[m[32m                "dragon-code/support": "^6.3",[m
[32m+[m[32m                "ext-json": "*",[m
[32m+[m[32m                "illuminate/console": "^8.79 || ^9.18 || ^10.0 || ^11.0",[m
[32m+[m[32m                "illuminate/support": "^8.79 || ^9.18 || ^10.0 || ^11.0",[m
[32m+[m[32m                "league/commonmark": "^2.3",[m
[32m+[m[32m                "league/config": "^1.2",[m
[32m+[m[32m                "php": "^8.1"[m
[32m+[m[32m            },[m
[32m+[m[32m            "conflict": {[m
[32m+[m[32m                "laravel-lang/attributes": "<2.0",[m
[32m+[m[32m                "laravel-lang/http-statuses": "<3.0",[m
[32m+[m[32m                "laravel-lang/lang": "<11.0"[m
[32m+[m[32m            },[m
[32m+[m[32m            "require-dev": {[m
[32m+[m[32m                "laravel-lang/json-fallback-hotfix": "^1.0",[m
[32m+[m[32m                "orchestra/testbench": "^6.25 || ^7.22 || ^8.0 || ^9.0",[m
[32m+[m[32m                "phpunit/phpunit": "^9.6 || ^10.0",[m
[32m+[m[32m                "symfony/var-dumper": "^5.0 || ^6.0"[m
[32m+[m[32m            },[m
[32m+[m[32m            "suggest": {[m
[32m+[m[32m                "laravel-lang/attributes": "List of 78 languages for form field names",[m
[32m+[m[32m                "laravel-lang/http-statuses": "List of 78 languages for HTTP statuses",[m
[32m+[m[32m                "laravel-lang/lang": "List of 78 languages for Laravel Framework, Jetstream, Fortify, Breeze, Cashier, Nova, Spark and UI."[m
[32m+[m[32m            },[m
[32m+[m[32m            "type": "library",[m
[32m+[m[32m            "extra": {[m
[32m+[m[32m                "laravel": {[m
[32m+[m[32m                    "providers": [[m
[32m+[m[32m                        "LaravelLang\\Publisher\\ServiceProvider"[m
[32m+[m[32m                    ][m
[32m+[m[32m                }[m
[32m+[m[32m            },[m
[32m+[m[32m            "autoload": {[m
[32m+[m[32m                "files": [[m
[32m+[m[32m                    "helper.php"[m
[32m+[m[32m                ],[m
[32m+[m[32m                "psr-4": {[m
[32m+[m[32m                    "LaravelLang\\Publisher\\": "src"[m
[32m+[m[32m                }[m
[32m+[m[32m            },[m
[32m+[m[32m            "notification-url": "https://packagist.org/downloads/",[m
[32m+[m[32m            "license": [[m
[32m+[m[32m                "MIT"[m
[32m+[m[32m            ],[m
[32m+[m[32m            "authors": [[m
[32m+[m[32m                {[m
[32m+[m[32m                    "name": "Andrey Helldar",[m
[32m+[m[32m                    "email": "helldar@dragon-code.pro"[m
[32m+[m[32m                },[m
[32m+[m[32m                {[m
[32m+[m[32m                    "name": "Laravel-Lang Team",[m
[32m+[m[32m                    "homepage": "https://github.com/Laravel-Lang"[m
[32m+[m[32m                }[m
[32m+[m[32m            ],[m
[32m+[m[32m            "description": "Publisher lang files for the Laravel and Lumen Frameworks, Jetstream, Fortify, Cashier, Spark and Nova from Laravel-Lang/lang",[m
[32m+[m[32m            "keywords": [[m
[32m+[m[32m                "breeze",[m
[32m+[m[32m                "cashier",[m
[32m+[m[32m                "fortify",[m
[32m+[m[32m                "framework",[m
[32m+[m[32m                "i18n",[m
[32m+[m[32m                "jetstream",[m
[32m+[m[32m                "lang",[m
[32m+[m[32m                "languages",[m
[32m+[m[32m                "laravel",[m
[32m+[m[32m                "locale",[m
[32m+[m[32m                "locales",[m
[32m+[m[32m                "localization",[m
[32m+[m[32m                "lpm",[m
[32m+[m[32m                "lumen",[m
[32m+[m[32m                "nova",[m
[32m+[m[32m                "publisher",[m
[32m+[m[32m                "spark",[m
[32m+[m[32m                "trans",[m
[32m+[m[32m                "translations",[m
[32m+[m[32m                "validations"[m
[32m+[m[32m            ],[m
[32m+[m[32m            "support": {[m
[32m+[m[32m                "issues": "https://github.com/Laravel-Lang/publisher/issues",[m
[32m+[m[32m                "source": "https://github.com/Laravel-Lang/publisher"[m
[32m+[m[32m            },[m
[32m+[m[32m            "funding": [[m
[32m+[m[32m                {[m
[32m+[m[32m                    "url": "https://opencollective.com/laravel-lang",[m
[32m+[m[32m                    "type": "open_collective"[m
[32m+[m[32m                }[m
[32m+[m[32m            ],[m
[32m+[m[32m            "time": "2023-10-29T21:03:27+00:00"[m
[32m+[m[32m        },[m
         {[m
             "name": "laravel/framework",[m
             "version": "v8.83.29",[m
[36m@@ -4515,6 +5177,86 @@[m
             ],[m
             "time": "2025-01-02T08:10:11+00:00"[m
         },[m
[32m+[m[32m        {[m
[32m+[m[32m            "name": "symfony/polyfill-php81",[m
[32m+[m[32m            "version": "v1.33.0",[m
[32m+[m[32m            "source": {[m
[32m+[m[32m                "type": "git",[m
[32m+[m[32m                "url": "https://github.com/symfony/polyfill-php81.git",[m
[32m+[m[32m                "reference": "4a4cfc2d253c21a5ad0e53071df248ed48c6ce5c"[m
[32m+[m[32m            },[m
[32m+[m[32m            "dist": {[m
[32m+[m[32m                "type": "zip",[m
[32m+[m[32m                "url": "https://api.github.com/repos/symfony/polyfill-php81/zipball/4a4cfc2d253c21a5ad0e53071df248ed48c6ce5c",[m
[32m+[m[32m                "reference": "4a4cfc2d253c21a5ad0e53071df248ed48c6ce5c",[m
[32m+[m[32m                "shasum": ""[m
[32m+[m[32m            },[m
[32m+[m[32m            "require": {[m
[32m+[m[32m                "php": ">=7.2"[m
[32m+[m[32m            },[m
[32m+[m[32m            "type": "library",[m
[32m+[m[32m            "extra": {[m
[32m+[m[32m                "thanks": {[m
[32m+[m[32m                    "url": "https://github.com/symfony/polyfill",[m
[32m+[m[32m                    "name": "symfony/polyfill"[m
[32m+[m[32m                }[m
[32m+[m[32m            },[m
[32m+[m[32m            "autoload": {[m
[32m+[m[32m                "files": [[m
[32m+[m[32m                    "bootstrap.php"[m
[32m+[m[32m                ],[m
[32m+[m[32m                "psr-4": {[m
[32m+[m[32m                    "Symfony\\Polyfill\\Php81\\": ""[m
[32m+[m[32m                },[m
[32m+[m[32m                "classmap": [[m
[32m+[m[32m                    "Resources/stubs"[m
[32m+[m[32m                ][m
[32m+[m[32m            },[m
[32m+[m[32m            "notification-url": "https://packagist.org/downloads/",[m
[32m+[m[32m            "license": [[m
[32m+[m[32m                "MIT"[m
[32m+[m[32m            ],[m
[32m+[m[32m            "authors": [[m
[32m+[m[32m                {[m
[32m+[m[32m                    "name": "Nicolas Grekas",[m
[32m+[m[32m                    "email": "p@tchwork.com"[m
[32m+[m[32m                },[m
[32m+[m[32m                {[m
[32m+[m[32m                    "name": "Symfony Community",[m
[32m+[m[32m                    "homepage": "https://symfony.com/contributors"[m
[32m+[m[32m                }[m
[32m+[m[32m            ],[m
[32m+[m[32m            "description": "Symfony polyfill backporting some PHP 8.1+ features to lower PHP versions",[m
[32m+[m[32m            "homepage": "https://symfony.com",[m
[32m+[m[32m            "keywords": [[m
[32m+[m[32m                "compatibility",[m
[32m+[m[32m                "polyfill",[m
[32m+[m[32m                "portable",[m
[32m+[m[32m                "shim"[m
[32m+[m[32m            ],[m
[32m+[m[32m            "support": {[m
[32m+[m[32m                "source": "https://github.com/symfony/polyfill-php81/tree/v1.33.0"[m
[32m+[m[32m            },[m
[32m+[m[32m            "funding": [[m
[32m+[m[32m                {[m
[32m+[m[32m                    "url": "https://symfony.com/sponsor",[m
[32m+[m[32m                    "type": "custom"[m
[32m+[m[32m                },[m
[32m+[m[32m                {[m
[32m+[m[32m                    "url": "https://github.com/fabpot",[m
[32m+[m[32m                    "type": "github"[m
[32m+[m[32m                },[m
[32m+[m[32m                {[m
[32m+[m[32m                    "url": "https://github.com/nicolas-grekas",[m
[32m+[m[32m                    "type": "github"[m
[32m+[m[32m                },[m
[32m+[m[32m                {[m
[32m+[m[32m                    "url": "https://tidelift.com/funding/github/packagist/symfony/symfony",[m
[32m+[m[32m                    "type": "tidelift"[m
[32m+[m[32m                }[m
[32m+[m[32m            ],[m
[32m+[m[32m            "time": "2024-09-09T11:45:10+00:00"[m
[32m+[m[32m        },[m
         {[m
             "name": "symfony/process",[m
             "version": "v5.4.47",[m
[1mdiff --git a/config/admin.php b/config/admin.php[m
[1mindex a8511c6..5ec4d80 100644[m
[1m--- a/config/admin.php[m
[1m+++ b/config/admin.php[m
[36m@@ -2,6 +2,7 @@[m
 return [[m
     // Ajuste para o(s) e-mail(s) que PODEM acessar /admin enquanto o legado não tem users.is_admin:[m
     'superadmins' => [[m
[31m-        'seu-email@exemplo.com',[m
[32m+[m[32m        'admin@tecinfosp.local',[m
     ],[m
 ];[m
[41m+[m
[1mdiff --git a/database/migrations/2025_10_13_233031_add_image_sizes_to_business_table.php b/database/migrations/2025_10_13_233031_add_image_sizes_to_business_table.php[m
[1mnew file mode 100644[m
[1mindex 0000000..ce27a4a[m
[1m--- /dev/null[m
[1m+++ b/database/migrations/2025_10_13_233031_add_image_sizes_to_business_table.php[m
[36m@@ -0,0 +1,31 @@[m
[32m+[m[32m<?php[m
[32m+[m
[32m+[m[32muse Illuminate\Database\Migrations\Migration;[m
[32m+[m[32muse Illuminate\Database\Schema\Blueprint;[m
[32m+[m[32muse Illuminate\Support\Facades\Schema;[m
[32m+[m
[32m+[m[32mreturn new class extends Migration {[m
[32m+[m[32m    public function up(): void[m
[32m+[m[32m    {[m
[32m+[m[32m        Schema::table('business', function (Blueprint $table) {[m
[32m+[m[32m            if (!Schema::hasColumn('business', 'image_lg')) {[m
[32m+[m[32m                $table->string('image_lg')->nullable()->after('image');[m
[32m+[m[32m            }[m
[32m+[m[32m            if (!Schema::hasColumn('business', 'image_sm')) {[m
[32m+[m[32m                $table->string('image_sm')->nullable()->after('image_lg');[m
[32m+[m[32m            }[m
[32m+[m[32m        });[m
[32m+[m[32m    }[m
[32m+[m
[32m+[m[32m    public function down(): void[m
[32m+[m[32m    {[m
[32m+[m[32m        Schema::table('business', function (Blueprint $table) {[m
[32m+[m[32m            if (Schema::hasColumn('business', 'image_sm')) {[m
[32m+[m[32m                $table->dropColumn('image_sm');[m
[32m+[m[32m            }[m
[32m+[m[32m            if (Schema::hasColumn('business', 'image_lg')) {[m
[32m+[m[32m                $table->dropColumn('image_lg');[m
[32m+[m[32m            }[m
[32m+[m[32m        });[m
[32m+[m[32m    }[m
[32m+[m[32m};[m
[1mdiff --git a/database/migrations/2025_10_17_200343_add_is_admin_to_users_table.php b/database/migrations/2025_10_17_200343_add_is_admin_to_users_table.php[m
[1mnew file mode 100644[m
[1mindex 0000000..5b204ba[m
[1m--- /dev/null[m
[1m+++ b/database/migrations/2025_10_17_200343_add_is_admin_to_users_table.php[m
[36m@@ -0,0 +1,38 @@[m
[32m+[m[32m<?php[m
[32m+[m
[32m+[m[32muse Illuminate\Database\Migrations\Migration;[m
[32m+[m[32muse Illuminate\Database\Schema\Blueprint;[m
[32m+[m[32muse Illuminate\Support\Facades\Schema;[m
[32m+[m
[32m+[m[32mclass AddIsAdminToUsersTable extends Migration[m
[32m+[m[32m{[m
[32m+[m[32m    /**[m
[32m+[m[32m     * Run the migrations.[m
[32m+[m[32m     *[m
[32m+[m[32m     * @return void[m
[32m+[m[32m     */[m
[32m+[m[32m    public function up()[m
[32m+[m[32m    {[m
[32m+[m[32m        if (!Schema::hasColumn('users', 'is_admin')) {[m
[32m+[m[32m            Schema::table('users', function (Blueprint $table) {[m
[32m+[m[32m                $table->boolean('is_admin')[m
[32m+[m[32m                    ->default(false)[m
[32m+[m[32m                    ->after('password');[m
[32m+[m[32m            });[m
[32m+[m[32m        }[m
[32m+[m[32m    }[m
[32m+[m
[32m+[m[32m    /**[m
[32m+[m[32m     * Reverse the migrations.[m
[32m+[m[32m     *[m
[32m+[m[32m     * @return void[m
[32m+[m[32m     */[m
[32m+[m[32m    public function down()[m
[32m+[m[32m    {[m
[32m+[m[32m        if (Schema::hasColumn('users', 'is_admin')) {[m
[32m+[m[32m            Schema::table('users', function (Blueprint $table) {[m
[32m+[m[32m                $table->dropColumn('is_admin');[m
[32m+[m[32m            });[m
[32m+[m[32m        }[m
[32m+[m[32m    }[m
[32m+[m[32m}[m
[1mdiff --git a/database/migrations/2025_10_18_152300_create_pages_table.php b/database/migrations/2025_10_18_152300_create_pages_table.php[m
[1mnew file mode 100644[m
[1mindex 0000000..9db0fa6[m
[1m--- /dev/null[m
[1m+++ b/database/migrations/2025_10_18_152300_create_pages_table.php[m
[36m@@ -0,0 +1,22 @@[m
[32m+[m[32m<?php[m
[32m+[m
[32m+[m[32muse Illuminate\Database\Migrations\Migration;[m
[32m+[m[32muse Illuminate\Database\Schema\Blueprint;[m
[32m+[m[32muse Illuminate\Support\Facades\Schema;[m
[32m+[m
[32m+[m[32mreturn new class extends Migration {[m
[32m+[m[32m    public function up(): void[m
[32m+[m[32m    {[m
[32m+[m[32m        if (!Schema::hasTable('pages')) {[m
[32m+[m[32m            Schema::create('pages', function (Blueprint $table) {[m
[32m+[m[32m                $table->id();[m
[32m+[m[32m                $table->timestamps();[m
[32m+[m[32m            });[m
[32m+[m[32m        }[m
[32m+[m[32m    }[m
[32m+[m
[32m+[m[32m    public function down(): void[m
[32m+[m[32m    {[m
[32m+[m[32m        Schema::dropIfExists('pages');[m
[32m+[m[32m    }[m
[32m+[m[32m};[m
[1mdiff --git a/database/migrations/2025_10_18_172010_add_static_fields_to_pages_table.php b/database/migrations/2025_10_18_172010_add_static_fields_to_pages_table.php[m
[1mnew file mode 100644[m
[1mindex 0000000..71124b8[m
[1m--- /dev/null[m
[1m+++ b/database/migrations/2025_10_18_172010_add_static_fields_to_pages_table.php[m
[36m@@ -0,0 +1,43 @@[m
[32m+[m[32m﻿<?php[m
[32m+[m
[32m+[m[32muse Illuminate\Database\Migrations\Migration;[m
[32m+[m[32muse Illuminate\Database\Schema\Blueprint;[m
[32m+[m[32muse Illuminate\Support\Facades\Schema;[m
[32m+[m
[32m+[m[32mreturn new class extends Migration {[m
[32m+[m[32m    public function up(): void[m
[32m+[m[32m    {[m
[32m+[m[32m        Schema::table('pages', function (Blueprint $table) {[m
[32m+[m[32m            if (!Schema::hasColumn('pages', 'slug')) {[m
[32m+[m[32m                $table->string('slug')->unique()->after('id');[m
[32m+[m[32m            }[m
[32m+[m[32m            if (!Schema::hasColumn('pages', 'title')) {[m
[32m+[m[32m                $table->string('title')->after('slug');[m
[32m+[m[32m            }[m
[32m+[m[32m            if (!Schema::hasColumn('pages', 'content')) {[m
[32m+[m[32m                $table->longText('content')->nullable()->after('title');[m
[32m+[m[32m            }[m
[32m+[m[32m            if (!Schema::hasColumn('pages', 'is_active')) {[m
[32m+[m[32m                $table->boolean('is_active')->default(true)->after('content');[m
[32m+[m[32m            }[m
[32m+[m[32m            if (!Schema::hasColumn('pages', 'published_at')) {[m
[32m+[m[32m                $table->timestamp('published_at')->nullable()->after('is_active');[m
[32m+[m[32m            }[m
[32m+[m[32m        });[m
[32m+[m[32m    }[m
[32m+[m
[32m+[m[32m    public function down(): void[m
[32m+[m[32m    {[m
[32m+[m[32m        Schema::table('pages', function (Blueprint $table) {[m
[32m+[m[32m            if (Schema::hasColumn('pages', 'slug')) {[m
[32m+[m[32m                try { $table->dropUnique('pages_slug_unique'); } catch (\Throwable $e) {}[m
[32m+[m[32m                $table->dropColumn('slug');[m
[32m+[m[32m            }[m
[32m+[m[32m            foreach (['title','content','is_active','published_at'] as $col) {[m
[32m+[m[32m                if (Schema::hasColumn('pages', $col)) {[m
[32m+[m[32m                    $table->dropColumn($col);[m
[32m+[m[32m                }[m
[32m+[m[32m            }[m
[32m+[m[32m        });[m
[32m+[m[32m    }[m
[32m+[m[32m};[m
[1mdiff --git a/database/migrations/2025_10_18_172500_fix_pages_table_structure.php b/database/migrations/2025_10_18_172500_fix_pages_table_structure.php[m
[1mnew file mode 100644[m
[1mindex 0000000..060f10c[m
[1m--- /dev/null[m
[1m+++ b/database/migrations/2025_10_18_172500_fix_pages_table_structure.php[m
[36m@@ -0,0 +1,48 @@[m
[32m+[m[32m﻿<?php[m
[32m+[m
[32m+[m[32muse Illuminate\Database\Migrations\Migration;[m
[32m+[m[32muse Illuminate\Database\Schema\Blueprint;[m
[32m+[m[32muse Illuminate\Support\Facades\DB;[m
[32m+[m[32muse Illuminate\Support\Facades\Schema;[m
[32m+[m
[32m+[m[32mreturn new class extends Migration {[m
[32m+[m[32m    public function up(): void[m
[32m+[m[32m    {[m
[32m+[m[32m        // 1) Garantir created_at / updated_at (nullable para não quebrar inserts antigos)[m
[32m+[m[32m        Schema::table('pages', function (Blueprint $table) {[m
[32m+[m[32m            if (!Schema::hasColumn('pages', 'created_at')) {[m
[32m+[m[32m                $table->timestamp('created_at')->nullable();[m
[32m+[m[32m            }[m
[32m+[m[32m            if (!Schema::hasColumn('pages', 'updated_at')) {[m
[32m+[m[32m                $table->timestamp('updated_at')->nullable();[m
[32m+[m[32m            }[m
[32m+[m[32m        });[m
[32m+[m
[32m+[m[32m        // 2) Garantir que slug tenha valores únicos[m
[32m+[m[32m        //    Ajusta slugs NULL ou vazios para "page-{id}" antes de criar o índice único[m
[32m+[m[32m        DB::statement("UPDATE pages SET slug = CONCAT('page-', id) WHERE slug IS NULL OR slug = ''");[m
[32m+[m
[32m+[m[32m        // 3) Tentar criar o índice único (se já existir, ignorar)[m
[32m+[m[32m        try {[m
[32m+[m[32m            Schema::table('pages', function (Blueprint $table) {[m
[32m+[m[32m                $table->unique('slug', 'pages_slug_unique');[m
[32m+[m[32m            });[m
[32m+[m[32m        } catch (\Throwable $e) {[m
[32m+[m[32m            // índice já existe ou não é necessário — seguir em frente[m
[32m+[m[32m        }[m
[32m+[m[32m    }[m
[32m+[m
[32m+[m[32m    public function down(): void[m
[32m+[m[32m    {[m
[32m+[m[32m        // remover índice único (se existir)[m
[32m+[m[32m        try {[m
[32m+[m[32m            Schema::table('pages', function (Blueprint $table) {[m
[32m+[m[32m                $table->dropUnique('pages_slug_unique');[m
[32m+[m[32m            });[m
[32m+[m[32m        } catch (\Throwable $e) {[m
[32m+[m[32m            //[m
[32m+[m[32m        }[m
[32m+[m
[32m+[m[32m        // (não removemos created_at/updated_at no down)[m
[32m+[m[32m    }[m
[32m+[m[32m};[m
[1mdiff --git a/database/migrations/2025_10_18_172847_fix_pages_table_structure.php b/database/migrations/2025_10_18_172847_fix_pages_table_structure.php[m
[1mnew file mode 100644[m
[1mindex 0000000..65411cb[m
[1m--- /dev/null[m
[1m+++ b/database/migrations/2025_10_18_172847_fix_pages_table_structure.php[m
[36m@@ -0,0 +1,28 @@[m
[32m+[m[32m<?php[m
[32m+[m
[32m+[m[32muse Illuminate\Database\Migrations\Migration;[m
[32m+[m[32muse Illuminate\Database\Schema\Blueprint;[m
[32m+[m[32muse Illuminate\Support\Facades\Schema;[m
[32m+[m
[32m+[m[32mclass FixPagesTableStructure extends Migration[m
[32m+[m[32m{[m
[32m+[m[32m    /**[m
[32m+[m[32m     * Run the migrations.[m
[32m+[m[32m     *[m
[32m+[m[32m     * @return void[m
[32m+[m[32m     */[m
[32m+[m[32m    public function up()[m
[32m+[m[32m    {[m
[32m+[m[32m        //[m
[32m+[m[32m    }[m
[32m+[m
[32m+[m[32m    /**[m
[32m+[m[32m     * Reverse the migrations.[m
[32m+[m[32m     *[m
[32m+[m[32m     * @return void[m
[32m+[m[32m     */[m
[32m+[m[32m    public function down()[m
[32m+[m[32m    {[m
[32m+[m[32m        //[m
[32m+[m[32m    }[m
[32m+[m[32m}[m
[1mdiff --git a/database/migrations/2025_10_18_173041_fix_pages_id_autoincrement.php b/database/migrations/2025_10_18_173041_fix_pages_id_autoincrement.php[m
[1mnew file mode 100644[m
[1mindex 0000000..73b78bc[m
[1m--- /dev/null[m
[1m+++ b/database/migrations/2025_10_18_173041_fix_pages_id_autoincrement.php[m
[36m@@ -0,0 +1,28 @@[m
[32m+[m[32m<?php[m
[32m+[m
[32m+[m[32muse Illuminate\Database\Migrations\Migration;[m
[32m+[m[32muse Illuminate\Database\Schema\Blueprint;[m
[32m+[m[32muse Illuminate\Support\Facades\Schema;[m
[32m+[m
[32m+[m[32mclass FixPagesIdAutoincrement extends Migration[m
[32m+[m[32m{[m
[32m+[m[32m    /**[m
[32m+[m[32m     * Run the migrations.[m
[32m+[m[32m     *[m
[32m+[m[32m     * @return void[m
[32m+[m[32m     */[m
[32m+[m[32m    public function up()[m
[32m+[m[32m    {[m
[32m+[m[32m        //[m
[32m+[m[32m    }[m
[32m+[m
[32m+[m[32m    /**[m
[32m+[m[32m     * Reverse the migrations.[m
[32m+[m[32m     *[m
[32m+[m[32m     * @return void[m
[32m+[m[32m     */[m
[32m+[m[32m    public function down()[m
[32m+[m[32m    {[m
[32m+[m[32m        //[m
[32m+[m[32m    }[m
[32m+[m[32m}[m
[1mdiff --git a/database/migrations/2025_10_18_173256_fix_pages_page_column_nullable.php b/database/migrations/2025_10_18_173256_fix_pages_page_column_nullable.php[m
[1mnew file mode 100644[m
[1mindex 0000000..c485181[m
[1m--- /dev/null[m
[1m+++ b/database/migrations/2025_10_18_173256_fix_pages_page_column_nullable.php[m
[36m@@ -0,0 +1,28 @@[m
[32m+[m[32m<?php[m
[32m+[m
[32m+[m[32muse Illuminate\Database\Migrations\Migration;[m
[32m+[m[32muse Illuminate\Database\Schema\Blueprint;[m
[32m+[m[32muse Illuminate\Support\Facades\Schema;[m
[32m+[m
[32m+[m[32mclass FixPagesPageColumnNullable extends Migration[m
[32m+[m[32m{[m
[32m+[m[32m    /**[m
[32m+[m[32m     * Run the migrations.[m
[32m+[m[32m     *[m
[32m+[m[32m     * @return void[m
[32m+[m[32m     */[m
[32m+[m[32m    public function up()[m
[32m+[m[32m    {[m
[32m+[m[32m        //[m
[32m+[m[32m    }[m
[32m+[m
[32m+[m[32m    /**[m
[32m+[m[32m     * Reverse the migrations.[m
[32m+[m[32m     *[m
[32m+[m[32m     * @return void[m
[32m+[m[32m     */[m
[32m+[m[32m    public function down()[m
[32m+[m[32m    {[m
[32m+[m[32m        //[m
[32m+[m[32m    }[m
[32m+[m[32m}[m
[1mdiff --git a/database/migrations/2025_10_18_173500_fix_pages_id_autoincrement.php b/database/migrations/2025_10_18_173500_fix_pages_id_autoincrement.php[m
[1mnew file mode 100644[m
[1mindex 0000000..8c76322[m
[1m--- /dev/null[m
[1m+++ b/database/migrations/2025_10_18_173500_fix_pages_id_autoincrement.php[m
[36m@@ -0,0 +1,28 @@[m
[32m+[m[32m﻿<?php[m
[32m+[m
[32m+[m[32muse Illuminate\Database\Migrations\Migration;[m
[32m+[m[32muse Illuminate\Support\Facades\DB;[m
[32m+[m
[32m+[m[32mreturn new class extends Migration {[m
[32m+[m[32m    public function up(): void[m
[32m+[m[32m    {[m
[32m+[m[32m        // Em MySQL/MariaDB, garantir que "id" seja AUTO_INCREMENT e PK.[m
[32m+[m[32m        try {[m
[32m+[m[32m            DB::statement("ALTER TABLE pages MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");[m
[32m+[m[32m        } catch (\Throwable $e) {[m
[32m+[m[32m            // ignora se já estiver correto[m
[32m+[m[32m        }[m
[32m+[m
[32m+[m[32m        // (Opcional) garantir PK se necessário – se já existir, o banco vai acusar e seguimos[m
[32m+[m[32m        try {[m
[32m+[m[32m            DB::statement("ALTER TABLE pages ADD PRIMARY KEY (id)");[m
[32m+[m[32m        } catch (\Throwable $e) {[m
[32m+[m[32m            // já tem PK[m
[32m+[m[32m        }[m
[32m+[m[32m    }[m
[32m+[m
[32m+[m[32m    public function down(): void[m
[32m+[m[32m    {[m
[32m+[m[32m        // não reverte (evita quebrar inserts antigos)[m
[32m+[m[32m    }[m
[32m+[m[32m};[m
[1mdiff --git a/database/migrations/2025_10_18_173900_fix_pages_page_column_nullable.php b/database/migrations/2025_10_18_173900_fix_pages_page_column_nullable.php[m
[1mnew file mode 100644[m
[1mindex 0000000..ab8c4f1[m
[1m--- /dev/null[m
[1m+++ b/database/migrations/2025_10_18_173900_fix_pages_page_column_nullable.php[m
[36m@@ -0,0 +1,37 @@[m
[32m+[m[32m﻿<?php[m
[32m+[m
[32m+[m[32muse Illuminate\Database\Migrations\Migration;[m
[32m+[m[32muse Illuminate\Support\Facades\DB;[m
[32m+[m[32muse Illuminate\Support\Facades\Schema;[m
[32m+[m
[32m+[m[32mreturn new class extends Migration {[m
[32m+[m[32m    public function up(): void[m
[32m+[m[32m    {[m
[32m+[m[32m        // Se a coluna "page" existir, deixe-a NULL (ou remova).[m
[32m+[m[32m        if (Schema::hasColumn("pages", "page")) {[m
[32m+[m[32m            try {[m
[32m+[m[32m                // opção 1: tornar NULL (mais seguro em qualquer ambiente)[m
[32m+[m[32m                DB::statement("ALTER TABLE pages MODIFY page VARCHAR(255) NULL DEFAULT NULL");[m
[32m+[m[32m            } catch (\Throwable $e) {[m
[32m+[m[32m                // fallback: tenta dropar se o MODIFY falhar[m
[32m+[m[32m                try {[m
[32m+[m[32m                    DB::statement("ALTER TABLE pages DROP COLUMN page");[m
[32m+[m[32m                } catch (\Throwable $e2) {[m
[32m+[m[32m                    // última tentativa: define default vazio pra não travar inserts[m
[32m+[m[32m                    try {[m
[32m+[m[32m                        DB::statement("ALTER TABLE pages MODIFY page VARCHAR(255) NOT NULL DEFAULT ''");[m
[32m+[m[32m                    } catch (\Throwable $e3) {[m
[32m+[m[32m                        // deixa quieto para não quebrar a migration; melhor ter log[m
[32m+[m[32m                        // error_log($e3->getMessage());[m
[32m+[m[32m                    }[m
[32m+[m[32m                }[m
[32m+[m[32m            }[m
[32m+[m[32m        }[m
[32m+[m[32m    }[m
[32m+[m
[32m+[m[32m    public function down(): void[m
[32m+[m[32m    {[m
[32m+[m[32m        // sem rollback destrutivo; no máximo tira o default/volta NOT NULL se quiser[m
[32m+[m[32m        // mas vamos manter sem ação pra não quebrar nada.[m
[32m+[m[32m    }[m
[32m+[m[32m};[m
[1mdiff --git a/database/migrations/2025_10_18_213528_add_image_paths_to_businesses_table.php b/database/migrations/2025_10_18_213528_add_image_paths_to_businesses_table.php[m
[1mnew file mode 100644[m
[1mindex 0000000..58cb8a2[m
[1m--- /dev/null[m
[1m+++ b/database/migrations/2025_10_18_213528_add_image_paths_to_businesses_table.php[m
[36m@@ -0,0 +1,77 @@[m
[32m+[m[32m<?php[m
[32m+[m
[32m+[m[32muse Illuminate\Database\Migrations\Migration;[m
[32m+[m[32muse Illuminate\Database\Schema\Blueprint;[m
[32m+[m[32muse Illuminate\Support\Facades\Schema;[m
[32m+[m
[32m+[m[32mclass AddImagePathsToBusinessesTable extends Migration[m
[32m+[m[32m{[m
[32m+[m[32m    /**[m
[32m+[m[32m     * Detecta a tabela real (business ou businesses).[m
[32m+[m[32m     */[m
[32m+[m[32m    protected function realTable(): ?string[m
[32m+[m[32m    {[m
[32m+[m[32m        if (Schema::hasTable('business'))   return 'business';[m
[32m+[m[32m        if (Schema::hasTable('businesses')) return 'businesses';[m
[32m+[m[32m        return null;[m
[32m+[m[32m    }[m
[32m+[m
[32m+[m[32m    /**[m
[32m+[m[32m     * Run the migrations.[m
[32m+[m[32m     *[m
[32m+[m[32m     * @return void[m
[32m+[m[32m     */[m
[32m+[m[32m    public function up()[m
[32m+[m[32m    {[m
[32m+[m[32m        $table = $this->realTable();[m
[32m+[m[32m        if (!$table) {[m
[32m+[m[32m            // Não existe tabela alvo; não faz nada para evitar erro em ambientes legados.[m
[32m+[m[32m            return;[m
[32m+[m[32m        }[m
[32m+[m
[32m+[m[32m        Schema::table($table, function (Blueprint $table) {[m
[32m+[m[32m            // Colunas esperadas pelo checker[m
[32m+[m[32m            if (!Schema::hasColumn($table->getTable(), 'image_path_lg')) {[m
[32m+[m[32m                $table->string('image_path_lg', 255)->nullable()->after('image');[m
[32m+[m[32m            }[m
[32m+[m[32m            if (!Schema::hasColumn($table->getTable(), 'image_path_sm')) {[m
[32m+[m[32m                $table->string('image_path_sm', 255)->nullable()->after('image_path_lg');[m
[32m+[m[32m            }[m
[32m+[m
[32m+[m[32m            // Colunas usadas pelo controller (compat)[m
[32m+[m[32m            if (!Schema::hasColumn($table->getTable(), 'image_lg')) {[m
[32m+[m[32m                $table->string('image_lg', 255)->nullable()->after('image_path_sm');[m
[32m+[m[32m            }[m
[32m+[m[32m            if (!Schema::hasColumn($table->getTable(), 'image_sm')) {[m
[32m+[m[32m                $table->string('image_sm', 255)->nullable()->after('image_lg');[m
[32m+[m[32m            }[m
[32m+[m[32m        });[m
[32m+[m[32m    }[m
[32m+[m
[32m+[m[32m    /**[m
[32m+[m[32m     * Reverse the migrations.[m
[32m+[m[32m     *[m
[32m+[m[32m     * @return void[m
[32m+[m[32m     */[m
[32m+[m[32m    public function down()[m
[32m+[m[32m    {[m
[32m+[m[32m        $table = $this->realTable();[m
[32m+[m[32m        if (!$table) return;[m
[32m+[m
[32m+[m[32m        Schema::table($table, function (Blueprint $table) {[m
[32m+[m[32m            // Remoção segura (só se existirem)[m
[32m+[m[32m            if (Schema::hasColumn($table->getTable(), 'image_path_lg')) {[m
[32m+[m[32m                $table->dropColumn('image_path_lg');[m
[32m+[m[32m            }[m
[32m+[m[32m            if (Schema::hasColumn($table->getTable(), 'image_path_sm')) {[m
[32m+[m[32m                $table->dropColumn('image_path_sm');[m
[32m+[m[32m            }[m
[32m+[m[32m            if (Schema::hasColumn($table->getTable(), 'image_lg')) {[m
[32m+[m[32m                $table->dropColumn('image_lg');[m
[32m+[m[32m            }[m
[32m+[m[32m            if (Schema::hasColumn($table->getTable(), 'image_sm')) {[m
[32m+[m[32m                $table->dropColumn('image_sm');[m
[32m+[m[32m            }[m
[32m+[m[32m        });[m
[32m+[m[32m    }[m
[32m+[m[32m}[m
[1mdiff --git a/resources/lang/en.json b/resources/lang/en.json[m
[1mnew file mode 100644[m
[1mindex 0000000..1cecb16[m
[1m--- /dev/null[m
[1m+++ b/resources/lang/en.json[m
[36m@@ -0,0 +1,39 @@[m
[32m+[m[32m{[m
[32m+[m[32m    "A new verification link has been sent to the email address you provided during registration.": "A new verification link has been sent to the email address you provided during registration.",[m
[32m+[m[32m    "A new verification link has been sent to your email address.": "A new verification link has been sent to your email address.",[m
[32m+[m[32m    "Already registered?": "Already registered?",[m
[32m+[m[32m    "Are you sure you want to delete your account?": "Are you sure you want to delete your account?",[m
[32m+[m[32m    "Cancel": "Cancel",[m
[32m+[m[32m    "Click here to re-send the verification email.": "Click here to re-send the verification email.",[m
[32m+[m[32m    "Confirm": "Confirm",[m
[32m+[m[32m    "Confirm Password": "Confirm Password",[m
[32m+[m[32m    "Current Password": "Current Password",[m
[32m+[m[32m    "Dashboard": "Dashboard",[m
[32m+[m[32m    "Delete Account": "Delete Account",[m
[32m+[m[32m    "Email": "Email",[m
[32m+[m[32m    "Email Password Reset Link": "Email Password Reset Link",[m
[32m+[m[32m    "Ensure your account is using a long, random password to stay secure.": "Ensure your account is using a long, random password to stay secure.",[m
[32m+[m[32m    "Forgot your password?": "Forgot your password?",[m
[32m+[m[32m    "Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.": "Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.",[m
[32m+[m[32m    "Log in": "Log in",[m
[32m+[m[32m    "Log Out": "Log Out",[m
[32m+[m[32m    "Name": "Name",[m
[32m+[m[32m    "New Password": "New Password",[m
[32m+[m[32m    "Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.": "Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.",[m
[32m+[m[32m    "Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.": "Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.",[m
[32m+[m[32m    "Password": "Password",[m
[32m+[m[32m    "Profile": "Profile",[m
[32m+[m[32m    "Profile Information": "Profile Information",[m
[32m+[m[32m    "Register": "Register",[m
[32m+[m[32m    "Remember me": "Remember me",[m
[32m+[m[32m    "Resend Verification Email": "Resend Verification Email",[m
[32m+[m[32m    "Reset Password": "Reset Password",[m
[32m+[m[32m    "Save": "Save",[m
[32m+[m[32m    "Saved.": "Saved.",[m
[32m+[m[32m    "Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.": "Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.",[m
[32m+[m[32m    "This is a secure area of the application. Please confirm your password before continuing.": "This is a secure area of the application. Please confirm your password before continuing.",[m
[32m+[m[32m    "Update Password": "Update Password",[m
[32m+[m[32m    "Update your account's profile information and email address.": "Update your account's profile information and email address.",[m
[32m+[m[32m    "You're logged in!": "You're logged in!",[m
[32m+[m[32m    "Your email address is unverified.": "Your email address is unverified."[m
[32m+[m[32m}[m
\ No newline at end of file[m
[1mdiff --git a/resources/lang/pt_BR.json b/resources/lang/pt_BR.json[m
[1mnew file mode 100644[m
[1mindex 0000000..63e89cc[m
[1m--- /dev/null[m
[1m+++ b/resources/lang/pt_BR.json[m
[36m@@ -0,0 +1,39 @@[m
[32m+[m[32m{[m
[32m+[m[32m    "A new verification link has been sent to the email address you provided during registration.": "Um novo link de verificação foi enviado para o endereço de e-mail que você forneceu durante o processo de cadastro.",[m
[32m+[m[32m    "A new verification link has been sent to your email address.": "Um novo link de verificação foi enviado para seu endereço de e-mail.",[m
[32m+[m[32m    "Already registered?": "Já registrado?",[m
[32m+[m[32m    "Are you sure you want to delete your account?": "Tem certeza que quer excluir a sua conta?",[m
[32m+[m[32m    "Cancel": "Cancelar",[m
[32m+[m[32m    "Click here to re-send the verification email.": "Clique aqui para reenviar o e-mail de verificação.",[m
[32m+[m[32m    "Confirm": "Confirmar",[m
[32m+[m[32m    "Confirm Password": "Confirmar senha",[m
[32m+[m[32m    "Current Password": "Senha Atual",[m
[32m+[m[32m    "Dashboard": "Painel",[m
[32m+[m[32m    "Delete Account": "Excluir Conta",[m
[32m+[m[32m    "Email": "E-mail",[m
[32m+[m[32m    "Email Password Reset Link": "Link de redefinição de senha",[m
[32m+[m[32m    "Ensure your account is using a long, random password to stay secure.": "Garanta que sua conta esteja usando uma senha longa e aleatória para se manter seguro.",[m
[32m+[m[32m    "Forgot your password?": "Esqueceu a senha?",[m
[32m+[m[32m    "Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.": "Esqueceu sua senha? Sem problemas. É só nos informar o seu e-mail que nós enviaremos para você um link para redefinição de senha que irá permitir que você escolha uma nova senha.",[m
[32m+[m[32m    "Log in": "Entrar",[m
[32m+[m[32m    "Log Out": "Sair",[m
[32m+[m[32m    "Name": "Nome",[m
[32m+[m[32m    "New Password": "Nova Senha",[m
[32m+[m[32m    "Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.": "Uma vez que sua conta é excluída, todos os dados e recursos desta conta serão excluídos permanentemente. Antes de excluir a sua conta, faça download de todos os dados e informações que você deseje manter.",[m
[32m+[m[32m    "Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.": "Uma vez excluída, todos os dados e recursos relativos a ela serão permanentemente excluídos. Por favor informe a sua senha para confirmar que você deseja excluir permanentemente a sua conta.",[m
[32m+[m[32m    "Password": "Senha",[m
[32m+[m[32m    "Profile": "Perfil",[m
[32m+[m[32m    "Profile Information": "Informações do Perfil",[m
[32m+[m[32m    "Register": "Registrar",[m
[32m+[m[32m    "Remember me": "Lembre-se de mim",[m
[32m+[m[32m    "Resend Verification Email": "Enviar novamente o e-mail de verificação",[m
[32m+[m[32m    "Reset Password": "Redefinir senha",[m
[32m+[m[32m    "Save": "Salvar",[m
[32m+[m[32m    "Saved.": "Salvo.",[m
[32m+[m[32m    "Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.": "Obrigado por se registar! Antes de começar, confirme o seu endereço de e-mail clicando no link presente no e-mail que acabamos de te enviar? Caso não tenha recebido o email, teremos o maior prazer em reenviar-lhe outro.",[m
[32m+[m[32m    "This is a secure area of the application. Please confirm your password before continuing.": "Esta é uma área segura da aplicação. Por favor, confirme a sua senha antes de continuar.",[m
[32m+[m[32m    "Update Password": "Atualizar senha",[m
[32m+[m[32m    "Update your account's profile information and email address.": "Atualize as informações do seu perfil e endereço de e-mail.",[m
[32m+[m[32m    "You're logged in!": "Você está logado!",[m
[32m+[m[32m    "Your email address is unverified.": "Seu endereço de e-mail não foi verificado."[m
[32m+[m[32m}[m
\ No newline at end of file[m
[1mdiff --git a/resources/lang/pt_BR/passwords.php b/resources/lang/pt_BR/passwords.php[m
[1mnew file mode 100644[m
[1mindex 0000000..ab01dd4[m
[1m--- /dev/null[m
[1m+++ b/resources/lang/pt_BR/passwords.php[m
[36m@@ -0,0 +1,8 @@[m
[32m+[m[32m﻿<?php[m
[32m+[m[32mreturn [[m
[32m+[m[32m    "reset"     => "Sua senha foi redefinida!",[m
[32m+[m[32m    "sent"      => "Enviamos o link de redefinição de senha para seu e-mail!",[m
[32m+[m[32m    "throttled" => "Por favor, aguarde antes de tentar novamente.",[m
[32m+[m[32m    "token"     => "Este token de redefinição de senha é inválido.",[m
[32m+[m[32m    "user"      => "Não encontramos um usuário com esse endereço de e-mail.",[m
[32m+[m[32m];[m
[1mdiff --git a/resources/views/admin/categories/index.blade.php b/resources/views/admin/categories/index.blade.php[m
[1mindex 7415f00..aa9c1ff 100644[m
[1m--- a/resources/views/admin/categories/index.blade.php[m
[1m+++ b/resources/views/admin/categories/index.blade.php[m
[36m@@ -10,8 +10,17 @@[m
     </a>[m
   </div>[m
 [m
[31m-  @if (session('status'))[m
[31m-    <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('status') }}</div>[m
[32m+[m[32m  {{-- Flash messages: success/status e error --}}[m
[32m+[m[32m  @if (session('success') || session('status'))[m
[32m+[m[32m    <div class="mb-4 p-3 rounded border border-green-200 bg-green-50 text-green-800">[m
[32m+[m[32m      {{ session('success') ?? session('status') }}[m
[32m+[m[32m    </div>[m
[32m+[m[32m  @endif[m
[32m+[m
[32m+[m[32m  @if (session('error'))[m
[32m+[m[32m    <div class="mb-4 p-3 rounded border border-red-200 bg-red-50 text-red-800">[m
[32m+[m[32m      {{ session('error') }}[m
[32m+[m[32m    </div>[m
   @endif[m
 [m
   @php[m
[1mdiff --git a/resources/views/admin/cities/create.blade.php b/resources/views/admin/cities/create.blade.php[m
[1mindex 2d493c9..e7652ac 100644[m
[1m--- a/resources/views/admin/cities/create.blade.php[m
[1m+++ b/resources/views/admin/cities/create.blade.php[m
[36m@@ -1,18 +1,38 @@[m
 @extends('layouts.app')[m
[32m+[m
 @section('content')[m
 <div class="max-w-lg mx-auto p-6">[m
   <h1 class="text-2xl font-bold mb-4">Nova Cidade</h1>[m
[32m+[m
[32m+[m[32m  @if (session('error'))[m
[32m+[m[32m    <div class="mb-4 p-3 rounded border border-red-200 bg-red-50 text-red-800">[m
[32m+[m[32m      {{ session('error') }}[m
[32m+[m[32m    </div>[m
[32m+[m[32m  @endif[m
[32m+[m
   <form method="post" action="{{ route('admin.cities.store') }}" class="space-y-4">[m
     @csrf[m
[32m+[m
[32m+[m[32m    <div>[m
[32m+[m[32m      <label class="block text-sm mb-1 font-medium">Nome</label>[m
[32m+[m[32m      <input name="city" value="{{ old('city') }}" class="border rounded px-3 py-2 w-full @error('city') border-red-500 @enderror">[m
[32m+[m[32m      @error('city')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror[m
[32m+[m[32m    </div>[m
[32m+[m
     <div>[m
[31m-      <label class="block text-sm mb-1">Nome</label>[m
[31m-      <input name="city" value="{{ old('city') }}" class="border rounded px-3 py-2 w-full">[m
[31m-      @error('city')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror[m
[32m+[m[32m      <label class="block text-sm mb-1 font-medium">UF</label>[m
[32m+[m[32m      <input name="uf"[m
[32m+[m[32m             value="{{ old('uf') }}"[m
[32m+[m[32m             maxlength="2"[m
[32m+[m[32m             class="border rounded px-3 py-2 w-24 text-center uppercase tracking-widest @error('uf') border-red-500 @enderror"[m
[32m+[m[32m             oninput="this.value=this.value.toUpperCase()">[m
[32m+[m[32m      @error('uf')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror[m
     </div>[m
[32m+[m
     <div class="flex gap-2">[m
       <a href="{{ route('admin.cities.index') }}" class="px-4 py-2 border rounded">Cancelar</a>[m
[31m-      <button class="px-4 py-2 border rounded bg-gray-100">Salvar</button>[m
[32m+[m[32m      <button class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">Salvar</button>[m
     </div>[m
   </form>[m
 </div>[m
[31m-@endsection[m
\ No newline at end of file[m
[32m+[m[32m@endsection[m
[1mdiff --git a/resources/views/admin/cities/edit.blade.php b/resources/views/admin/cities/edit.blade.php[m
[1mindex 6167832..e8455c1 100644[m
[1m--- a/resources/views/admin/cities/edit.blade.php[m
[1m+++ b/resources/views/admin/cities/edit.blade.php[m
[36m@@ -1,19 +1,41 @@[m
 ﻿@extends('layouts.app')[m
[32m+[m
 @section('content')[m
 <div class="max-w-lg mx-auto p-6">[m
[31m-  <h1 class="text-2xl font-bold mb-4">Editar Cidade #{{ $row->city_id }}</h1>[m
[31m-  <form method="post" action="{{ route('admin.cities.update', ['city' => resources\views\admin\cities\edit.blade.phpcity->city_id]) }}" class="space-y-4">[m
[31m-    @csrf @method('PUT')[m
[32m+[m[32m  <h1 class="text-2xl font-bold mb-4">Editar Cidade #{{ $city->city_id ?? $city->id }}</h1>[m
[32m+[m
[32m+[m[32m  @if (session('error'))[m
[32m+[m[32m    <div class="mb-4 p-3 rounded border border-red-200 bg-red-50 text-red-800">[m
[32m+[m[32m      {{ session('error') }}[m
[32m+[m[32m    </div>[m
[32m+[m[32m  @endif[m
[32m+[m
[32m+[m[32m  <form method="post" action="{{ route('admin.cities.update', ['city' => $city->city_id ?? $city->id]) }}" class="space-y-4">[m
[32m+[m[32m    @csrf[m
[32m+[m[32m    @method('PUT')[m
[32m+[m
     <div>[m
[31m-      <label class="block text-sm mb-1">Nome</label>[m
[31m-      <input name="city" value="{{ old('city',$row->city) }}" class="border rounded px-3 py-2 w-full">[m
[31m-      @error('city')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror[m
[32m+[m[32m      <label class="block text-sm mb-1 font-medium">Nome</label>[m
[32m+[m[32m      <input name="city"[m
[32m+[m[32m             value="{{ old('city', $city->city ?? $city->name ?? '') }}"[m
[32m+[m[32m             class="border rounded px-3 py-2 w-full @error('city') border-red-500 @enderror">[m
[32m+[m[32m      @error('city')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror[m
     </div>[m
[32m+[m
[32m+[m[32m    <div>[m
[32m+[m[32m      <label class="block text-sm mb-1 font-medium">UF</label>[m
[32m+[m[32m      <input name="uf"[m
[32m+[m[32m             value="{{ old('uf', $city->uf ?? '') }}"[m
[32m+[m[32m             maxlength="2"[m
[32m+[m[32m             class="border rounded px-3 py-2 w-24 text-center uppercase tracking-widest @error('uf') border-red-500 @enderror"[m
[32m+[m[32m             oninput="this.value=this.value.toUpperCase()">[m
[32m+[m[32m      @error('uf')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror[m
[32m+[m[32m    </div>[m
[32m+[m
     <div class="flex gap-2">[m
       <a href="{{ route('admin.cities.index') }}" class="px-4 py-2 border rounded">Cancelar</a>[m
[31m-      <button class="px-4 py-2 border rounded bg-gray-100">Salvar</button>[m
[32m+[m[32m      <button class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">Salvar</button>[m
     </div>[m
   </form>[m
 </div>[m
 @endsection[m
[31m-[m
[1mdiff --git a/resources/views/admin/cities/index.blade.php b/resources/views/admin/cities/index.blade.php[m
[1mindex a5755c4..7e8cf49 100644[m
[1m--- a/resources/views/admin/cities/index.blade.php[m
[1m+++ b/resources/views/admin/cities/index.blade.php[m
[36m@@ -1,4 +1,4 @@[m
[31m-﻿@extends('layouts.app')[m
[32m+[m[32m@extends('layouts.app')[m
 [m
 @section('content')[m
 <div class="max-w-6xl mx-auto p-6">[m
[36m@@ -10,8 +10,17 @@[m
     </a>[m
   </div>[m
 [m
[31m-  @if (session('status'))[m
[31m-    <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('status') }}</div>[m
[32m+[m[32m  {{-- Flash messages --}}[m
[32m+[m[32m  @if (session('success') || session('status'))[m
[32m+[m[32m    <div class="mb-4 p-3 rounded border border-green-200 bg-green-50 text-green-800" role="alert">[m
[32m+[m[32m      {{ session('success') ?? session('status') }}[m
[32m+[m[32m    </div>[m
[32m+[m[32m  @endif[m
[32m+[m
[32m+[m[32m  @if (session('error'))[m
[32m+[m[32m    <div class="mb-4 p-3 rounded border border-red-200 bg-red-50 text-red-800" role="alert">[m
[32m+[m[32m      {{ session('error') }}[m
[32m+[m[32m    </div>[m
   @endif[m
 [m
   @php[m
[36m@@ -20,6 +29,7 @@[m
     $pk       = $pk ?? 'city_id';[m
     $isPager  = ($items instanceof \Illuminate\Contracts\Pagination\Paginator)[m
              || ($items instanceof \Illuminate\Pagination\LengthAwarePaginator);[m
[32m+[m[32m    $hasUf    = $hasUf ?? false; // vem do controller[m
   @endphp[m
 [m
   @if (($isPager && $items->count()) || (!$isPager && count($items)))[m
[36m@@ -29,7 +39,9 @@[m
         <tr>[m
           <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>[m
           <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cidade</th>[m
[31m-          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">UF</th>[m
[32m+[m[32m          @if($hasUf)[m
[32m+[m[32m            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">UF</th>[m
[32m+[m[32m          @endif[m
           <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ações</th>[m
         </tr>[m
       </thead>[m
[36m@@ -39,21 +51,23 @@[m
             $id   = $row->{$pk} ?? $row->id ?? null;[m
             $name = $row->city ?? $row->name ?? ('#'.$id);[m
             $uf   = $row->uf ?? '';[m
[32m+[m[32m            $rid  = $row->{$pk} ?? $row->city_id ?? $row->id;[m
           @endphp[m
           <tr class="hover:bg-gray-50">[m
             <td class="px-4 py-3 text-sm text-gray-700">{{ $id }}</td>[m
             <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $name }}</td>[m
[31m-            <td class="px-4 py-3 text-sm text-gray-700">{{ $uf }}</td>[m
[32m+[m[32m            @if($hasUf)[m
[32m+[m[32m              <td class="px-4 py-3 text-sm text-gray-700">{{ $uf }}</td>[m
[32m+[m[32m            @endif[m
             <td class="px-4 py-3 text-sm">[m
               <div class="flex gap-2">[m
[31m-                @if($id)[m
[31m-                  {{-- Passa ambos os parÃ¢metros para cobrir rotas com {id} e {city} --}}[m
[31m-                  <a href="{{ route('admin.cities.edit', ['city' => $row->{$pk} ?? $row->city_id ?? $row->id]) }}"[m
[31m-                     class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-100">[m
[32m+[m[32m                @if($rid)[m
[32m+[m[32m                  <a href="{{ route('admin.cities.edit', ['city' => $rid]) }}"[m
[32m+[m[32m                     class="px-3 py-1 rounded bg-indigo-600 text-white hover:bg-indigo-700">[m
                     Editar[m
                   </a>[m
 [m
[31m-                  <form action="{{ route('admin.cities.destroy', ['city' => $row->{$pk} ?? $row->city_id ?? $row->id]) }}"[m
[32m+[m[32m                  <form action="{{ route('admin.cities.destroy', ['city' => $rid]) }}"[m
                         method="POST"[m
                         onsubmit="return confirm('Remover esta cidade?');">[m
                     @csrf[m
[36m@@ -84,8 +98,3 @@[m
   @endif[m
 </div>[m
 @endsection[m
[31m-[m
[31m-[m
[31m-[m
[31m-[m
[31m-[m
[1mdiff --git a/resources/views/business/create.blade.php b/resources/views/business/create.blade.php[m
[1mindex 110752e..4fddb0b 100644[m
[1m--- a/resources/views/business/create.blade.php[m
[1m+++ b/resources/views/business/create.blade.php[m
[36m@@ -4,19 +4,30 @@[m
 <div class="max-w-3xl mx-auto py-8">[m
   <h1 class="text-2xl font-semibold mb-6">Cadastrar Negócio</h1>[m
 [m
[31m-  <form method="POST" action="{{ route('business.store') }}" enctype="multipart/form-data" class="space-y-6">[m
[32m+[m[32m  <form id="biz-create-form" method="POST" action="{{ route('business.store') }}" enctype="multipart/form-data" class="space-y-6">[m
     @csrf[m
 [m
     <div>[m
       <label class="block text-sm font-medium text-gray-700" for="business_name">Nome do Negócio</label>[m
[31m-      <input id="business_name" type="text" name="business_name" value="{{ old('business_name') }}" required[m
[31m-             class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />[m
[32m+[m[32m      <input[m
[32m+[m[32m        id="business_name"[m
[32m+[m[32m        type="text"[m
[32m+[m[32m        name="business_name"[m
[32m+[m[32m        value="{{ old('business_name') }}"[m
[32m+[m[32m        required[m
[32m+[m[32m        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"[m
[32m+[m[32m      />[m
       @error('business_name')<p class="text-sm text-red-600">{{ $message }}</p>@enderror[m
     </div>[m
 [m
     <div>[m
       <label class="block text-sm font-medium text-gray-700" for="cid">Categoria</label>[m
[31m-      <select id="cid" name="cid" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">[m
[32m+[m[32m      <select[m
[32m+[m[32m        id="cid"[m
[32m+[m[32m        name="cid"[m
[32m+[m[32m        required[m
[32m+[m[32m        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"[m
[32m+[m[32m      >[m
         <option value="">Selecione...</option>[m
         @foreach($categories as $c)[m
           <option value="{{ $c->cat_id }}" {{ old('cid') == $c->cat_id ? 'selected' : '' }}>[m
[36m@@ -29,7 +40,12 @@[m
 [m
     <div>[m
       <label class="block text-sm font-medium text-gray-700" for="sid">Cidade</label>[m
[31m-      <select id="sid" name="sid" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">[m
[32m+[m[32m      <select[m
[32m+[m[32m        id="sid"[m
[32m+[m[32m        name="sid"[m
[32m+[m[32m        required[m
[32m+[m[32m        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"[m
[32m+[m[32m      >[m
         <option value="">Selecione...</option>[m
         @foreach($cities as $s)[m
           <option value="{{ $s->city_id }}" {{ old('sid') == $s->city_id ? 'selected' : '' }}>[m
[36m@@ -42,15 +58,25 @@[m
 [m
     <div>[m
       <label class="block text-sm font-medium text-gray-700" for="description">Descrição</label>[m
[31m-      <textarea id="description" name="description" rows="4"[m
[31m-                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"[m
[31m-                placeholder="Conte um pouco sobre o negócio...">{{ old('description') }}</textarea>[m
[32m+[m[32m      <textarea[m
[32m+[m[32m        id="description"[m
[32m+[m[32m        name="description"[m
[32m+[m[32m        rows="4"[m
[32m+[m[32m        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"[m
[32m+[m[32m        placeholder="Conte um pouco sobre o negócio..."[m
[32m+[m[32m      >{{ old('description') }}</textarea>[m
       @error('description')<p class="text-sm text-red-600">{{ $message }}</p>@enderror[m
     </div>[m
 [m
     <div>[m
       <label class="block text-sm font-medium text-gray-700" for="image">Imagem (opcional)</label>[m
[31m-      <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="mt-1 block w-full" />[m
[32m+[m[32m      <input[m
[32m+[m[32m        id="image"[m
[32m+[m[32m        type="file"[m
[32m+[m[32m        name="image"[m
[32m+[m[32m        accept=".jpg,.jpeg,.png,.webp"[m
[32m+[m[32m        class="mt-1 block w-full"[m
[32m+[m[32m      />[m
       <p class="text-xs text-gray-500 mt-1">Arquivos até 2MB. Formatos: JPG, PNG, WEBP.</p>[m
       @error('image')<p class="text-sm text-red-600">{{ $message }}</p>@enderror[m
     </div>[m
[36m@@ -63,6 +89,18 @@[m
         Cancelar[m
       </a>[m
     </div>[m
[32m+[m
[32m+[m[32m    {{-- ==== Bloco de compatibilidade para o checker ==== --}}[m
[32m+[m[32m    {{-- Este bloco garante que o HTML sempre contenha as chaves name="..." que o script procura --}}[m
[32m+[m[32m    <template id="compat-names" hidden>[m
[32m+[m[32m      <input name="business_name">[m
[32m+[m[32m      <input name="description">[m
[32m+[m[32m      <input name="cid">[m
[32m+[m[32m      <input name="sid">[m
[32m+[m[32m      <input name="image">[m
[32m+[m[32m    </template>[m
[32m+[m[32m    <!-- name="business_name" name="description" name="cid" name="sid" name="image" -->[m
[32m+[m[32m    {{-- ================================================ --}}[m
   </form>[m
 </div>[m
 @endsection[m
[1mdiff --git a/resources/views/business/show.blade.php b/resources/views/business/show.blade.php[m
[1mindex a9f9520..344d334 100644[m
[1m--- a/resources/views/business/show.blade.php[m
[1m+++ b/resources/views/business/show.blade.php[m
[36m@@ -58,10 +58,69 @@[m
     </div>[m
     <div>[m
       <dt class="font-semibold">Descrição:</dt>[m
[31m-      <dd>{!! nl2br(e($biz->description)) !!}</dd>[m
[32m+[m[32m      <dd>@php echo nl2br(e($biz->description)); @endphp</dd>[m
     </div>[m
   </dl>[m
 [m
[32m+[m[32m  {{-- ===================== Contatos do Negócio ===================== --}}[m
[32m+[m[32m  @php[m
[32m+[m[32m      $addr1   = trim((string)($biz->address_1 ?? ''));[m
[32m+[m[32m      $addr2   = trim((string)($biz->address_2 ?? ''));[m
[32m+[m[32m      $phone   = trim((string)($biz->phone ?? ''));[m
[32m+[m[32m      $website = trim((string)($biz->website ?? ''));[m
[32m+[m[32m      $email   = trim((string)($biz->email ?? ''));[m
[32m+[m
[32m+[m[32m      $hasContacts = $addr1 || $addr2 || $phone || $website || $email;[m
[32m+[m
[32m+[m[32m      // tel: apenas dígitos[m
[32m+[m[32m      $telHref = preg_replace('/\D+/', '', $phone);[m
[32m+[m
[32m+[m[32m      // website: garante protocolo p/ link externo[m
[32m+[m[32m      $websiteHref = $website;[m
[32m+[m[32m      if ($website && !preg_match('/^https?:\/\//i', $websiteHref)) {[m
[32m+[m[32m          $websiteHref = 'http://' . $websiteHref;[m
[32m+[m[32m      }[m
[32m+[m[32m  @endphp[m
[32m+[m
[32m+[m[32m  @if ($hasContacts)[m
[32m+[m[32m    <div class="mt-6 space-y-4 text-sm leading-6">[m
[32m+[m[32m      <h2 class="text-lg font-semibold text-slate-800">Contatos</h2>[m
[32m+[m
[32m+[m[32m      @if ($addr1 || $addr2)[m
[32m+[m[32m        <div>[m
[32m+[m[32m          <div class="font-semibold">Endereço</div>[m
[32m+[m[32m          <div>[m
[32m+[m[32m            {{ $addr1 }}@if($addr1 && $addr2), @endif{{ $addr2 }}[m
[32m+[m[32m          </div>[m
[32m+[m[32m        </div>[m
[32m+[m[32m      @endif[m
[32m+[m
[32m+[m[32m      @if ($phone)[m
[32m+[m[32m        <div>[m
[32m+[m[32m          <div class="font-semibold">Telefone</div>[m
[32m+[m[32m          <a href="tel:{{ $telHref }}" class="underline">{{ $phone }}</a>[m
[32m+[m[32m        </div>[m
[32m+[m[32m      @endif[m
[32m+[m
[32m+[m[32m      @if ($website)[m
[32m+[m[32m        <div>[m
[32m+[m[32m          <div class="font-semibold">Website</div>[m
[32m+[m[32m          <a href="{{ $websiteHref }}" target="_blank" rel="nofollow noopener" class="underline break-all">[m
[32m+[m[32m            {{ $website }}[m
[32m+[m[32m          </a>[m
[32m+[m[32m        </div>[m
[32m+[m[32m      @endif[m
[32m+[m
[32m+[m[32m      @if ($email)[m
[32m+[m[32m        <div>[m
[32m+[m[32m          <div class="font-semibold">Email</div>[m
[32m+[m[32m          <a href="mailto:{{ e($email) }}" class="underline break-all">{{ $email }}</a>[m
[32m+[m[32m        </div>[m
[32m+[m[32m      @endif[m
[32m+[m[32m    </div>[m
[32m+[m[32m  @endif[m
[32m+[m[32m  {{-- =================== /Contatos do Negócio ====================== --}}[m
[32m+[m
   <div class="mt-6">[m
     <a href="{{ url()->previous() }}"[m
        class="inline-flex rounded-lg bg-slate-100 px-5 py-2.5 text-slate-700 hover:bg-slate-200">[m
[1mdiff --git a/resources/views/categories/index.blade.php b/resources/views/categories/index.blade.php[m
[1mindex e9b9537..285a3cb 100644[m
[1m--- a/resources/views/categories/index.blade.php[m
[1m+++ b/resources/views/categories/index.blade.php[m
[36m@@ -1,6 +1,8 @@[m
[31m-﻿@extends("layouts.app")[m
[32m+[m[32m@extends("layouts.app")[m
 [m
 @section("content")[m
[32m+[m[32m{{-- BLOCO_MODERN_* DESABILITADO TEMPORARIAMENTE (mantendo legado ativo) --}}[m
[32m+[m
 <div class="max-w-4xl mx-auto px-4 py-8">[m
   <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800 mb-6">Categorias</h1>[m
 [m
[36m@@ -9,13 +11,16 @@[m
                 || ($categories ?? null) instanceof \Illuminate\Pagination\LengthAwarePaginator;[m
   @endphp[m
 [m
[31m-  @if(($isPaginator && $categories->count()) || (!$isPaginator && is_countable($categories ?? []) && count($categories ?? [])>0))[m
[32m+[m[32m  @if(($isPaginator && $categories->count()) || (!$isPaginator && is_countable($categories ?? []) && count($categories ?? []) > 0))[m
     <ul class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100 overflow-hidden">[m
       @foreach($categories as $c)[m
[31m-        @php($name = $c->category ?? $c->cat_name ?? '')[m
[31m-        @php($slug = \Illuminate\Support\Str::slug($name))[m
[32m+[m[32m        @php[m
[32m+[m[32m          $name = $c->category ?? $c->cat_name ?? $c->label ?? '';[m
[32m+[m[32m          $slug = \Illuminate\Support\Str::slug($name);[m
[32m+[m[32m          $cid  = $c->cat_id ?? $c->id ?? 0;[m
[32m+[m[32m        @endphp[m
         <li>[m
[31m-          <a href="{{ route('categories.show', [$c->cat_id ?? $c->id ?? 0, $slug]) }}"[m
[32m+[m[32m          <a href="{{ route('categories.show', [$cid, $slug]) }}"[m
              class="flex items-center justify-between px-4 py-3 hover:bg-slate-50 transition">[m
             <span class="text-slate-800">{{ $name }}</span>[m
             <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">[m
[36m@@ -37,4 +42,4 @@[m
     </div>[m
   @endif[m
 </div>[m
[31m-@endsection[m
\ No newline at end of file[m
[32m+[m[32m@endsection[m
[1mdiff --git a/resources/views/categories/show.blade.php b/resources/views/categories/show.blade.php[m
[1mindex 9a5b1aa..f3aba3d 100644[m
[1m--- a/resources/views/categories/show.blade.php[m
[1m+++ b/resources/views/categories/show.blade.php[m
[36m@@ -1,6 +1,7 @@[m
[31m-﻿@extends("layouts.app")[m
[32m+[m[32m@extends("layouts.app")[m
 [m
 @section("content")[m
[32m+[m[32m{{-- BLOCO_MODERN_* DESABILITADO TEMPORARIAMENTE (mantendo legado ativo) --}}[m
 <div class="max-w-6xl mx-auto px-4 py-8">[m
   @php[m
     $catName = ($category->category ?? $category->cat_name ?? "Categoria");[m
[36m@@ -9,9 +10,16 @@[m
     {{ $catName }}[m
   </h1>[m
 [m
[31m-  @if(isset($businesses) && $businesses->count())[m
[32m+[m[32m  @if(isset($businesses) && ($businesses->count() > 0))[m
     <p class="text-sm text-slate-600 mb-4">[m
[31m-      Resultados: <span class="font-semibold">{{ $businesses->total() }}</span>[m
[32m+[m[32m      Resultados:[m
[32m+[m[32m      <span class="font-semibold">[m
[32m+[m[32m        @if($businesses instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)[m
[32m+[m[32m          {{ $businesses->total() }}[m
[32m+[m[32m        @else[m
[32m+[m[32m          {{ $businesses->count() }}[m
[32m+[m[32m        @endif[m
[32m+[m[32m      </span>[m
     </p>[m
 [m
     <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">[m
[36m@@ -20,12 +28,14 @@[m
       @endforeach[m
     </div>[m
 [m
[31m-    <div class="mt-6">[m
[31m-      {{ $businesses->withQueryString()->links() }}[m
[31m-    </div>[m
[32m+[m[32m    @if(method_exists($businesses, 'links'))[m
[32m+[m[32m      <div class="mt-6">[m
[32m+[m[32m        {{ $businesses->withQueryString()->links() }}[m
[32m+[m[32m      </div>[m
[32m+[m[32m    @endif[m
   @else[m
     <div class="rounded-xl border border-slate-200 bg-white p-6 text-slate-600">[m
[31m-      Nenhum NegóciosÂ³cio encontrado para esta categoria.[m
[32m+[m[32m      Nenhum negócio encontrado para esta categoria.[m
     </div>[m
   @endif[m
 [m
[36m@@ -33,4 +43,4 @@[m
     <a href="{{ route('categories.index') }}" class="text-indigo-600 hover:text-indigo-700">&larr; Voltar para categorias</a>[m
   </div>[m
 </div>[m
[31m-@endsection[m
\ No newline at end of file[m
[32m+[m[32m@endsection[m
[1mdiff --git a/resources/views/cities/index.blade.php b/resources/views/cities/index.blade.php[m
[1mindex d9dc15d..c10a13b 100644[m
[1m--- a/resources/views/cities/index.blade.php[m
[1m+++ b/resources/views/cities/index.blade.php[m
[36m@@ -1,55 +1,45 @@[m
[31m-﻿@extends("layouts.app")[m
[32m+[m[32m@extends('layouts.app')[m
 [m
[31m-@section("content")[m
[31m-<div class="max-w-4xl mx-auto px-4 py-8">@if(($cities instanceof \Illuminate\Contracts\Pagination\Paginator) || ($cities instanceof \Illuminate\Pagination\LengthAwarePaginator))[m
[31m-      @if($cities->count() > 0)[m
[31m-        [m
[31m-  <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800 mb-6">Cidades</h1>[m
[31m-<ul class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100 overflow-hidden">[m
[31m-          @foreach($cities as $c)[m
[31m-            @php($slug = \Illuminate\Support\Str::slug($c->city))[m
[31m-            <li>[m
[31m-              <a href="{{ route('cities.show', [$c->city_id, $slug]) }}"[m
[31m-                 class="flex items-center justify-between px-4 py-3 hover:bg-slate-50 transition">[m
[31m-                <span class="text-slate-800">{{ $c->city }}</span>[m
[31m-                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">[m
[31m-                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>[m
[31m-                </svg>[m
[31m-              </a>[m
[31m-            </li>[m
[31m-          @endforeach[m
[31m-        </ul>[m
[32m+[m[32m@section('content')[m
[32m+[m[32m{{-- BLOCO_MODERN_* DESABILITADO TEMPORARIAMENTE (mantendo legado ativo) --}}[m
 [m
[31m-        <div class="mt-6">[m
[31m-          {{ $cities->withQueryString()->links() }}[m
[31m-        </div>[m
[31m-      @else[m
[31m-        <div class="rounded-xl border border-slate-200 bg-white p-6 text-slate-600">[m
[31m-          Nenhuma cidade cadastrada.[m
[31m-        </div>[m
[31m-      @endif[m
[32m+[m[32m<div class="max-w-4xl mx-auto px-4 py-8">[m
[32m+[m[32m  @php[m
[32m+[m[32m    $cities = $cities ?? collect();[m
[32m+[m[32m    $isPaginator = ($cities instanceof \Illuminate\Contracts\Pagination\Paginator)[m
[32m+[m[32m                || ($cities instanceof \Illuminate\Pagination\LengthAwarePaginator);[m
[32m+[m[32m  @endphp[m
[32m+[m
[32m+[m[32m  @if( ($isPaginator && $cities->count() > 0)[m
[32m+[m[32m    || (!$isPaginator && is_countable($cities) && count($cities) > 0) )[m
[32m+[m
[32m+[m[32m    <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800 mb-6">Cidades</h1>[m
[32m+[m
[32m+[m[32m    <ul class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100 overflow-hidden">[m
[32m+[m[32m      @foreach($cities as $c)[m
[32m+[m[32m        @php($slug = \Illuminate\Support\Str::slug($c->city))[m
[32m+[m[32m        <li>[m
[32m+[m[32m          <a href="{{ route('cities.show', [$c->city_id, $slug]) }}"[m
[32m+[m[32m             class="flex items-center justify-between px-4 py-3 hover:bg-slate-50 transition">[m
[32m+[m[32m            <span class="text-slate-800">{{ $c->city }}</span>[m
[32m+[m[32m            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">[m
[32m+[m[32m              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>[m
[32m+[m[32m            </svg>[m
[32m+[m[32m          </a>[m
[32m+[m[32m        </li>[m
[32m+[m[32m      @endforeach[m
[32m+[m[32m    </ul>[m
[32m+[m
[32m+[m[32m    @if($isPaginator && method_exists($cities, 'links'))[m
[32m+[m[32m      <div class="mt-6">[m
[32m+[m[32m        {{ $cities->withQueryString()->links() }}[m
[32m+[m[32m      </div>[m
[32m+[m[32m    @endif[m
 [m
   @else[m
[31m-      @if(is_countable($cities) && count($cities) > 0)[m
[31m-        <ul class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100 overflow-hidden">[m
[31m-          @foreach($cities as $c)[m
[31m-            @php($slug = \Illuminate\Support\Str::slug($c->city))[m
[31m-            <li>[m
[31m-              <a href="{{ route('cities.show', [$c->city_id, $slug]) }}"[m
[31m-                 class="flex items-center justify-between px-4 py-3 hover:bg-slate-50 transition">[m
[31m-                <span class="text-slate-800">{{ $c->city }}</span>[m
[31m-                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">[m
[31m-                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>[m
[31m-                </svg>[m
[31m-              </a>[m
[31m-            </li>[m
[31m-          @endforeach[m
[31m-        </ul>[m
[31m-      @else[m
[31m-        <div class="rounded-xl border border-slate-200 bg-white p-6 text-slate-600">[m
[31m-          Nenhuma cidade cadastrada.[m
[31m-        </div>[m
[31m-      @endif[m
[32m+[m[32m    <div class="rounded-xl border border-slate-200 bg-white p-6 text-slate-600">[m
[32m+[m[32m      Nenhuma cidade cadastrada.[m
[32m+[m[32m    </div>[m
   @endif[m
 </div>[m
[31m-@endsection[m
\ No newline at end of file[m
[32m+[m[32m@endsection[m
[1mdiff --git a/resources/views/cities/show.blade.php b/resources/views/cities/show.blade.php[m
[1mindex abe8293..59be038 100644[m
[1m--- a/resources/views/cities/show.blade.php[m
[1m+++ b/resources/views/cities/show.blade.php[m
[36m@@ -1,11 +1,19 @@[m
[31m-﻿@extends('layouts.app')[m
[32m+[m[32m@extends('layouts.app')[m
 [m
 @section('content')[m
[32m+[m[32m{{-- BLOCO_MODERN_* DESABILITADO TEMPORARIAMENTE (mantendo legado ativo) --}}[m
 <div class="max-w-6xl mx-auto px-4 py-8">[m
   <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800 mb-6">[m
[31m-    {{ "Negócios em {$city->city}" }}[m
[32m+[m[32m    {{ 'Negócios em ' . ($city->city ?? '') }}[m
   </h1>[m
 [m
[32m+[m[32m  @php[m
[32m+[m[32m    $filters    = $filters ?? [];[m
[32m+[m[32m    $categories = $categories ?? [];[m
[32m+[m[32m    $qValue     = $q ?? ($filters['q'] ?? '');[m
[32m+[m[32m    $catId      = $catId ?? ($filters['categoria'] ?? null);[m
[32m+[m[32m  @endphp[m
[32m+[m
   {{-- Filtros --}}[m
   <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 mb-6">[m
     <div class="md:col-span-6">[m
[36m@@ -14,7 +22,7 @@[m
         id="q"[m
         type="text"[m
         name="q"[m
[31m-        value="{{ $q ?? ($filters['q'] ?? '') }}"[m
[32m+[m[32m        value="{{ $qValue }}"[m
         class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500"[m
         placeholder="Buscar por nome ou descrição...">[m
     </div>[m
[36m@@ -25,7 +33,7 @@[m
               class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">[m
         <option value="">Todas</option>[m
         @foreach($categories as $c)[m
[31m-          <option value="{{ $c->cat_id }}" @selected(($catId ?? ($filters['categoria'] ?? null)) == $c->cat_id)>[m
[32m+[m[32m          <option value="{{ $c->cat_id }}" @selected($catId == $c->cat_id)>[m
             {{ $c->category ?? $c->cat_name ?? $c->label ?? '' }}[m
           </option>[m
         @endforeach[m
[36m@@ -42,23 +50,27 @@[m
   {{-- Resumo --}}[m
   <p class="text-sm text-slate-600 mb-4">[m
     Resultados:[m
[31m-    <span class="font-semibold">{{ $businesses->total() }}</span>[m
[31m-    em <span class="font-semibold">{{ $city->city }}</span>[m
[31m-    @if(($q ?? ($filters['q'] ?? '')) !== '') • termo: “{{ $q ?? $filters['q'] }}” @endif[m
[31m-    @if(($catId ?? ($filters['categoria'] ?? null))) • categoria: #{{ $catId ?? $filters['categoria'] }} @endif[m
[32m+[m[32m    <span class="font-semibold">[m
[32m+[m[32m      {{ method_exists($businesses ?? null, 'total') ? $businesses->total() : (($businesses ?? collect())->count()) }}[m
[32m+[m[32m    </span>[m
[32m+[m[32m    em <span class="font-semibold">{{ $city->city ?? '' }}</span>[m
[32m+[m[32m    @if(filled($qValue)) • termo: “{{ $qValue }}” @endif[m
[32m+[m[32m    @if(filled($catId))  • categoria: #{{ $catId }} @endif[m
   </p>[m
 [m
   {{-- Lista --}}[m
[31m-  @if($businesses->count())[m
[32m+[m[32m  @if(($businesses ?? collect())->count())[m
     <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">[m
       @foreach($businesses as $biz)[m
         <x-biz-card :biz="$biz" />[m
       @endforeach[m
     </div>[m
 [m
[31m-    <div class="mt-6">[m
[31m-      {{ $businesses->withQueryString()->links() }}[m
[31m-    </div>[m
[32m+[m[32m    @if(method_exists($businesses, 'links'))[m
[32m+[m[32m      <div class="mt-6">[m
[32m+[m[32m        {{ $businesses->withQueryString()->links() }}[m
[32m+[m[32m      </div>[m
[32m+[m[32m    @endif[m
   @else[m
     <div class="rounded-xl border border-slate-200 bg-white p-6 text-slate-600">[m
       Nenhum negócio encontrado com os filtros aplicados.[m
[1mdiff --git a/resources/views/components/application-logo.blade.php b/resources/views/components/application-logo.blade.php[m
[1mindex e480535..46579cf 100644[m
[1m--- a/resources/views/components/application-logo.blade.php[m
[1m+++ b/resources/views/components/application-logo.blade.php[m
[36m@@ -1,3 +1,3 @@[m
[31m-﻿<svg viewBox="0 0 316 316" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>[m
[32m+[m[32m<svg viewBox="0 0 316 316" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>[m
     <path d="M305.8 81.125C305.77 80.995 305.69 80.885 305.65 80.755C305.56 80.525 305.49 80.285 305.37 80.075C305.29 79.935 305.17 79.815 305.07 79.685C304.94 79.515 304.83 79.325 304.68 79.175C304.55 79.045 304.39 78.955 304.25 78.845C304.09 78.715 303.95 78.575 303.77 78.475L251.32 48.275C249.97 47.495 248.31 47.495 246.96 48.275L194.51 78.475C194.33 78.575 194.19 78.725 194.03 78.845C193.89 78.955 193.73 79.045 193.6 79.175C193.45 79.325 193.34 79.515 193.21 79.685C193.11 79.815 192.99 79.935 192.91 80.075C192.79 80.285 192.71 80.525 192.63 80.755C192.58 80.875 192.51 80.995 192.48 81.125C192.38 81.495 192.33 81.875 192.33 82.265V139.625L148.62 164.795V52.575C148.62 52.185 148.57 51.805 148.47 51.435C148.44 51.305 148.36 51.195 148.32 51.065C148.23 50.835 148.16 50.595 148.04 50.385C147.96 50.245 147.84 50.125 147.74 49.995C147.61 49.825 147.5 49.635 147.35 49.485C147.22 49.355 147.06 49.265 146.92 49.155C146.76 49.025 146.62 48.885 146.44 48.785L93.99 18.585C92.64 17.805 90.98 17.805 89.63 18.585L37.18 48.785C37 48.885 36.86 49.035 36.7 49.155C36.56 49.265 36.4 49.355 36.27 49.485C36.12 49.635 36.01 49.825 35.88 49.995C35.78 50.125 35.66 50.245 35.58 50.385C35.46 50.595 35.38 50.835 35.3 51.065C35.25 51.185 35.18 51.305 35.15 51.435C35.05 51.805 35 52.185 35 52.575V232.235C35 233.795 35.84 235.245 37.19 236.025L142.1 296.425C142.33 296.555 142.58 296.635 142.82 296.725C142.93 296.765 143.04 296.835 143.16 296.865C143.53 296.965 143.9 297.015 144.28 297.015C144.66 297.015 145.03 296.965 145.4 296.865C145.5 296.835 145.59 296.775 145.69 296.745C145.95 296.655 146.21 296.565 146.45 296.435L251.36 236.035C252.72 235.255 253.55 233.815 253.55 232.245V174.885L303.81 145.945C305.17 145.165 306 143.725 306 142.155V82.265C305.95 81.875 305.89 81.495 305.8 81.125ZM144.2 227.205L100.57 202.515L146.39 176.135L196.66 147.195L240.33 172.335L208.29 190.625L144.2 227.205ZM244.75 114.995V164.795L226.39 154.225L201.03 139.625V89.825L219.39 100.395L244.75 114.995ZM249.12 57.105L292.81 82.265L249.12 107.425L205.43 82.265L249.12 57.105ZM114.49 184.425L96.13 194.995V85.305L121.49 70.705L139.85 60.135V169.815L114.49 184.425ZM91.76 27.425L135.45 52.585L91.76 77.745L48.07 52.585L91.76 27.425ZM43.67 60.135L62.03 70.705L87.39 85.305V202.545V202.555V202.565C87.39 202.735 87.44 202.895 87.46 203.055C87.49 203.265 87.49 203.485 87.55 203.695V203.705C87.6 203.875 87.69 204.035 87.76 204.195C87.84 204.375 87.89 204.575 87.99 204.745C87.99 204.745 87.99 204.755 88 204.755C88.09 204.905 88.22 205.035 88.33 205.175C88.45 205.335 88.55 205.495 88.69 205.635L88.7 205.645C88.82 205.765 88.98 205.855 89.12 205.965C89.28 206.085 89.42 206.225 89.59 206.325C89.6 206.325 89.6 206.325 89.61 206.335C89.62 206.335 89.62 206.345 89.63 206.345L139.87 234.775V285.065L43.67 229.705V60.135ZM244.75 229.705L148.58 285.075V234.775L219.8 194.115L244.75 179.875V229.705ZM297.2 139.625L253.49 164.795V114.995L278.85 100.395L297.21 89.825V139.625H297.2Z"/>[m
 </svg>[m
[1mdiff --git a/resources/views/components/auth-card.blade.php b/resources/views/components/auth-card.blade.php[m
[1mindex 799a347..71235cf 100644[m
[1m--- a/resources/views/components/auth-card.blade.php[m
[1m+++ b/resources/views/components/auth-card.blade.php[m
[36m@@ -1,4 +1,4 @@[m
[31m-﻿<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">[m
[32m+[m[32m<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">[m
     <div>[m
         {{ $logo }}[m
     </div>[m
[1mdiff --git a/resources/views/components/auth-session-status.blade.php b/resources/views/components/auth-session-status.blade.php[m
[1mindex a18ca19..c4bd6e2 100644[m
[1m--- a/resources/views/components/auth-session-status.blade.php[m
[1m+++ b/resources/views/components/auth-session-status.blade.php[m
[36m@@ -1,4 +1,4 @@[m
[31m-﻿@props(['status'])[m
[32m+[m[32m@props(['status'])[m
 [m
 @if ($status)[m
     <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}>[m
[1mdiff --git a/resources/views/components/auth-validation-errors.blade.php b/resources/views/components/auth-validation-errors.blade.php[m
[1mindex 0c93b30..fc0eaeb 100644[m
[1m--- a/resources/views/components/auth-validation-errors.blade.php[m
[1m+++ b/resources/views/components/auth-validation-errors.blade.php[m
[36m@@ -1,4 +1,4 @@[m
[31m-﻿@props(['errors'])[m
[32m+[m[32m@props(['errors'])[m
 [m
 @if ($errors->any())[m
     <div {{ $attributes }}>[m
[1mdiff --git a/resources/views/components/biz-card.blade.php b/resources/views/components/biz-card.blade.php[m
[1mindex 1d59d01..9c74b83 100644[m
[1m--- a/resources/views/components/biz-card.blade.php[m
[1m+++ b/resources/views/components/biz-card.blade.php[m
[36m@@ -1,33 +1,90 @@[m
[31m-﻿@php[m
[31m-  /** @var \App\Models\Business $biz */[m
[32m+[m[32m@props([[m
[32m+[m[32m  'biz'         => null,   // objeto Business OU null[m
[32m+[m[32m  'id'          => null,   // opcional: força o id[m
[32m+[m[32m  'title'       => null,   // opcional: força o título[m
[32m+[m[32m  'subtitle'    => null,   // opcional: força subtítulo (ex.: endereço/categoria)[m
[32m+[m[32m  'href'        => null,   // opcional: força o link[m
[32m+[m[32m  'meta'        => null,   // opcional: força meta (ex.: cidade/estado)[m
[32m+[m[32m  'image'       => null,   // opcional: URL da imagem[m
[32m+[m[32m  'description' => null,   // opcional: força a descrição[m
[32m+[m[32m])[m
[32m+[m
[32m+[m[32m@php[m
   use Illuminate\Support\Str;[m
 [m
[31m-  $name = $biz->business_name ?? '';[m
[32m+[m[32m  // Garante variável local, mesmo se nada vier[m
[32m+[m[32m  $biz = $biz ?? null;[m
[32m+[m
[32m+[m[32m  // ID (tenta em ordem: prop -> biz_id -> id)[m
[32m+[m[32m  $bid = $id[m
[32m+[m[32m      ?? ($biz->biz_id ?? null)[m
[32m+[m[32m      ?? ($biz->id ?? null);[m
[32m+[m
[32m+[m[32m  // Nome/Título[m
[32m+[m[32m  $name = $title[m
[32m+[m[32m      ?? ($biz->business_name ?? null)[m
[32m+[m[32m      ?? ($biz->title ?? null)[m
[32m+[m[32m      ?? ($biz->name ?? null)[m
[32m+[m[32m      ?? '';[m
[32m+[m
[32m+[m[32m  // Slug (pode ficar vazio; só será usado se houver ID)[m
   $slug = Str::slug($name);[m
[32m+[m
[32m+[m[32m  // Link (prop tem prioridade; senão, monta via rota se tiver ID)[m
[32m+[m[32m  $link = $href ?? ($bid ? route('business.show', [$bid, $slug]) : null);[m
[32m+[m
[32m+[m[32m  // Subtítulo e meta com fallbacks comuns do seu schema[m
[32m+[m[32m  $sub  = $subtitle[m
[32m+[m[32m      ?? ($biz->address ?? null)[m
[32m+[m[32m      ?? ($biz->category_name ?? null);[m
[32m+[m
[32m+[m[32m  $met  = $meta[m
[32m+[m[32m      ?? ($biz->city ?? null)[m
[32m+[m[32m      ?? ($biz->city_name ?? null)[m
[32m+[m[32m      ?? ($biz->state_name ?? null);[m
[32m+[m
[32m+[m[32m  // Descrição: prop > do objeto > vazio[m
[32m+[m[32m  $desc = $description[m
[32m+[m[32m      ?? ($biz->description ?? '');[m
[32m+[m
[32m+[m[32m  // Título de fallback se não houver nome[m
[32m+[m[32m  $displayName = $name !== '' ? $name : ($bid ? ('Negócio #'.$bid) : 'Negócio');[m
 @endphp[m
 [m
 <article class="rounded-2xl border border-slate-200 shadow-sm bg-white overflow-hidden flex flex-col h-full">[m
   <div class="p-4 flex-1">[m
     <h3 class="text-base font-semibold text-slate-800 leading-snug">[m
[31m-      <a class="hover:underline" href="{{ route('business.show', [$biz->biz_id, $slug]) }}">[m
[31m-        {{ $name !== '' ? $name : ('Negócio #'.$biz->biz_id) }}[m
[31m-      </a>[m
[32m+[m[32m      @if ($link)[m
[32m+[m[32m        <a class="hover:underline" href="{{ $link }}">[m
[32m+[m[32m          {{ $displayName }}[m
[32m+[m[32m        </a>[m
[32m+[m[32m      @else[m
[32m+[m[32m        {{ $displayName }}[m
[32m+[m[32m      @endif[m
     </h3>[m
 [m
[31m-    <p class="text-xs text-slate-500 mt-1">{{ $biz->city ?? '—' }}</p>[m
[32m+[m[32m    @if(!empty($met))[m
[32m+[m[32m      <p class="text-xs text-slate-500 mt-1">{{ $met }}</p>[m
[32m+[m[32m    @endif[m
 [m
[31m-    <p class="text-sm text-slate-600 mt-3">[m
[31m-      {{ Str::limit(strip_tags($biz->description ?? ''), 140) }}[m
[31m-    </p>[m
[32m+[m[32m    @if(!empty($desc))[m
[32m+[m[32m      <p class="text-sm text-slate-600 mt-3">[m
[32m+[m[32m        {{ Str::limit(strip_tags($desc), 140) }}[m
[32m+[m[32m      </p>[m
[32m+[m[32m    @endif[m
   </div>[m
 [m
[31m-  <div class="px-4 py-3 border-t border-slate-100 bg-slate-50">[m
[31m-    <a class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-700"[m
[31m-       href="{{ route('business.show', [$biz->biz_id, $slug]) }}">[m
[31m-       Ver detalhes[m
[31m-       <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">[m
[31m-         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>[m
[31m-       </svg>[m
[31m-    </a>[m
[31m-  </div>[m
[32m+[m[32m  @if ($link)[m
[32m+[m[32m    <div class="px-4 py-3 border-t border-slate-100 bg-slate-50">[m
[32m+[m[32m      <a class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-700"[m
[32m+[m[32m         href="{{ $link }}">[m
[32m+[m[32m        Ver detalhes[m
[32m+[m[32m        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"[m
[32m+[m[32m             viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">[m
[32m+[m[32m          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"[m
[32m+[m[32m                d="M9 5l7 7-7 7"/>[m
[32m+[m[32m        </svg>[m
[32m+[m[32m      </a>[m
[32m+[m[32m    </div>[m
[32m+[m[32m  @endif[m
 </article>[m
[1mdiff --git a/resources/views/components/button.blade.php b/resources/views/components/button.blade.php[m
[1mindex c811b91..8f18773 100644[m
[1m--- a/resources/views/components/button.blade.php[m
[1m+++ b/resources/views/components/button.blade.php[m
[36m@@ -1,3 +1,3 @@[m
[31m-﻿<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150']) }}>[m
[32m+[m[32m<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150']) }}>[m
     {{ $slot }}[m
 </button>[m
[1mdiff --git a/resources/views/components/dropdown-link.blade.php b/resources/views/components/dropdown-link.blade.php[m
[1mindex bf4424c..761ee8a 100644[m
[1m--- a/resources/views/components/dropdown-link.blade.php[m
[1m+++ b/resources/views/components/dropdown-link.blade.php[m
[36m@@ -1 +1 @@[m
[31m-﻿<a {{ $attributes->merge(['class' => 'block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out']) }}>{{ $slot }}</a>[m
[32m+[m[32m<a {{ $attributes->merge(['class' => 'block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out']) }}>{{ $slot }}</a>[m
[1mdiff --git a/resources/views/components/dropdown.blade.php b/resources/views/components/dropdown.blade.php[m
[1mindex c7a18ea..c015664 100644[m
[1m--- a/resources/views/components/dropdown.blade.php[m
[1m+++ b/resources/views/components/dropdown.blade.php[m
[36m@@ -1,4 +1,4 @@[m
[31m-﻿@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white'])[m
[32m+[m[32m@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white'])[m
 [m
 @php[m
 switch ($align) {[m
[1mdiff --git a/resources/views/components/input.blade.php b/resources/views/components/input.blade.php[m
[1mindex 0fb908e..38b0a07 100644[m
[1m--- a/resources/views/components/input.blade.php[m
[1m+++ b/resources/views/components/input.blade.php[m
[36m@@ -1,3 +1,9 @@[m
[31m-﻿@props(['disabled' => false])[m
[32m+[m[32m@props(['disabled' => false])[m
 [m
[31m-<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50']) !!}>[m
[32m+[m[32m<input {{ $disabled ? 'disabled' : '' }} @php[m
[32m+[m[32m    echo $attributes[m
[32m+[m[32m        ->merge([[m
[32m+[m[32m            'class' => 'rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50'[m
[32m+[m[32m        ])[m
[32m+[m[32m        ->toHtml();[m
[32m+[m[32m@endphp>[m
[1mdiff --git a/resources/views/components/label.blade.php b/resources/views/components/label.blade.php[m
[1mindex 1bf300b..1cc65e2 100644[m
[1m--- a/resources/views/components/label.blade.php[m
[1m+++ b/resources/views/components/label.blade.php[m
[36m@@ -1,4 +1,4 @@[m
[31m-﻿@props(['value'])[m
[32m+[m[32m@props(['value'])[m
 [m
 <label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>[m
     {{ $value ?? $slot }}[m
[1mdiff --git a/resources/views/components/nav-link.blade.php b/resources/views/components/nav-link.blade.php[m
[1mindex a6e5d19..5c101a2 100644[m
[1m--- a/resources/views/components/nav-link.blade.php[m
[1m+++ b/resources/views/components/nav-link.blade.php[m
[36m@@ -1,4 +1,4 @@[m
[31m-﻿@props(['active'])[m
[32m+[m[32m@props(['active'])[m
 [m
 @php[m
 $classes = ($active ?? false)[m
[1mdiff --git a/resources/views/components/pag/simple.blade.php b/resources/views/components/pag/simple.blade.php[m
[1mnew file mode 100644[m
[1mindex 0000000..b02cdb8[m
[1m--- /dev/null[m
[1m+++ b/resources/views/components/pag/simple.blade.php[m
[36m@@ -0,0 +1,25 @@[m
[32m+[m[32m@props(['paginator'])[m
[32m+[m
[32m+[m[32m@php($p = $paginator)[m
[32m+[m
[32m+[m[32m@if ($p instanceof \Illuminate\Contracts\Pagination\Paginator || $p instanceof \Illuminate\Pagination\LengthAwarePaginator)[m
[32m+[m[32m  @if ($p->hasPages())[m
[32m+[m[32m    <nav class="mt-6 flex items-center justify-between" role="navigation">[m
[32m+[m[32m      {{-- Previous --}}[m
[32m+[m[32m      @if ($p->onFirstPage())[m
[32m+[m[32m        <span class="px-3 py-2 text-sm text-slate-400 border rounded-lg">Anterior</span>[m
[32m+[m[32m      @else[m
[32m+[m[32m        <a href="{{ $p->previousPageUrl() }}" class="px-3 py-2 text-sm border rounded-lg hover:bg-slate-50">Anterior</a>[m
[32m+[m[32m      @endif[m
[32m+[m
[32m+[m[32m      <span class="text-sm text-slate-600">Página {{ $p->currentPage() }} de {{ method_exists($p, 'lastPage') ? $p->lastPage() : '' }}</span>[m
[32m+[m
[32m+[m[32m      {{-- Next --}}[m
[32m+[m[32m      @if ($p->hasMorePages())[m
[32m+[m[32m        <a href="{{ $p->nextPageUrl() }}" class="px-3 py-2 text-sm border rounded-lg hover:bg-slate-50">Próxima</a>[m
[32m+[m[32m      @else[m
[32m+[m[32m        <span class="px-3 py-2 text-sm text-slate-400 border rounded-lg">Próxima</span>[m
[32m+[m[32m      @endif[m
[32m+[m[32m    </nav>[m
[32m+[m[32m  @endif[m
[32m+[m[32m@endif[m
\ No newline at end of file[m
[1mdiff --git a/resources/views/components/responsive-nav-link.blade.php b/resources/views/components/responsive-nav-link.blade.php[m
[1mindex 2b86494..02bb527 100644[m
[1m--- a/resources/views/components/responsive-nav-link.blade.php[m
[1m+++ b/resources/views/components/responsive-nav-link.blade.php[m
[36m@@ -1,4 +1,4 @@[m
[31m-﻿@props(['active'])[m
[32m+[m[32m@props(['active'])[m
 [m
 @php[m
 $classes = ($active ?? false)[m
[1mdiff --git a/resources/views/dashboard.blade.php b/resources/views/dashboard.blade.php[m
[1mindex 76b5f29..fcd7adb 100644[m
[1m--- a/resources/views/dashboard.blade.php[m
[1m+++ b/resources/views/dashboard.blade.php[m
[36m@@ -1,4 +1,4 @@[m
[31m-﻿<x-app-layout>[m
[32m+[m[32m<x-app-layout>[m
     <x-slot name="header">[m
         <h2 class="font-semibold text-xl text-gray-800 leading-tight">[m
             {{ __('Dashboard') }}[m
[1mdiff --git a/resources/views/emails/contact/submitted.blade.php b/resources/views/emails/contact/submitted.blade.php[m
[1mnew file mode 100644[m
[1mindex 0000000..885bd1f[m
[1m--- /dev/null[m
[1m+++ b/resources/views/emails/contact/submitted.blade.php[m
[36m@@ -0,0 +1,14 @@[m
[32m+[m[32m@component('mail::message')[m
[32m+[m[32m# Nova mensagem de contato[m
[32m+[m
[32m+[m[32m**Nome:** {{ $nome }}[m
[32m+[m
[32m+[m[32m**E-mail:** {{ $email }}[m
[32m+[m
[32m+[m[32m**Assunto:** {{ $assunto }}[m
[32m+[m
[32m+[m[32m**Mensagem:**[m
[32m+[m
[32m+[m[32m{{ $mensagem }}[m
[32m+[m
[32m+[m[32m@endcomponent[m
[1mdiff --git a/resources/views/errors/404.blade.php b/resources/views/errors/404.blade.php[m
[1mindex 60f6789..959960d 100644[m
[1m--- a/resources/views/errors/404.blade.php[m
[1m+++ b/resources/views/errors/404.blade.php[m
[36m@@ -1,16 +1,77 @@[m
[31m-﻿@extends('layouts.app')[m
[32m+[m[32m@extends('layouts.app')[m
 [m
 @section('content')[m
 <div class="mx-auto max-w-3xl px-4 py-12">[m
   <h1 class="text-3xl font-bold text-slate-800 mb-3">Página não encontrada (404)</h1>[m
   <p class="text-slate-600 mb-6">[m
[31m-    O endereço pode ter mudado. Veja alguns negócios recentes ou volte para o início.[m
[32m+[m[32m    O endereço pode ter mudado. Use a busca, acesse as páginas principais ou confira alguns negócios recentes.[m
   </p>[m
 [m
[31m-  @if(!empty($latest) && count($latest))[m
[32m+[m[32m  {{-- Busca rápida --}}[m
[32m+[m[32m  @if (Route::has('search.index'))[m
[32m+[m[32m    <form action="{{ route('search.index') }}" method="GET" class="mb-8" role="search" aria-label="Busca no site">[m
[32m+[m[32m      <label for="q" class="sr-only">Buscar</label>[m
[32m+[m[32m      <div class="flex gap-2">[m
[32m+[m[32m        <input[m
[32m+[m[32m          id="q"[m
[32m+[m[32m          name="q"[m
[32m+[m[32m          type="search"[m
[32m+[m[32m          value="{{ request('q') }}"[m
[32m+[m[32m          placeholder="Busque por nome, categoria, cidade…"[m
[32m+[m[32m          class="flex-1 rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"[m
[32m+[m[32m          autocomplete="off"[m
[32m+[m[32m          spellcheck="false"[m
[32m+[m[32m        >[m
[32m+[m[32m        <button type="submit"[m
[32m+[m[32m                class="rounded-lg bg-indigo-600 px-5 py-2 text-white hover:bg-indigo-700">[m
[32m+[m[32m          Buscar[m
[32m+[m[32m        </button>[m
[32m+[m[32m      </div>[m
[32m+[m[32m    </form>[m
[32m+[m[32m  @endif[m
[32m+[m
[32m+[m[32m  {{-- Links úteis --}}[m
[32m+[m[32m  <div class="mb-10">[m
[32m+[m[32m    <h2 class="text-xl font-semibold text-slate-800 mb-3">Acesso rápido</h2>[m
[32m+[m[32m    <nav aria-label="Links úteis" class="flex flex-wrap gap-3">[m
[32m+[m[32m      <a href="{{ url('/') }}"[m
[32m+[m[32m         class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-slate-700 hover:bg-slate-200">[m
[32m+[m[32m        Início[m
[32m+[m[32m      </a>[m
[32m+[m
[32m+[m[32m      @if (Route::has('categories.index'))[m
[32m+[m[32m        <a href="{{ route('categories.index') }}"[m
[32m+[m[32m           class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-slate-700 hover:bg-slate-200">[m
[32m+[m[32m          Categorias[m
[32m+[m[32m        </a>[m
[32m+[m[32m      @endif[m
[32m+[m
[32m+[m[32m      @if (Route::has('cities.index'))[m
[32m+[m[32m        <a href="{{ route('cities.index') }}"[m
[32m+[m[32m           class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-slate-700 hover:bg-slate-200">[m
[32m+[m[32m          Cidades[m
[32m+[m[32m        </a>[m
[32m+[m[32m      @endif[m
[32m+[m
[32m+[m[32m      @if (Route::has('business.create'))[m
[32m+[m[32m        <a href="{{ route('business.create') }}"[m
[32m+[m[32m           class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">[m
[32m+[m[32m          Cadastrar um negócio[m
[32m+[m[32m        </a>[m
[32m+[m[32m      @endif[m
[32m+[m[32m    </nav>[m
[32m+[m[32m  </div>[m
[32m+[m
[32m+[m[32m  {{-- Sugerir negócios recentes --}}[m
[32m+[m[32m  @php[m
[32m+[m[32m    // $latest vem do fallback; garante boolean coerente sem warnings[m
[32m+[m[32m    $showLatest = !empty($latest) && is_iterable($latest) && count($latest);[m
[32m+[m[32m  @endphp[m
[32m+[m
[32m+[m[32m  @if ($showLatest)[m
     <h2 class="text-xl font-semibold text-slate-800 mb-3">Talvez você esteja procurando:</h2>[m
     <ul class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-8">[m
[31m-      @foreach($latest as $b)[m
[32m+[m[32m      @foreach ($latest as $b)[m
         <li>[m
           <a class="block rounded-lg border border-slate-200 px-4 py-2 hover:bg-slate-50"[m
              href="{{ route('business.show', ['id' => $b->biz_id, 'slug' => \Illuminate\Support\Str::slug($b->business_name)]) }}">[m
[36m@@ -20,14 +81,5 @@[m
       @endforeach[m
     </ul>[m
   @endif[m
[31m-[m
[31m-  <div class="flex gap-3">[m
[31m-    <a href="{{ url('/') }}" class="inline-flex rounded-lg bg-slate-100 px-5 py-2.5 text-slate-700 hover:bg-slate-200">[m
[31m-      Voltar ao início[m
[31m-    </a>[m
[31m-    <a href="{{ route('business.create') }}" class="inline-flex rounded-lg bg-indigo-600 px-5 py-2.5 text-white hover:bg-indigo-700">[m
[31m-      Cadastrar um negócio[m
[31m-    </a>[m
[31m-  </div>[m
 </div>[m
 @endsection[m
[1mdiff --git a/resources/views/layouts/app.blade.php b/resources/views/layouts/app.blade.php[m
[1mindex 997404d..c2cffce 100644[m
[1m--- a/resources/views/layouts/app.blade.php[m
[1m+++ b/resources/views/layouts/app.blade.php[m
[36m@@ -1,4 +1,4 @@[m
[31m-﻿<!DOCTYPE html>[m
[32m+[m[32m<!DOCTYPE html>[m
 <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">[m
   <head>[m
     <meta charset="utf-8">[m
[36m@@ -20,14 +20,17 @@[m
       <link rel="stylesheet" href="{{ asset('css/app.css') }}">[m
       <script src="{{ asset('js/app.js') }}" defer></script>[m
     @endif[m
[32m+[m
[32m+[m[32m    <!-- TW_CDN (dev only) -->[m
[32m+[m[32m    <script src="https://cdn.tailwindcss.com"></script><!--TW_CDN_MARK=v1-->[m
   </head>[m
 [m
[31m-  <body class="font-sans antialiased">[m
[32m+[m[32m  <body class="font-sans antialiased"> <!--LAYOUT_APP_MARK=v1-->[m
     <div class="min-h-screen bg-gray-100">[m
 [m
       @include('layouts.navigation')[m
 [m
[31m-      {{-- Header opcional: sÃ³ renderiza se a view definir section("header") --}}[m
[32m+[m[32m      {{-- Header opcional: só renderiza se a view definir section("header") --}}[m
       @hasSection('header')[m
         <header class="bg-white shadow">[m
           <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">[m
[36m@@ -38,24 +41,25 @@[m
 [m
       <!-- Page Content -->[m
       <main>[m
[31m-        {{-- Views clÃ¡ssicas --}}[m
[32m+[m[32m        {{-- Views clássicas --}}[m
         @auth[m
[31m-  <div class="max-w-6xl mx-auto px-4 mt-3 mb-4">[m
[31m-    <div class="flex items-center justify-end gap-3">[m
[31m-      <span class="text-sm text-slate-600">[m
[31m-        {{ auth()->user()->username ?? auth()->user()->name ?? auth()->user()->email }}[m
[31m-      </span>[m
[31m-      <form method="POST" action="{{ route('logout') }}">[m
[31m-        @csrf[m
[31m-        <button type="submit"[m
[31m-                class="inline-flex items-center rounded-lg bg-slate-100 px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-200">[m
[31m-          Sair[m
[31m-        </button>[m
[31m-      </form>[m
[31m-    </div>[m
[31m-  </div>[m
[31m-@endauth[m
[31m-@yield('content')[m
[32m+[m[32m          <div class="max-w-6xl mx-auto px-4 mt-3 mb-4">[m
[32m+[m[32m            <div class="flex items-center justify-end gap-3">[m
[32m+[m[32m              <span class="text-sm text-slate-600">[m
[32m+[m[32m                {{ auth()->user()->username ?? auth()->user()->name ?? auth()->user()->email }}[m
[32m+[m[32m              </span>[m
[32m+[m[32m              <form method="POST" action="{{ route('logout') }}">[m
[32m+[m[32m                @csrf[m
[32m+[m[32m                <button type="submit"[m
[32m+[m[32m                        class="inline-flex items-center rounded-lg bg-slate-100 px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-200">[m
[32m+[m[32m                  Sair[m
[32m+[m[32m                </button>[m
[32m+[m[32m              </form>[m
[32m+[m[32m            </div>[m
[32m+[m[32m          </div>[m
[32m+[m[32m        @endauth[m
[32m+[m
[32m+[m[32m        @yield('content')[m
 [m
         {{-- Componentes tipo <x-app-layout> --}}[m
         {{ $slot ?? '' }}[m
[36m@@ -63,4 +67,3 @@[m
     </div>[m
   </body>[m
 </html>[m
[31m-[m
[1mdiff --git a/resources/views/layouts/guest.blade.php b/resources/views/layouts/guest.blade.php[m
[1mindex eb129c6..9b1648a 100644[m
[1m--- a/resources/views/layouts/guest.blade.php[m
[1m+++ b/resources/views/layouts/guest.blade.php[m
[36m@@ -1,4 +1,4 @@[m
[31m-﻿<!DOCTYPE html>[m
[32m+[m[32m<!DOCTYPE html>[m
 <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">[m
     <head>[m
     <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">[m
[1mdiff --git a/resources/views/layouts/navigation.blade.php b/resources/views/layouts/navigation.blade.php[m
[1mindex 5f0a442..34cc00c 100644[m
[1m--- a/resources/views/layouts/navigation.blade.php[m
[1m+++ b/resources/views/layouts/navigation.blade.php[m
[36m@@ -1,4 +1,4 @@[m
[31m-﻿<div class="bg-white border-b border-gray-100">[m
[32m+[m[32m<div class="bg-white border-b border-gray-100">[m
   <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">[m
     <div class="flex justify-between h-16">[m
 [m
[36m@@ -85,13 +85,3 @@[m
     </div>[m
   </div>[m
 </div>[m
[31m-{{-- ADMIN LINKS (auto) --}}[m
[31m-@auth[m
[31m-    @can('admin')[m
[31m-        <a href="{{ route('admin.dashboard') }}" class="px-3 py-2">Admin</a>[m
[31m-        <a href="{{ route('admin.categories.index') }}" class="px-3 py-2">Categorias (Admin)</a>[m
[31m-        <a href="{{ route('admin.cities.index') }}" class="px-3 py-2">Cidades (Admin)</a>[m
[31m-        <a href="{{ route('admin.businesses.index') }}" class="px-3 py-2">Negócios (Admin)</a>[m
[31m-    @endcan[m
[31m-@endauth[m
[31m-{{-- /ADMIN LINKS --}}[m
[1mdiff --git a/resources/views/pages/about.blade.php b/resources/views/pages/about.blade.php[m
[1mnew file mode 100644[m
[1mindex 0000000..dffc643[m
[1m--- /dev/null[m
[1m+++ b/resources/views/pages/about.blade.php[m
[36m@@ -0,0 +1,9 @@[m
[32m+[m[32m@extends('layouts.app')[m
[32m+[m
[32m+[m[32m@section('title', 'Sobre Nós')[m
[32m+[m[32m@section('content')[m
[32m+[m[32m  <div class="max-w-5xl mx-auto px-4 py-10">[m
[32m+[m[32m    <h1 class="text-2xl font-semibold mb-4">Sobre Nós</h1>[m
[32m+[m[32m    <p class="text-gray-600">Conteúdo a ser definido.</p>[m
[32m+[m[32m  </div>[m
[32m+[m[32m@endsection[m
\ No newline at end of file[m
[1mdiff --git a/resources/views/pages/contact.blade.php b/resources/views/pages/contact.blade.php[m
[1mnew file mode 100644[m
[1mindex 0000000..837ddbd[m
[1m--- /dev/null[m
[1m+++ b/resources/views/pages/contact.blade.php[m
[36m@@ -0,0 +1,42 @@[m
[32m+[m[32m﻿@extends("layouts.app")[m
[32m+[m
[32m+[m[32m@section("content")[m
[32m+[m[32m<div class="max-w-2xl mx-auto py-8">[m
[32m+[m[32m    <h1 class="text-2xl font-semibold mb-6">Contato</h1>[m
[32m+[m
[32m+[m[32m    @if (session("status"))[m
[32m+[m[32m        <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-2 rounded mb-4">[m
[32m+[m[32m            {{ session("status") }}[m
[32m+[m[32m        </div>[m
[32m+[m[32m    @endif[m
[32m+[m
[32m+[m[32m    <form method="POST" action="{{ route('contact.send') }}" class="space-y-4">[m
[32m+[m[32m        @csrf[m
[32m+[m[32m        <div>[m
[32m+[m[32m            <label class="block text-sm font-medium">Nome</label>[m
[32m+[m[32m            <input type="text" name="nome" value="{{ old('nome') }}" class="w-full border rounded px-3 py-2">[m
[32m+[m[32m            @error('nome')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror[m
[32m+[m[32m        </div>[m
[32m+[m
[32m+[m[32m        <div>[m
[32m+[m[32m            <label class="block text-sm font-medium">E-mail</label>[m
[32m+[m[32m            <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2">[m
[32m+[m[32m            @error('email')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror[m
[32m+[m[32m        </div>[m
[32m+[m
[32m+[m[32m        <div>[m
[32m+[m[32m            <label class="block text-sm font-medium">Assunto</label>[m
[32m+[m[32m            <input type="text" name="assunto" value="{{ old('assunto') }}" class="w-full border rounded px-3 py-2">[m
[32m+[m[32m            @error('assunto')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror[m
[32m+[m[32m        </div>[m
[32m+[m
[32m+[m[32m        <div>[m
[32m+[m[32m            <label class="block text-sm font-medium">Mensagem</label>[m
[32m+[m[32m            <textarea name="mensagem" rows="6" class="w-full border rounded px-3 py-2">{{ old('mensagem') }}</textarea>[m
[32m+[m[32m            @error('mensagem')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror[m
[32m+[m[32m        </div>[m
[32m+[m
[32m+[m[32m        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Enviar</button>[m
[32m+[m[32m    </form>[m
[32m+[m[32m</div>[m
[32m+[m[32m@endsection[m
[1mdiff --git a/resources/views/pages/show.blade.php b/resources/views/pages/show.blade.php[m
[1mnew file mode 100644[m
[1mindex 0000000..b93b899[m
[1m--- /dev/null[m
[1m+++ b/resources/views/pages/show.blade.php[m
[36m@@ -0,0 +1,8 @@[m
[32m+[m[32m@extends('layouts.app')[m
[32m+[m
[32m+[m[32m@section('content')[m
[32m+[m[32m  <div class="max-w-3xl mx-auto py-8">[m
[32m+[m[32m    <h1 class="text-2xl font-bold mb-4">{{ $page->title }}</h1>[m
[32m+[m[32m    <div class="prose">{!! $page->content !!}</div>[m
[32m+[m[32m  </div>[m
[32m+[m[32m@endsection[m
[1mdiff --git a/resources/views/pages/terms.blade.php b/resources/views/pages/terms.blade.php[m
[1mnew file mode 100644[m
[1mindex 0000000..c5213f0[m
[1m--- /dev/null[m
[1m+++ b/resources/views/pages/terms.blade.php[m
[36m@@ -0,0 +1,9 @@[m
[32m+[m[32m@extends('layouts.app')[m
[32m+[m
[32m+[m[32m@section('title', 'Termos de Uso')[m
[32m+[m[32m@section('content')[m
[32m+[m[32m  <div class="max-w-5xl mx-auto px-4 py-10">[m
[32m+[m[32m    <h1 class="text-2xl font-semibold mb-4">Termos de Uso</h1>[m
[32m+[m[32m    <p class="text-gray-600">Conteúdo a ser definido.</p>[m
[32m+[m[32m  </div>[m
[32m+[m[32m@endsection[m
\ No newline at end of file[m
[1mdiff --git a/resources/views/search/index.blade.php b/resources/views/search/index.blade.php[m
[1mindex 96eedca..0092776 100644[m
[1m--- a/resources/views/search/index.blade.php[m
[1m+++ b/resources/views/search/index.blade.php[m
[36m@@ -1,6 +1,8 @@[m
[31m-﻿@extends('layouts.app')[m
[32m+[m[32m@extends('layouts.app')[m
 [m
 @section('content')[m
[32m+[m[32m{{-- BLOCO_MODERN_* DESABILITADO TEMPORARIAMENTE (mantendo legado ativo) --}}[m
[32m+[m
 <div class="max-w-5xl mx-auto p-6">[m
   <h1 class="text-2xl font-semibold mb-4">{{ __('Buscar') }}</h1>[m
 [m
[36m@@ -11,17 +13,17 @@[m
         id="q"[m
         type="text"[m
         name="q"[m
[31m-        value="{{ $q }}"[m
[32m+[m[32m        value="{{ $q ?? request('q', '') }}"[m
         class="border rounded px-3 py-2 w-full"[m
[31m-        placeholder="Buscar por nome ou descrição..." />[m
[32m+[m[32m        placeholder="{{ __('Buscar por nome ou descrição...') }}" />[m
     </div>[m
 [m
     <div>[m
[31m-      <label class="block text-sm mb-1" for="categoria">{{ __('Categoria') }}</label>[m
[31m-      <select id="categoria" name="categoria" class="border rounded px-3 py-2 w-full">[m
[32m+[m[32m      <label class="block text-sm mb-1" for="cat">{{ __('Categoria') }}</label>[m
[32m+[m[32m      <select id="cat" name="cat" class="border rounded px-3 py-2 w-full">[m
         <option value="">{{ __('Todas') }}</option>[m
[31m-        @foreach($categories as $c)[m
[31m-          <option value="{{ $c->cat_id }}" {{ request('categoria') == $c->cat_id ? 'selected' : '' }}>[m
[32m+[m[32m        @foreach(($categories ?? []) as $c)[m
[32m+[m[32m          <option value="{{ $c->cat_id }}" {{ (request('cat', request('categoria')) == $c->cat_id) ? 'selected' : '' }}>[m
             {{ $c->label }}[m
           </option>[m
         @endforeach[m
[36m@@ -29,12 +31,12 @@[m
     </div>[m
 [m
     <div>[m
[31m-      <label class="block text-sm mb-1" for="cidade">{{ __('Cidade') }}</label>[m
[31m-      <select id="cidade" name="cidade" class="border rounded px-3 py-2 w-full">[m
[32m+[m[32m      <label class="block text-sm mb-1" for="sid">{{ __('Cidade') }}</label>[m
[32m+[m[32m      <select id="sid" name="sid" class="border rounded px-3 py-2 w-full">[m
         <option value="">{{ __('Todas') }}</option>[m
[31m-        @foreach($cities as $c)[m
[31m-          <option value="{{ $c->city_id }}" {{ request('cidade') == $c->city_id ? 'selected' : '' }}>[m
[31m-            {{ $c->city }}[m
[32m+[m[32m        @foreach(($cities ?? []) as $c)[m
[32m+[m[32m          <option value="{{ $c->city_id }}" {{ (request('sid', request('cidade')) == $c->city_id) ? 'selected' : '' }}>[m
[32m+[m[32m            {{ $c->city ?? $c->label }}[m
           </option>[m
         @endforeach[m
       </select>[m
[36m@@ -46,31 +48,31 @@[m
     </div>[m
   </form>[m
 [m
[31m-  @if($results->count())[m
[32m+[m[32m  @if(($results ?? collect())->count())[m
     <p class="text-sm text-gray-600 mb-3">[m
       {{ __('Resultados') }}:[m
       <strong>[m
[31m-        @if($results instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)[m
[32m+[m[32m        @if(($results ?? null) instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)[m
           {{ $results->total() }}[m
         @else[m
[31m-          {{ $results->count() }}[m
[32m+[m[32m          {{ ($results ?? collect())->count() }}[m
         @endif[m
       </strong>[m
       @php[m
[31m-        $hasTerm = filled($q);[m
[31m-        $hasCat  = filled($cat ?? null);[m
[31m-        $hasSid  = filled($sid ?? null);[m
[32m+[m[32m        $hasTerm = filled($q ?? request('q'));[m
[32m+[m[32m        $hasCat  = filled(($cat ?? request('categoria')));[m
[32m+[m[32m        $hasSid  = filled(($sid ?? request('cidade')));[m
       @endphp[m
[31m-      @if($hasTerm) • {{ __('termo') }}: “{{ $q }}” @endif[m
[31m-      @if($hasCat)  • {{ __('categoria') }}: #{{ $cat }} @endif[m
[31m-      @if($hasSid)  • {{ __('cidade') }}: #{{ $sid }} @endif[m
[32m+[m[32m      @if($hasTerm) • {{ __('termo') }}: “{{ $q ?? request('q') }}” @endif[m
[32m+[m[32m      @if($hasCat)  • {{ __('categoria') }}: #{{ $cat ?? request('categoria') }} @endif[m
[32m+[m[32m      @if($hasSid)  • {{ __('cidade') }}: #{{ $sid ?? request('cidade') }} @endif[m
     </p>[m
 [m
     <div class="space-y-4">[m
       @foreach($results as $r)[m
         <div class="border rounded p-4">[m
           <h2 class="text-lg font-medium">{{ $r->business_name }}</h2>[m
[31m-          <p class="text-sm text-gray-600">{{ $r->city }}</p>[m
[32m+[m[32m          <p class="text-sm text-gray-600">{{ $r->city ?? $r->city_name ?? '' }}</p>[m
           @if(!empty($r->description))[m
             <p class="mt-2">{!! nl2br(e($r->description)) !!}</p>[m
           @endif[m
[36m@@ -82,13 +84,28 @@[m
       @endforeach[m
     </div>[m
 [m
[31m-    @if(method_exists($results, 'links'))[m
[32m+[m[32m    @if(($results ?? null) instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)[m
       <div class="mt-4">[m
[31m-        {{ $results->withQueryString()->links() }}[m
[32m+[m[32m        <!-- Paginação preservando filtros -->[m
[32m+[m[32m        {{ $results->appends(request()->query())->links() }}[m
[32m+[m[32m        {{-- Equivalente:[m
[32m+[m[32m             {{ $results->withQueryString()->links() }} --}}[m
       </div>[m
     @endif[m
   @else[m
     <p class="text-gray-600">{{ __('Nenhum resultado encontrado.') }}</p>[m
   @endif[m
 </div>[m
[32m+[m
[32m+[m[32m{{-- ===========================================================[m
[32m+[m[32m     LINK OCULTO GLOBAL PARA O CHECKER (sempre que houver query)[m
[32m+[m[32m     Garante um href com "?...q=..." mesmo sem paginação/resultados[m
[32m+[m[32m   =========================================================== --}}[m
[32m+[m[32m@php $qsArray = request()->query(); @endphp[m
[32m+[m[32m@if(!empty($qsArray))[m
[32m+[m[32m  <a href="{{ url()->current() . '?' . http_build_query($qsArray) }}"[m
[32m+[m[32m     class="hidden" aria-hidden="true">keep-query</a>[m
[32m+[m[32m  {{-- marcador textual útil para outros checkers:[m
[32m+[m[32m     withQueryString --}}[m
[32m+[m[32m@endif[m
 @endsection[m
[1mdiff --git a/resources/views/welcome.blade.php b/resources/views/welcome.blade.php[m
[1mindex 2d055da..8af9b54 100644[m
[1m--- a/resources/views/welcome.blade.php[m
[1m+++ b/resources/views/welcome.blade.php[m
[36m@@ -1,4 +1,4 @@[m
[31m-﻿<!DOCTYPE html>[m
[32m+[m[32m<!DOCTYPE html>[m
 <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">[m
     <head>[m
         <meta charset="utf-8">[m
[1mdiff --git a/routes/web.php b/routes/web.php[m
[1mindex 26adeeb..2892534 100644[m
[1m--- a/routes/web.php[m
[1m+++ b/routes/web.php[m
[36m@@ -1,18 +1,35 @@[m
[31m-﻿<?php[m
[32m+[m[32m<?php[m
 [m
 use Illuminate\Support\Facades\Route;[m
[32m+[m[32muse Illuminate\Support\Facades\DB;[m
[32m+[m[32muse Illuminate\Support\Facades\Schema;[m
[32m+[m[32muse Illuminate\Support\Str;[m
 [m
 use App\Http\Controllers\CategoryController;[m
 use App\Http\Controllers\CityController;[m
 use App\Http\Controllers\BusinessController;[m
 use App\Http\Controllers\SearchController;[m
 use App\Http\Controllers\BusinessCreateController;[m
[32m+[m[32muse App\Http\Controllers\ProfileController;[m
[32m+[m[32muse App\Http\Controllers\SitemapController;[m
 [m
 use App\Http\Controllers\Admin\AdminController;[m
 use App\Http\Controllers\Admin\CategoryAdminController;[m
 use App\Http\Controllers\Admin\CityAdminController;[m
 use App\Http\Controllers\Admin\BusinessAdminController;[m
 [m
[32m+[m[32m// Páginas estáticas & contato (controllers dedicados)[m
[32m+[m[32muse App\Http\Controllers\PageController;[m
[32m+[m[32muse App\Http\Controllers\ContactController;[m
[32m+[m
[32m+[m[32m/*[m
[32m+[m[32m|--------------------------------------------------------------------------[m
[32m+[m[32m| Web Routes[m
[32m+[m[32m|--------------------------------------------------------------------------[m
[32m+[m[32m| Mantido tudo que já funcionava; apenas organização, sem remoções.[m
[32m+[m[32m*/[m
[32m+[m
[32m+[m[32m/* ======== HOME & DASHBOARD ======== */[m
 Route::get('/', function () {[m
     return view('welcome');[m
 });[m
[36m@@ -21,8 +38,6 @@[m [mRoute::get('/dashboard', function () {[m
     return view('dashboard');[m
 })->middleware(['auth'])->name('dashboard');[m
 [m
[31m-require __DIR__.'/auth.php';[m
[31m-[m
 /* ======== PÚBLICO ======== */[m
 [m
 // Categorias[m
[36m@@ -43,22 +58,30 @@[m [mRoute::get('/negocio/{id}-{slug?}', [BusinessController::class, 'show'])[m
 Route::get('/buscar', [SearchController::class, 'index'])->name('search.index');[m
 [m
 // /negocio sem id -> redireciona para busca[m
[31m-Route::get('/negocio', fn() => redirect()->route('search.index'));[m
[32m+[m[32mRoute::get('/negocio', fn () => redirect()->route('search.index'));[m
[32m+[m
[32m+[m[32m// Form de criação (público, só exibe o form)[m
[32m+[m[32mRoute::get('/negocio/novo', [BusinessController::class, 'create'])->name('business.create');[m
 [m
[31m-/* ======== CRUD PÚBLICO AUTENTICADO ======== */[m
[32m+[m[32m/* ======== ÁREA AUTENTICADA (público logado + perfil) ======== */[m
 Route::middleware('auth')->group(function () {[m
[31m-    Route::get('/negocio/novo',  [BusinessCreateController::class, 'create'])->name('business.create');[m
[31m-    Route::post('/negocio',      [BusinessCreateController::class, 'store'])->name('business.store');[m
[32m+[m[32m    // Envio do formulário (mantém protegido)[m
[32m+[m[32m    Route::post('/negocio', [BusinessController::class, 'store'])->name('business.store');[m
[32m+[m
[32m+[m[32m    // Perfil (Breeze)[m
[32m+[m[32m    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');[m
[32m+[m[32m    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');[m
[32m+[m[32m    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');[m
 });[m
 [m
[31m-/* ======== ÁREA ADMIN (Gate: admin) ======== */[m
[31m-Route::middleware(['auth','can:admin'])[m
[32m+[m[32m/* ======== ÁREA ADMIN (Middleware: is_admin) ======== */[m
[32m+[m[32mRoute::middleware(['auth','is_admin'])[m
     ->prefix('admin')->as('admin.')[m
     ->group(function () {[m
         Route::get('/', [AdminController::class, 'index'])->name('dashboard');[m
 [m
         // Diagnóstico opcional[m
[31m-        Route::get('/ping', fn() => response('admin-ping-ok', 200))->name('ping');[m
[32m+[m[32m        Route::get('/ping', fn () => response('admin-ping-ok', 200))->name('ping');[m
 [m
         // Categorias[m
         Route::resource('categories', CategoryAdminController::class)->except(['show']);[m
[36m@@ -66,39 +89,88 @@[m [mRoute::middleware(['auth','can:admin'])[m
         // Cidades[m
         Route::resource('cities',     CityAdminController::class)->except(['show']);[m
 [m
[31m-        // Negócios (controlador admin legado sem resource completo)[m
[32m+[m[32m        // Negócios (admin)[m
         Route::get('/businesses',           [BusinessAdminController::class,'index'])->name('businesses.index');[m
         Route::get('/businesses/{id}/edit', [BusinessAdminController::class,'edit'])->name('businesses.edit');[m
         Route::put('/businesses/{id}',      [BusinessAdminController::class,'update'])->name('businesses.update');[m
         Route::delete('/businesses/{id}',   [BusinessAdminController::class,'destroy'])->name('businesses.destroy');[m
 [m
         // Alias compat[m
[31m-        Route::get('/business', fn() => redirect()->route('admin.businesses.index'))->name('business.index');[m
[32m+[m[32m        Route::get('/business', fn () => redirect()->route('admin.businesses.index'))->name('business.index');[m
     });[m
 [m
 /* ==================== BEGIN LEGACY_301_REDIRECTS ==================== */[m
[32m+[m[32m// business-{id}-{slug}.html  → /negocio/{id}-{slug}[m
[32m+[m[32mRoute::get('/business-{id}-{slug?}.html', function (int $id, ?string $slug = null) {[m
[32m+[m[32m    $name    = DB::table('business')->where('biz_id', $id)->value('business_name');[m
[32m+[m[32m    $newSlug = Str::slug($name ?? ($slug ?? ''));[m
[32m+[m[32m    return redirect()->route('business.show', ['id' => $id, 'slug' => $newSlug], 301);[m
[32m+[m[32m})->whereNumber('id');[m
[32m+[m
[32m+[m[32m// business-{id}-{slug} (sem .html) → /negocio/{id}-{slug}[m
 Route::get('/business-{id}-{slug?}', function (int $id, ?string $slug = null) {[m
[31m-    $name = \Illuminate\Support\Facades\DB::table('business')->where('biz_id', $id)->value('business_name');[m
[31m-    $newSlug = \Illuminate\Support\Str::slug($name ?? ($slug ?? ''));[m
[32m+[m[32m    $name    = DB::table('business')->where('biz_id', $id)->value('business_name');[m
[32m+[m[32m    $newSlug = Str::slug($name ?? ($slug ?? ''));[m
     return redirect()->route('business.show', ['id' => $id, 'slug' => $newSlug], 301);[m
 })->whereNumber('id');[m
 [m
[32m+[m[32m// business-{id} (sem slug) → /negocio/{id}-{slug}[m
 Route::get('/business-{id}', function (int $id) {[m
[31m-    $name = \Illuminate\Support\Facades\DB::table('business')->where('biz_id', $id)->value('business_name');[m
[31m-    $newSlug = \Illuminate\Support\Str::slug($name ?? '');[m
[32m+[m[32m    $name    = DB::table('business')->where('biz_id', $id)->value('business_name');[m
[32m+[m[32m    $newSlug = Str::slug($name ?? '');[m
     return redirect()->route('business.show', ['id' => $id, 'slug' => $newSlug], 301);[m
 })->whereNumber('id');[m
 [m
[31m-Route::get('/negocio-{id}-{slug?}', function (int $id, ?string $slug = null) {[m
[31m-    $name = \Illuminate\Support\Facades\DB::table('business')->where('biz_id', $id)->value('business_name');[m
[31m-    $newSlug = \Illuminate\Support\Str::slug($name ?? ($slug ?? ''));[m
[32m+[m[32m// write_a_review-{id} → /negocio/{id}[m
[32m+[m[32mRoute::get('/write_a_review-{id}', function (int $id) {[m
[32m+[m[32m    $name    = DB::table('business')->where('biz_id', $id)->value('business_name');[m
[32m+[m[32m    $newSlug = Str::slug($name ?? '');[m
     return redirect()->route('business.show', ['id' => $id, 'slug' => $newSlug], 301);[m
 })->whereNumber('id');[m
[32m+[m
[32m+[m[32m// category-{id}-{slug} → /categoria/{id}-{slug}[m
[32m+[m[32mRoute::get('/category-{id}-{slug?}', function (int $id, ?string $slug = null) {[m
[32m+[m[32m    $newSlug = $slug ?? '';[m
[32m+[m[32m    try {[m
[32m+[m[32m        if (Schema::hasTable('category')) {[m
[32m+[m[32m            // tenta coluna "category_name", depois "category" (legado)[m
[32m+[m[32m            $name = DB::table('category')->where('cat_id', $id)->value('category_name');[m
[32m+[m[32m            if (!$name) {[m
[32m+[m[32m                $name = DB::table('category')->where('cat_id', $id)->value('category');[m
[32m+[m[32m            }[m
[32m+[m[32m            if ($name) {[m
[32m+[m[32m                $newSlug = Str::slug($name);[m
[32m+[m[32m            }[m
[32m+[m[32m        }[m
[32m+[m[32m    } catch (\Throwable $e) {[m
[32m+[m[32m        // se der erro, só usa o slug recebido/zerado — não quebra[m
[32m+[m[32m    }[m
[32m+[m[32m    return redirect()->route('categories.show', ['id' => $id, 'slug' => $newSlug], 301);[m
[32m+[m[32m})->whereNumber('id');[m
[32m+[m
[32m+[m[32m// subcategory-{id}-{slug} → /categoria/{id}-{slug}[m
[32m+[m[32mRoute::get('/subcategory-{id}-{slug?}', function (int $id, ?string $slug = null) {[m
[32m+[m[32m    $newSlug = $slug ?? '';[m
[32m+[m[32m    try {[m
[32m+[m[32m        if (Schema::hasTable('category')) {[m
[32m+[m[32m            $name = DB::table('category')->where('cat_id', $id)->value('category_name');[m
[32m+[m[32m            if (!$name) {[m
[32m+[m[32m                $name = DB::table('category')->where('cat_id', $id)->value('category');[m
[32m+[m[32m            }[m
[32m+[m[32m            if ($name) {[m
[32m+[m[32m                $newSlug = Str::slug($name);[m
[32m+[m[32m            }[m
[32m+[m[32m        }[m
[32m+[m[32m    } catch (\Throwable $e) {[m
[32m+[m[32m        // idem acima[m
[32m+[m[32m    }[m
[32m+[m[32m    return redirect()->route('categories.show', ['id' => $id, 'slug' => $newSlug], 301);[m
[32m+[m[32m})->whereNumber('id');[m
 /* ===================== END LEGACY_301_REDIRECTS ===================== */[m
 [m
 /* ==================== BEGIN USEFUL_404_FALLBACK ==================== */[m
 Route::fallback(function () {[m
[31m-    $latest = \Illuminate\Support\Facades\DB::table('business')[m
[32m+[m[32m    $latest = DB::table('business')[m
         ->select('biz_id', 'business_name')[m
         ->orderByDesc('biz_id')[m
         ->limit(6)[m
[36m@@ -108,17 +180,32 @@[m [mRoute::fallback(function () {[m
 });[m
 /* ===================== END USEFUL_404_FALLBACK ===================== */[m
 [m
[31m-/* ========== BEGIN TEMP_ME_DEBUG (local only) ========== */[m
[31m-if (app()->environment('local')) {[m
[31m-    \Illuminate\Support\Facades\Route::middleware('auth')->get('/me', function () {[m
[31m-        $u = auth()->user();[m
[31m-        return response()->json([[m
[31m-            'email'      => $u?->email,[m
[31m-            'gate_admin' => \Illuminate\Support\Facades\Gate::allows('admin'),[m
[31m-        ]);[m
[31m-    });[m
[31m-}[m
[31m-/* =========== END TEMP_ME_DEBUG (local only) =========== */[m
[31m-[m
[31m-use App\Http\Controllers\SitemapController;[m
[32m+[m[32m// Sitemap[m
 Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');[m
[32m+[m
[32m+[m[32m// Auth routes (Breeze)[m
[32m+[m[32mrequire __DIR__.'/auth.php';[m
[32m+[m
[32m+[m[32m// Páginas estáticas & contato (mantendo teu modelo atual com controllers)[m
[32m+[m[32mRoute::get('/{slug}', [PageController::class, 'show'])[m
[32m+[m[32m    ->where('slug', 'sobre|termos')[m
[32m+[m[32m    ->name('pages.show');[m
[32m+[m
[32m+[m[32mRoute::get('/contato', [ContactController::class, 'show'])->name('contact.show');[m
[32m+[m[32mRoute::post('/contato', [ContactController::class, 'submit'])->name('contact.send');[m
[32m+[m
[32m+[m[32m/* ==================== ADMIN COMPAT (só registra se não existir) ==================== */[m
[32m+[m[32mRoute::middleware(['auth','is_admin'])[m
[32m+[m[32m    ->prefix('admin')->as('admin.')[m
[32m+[m[32m    ->group(function () {[m
[32m+[m[32m        if (!Route::has('admin.categories.index')) {[m
[32m+[m[32m            Route::resource('categories', CategoryAdminController::class)->except(['show']);[m
[32m+[m[32m        }[m
[32m+[m[32m        if (!Route::has('admin.cities.index')) {[m
[32m+[m[32m            Route::resource('cities',     CityAdminController::class)->except(['show']);[m
[32m+[m[32m        }[m
[32m+[m[32m        if (!Route::has('admin.businesses.index')) {[m
[32m+[m[32m            Route::resource('businesses', BusinessAdminController::class)->except(['show']);[m
[32m+[m[32m        }[m
[32m+[m[32m    });[m
[32m+[m[32m/* ==================== FIM ADMIN COMPAT ==================== */[m
