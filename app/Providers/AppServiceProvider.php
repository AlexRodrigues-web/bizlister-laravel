<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        // ...
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Paginação no estilo Bootstrap (Laravel 8)
        Paginator::useBootstrap();

        // Compartilha categorias para a navbar (dropdown dinâmico)
        View::composer('*', function ($view) {
            $cats = Cache::remember('nav.cats', 300, function () {
                try {
                    // Detecta a tabela de categorias (categories OU category)
                    $table = Schema::hasTable('categories')
                        ? 'categories'
                        : (Schema::hasTable('category') ? 'category' : null);

                    if (!$table) {
                        return collect();
                    }

                    $cols = Schema::getColumnListing($table);

                    // Coluna de ID
                    $idCol = in_array('cat_id', $cols, true)
                        ? 'cat_id'
                        : (in_array('id', $cols, true) ? 'id' : null);

                    if (!$idCol) {
                        return collect();
                    }

                    // Coluna de rótulo preferencial
                    $labelCol = null;
                    foreach (['category','category_name','cat_name','name','title','label'] as $c) {
                        if (in_array($c, $cols, true)) {
                            $labelCol = $c;
                            break;
                        }
                    }

                    // SELECT dinâmico
                    $select = $labelCol
                        ? "$idCol AS cat_id, $labelCol AS label"
                        : "$idCol AS cat_id, CONCAT('Categoria #', $idCol) AS label";

                    return DB::table($table)
                        ->selectRaw($select)
                        ->orderBy($labelCol ?: $idCol)
                        ->limit(12) // mostra até 12 no dropdown
                        ->get()
                        ->map(function ($r) {
                            $r->slug = Str::slug($r->label ?? '');
                            return $r;
                        });
                } catch (\Throwable $e) {
                    // Em caso de erro no banco, não quebra a view
                    return collect();
                }
            });

            $view->with('__navCats', $cats);
        });
    }
}
