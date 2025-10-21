<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReviewController extends Controller
{
    public function store(Request $request, int $bizId)
    {
        // 1) Validação
        $validated = $request->validate([
            'rating' => ['required','integer','min:1','max:5'],
            'title'  => ['nullable','string','max:120'],
            'body'   => ['required','string'],
        ]);

        $userId = Auth::id();

        // 2) Base comum (sem NULL em textos)
        $data = [
            'rating'     => (int) $validated['rating'],
            'title'      => $validated['title'] ?? '',
            'body'       => $validated['body'] ?? '',
            'biz_id'     => $bizId,
            'user_id'    => $userId ?: null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // 3) LEGADO: 'review' (espelha body; nunca NULL)
        if (Schema::hasColumn('reviews', 'review')) {
            $data['review'] = $data['body'] !== '' ? $data['body'] : ($data['title'] ?? '');
            if ($data['review'] === null) $data['review'] = '';
        }

        // 4) LEGADO: 'u_id' (usuário legado)
        if (Schema::hasColumn('reviews', 'u_id')) {
            $data['u_id'] = $userId ?: 0;
        }

        // 5) LEGADO: 'rew_date' (DATE NOT NULL)
        if (Schema::hasColumn('reviews', 'rew_date')) {
            $data['rew_date'] = now()->toDateString(); // YYYY-MM-DD
        }

        // 6) LEGADO: 'avg' (NOT NULL) — use a nota como média inicial
        if (Schema::hasColumn('reviews', 'avg')) {
            $data['avg'] = (float) $data['rating'];
        }

        // 7) LEGADO: 'uniq' (NOT NULL) — token único
        if (Schema::hasColumn('reviews', 'uniq')) {
            $data['uniq'] = bin2hex(random_bytes(8)) . '-' .
                            ($userId ?? 'guest') . '-' .
                            $bizId . '-' .
                            now()->format('YmdHis');
        }

        // 7.1) LEGADO: 'b_id' (alias de biz_id)
        if (Schema::hasColumn('reviews', 'b_id')) {
            $data['b_id'] = $bizId;
        }

        // 7.2) LEGADO: 'rev_active' (flag NOT NULL) — define como ativo por padrão
        if (Schema::hasColumn('reviews', 'rev_active')) {
            $data['rev_active'] = 1;
        }

        // 8) Aprovação padrão (se existir)
        if (Schema::hasColumn('reviews', 'is_approved') && !array_key_exists('is_approved', $data)) {
            $data['is_approved'] = 0;
        }

        // 9) Campos textuais comuns no legado (evita NOT NULL sem default)
        foreach (['name','email','ip','ip_address','agent','user_agent','source','status'] as $txt) {
            if (Schema::hasColumn('reviews', $txt) && !array_key_exists($txt, $data)) {
                if (in_array($txt, ['ip','ip_address'])) {
                    $data[$txt] = (string) $request->ip();
                } elseif (in_array($txt, ['agent','user_agent'])) {
                    $data[$txt] = (string) ($request->userAgent() ?? '');
                } else {
                    $data[$txt] = '';
                }
            }
        }

        // 10) approved_at nulo quando não aprovado
        if (Schema::hasColumn('reviews', 'approved_at')
            && isset($data['is_approved']) && (int)$data['is_approved'] === 0) {
            $data['approved_at'] = null;
        }

        DB::table('reviews')->insert($data);

        return back()->with('success', 'Avaliação enviada! Ela será exibida após aprovação.');
    }
}
