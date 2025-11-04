<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_cotizacion', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_cotizacion', 'id_estado_cotizacion_fk')) {
                $table->unsignedInteger('id_estado_cotizacion_fk')->nullable()->after('anticipo_requerido');
                $table->index('id_estado_cotizacion_fk', 'fk_cotizacion_estado');
            }
        });

        // Add FK constraint in a separate statement to avoid issues if table was altered previously
        try {
            Schema::table('tbl_cotizacion', function (Blueprint $table) {
                // Defensive drop if exists with same name to avoid duplicate name errors across environments
                // Note: Laravel doesn't provide 'dropForeignIfExists', so we rely on unique name guard
                $table->foreign('id_estado_cotizacion_fk', 'fk_cotizacion_estado')
                    ->references('id_estado_cotizacion_pk')
                    ->on('tbl_estado_cotizacion')
                    ->onUpdate('cascade')
                    ->onDelete('restrict');
            });
        } catch (\Throwable $e) {
            // Some DBs might require a different FK name or pre-existing index — ignore if already added
        }
    }

    public function down(): void
    {
        Schema::table('tbl_cotizacion', function (Blueprint $table) {
            try {
                $table->dropForeign('fk_cotizacion_estado');
            } catch (\Throwable $e) {
            }
            try {
                $table->dropIndex('fk_cotizacion_estado');
            } catch (\Throwable $e) {
            }
            if (Schema::hasColumn('tbl_cotizacion', 'id_estado_cotizacion_fk')) {
                $table->dropColumn('id_estado_cotizacion_fk');
            }
        });
    }
};
