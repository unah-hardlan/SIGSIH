<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('tbl_rol_permiso')) {
            $dbName = DB::getDatabaseName();
            $rolCol = DB::selectOne("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME='tbl_ms_rol' AND COLUMN_NAME='id_rol_pk' LIMIT 1", [$dbName]);
            $permCol = DB::selectOne("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME='tbl_ms_permisos' AND COLUMN_NAME='id_permiso_pk' LIMIT 1", [$dbName]);
            $rolIsBig = isset($rolCol->DATA_TYPE) && strtolower($rolCol->DATA_TYPE) === 'bigint';
            $permIsBig = isset($permCol->DATA_TYPE) && strtolower($permCol->DATA_TYPE) === 'bigint';

            Schema::create('tbl_rol_permiso', function (Blueprint $table) use ($rolIsBig, $permIsBig) {
                if ($rolIsBig) {
                    $table->unsignedBigInteger('id_rol_fk');
                } else {
                    $table->unsignedInteger('id_rol_fk');
                }
                if ($permIsBig) {
                    $table->unsignedBigInteger('id_permiso_fk');
                } else {
                    $table->unsignedInteger('id_permiso_fk');
                }
                $table->primary(['id_rol_fk', 'id_permiso_fk'], 'pk_tbl_rol_permiso');
            });

            try {
                Schema::table('tbl_rol_permiso', function (Blueprint $table) {
                    $table->foreign('id_rol_fk', 'fk_rol_permiso_rol')
                        ->references('id_rol_pk')->on('tbl_ms_rol')->onDelete('cascade');
                });
            } catch (\Throwable $e) {
            }
            try {
                Schema::table('tbl_rol_permiso', function (Blueprint $table) {
                    $table->foreign('id_permiso_fk', 'fk_rol_permiso_permiso')
                        ->references('id_permiso_pk')->on('tbl_ms_permisos')->onDelete('cascade');
                });
            } catch (\Throwable $e) {
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_rol_permiso');
    }
};
