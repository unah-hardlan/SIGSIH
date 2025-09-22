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
        Schema::table('tbl_kardex', function (Blueprint $table) {
            $table->enum('tipo_movimiento', ['ENTRADA', 'SALIDA', 'AJUSTE'])->after('id_kardex_pk');
            $table->string('origen', 50)->nullable()->after('tipo_movimiento');
            $table->unsignedInteger('id_origen')->nullable()->after('origen');
            $table->index(['id_producto_fk', 'fecha_movimiento'], 'idx_kardex_producto_fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_kardex', function (Blueprint $table) {
            $table->dropIndex('idx_kardex_producto_fecha');
            $table->dropColumn(['tipo_movimiento', 'origen', 'id_origen']);
        });
    }
};
