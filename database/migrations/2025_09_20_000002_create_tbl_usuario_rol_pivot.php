<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('tbl_usuario_rol')) {
            // Detect column types from information_schema to avoid FK type mismatch
            $dbName = DB::getDatabaseName();
            $userColType = DB::selectOne("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'tbl_ms_usuario' AND COLUMN_NAME = 'id_usuario_pk' LIMIT 1", [$dbName]);
            $rolColType = DB::selectOne("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'tbl_ms_rol' AND COLUMN_NAME = 'id_rol_pk' LIMIT 1", [$dbName]);
            $userIsBig = isset($userColType->DATA_TYPE) && strtolower($userColType->DATA_TYPE) === 'bigint';
            $rolIsBig = isset($rolColType->DATA_TYPE) && strtolower($rolColType->DATA_TYPE) === 'bigint';

            Schema::create('tbl_usuario_rol', function (Blueprint $table) use ($userIsBig, $rolIsBig) {
                if ($userIsBig) {
                    $table->unsignedBigInteger('id_usuario_fk');
                } else {
                    $table->unsignedInteger('id_usuario_fk');
                }
                if ($rolIsBig) {
                    $table->unsignedBigInteger('id_rol_fk');
                } else {
                    $table->unsignedInteger('id_rol_fk');
                }
                $table->primary(['id_usuario_fk', 'id_rol_fk'], 'pk_tbl_usuario_rol');
            });

            // Try to add foreign keys; ignore if incompatible
            try {
                Schema::table('tbl_usuario_rol', function (Blueprint $table) {
                    $table->foreign('id_usuario_fk', 'fk_usuario_rol_usuario')
                        ->references('id_usuario_pk')->on('tbl_ms_usuario')->onDelete('cascade');
                });
            } catch (\Throwable $e) {
            }
            try {
                Schema::table('tbl_usuario_rol', function (Blueprint $table) {
                    $table->foreign('id_rol_fk', 'fk_usuario_rol_rol')
                        ->references('id_rol_pk')->on('tbl_ms_rol')->onDelete('cascade');
                });
            } catch (\Throwable $e) {
            }
        }

        if (Schema::hasTable('tbl_ms_usuario') && Schema::hasColumn('tbl_ms_usuario', 'id_rol_fk')) {
            try {
                DB::statement("INSERT INTO tbl_usuario_rol (id_usuario_fk, id_rol_fk)\n                    SELECT id_usuario_pk, id_rol_fk FROM tbl_ms_usuario WHERE id_rol_fk IS NOT NULL");
            } catch (\Throwable $e) {
                // ignore backfill errors (duplicates etc.)
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_usuario_rol');
    }
};
