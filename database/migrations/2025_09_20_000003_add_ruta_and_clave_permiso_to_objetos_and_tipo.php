<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Note: current tables are tbl_objetos and tbl_tipo_objetos per models
        if (Schema::hasTable('tbl_objetos')) {
            Schema::table('tbl_objetos', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_objetos', 'ruta')) {
                    $table->string('ruta', 255)->nullable()->after('descripcion_objeto');
                }
                if (!Schema::hasColumn('tbl_objetos', 'clave_permiso')) {
                    $table->string('clave_permiso', 128)->nullable()->after('ruta');
                    $table->index('clave_permiso', 'idx_tbl_objetos_clave_permiso');
                }
            });
        }

        if (Schema::hasTable('tbl_tipo_objetos')) {
            Schema::table('tbl_tipo_objetos', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_tipo_objetos', 'clave_permiso')) {
                    $table->string('clave_permiso', 128)->nullable()->after('descripcion_tipo_objeto');
                    $table->index('clave_permiso', 'idx_tbl_tipo_objetos_clave_permiso');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tbl_objetos')) {
            Schema::table('tbl_objetos', function (Blueprint $table) {
                if (Schema::hasColumn('tbl_objetos', 'clave_permiso')) {
                    $table->dropIndex('idx_tbl_objetos_clave_permiso');
                    $table->dropColumn('clave_permiso');
                }
                if (Schema::hasColumn('tbl_objetos', 'ruta')) {
                    $table->dropColumn('ruta');
                }
            });
        }
        if (Schema::hasTable('tbl_tipo_objetos')) {
            Schema::table('tbl_tipo_objetos', function (Blueprint $table) {
                if (Schema::hasColumn('tbl_tipo_objetos', 'clave_permiso')) {
                    $table->dropIndex('idx_tbl_tipo_objetos_clave_permiso');
                    $table->dropColumn('clave_permiso');
                }
            });
        }
    }
};
