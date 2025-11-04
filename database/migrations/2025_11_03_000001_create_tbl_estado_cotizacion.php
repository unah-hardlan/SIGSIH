<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tbl_estado_cotizacion')) {
            Schema::create('tbl_estado_cotizacion', function (Blueprint $table) {
                $table->increments('id_estado_cotizacion_pk');
                $table->string('codigo', 50)->unique();
                $table->string('nombre', 100);
                $table->string('descripcion', 255)->nullable();
                $table->boolean('es_final')->default(false);
                $table->smallInteger('orden')->default(0);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_estado_cotizacion');
    }
};
