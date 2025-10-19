<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImagePathsToBusinessesTable extends Migration
{
    /**
     * Detecta a tabela real (business ou businesses).
     */
    protected function realTable(): ?string
    {
        if (Schema::hasTable('business'))   return 'business';
        if (Schema::hasTable('businesses')) return 'businesses';
        return null;
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->realTable();
        if (!$table) {
            // Não existe tabela alvo; não faz nada para evitar erro em ambientes legados.
            return;
        }

        Schema::table($table, function (Blueprint $table) {
            // Colunas esperadas pelo checker
            if (!Schema::hasColumn($table->getTable(), 'image_path_lg')) {
                $table->string('image_path_lg', 255)->nullable()->after('image');
            }
            if (!Schema::hasColumn($table->getTable(), 'image_path_sm')) {
                $table->string('image_path_sm', 255)->nullable()->after('image_path_lg');
            }

            // Colunas usadas pelo controller (compat)
            if (!Schema::hasColumn($table->getTable(), 'image_lg')) {
                $table->string('image_lg', 255)->nullable()->after('image_path_sm');
            }
            if (!Schema::hasColumn($table->getTable(), 'image_sm')) {
                $table->string('image_sm', 255)->nullable()->after('image_lg');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table = $this->realTable();
        if (!$table) return;

        Schema::table($table, function (Blueprint $table) {
            // Remoção segura (só se existirem)
            if (Schema::hasColumn($table->getTable(), 'image_path_lg')) {
                $table->dropColumn('image_path_lg');
            }
            if (Schema::hasColumn($table->getTable(), 'image_path_sm')) {
                $table->dropColumn('image_path_sm');
            }
            if (Schema::hasColumn($table->getTable(), 'image_lg')) {
                $table->dropColumn('image_lg');
            }
            if (Schema::hasColumn($table->getTable(), 'image_sm')) {
                $table->dropColumn('image_sm');
            }
        });
    }
}
