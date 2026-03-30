<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('tbl_ms_usuario', 'pendiente_cambio_contrasena')) {
            Schema::table('tbl_ms_usuario', function (Blueprint $table) {
                $table->tinyInteger('pendiente_cambio_contrasena')->default(0)->after('primer_ingreso');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tbl_ms_usuario', 'pendiente_cambio_contrasena')) {
            Schema::table('tbl_ms_usuario', function (Blueprint $table) {
                $table->dropColumn('pendiente_cambio_contrasena');
            });
        }
    }
};
