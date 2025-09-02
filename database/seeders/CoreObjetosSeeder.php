<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoreObjetosSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $usuario = 'system';
        // Resolve a sensible tipo de objeto (Configuración) as default; fallback to the first available ID
        $tipoId = DB::table('tbl_tipo_objetos')
            ->whereRaw("LOWER(nombre_tipo_objeto) LIKE 'configur%'")
            ->value('id_tipo_objeto_pk');
        if (!$tipoId) {
            $tipoId = DB::table('tbl_tipo_objetos')->orderBy('id_tipo_objeto_pk')->value('id_tipo_objeto_pk');
        }
        $objetos = [
            ['nombre_objeto' => 'Login', 'descripcion_objeto' => 'Autenticación de usuarios'],
            ['nombre_objeto' => 'Usuarios', 'descripcion_objeto' => 'Gestión de usuarios'],
            ['nombre_objeto' => 'Roles', 'descripcion_objeto' => 'Gestión de roles'],
            ['nombre_objeto' => 'Permisos', 'descripcion_objeto' => 'Gestión de permisos'],
            ['nombre_objeto' => 'Perfil', 'descripcion_objeto' => 'Gestión de perfil de usuario'],
            ['nombre_objeto' => 'Parámetros', 'descripcion_objeto' => 'Parámetros del sistema'],
            ['nombre_objeto' => 'Objetos', 'descripcion_objeto' => 'Catálogo de objetos'],
            ['nombre_objeto' => 'Bitácora', 'descripcion_objeto' => 'Registro de eventos'],
        ];

        foreach ($objetos as $obj) {
            $exists = DB::table('tbl_objetos')->where('nombre_objeto', $obj['nombre_objeto'])->exists();
            if ($exists) continue;
            DB::table('tbl_objetos')->insert([
                'nombre_objeto' => $obj['nombre_objeto'],
                'descripcion_objeto' => $obj['descripcion_objeto'],
                'id_tipo_objetos_fk' => $tipoId,
                'creado_por' => $usuario,
                'fecha_creacion' => $now,
            ]);
        }
    }
}
