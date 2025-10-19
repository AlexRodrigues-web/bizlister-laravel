<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    /**
     * GET /sitemap.xml
     */
    public function index(): Response
    {
        try {
            $base   = url('/');
            $nowIso = now()->toAtomString();

            // Helper: só adiciona rota se existir
            $static = [];
            foreach (['categories.index', 'cities.index', 'search.index'] as $rname) {
                if (RouteFacade::has($rname)) {
                    $static[] = route($rname);
                }
            }

            // ==== Coletas com tolerância a esquema ====
            $cats   = collect();
            $cities = collect();
            $biz    = collect();

            // Categorias (legacy: tabela category; coluna 'category' ou 'category_name')
            if (Schema::hasTable('category')) {
                $catQuery = DB::table('category')->select('cat_id');

                // seleciona também a coluna de nome que existir
                if (Schema::hasColumn('category', 'category_name')) {
                    $catQuery->addSelect('category_name');
                }
                if (Schema::hasColumn('category', 'category')) {
                    $catQuery->addSelect('category');
                }

                $cats = $catQuery->orderBy('cat_id')->get();
            }

            // Cidades (legacy: tabela city; colunas 'city_id', 'city')
            if (Schema::hasTable('city')) {
                $cityQuery = DB::table('city')->select('city_id');
                if (Schema::hasColumn('city', 'city')) {
                    $cityQuery->addSelect('city');
                }
                $cities = $cityQuery->orderBy('city_id')->get();
            }

            // Negócios (legacy: tabela business; colunas 'biz_id', 'business_name')
            if (Schema::hasTable('business')) {
                $bizQuery = DB::table('business')->select('biz_id');
                if (Schema::hasColumn('business', 'business_name')) {
                    $bizQuery->addSelect('business_name')->whereNotNull('business_name');
                }
                $biz = $bizQuery->orderByDesc('biz_id')->get();
            }

            // ==== Montagem do XML ====
            $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

            // home
            $xml .= $this->urlNode($base . '/', $nowIso, 'daily', '1.0');

            // estáticas (se existirem)
            foreach ($static as $u) {
                $xml .= $this->urlNode($u, $nowIso, 'weekly', '0.7');
            }

            // categorias
            foreach ($cats as $c) {
                $name = $c->category_name ?? $c->category ?? null;
                $slug = Str::slug($name ?? (string) $c->cat_id);
                // só gera se a rota existir
                if (RouteFacade::has('categories.show')) {
                    $loc = route('categories.show', ['id' => $c->cat_id, 'slug' => $slug]);
                    $xml .= $this->urlNode($loc, $nowIso, 'weekly', '0.6');
                }
            }

            // cidades
            foreach ($cities as $c) {
                $name = $c->city ?? null;
                $slug = Str::slug($name ?? (string) $c->city_id);
                if (RouteFacade::has('cities.show')) {
                    $loc = route('cities.show', ['id' => $c->city_id, 'slug' => $slug]);
                    $xml .= $this->urlNode($loc, $nowIso, 'weekly', '0.6');
                }
            }

            // negócios
            foreach ($biz as $b) {
                $name = $b->business_name ?? null;
                $slug = Str::slug($name ?? (string) $b->biz_id);
                if (RouteFacade::has('business.show')) {
                    $loc = route('business.show', ['id' => $b->biz_id, 'slug' => $slug]);
                    $xml .= $this->urlNode($loc, $nowIso, 'weekly', '0.8');
                }
            }

            $xml .= "</urlset>\n";

            return response($xml, 200)
                ->header('Content-Type', 'application/xml; charset=UTF-8');
        } catch (\Throwable $e) {
            // Loga para diagnóstico e ainda assim devolve um XML mínimo válido
            Log::error('sitemap.xml error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $fallback  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            $fallback .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
            $fallback .= $this->urlNode(url('/'), now()->toAtomString(), 'daily', '1.0');
            $fallback .= "</urlset>\n";

            // Mantém 200 para não prejudicar bots enquanto você verifica o log
            return response($fallback, 200)
                ->header('Content-Type', 'application/xml; charset=UTF-8');
        }
    }

    private function urlNode(string $loc, string $lastmod, string $changefreq, string $priority): string
    {
        // Escapa como XML
        $locEsc = htmlspecialchars($loc, ENT_XML1);
        return
            "  <url>\n" .
            "    <loc>{$locEsc}</loc>\n" .
            "    <lastmod>{$lastmod}</lastmod>\n" .
            "    <changefreq>{$changefreq}</changefreq>\n" .
            "    <priority>{$priority}</priority>\n" .
            "  </url>\n";
    }
}
