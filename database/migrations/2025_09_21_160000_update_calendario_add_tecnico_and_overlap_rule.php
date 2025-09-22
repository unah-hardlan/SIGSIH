<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('tbl_calendario') || !Schema::hasTable('tbl_ms_usuario')) {
            return;
        }

        $ref = DB::selectOne(
            "SELECT DATA_TYPE, COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_ms_usuario' AND COLUMN_NAME = 'id_usuario_pk'"
        );
        $dataType = strtolower($ref->DATA_TYPE ?? 'int');
        $colType = strtolower($ref->COLUMN_TYPE ?? 'int');
        $type = str_contains($dataType, 'bigint') ? 'BIGINT' : 'INT';
        $unsigned = str_contains($colType, 'unsigned') ? ' UNSIGNED' : '';

        if (!Schema::hasColumn('tbl_calendario', 'id_usuario_fk')) {
            DB::statement("ALTER TABLE `tbl_calendario` ADD `id_usuario_fk` {$type}{$unsigned} NULL AFTER `id_agencias_fk`");
        }

        // Add FK constraint if missing
        $fkRow = DB::selectOne("SELECT 1 FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_calendario' AND COLUMN_NAME = 'id_usuario_fk' AND REFERENCED_TABLE_NAME = 'tbl_ms_usuario'");
        if (!$fkRow) {
            // Drop leftover FK name if exists
            $refCons = DB::selectOne("SELECT CONSTRAINT_NAME FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_calendario' AND CONSTRAINT_NAME = 'fk_cal_usuario'");
            if ($refCons) {
                DB::statement("ALTER TABLE `tbl_calendario` DROP FOREIGN KEY `fk_cal_usuario`");
            }
            DB::statement("ALTER TABLE `tbl_calendario` ADD CONSTRAINT `fk_cal_usuario` FOREIGN KEY (`id_usuario_fk`) REFERENCES `tbl_ms_usuario`(`id_usuario_pk`) ON UPDATE CASCADE ON DELETE SET NULL");
        }

        // Unique index to prevent same technician at the same datetime
        $idx = DB::selectOne("SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_calendario' AND INDEX_NAME = 'ux_cal_tecnico_fecha'");
        if (!$idx) {
            DB::statement("ALTER TABLE `tbl_calendario` ADD UNIQUE KEY `ux_cal_tecnico_fecha` (`id_usuario_fk`,`fecha`)");
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('tbl_calendario')) return;
        // Drop unique index
        $idx = DB::selectOne("SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_calendario' AND INDEX_NAME = 'ux_cal_tecnico_fecha'");
        if ($idx) {
            DB::statement("ALTER TABLE `tbl_calendario` DROP INDEX `ux_cal_tecnico_fecha`");
        }
        // Drop FK and column
        $refCons = DB::selectOne("SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_calendario' AND CONSTRAINT_NAME = 'fk_cal_usuario'");
        if ($refCons) {
            DB::statement("ALTER TABLE `tbl_calendario` DROP FOREIGN KEY `fk_cal_usuario`");
        }
        if (Schema::hasColumn('tbl_calendario', 'id_usuario_fk')) {
            DB::statement("ALTER TABLE `tbl_calendario` DROP COLUMN `id_usuario_fk`");
        }
    }
};
