<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // tbl_objetos: drop index and columns if exist
        if (Schema::hasTable('tbl_objetos')) {
            Schema::table('tbl_objetos', function (Blueprint $table) {
                // Drop index safely if present
                try {
                    DB::statement('DROP INDEX idx_tbl_objetos_clave_permiso ON tbl_objetos');
                } catch (\Throwable $e) {
                }
                if (Schema::hasColumn('tbl_objetos', 'clave_permiso')) {
                    $table->dropColumn('clave_permiso');
                }
                if (Schema::hasColumn('tbl_objetos', 'ruta')) {
                    $table->dropColumn('ruta');
                }
            });
        }

        // tbl_tipo_objetos: drop index and column if exist
        if (Schema::hasTable('tbl_tipo_objetos')) {
            Schema::table('tbl_tipo_objetos', function (Blueprint $table) {
                try {
                    DB::statement('DROP INDEX idx_tbl_tipo_objetos_clave_permiso ON tbl_tipo_objetos');
                } catch (\Throwable $e) {
                }
                if (Schema::hasColumn('tbl_tipo_objetos', 'clave_permiso')) {
                    $table->dropColumn('clave_permiso');
                }
            });
        }
    }

    public function down(): void
    {
        // Re-add columns (nullable) and indexes
        if (Schema::hasTable('tbl_objetos')) {
            Schema::table('tbl_objetos', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_objetos', 'ruta')) {
                    $table->string('ruta', 255)->nullable()->after('descripcion_objeto');
                }
            });
            Schema::table('tbl_objetos', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_objetos', 'clave_permiso')) {
                    $table->string('clave_permiso', 128)->nullable()->after('ruta');
                    DB::statement('ALTER TABLE tbl_objetos ADD INDEX IF NOT EXISTS idx_tbl_objetos_clave_permiso (clave_permiso)');
                }
            });
        }

        if (Schema::hasTable('tbl_tipo_objetos')) {
            Schema::table('tbl_tipo_objetos', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_tipo_objetos', 'clave_permiso')) {
                    $table->string('clave_permiso', 128)->nullable()->after('descripcion_tipo_objeto');
                    DB::statement('ALTER TABLE tbl_tipo_objetos ADD INDEX IF NOT EXISTS idx_tbl_tipo_objetos_clave_permiso (clave_permiso)');
                }
            });
        }
    }
};
