<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private array $tables = [
        'tbl_estado_cai' => ['name_cols' => ['nombre_estado_cai','nombre_estado','nombre']],
        'tbl_estado_calendario' => ['name_cols' => ['nombre_estado','nombre']],
        'tbl_estado_factura' => ['name_cols' => ['nombre_estado','nombre']],
        'tbl_estado_proyecto' => ['name_cols' => ['nombre_estado','nombre']],
        'tbl_estado_solicitud' => ['name_cols' => ['nombre_estado','nombre']],
        'tbl_estado_ticket' => ['name_cols' => ['nombre_estado','nombre']],
    ];

    public function up(): void
    {
        foreach ($this->tables as $table => $meta) {
            if (!Schema::hasTable($table)) continue;
            Schema::table($table, function (Blueprint $t) use ($table) {
                if (!Schema::hasColumn($table, 'codigo')) {
                    $t->string('codigo', 50)->nullable();
                }
                if (!Schema::hasColumn($table, 'nombre')) {
                    $t->string('nombre', 100)->nullable();
                }
                if (!Schema::hasColumn($table, 'es_final')) {
                    $t->boolean('es_final')->default(false);
                }
                if (!Schema::hasColumn($table, 'orden')) {
                    $t->unsignedSmallInteger('orden')->default(0);
                }
            });

            // Backfill 'nombre' from the first existing legacy column
            $legacyCols = $meta['name_cols'] ?? [];
            $legacy = null;
            foreach ($legacyCols as $col) {
                if (Schema::hasColumn($table, $col)) { $legacy = $col; break; }
            }
            if ($legacy) {
                DB::statement("UPDATE {$table} SET nombre = COALESCE(nombre, {$legacy})");
            }

            // Generate a simple codigo if null using a slug of nombre (lowercase, hyphens)
            // MySQL expression to slugify basic spaces to hyphens and lowercase
            DB::statement("UPDATE {$table} SET codigo = COALESCE(codigo, LOWER(REPLACE(TRIM(nombre),' ', '-'))) WHERE nombre IS NOT NULL");

            // Add indexes (ignore if already exist)
            Schema::table($table, function (Blueprint $t) use ($table) {
                try { $t->index('orden', "idx_{$table}_orden"); } catch (\Throwable $e) {}
                try { $t->unique('codigo', "uniq_{$table}_codigo"); } catch (\Throwable $e) {}
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table => $_) {
            if (!Schema::hasTable($table)) continue;
            Schema::table($table, function (Blueprint $t) use ($table) {
                try { $t->dropUnique("uniq_{$table}_codigo"); } catch (\Throwable $e) {}
                try { $t->dropIndex("idx_{$table}_orden"); } catch (\Throwable $e) {}
                if (Schema::hasColumn($table, 'orden')) { $t->dropColumn('orden'); }
                if (Schema::hasColumn($table, 'es_final')) { $t->dropColumn('es_final'); }
                if (Schema::hasColumn($table, 'nombre')) { $t->dropColumn('nombre'); }
                if (Schema::hasColumn($table, 'codigo')) { $t->dropColumn('codigo'); }
            });
        }
    }
};
