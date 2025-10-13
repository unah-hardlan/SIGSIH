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
        // Add column only if it doesn't already exist (avoids duplicate-column error when multiple migrations add it)
        if (!Schema::hasColumn('tbl_cliente_empresa', 'avatar')) {
            Schema::table('tbl_cliente_empresa', function (Blueprint $table) {
                $table->string('avatar', 255)->nullable()->after('horario_atencion');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop column only if it exists
        if (Schema::hasColumn('tbl_cliente_empresa', 'avatar')) {
            Schema::table('tbl_cliente_empresa', function (Blueprint $table) {
                $table->dropColumn('avatar');
            });
        }
    }
};
