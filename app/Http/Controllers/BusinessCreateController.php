<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBusinessRequest;
use App\Models\Business;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class BusinessCreateController extends Controller
{
    /**
     * GET /negocio/novo
     */
    public function create(): View
    {
        // Categorias (tabela legado: category -> cat_id, category)
        $categories = DB::table('category')
            ->selectRaw('cat_id, category AS label')
            ->orderBy('label')
            ->get();

        // Cidades (tabela legado: city -> city_id, city)
        $cities = DB::table('city')
            ->selectRaw('city_id, city AS label')
            ->orderBy('label')
            ->get();

        return view('business.create', compact('categories', 'cities'));
    }

    /**
     * POST /negocio
     */
    public function store(StoreBusinessRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Garante cidade válida (independente de Model)
        $city = DB::table('city')->where('city_id', $data['sid'])->first();
        if (! $city) {
            abort(404, 'Cidade não encontrada');
        }

        // Cria registro na tabela legado: business
        $biz = new Business();
        $biz->business_name = $data['business_name'];
        $biz->description   = $data['description'] ?? null;
        $biz->cid           = (int) $data['cid'];           // FK categoria (legado)
        $biz->sid           = (int) $city->city_id;         // FK cidade (legado)
        $biz->city          = $city->city ?? null;          // compat: nome da cidade em texto
        $biz->save();

        // ===== Upload & Thumbs (opcional) =====
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $dir = "businesses/{$biz->biz_id}";

            // carrega e normaliza rotação/EXIF
            $img = Image::make($request->file('image')->getRealPath())->orientate();

            // original (padroniza em webp)
            $orig = (clone $img)->encode('webp', 90);
            Storage::disk('public')->put("$dir/orig.webp", $orig);

            // large ~800x600
            $lg = (clone $img)
                ->resize(800, 600, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                })
                ->encode('webp', 85);
            Storage::disk('public')->put("$dir/lg.webp", $lg);

            // small/thumb ~360x240
            $sm = (clone $img)
                ->resize(360, 240, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                })
                ->encode('webp', 80);
            Storage::disk('public')->put("$dir/sm.webp", $sm);

            // salva caminhos relativos ao disco 'public'
            $biz->image    = "$dir/orig.webp";
            $biz->image_lg = "$dir/lg.webp";
            $biz->image_sm = "$dir/sm.webp";
            $biz->save();
        }
        // ===== /Upload & Thumbs =====

        return redirect()
            ->route('business.show', [
                'id'   => $biz->biz_id,
                'slug' => Str::slug($biz->business_name),
            ])
            ->with('success', 'Negócio cadastrado com sucesso.');
    }
}
