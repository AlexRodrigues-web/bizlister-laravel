<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, int $id)
    {
        $biz = Business::where('biz_id', $id)->firstOrFail();

        $data = $request->validate([
            'rating' => ['required','integer','between:1,5'],
            'title'  => ['nullable','string','max:150'],
            'body'   => ['nullable','string','max:5000'],
        ]);

        $review = new Review($data);
        $review->biz_id      = $biz->biz_id;
        $review->user_id     = auth()->id();
        $review->is_approved = false; // moderação
        $review->save();

        return redirect()
            ->route('business.show', ['id' => $biz->biz_id, 'slug' => request('slug')])
            ->with('status', 'Avaliação enviada e aguardando aprovação.');
    }
}