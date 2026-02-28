<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl_solicitud', function (Blueprint $table) {
            // Nullable to keep backward compatibility; reasonable length 150
            if (!Schema::hasColumn('tbl_solicitud', 'nombre_solicitud')) {
                $table->string('nombre_solicitud', 150)->nullable()->after('id_cliente_fk');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_solicitud', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_solicitud', 'nombre_solicitud')) {
                $table->dropColumn('nombre_solicitud');
            }
        });
    }
};
