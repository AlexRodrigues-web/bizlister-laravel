<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('biz_id');
                $table->unsignedBigInteger('user_id')->nullable();

                $table->unsignedTinyInteger('rating'); // 1..5
                $table->string('title', 150)->nullable();
                $table->text('body')->nullable();
                $table->boolean('is_approved')->default(false);

                $table->timestamps();

                // FK: business.biz_id
                $table->foreign('biz_id')->references('biz_id')->on('business')->onDelete('cascade');
                // FK: users.id
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

                $table->index(['biz_id', 'is_approved']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};