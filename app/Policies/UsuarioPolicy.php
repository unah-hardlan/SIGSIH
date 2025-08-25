<?php

namespace App\Policies;

use App\Models\Usuario;

class UsuarioPolicy
{
    // Cualquiera autenticado puede listar/ver
    public function viewAny(Usuario $user): bool { return true; }
    public function view(Usuario $user, Usuario $model): bool { return true; }

    // Crear/actualizar/eliminar restringido a no-Técnico
    public function create(Usuario $user): bool
    {
    $nombreRol = optional($user->rol)->rol;
    return !($nombreRol && in_array(mb_strtolower($nombreRol), ['técnico','tecnico']));
    }

    public function update(Usuario $user, Usuario $model): bool
    {
    $nombreRol = optional($user->rol)->rol;
    return !($nombreRol && in_array(mb_strtolower($nombreRol), ['técnico','tecnico']));
    }

    public function delete(Usuario $user, Usuario $model): bool
    {
    $nombreRol = optional($user->rol)->rol;
    return !($nombreRol && in_array(mb_strtolower($nombreRol), ['técnico','tecnico']));
    }
}
