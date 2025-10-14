<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityAdminController extends Controller
{
    // GET /admin/cities
    public function index()
    {
        // Tabela legada: city (city_id, city)
        $items = DB::table("city")
            ->select("city_id", "city")
            ->orderBy("city_id")
            ->paginate(15);

        $pk = "city_id";

        return view("admin.cities.index", compact("items", "pk"));
    }

    // GET /admin/cities/create
    public function create()
    {
        return view("admin.cities.create");
    }

    // POST /admin/cities
    public function store(Request $request)
    {
        $data = $request->validate([
            "city" => ["required","string","max:190"],
        ]);

        DB::table("city")->insert(["city" => $data["city"]]);

        return redirect()->route("admin.cities.index")
                         ->with("status","Cidade criada com sucesso.");
    }

    // GET /admin/cities/{id}/edit
    public function edit($id)
    {
        $city = DB::table("city")->where("city_id", $id)->first();
        abort_unless($city, 404);
        return view("admin.cities.edit", compact("city"));
    }

    // PUT /admin/cities/{id}
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            "city" => ["required","string","max:190"],
        ]);

        DB::table("city")->where("city_id", $id)->update(["city" => $data["city"]]);

        return redirect()->route("admin.cities.index")
                         ->with("status","Cidade atualizada com sucesso.");
    }

    // DELETE /admin/cities/{id}
    public function destroy($id)
    {
        DB::table("city")->where("city_id", $id)->delete();

        return redirect()->route("admin.cities.index")
                         ->with("status","Cidade excluída com sucesso.");
    }
}