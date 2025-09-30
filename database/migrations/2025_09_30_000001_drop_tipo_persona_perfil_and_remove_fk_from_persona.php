<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Remove FKs and columns from tbl_persona
        if (Schema::hasTable('tbl_persona')) {
            // Drop FKs by discovering their names (handles non-standard names)
            $constraints = DB::select(
                "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_persona' AND COLUMN_NAME IN ('id_tipo_persona_fk','id_perfil_fk') AND REFERENCED_TABLE_NAME IS NOT NULL"
            );
            foreach ($constraints as $row) {
                $name = $row->CONSTRAINT_NAME ?? null;
                if ($name) {
                    try { DB::statement("ALTER TABLE `tbl_persona` DROP FOREIGN KEY `{$name}`"); } catch (\Throwable $e) {}
                }
            }

            Schema::table('tbl_persona', function (Blueprint $table) {
                if (Schema::hasColumn('tbl_persona', 'id_tipo_persona_fk')) {
                    $table->dropColumn('id_tipo_persona_fk');
                }
                if (Schema::hasColumn('tbl_persona', 'id_perfil_fk')) {
                    $table->dropColumn('id_perfil_fk');
                }
            });
        }

        // Drop tables if exist
        if (Schema::hasTable('tbl_tipo_persona')) {
            Schema::drop('tbl_tipo_persona');
        }
        if (Schema::hasTable('tbl_perfil')) {
            Schema::drop('tbl_perfil');
        }
    }

    public function down(): void
    {
        // Recreate tables (minimal), and columns back in tbl_persona for rollback purposes
        if (!Schema::hasTable('tbl_tipo_persona')) {
            Schema::create('tbl_tipo_persona', function (Blueprint $table) {
                $table->increments('id_tipo_persona_pk');
                $table->string('nombre_tipo_persona', 100);
                $table->string('descripcion', 255)->nullable();
            });
        }
        if (!Schema::hasTable('tbl_perfil')) {
            Schema::create('tbl_perfil', function (Blueprint $table) {
                $table->increments('id_perfil_pk');
                $table->string('nombre_perfil', 100);
                $table->string('descripcion_perfil', 255)->nullable();
            });
        }
        if (Schema::hasTable('tbl_persona')) {
            Schema::table('tbl_persona', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_persona', 'id_tipo_persona_fk')) {
                    $table->unsignedInteger('id_tipo_persona_fk')->nullable();
                }
                if (!Schema::hasColumn('tbl_persona', 'id_perfil_fk')) {
                    $table->unsignedInteger('id_perfil_fk')->nullable();
                }
            });
            // Add back FKs (nullable for safety)
            Schema::table('tbl_persona', function (Blueprint $table) {
                try { $table->foreign('id_tipo_persona_fk')->references('id_tipo_persona_pk')->on('tbl_tipo_persona')->nullOnDelete(); } catch (\Throwable $e) {}
                try { $table->foreign('id_perfil_fk')->references('id_perfil_pk')->on('tbl_perfil')->nullOnDelete(); } catch (\Throwable $e) {}
            });
        }
    }
};
