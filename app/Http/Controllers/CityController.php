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
        // GET /cidade/{id}-{slug?}
    public function show($id, $slug = null, Request $request)
    {
        $city = City::where("city_id", $id)->firstOrFail();

        // filtros da querystring
        $catParam = $request->query("categoria");
        $catId = is_null($catParam) ? null : (int) $catParam;

        $term = trim((string) $request->query("q", ""));

        // base: por SID
        $qSid = Business::where("sid", $city->city_id);

        if (!is_null($catId) && $catId > 0) {
            $qSid->where("cid", $catId);
        }
        if ($term !== "") {
            $qSid->where("business_name", "LIKE", "%".$term."%");
        }

        $businesses = $qSid->orderBy("biz_id", "desc")
                           ->paginate(12)
                           ->withQueryString();

        // Fallback: se NÃO há filtros e a busca por SID veio vazia, tenta por nome da cidade
        $noFilters = (is_null($catId) || $catId <= 0) && ($term === "");
        if ($noFilters && $businesses->total() === 0) {
            $qCity = Business::whereRaw("LOWER(TRIM(city)) = LOWER(TRIM(?))", [$city->city]);
            $businesses = $qCity->orderBy("biz_id", "desc")
                                ->paginate(12)
                                ->withQueryString();
        }

        $categories = \App\Models\Category::orderBy("category")->get();

        return view("cities.show", [
            "city"        => $city,
            "businesses"  => $businesses,
            "categories"  => $categories,
            "filters"     => [
                "categoria" => $catId,
                "q"         => $term,
            ],
        ]);
    }// filtro por termo (nome do negÃ³cio)
        $term = trim((string) $request->query("q", ""));
        if ($term !== "") {
            $q->where("business_name", "LIKE", "%".$term."%");
        }

        // ordenaÃ§Ã£o + paginaÃ§Ã£o (preserva filtros na URL)
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