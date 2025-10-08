<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Business;
use App\Models\Category;
use App\Models\City;

class BusinessCreateController extends Controller
{
    // GET /negocio/novo
    public function create()
    {
        // label da categoria: category OU cat_name
        if (Schema::hasColumn("categories", "category")) {
            $categories = Category::select(["cat_id"])->selectRaw("category AS label")->orderBy("label")->get();
        } elseif (Schema::hasColumn("categories", "cat_name")) {
            $categories = Category::select(["cat_id"])->selectRaw("cat_name AS label")->orderBy("label")->get();
        } else {
            $categories = Category::select(["cat_id"])->selectRaw("CAST(cat_id AS CHAR) AS label")->orderBy("cat_id")->get();
        }

        $cities = City::orderBy("city")->get(["city_id", "city"]);

        return view('business.create', compact("categories", "cities"));
    }

    // POST /negocio
    public function store(Request $request)
    {
        // validaÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â£o mÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â­nima
        $data = $request->validate([
            "business_name" => ["required","string","max:190"],
            "description"   => ["nullable","string"],
            "categoria"     => ["required","integer","min:1"],
            "cidade"        => ["required","integer","min:1"], // city_id
        ]);

        // cidade escolhida
        $city = City::where("city_id", $data["cidade"])->firstOrFail();

        // cria registro (tabela legado: business)
        $biz = new Business();
        $biz->business_name = $data["business_name"];
        $biz->description   = $data["description"] ?? null;
        $biz->cid           = (int) $data["categoria"];    // fk categoria (legado)
        $biz->sid           = (int) $city->city_id;        // fk cidade (legado)
        $biz->city          = $city->city ?? null;         // compat: nome da cidade em texto
        // outros campos do legado podem ficar nulos por enquanto

        $biz->save();

        // redirect pro detalhe
        $slug = Str::slug($biz->business_name);
        return redirect()->route("business.show", [$biz->biz_id, $slug])
                         ->with("status","NegÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â³cio cadastrado com sucesso.");
    }
}
