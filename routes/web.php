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
use App\Http\Controllers\Admin\AdvertisementAdminController;
use App\Http\Controllers\HoursController;
use App\Http\Controllers\BusinessBookmarkController;
use App\Http\Controllers\Auth\SocialAuthController; // OAuth Google/Facebook
use App\Http\Controllers\SubcategoryController;      // Subcategorias
use App\Http\Controllers\BusinessGalleryController;  // Galeria
// Admin (NOVOS)
use App\Http\Controllers\Admin\SettingAdminController;
use App\Http\Controllers\Admin\PageAdminController;

// ---------------------------------------------------------
// Redirects simples
// ---------------------------------------------------------
Route::redirect('/sobre-nos', '/sobre', 301);
Route::redirect('/meu-perfil', '/perfil', 301); // atalho

// + Aliases típicos -> slugs oficiais das páginas
Route::redirect('/about', '/sobre', 301);                       // +
Route::redirect('/about-us', '/sobre', 301);                    // +
Route::redirect('/terms', '/termos', 301);                      // +
Route::redirect('/terms-of-service', '/termos', 301);           // +
Route::redirect('/privacy', '/politica-de-privacidade', 301);   // +
Route::redirect('/privacy-policy', '/politica-de-privacidade', 301); // +
Route::redirect('/privacidade', '/politica-de-privacidade', 301);    // +

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

// Subcategorias (NOVIDADE)
Route::get('/subcategories/{subcategory:slug}', [SubcategoryController::class, 'show'])
    ->name('subcategories.show');

// Endpoint JSON p/ carregar subcategorias de uma categoria (AJAX no formulário de business)
Route::get('/categories/{category}/subcategories', function (\App\Models\Category $category) {
    return $category->subcategories()->select('id','name','slug')->orderBy('name')->get();
})->name('categories.subcategories.index');

// Negócio (detalhe)
Route::get('/negocio/{id}-{slug?}', [BusinessController::class, 'show'])
    ->whereNumber('id')->name('business.show');

// /negocio sem id -> busca
Route::get('/negocio', function () {
    return redirect()->route('search.index');
});

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

    // ---------------------------------------------------------
    // GALERIA DO NEGÓCIO
    // ---------------------------------------------------------
    Route::get('/negocio/{biz}/galeria', [BusinessGalleryController::class, 'index'])
        ->whereNumber('biz')->name('business.gallery.index');

    Route::post('/negocio/{biz}/galeria', [BusinessGalleryController::class, 'store'])
        ->whereNumber('biz')->name('business.gallery.store');

    Route::delete('/negocio/{biz}/galeria/{image}', [BusinessGalleryController::class, 'destroy'])
        ->whereNumber('biz')->name('business.gallery.destroy');

    Route::post('/negocio/{biz}/galeria/{image}/primary', [BusinessGalleryController::class, 'setPrimary'])
        ->whereNumber('biz')->name('business.gallery.primary');

    Route::post('/negocio/{biz}/galeria/reorder', [BusinessGalleryController::class, 'reorder'])
        ->whereNumber('biz')->name('business.gallery.reorder');
});

// ---------------------------------------------------------
// ÁREA ADMIN
// ---------------------------------------------------------
Route::middleware(['auth','is_admin'])
    ->prefix('admin')->as('admin.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminController::class, 'index'])
            ->name('dashboard');

        Route::get('/ping', function () { return response('admin-ping-ok', 200); })->name('ping');

        Route::resource('categories', \App\Http\Controllers\Admin\CategoryAdminController::class)->except(['show']);
        Route::resource('cities', \App\Http\Controllers\Admin\CityAdminController::class)->except(['show']);
        Route::resource('businesses', \App\Http\Controllers\Admin\BusinessAdminController::class)->except(['show']);
        Route::get('/advertisements', [AdvertisementAdminController::class, 'edit'])
            ->name('advertisements.edit');
        Route::put('/advertisements', [AdvertisementAdminController::class, 'update'])
            ->name('advertisements.update');

        Route::get('/business', function () {
            return redirect()->route('admin.businesses.index');
        })->name('business.index');

        Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewAdminController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{review}/approve', [\App\Http\Controllers\Admin\ReviewAdminController::class, 'approve'])
            ->whereNumber('review')->name('reviews.approve');
        Route::post('/reviews/{review}/hide', [\App\Http\Controllers\Admin\ReviewAdminController::class,'hide'])
            ->whereNumber('review')->name('reviews.hide');
        Route::delete('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewAdminController::class, 'destroy'])
            ->whereNumber('review')->name('reviews.destroy');

        // =======================
        // SETTINGS (form único)
        // =======================
        Route::get('/settings', [SettingAdminController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingAdminController::class, 'update'])->name('settings.update');

        // =======================
        // PAGES (CRUD)
        // =======================
        Route::resource('pages', PageAdminController::class)->except(['show']);
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

// Legacy de subcategoria -> nova rota por slug
Route::get('/subcategory-{id}-{slug?}', function (int $id, ?string $slug = null) {
    try {
        if (Schema::hasTable('subcategories')) {
            $pk = Schema::hasColumn('subcategories', 'id') ? 'id' : (Schema::hasColumn('subcategories', 'sid') ? 'sid' : null);
            if ($pk) {
                $row = DB::table('subcategories')->where($pk, $id)->first();
                if ($row) {
                    $slugCol = Schema::hasColumn('subcategories', 'slug') ? 'slug' : null;
                    $nameCol = Schema::hasColumn('subcategories', 'name') ? 'name'
                             : (Schema::hasColumn('subcategories', 'subcategory') ? 'subcategory' : null);

                    $finalSlug = $slugCol && !empty($row->$slugCol)
                               ? $row->$slugCol
                               : Str::slug($nameCol ? ($row->$nameCol ?? ($slug ?? '')) : ($slug ?? ''));

                    // passa param nomeado, pois a rota usa {subcategory:slug}
                    return redirect()->route('subcategories.show', ['subcategory' => $finalSlug], 301);
                }
            }
        }

        // Fallback: tenta categoria
        if (Schema::hasTable('category') || Schema::hasTable('categories')) {
            $table = Schema::hasTable('category') ? 'category' : 'categories';
            $name = DB::table($table)->where('cat_id', $id)->value('name')
                 ?: DB::table($table)->where('cat_id', $id)->value('category')
                 ?: DB::table($table)->where('cat_id', $id)->value('category_name');
            $slug2 = Str::slug($name ?? ($slug ?? ''));
            return redirect()->route('categories.show', ['id' => $id, 'slug' => $slug2], 301);
        }
    } catch (\Throwable $e) {}

    return redirect()->route('categories.index', [], 301);
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
            $q->whereNull('published_at')
              ->orWhere('published_at', '<=', \Illuminate\Support\Facades\DB::raw('NOW()')); // <= aqui
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
// (exclui caminhos sensíveis pra não engolir rotas)
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
