<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BookmarksController extends Controller
{
    /**
     * GET /bookmarks
     * Lista os bookmarks do usuário autenticado (robusto p/ esquemas legados)
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Tabela de favoritos: bookmarks | bookmark
        $bmtTable = Schema::hasTable('bookmarks') ? 'bookmarks'
                  : (Schema::hasTable('bookmark')  ? 'bookmark'  : null);

        if (!$bmtTable) {
            // Sem tabela: devolve vazio sem quebrar
            return view('bookmarks.index', ['bookmarks' => collect()]);
        }

        $cols = Schema::getColumnListing($bmtTable);

        // Coluna PK (para show/destroy e paginação amigável)
        $pkCol = collect(['id','bookmark_id','bm_id'])->first(fn($c) => in_array($c, $cols, true)) ?? 'id';

        // Coluna do negócio no bookmark
        $bizFkCol = collect(['bizid','biz_id','business_id'])
            ->first(fn($c) => in_array($c, $cols, true));

        // Coluna de ordenação
        $orderCol = collect(['created_at','updated_at','id','bookmark_id','bm_id'])
            ->first(fn($c) => in_array($c, $cols, true));

        // Se não houver FK para negócio, lista só o bookmark cru
        if (!$bizFkCol) {
            $q = DB::table($bmtTable)->where('user_id', $userId);
            if ($orderCol) $q->orderByDesc($orderCol);

            $bookmarks = $q->paginate(12)->withQueryString();

            return view('bookmarks.index', compact('bookmarks'));
        }

        // Garantir tabela de negócios
        if (!Schema::hasTable('business')) {
            // Sem tabela de negócios: volta sem JOIN
            $q = DB::table($bmtTable)->where('user_id', $userId);
            if ($orderCol) $q->orderByDesc($orderCol);

            $bookmarks = $q->paginate(12)->withQueryString();
            return view('bookmarks.index', compact('bookmarks'));
        }

        // Monta a query com JOIN em business (PK biz_id)
        $q = DB::table($bmtTable.' as bm')
            ->where('bm.user_id', $userId)
            ->leftJoin('business as b', "b.$bizFkCol" === 'b.biz_id' ? 'bm.'.$bizFkCol : 'bm.'.$bizFkCol, '=', 'b.biz_id')
            // Seleciona colunas do bookmark + dados essenciais do negócio
            ->select([
                "bm.$pkCol as bookmark_pk",
                "bm.$bizFkCol as bm_biz_id",
                'bm.user_id',
                DB::raw("COALESCE(b.business_name, 'Negócio') as business_name"),
                'b.biz_id',
                'b.image',
                'b.image_lg',
                DB::raw("COALESCE(b.city, '') as city"),
            ]);

        if ($orderCol) $q->orderByDesc("bm.$orderCol");

        // Paginado
        $bookmarks = $q->paginate(12)->withQueryString();

        return view('bookmarks.index', compact('bookmarks'));
    }

    /**
     * POST /bookmarks
     * Cria (ou garante) um bookmark para o negócio informado (biz_id)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'biz_id' => ['required', 'integer', 'exists:business,biz_id'],
        ]);

        $userId = Auth::id();

        // Tabela de favoritos
        $bmtTable = Schema::hasTable('bookmarks') ? 'bookmarks'
                  : (Schema::hasTable('bookmark')  ? 'bookmark'  : null);

        if (!$bmtTable) {
            return back()->with('error', 'Tabela de favoritos não encontrada.');
        }

        $cols = Schema::getColumnListing($bmtTable);
        $bizFkCol = collect(['bizid','biz_id','business_id'])
            ->first(fn($c) => in_array($c, $cols, true));

        if (!$bizFkCol) {
            return back()->with('error', 'Coluna de vínculo com o negócio não encontrada no bookmark.');
        }

        // Evita duplicados (user_id + biz_fk)
        $exists = DB::table($bmtTable)
            ->where('user_id', $userId)
            ->where($bizFkCol, $data['biz_id'])
            ->exists();

        if (!$exists) {
            DB::table($bmtTable)->insert([
                'user_id' => $userId,
                $bizFkCol => $data['biz_id'],
                // timestamps, se existirem
                ...(in_array('created_at', $cols, true) ? ['created_at' => now()] : []),
                ...(in_array('updated_at', $cols, true) ? ['updated_at' => now()] : []),
            ]);
        }

        return back()->with('success', 'Negócio adicionado aos favoritos.');
    }

    /**
     * GET /bookmarks/{bookmark}
     * Mostra um bookmark + dados do negócio
     */
    public function show($id)
    {
        $userId = Auth::id();

        $bmtTable = Schema::hasTable('bookmarks') ? 'bookmarks'
                  : (Schema::hasTable('bookmark')  ? 'bookmark'  : null);

        if (!$bmtTable) abort(404);

        $cols = Schema::getColumnListing($bmtTable);

        $pkCol = collect(['id','bookmark_id','bm_id'])->first(fn($c) => in_array($c, $cols, true)) ?? 'id';
        $bizFkCol = collect(['bizid','biz_id','business_id'])
            ->first(fn($c) => in_array($c, $cols, true));

        $row = DB::table($bmtTable.' as bm')
            ->where("bm.$pkCol", $id)
            ->where('bm.user_id', $userId)
            ->when($bizFkCol && Schema::hasTable('business'), function ($q) use ($bizFkCol) {
                $q->leftJoin('business as b', 'bm.'.$bizFkCol, '=', 'b.biz_id')
                  ->addSelect('b.*');
            })
            ->addSelect("bm.$pkCol as bookmark_pk", 'bm.user_id', $bizFkCol ? "bm.$bizFkCol as bm_biz_id" : DB::raw('NULL as bm_biz_id'))
            ->first();

        abort_if(!$row, 404);

        return view('bookmarks.show', ['bookmark' => $row]);
    }

    /**
     * DELETE /bookmarks/{bookmark}
     * Remove o bookmark do usuário (independente do nome do PK)
     */
    public function destroy($id)
    {
        $userId = Auth::id();

        $bmtTable = Schema::hasTable('bookmarks') ? 'bookmarks'
                  : (Schema::hasTable('bookmark')  ? 'bookmark'  : null);

        if (!$bmtTable) return back()->with('error', 'Tabela de favoritos não encontrada.');

        $cols = Schema::getColumnListing($bmtTable);
        $pkCol = collect(['id','bookmark_id','bm_id'])->first(fn($c) => in_array($c, $cols, true)) ?? 'id';

        $deleted = DB::table($bmtTable)->where($pkCol, $id)->where('user_id', $userId)->delete();

        return back()->with($deleted ? 'success' : 'error', $deleted ? 'Removido dos favoritos.' : 'Favorito não encontrado.');
    }

    // Métodos não usados pelo resource atual
    public function create() { abort(404); }
    public function edit($id) { abort(404); }
    public function update(Request $r, $id) { abort(404); }
}
