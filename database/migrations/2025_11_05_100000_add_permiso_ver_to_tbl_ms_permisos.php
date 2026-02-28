<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_ms_permisos', function (Blueprint $table) {
            $table->boolean('permiso_ver')->nullable()->after('permiso_consultar');
        });
        try {
            DB::table('tbl_ms_permisos')->update(['permiso_ver' => DB::raw('permiso_consultar')]);
        } catch (\Throwable $e) {
        }
    }

    public function down(): void
    {
        Schema::table('tbl_ms_permisos', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_ms_permisos', 'permiso_ver')) {
                $table->dropColumn('permiso_ver');
            }
        });
    }
};