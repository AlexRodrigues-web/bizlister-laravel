<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\BusinessCreateController;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\CityAdminController;
use App\Http\Controllers\Admin\BusinessAdminController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

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
Route::get('/negocio', fn() => redirect()->route('search.index'));

/* ======== CRUD PÚBLICO AUTENTICADO ======== */
Route::middleware('auth')->group(function () {
    Route::get('/negocio/novo',  [BusinessCreateController::class, 'create'])->name('business.create');
    Route::post('/negocio',      [BusinessCreateController::class, 'store'])->name('business.store');
});

/* ======== ÁREA ADMIN (Gate: admin) ======== */
Route::middleware(['auth','can:admin'])
    ->prefix('admin')->as('admin.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');

        // Diagnóstico opcional
        Route::get('/ping', fn() => response('admin-ping-ok', 200))->name('ping');

        // Categorias
        Route::resource('categories', CategoryAdminController::class)->except(['show']);

        // Cidades
        Route::resource('cities',     CityAdminController::class)->except(['show']);

        // Negócios (controlador admin legado sem resource completo)
        Route::get('/businesses',           [BusinessAdminController::class,'index'])->name('businesses.index');
        Route::get('/businesses/{id}/edit', [BusinessAdminController::class,'edit'])->name('businesses.edit');
        Route::put('/businesses/{id}',      [BusinessAdminController::class,'update'])->name('businesses.update');
        Route::delete('/businesses/{id}',   [BusinessAdminController::class,'destroy'])->name('businesses.destroy');

        // Alias compat
        Route::get('/business', fn() => redirect()->route('admin.businesses.index'))->name('business.index');
    });

/* ==================== BEGIN LEGACY_301_REDIRECTS ==================== */
Route::get('/business-{id}-{slug?}', function (int $id, ?string $slug = null) {
    $name = \Illuminate\Support\Facades\DB::table('business')->where('biz_id', $id)->value('business_name');
    $newSlug = \Illuminate\Support\Str::slug($name ?? ($slug ?? ''));
    return redirect()->route('business.show', ['id' => $id, 'slug' => $newSlug], 301);
})->whereNumber('id');

Route::get('/business-{id}', function (int $id) {
    $name = \Illuminate\Support\Facades\DB::table('business')->where('biz_id', $id)->value('business_name');
    $newSlug = \Illuminate\Support\Str::slug($name ?? '');
    return redirect()->route('business.show', ['id' => $id, 'slug' => $newSlug], 301);
})->whereNumber('id');

Route::get('/negocio-{id}-{slug?}', function (int $id, ?string $slug = null) {
    $name = \Illuminate\Support\Facades\DB::table('business')->where('biz_id', $id)->value('business_name');
    $newSlug = \Illuminate\Support\Str::slug($name ?? ($slug ?? ''));
    return redirect()->route('business.show', ['id' => $id, 'slug' => $newSlug], 301);
})->whereNumber('id');
/* ===================== END LEGACY_301_REDIRECTS ===================== */

/* ==================== BEGIN USEFUL_404_FALLBACK ==================== */
Route::fallback(function () {
    $latest = \Illuminate\Support\Facades\DB::table('business')
        ->select('biz_id', 'business_name')
        ->orderByDesc('biz_id')
        ->limit(6)
        ->get();

    return response()->view('errors.404', ['latest' => $latest], 404);
});
/* ===================== END USEFUL_404_FALLBACK ===================== */

/* ========== BEGIN TEMP_ME_DEBUG (local only) ========== */
if (app()->environment('local')) {
    \Illuminate\Support\Facades\Route::middleware('auth')->get('/me', function () {
        $u = auth()->user();
        return response()->json([
            'email'      => $u?->email,
            'gate_admin' => \Illuminate\Support\Facades\Gate::allows('admin'),
        ]);
    });
}
/* =========== END TEMP_ME_DEBUG (local only) =========== */

use App\Http\Controllers\SitemapController;
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
