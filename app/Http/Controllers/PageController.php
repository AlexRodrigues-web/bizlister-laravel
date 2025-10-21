<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::published()
            ->where('slug', $slug)
            ->first();

        if (!$page) {
            throw new NotFoundHttpException();
        }

        return view('pages.show', compact('page'));
    }
}