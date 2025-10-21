<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class PagesSeeder extends Seeder
{
    public function run()
    {
        $items = [
            [
                'slug' => 'sobre-nos',
                'title' => 'Sobre Nós',
                'content' => '<p>Conteúdo padrão da página Sobre Nós.</p>',
                'is_active' => 1,
                'published_at' => now(),
            ],
            [
                'slug' => 'termos-de-uso',
                'title' => 'Termos de Uso',
                'content' => '<p>Seus termos de uso aqui.</p>',
                'is_active' => 1,
                'published_at' => now(),
            ],
            [
                'slug' => 'politica-de-privacidade',
                'title' => 'Política de Privacidade',
                'content' => '<p>Sua política de privacidade aqui.</p>',
                'is_active' => 1,
                'published_at' => now(),
            ],
        ];

        foreach ($items as $it) {
            Page::updateOrCreate(['slug' => $it['slug']], $it);
        }
    }
}