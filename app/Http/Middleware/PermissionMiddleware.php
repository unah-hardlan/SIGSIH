<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Objeto;
use App\Models\Permiso;
use App\Models\Usuario;
use App\Services\PermissionService;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: permiso:usuarios,consultar | permiso:usuarios,insercion | permiso:usuarios,actualizacion | permiso:usuarios,eliminacion
     */
    public function handle(Request $request, Closure $next, string $objetoKey, string $accion): Response
    {
        // Allow CORS preflight
        if (strtoupper($request->method()) === 'OPTIONS') {
            return $next($request);
        }
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        // Bypass para Administrador (rol único por FK)
        try {
            if (($user instanceof Usuario) && $user->rol && mb_strtolower($user->rol->rol) === 'administrador') {
                return $next($request);
            }
        } catch (\Throwable $e) {
            // si falla relación, continuar a verificación estándar
        }

        // Resolver objeto por nombre_objeto (case-insensitive)
        $objeto = Objeto::whereRaw('LOWER(nombre_objeto) = ?', [mb_strtolower($objetoKey)])->first();
        if (!$objeto) {
            return response()->json(['error' => 'Objeto no configurado', 'objeto' => $objetoKey], 403);
        }

        // Si accion=auto, derivar por método HTTP
        if ($accion === 'auto') {
            $accion = match (strtoupper($request->method())) {
                'POST' => 'insercion',
                'PUT', 'PATCH' => 'actualizacion',
                'DELETE' => 'eliminacion',
                default => 'consultar',
            };
        }

        // Mapear acción => columna
        $map = [
            'consultar' => 'permiso_consultar',
            'insercion' => 'permiso_insercion',
            'actualizacion' => 'permiso_actualizar',
            'eliminacion' => 'permiso_eliminacion',
        ];
        $col = $map[$accion] ?? null;
        if (!$col) {
            return response()->json(['error' => 'Acción inválida', 'accion' => $accion], 400);
        }

        // Rol del usuario (FK)
        $rolId = ($user instanceof Usuario) ? $user->id_rol_fk : null;
        if (!$rolId) {
            return response()->json(['error' => 'Sin rol asignado'], 403);
        }

        $allowed = Permiso::where('id_objeto_fk', $objeto->id_objetos_pk)
            ->where('id_rol_fk', $rolId)
            ->where($col, true)
            ->exists();

        if (!$allowed) {
            // Permitir acceso a Usuarios si el rol posee Configuración de accesos con la misma acción
            if (mb_strtolower($objetoKey) === 'usuarios') {
                $permService = app(PermissionService::class);
                $altKeys = ['Configuración de accesos', 'Configuracion de accesos', 'Permisos'];
                if ($permService->can($user, $altKeys, $accion)) {
                    return $next($request);
                }
            }

            return response()->json(['error' => 'Permiso denegado', 'objeto' => $objetoKey, 'accion' => $accion], 403);
        }

        return $next($request);
    }
}
