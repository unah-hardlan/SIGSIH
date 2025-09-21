<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('tbl_empresa_cliente')) return;
        Schema::table('tbl_empresa_cliente', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_empresa_cliente', 'estado_empresa')) {
                $table->string('estado_empresa', 30)->default('ACTIVO')->after('correo_electronico');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('tbl_empresa_cliente')) return;
        Schema::table('tbl_empresa_cliente', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_empresa_cliente', 'estado_empresa')) {
                $table->dropColumn('estado_empresa');
            }
        });
    }
};
