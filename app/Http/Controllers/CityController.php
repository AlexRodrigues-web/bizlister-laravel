<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Business;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CityController extends Controller
{
    /**
     * GET /cidades
     */
    public function index()
    {
        // Usa paginação se disponível; caso algo dê erro no paginate, faz fallback para get()
        try {
            $cities = City::orderBy('city_id')
                ->paginate(24)
                ->withQueryString();
        } catch (\Throwable $e) {
            $cities = City::orderBy('city_id')->get();
        }

        return view('cities.index', compact('cities'));
    }

    /**
     * GET /cidade/{id}-{slug?}
     */
    public function show($id, $slug = null, Request $request)
    {
        $city = City::where('city_id', $id)->firstOrFail();

        // Slug canônico para evitar conteúdo “cru” por não herdar layout após redirecionamentos estranhos
        $expectedSlug = Str::slug((string)($city->city ?? "cidade-{$city->city_id}"));
        if ($expectedSlug === '') {
            $expectedSlug = (string) $city->city_id;
        }
        if ($slug !== $expectedSlug) {
            return redirect()
                ->route('cities.show', ['id' => $city->city_id, 'slug' => $expectedSlug])
                ->setStatusCode(301);
        }

        // Filtros
        $catParam = $request->query('categoria');
        $catId    = is_null($catParam) ? null : (int) $catParam;
        $q        = trim((string) $request->query('q', ''));

        // Base: por SID (schema legado)
        $query = Business::where('sid', $city->city_id);

        if (!is_null($catId) && $catId > 0) {
            $query->where('cid', $catId);
        }
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('business_name', 'LIKE', "%{$q}%")
                  ->orWhere('description', 'LIKE', "%{$q}%");
            });
        }

        try {
            $businesses = $query->orderBy('business_name')
                ->paginate(12)
                ->withQueryString();
        } catch (\Throwable $e) {
            // Fallback extremo (sem paginação) se necessário
            $businesses = $query->orderBy('business_name')->get();
        }

        // Fallback: por nome da cidade (sem CONVERT/charset específico de MySQL)
        // Útil quando 'sid' não está preenchido para todos os registros
        if (method_exists($businesses, 'total') ? $businesses->total() === 0 : $businesses->count() === 0) {
            $qTxt = Business::whereRaw('LOWER(TRIM(city)) = LOWER(TRIM(?))', [(string) $city->city]);

            if (!is_null($catId) && $catId > 0) {
                $qTxt->where('cid', $catId);
            }
            if ($q !== '') {
                $qTxt->where(function ($w) use ($q) {
                    $w->where('business_name', 'LIKE', "%{$q}%")
                      ->orWhere('description', 'LIKE', "%{$q}%");
                });
            }

            try {
                $businesses = $qTxt->orderBy('business_name')
                    ->paginate(12)
                    ->withQueryString();
            } catch (\Throwable $e) {
                $businesses = $qTxt->orderBy('business_name')->get();
            }
        }

        // A view aceita várias chaves (category/name/label...), então basta trazer tudo
        $categories = Category::orderBy('category')->get();

        $pageTitle = "Negócios em " . ((string)($city->city ?? "Cidade #{$city->city_id}"));

        return view('cities.show', compact(
            'city',
            'businesses',
            'categories',
            'q',
            'catId',
            'pageTitle'
        ));
    }
}
