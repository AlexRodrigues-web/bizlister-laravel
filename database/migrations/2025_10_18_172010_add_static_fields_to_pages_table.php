<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (!Schema::hasColumn('pages', 'slug')) {
                $table->string('slug')->unique()->after('id');
            }
            if (!Schema::hasColumn('pages', 'title')) {
                $table->string('title')->after('slug');
            }
            if (!Schema::hasColumn('pages', 'content')) {
                $table->longText('content')->nullable()->after('title');
            }
            if (!Schema::hasColumn('pages', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('content');
            }
            if (!Schema::hasColumn('pages', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'slug')) {
                try { $table->dropUnique('pages_slug_unique'); } catch (\Throwable $e) {}
                $table->dropColumn('slug');
            }
            foreach (['title','content','is_active','published_at'] as $col) {
                if (Schema::hasColumn('pages', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
