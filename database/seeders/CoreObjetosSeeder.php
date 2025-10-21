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
            // Nuevos objetos solicitados
            ['nombre_objeto' => 'Mantenimiento del sistema', 'descripcion_objeto' => 'Operaciones de mantenimiento del sistema'],
            ['nombre_objeto' => 'Gestión de personas', 'descripcion_objeto' => 'Módulos para administrar personas y sus catálogos'],
            ['nombre_objeto' => 'Gestión de base de datos', 'descripcion_objeto' => 'Herramientas de gestión de base de datos'],
            ['nombre_objeto' => 'Origen Kardex', 'descripcion_objeto' => 'Catálogo de orígenes para movimientos de Kardex'],
            ['nombre_objeto' => 'Tipo de Mantenimiento', 'descripcion_objeto' => 'Catálogo de tipos de mantenimiento'],
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

        // Otorgar todos los permisos al rol Administrador para el nuevo objeto si existe
        try {
            $adminRolId = DB::table('tbl_ms_rol')->whereRaw("LOWER(rol) = 'administrador'")->value('id_rol_pk');
            if ($adminRolId) {
                $objNames = ['Origen Kardex', 'Tipo de Mantenimiento'];
                $objIds = DB::table('tbl_objetos')->whereIn('nombre_objeto', $objNames)->pluck('id_objetos_pk', 'nombre_objeto');
                foreach ($objIds as $objId) {
                    $exists = DB::table('tbl_ms_permisos')->where(['id_rol_fk' => $adminRolId, 'id_objeto_fk' => $objId])->exists();
                    if (!$exists) {
                        DB::table('tbl_ms_permisos')->insert([
                            'id_rol_fk' => $adminRolId,
                            'id_objeto_fk' => $objId,
                            'permiso_insercion' => 1,
                            'permiso_consultar' => 1,
                            'permiso_actualizar' => 1,
                            'permiso_eliminacion' => 1,
                            'creado_por' => $usuario,
                            'fecha_creacion' => $now,
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
        }
    }
}
