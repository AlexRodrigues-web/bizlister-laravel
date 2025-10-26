<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('business')) {
            Schema::table('business', function (Blueprint $table) {
                if (!Schema::hasColumn('business', 'image_lg')) {
                    $table->string('image_lg')->nullable()->after('image');
                }
                if (!Schema::hasColumn('business', 'image_sm')) {
                    $table->string('image_sm')->nullable()->after('image_lg');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('business')) {
            Schema::table('business', function (Blueprint $table) {
                if (Schema::hasColumn('business', 'image_sm')) {
                    $table->dropColumn('image_sm');
                }
                if (Schema::hasColumn('business', 'image_lg')) {
                    $table->dropColumn('image_lg');
                }
            });
        }
    }
};
