<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private function addIndexIfMissing(string $table, string $indexName, string $columns): void
    {
        if (!Schema::hasTable($table)) return;
        $exists = DB::selectOne(
            "SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?",
            [$table, $indexName]
        );
        if (!$exists) {
            DB::statement("ALTER TABLE `{$table}` ADD INDEX `{$indexName}` ({$columns})");
        }
    }

    public function up(): void
    {
        // Index common FK columns (id_*_fk) across known tables
        $fkIndexes = [
            ['tbl_cotizacion', 'idx_cot_cliente', 'id_cliente_fk'],
            ['tbl_cotizacion', 'idx_cot_os', 'id_orden_servicio_fk'],
            ['tbl_factura', 'idx_fact_cot', 'id_cotizacion_fk'],
            ['tbl_detalle_factura', 'idx_det_fact_fact', 'id_factura_fk'],
            ['tbl_detalle_factura', 'idx_det_fact_serv', 'id_servicio_fk'],
            ['tbl_detalle_orden_producto', 'idx_det_op_os', 'id_orden_servicio_fk'],
            ['tbl_detalle_orden_producto', 'idx_det_op_prod', 'id_producto_fk'],
            ['tbl_movimiento_financiero', 'idx_mov_cat', 'id_categoria_fk'],
            ['tbl_movimiento_financiero', 'idx_mov_os', 'id_orden_servicio_fk'],
            ['tbl_movimiento_financiero', 'idx_mov_asiento', 'id_asiento_fk'],
            ['tbl_asiento_detalle', 'idx_det_asiento_asiento', 'id_asiento_fk'],
            ['tbl_calendario', 'idx_cal_agencia', 'id_agencias_fk'],
            ['tbl_calendario', 'idx_cal_estado', 'id_estado_calendario_fk'],
            ['tbl_calendario', 'idx_cal_os', 'id_orden_servicio_fk'],
            ['tbl_calendario', 'idx_cal_tipo_mant', 'id_tipo_mantenimiento_fk'],
            ['tbl_calendario', 'idx_cal_cliente', 'id_cliente_fk'],
            ['tbl_calendario', 'idx_cal_tecnico', 'id_usuario_fk'],
            ['tbl_direccion', 'idx_dir_ciudad', 'id_ciudad_fk'],
            ['tbl_ciudad', 'idx_ciu_dep', 'id_departamento_fk'],
            ['tbl_departamento', 'idx_dep_pais', 'id_pais_fk'],
        ];
        foreach ($fkIndexes as [$table, $idx, $cols]) {
            $this->addIndexIfMissing($table, $idx, $cols);
        }

        // Compound indexes for frequent queries
        $this->addIndexIfMissing('tbl_detalle_factura', 'idx_det_fact_fact_serv', 'id_factura_fk, id_servicio_fk');
        $this->addIndexIfMissing('tbl_detalle_orden_producto', 'idx_det_op_os_prod', 'id_orden_servicio_fk, id_producto_fk');
        // Kardex already has idx_kardex_producto_fecha
    }

    public function down(): void
    {
        $dropIndexes = [
            ['tbl_cotizacion', 'idx_cot_cliente'],
            ['tbl_cotizacion', 'idx_cot_os'],
            ['tbl_factura', 'idx_fact_cot'],
            ['tbl_detalle_factura', 'idx_det_fact_fact'],
            ['tbl_detalle_factura', 'idx_det_fact_serv'],
            ['tbl_detalle_factura', 'idx_det_fact_fact_serv'],
            ['tbl_detalle_orden_producto', 'idx_det_op_os'],
            ['tbl_detalle_orden_producto', 'idx_det_op_prod'],
            ['tbl_detalle_orden_producto', 'idx_det_op_os_prod'],
            ['tbl_movimiento_financiero', 'idx_mov_cat'],
            ['tbl_movimiento_financiero', 'idx_mov_os'],
            ['tbl_movimiento_financiero', 'idx_mov_asiento'],
            ['tbl_asiento_detalle', 'idx_det_asiento_asiento'],
            ['tbl_calendario', 'idx_cal_agencia'],
            ['tbl_calendario', 'idx_cal_estado'],
            ['tbl_calendario', 'idx_cal_os'],
            ['tbl_calendario', 'idx_cal_tipo_mant'],
            ['tbl_calendario', 'idx_cal_cliente'],
            ['tbl_calendario', 'idx_cal_tecnico'],
            ['tbl_direccion', 'idx_dir_ciudad'],
            ['tbl_ciudad', 'idx_ciu_dep'],
            ['tbl_departamento', 'idx_dep_pais'],
        ];
        foreach ($dropIndexes as [$table, $idx]) {
            if (!Schema::hasTable($table)) continue;
            $exists = DB::selectOne(
                "SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?",
                [$table, $idx]
            );
            if ($exists) {
                DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$idx}`");
            }
        }
    }
};
