<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessBookmarkController extends Controller
{
    public function store(Request $request, int $biz)
    {
        // user_id 0 = guest; se houver auth, use auth()->id()
        $userId = auth()->check() ? auth()->id() : 0;

        $exists = DB::table('bookmarks')->where(['user_id'=>$userId, 'bizid'=>$biz])->first();
        if (!$exists) {
            DB::table('bookmarks')->insert(['user_id'=>$userId, 'bizid'=>$biz]);
            // opcional: incrementar contador em business.bookmarks
            try {
                DB::table('business')->where('biz_id', $biz)->increment('bookmarks');
            } catch (\Throwable $e) {}
            return back()->with('success', 'Bookmark adicionado.');
        }
        return back()->with('info', 'Já estava nos favoritos.');
    }
}