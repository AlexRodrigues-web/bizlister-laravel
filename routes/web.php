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
use App\Http\Controllers\BookmarksController;
use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\HoursController;
use App\Http\Controllers\BusinessBookmarkController;
use App\Http\Controllers\Auth\SocialAuthController; // OAuth Google/Facebook

// ---------------------------------------------------------
// Redirects simples
// ---------------------------------------------------------
Route::redirect('/sobre-nos', '/sobre', 301);
Route::redirect('/meu-perfil', '/perfil', 301); // atalho

// ---------------------------------------------------------
// BUSCA (antes do catch-all)
// ---------------------------------------------------------
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/buscar', [SearchController::class, 'index'])->name('search.alias');

// ---------------------------------------------------------
// HOME & PERFIL/DASHBOARD
// ---------------------------------------------------------
Route::view('/', 'welcome')->name('welcome');

// Perfil precisa existir e exigir auth
Route::view('/perfil', 'profile.show')->middleware('auth')->name('profile.show');

// Dashboard redireciona para o perfil
Route::get('/dashboard', function () {
    return redirect()->route('profile.show');
})->middleware(['auth'])->name('dashboard');

// ---------------------------------------------------------
// LOGIN SOCIAL (Google / Facebook)
// ---------------------------------------------------------
Route::prefix('auth')->name('social.')->group(function () {
    Route::get('/{provider}/redirect', [SocialAuthController::class, 'redirect'])
        ->where('provider', '^(google|facebook)$')
        ->name('redirect');

    Route::get('/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->where('provider', '^(google|facebook)$')
        ->name('callback');
});

// ---------------------------------------------------------
// PÚBLICO
// ---------------------------------------------------------
// Categorias
Route::get('/categorias', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categoria/{id}-{slug?}', [CategoryController::class, 'show'])
    ->whereNumber('id')->name('categories.show');

// Cidades
Route::get('/cidades', [CityController::class, 'index'])->name('cities.index');
Route::get('/cidade/{id}-{slug?}', [CityController::class, 'show'])
    ->whereNumber('id')->name('cities.show');

// Negócio (detalhe)
Route::get('/negocio/{id}-{slug?}', [BusinessController::class, 'show'])
    ->whereNumber('id')->name('business.show');

// /negocio sem id -> busca
Route::get('/negocio', fn () => redirect()->route('search.index'));

// Form de criação (público)
Route::get('/negocio/novo', [BusinessController::class, 'create'])->name('business.create');

// Páginas estáticas & contato
Route::get('/contato', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contato', [ContactController::class, 'submit'])->name('contact.send');

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// ---------------------------------------------------------
// ÁREA AUTENTICADA
// ---------------------------------------------------------
Route::middleware('auth')->group(function () {
    // Envio do formulário de negócio
    Route::post('/negocio', [BusinessController::class, 'store'])->name('business.store');

    // Perfil (Breeze)
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');

    // Reviews
    Route::post('/negocio/{id}/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])
        ->whereNumber('id')->name('reviews.store');

    // Bookmarks & Hours
    Route::resource('bookmarks', BookmarksController::class)->only(['index','store','destroy','show']);
    Route::resource('hours', HoursController::class)->only(['show','update']);

    // Ação avulsa (bookmark)
    Route::post('/negocio/{biz}/bookmark', [BusinessBookmarkController::class, 'store'])
        ->whereNumber('biz')->name('business.bookmark');
});

// ---------------------------------------------------------
// ÁREA ADMIN
// ---------------------------------------------------------
Route::middleware(['auth','is_admin'])
    ->prefix('admin')->as('admin.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminController::class, 'index'])
            ->name('dashboard');

        Route::get('/ping', fn () => response('admin-ping-ok', 200))->name('ping');

        Route::resource('categories', \App\Http\Controllers\Admin\CategoryAdminController::class)->except(['show']);
        Route::resource('cities', \App\Http\Controllers\Admin\CityAdminController::class)->except(['show']);
        Route::resource('businesses', \App\Http\Controllers\Admin\BusinessAdminController::class)->except(['show']);
        Route::resource('advertisements', AdvertisementController::class);

        Route::get('/business', fn () => redirect()->route('admin.businesses.index'))->name('business.index');

        Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewAdminController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{review}/approve', [\App\Http\Controllers\Admin\ReviewAdminController::class, 'approve'])
            ->whereNumber('review')->name('reviews.approve');
        Route::post('/reviews/{review}/hide', [\App\Http\Controllers\Admin\ReviewAdminController::class,'hide'])
            ->whereNumber('review')->name('reviews.hide');
        Route::delete('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewAdminController::class, 'destroy'])
            ->whereNumber('review')->name('reviews.destroy');
    });

// ---------------------------------------------------------
// LEGACY 301
// ---------------------------------------------------------
Route::get('/business-{id}-{slug?}.html', function (int $id, ?string $slug = null) {
    $name    = DB::table('business')->where('biz_id', $id)->value('business_name');
    $newSlug = Str::slug($name ?? ($slug ?? ''));
    return redirect()->route('business.show', ['id' => $id, 'slug' => $newSlug], 301);
})->whereNumber('id');

Route::get('/business-{id}-{slug?}', function (int $id, ?string $slug = null) {
    $name    = DB::table('business')->where('biz_id', $id)->value('business_name');
    $newSlug = Str::slug($name ?? ($slug ?? ''));
    return redirect()->route('business.show', ['id' => $id, 'slug' => $newSlug], 301);
})->whereNumber('id');

Route::get('/business-{id}', function (int $id) {
    $name    = DB::table('business')->where('biz_id', $id)->value('business_name');
    $newSlug = Str::slug($name ?? '');
    return redirect()->route('business.show', ['id' => $id, 'slug' => $newSlug], 301);
})->whereNumber('id');

Route::get('/category-{id}-{slug?}', function (int $id, ?string $slug = null) {
    $newSlug = $slug ?? '';
    try {
        $table = Schema::hasTable('category') ? 'category' : (Schema::hasTable('categories') ? 'categories' : null);
        if ($table) {
            $name = DB::table($table)->where('cat_id', $id)->value('category')
                 ?: DB::table($table)->where('cat_id', $id)->value('category_name')
                 ?: DB::table($table)->where('cat_id', $id)->value('name')
                 ?: DB::table($table)->where('cat_id', $id)->value('title');
            if ($name) $newSlug = Str::slug($name);
        }
    } catch (\Throwable $e) {}
    return redirect()->route('categories.show', ['id' => $id, 'slug' => $newSlug], 301);
})->whereNumber('id');

Route::get('/subcategory-{id}-{slug?}', function (int $id, ?string $slug = null) {
    $newSlug = $slug ?? '';
    try {
        $table = Schema::hasTable('category') ? 'category' : (Schema::hasTable('categories') ? 'categories' : null);
        if ($table) {
            $name = DB::table($table)->where('cat_id', $id)->value('category')
                 ?: DB::table($table)->where('cat_id', $id)->value('category_name')
                 ?: DB::table($table)->where('cat_id', $id)->value('name')
                 ?: DB::table($table)->where('cat_id', $id)->value('title');
            if ($name) $newSlug = Str::slug($name);
        }
    } catch (\Throwable $e) {}
    return redirect()->route('categories.show', ['id' => $id, 'slug' => $newSlug], 301);
})->whereNumber('id');

Route::get('/write_a_review-{id}', function (int $id) {
    return redirect()->to(route('business.show', ['id' => $id]) . '#reviews', 301);
})->whereNumber('id');

// ---------------------------------------------------------
// DEBUG (antes do catch-all)
// ---------------------------------------------------------
Route::get('/_debug/pages', function () {
    $all = DB::table('pages')->select('id','slug','is_active','published_at')->orderBy('id')->get();
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

// ---------------------------------------------------------
// AUTH (Breeze)
// ---------------------------------------------------------
require __DIR__.'/auth.php';

// ---------------------------------------------------------
// PROBE (somente em testing)
// ---------------------------------------------------------
if (app()->environment('testing')) {
    Route::get('/_probe_alive', function () {
        return response('OK', 200);
    })->name('probe.alive');
}

// ---------------------------------------------------------
// Catch-all de páginas estáticas — por ÚLTIMO
//  ⚠️ IMPORTANTE: excluímos 'auth/' para não engolir as rotas OAuth
// ---------------------------------------------------------
Route::get('/{slug}', [\App\Http\Controllers\PageController::class, 'show'])
    ->where('slug', '^(?!api/|admin/|auth/|dashboard|login|register|password|negocio|business|categoria|category|cidade|city|contato|contact|sitemap\.xml|_debug/).+')
    ->name('pages.show');

// ---------------------------------------------------------
// FALLBACK 404
// ---------------------------------------------------------
Route::fallback(function () {
    $latest = DB::table('business')
        ->select('biz_id', 'business_name')
        ->orderByDesc('biz_id')
        ->limit(6)
        ->get();

    return response()->view('errors.404', ['latest' => $latest], 404);
});
