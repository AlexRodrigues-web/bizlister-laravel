<?php
namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Business;
use Illuminate\Support\Str;

class CityController extends Controller
{
    // GET /cidades
    public function index()
    {
        $cities = City::orderBy('city_id')->get();
        return view('cities.index', compact('cities'));
    }

    // GET /cidade/{id}-{slug?}
    public function show($id, $slug = null)
    {
        $city = City::where('city_id', $id)->firstOrFail();

        // No legado, business.sid referencia a cidade
        $businesses = Business::where('sid', $city->city_id)
            ->orderBy('biz_id', 'desc')
            ->paginate(20);

        return view('cities.show', compact('city', 'businesses'));
    }
}
