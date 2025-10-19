<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\BusinessCreateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\CityAdminController;
use App\Http\Controllers\Admin\BusinessAdminController;

// Páginas estáticas & contato (controllers dedicados)
use App\Http\Controllers\PageController;
use App\Http\Controllers\ContactController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Mantido tudo que já funcionava; apenas organização, sem remoções.
*/

/* ======== HOME & DASHBOARD ======== */
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

/* ======== PÚBLICO ======== */

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

// Busca
Route::get('/buscar', [SearchController::class, 'index'])->name('search.index');

// /negocio sem id -> redireciona para busca
Route::get('/negocio', fn () => redirect()->route('search.index'));

// Form de criação (público, só exibe o form)
Route::get('/negocio/novo', [BusinessController::class, 'create'])->name('business.create');

/* ======== ÁREA AUTENTICADA (público logado + perfil) ======== */
Route::middleware('auth')->group(function () {
    // Envio do formulário (mantém protegido)
    Route::post('/negocio', [BusinessController::class, 'store'])->name('business.store');

    // Perfil (Breeze)
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});

/* ======== ÁREA ADMIN (Middleware: is_admin) ======== */
Route::middleware(['auth','is_admin'])
    ->prefix('admin')->as('admin.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');

        // Diagnóstico opcional
        Route::get('/ping', fn () => response('admin-ping-ok', 200))->name('ping');

        // Categorias
        Route::resource('categories', CategoryAdminController::class)->except(['show']);

        // Cidades
        Route::resource('cities',     CityAdminController::class)->except(['show']);

        // Negócios (admin)
        Route::get('/businesses',           [BusinessAdminController::class,'index'])->name('businesses.index');
        Route::get('/businesses/{id}/edit', [BusinessAdminController::class,'edit'])->name('businesses.edit');
        Route::put('/businesses/{id}',      [BusinessAdminController::class,'update'])->name('businesses.update');
        Route::delete('/businesses/{id}',   [BusinessAdminController::class,'destroy'])->name('businesses.destroy');

        // Alias compat
        Route::get('/business', fn () => redirect()->route('admin.businesses.index'))->name('business.index');
    });

/* ==================== BEGIN LEGACY_301_REDIRECTS ==================== */
// business-{id}-{slug}.html  → /negocio/{id}-{slug}
Route::get('/business-{id}-{slug?}.html', function (int $id, ?string $slug = null) {
    $name    = DB::table('business')->where('biz_id', $id)->value('business_name');
    $newSlug = Str::slug($name ?? ($slug ?? ''));
    return redirect()->route('business.show', ['id' => $id, 'slug' => $newSlug], 301);
})->whereNumber('id');

// business-{id}-{slug} (sem .html) → /negocio/{id}-{slug}
Route::get('/business-{id}-{slug?}', function (int $id, ?string $slug = null) {
    $name    = DB::table('business')->where('biz_id', $id)->value('business_name');
    $newSlug = Str::slug($name ?? ($slug ?? ''));
    return redirect()->route('business.show', ['id' => $id, 'slug' => $newSlug], 301);
})->whereNumber('id');

// business-{id} (sem slug) → /negocio/{id}-{slug}
Route::get('/business-{id}', function (int $id) {
    $name    = DB::table('business')->where('biz_id', $id)->value('business_name');
    $newSlug = Str::slug($name ?? '');
    return redirect()->route('business.show', ['id' => $id, 'slug' => $newSlug], 301);
})->whereNumber('id');

// write_a_review-{id} → /negocio/{id}
Route::get('/write_a_review-{id}', function (int $id) {
    $name    = DB::table('business')->where('biz_id', $id)->value('business_name');
    $newSlug = Str::slug($name ?? '');
    return redirect()->route('business.show', ['id' => $id, 'slug' => $newSlug], 301);
})->whereNumber('id');

// category-{id}-{slug} → /categoria/{id}-{slug}
Route::get('/category-{id}-{slug?}', function (int $id, ?string $slug = null) {
    $newSlug = $slug ?? '';
    try {
        if (Schema::hasTable('category')) {
            // tenta coluna "category_name", depois "category" (legado)
            $name = DB::table('category')->where('cat_id', $id)->value('category_name');
            if (!$name) {
                $name = DB::table('category')->where('cat_id', $id)->value('category');
            }
            if ($name) {
                $newSlug = Str::slug($name);
            }
        }
    } catch (\Throwable $e) {
        // se der erro, só usa o slug recebido/zerado — não quebra
    }
    return redirect()->route('categories.show', ['id' => $id, 'slug' => $newSlug], 301);
})->whereNumber('id');

// subcategory-{id}-{slug} → /categoria/{id}-{slug}
Route::get('/subcategory-{id}-{slug?}', function (int $id, ?string $slug = null) {
    $newSlug = $slug ?? '';
    try {
        if (Schema::hasTable('category')) {
            $name = DB::table('category')->where('cat_id', $id)->value('category_name');
            if (!$name) {
                $name = DB::table('category')->where('cat_id', $id)->value('category');
            }
            if ($name) {
                $newSlug = Str::slug($name);
            }
        }
    } catch (\Throwable $e) {
        // idem acima
    }
    return redirect()->route('categories.show', ['id' => $id, 'slug' => $newSlug], 301);
})->whereNumber('id');
/* ===================== END LEGACY_301_REDIRECTS ===================== */

/* ==================== BEGIN USEFUL_404_FALLBACK ==================== */
Route::fallback(function () {
    $latest = DB::table('business')
        ->select('biz_id', 'business_name')
        ->orderByDesc('biz_id')
        ->limit(6)
        ->get();

    return response()->view('errors.404', ['latest' => $latest], 404);
});
/* ===================== END USEFUL_404_FALLBACK ===================== */

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Auth routes (Breeze)
require __DIR__.'/auth.php';

// Páginas estáticas & contato (mantendo teu modelo atual com controllers)
Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', 'sobre|termos')
    ->name('pages.show');

Route::get('/contato', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contato', [ContactController::class, 'submit'])->name('contact.send');

/* ==================== ADMIN COMPAT (só registra se não existir) ==================== */
Route::middleware(['auth','is_admin'])
    ->prefix('admin')->as('admin.')
    ->group(function () {
        if (!Route::has('admin.categories.index')) {
            Route::resource('categories', CategoryAdminController::class)->except(['show']);
        }
        if (!Route::has('admin.cities.index')) {
            Route::resource('cities',     CityAdminController::class)->except(['show']);
        }
        if (!Route::has('admin.businesses.index')) {
            Route::resource('businesses', BusinessAdminController::class)->except(['show']);
        }
    });
/* ==================== FIM ADMIN COMPAT ==================== */
