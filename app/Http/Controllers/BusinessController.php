<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Category;
use App\Models\City;
use Illuminate\Support\Str;

class BusinessController extends Controller
{
    // GET /negocio/{id}-{slug?}
    public function show($id, $slug = null)
    {
        $biz = Business::where("biz_id", $id)->firstOrFail();

        // Relacionamentos simples
        $category = Category::where("cat_id", $biz->cid)->first();
        $city     = City::where("city_id", $biz->sid)->first();

        return view("businesses.show", compact("biz", "category", "city"));
    }
}
