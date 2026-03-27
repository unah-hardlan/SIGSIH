<?php

namespace App\Http\Middleware;

use App\Services\BitacoraService;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class LogCrudActions
{
    public function __construct(private BitacoraService $bitacora) {}

    public function handle(Request $request, Closure $next)
    {
        $method = strtoupper($request->method());
        if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $next($request);
        }

        [$controllerBase, $objeto] = $this->resolveControllerInfo($request);
        $recordId = $this->resolveRouteRecordId($request);
        $modelClass = $this->resolveModelClass($controllerBase);
        $beforeState = null;
        $table = null;

        if ($modelClass) {
            try {
                /** @var Model $modelProbe */
                $modelProbe = new $modelClass();
                $table = $modelProbe->getTable();
                if (in_array($method, ['PUT', 'PATCH', 'DELETE']) && $recordId !== null) {
                    $current = $modelClass::query()->find($recordId);
                    if ($current) {
                        $beforeState = $this->sanitizePayload($current->toArray());
                        $recordId = $current->getKey();
                    }
                }
            } catch (\Throwable $e) {
            }
        }

        $afterState = null;
        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $afterState = $this->sanitizePayload($request->except([
                '_token',
                '_method',
                'contrasena',
                'password',
                'password_confirmation',
                'two_factor_secret',
                'two_factor_recovery_codes',
            ]));
            if (empty($afterState)) {
                $afterState = null;
            }
        }

        $response = $next($request);
        try {
            $status = (int) $response->getStatusCode();
            if ($status < 200 || $status >= 300) {
                return $response;
            }

            if ($request->attributes->get('bitacora_logged')) {
                return $response;
            }

            $userId = optional(auth()->user())->id_usuario_pk ?? 'guest';
            $path = $request->path();
            $key = 'bitacora:dedup:' . $userId . ':' . $method . ':' . $path;
            if (cache()->get($key)) {
                return $response;
            }

            if ($recordId === null && $method === 'POST') {
                $recordId = $this->extractIdFromResponse($response);
            }

            $accion = match ($method) {
                'POST' => 'Insertar',
                'PUT', 'PATCH' => 'Actualizar',
                'DELETE' => 'Eliminar',
                default => 'Accion',
            };


            $path = $request->path();
            if (preg_match('#(^|/)login($|/)#i', $path) || preg_match('#(^|/)logout($|/)#i', $path)) {
                return $response;
            }

            $descripcion = $this->friendlyDescription($accion, $objeto, $recordId, $path);
            $this->bitacora->logFor($objeto, $accion, $descripcion, null, [
                'tabla' => $table,
                'id_registro' => is_scalar($recordId) ? (string) $recordId : null,
                'antes' => $beforeState,
                'despues' => $afterState,
            ]);
        } catch (\Throwable $e) {
        }
        return $response;
    }

    private function resolveControllerInfo(Request $request): array
    {
        $route = $request->route();
        $controller = $route ? ($route->getActionName() ?? '') : '';
        $controllerBase = $controller;
        if (is_string($controller) && str_contains($controller, '@')) {
            [$class] = explode('@', $controller);
            $controllerBase = class_basename($class);
        } else {
            $controllerBase = class_basename((string) $controller);
        }

        $controllerBase = str_ends_with($controllerBase, 'Controller')
            ? substr($controllerBase, 0, -10)
            : $controllerBase;

        $map = [
            'Auth' => 'Login',
            'Usuario' => 'Usuarios',
            'Rol' => 'Roles',
            'Permiso' => 'Permisos',
            'Parametro' => 'Parámetros',
            'Objeto' => 'Objetos',
            'Bitacora' => 'Bitácora',
        ];

        return [$controllerBase, $map[$controllerBase] ?? $controllerBase];
    }

    private function resolveRouteRecordId(Request $request): mixed
    {
        $route = $request->route();
        if (!$route) return null;
        $params = array_values($route->parameters() ?? []);
        for ($i = count($params) - 1; $i >= 0; $i--) {
            $value = $params[$i];
            if (is_scalar($value) && (string) $value !== '') {
                return $value;
            }
            if (is_object($value) && method_exists($value, 'getKey')) {
                return $value->getKey();
            }
        }
        return null;
    }

    private function resolveModelClass(string $controllerBase): ?string
    {
        $map = [
            'Ciudades' => 'Ciudad',
            'Paises' => 'Pais',
            'Agencias' => 'Agencia',
            'Departamentos' => 'Departamento',
            'Usuarios' => 'Usuario',
            'Roles' => 'Rol',
            'Permisos' => 'Permiso',
            'Parametros' => 'Parametro',
            'Objetos' => 'Objeto',
            'NombresEmpresa' => 'NombreEmpresa',
            'OficinasEmpresa' => 'OficinaEmpresa',
            'EmpresasCliente' => 'EmpresaCliente',
            'HistorialContrasenas' => 'HistorialContrasena',
        ];

        $candidates = [];
        $candidates[] = $controllerBase;
        if (isset($map[$controllerBase])) {
            $candidates[] = $map[$controllerBase];
        }
        if (str_ends_with($controllerBase, 'es')) {
            $candidates[] = substr($controllerBase, 0, -2);
        }
        if (str_ends_with($controllerBase, 's')) {
            $candidates[] = substr($controllerBase, 0, -1);
        }

        foreach (array_unique($candidates) as $name) {
            $class = 'App\\Models\\' . $name;
            if (class_exists($class) && is_subclass_of($class, Model::class)) {
                return $class;
            }
        }
        return null;
    }

    private function sanitizePayload(array $payload): array
    {
        $deny = [
            'contrasena',
            'password',
            'password_confirmation',
            'two_factor_secret',
            'two_factor_recovery_codes',
        ];

        $sanitizeValue = function ($value) use (&$sanitizeValue) {
            if (is_array($value)) {
                $out = [];
                foreach ($value as $k => $v) {
                    $out[$k] = $sanitizeValue($v);
                }
                return $out;
            }
            if (is_object($value)) {
                if (method_exists($value, 'toArray')) {
                    return $sanitizeValue($value->toArray());
                }
                return (string) $value;
            }
            return $value;
        };

        $clean = [];
        foreach ($payload as $k => $v) {
            if (in_array((string) $k, $deny, true)) continue;
            $clean[$k] = $sanitizeValue($v);
        }

        return $clean;
    }

    private function extractIdFromResponse($response): mixed
    {
        if (!is_object($response) || !method_exists($response, 'getContent')) {
            return null;
        }
        try {
            $raw = $response->getContent();
            if (!is_string($raw) || trim($raw) === '') return null;
            $json = json_decode($raw, true);
            if (!is_array($json)) return null;
            $data = $json['data'] ?? $json;
            if (!is_array($data)) return null;
            foreach (['id', 'id_usuario_pk', 'id_cliente_pk', 'id_objetos_pk', 'id_agencias_pk'] as $key) {
                if (isset($data[$key]) && is_scalar($data[$key])) {
                    return $data[$key];
                }
            }
        } catch (\Throwable $e) {
        }
        return null;
    }

    private function friendlyDescription(string $accion, string $objeto, mixed $recordId, string $path): string
    {
        $idText = $recordId !== null && $recordId !== '' ? (' (ID: ' . $recordId . ')') : '';
        return match ($accion) {
            'Insertar' => 'Se creó un registro en ' . $objeto . $idText,
            'Actualizar' => 'Se actualizó un registro en ' . $objeto . $idText,
            'Eliminar' => 'Se eliminó un registro de ' . $objeto . $idText,
            default => strtoupper($accion) . ' ' . $path,
        };
    }
}
