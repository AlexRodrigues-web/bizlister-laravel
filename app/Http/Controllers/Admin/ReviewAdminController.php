<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

class ReviewAdminController extends Controller
{
    public function index()
    {
        $pending  = Review::with('business','user')->where('is_approved', false)->latest()->paginate(20);
        $approved = Review::with('business','user')->where('is_approved', true)->latest()->paginate(20);
        return view('admin.reviews.index', compact('pending','approved'));
    }

    public function approve(Review $review)
    {
        $review->is_approved = true;
        $review->save();

        return back()->with('status', 'Review aprovado.');
    }

    public function hide(Review $review)
    {
        $review->is_approved = false;
        $review->save();

        return back()->with('status', 'Review ocultado.');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('status', 'Review removido.');
    }
}