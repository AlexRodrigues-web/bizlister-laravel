<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessImagesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('business_images')) {
            Schema::create('business_images', function (Blueprint $table) {
                $table->id();
                // legado usa business.biz_id (PK não-padrão)
                $table->unsignedBigInteger('business_id');
                $table->string('path', 1024);     // ex.: businesses/{biz}/gallery/xxx.jpg
                $table->boolean('is_primary')->default(false);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                // índice útil para ordenações
                $table->index(['business_id', 'is_primary', 'sort_order']);

                // Se quiser FK (não obrigatório pois PK é 'biz_id' não padrão):
                // $table->foreign('business_id')->references('biz_id')->on('business')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('business_images');
    }
}
