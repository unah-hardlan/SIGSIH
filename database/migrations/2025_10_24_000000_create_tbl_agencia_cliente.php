<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblAgenciaCliente extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ensure any leftover partial table is removed before creating
        Schema::dropIfExists('tbl_agencia_cliente');

        Schema::create('tbl_agencia_cliente', function (Blueprint $table) {
            // use signed integer to match existing primary keys in the database
            $table->integer('id_agencia_fk');
            $table->integer('id_cliente_fk');

            $table->primary(['id_agencia_fk', 'id_cliente_fk']);

            $table->foreign('id_agencia_fk', 'fk_agencia_cliente_agencia')
                  ->references('id_agencias_pk')->on('tbl_agencias')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('id_cliente_fk', 'fk_agencia_cliente_cliente')
                  ->references('id_cliente_pk')->on('tbl_cliente')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_agencia_cliente', function (Blueprint $table) {
            $table->dropForeign('fk_agencia_cliente_agencia');
            $table->dropForeign('fk_agencia_cliente_cliente');
        });
        Schema::dropIfExists('tbl_agencia_cliente');
    }
}
