<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoOrdenServicioSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            ['codigo' => 'creada', 'nombre' => 'Creada', 'descripcion' => 'Orden creada', 'orden' => 1, 'es_final' => 0],
            ['codigo' => 'asignada', 'nombre' => 'Asignada', 'descripcion' => 'Técnico asignado', 'orden' => 2, 'es_final' => 0],
            ['codigo' => 'en_progreso', 'nombre' => 'En progreso', 'descripcion' => 'Trabajo en ejecución', 'orden' => 3, 'es_final' => 0],
            ['codigo' => 'finalizada', 'nombre' => 'Finalizada', 'descripcion' => 'Trabajo finalizado', 'orden' => 4, 'es_final' => 1],
            ['codigo' => 'cancelada', 'nombre' => 'Cancelada', 'descripcion' => 'Orden cancelada', 'orden' => 5, 'es_final' => 1],
        ];

        foreach ($estados as $e) {
            $exists = DB::table('tbl_estado_orden_servicio')->where('codigo', $e['codigo'])->exists();
            if ($exists) continue;
            DB::table('tbl_estado_orden_servicio')->insert($e);
        }
    }
}
