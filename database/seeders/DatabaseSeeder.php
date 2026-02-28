<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    
    public function run(): void
    {
        $this->call([
            ParametroAuthSeeder::class,
            ParametroSystemSeeder::class,
            CoreObjetosSeeder::class,
            EstadoOrdenServicioSeeder::class,
            EstadoCotizacionSeeder::class,
        ]);
    }
}
