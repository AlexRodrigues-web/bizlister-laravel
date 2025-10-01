<?php
namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Business;
use App\Models\Category;
use Illuminate\Http\Request;

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

        // filtros
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

        // fallback: se não houver filtros e por SID veio vazio, tenta pelo nome da cidade (legacy)
        $noFilters = (is_null($catId) || $catId <= 0) && ($term === "");
        if ($noFilters && $businesses->total() === 0) {
            $qCity = Business::whereRaw("LOWER(TRIM(city)) = LOWER(TRIM(?))", [$city->city]);
            $businesses = $qCity->orderBy("biz_id", "desc")
                                ->paginate(12)
                                ->withQueryString();
        }

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