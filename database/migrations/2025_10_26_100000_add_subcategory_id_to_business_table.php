<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubcategoryIdToBusinessTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('business') && !Schema::hasColumn('business', 'subcategory_id')) {
            Schema::table('business', function (Blueprint $table) {
                // Coluna opcional para não quebrar legado
                $table->unsignedBigInteger('subcategory_id')->nullable()->after('cid');
                $table->index('subcategory_id', 'business_subcategory_id_index');
            });

            // Opcional: só crie FK se tiver certeza que ambas as tabelas/colunas existem e combinam
            if (
                Schema::hasTable('subcategories') &&
                Schema::hasColumn('subcategories', 'id')
            ) {
                // Evita erro em ambientes legados: envolvemos em try/catch
                try {
                    Schema::table('business', function (Blueprint $table) {
                        $table->foreign('subcategory_id', 'business_subcategory_id_fk')
                              ->references('id')
                              ->on('subcategories')
                              ->nullOnDelete();
                    });
                } catch (\Throwable $e) {
                    // Se der erro de engine/colação, seguimos só com o índice.
                }
            }
        }
    }

    public function down()
    {
        if (Schema::hasTable('business') && Schema::hasColumn('business', 'subcategory_id')) {
            // tenta remover FK, se existir
            try {
                Schema::table('business', function (Blueprint $table) {
                    $table->dropForeign('business_subcategory_id_fk');
                });
            } catch (\Throwable $e) {}

            Schema::table('business', function (Blueprint $table) {
                $table->dropIndex('business_subcategory_id_index');
                $table->dropColumn('subcategory_id');
            });
        }
    }
}
