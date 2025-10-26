<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CmsPagesSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $pages = [
            [
                'slug' => 'sobre',
                'title' => 'Sobre Nós',
                'content' => <<<'HTML'
<h1>Sobre Nós</h1>
<p>... cole aqui o HTML completo do legado ...</p>
HTML,
            ],
            [
                'slug' => 'termos',
                'title' => 'Termos de Uso',
                'content' => <<<'HTML'
<h1>Termos de Uso</h1>
<p>... cole aqui o HTML completo dos termos ...</p>
HTML,
            ],
            [
                'slug' => 'politica-de-privacidade',
                'title' => 'Política de Privacidade',
                'content' => <<<'HTML'
<h1>Política de Privacidade</h1>
<p>... cole aqui o HTML completo da política ...</p>
HTML,
            ],
        ];

        foreach ($pages as $p) {
            DB::table('pages')->updateOrInsert(
                ['slug' => $p['slug']],
                [
                    'title'        => $p['title'],
                    'content'      => $p['content'],
                    'is_active'    => 1,
                    'published_at' => $now,
                    'updated_at'   => $now,
                    'created_at'   => $now,
                ]
            );
        }
    }
}
