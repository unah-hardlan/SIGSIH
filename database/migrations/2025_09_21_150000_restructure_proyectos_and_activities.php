<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Crear tabla de actividades del proyecto (plan estructurado)
        Schema::create('tbl_proyecto_actividad', function (Blueprint $table) {
            $table->integer('id_proyecto_actividad_pk')->autoIncrement();
            $table->integer('id_proyecto_fk');
            $table->string('nombre_actividad', 200);
            $table->text('descripcion_actividad')->nullable();
            $table->date('fecha_inicio_actividad')->nullable();
            $table->date('fecha_fin_actividad')->nullable();
            $table->enum('estado_actividad', ['PENDIENTE', 'EN_PROGRESO', 'COMPLETADA', 'PAUSADA', 'CANCELADA'])->default('PENDIENTE');
            $table->integer('orden')->default(1); // Para secuencia de actividades
            $table->integer('id_responsable_fk')->nullable(); // Técnico responsable
            $table->decimal('progreso_porcentaje', 5, 2)->default(0); // 0-100%
            $table->text('observaciones')->nullable();
            $table->datetime('fecha_creacion')->default(DB::raw('CURRENT_TIMESTAMP'));
            
            // Índices
            $table->index('id_proyecto_fk');
            $table->index('id_responsable_fk');
            $table->index(['id_proyecto_fk', 'orden']); // Para ordenar actividades
            
            // Foreign keys
            $table->foreign('id_proyecto_fk')
                  ->references('id_proyecto_pk')
                  ->on('tbl_proyectos')
                  ->onDelete('cascade');
                  
            $table->foreign('id_responsable_fk')
                  ->references('id_persona_pk')
                  ->on('tbl_persona')
                  ->onDelete('set null');
        });

        // 2. Migrar actividades existentes del campo texto a tabla estructurada
        $proyectos = DB::table('tbl_proyectos')->whereNotNull('actividades_proyecto')->get();
        
        foreach ($proyectos as $proyecto) {
            if (!empty($proyecto->actividades_proyecto)) {
                // Dividir actividades por líneas o punto y coma
                $actividades = preg_split('/[\n\r;]+/', $proyecto->actividades_proyecto);
                $orden = 1;
                
                foreach ($actividades as $actividad) {
                    $actividad = trim($actividad);
                    if (!empty($actividad)) {
                        DB::table('tbl_proyecto_actividad')->insert([
                            'id_proyecto_fk' => $proyecto->id_proyecto_pk,
                            'nombre_actividad' => substr($actividad, 0, 200),
                            'descripcion_actividad' => strlen($actividad) > 200 ? $actividad : null,
                            'orden' => $orden++,
                            'fecha_creacion' => now()
                        ]);
                    }
                }
            }
        }

        // 3. Agregar columnas alias más simples para las fechas (mantener compatibilidad)
        Schema::table('tbl_proyectos', function (Blueprint $table) {
            // Agregar aliases más simples manteniendo los campos originales
            DB::statement("ALTER TABLE tbl_proyectos ADD COLUMN fecha_inicio DATE GENERATED ALWAYS AS (fecha_inicio_proyecto) VIRTUAL");
            DB::statement("ALTER TABLE tbl_proyectos ADD COLUMN fecha_estimada_fin DATE GENERATED ALWAYS AS (fecha_estimada_fin_proyecto) VIRTUAL");
            DB::statement("ALTER TABLE tbl_proyectos ADD COLUMN fecha_fin DATE GENERATED ALWAYS AS (fecha_finalizacion_proyecto) VIRTUAL");
        });

        // 4. Remover campo actividades_proyecto después de migrar
        Schema::table('tbl_proyectos', function (Blueprint $table) {
            $table->dropColumn('actividades_proyecto');
        });
    }

    public function down(): void
    {
        // Restaurar campo actividades_proyecto
        Schema::table('tbl_proyectos', function (Blueprint $table) {
            $table->string('actividades_proyecto', 500)->nullable();
        });

        // Remover columnas virtuales
        DB::statement("ALTER TABLE tbl_proyectos DROP COLUMN IF EXISTS fecha_inicio");
        DB::statement("ALTER TABLE tbl_proyectos DROP COLUMN IF EXISTS fecha_estimada_fin");
        DB::statement("ALTER TABLE tbl_proyectos DROP COLUMN IF EXISTS fecha_fin");

        // Migrar actividades de vuelta a texto (opcional, para rollback)
        $proyectos = DB::table('tbl_proyectos')->get();
        
        foreach ($proyectos as $proyecto) {
            $actividades = DB::table('tbl_proyecto_actividad')
                          ->where('id_proyecto_fk', $proyecto->id_proyecto_pk)
                          ->orderBy('orden')
                          ->pluck('nombre_actividad')
                          ->toArray();
                          
            if (!empty($actividades)) {
                DB::table('tbl_proyectos')
                  ->where('id_proyecto_pk', $proyecto->id_proyecto_pk)
                  ->update(['actividades_proyecto' => implode('; ', $actividades)]);
            }
        }

        Schema::dropIfExists('tbl_proyecto_actividad');
    }
};