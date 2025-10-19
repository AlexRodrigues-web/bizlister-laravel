<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('reviews')) {
            return;
        }

        // 1) Primeiro garante as chaves (sem AFTER)
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'biz_id')) {
                $table->unsignedBigInteger('biz_id')->nullable();
            }
            if (!Schema::hasColumn('reviews', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }
        });

        // 2) Depois os campos de review (sem AFTER)
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'rating')) {
                $table->unsignedTinyInteger('rating');
            }
            if (!Schema::hasColumn('reviews', 'title')) {
                $table->string('title', 150)->nullable();
            }
            if (!Schema::hasColumn('reviews', 'body')) {
                $table->text('body')->nullable();
            }
            if (!Schema::hasColumn('reviews', 'is_approved')) {
                $table->boolean('is_approved')->default(false);
            }
        });

        // Índices e FKs ficam para uma migration futura, para evitar colisões em ambientes legados
    }

    public function down(): void
    {
        // Intencionalmente vazio para não perder dados
    }
};