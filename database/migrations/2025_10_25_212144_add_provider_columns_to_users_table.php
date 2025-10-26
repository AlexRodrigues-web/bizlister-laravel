<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProviderColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Observação: o schema legado usa 'user_id' como PK; não alteramos isso.

            // Provedor (google/facebook) e ID do provedor
            if (!Schema::hasColumn('users', 'provider')) {
                // perto do password para ficar lógico no esquema
                $table->string('provider', 32)->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'provider_id')) {
                // 191 para compatibilidade com utf8mb4 e índices
                $table->string('provider_id', 191)->nullable()->after('provider');
            }

            // Tokens (opcional guardar – úteis para refresh ou re-sync de avatar)
            if (!Schema::hasColumn('users', 'provider_token')) {
                $table->text('provider_token')->nullable()->after('provider_id');
            }
            if (!Schema::hasColumn('users', 'provider_refresh_token')) {
                $table->text('provider_refresh_token')->nullable()->after('provider_token');
            }

            // URL da foto de perfil remota (mantém 'avatar' legado intacto)
            if (!Schema::hasColumn('users', 'profile_photo_url')) {
                // usar after('avatar') se existir, senão after('about') se existir
                try {
                    $table->string('profile_photo_url', 512)->nullable()->after('avatar');
                } catch (\Throwable $e) {
                    // fallback se 'avatar' não existir na sua cópia
                    $table->string('profile_photo_url', 512)->nullable();
                }
            }

            // Índice auxiliar para buscas por provedor (não é unique; permite vincular vários provedores futuramente)
            // A migration roda uma vez, então podemos criar direto.
            $table->index(['provider', 'provider_id'], 'users_provider_provider_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Remover índice (ignora erro se já não existir)
            try {
                $table->dropIndex('users_provider_provider_id_index');
            } catch (\Throwable $e) {}

            // Dropar colunas de forma segura (uma a uma, evitando exigir doctrine/dbal)
            foreach ([
                'profile_photo_url',
                'provider_refresh_token',
                'provider_token',
                'provider_id',
                'provider',
            ] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    try {
                        $table->dropColumn($col);
                    } catch (\Throwable $e) {
                        // silencioso: alguns bancos exigem DBAL para múltiplas operações
                    }
                }
            }
        });
    }
}
