<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parametro;

class ParametroAuthSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['parametro' => 'ADMIN_INTENTOS_INICIO SESION', 'valor' => '3'],
            ['parametro' => 'ADMIN_CORREO', 'valor' => 'correo@dominio.com'],
            ['parametro' => 'ADMIN_CUSER', 'valor' => 'USUARIO'],
            ['parametro' => 'ADMIN_CPASS', 'valor' => 'PASSWORD'],
        ];
        foreach ($defaults as $d) {
            Parametro::updateOrCreate(
                ['parametro' => $d['parametro']],
                ['valor' => $d['valor'], 'id_usuario_fk' => 1, 'creado_por' => 'system', 'fecha_creacion' => now()]
            );
        }
    }
}
