<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->increments('cat_id');
                $table->string('name', 190);
                $table->string('slug', 190)->unique();
                $table->text('description')->nullable();
                $table->unsignedInteger('parent_id')->nullable()->index();
                $table->unsignedTinyInteger('is_active')->default(1)->index();
                $table->timestamps();
                $table->index('name');
            });
        } else {
            Schema::table('categories', function (Blueprint $table) {
                if (!Schema::hasColumn('categories','cat_id')) {
                    $table->increments('cat_id');
                }
                if (!Schema::hasColumn('categories','name')) {
                    $table->string('name', 190);
                }
                if (!Schema::hasColumn('categories','slug')) {
                    $table->string('slug', 190)->unique();
                }
                if (!Schema::hasColumn('categories','description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('categories','parent_id')) {
                    $table->unsignedInteger('parent_id')->nullable()->index();
                }
                if (!Schema::hasColumn('categories','is_active')) {
                    $table->unsignedTinyInteger('is_active')->default(1)->index();
                }
                if (!Schema::hasColumn('categories','created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('categories','updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};