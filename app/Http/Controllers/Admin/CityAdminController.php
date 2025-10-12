<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Models\City;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CityAdminController extends Controller
{
    public function index()
    {
        $items = City::orderBy("city")->paginate(15);
        return view("admin.cities.index", compact("items"));
    }

    public function create()
    {
        return view("admin.cities.create");
    }

    public function store(StoreCityRequest $request): RedirectResponse
    {
        City::create($request->validated());
        return redirect()->route("admin.cities.index")->with("success","Cidade criada.");
    }

    public function edit($city)
    {
        $item = City::where("city_id",$city)->firstOrFail();
        return view("admin.cities.edit", compact("item"));
    }

    public function update(UpdateCityRequest $request, $city): RedirectResponse
    {
        $item = City::where("city_id",$city)->firstOrFail();
        $item->update($request->validated());
        return redirect()->route("admin.cities.index")->with("success","Cidade atualizada.");
    }

    public function destroy($city): RedirectResponse
    {
        $item = City::where("city_id",$city)->firstOrFail();
        $item->delete();
        return back()->with("success","Cidade removida.");
    }
}
