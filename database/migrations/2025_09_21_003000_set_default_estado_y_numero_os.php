<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('tbl_orden_servicio')) return;

        // Ensure 'creada' estado exists and get its id
        $estadoCreada = DB::table('tbl_estado_orden_servicio')
            ->where('codigo', 'creada')
            ->first();

        if (!$estadoCreada) {
            $id = DB::table('tbl_estado_orden_servicio')->insertGetId([
                'codigo' => 'creada',
                'nombre' => 'Creada',
                'descripcion' => 'Orden creada',
                'es_final' => 0,
                'orden' => 1,
            ]);
            $estadoCreadaId = $id;
        } else {
            $estadoCreadaId = $estadoCreada->id_estado_orden_servicio_pk;
        }

        // Backfill id_estado_orden_servicio_fk where null
        if (Schema::hasColumn('tbl_orden_servicio', 'id_estado_orden_servicio_fk')) {
            DB::statement("UPDATE tbl_orden_servicio SET id_estado_orden_servicio_fk = " . (int)$estadoCreadaId . " WHERE id_estado_orden_servicio_fk IS NULL");
        }

        // Backfill fecha_creada if needed (safety) using fecha_recepcion or NOW()
        if (Schema::hasColumn('tbl_orden_servicio', 'fecha_creada')) {
            DB::statement("UPDATE tbl_orden_servicio SET fecha_creada = COALESCE(fecha_creada, fecha_recepcion, NOW())");
        }

        // Generate numero_orden_servicio if missing with pattern OS-YYYYMM-######
        if (Schema::hasColumn('tbl_orden_servicio', 'numero_orden_servicio')) {
            DB::statement("UPDATE tbl_orden_servicio SET numero_orden_servicio = CONCAT('OS-', DATE_FORMAT(COALESCE(fecha_creada, NOW()), '%Y%m'), '-', LPAD(id_orden_servicio_pk, 6, '0')) WHERE numero_orden_servicio IS NULL OR numero_orden_servicio = ''");
        }
    }

    public function down(): void
    {
        // No destructive rollback: leave data as-is
    }
};
