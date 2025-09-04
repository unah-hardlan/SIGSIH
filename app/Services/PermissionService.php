<?php

namespace App\Services;

use App\Models\Objeto;
use App\Models\Permiso;
use App\Models\Usuario;

class PermissionService
{
    /**
     * Check if the given user can perform an action on any of the provided objeto names.
     *
     * @param mixed $user Authenticated user model (expects App\Models\Usuario)
     * @param string|array $objetoKeys One or more objeto names to try (case-insensitive)
     * @param string $accion One of consultar|insercion|actualizacion|eliminacion
     */
    public function can($user, $objetoKeys, string $accion): bool
    {
        if (!$user instanceof Usuario) return false;
        $rolId = $user->id_rol_fk ?? null;
        if (!$rolId) return false;

        $keys = is_array($objetoKeys) ? $objetoKeys : [$objetoKeys];
        $keys = array_values(array_filter(array_unique(array_map(function ($k) {
            return trim((string) $k);
        }, $keys))));
        if (empty($keys)) return false;

        $map = [
            'consultar' => 'permiso_consultar',
            'insercion' => 'permiso_insercion',
            'actualizacion' => 'permiso_actualizar',
            'eliminacion' => 'permiso_eliminacion',
        ];
        $col = $map[$accion] ?? null;
        if (!$col) return false;

        foreach ($keys as $name) {
            $obj = Objeto::whereRaw('LOWER(nombre_objeto) = ?', [mb_strtolower($name)])->first();
            if (!$obj) continue;
            $ok = Permiso::where('id_objeto_fk', $obj->id_objetos_pk)
                ->where('id_rol_fk', $rolId)
                ->where($col, true)
                ->exists();
            if ($ok) return true;
        }

        return false;
    }
}
