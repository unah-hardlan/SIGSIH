<?php

namespace App\Http\Middleware;

use App\Models\Usuario;
use App\Services\PermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutoPermissionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (strtoupper($request->method()) === 'OPTIONS') {
            return $next($request);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        // Allow some endpoints without permisos (self and dashboard datasets)
        if ($request->is('api/me') || $request->is('api/login') || $request->is('api/logout') || $request->is('api/register') || $request->is('api/dashboard/*')) {
            return $next($request);
        }

        // Admin bypass by rol name
        try {
            if (($user instanceof Usuario) && $user->rol && mb_strtolower($user->rol->rol) === 'administrador') {
                return $next($request);
            }
        } catch (\Throwable $e) {}

        // Allow listing roles/objetos/tipos-objeto if user can view "Permisos" (to cargar matriz de permisos)
        if (in_array(strtoupper($request->method()), ['GET','HEAD'], true)) {
            $path = trim($request->path(), '/');
            if (preg_match('#^api/(roles|objetos|tipos-objeto)(/.*)?$#i', $path)) {
                $perm = app(PermissionService::class);
                if ($perm->can($user, ['Permisos','Configuración de accesos','Configuracion de accesos'], 'consultar')) {
                    return $next($request);
                }
                // fall-through to standard check sobre el mismo recurso
            }
        }

        // Infer candidates for objeto name from controller and path
        $route = $request->route();
        $controller = $route ? ($route->getActionName() ?? '') : '';
        $controllerBase = class_basename(is_string($controller) ? explode('@', (string) $controller)[0] : (string) $controller);
        $controllerBase = str_ends_with($controllerBase, 'Controller') ? substr($controllerBase, 0, -10) : $controllerBase;
        $synonyms = [
            'Auth' => ['Login'],
            'Usuario' => ['Usuarios','Usuario'],
            'Rol' => ['Roles','Rol'],
            'Permiso' => ['Permisos','Permiso','Configuración de accesos','Configuracion de accesos'],
            'Parametro' => ['Parámetros','Parametros','Parámetro','Parametro'],
            'Objeto' => ['Objetos','Objeto'],
            'Bitacora' => ['Bitácora','Bitacora'],
            'Profile' => ['Profile','Perfil'],
            'Perfil' => ['Perfil','Profile'],
            'Dashboard' => ['Dashboard'],
            // Nuevos
            'MantenimientoGeneral' => ['Mantenimiento del sistema','Mantenimiento'],
            'GestionPersonas' => ['Gestión de personas','Gestion de personas'],
            'GestionDb' => ['Gestión de base de datos','Gestion de base de datos'],
        ];
        $candidates = $synonyms[$controllerBase] ?? [];
        $first = explode('/', trim($request->path(), '/'))[1] ?? '';
        if ($first) $candidates[] = ucfirst($first);

        $method = strtoupper($request->method());
        $accion = match ($method) {
            'POST' => 'insercion',
            'PUT', 'PATCH' => 'actualizacion',
            'DELETE' => 'eliminacion',
            default => 'consultar',
        };

        $perm = app(PermissionService::class);
        if (!$perm->can($user, $candidates, $accion)) {
            return response()->json(['error' => 'Permiso denegado', 'objeto' => $candidates[0] ?? 'desconocido', 'accion' => $accion], 403);
        }

        return $next($request);
    }
}
