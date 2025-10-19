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

        // Negócios (admin) — usar APENAS resource para evitar duplicações de names
        Route::resource('businesses', BusinessAdminController::class)->except(['show']);

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

/* ======== AUTH (Breeze) ======== */
require __DIR__.'/auth.php';

/* ======== PROBES DE TESTE (somente APP_ENV=testing) ======== */
if (app()->environment('testing') && file_exists(base_path('routes/testing_probes.php'))) {
    require base_path('routes/testing_probes.php');
}

/* ======== PÁGINAS ESTÁTICAS & CONTATO ======== */
Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', 'sobre|termos')
    ->name('pages.show');

Route::get('/contato', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contato', [ContactController::class, 'submit'])->name('contact.send');

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

/* ==================== FALLBACK 404 (DEIXE POR ÚLTIMO) ==================== */
Route::fallback(function () {
    $latest = DB::table('business')
        ->select('biz_id', 'business_name')
        ->orderByDesc('biz_id')
        ->limit(6)
        ->get();

    return response()->view('errors.404', ['latest' => $latest], 404);
});
/* ===================== END FALLBACK ===================== */

/* ======== REVIEWS ======== */
// P�blico (enviar review)
Route::middleware("auth")->group(function () {
    Route::post("/negocio/{id}/reviews", [\App\Http\Controllers\ReviewController::class, "store"])
        ->whereNumber("id")
        ->name("reviews.store");
});

// Admin (modera��o)
Route::middleware(["auth","is_admin"])
    ->prefix("admin")->as("admin.")
    ->group(function () {
        Route::get("/reviews", [\App\Http\Controllers\Admin\ReviewAdminController::class, "index"])->name("reviews.index");
        Route::post("/reviews/{review}/approve", [\App\Http\Controllers\Admin\ReviewAdminController::class, "approve"])->name("reviews.approve");
        Route::post("/reviews/{review}/hide",    [\App\Http\Controllers\Admin\ReviewAdminController::class, "hide"])->name("reviews.hide");
        Route::delete("/reviews/{review}",       [\App\Http\Controllers\Admin\ReviewAdminController::class, "destroy"])->name("reviews.destroy");
    });

// Legado: /write_a_review-{id} -> ancora #reviews na p�gina do neg�cio
Route::get("/write_a_review-{id}", function (int $id) {
    return redirect()->route("business.show", ["id"=>$id]) . "#reviews";
})->whereNumber("id");

