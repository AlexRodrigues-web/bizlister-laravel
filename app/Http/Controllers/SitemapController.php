<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $base = url('/');
        $nowIso = now()->toAtomString();

        $urls = [];

        // Home
        $urls[] = ['loc' => $base . '/', 'lastmod' => $nowIso];

        // Páginas estáticas publicadas (usa scopePublished)
        $pages = Page::published()->get(['slug', 'updated_at']);
        foreach ($pages as $p) {
            $urls[] = [
                'loc'     => $base . '/' . $p->slug,
                'lastmod' => optional($p->updated_at)->toAtomString(),
            ];
        }

        // Montagem do XML simples (sem view para reduzir dependência)
        $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        foreach ($urls as $u) {
            $loc = htmlspecialchars($u['loc'], ENT_XML1);
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$loc}</loc>\n";
            if (!empty($u['lastmod'])) {
                $xml .= "    <lastmod>{$u['lastmod']}</lastmod>\n";
            }
            $xml .= "  </url>\n";
        }
        $xml .= "</urlset>\n";

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}