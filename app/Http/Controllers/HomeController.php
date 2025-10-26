<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // últimos negócios ativos (ajuste nomes de colunas/tabela se preciso)
        $items = DB::table('business')
            ->select('biz_id','business_name','city','description','slug')
            ->where('is_active', 1)
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('welcome', [
            'title' => 'Início',
            'items' => $items,
        ]);
    }
}
