<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Category;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CityController extends Controller
{
    // GET /cidades
    public function index()
    {
        $cities = City::orderBy("city_id")->get();
        return view("cities.index", compact("cities"));
    }

    // GET /cidade/{id}-{slug?}
    public function show($id, $slug = null, Request $request)
    {
        $city = City::where("city_id", $id)->firstOrFail();

        // base: todos os negócios da cidade
        $q = Business::where("sid", $city->city_id);

        // filtro por categoria (cat_id)
        $catId = $request->integer("categoria");
        if ($catId) {
            $q->where("cid", $catId);
        }

        // filtro por termo (nome do negócio)
        $term = trim((string) $request->get("q", ""));
        if ($term !== "") {
            $q->where("business_name", "LIKE", "%".$term."%");
        }

        // ordenação + paginação
        $businesses = $q->orderBy("biz_id", "desc")
                        ->paginate(12)
                        ->withQueryString();

        // categorias para o <select>
        $categories = Category::orderBy("category")->get();

        return view("cities.show", [
            "city"        => $city,
            "businesses"  => $businesses,
            "categories"  => $categories,
            "filters"     => [
                "categoria" => $catId,
                "q"         => $term,
            ],
        ]);
    }
}