<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbl_empresa_cliente', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_empresa_cliente', 'estado_empresa')) {
                $table->string('estado_empresa', 20)->default('activo')->after('id_oficina_fk');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_empresa_cliente', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_empresa_cliente', 'estado_empresa')) {
                $table->dropColumn('estado_empresa');
            }
        });
    }
};
