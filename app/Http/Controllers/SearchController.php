<?php

namespace App\Http\Controllers;
use App\Models\City;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q      = trim((string)$request->input("q", ""));
        $cat    = (int)$request->input("categoria", 0);
        $sid    = (int)$request->input("cidade", 0);

        // combos
        $categories = DB::table("category")->selectRaw("cat_id, category AS label")->orderBy("label")->get();

        $cities = City::orderBy("city")->get(["city_id","city"]);

        // query principal
        $biz = DB::table("business")
            ->select("biz_id","business_name","description","city","cid","sid");

        if ($q !== "") {
            $like = "%".$q."%";
            $biz->where(function($w) use ($like){
                $w->where("business_name","LIKE",$like)
                  ->orWhere("description","LIKE",$like);
            });
        }
        if ($cat > 0) $biz->where("cid",$cat);
        if ($sid > 0) $biz->where("sid",$sid);

        $results = $biz->orderBy("business_name")->get();

        return view("search.index", compact("q","cat","sid","categories","cities","results"));
    }
}