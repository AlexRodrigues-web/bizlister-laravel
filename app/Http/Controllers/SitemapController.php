<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * GET /sitemap.xml
     */
    public function index(): Response
    {
        $base   = url('/');
        $nowIso = now()->toAtomString();

        // rotas estáticas relevantes
        $static = [
            route('categories.index'),
            route('cities.index'),
            route('search.index'),
        ];

        // legado: category (cat_id, category)
        $cats = DB::table('category')
            ->select('cat_id', 'category')
            ->orderBy('cat_id')
            ->get();

        // legado: city (city_id, city)
        $cities = DB::table('city')
            ->select('city_id', 'city')
            ->orderBy('city_id')
            ->get();

        // legado: business (biz_id, business_name)
        $biz = DB::table('business')
            ->select('biz_id', 'business_name')
            ->whereNotNull('business_name')
            ->orderByDesc('biz_id')
            ->get();

        $urls = [];

        // home
        $urls[] = [
            'loc'        => $base . '/',
            'changefreq' => 'daily',
            'priority'   => '1.0',
            'lastmod'    => $nowIso,
        ];

        foreach ($static as $u) {
            $urls[] = ['loc' => $u, 'changefreq' => 'weekly', 'priority' => '0.7', 'lastmod' => $nowIso];
        }

        foreach ($cats as $c) {
            $slug = Str::slug($c->category ?? (string)$c->cat_id);
            $urls[] = [
                'loc'        => route('categories.show', ['id' => $c->cat_id, 'slug' => $slug]),
                'changefreq' => 'weekly',
                'priority'   => '0.6',
                'lastmod'    => $nowIso,
            ];
        }

        foreach ($cities as $c) {
            $slug = Str::slug($c->city ?? (string)$c->city_id);
            $urls[] = [
                'loc'        => route('cities.show', ['id' => $c->city_id, 'slug' => $slug]),
                'changefreq' => 'weekly',
                'priority'   => '0.6',
                'lastmod'    => $nowIso,
            ];
        }

        foreach ($biz as $b) {
            $slug = Str::slug($b->business_name ?? (string)$b->biz_id);
            $urls[] = [
                'loc'        => route('business.show', ['id' => $b->biz_id, 'slug' => $slug]),
                'changefreq' => 'weekly',
                'priority'   => '0.8',
                'lastmod'    => $nowIso,
            ];
        }

        // XML simples e válido
        $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        foreach ($urls as $u) {
            // Escapar como XML (ENT_XML1)
            $loc = htmlspecialchars($u['loc'], ENT_XML1);
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$loc}</loc>\n";
            if (!empty($u['lastmod']))    { $xml .= "    <lastmod>{$u['lastmod']}</lastmod>\n"; }
            if (!empty($u['changefreq'])) { $xml .= "    <changefreq>{$u['changefreq']}</changefreq>\n"; }
            if (!empty($u['priority']))   { $xml .= "    <priority>{$u['priority']}</priority>\n"; }
            $xml .= "  </url>\n";
        }
        $xml .= "</urlset>\n";

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
