<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ContactController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| MantÃ©m o que jÃ¡ funcionava, apenas organizado e sem duplicaÃ§Ãµes.
*/

/* ======== HOME & DASHBOARD ======== */
Route::view('/', 'welcome')->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

/* ======== PÃšBLICO ======== */

// Categorias
Route::get('/categorias', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categoria/{id}-{slug?}', [CategoryController::class, 'show'])
    ->whereNumber('id')->name('categories.show');

// Cidades
Route::get('/cidades', [CityController::class, 'index'])->name('cities.index');
Route::get('/cidade/{id}-{slug?}', [CityController::class, 'show'])
    ->whereNumber('id')->name('cities.show');

// NegÃ³cio (detalhe)
Route::get('/negocio/{id}-{slug?}', [BusinessController::class, 'show'])
    ->whereNumber('id')->name('business.show');

// Busca
Route::get('/buscar', [SearchController::class, 'index'])->name('search.index');

// /negocio sem id -> redireciona para busca
Route::get('/negocio', fn () => redirect()->route('search.index'));

// Form de criaÃ§Ã£o (pÃºblico, sÃ³ exibe o form)
Route::get('/negocio/novo', [BusinessController::class, 'create'])->name('business.create');

/* ======== ÃREA AUTENTICADA (pÃºblico logado + perfil) ======== */
Route::middleware('auth')->group(function () {
    // Envio do formulÃ¡rio (mantÃ©m protegido)
    Route::post('/negocio', [BusinessController::class, 'store'])->name('business.store');

    // Perfil (Breeze)
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});

/* ======== ÃREA ADMIN (Middleware: is_admin) ======== */
Route::middleware(['auth','is_admin'])
    ->prefix('admin')->as('admin.')
    ->group(function () {
        // Dashboard do admin
        Route::get('/', [\App\Http\Controllers\Admin\AdminController::class, 'index'])
            ->name('dashboard');

        // DiagnÃ³stico
        Route::get('/ping', fn () => response('admin-ping-ok', 200))->name('ping');

        // Categorias
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryAdminController::class)
            ->except(['show']);

        // Cidades
        Route::resource('cities', \App\Http\Controllers\Admin\CityAdminController::class)
            ->except(['show']);

        // NegÃ³cios (admin)
        Route::resource('businesses', \App\Http\Controllers\Admin\BusinessAdminController::class)
            ->except(['show']);

        // Alias compat
        Route::get('/business', fn () => redirect()->route('admin.businesses.index'))
            ->name('business.index');
    });

/* ==================== BEGIN LEGACY_301_REDIRECTS ==================== */
// business-{id}-{slug}.html  â†’ /negocio/{id}-{slug}
Route::get('/business-{id}-{slug?}.html', function (int $id, ?string $slug = null) {
    $name    = DB::table('business')->where('biz_id', $id)->value('business_name');
    $newSlug = Str::slug($name ?? ($slug ?? ''));
    return redirect()->route('business.show', ['id' => $id, 'slug' => $newSlug], 301);
})->whereNumber('id');

// business-{id}-{slug} (sem .html) â†’ /negocio/{id}-{slug}
Route::get('/business-{id}-{slug?}', function (int $id, ?string $slug = null) {
    $name    = DB::table('business')->where('biz_id', $id)->value('business_name');
    $newSlug = Str::slug($name ?? ($slug ?? ''));
    return redirect()->route('business.show', ['id' => $id, 'slug' => $newSlug], 301);
})->whereNumber('id');

// business-{id} (sem slug) â†’ /negocio/{id}-{slug}
Route::get('/business-{id}', function (int $id) {
    $name    = DB::table('business')->where('biz_id', $id)->value('business_name');
    $newSlug = Str::slug($name ?? '');
    return redirect()->route('business.show', ['id' => $id, 'slug' => $newSlug], 301);
})->whereNumber('id');

// category-{id}-{slug} â†’ /categoria/{id}-{slug}
Route::get('/category-{id}-{slug?}', function (int $id, ?string $slug = null) {
    $newSlug = $slug ?? '';
    try {
        if (Schema::hasTable('category')) {
            $name = DB::table('category')->where('cat_id', $id)->value('category_name')
                 ?: DB::table('category')->where('cat_id', $id)->value('category');
            if ($name) {
                $newSlug = Str::slug($name);
            }
        }
    } catch (\Throwable $e) {
        // ignora e segue
    }
    return redirect()->route('categories.show', ['id' => $id, 'slug' => $newSlug], 301);
})->whereNumber('id');

// subcategory-{id}-{slug} â†’ /categoria/{id}-{slug}
Route::get('/subcategory-{id}-{slug?}', function (int $id, ?string $slug = null) {
    $newSlug = $slug ?? '';
    try {
        if (Schema::hasTable('category')) {
            $name = DB::table('category')->where('cat_id', $id)->value('category_name')
                 ?: DB::table('category')->where('cat_id', $id)->value('category');
            if ($name) {
                $newSlug = Str::slug($name);
            }
        }
    } catch (\Throwable $e) {
        // ignora e segue
    }
    return redirect()->route('categories.show', ['id' => $id, 'slug' => $newSlug], 301);
})->whereNumber('id');

// write_a_review-{id} â†’ Ã¢ncora #reviews na pÃ¡gina do negÃ³cio
Route::get('/write_a_review-{id}', function (int $id) {
    return redirect()->to(route('business.show', ['id' => $id]) . '#reviews', 301);
})->whereNumber('id');
/* ===================== END LEGACY_301_REDIRECTS ===================== */

/* ======== AUTH (Breeze) ======== */
require __DIR__.'/auth.php';

/* ======== PÃGINAS ESTÃTICAS & CONTATO ======== */
Route::get('/contato', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contato', [ContactController::class, 'submit'])->name('contact.send');

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

/* ======== DEBUG PAGES (temporÃ¡rio; manter ANTES do catch-all) ======== */
Route::get('/_debug/pages', function () {
    $all = DB::table('pages')
        ->select('id','slug','is_active','published_at')
        ->orderBy('id')
        ->get();
    return response()->json(['ok'=>true,'all'=>$all], 200, [], JSON_UNESCAPED_UNICODE);
});

Route::get('/_debug/page/{slug}', function (string $slug) {
    $pub = \App\Models\Page::where('slug', $slug)
        ->where('is_active', 1)
        ->where(function($q){
            $q->whereNull('published_at')->orWhere('published_at', '<=', now());
        })
        ->first();

    return response()->json([
        'slug' => $slug,
        'published_exists' => (bool)$pub,
        'published' => $pub ? [
            'id' => $pub->id,
            'slug' => $pub->slug,
            'is_active' => $pub->is_active,
            'published_at' => (string)$pub->published_at,
        ] : null,
    ], 200, [], JSON_UNESCAPED_UNICODE);
});

/*
 * Catch-all de pÃ¡ginas estÃ¡ticas â€” MANTER POR ÃšLTIMO (antes do fallback)!
 */
  
/* ======== REVIEWS (público autenticado) ======== */
Route::middleware('auth')->group(function () {
    Route::post('/negocio/{id}/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])
        ->whereNumber('id')
        ->name('reviews.store');
});


/* ==================== FALLBACK 404 (DEIXE POR ÃšLTIMO) ==================== */
/* ======== ADMIN REVIEWS (moderação) ======== */
Route::middleware(['auth','is_admin'])
    ->prefix('admin')->as('admin.')
    ->group(function () {
        // Listagem
        Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewAdminController::class, 'index'])
            ->name('reviews.index');

        // Aprovar / Ocultar / Excluir
        Route::post('/reviews/{review}/approve', [\App\Http\Controllers\Admin\ReviewAdminController::class, 'approve'])
            ->whereNumber('review')->name('reviews.approve');

        Route::post('/reviews/{review}/hide', [\App\Http\Controllers\Admin\ReviewAdminController::class,'hide'])
            ->whereNumber('review')->name('reviews.hide');

        Route::delete('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewAdminController::class, 'destroy'])
            ->whereNumber('review')->name('reviews.destroy');
    });
(function () {
    $latest = DB::table('business')
        ->select('biz_id', 'business_name')
        ->orderByDesc('biz_id')
        ->limit(6)
        ->get();

    return response()->view('errors.404', ['latest' => $latest], 404);
});
/* ===================== END FALLBACK ===================== */

Route::get('/{slug}', [\App\Http\Controllers\PageController::class, 'show'])
    ->where('slug', '^(?!api/|admin/|dashboard|login|register|password|negocio|business|categoria|category|cidade|city|contato|contact|sitemap\.xml|_debug/).+')
    ->name('pages.show');


