<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parametro;

class ParametroSystemSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $rows = [
            ['parametro' => 'app.timezone', 'valor' => 'UTC'],
            ['parametro' => 'app.date_format', 'valor' => 'Y-m-d'],
            ['parametro' => 'auth.sessions_limit', 'valor' => '1'],
        ];
        foreach ($rows as $r) {
            $param = Parametro::updateOrCreate(
                ['parametro' => $r['parametro']],
                [
                    'valor' => $r['valor'],
                    'id_usuario_fk' => 1,
                    'creado_por' => 'system',
                    'fecha_creacion' => $now,
                    'modificado_por' => 'system',
                    'fecha_modificacion' => $now,
                ]
            );
            // keep creado_por/fecha_creacion intact on existing rows
        }
    }
}
