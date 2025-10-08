<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable("business") && Schema::hasColumn("business","address_1")) {
            // Usa SQL direto para não depender de DBAL
            DB::statement("ALTER TABLE `business` MODIFY `address_1` VARCHAR(255) NULL DEFAULT NULL");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable("business") && Schema::hasColumn("business","address_1")) {
            // Reverte para NOT NULL sem default (ajuste se quiser outro default)
            DB::statement("ALTER TABLE `business` MODIFY `address_1` VARCHAR(255) NOT NULL");
        }
    }
};