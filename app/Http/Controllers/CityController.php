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
    // Paginação para permitir ->links() e ->withQueryString() na view
    $cities = City::orderBy("city_id")->paginate(24)->withQueryString();
    return view("cities.index", compact("cities"));
}

    // GET /cidade/{id}-{slug?}
    public function show($id, $slug = null, Request $request)
    {
        $city = City::where("city_id", $id)->firstOrFail();

        // Filtros compatÃ­veis com Laravel 8
        $catParam = $request->query("categoria");
        $catId    = is_null($catParam) ? null : (int) $catParam;
        $q        = trim((string) $request->query("q", ""));

        // Base: por SID
        $query = Business::where("sid", $city->city_id);

        if (!is_null($catId) && $catId > 0) {
            $query->where("cid", $catId);
        }
        if ($q !== "") {
            $query->where(function($w) use ($q) {
                $w->where("business_name", "LIKE", "%{$q}%")
                  ->orWhere("description", "LIKE", "%{$q}%");
            });
        }

        $businesses = $query->orderBy("business_name")
                            ->paginate(12)
                            ->withQueryString();

        // Fallback: por texto de cidade se ainda nÃ£o houver resultados (quando sid nÃ£o estÃ¡ 100%)
        if ($businesses->total() === 0) {
            $qTxt = Business::whereRaw(
                "LOWER(TRIM(CONVERT(city USING utf8mb4))) = LOWER(TRIM(?))",
                [$city->city]
            );

            if (!is_null($catId) && $catId > 0) {
                $qTxt->where("cid", $catId);
            }
            if ($q !== "") {
                $qTxt->where(function($w) use ($q) {
                    $w->where("business_name", "LIKE", "%{$q}%")
                      ->orWhere("description", "LIKE", "%{$q}%");
                });
            }

            $businesses = $qTxt->orderBy("business_name")
                               ->paginate(12)
                               ->withQueryString();
        }

        // No teu blade atual, as opÃ§Ãµes usam cat_name
        $categories = Category::orderBy("cat_name")->get(["cat_id","cat_name"]);

        $pageTitle = "NegÃ³cios em {$city->city}";

        return view("cities.show", compact(
            "city", "businesses", "categories", "q", "catId", "pageTitle"
        ));
    }
}