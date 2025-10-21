<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReviewAdminController extends Controller
{
    /**
     * Lista de reviews para moderação.
     * Mantida exatamente como estava, usando o Model.
     */
    public function index()
    {
        $pending  = Review::with('business','user')->where('is_approved', false)->latest()->paginate(20);
        $approved = Review::with('business','user')->where('is_approved', true)->latest()->paginate(20);

        return view('admin.reviews.index', compact('pending','approved'));
    }

    /**
     * Aprova um review (compatível com PK legada).
     */
    public function approve(int $review)
    {
        $pk = $this->reviewsPk();
        $row = DB::table('reviews')->where($pk, $review)->first();

        if (!$row) {
            return back()->with('status', 'Review não encontrado.');
        }

        $payload = ['is_approved' => 1];
        if ($this->hasCol('approved_at')) {
            $payload['approved_at'] = now();
        }

        DB::table('reviews')->where($pk, $review)->update($payload);

        return back()->with('status', 'Review aprovado.');
    }

    /**
     * Oculta (desaprova) um review (compatível com PK legada).
     */
    public function hide(int $review)
    {
        $pk = $this->reviewsPk();
        $row = DB::table('reviews')->where($pk, $review)->first();

        if (!$row) {
            return back()->with('status', 'Review não encontrado.');
        }

        $payload = ['is_approved' => 0];
        if ($this->hasCol('approved_at')) {
            $payload['approved_at'] = null;
        }

        DB::table('reviews')->where($pk, $review)->update($payload);

        return back()->with('status', 'Review ocultado.');
    }

    /**
     * Remove um review (compatível com PK legada).
     */
    public function destroy(int $review)
    {
        $pk = $this->reviewsPk();
        $deleted = DB::table('reviews')->where($pk, $review)->delete();

        if (!$deleted) {
            return back()->with('status', 'Review não encontrado.');
        }

        return back()->with('status', 'Review removido.');
    }

    /* =========================
       Helpers de compatibilidade
       ========================= */

    /**
     * Descobre a PK usada na tabela reviews em bancos legados.
     * Tenta, em ordem: id, review_id, rev_id, rid.
     */
    protected function reviewsPk(): string
    {
        static $cached;
        if ($cached) return $cached;

        $candidates = ['id', 'review_id', 'rev_id', 'rid'];
        foreach ($candidates as $c) {
            if ($this->hasCol($c)) {
                return $cached = $c;
            }
        }

        // fallback: se nenhuma dessas existir, usa a primeira coluna existente
        $cols = Schema::hasTable('reviews') ? Schema::getColumnListing('reviews') : [];
        return $cached = ($cols[0] ?? 'id');
    }

    /**
     * Verifica existência de coluna na tabela reviews.
     */
    protected function hasCol(string $col): bool
    {
        try {
            return Schema::hasColumn('reviews', $col);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
