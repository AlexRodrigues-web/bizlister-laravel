<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Define o <title> da página (fallback para o slug “humanizado”)
        view()->share('title', $page->title ?: ucfirst(str_replace('-', ' ', $slug)));

        return view('pages.show', compact('page'));
    }
}
