<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_cotizacion', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_cotizacion', 'impuesto_otros')) {
                $table->decimal('impuesto_otros', 12, 2)->default(0)->after('otros_cargos');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_cotizacion', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_cotizacion', 'impuesto_otros')) {
                $table->dropColumn('impuesto_otros');
            }
        });
    }
};
