<?php

namespace App\Http\Middleware;

use App\Services\BitacoraService;
use Closure;
use Illuminate\Http\Request;

class LogCrudActions
{
    public function __construct(private BitacoraService $bitacora) {}

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        try {
            $method = strtoupper($request->method());
            if (!in_array($method, ['POST','PUT','PATCH','DELETE'])) {
                return $response;
            }
            // Solo si la respuesta es 2xx
            $status = (int) $response->getStatusCode();
            if ($status < 200 || $status >= 300) {
                return $response;
            }
            // Si ya se registró manualmente en el controlador, no duplicar
            if ($request->attributes->get('bitacora_logged')) {
                return $response;
            }
            // Evitar duplicado inmediato por misma ruta/método/usuario en ventana corta
            $userId = optional(auth()->user())->id_usuario_pk ?? 'guest';
            $path = $request->path();
            $key = 'bitacora:dedup:' . $userId . ':' . $method . ':' . $path;
            if (cache()->get($key)) {
                return $response;
            }

            $accion = match ($method) {
                'POST' => 'Insertar',
                'PUT', 'PATCH' => 'Actualizar',
                'DELETE' => 'Eliminar',
                default => 'Accion',
            };

            $route = $request->route();
            $controller = $route ? ($route->getActionName() ?? '') : '';
            $controllerBase = $controller;
            if (str_contains($controller, '@')) {
                [$class, $methodName] = explode('@', $controller);
                $controllerBase = class_basename($class);
            } else {
                $controllerBase = class_basename((string) $controller);
            }
            $controllerBase = str_ends_with($controllerBase, 'Controller')
                ? substr($controllerBase, 0, -10)
                : $controllerBase;

            // Mapeo a nombres de objeto conocidos
            $map = [
                'Auth' => 'Login',
                'Usuario' => 'Usuarios',
                'Rol' => 'Roles',
                'Permiso' => 'Permisos',
                'Parametro' => 'Parámetros',
                'Objeto' => 'Objetos',
                'Bitacora' => 'Bitácora',
            ];
            $objeto = $map[$controllerBase] ?? $controllerBase;

            // Evitar doble registro en login/logout ya cubiertos explícitamente
            $path = $request->path();
            if (preg_match('#(^|/)login($|/)#i', $path) || preg_match('#(^|/)logout($|/)#i', $path)) {
                return $response;
            }

            $descripcion = sprintf('%s %s', $request->method(), $path);
            $this->bitacora->logFor($objeto, $accion, $descripcion);
        } catch (\Throwable $e) {
            // No romper el flujo por errores de bitácora
        }
        return $response;
    }
}
