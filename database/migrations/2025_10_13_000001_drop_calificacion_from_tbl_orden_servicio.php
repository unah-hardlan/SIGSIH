<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Drop any FK that references the column
        try {
            $constraints = DB::select(<<<SQL
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'tbl_orden_servicio'
                  AND COLUMN_NAME = 'id_calificacion_servicio_fk'
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            SQL);
            foreach ($constraints as $row) {
                $name = $row->CONSTRAINT_NAME ?? null;
                if ($name) {
                    try { DB::statement("ALTER TABLE `tbl_orden_servicio` DROP FOREIGN KEY `{$name}`"); } catch (\Throwable $e) {}
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // 2) Drop any index with the known name
        try { DB::statement('ALTER TABLE `tbl_orden_servicio` DROP INDEX `fk_orden_servicio_calificacion`'); } catch (\Throwable $e) {}

        // 3) Finally drop the column if it exists
        if (Schema::hasColumn('tbl_orden_servicio', 'id_calificacion_servicio_fk')) {
            Schema::table('tbl_orden_servicio', function (Blueprint $table) {
                $table->dropColumn('id_calificacion_servicio_fk');
            });
        }
    }

    public function down(): void
    {
        // Re-create column (nullable) without enforcing the FK to keep rollback simple
        Schema::table('tbl_orden_servicio', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_orden_servicio', 'id_calificacion_servicio_fk')) {
                $table->unsignedBigInteger('id_calificacion_servicio_fk')->nullable()->after('diagnostico_cliente');
            }
        });
        // Optionally restore index
        try { DB::statement('ALTER TABLE `tbl_orden_servicio` ADD INDEX `fk_orden_servicio_calificacion` (`id_calificacion_servicio_fk`)'); } catch (\Throwable $e) {}
        // Optionally restore FK if table exists
        try {
            DB::statement('ALTER TABLE `tbl_orden_servicio` ADD CONSTRAINT `fk_orden_servicio_calificacion` FOREIGN KEY (`id_calificacion_servicio_fk`) REFERENCES `tbl_calificacion_servicio`(`id_calificacion_servicio_pk`) ON DELETE SET NULL ON UPDATE CASCADE');
        } catch (\Throwable $e) {}
    }
};
