<?php

namespace App\Services;

use App\Models\Bitacora;
use App\Models\Objeto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BitacoraService
{
    protected function objetoIdFor(string $slug): ?int
    {
        $name = trim($slug);
        $key = 'bitacora:objetoId:' . strtolower($name);
        $cached = cache()->get($key);
        if ($cached) return (int) $cached;
        $id = Objeto::where('nombre_objeto', $name)->value('id_objetos_pk')
            ?? Objeto::whereRaw('LOWER(nombre_objeto)=?', [strtolower($name)])->value('id_objetos_pk');
        if (!$id) {
            // Auto-crear objeto si no existe
            $tipoId = DB::table('tbl_tipo_objetos')
                ->whereRaw("LOWER(nombre_tipo_objeto) LIKE 'configur%'")
                ->value('id_tipo_objeto_pk')
                ?? DB::table('tbl_tipo_objetos')->orderBy('id_tipo_objeto_pk')->value('id_tipo_objeto_pk');
            try {
                $id = DB::table('tbl_objetos')->insertGetId([
                    'nombre_objeto' => $name,
                    'descripcion_objeto' => 'Generado automáticamente para bitácora',
                    'id_tipo_objetos_fk' => $tipoId,
                    'creado_por' => 'system',
                    'fecha_creacion' => now(),
                ]);
            } catch (\Throwable $e) {
                // ignore
            }
        }
        if ($id) cache()->put($key, (int) $id, now()->addHours(6));
        return $id ? (int) $id : null;
    }

    public function logFor(string $slugObjeto, string $accion, string $descripcion = null, ?int $idUsuario = null): Bitacora
    {
        $idObjeto = $this->objetoIdFor($slugObjeto);
    return $this->log($accion, $descripcion, $idObjeto, $idUsuario);
    }

    public function log(string $accion, string $descripcion = null, ?int $idObjeto = null, ?int $idUsuario = null): Bitacora
    {
    $user = Auth::user();
    $userId = $idUsuario ?? ($user->id_usuario_pk ?? null);
        // Inferir objeto si no fue provisto
        if ($idObjeto === null) {
            try {
                $route = request()->route();
                $controller = $route ? ($route->getActionName() ?? '') : '';
                $controllerBase = $controller;
                if (is_string($controller) && str_contains($controller, '@')) {
                    [$class, $methodName] = explode('@', $controller);
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
                $objetoName = $map[$controllerBase] ?? $controllerBase ?: null;
                if (!$objetoName) {
                    $path = request()->path();
                    $first = $path ? explode('/', trim($path, '/'))[0] : null;
                    if ($first) $objetoName = ucfirst($first);
                }
                if ($objetoName) {
                    $idObjeto = $this->objetoIdFor($objetoName);
                }
            } catch (\Throwable $e) {}
        }
        $bit = new Bitacora();
    $bit->fecha_evento = now();
        $bit->id_usuario_fk = $userId;
        if ($idObjeto) $bit->id_objetos_fk = $idObjeto;
        $bit->accion = $accion;
        if ($descripcion) $bit->descripcion = $descripcion;
    // Auditoría explícita (por si la BD exige NOT NULL)
    $bit->creado_por = $user->usuario ?? 'system';
    $bit->fecha_creacion = now();
        $bit->save();
        // Señal para evitar duplicados en el middleware y dedupe por unos segundos
        try {
            request()->attributes->set('bitacora_logged', true);
            $path = request()->path();
            $method = strtoupper(request()->method());
            $userKey = $userId ?: 'guest';
            cache()->put("bitacora:dedup:$userKey:$method:$path", 1, now()->addSeconds(5));
        } catch (\Throwable $e) {}
        return $bit;
    }
}
