<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $getRef = function (string $table, string $pk) {
            return DB::selectOne(
                "SELECT DATA_TYPE, COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?",
                [$table, $pk]
            );
        };

        $getType = function ($row): array {
            if (!$row) return ['INT', ''];
            $dataType = strtolower($row->DATA_TYPE ?? 'int');
            $colType = strtolower($row->COLUMN_TYPE ?? 'int');
            $type = str_contains($dataType, 'bigint') ? 'BIGINT' : 'INT';
            $unsigned = str_contains($colType, 'unsigned') ? ' UNSIGNED' : '';
            return [$type, $unsigned];
        };

        // Create tbl_asiento
        if (!Schema::hasTable('tbl_asiento')) {
            DB::statement("CREATE TABLE `tbl_asiento` (
                `id_asiento_pk` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `fecha_asiento` DATETIME NOT NULL,
                `descripcion` VARCHAR(255) NULL,
                `referencia` VARCHAR(100) NULL,
                `estado` VARCHAR(20) NOT NULL DEFAULT 'borrador',
                PRIMARY KEY (`id_asiento_pk`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        // Create tbl_asiento_detalle
        if (!Schema::hasTable('tbl_asiento_detalle')) {
            $ref = $getRef('tbl_asiento', 'id_asiento_pk');
            [$t, $u] = $getType($ref);
            DB::statement("CREATE TABLE `tbl_asiento_detalle` (
                `id_asiento_detalle_pk` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_asiento_fk` {$t}{$u} NOT NULL,
                `cuenta` VARCHAR(50) NOT NULL,
                `descripcion` VARCHAR(255) NULL,
                `debe` DECIMAL(15,2) NOT NULL DEFAULT 0,
                `haber` DECIMAL(15,2) NOT NULL DEFAULT 0,
                PRIMARY KEY (`id_asiento_detalle_pk`),
                INDEX `idx_asiento` (`id_asiento_fk`),
                CONSTRAINT `fk_det_asiento` FOREIGN KEY (`id_asiento_fk`) REFERENCES `tbl_asiento`(`id_asiento_pk`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        // Create tbl_movimiento_financiero
        if (!Schema::hasTable('tbl_movimiento_financiero')) {
            $catRef = $getRef('tbl_categorias', 'id_categoria_pk');
            [$ct, $cu] = $getType($catRef);

            $osRef = $getRef('tbl_orden_servicio', 'id_orden_servicio_pk');
            [$ot, $ou] = $getType($osRef);

            $asRef = $getRef('tbl_asiento', 'id_asiento_pk');
            [$at, $au] = $getType($asRef);

            DB::statement("CREATE TABLE `tbl_movimiento_financiero` (
                `id_movimiento_financiero_pk` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `tipo_movimiento` VARCHAR(10) NOT NULL,
                `fecha_movimiento` DATETIME NOT NULL,
                `monto` DECIMAL(15,2) NOT NULL,
                `descripcion` VARCHAR(255) NULL,
                `id_categoria_fk` {$ct}{$cu} NOT NULL,
                `atribuible_a_proyecto` TINYINT(1) NOT NULL DEFAULT 0,
                `id_orden_servicio_fk` {$ot}{$ou} NULL,
                `id_asiento_fk` {$at}{$au} NULL,
                PRIMARY KEY (`id_movimiento_financiero_pk`),
                INDEX `idx_cat` (`id_categoria_fk`),
                INDEX `idx_os` (`id_orden_servicio_fk`),
                INDEX `idx_asiento_fk` (`id_asiento_fk`),
                CONSTRAINT `fk_mov_cat` FOREIGN KEY (`id_categoria_fk`) REFERENCES `tbl_categorias`(`id_categoria_pk`) ON UPDATE CASCADE ON DELETE RESTRICT,
                CONSTRAINT `fk_mov_os` FOREIGN KEY (`id_orden_servicio_fk`) REFERENCES `tbl_orden_servicio`(`id_orden_servicio_pk`) ON UPDATE CASCADE ON DELETE SET NULL,
                CONSTRAINT `fk_mov_asiento` FOREIGN KEY (`id_asiento_fk`) REFERENCES `tbl_asiento`(`id_asiento_pk`) ON UPDATE CASCADE ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        // Unique normalization for categorias (tipo, nombre)
        if (Schema::hasTable('tbl_categorias')) {
            $exists = DB::selectOne("SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_categorias' AND INDEX_NAME = 'ux_cat_tipo_nombre'");
            if (!$exists) {
                DB::statement("ALTER TABLE `tbl_categorias` ADD UNIQUE KEY `ux_cat_tipo_nombre` (`tipo_categoria`, `nombre_categoria`)");
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tbl_movimiento_financiero')) {
            DB::statement("DROP TABLE `tbl_movimiento_financiero`");
        }
        if (Schema::hasTable('tbl_asiento_detalle')) {
            DB::statement("DROP TABLE `tbl_asiento_detalle`");
        }
        if (Schema::hasTable('tbl_asiento')) {
            DB::statement("DROP TABLE `tbl_asiento`");
        }
        // Keep unique index on categorias in place intentionally
    }
};
