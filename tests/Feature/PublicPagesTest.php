<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    protected $catSeedId  = 999901;
    protected $citySeedId = 999901;

    protected function setUp(): void
    {
        parent::setUp();

        // Garante que a base URL nos testes é http://localhost (sem subpasta)
        config(['app.url' => 'http://localhost']);
        URL::forceRootUrl(config('app.url'));

        // Seed compatível com legado: parent_id=0 e colunas category/cat_description
        if (!DB::table('categories')->where('cat_id', $this->catSeedId)->exists()) {
            DB::table('categories')->insert([
                'cat_id'          => $this->catSeedId,
                'name'            => 'Teste Categoria',
                'category'        => 'Teste Categoria',
                'cat_description' => '',
                'slug'            => 'teste-categoria',
                'description'     => null,
                'parent_id'       => 0,
                'is_active'       => 1,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        // Seed da city incluindo campo legado 'city' (NOT NULL)
        if (!DB::table('city')->where('city_id', $this->citySeedId)->exists()) {
            DB::table('city')->insert([
                'city_id'    => $this->citySeedId,
                'city'       => 'Teste Cidade',
                'name'       => 'Teste Cidade',
                'slug'       => 'teste-cidade',
                'state'      => 'SP',
                'is_active'  => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function tearDown(): void
    {
        DB::table('categories')->where('cat_id', $this->catSeedId)->delete();
        DB::table('city')->where('city_id', $this->citySeedId)->delete();
        parent::tearDown();
    }

    /** @test */
    public function categorias_responde_200(): void
    {
        $this->get('/categorias')->assertStatus(200);
    }

    /** @test */
    public function cidades_responde_200(): void
    {
        $this->get('/cidades')->assertStatus(200);
    }
}