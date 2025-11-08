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
    
    public function handle(Request $request, Closure $next, string $objetoKey, string $accion): Response
    {
        
        if (strtoupper($request->method()) === 'OPTIONS') {
            return $next($request);
        }
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        
        try {
            if (($user instanceof Usuario) && $user->rol && mb_strtolower($user->rol->rol) === 'administrador') {
                return $next($request);
            }
        } catch (\Throwable $e) {
            
        }

        
        $objeto = Objeto::whereRaw('LOWER(nombre_objeto) = ?', [mb_strtolower($objetoKey)])->first();
        if (!$objeto) {
            return response()->json(['error' => 'Objeto no configurado', 'objeto' => $objetoKey], 403);
        }

        
        if ($accion === 'auto') {
            $accion = match (strtoupper($request->method())) {
                'POST' => 'insercion',
                'PUT', 'PATCH' => 'actualizacion',
                'DELETE' => 'eliminacion',
                default => 'consultar',
            };
        }

        
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

        
        $rolId = ($user instanceof Usuario) ? $user->id_rol_fk : null;
        if (!$rolId) {
            return response()->json(['error' => 'Sin rol asignado'], 403);
        }

        $allowed = Permiso::where('id_objeto_fk', $objeto->id_objetos_pk)
            ->where('id_rol_fk', $rolId)
            ->where($col, true)
            ->exists();

        if (!$allowed) {
            
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
