<?php
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\CityAdminController;


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

// Categorias
Route::get('/categorias', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categoria/{id}-{slug?}', [CategoryController::class, 'show'])
    ->whereNumber('id')
    ->name('categories.show');

// Cidades
Route::get('/cidades', [CityController::class, 'index'])->name('cities.index');
Route::get('/cidade/{id}-{slug?}', [CityController::class, 'show'])
    ->whereNumber('id')
    ->name('cities.show');

// Negócio (detalhe)
Route::get('/negocio/{id}-{slug?}', [BusinessController::class, 'show'])
    ->whereNumber('id')
    ->name('business.show');

// Busca
Route::get('/buscar', [SearchController::class, 'index'])->name('search.index');

// Negócio (criar/salvar) — com autenticação
Route::middleware('auth')->group(function () {
    Route::get('/negocio/novo', [\App\Http\Controllers\BusinessCreateController::class, 'create'])
        ->name('business.create');
    Route::post('/negocio', [\App\Http\Controllers\BusinessCreateController::class, 'store'])
        ->name('business.store');
});

// /negocio sem id -> redireciona para busca
Route::get('/negocio', function () {
    return redirect()->route('search.index');
});

// Admin
Route::middleware(['auth', 'can:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});/*
|----------------------------------------------------------------------
| Admin CRUD (Categorias, Cidades)
|----------------------------------------------------------------------
*/
Route::middleware(['auth','can:admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        // dashboard (já existe em outro ponto, mas mantemos aqui se quiser unificar)
        // Route::get('/', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');

        Route::resource('categories', \App\Http\Controllers\Admin\CategoryAdminController::class);
        Route::resource('cities', \App\Http\Controllers\Admin\CityAdminController::class);
        // Se tiver o controller de negócios, descomente a seguir:
        // Route::resource('businesses', \App\Http\Controllers\Admin\BusinessAdminController::class);
    });
