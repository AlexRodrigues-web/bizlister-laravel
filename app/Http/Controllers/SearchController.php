<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Inputs padronizados (mantém compat com 'categoria' e 'cidade')
        $q   = trim((string) $request->query('q', ''));
        $cid = $request->query('cid', $request->query('cat', $request->query('categoria'))); // cat_id
        $sid = $request->query('sid', $request->query('cidade'));                             // city_id

        // compat para a view (usa $cat)
        $cat = $cid;

        // Combos (mantém 'label' para a view atual)
        $categories = DB::table('category')
            ->select('cat_id', 'category', DB::raw('category as label'))
            ->orderBy('category')
            ->get();

        $cities = DB::table('city')
            ->select('city_id', 'city', DB::raw('city as label'))
            ->orderBy('city')
            ->get();

        // Base da consulta
        $qb = DB::table('business as b')
            ->leftJoin('category as c', 'c.cat_id', '=', 'b.cid')
            ->leftJoin('city as ci', 'ci.city_id', '=', 'b.sid')
            ->select([
                'b.biz_id',
                'b.business_name',
                'b.description',
                'b.city',
                'b.cid',
                'b.sid',
                'b.image', 'b.image_lg', 'b.image_sm',
                'c.category as category_name',
                'ci.city as city_name',
            ]);

        // Termo + score simples (prioriza nome)
        if ($q !== '') {
            $q_any   = "%{$q}%";
            $q_word  = "% {$q} %";
            $q_start = "{$q}%";

            $qb->where(function ($w) use ($q_any) {
                $w->where('b.business_name', 'like', $q_any)
                  ->orWhere('b.description',  'like', $q_any);
            });

            $qb->addSelect(DB::raw(
                "(CASE
                    WHEN b.business_name LIKE ? THEN 3
                    WHEN CONCAT(' ', b.business_name, ' ') LIKE ? THEN 2
                    WHEN b.business_name LIKE ? THEN 1
                    WHEN b.description   LIKE ? THEN 1
                    ELSE 0
                  END) AS rel_score"
            ))->addBinding([$q_start, $q_word, $q_any, $q_any], 'select');
        } else {
            $qb->addSelect(DB::raw('0 AS rel_score'));
        }

        // Filtros combinados
        if ($cid !== null && $cid !== '') {
            $qb->where('b.cid', $cid);
        }
        if ($sid !== null && $sid !== '') {
            $qb->where('b.sid', $sid);
        }

        // Ordenação: previsível por nome
        if ($q !== '') {
            $qb->orderByDesc('rel_score')
               ->orderBy('b.business_name'); // ASC (padrão)
        } else {
            $qb->orderBy('b.business_name'); // só por nome
        }

        // IMPORTANTE: manter filtros nos links de paginação
        $results = $qb->paginate(12)->withQueryString();

        return view('search.index', compact('q', 'cat', 'sid', 'categories', 'cities', 'results'));
    }
}
