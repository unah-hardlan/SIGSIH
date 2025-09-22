<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('tbl_origen')) {
            Schema::create('tbl_origen', function (Blueprint $table) {
                $table->bigIncrements('id_origen_pk');
                $table->string('nombre_origen', 50)->unique();
                $table->string('descripcion_origen', 255)->nullable();
                $table->boolean('activo')->default(true);
            });
        } else {
            // Ensure required columns exist
            Schema::table('tbl_origen', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_origen', 'nombre_origen')) {
                    $table->string('nombre_origen', 50)->unique();
                }
                if (!Schema::hasColumn('tbl_origen', 'descripcion_origen')) {
                    $table->string('descripcion_origen', 255)->nullable();
                }
                if (!Schema::hasColumn('tbl_origen', 'activo')) {
                    $table->boolean('activo')->default(true);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_origen');
    }
};
