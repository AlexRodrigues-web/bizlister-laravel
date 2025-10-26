<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StaticPagesSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug'    => 'sobre',
                'title'   => 'Sobre Nós',
                'content' => '<h1>Sobre Nós</h1><p>Conteúdo institucional migrado.</p>',
                'active'  => 1,
            ],
            [
                'slug'    => 'termos',
                'title'   => 'Termos de Uso',
                'content' => '<h1>Termos de Uso</h1><p>Condições e políticas do serviço.</p>',
                'active'  => 1,
            ],
            [
                'slug'    => 'politica-de-privacidade',
                'title'   => 'Política de Privacidade',
                'content' => '<h1>Política de Privacidade</h1><p>Como tratamos seus dados.</p>',
                'active'  => 1,
            ],
        ];

        $hasActive = Schema::hasColumn('pages', 'active');

        foreach ($pages as $p) {
            $exists = DB::table('pages')->where('slug', $p['slug'])->first();

            $update = [
                'title'      => $p['title'],
                'content'    => $p['content'],
                'updated_at' => now(),
            ];
            if ($hasActive) { $update['active'] = $p['active']; }

            if ($exists) {
                DB::table('pages')->where('id', $exists->id)->update($update);
            } else {
                $insert = array_merge(
                    [
                        'slug'       => $p['slug'],
                        'title'      => $p['title'],
                        'content'    => $p['content'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    $hasActive ? ['active' => $p['active']] : []
                );
                DB::table('pages')->insert($insert);
            }
        }
    }
}