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
        // mantém como já usado no projeto
        $cities = City::orderBy("city_id")->get();
        return view("cities.index", compact("cities"));
    }

    // GET /cidade/{id}-{slug?}
    public function show($id, $slug = null, Request $request)
    {
        $city = City::where("city_id", $id)->firstOrFail();

        // Filtros (compatíveis com Laravel 8)
        $catParam = $request->query("categoria");
        $catId    = is_null($catParam) ? null : (int) $catParam;
        $term     = trim((string) $request->query("q", ""));

        // Base: por SID
        $qSid = Business::where("sid", $city->city_id);

        if (!is_null($catId) && $catId > 0) {
            $qSid->where("cid", $catId);
        }
        if ($term !== "") {
            $qSid->where(function($w) use ($term) {
                $w->where("business_name", "LIKE", "%{$term}%")
                  ->orWhere("description", "LIKE", "%{$term}%");
            });
        }

        $businesses = $qSid->orderBy("business_name")
                           ->paginate(12)
                           ->withQueryString();

        // Fallback: por texto de cidade (quando sid ainda não está 100%)
        if ($businesses->total() === 0) {
            $qTxt = Business::whereRaw(
                "LOWER(TRIM(CONVERT(city USING utf8mb4))) = LOWER(TRIM(?))",
                [$city->city]
            );

            if (!is_null($catId) && $catId > 0) {
                $qTxt->where("cid", $catId);
            }
            if ($term !== "") {
                $qTxt->where(function($w) use ($term) {
                    $w->where("business_name", "LIKE", "%{$term}%")
                      ->orWhere("description", "LIKE", "%{$term}%");
                });
            }

            $businesses = $qTxt->orderBy("business_name")
                               ->paginate(12)
                               ->withQueryString();
        }

        // No teu projeto, o nome da coluna é "category"
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