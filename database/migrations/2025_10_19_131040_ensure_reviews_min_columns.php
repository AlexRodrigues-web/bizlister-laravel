<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                // chaves primeiro
                if (!Schema::hasColumn('reviews', 'biz_id')) {
                    $table->unsignedBigInteger('biz_id');
                }
                if (!Schema::hasColumn('reviews', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable();
                }
                // dados da review
                if (!Schema::hasColumn('reviews', 'rating')) {
                    $table->unsignedTinyInteger('rating'); // 1..5
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
                // timestamps se faltarem
                if (!Schema::hasColumn('reviews', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('reviews', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        // sem remoções para não perder dados
    }
};