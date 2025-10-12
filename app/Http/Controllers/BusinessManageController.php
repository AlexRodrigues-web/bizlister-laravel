<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBusinessRequest;
use App\Models\Business;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\ImageManagerStatic as Image;

class BusinessManageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $items = Business::orderBy('biz_id', 'desc')->paginate(15);
        return view('admin.business.index', compact('items'));
    }

    public function edit($biz): View
    {
        $item       = Business::where('biz_id', $biz)->firstOrFail();
        $categories = Category::orderBy('category')->get();
        $cities     = City::orderBy('city')->get();

        return view('admin.business.edit', compact('item','categories','cities'));
    }

    public function update(Request $request, $biz): RedirectResponse
    {
        $item = Business::where('biz_id', $biz)->firstOrFail();

        // Validação mínima (compatível com legado)
        $rules = [
            'business_name' => 'required|string|max:255',
            'description'   => 'nullable|string',
        ];
        if (Schema::hasColumn('business','cid')) {
            $rules['cid'] = 'nullable|integer';
        }
        if (Schema::hasColumn('business','city') || Schema::hasColumn('business','city_id')) {
            $rules['city_id'] = 'nullable|integer';
        }

        $data = $request->validate($rules);

        // Atribuições
        if (array_key_exists('business_name', $data)) $item->business_name = $data['business_name'];
        if (array_key_exists('description',   $data)) $item->description   = $data['description'];

        if (Schema::hasColumn('business','cid') && $request->filled('cid')) {
            $item->cid = (int) $request->input('cid');
        }

        // city/city_id (usa o que existir)
        if ($request->filled('city_id')) {
            if (Schema::hasColumn('business','city_id')) {
                $item->city_id = (int) $request->input('city_id');
            } elseif (Schema::hasColumn('business','city')) {
                // tabela legacy com nome da cidade em 'city'
                $city = City::where('city_id', (int) $request->input('city_id'))->first();
                if ($city) { $item->city = $city->city; }
            }
        }

        $item->save();

        return redirect()
            ->route('admin.business.index')
            ->with('success', 'Negócio atualizado.');
    }

    public function create(): View
    {
        // Detecta coluna de rótulo da categoria em tabela legada
        $catCols     = Schema::getColumnListing('category');
        $catPriority = ['category','title','name','cat_name'];
        $catLabelCol = 'category';
        foreach ($catPriority as $col) {
            if (in_array($col, $catCols, true)) { $catLabelCol = $col; break; }
        }

        $categories = DB::table('category')
            ->select('cat_id', DB::raw($catLabelCol.' as label'))
            ->orderBy('label', 'asc')
            ->get();

        $cities = DB::table('city')
            ->select('city_id','city')
            ->orderBy('city','asc')
            ->get();

        // Ajuste o caminho da view caso seu projeto use pasta admin aqui também
        return view('businesses.create', compact('categories','cities'));
    }

    public function store(StoreBusinessRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $biz = new Business();

        // Básico
        $biz->business_name = $data['business_name'];
        $biz->description   = $data['description'] ?? '';

        // FKs
        $biz->cid = (int) ($data['category_id'] ?? 0);
        $biz->sid = (int) ($data['city_id'] ?? 0);

        // City textual (legado)
        if (Schema::hasColumn('business','city')) {
            $cityName = DB::table('city')->where('city_id', $biz->sid)->value('city');
            $biz->city = $cityName ?: '';
        }

        // Upload de imagem (salva original + gera large e thumb)
        $imagePath = null;
        $basename  = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('businesses', 'public'); // ex.: businesses/abc.webp
            $basename  = basename($imagePath);

            $sourceAbs = Storage::disk('public')->path($imagePath);

            // Garante pastas
            Storage::disk('public')->makeDirectory('businesses/large');
            Storage::disk('public')->makeDirectory('businesses/thumbs');

            // cria large 800x600 (crop central com fit preservando proporção)
            $largeAbs = Storage::disk('public')->path("businesses/large/{$basename}");
            Image::make($sourceAbs)->orientate()->fit(800, 600, function($c){ $c->upsize(); })->save($largeAbs, 85);

            // cria thumb 360x240
            $thumbAbs = Storage::disk('public')->path("businesses/thumbs/{$basename}");
            Image::make($sourceAbs)->orientate()->fit(360, 240, function($c){ $c->upsize(); })->save($thumbAbs, 85);
        }

        // Persistir colunas existentes
        if ($imagePath && Schema::hasColumn('business','image')) {
            $biz->image = $imagePath; // original
        }
        if (Schema::hasColumn('business','featured_image')) {
            // usa versão large se gerada; senão, original ou string vazia
            $biz->featured_image = $basename ? "businesses/large/{$basename}" : ($imagePath ?: '');
        }

        // Texto (varchar) -> ''
        $textCols = [
            'address','email','phone','mobile','website','map','opening_hours','tags',
            'facebook','twitter','gplus','pinterest','instagram','youtube','linkedin',
            'seo_title','seo_description','contact_name','whatsapp','slug',
            'latitude','longitude'
        ];
        foreach ($textCols as $col) {
            if (Schema::hasColumn('business',$col) && ($biz->$col ?? null) === null) { $biz->$col = ''; }
        }

        // 'menu' (varchar)
        if (Schema::hasColumn('business','menu') && ($biz->menu ?? null) === null) {
            $biz->menu = '';
        }

        // 'date' (varchar)
        if (Schema::hasColumn('business','date') && empty($biz->date)) {
            $biz->date = now()->format('Y-m-d H:i:s');
        }

        // 'avg' (varchar)
        if (Schema::hasColumn('business','avg') && ($biz->avg ?? null) === null) {
            $biz->avg = '0';
        }

        // Inteiros/flags -> 0
        $intCols = [
            'active','approved','status','featured','priority','views','rating','votes',
            'country_id','state_id','reviews','comments','hits','bookmarks',
            'star1','star2','star3','star4','star5','rating_total','rating_count','tot','feat'
        ];
        foreach ($intCols as $col) {
            if (Schema::hasColumn('business',$col) && ($biz->$col ?? null) === null) { $biz->$col = 0; }
        }

        // Dono do negócio (se existir a coluna biz_user)
        if (Schema::hasColumn('business','biz_user') && ($biz->biz_user ?? null) === null) {
            $biz->biz_user = Auth::id() ?: 0;
        }

        // unique_biz (varchar) como UUID se existir a coluna
        if (Schema::hasColumn('business','unique_biz') && empty($biz->unique_biz)) {
            $biz->unique_biz = (string) Str::uuid();
        }

        $biz->save();

        return redirect()
            ->route('business.show', [$biz->biz_id, Str::slug($biz->business_name)])
            ->with('status', 'Negócio cadastrado com sucesso!');
    }

    public function destroy($biz): RedirectResponse
    {
        $item = Business::where('biz_id', $biz)->firstOrFail();

        // Remove arquivos no storage/public se houver imagem
        $orig = $item->image ?? null;
        if ($orig) {
            $basename = basename($orig);
            $paths = [
                "businesses/large/$basename",
                "businesses/thumbs/$basename",
            ];
            foreach ($paths as $p) {
                try { Storage::disk('public')->delete($p); } catch (\Throwable $e) {}
            }
        }

        $item->delete();

        return back()->with('success', 'Negócio removido.');
    }
}
