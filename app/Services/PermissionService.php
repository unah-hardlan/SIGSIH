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
        // Roles: considerar rol principal + roles del pivote (si existen)
        $roleIds = [];
        if ($user->id_rol_fk) $roleIds[] = (int) $user->id_rol_fk;
        try {
            $pivotRoles = method_exists($user, 'roles') ? $user->roles()->pluck('id_rol_pk')->all() : [];
            foreach ($pivotRoles as $rid) {
                $roleIds[] = (int) $rid;
            }
        } catch (\Throwable $e) {
        }
        $roleIds = array_values(array_unique(array_filter($roleIds)));
        if (empty($roleIds)) return false;

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
            'ver' => 'permiso_ver',
        ];
        $col = $map[$accion] ?? null;
        if (!$col) return false;

        foreach ($keys as $name) {
            $ln = mb_strtolower($name);
            $obj = Objeto::whereRaw('LOWER(nombre_objeto) = ?', [$ln])->first();
            if (!$obj) continue;
            $ok = Permiso::where('id_objeto_fk', $obj->id_objetos_pk)
                ->whereIn('id_rol_fk', $roleIds)
                ->where($col, true)
                ->exists();
            // A partir de la introducción de permiso_ver explícito,
            // no realizar fallback a permiso_consultar para la acción 'ver'.
            if ($ok) return true;
        }

        return false;
    }

    /** Convenience wrapper to check by a single objeto name. */
    public function canKey($user, string $clave, string $accion = 'consultar'): bool
    {
        return $this->can($user, [$clave], $accion);
    }
}
