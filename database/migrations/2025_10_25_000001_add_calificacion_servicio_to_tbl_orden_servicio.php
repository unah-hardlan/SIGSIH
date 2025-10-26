<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add a nullable string column to store the service rating: 'excelente','bueno','regular','deficiente'
        if (Schema::hasTable('tbl_orden_servicio')) {
            Schema::table('tbl_orden_servicio', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_orden_servicio', 'calificacion_servicio')) {
                    // Add column without specifying position to avoid issues if 'estado' doesn't exist
                    $table->string('calificacion_servicio', 20)->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('tbl_orden_servicio')) {
            Schema::table('tbl_orden_servicio', function (Blueprint $table) {
                if (Schema::hasColumn('tbl_orden_servicio', 'calificacion_servicio')) {
                    $table->dropColumn('calificacion_servicio');
                }
            });
        }
    }
};
