<?php
use App\Http\Controllers\ProfileController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

use App\Http\Controllers\CategoryController;

Route::get('/categorias', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categoria/{id}-{slug?}', [CategoryController::class, 'show'])
    ->whereNumber('id')
    ->name('categories.show');
use App\Http\Controllers\CityController;

Route::get('/cidades', [CityController::class, 'index'])->name('cities.index');
Route::get('/cidade/{id}-{slug?}', [CityController::class, 'show'])
    ->whereNumber('id')
    ->name('cities.show');
use App\Http\Controllers\BusinessController;

Route::get("/negocio/{id}-{slug?}", [BusinessController::class, "show"])
    ->whereNumber("id")
    ->name("business.show");


use App\Http\Controllers\SearchController;
Route::get("/buscar", [SearchController::class, "index"])->name("search.index");

Route::middleware(['auth'])->group(function () {
});

Route::middleware('auth')->group(function () {
});

Route::middleware('auth')->group(function () {
    });

# ====== BizLister: rotas para criar/armazenar negÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¾Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â³cio ======
Route::get('/negocio/novo', [\App\Http\Controllers\BusinessController::class, 'create'])->name('business.create');
Route::post('/negocio', [\App\Http\Controllers\BusinessController::class, 'store'])->name('business.store');

/**
 * DEBUG TEMPORÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¾Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚ÂRIO: mostra qual arquivo Blade estÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¾Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ sendo usado para 'business.create'.
 * Acesse: /_debug_view
 * Remova este bloco quando terminar.
 */
Route::get('/_debug_view', function () {
    return view()->getFinder()->find('business.create');
});

Route::middleware(['auth','can:admin'])->group(function () {
    Route::get('/admin', [\App\Http\Controllers\AdminController::class, 'index'])->name('admin.dashboard');
});
Route::middleware(['auth', 'can:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

Route::get('/buscar', [SearchController::class, 'index'])->name('search.index');


# === [BEGIN AUTO] Rotas de negÃƒÆ’Ã‚Â³cio (detalhe/novo/store/redirect) ===
Route::get('/negocio/{id}-{slug?}', [\App\Http\Controllers\BusinessController::class, 'show'])
    ->whereNumber('id')
    ->name('business.show');

Route::middleware('auth')->group(function () {
    Route::get('/negocio/novo', [\App\Http\Controllers\BusinessCreateController::class, 'create'])
        ->name('business.create');

    Route::post('/negocio', [\App\Http\Controllers\BusinessCreateController::class, 'store'])
        ->name('business.store');
});

Route::get('/negocio', function () {
    return redirect()->route('search.index'); // ajuste o destino se quiser
});
# === [END AUTO] ===
require __DIR__.'/auth.php';

Route::middleware("auth")->group(function () {
    Route::get("/profile", [ProfileController::class, "edit"])->name("profile.edit");
    Route::patch("/profile", [ProfileController::class, "update"])->name("profile.update");
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
<<<<<<< Updated upstream
});
=======
});

/*
|-------------------------------------------------------------------------
| SEO: canônico + redirects de legado (301)
|-------------------------------------------------------------------------
*/

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

// Canônico: /negocio/{id}-{slug?}
Route::get('/negocio/{id}-{slug?}', [App\Http\Controllers\BusinessController::class, 'show'])
    ->whereNumber('id')
    ->name('business.show');

// Legado: /negocios/{id}/{slug?}  ->  /negocio/{id}-{slug}
Route::get('/negocios/{id}/{slug?}', function ($id, $slug = null) {
    $slug = $slug ? Str::slug($slug) : '';
    return redirect(url('negocio/' . $id . ($slug ? '-' . $slug : '')), 301);
})->whereNumber('id');

// Legado: /busca/categoria/{id}-{slug?}  ->  /categoria/{id}-{slug}
Route::get('/busca/categoria/{id}-{slug?}', function ($id, $slug = null) {
    $slug = $slug ? Str::slug($slug) : '';
    return redirect(url('categoria/' . $id . ($slug ? '-' . $slug : '')), 301);
})->whereNumber('id');

// Legado: /busca/cidade/{id}-{slug?}  ->  /cidade/{id}-{slug}
Route::get('/busca/cidade/{id}-{slug?}', function ($id, $slug = null) {
    $slug = $slug ? Str::slug($slug) : '';
    return redirect(url('cidade/' . $id . ($slug ? '-' . $slug : '')), 301);
})->whereNumber('id');

// Legado: /perfil/{uuid}  ->  /negocio/{id}-{slug}
Route::get('/perfil/{uuid}', function ($uuid) {
    $biz = DB::table('business')->where('unique_biz', $uuid)->first();
    if (!$biz) abort(404);
    $slug = Str::slug($biz->business_name ?? '');
    return redirect()->route('business.show', ['id' => $biz->biz_id, 'slug' => $slug], 301);
});

/*
|------------------------------------------------------------------
| Admin (auth + is_admin) – rotas canônicas
|------------------------------------------------------------------
*/
Route::middleware(['web','auth','can:admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Categorias
    Route::get('/categorias', [CategoryAdminController::class, 'index'])->name('admin.categories.index');
    Route::get('/categorias/novo', [CategoryAdminController::class, 'create'])->name('admin.categories.create');
    Route::post('/categorias', [CategoryAdminController::class, 'store'])->name('admin.categories.store');
    Route::get('/categorias/{cat}/editar', [CategoryAdminController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/categorias/{cat}', [CategoryAdminController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categorias/{cat}', [CategoryAdminController::class, 'destroy'])->name('admin.categories.destroy');

    // Cidades
    Route::get('/cidades', [CityAdminController::class, 'index'])->name('admin.cities.index');
    Route::get('/cidades/novo', [CityAdminController::class, 'create'])->name('admin.cities.create');
    Route::post('/cidades', [CityAdminController::class, 'store'])->name('admin.cities.store');
    Route::get('/cidades/{city}/editar', [CityAdminController::class, 'edit'])->name('admin.cities.edit');
    Route::put('/cidades/{city}', [CityAdminController::class, 'update'])->name('admin.cities.update');
    Route::delete('/cidades/{city}', [CityAdminController::class, 'destroy'])->name('admin.cities.destroy');

    // Negócios (listar/editar/remover)
    Route::get('/negocios', [BusinessManageController::class, 'index'])->name('admin.business.index');
    Route::get('/negocios/{biz}/editar', [BusinessManageController::class, 'edit'])->name('admin.business.edit');
    Route::put('/negocios/{biz}', [BusinessManageController::class, 'update'])->name('admin.business.update');
    Route::delete('/negocios/{biz}', [BusinessManageController::class, 'destroy'])->name('admin.business.destroy');
});
Route::middleware(["web","auth","can:admin"])->prefix("admin")->group(function () {
    Route::get("/", [\App\Http\Controllers\AdminController::class, "dashboard"])->name("admin.dashboard");

    Route::get("/categorias", [\App\Http\Controllers\Admin\CategoryAdminController::class, "index"])->name("admin.categories.index");
    Route::get("/categorias/novo", [\App\Http\Controllers\Admin\CategoryAdminController::class, "create"])->name("admin.categories.create");
    Route::post("/categorias", [\App\Http\Controllers\Admin\CategoryAdminController::class, "store"])->name("admin.categories.store");
    Route::get("/categorias/{cat}/editar", [\App\Http\Controllers\Admin\CategoryAdminController::class, "edit"])->name("admin.categories.edit");
    Route::put("/categorias/{cat}", [\App\Http\Controllers\Admin\CategoryAdminController::class, "update"])->name("admin.categories.update");
    Route::delete("/categorias/{cat}", [\App\Http\Controllers\Admin\CategoryAdminController::class, "destroy"])->name("admin.categories.destroy");

    Route::get("/cidades", [\App\Http\Controllers\Admin\CityAdminController::class, "index"])->name("admin.cities.index");
    Route::get("/cidades/novo", [\App\Http\Controllers\Admin\CityAdminController::class, "create"])->name("admin.cities.create");
    Route::post("/cidades", [\App\Http\Controllers\Admin\CityAdminController::class, "store"])->name("admin.cities.store");
    Route::get("/cidades/{city}/editar", [\App\Http\Controllers\Admin\CityAdminController::class, "edit"])->name("admin.cities.edit");
    Route::put("/cidades/{city}", [\App\Http\Controllers\Admin\CityAdminController::class, "update"])->name("admin.cities.update");
    Route::delete("/cidades/{city}", [\App\Http\Controllers\Admin\CityAdminController::class, "destroy"])->name("admin.cities.destroy");

    Route::get("/negocios", [\App\Http\Controllers\BusinessManageController::class, "index"])->name("admin.business.index");
    Route::get("/negocios/{biz}/editar", [\App\Http\Controllers\BusinessManageController::class, "edit"])->name("admin.business.edit");
    Route::put("/negocios/{biz}", [\App\Http\Controllers\BusinessManageController::class, "update"])->name("admin.business.update");
    Route::delete("/negocios/{biz}", [\App\Http\Controllers\BusinessManageController::class, "destroy"])->name("admin.business.destroy");
});
>>>>>>> Stashed changes
