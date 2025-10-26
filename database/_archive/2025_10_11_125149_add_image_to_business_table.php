<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('business','image')) {
            Schema::table('business', function (Blueprint $table) {
$table->string('image')->nullable()->after('description');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('business','image')) {
            Schema::table('business', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }
};
