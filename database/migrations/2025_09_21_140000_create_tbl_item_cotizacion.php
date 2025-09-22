<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Crear tabla tbl_item_cotizacion que falta en la base de datos
        if (!Schema::hasTable('tbl_item_cotizacion')) {
            Schema::create('tbl_item_cotizacion', function (Blueprint $table) {
                $table->integer('id_item_cotizacion_pk')->autoIncrement();
                $table->string('descripcion', 255);
                $table->decimal('precio_unitario', 12, 2);
                $table->decimal('cantidad', 8, 2)->default(1);
                $table->decimal('impuesto', 12, 2)->default(0);
                $table->decimal('total', 12, 2);
                $table->integer('id_cotizacion_fk'); // Cambiar a int para coincidir con tbl_cotizacion
                
                // Índices
                $table->index('id_cotizacion_fk');
                
                // Foreign key
                $table->foreign('id_cotizacion_fk')
                      ->references('id_cotizacion_pk')
                      ->on('tbl_cotizacion')
                      ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_item_cotizacion');
    }
};