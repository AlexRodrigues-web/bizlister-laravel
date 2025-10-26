<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubcategoriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // ✅ Guard: se a tabela já existe, não tenta recriar
        if (Schema::hasTable('subcategories')) {
            return;
        }

        // Detecta o esquema atual de "categories"
        $hasId    = Schema::hasTable('categories') && Schema::hasColumn('categories', 'id');
        $hasCatId = Schema::hasTable('categories') && Schema::hasColumn('categories', 'cat_id');

        if ($hasId) {
            // ===== ESQUEMA MODERNO (categories.id) =====
            Schema::create('subcategories', function (Blueprint $table) {
                $table->id();

                $table->foreignId('category_id')
                      ->constrained('categories') // ->references('id')
                      ->cascadeOnDelete();

                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();

                $table->timestamps();

                $table->unique(['category_id', 'name']);
            });
        } elseif ($hasCatId) {
            // ===== ESQUEMA LEGADO (categories.cat_id) =====
            Schema::create('subcategories', function (Blueprint $table) {
                $table->id();

                // Usa inteiro sem sinal para casar com cat_id legado
                $table->unsignedInteger('category_id');

                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();

                $table->timestamps();

                $table->unique(['category_id', 'name']);
            });

            // Adiciona a FK explicitamente para cat_id
            Schema::table('subcategories', function (Blueprint $table) {
                $table->foreign('category_id')
                      ->references('cat_id')
                      ->on('categories')
                      ->onDelete('cascade');
            });
        } else {
            // ===== Sem tabela "categories" acessível — cria sem FK para não travar =====
            Schema::create('subcategories', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('category_id')->nullable(); // sem constraint

                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();

                $table->timestamps();

                $table->unique(['category_id', 'name']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('subcategories');
    }
}
