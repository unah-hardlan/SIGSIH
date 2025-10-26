<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('tbl_orden_servicio', 'repuestos')) {
            Schema::table('tbl_orden_servicio', function (Blueprint $table) {
                // Guardar una lista JSON de repuestos usados (id, nombre, cantidad)
                // Usamos json para mayor flexibilidad; es nullable para retrocompatibilidad
                $table->json('repuestos')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tbl_orden_servicio', 'repuestos')) {
            Schema::table('tbl_orden_servicio', function (Blueprint $table) {
                $table->dropColumn('repuestos');
            });
        }
    }
};
