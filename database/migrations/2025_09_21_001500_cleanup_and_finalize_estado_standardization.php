<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private array $tables = [
        'tbl_estado_cai' => [
            'name_cols' => ['nombre_estado_cai','nombre_estado','nombre'],
            'desc_cols' => ['descripcion_estado_cai','descripcion']
        ],
        'tbl_estado_calendario' => [
            'name_cols' => ['nombre_estado','nombre'],
            'desc_cols' => ['descripcion_estado_calendario','descripcion']
        ],
        'tbl_estado_factura' => [
            'name_cols' => ['nombre_estado','nombre'],
            'desc_cols' => ['descripcion_estado_factura','descripcion']
        ],
        'tbl_estado_proyecto' => [
            'name_cols' => ['nombre_estado','nombre'],
            'desc_cols' => ['descripcion_estado_proyecto','descripcion']
        ],
        'tbl_estado_solicitud' => [
            'name_cols' => ['nombre_estado','nombre'],
            'desc_cols' => ['descripcion_estado','descripcion']
        ],
        'tbl_estado_ticket' => [
            'name_cols' => ['nombre_estado','nombre'],
            'desc_cols' => ['descripcion_estado_ticket','descripcion']
        ],
    ];

    public function up(): void
    {
        foreach ($this->tables as $table => $cols) {
            if (!Schema::hasTable($table)) continue;
            Schema::table($table, function (Blueprint $t) use ($table) {
                if (!Schema::hasColumn($table, 'descripcion')) {
                    $t->string('descripcion', 255)->nullable()->after('nombre');
                }
            });

            // Backfill descripcion from first available legacy description column
            $descLegacy = null;
            foreach (($cols['desc_cols'] ?? []) as $dc) {
                if (Schema::hasColumn($table, $dc)) { $descLegacy = $dc; break; }
            }
            if ($descLegacy) {
                DB::statement("UPDATE {$table} SET descripcion = COALESCE(descripcion, {$descLegacy})");
            }

            // Drop legacy name/description columns except standardized 'nombre' + 'descripcion'
            Schema::table($table, function (Blueprint $t) use ($table, $cols) {
                foreach (($cols['name_cols'] ?? []) as $nc) {
                    if (in_array($nc, ['nombre'])) continue;
                    if (Schema::hasColumn($table, $nc)) {
                        $t->dropColumn($nc);
                    }
                }
                foreach (($cols['desc_cols'] ?? []) as $dc) {
                    if (in_array($dc, ['descripcion'])) continue;
                    if (Schema::hasColumn($table, $dc)) {
                        $t->dropColumn($dc);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        // Best-effort restore: re-add legacy columns (nullable) without data repopulation
        foreach ($this->tables as $table => $cols) {
            if (!Schema::hasTable($table)) continue;
            Schema::table($table, function (Blueprint $t) use ($table, $cols) {
                foreach (($cols['name_cols'] ?? []) as $nc) {
                    if ($nc !== 'nombre' && !Schema::hasColumn($table, $nc)) {
                        $t->string($nc, 100)->nullable();
                    }
                }
                foreach (($cols['desc_cols'] ?? []) as $dc) {
                    if ($dc !== 'descripcion' && !Schema::hasColumn($table, $dc)) {
                        $t->string($dc, 255)->nullable();
                    }
                }
                if (Schema::hasColumn($table, 'descripcion')) {
                    // keep descripcion; do not remove on down to avoid data loss
                }
            });
        }
    }
};
