<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoCotizacionSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            ['codigo' => 'borrador', 'nombre' => 'Borrador', 'descripcion' => 'Cotización en edición', 'orden' => 1, 'es_final' => 0],
            ['codigo' => 'enviada', 'nombre' => 'Enviada', 'descripcion' => 'Cotización enviada al cliente', 'orden' => 2, 'es_final' => 0],
            ['codigo' => 'aprobada', 'nombre' => 'Aprobada', 'descripcion' => 'Cotización aprobada por el cliente', 'orden' => 3, 'es_final' => 1],
            ['codigo' => 'rechazada', 'nombre' => 'Rechazada', 'descripcion' => 'Cotización rechazada por el cliente', 'orden' => 4, 'es_final' => 1],
            ['codigo' => 'vencida', 'nombre' => 'Vencida', 'descripcion' => 'Cotización vencida por fecha límite', 'orden' => 5, 'es_final' => 1],
        ];

        foreach ($estados as $e) {
            $exists = DB::table('tbl_estado_cotizacion')->where('codigo', $e['codigo'])->exists();
            if ($exists) continue;
            DB::table('tbl_estado_cotizacion')->insert($e);
        }
    }
}
