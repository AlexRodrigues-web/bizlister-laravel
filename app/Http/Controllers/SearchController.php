<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q    = trim((string)$request->get('q',''));
        $city = trim((string)$request->get('city',''));

        $sql = DB::table('business')->where('active', 1);

        if ($q !== '') {
            $like = "%$q%";
            $sql->where(function($w) use ($like) {
                $w->where('business_name', 'like', $like)
                  ->orWhere('description',   'like', $like)
                  ->orWhere('tags',          'like', $like);
            });
        }

        if ($city !== '') {
            // city no legado é texto livre; amarramos por slug da tabela city
            $c = DB::table('city')->where('slug', $city)->first();
            if ($c) {
                $sql->where('city', 'like', $c->city); // aproximado ao legado
            }
        }

        $items = $sql->orderByDesc('biz_id')->paginate(10)->appends($request->query());

        return view('search.index', [
            'items' => $items,
            'q'     => $q,
            'city'  => $city,
        ]);
    }
}