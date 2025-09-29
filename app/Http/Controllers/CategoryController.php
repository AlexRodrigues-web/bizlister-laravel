<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Business;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // GET /categorias
    public function index()
    {
        // ordenar pela PK real
        $categories = Category::orderBy("cat_id")->get();
        return view("categories.index", compact("categories"));
    }

    // GET /categoria/{id}-{slug?}
    public function show($id)
    {
        // busca pela PK real
        $category = Category::where("cat_id", $id)->firstOrFail();

        // lista negÃ³cios pela FK cid
        $businesses = Business::where("cid", $category->cat_id)
            ->orderBy("biz_id")
            ->paginate(20);

        return view("categories.show", compact("category", "businesses"));
    }
}
