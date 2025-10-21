<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('business')) {
            Schema::create('business', function (Blueprint $table) {
                // Legado usa biz_id como PK
                $table->increments('biz_id');
                $table->string('business_name');

                // Campos comumente referenciados no código/rotas
                $table->string('image')->nullable();
                $table->string('image_lg')->nullable();
                $table->string('image_sm')->nullable();

                // Campos de endereço/contato (deixa tudo nullable para compatibilidade)
                $table->string('address1')->nullable();
                $table->string('address2')->nullable();
                $table->unsignedInteger('sid')->nullable(); // vínculo com city_id (legacy)
                $table->string('city')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('website')->nullable();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('business');
    }
};
