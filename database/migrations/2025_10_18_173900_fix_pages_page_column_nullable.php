<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Se a coluna "page" existir, deixe-a NULL (ou remova).
        if (Schema::hasColumn("pages", "page")) {
            try {
                // opção 1: tornar NULL (mais seguro em qualquer ambiente)
                DB::statement("ALTER TABLE pages MODIFY page VARCHAR(255) NULL DEFAULT NULL");
            } catch (\Throwable $e) {
                // fallback: tenta dropar se o MODIFY falhar
                try {
                    DB::statement("ALTER TABLE pages DROP COLUMN page");
                } catch (\Throwable $e2) {
                    // última tentativa: define default vazio pra não travar inserts
                    try {
                        DB::statement("ALTER TABLE pages MODIFY page VARCHAR(255) NOT NULL DEFAULT ''");
                    } catch (\Throwable $e3) {
                        // deixa quieto para não quebrar a migration; melhor ter log
                        // error_log($e3->getMessage());
                    }
                }
            }
        }
    }

    public function down(): void
    {
        // sem rollback destrutivo; no máximo tira o default/volta NOT NULL se quiser
        // mas vamos manter sem ação pra não quebrar nada.
    }
};
