<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Em MySQL/MariaDB, garantir que "id" seja AUTO_INCREMENT e PK.
        try {
            DB::statement("ALTER TABLE pages MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");
        } catch (\Throwable $e) {
            // ignora se já estiver correto
        }

        // (Opcional) garantir PK se necessário – se já existir, o banco vai acusar e seguimos
        try {
            DB::statement("ALTER TABLE pages ADD PRIMARY KEY (id)");
        } catch (\Throwable $e) {
            // já tem PK
        }
    }

    public function down(): void
    {
        // não reverte (evita quebrar inserts antigos)
    }
};
