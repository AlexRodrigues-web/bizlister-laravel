<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Garantir created_at / updated_at (nullable para não quebrar inserts antigos)
        Schema::table('pages', function (Blueprint $table) {
            if (!Schema::hasColumn('pages', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('pages', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        // 2) Garantir que slug tenha valores únicos
        //    Ajusta slugs NULL ou vazios para "page-{id}" antes de criar o índice único
        DB::statement("UPDATE pages SET slug = CONCAT('page-', id) WHERE slug IS NULL OR slug = ''");

        // 3) Tentar criar o índice único (se já existir, ignorar)
        try {
            Schema::table('pages', function (Blueprint $table) {
                $table->unique('slug', 'pages_slug_unique');
            });
        } catch (\Throwable $e) {
            // índice já existe ou não é necessário — seguir em frente
        }
    }

    public function down(): void
    {
        // remover índice único (se existir)
        try {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropUnique('pages_slug_unique');
            });
        } catch (\Throwable $e) {
            //
        }

        // (não removemos created_at/updated_at no down)
    }
};
