<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tbl_ms_respaldo_bd')) {
            return;
        }

        Schema::create('tbl_ms_respaldo_bd', function (Blueprint $table) {
            $table->bigIncrements('id_respaldo_bd_pk');
            $table->string('nombre_archivo', 255);
            $table->string('ruta_archivo', 500);
            $table->unsignedBigInteger('tamano_bytes')->nullable();
            $table->char('checksum_sha1', 40)->nullable();
            $table->enum('tipo_respaldo', ['manual', 'automatico'])->default('manual');
            $table->enum('estado_respaldo', ['ACTIVO', 'ELIMINADO'])->default('ACTIVO');
            $table->unsignedBigInteger('id_usuario_fk')->nullable();
            $table->text('observacion')->nullable();
            $table->string('creado_por', 30)->nullable();
            $table->dateTime('fecha_creacion')->nullable();
            $table->string('modificado_por', 30)->nullable();
            $table->dateTime('fecha_modificacion')->nullable();

            $table->index(['estado_respaldo', 'fecha_creacion'], 'idx_respaldo_estado_fecha');
            $table->index('id_usuario_fk', 'idx_respaldo_usuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_ms_respaldo_bd');
    }
};
