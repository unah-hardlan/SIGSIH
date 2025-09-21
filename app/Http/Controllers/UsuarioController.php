<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Http\Resources\UsuarioResource;
use App\Http\Resources\RolResource;
use App\Models\Rol;
use App\Services\BitacoraService;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    public function __construct(private BitacoraService $bitacora)
    {
        // DB-backed permisos por objeto/acción
        $this->middleware('permiso:usuarios,consultar')->only(['index', 'show']);
        $this->middleware('permiso:usuarios,insercion')->only(['store']);
        $this->middleware('permiso:usuarios,actualizacion')->only(['update', 'setRol']);
        $this->middleware('permiso:usuarios,eliminacion')->only(['destroy']);
        $this->middleware('permiso:usuarios,consultar')->only(['rol']);
    }
    public function index()
    {
        $query = Usuario::with('rol');

        // Si no se especifica estado ni ?all=1, sólo activos
        if (!request()->has('estado') && request('all') != 1) {
            $query->where('estado_usuario', 'ACTIVO');
        }

        if ($estado = request('estado')) {
            $query->where('estado_usuario', $estado);
        }
        if ($q = request('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('usuario', 'like', "%$q%")
                    ->orWhere('nombre_usuario', 'like', "%$q%")
                    ->orWhere('correo_electronico', 'like', "%$q%");
            });
        }

        // Ordenamiento dinámico
        $sortable = [
            'nombre_usuario' => 'nombre_usuario',
            'usuario' => 'usuario',
            'correo_electronico' => 'correo_electronico',
            'estado_usuario' => 'estado_usuario',
            'creado' => 'fecha_creacion',
        ];
        $sort = request('sort');
        $direction = strtolower(request('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            // orden por defecto
            $query->orderBy('id_usuario_pk', 'desc');
        }

        $perPage = (int) request('per_page', 15);
        $usuarios = $query->with('rol')->paginate($perPage);

        return UsuarioResource::collection($usuarios)->additional([
            'meta' => [
                'page' => $usuarios->currentPage(),
                'per_page' => $usuarios->perPage(),
                'total' => $usuarios->total(),
                'last_page' => $usuarios->lastPage(),
            ]
        ]);
    }

    public function create() {}

    public function store(StoreUsuarioRequest $request)
    {
        $data = $request->validated();
        $usuario = Usuario::create($data); // mutator hash contrasena
        try {
            $this->bitacora->logFor('Usuarios', 'Insertar', 'Creación de usuario ' . $usuario->usuario, null, [
                'tabla' => 'tbl_ms_usuario',
                'id_registro' => $usuario->id_usuario_pk,
                'despues' => $usuario->getAttributes(),
            ]);
        } catch (\Throwable $e) {
        }
        return (new UsuarioResource($usuario))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        return (new UsuarioResource($usuario))->response();
    }

    public function edit(string $id) {}

    public function update(UpdateUsuarioRequest $request, $id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        $data = $request->validated();
        $antes = $usuario->getOriginal();
        $usuario->update($data); // mutator hash contrasena si viene
        $usuario->refresh();
        try {
            $this->bitacora->logFor('Usuarios', 'Actualizar', 'Actualización de usuario ' . $usuario->usuario, null, [
                'tabla' => 'tbl_ms_usuario',
                'id_registro' => $usuario->id_usuario_pk,
                'antes' => $antes,
                'despues' => $usuario->getAttributes(),
            ]);
        } catch (\Throwable $e) {
        }
        return (new UsuarioResource($usuario))->response();
    }

    public function destroy($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        if ($usuario->estado_usuario === 'INACTIVO') {
            return response()->json(['message' => 'Usuario ya estaba inactivo'], 200);
        }
        $usuario->estado_usuario = 'INACTIVO';
        $usuario->save();
        try {
            $this->bitacora->logFor('Usuarios', 'Eliminar', 'Inactivación de usuario ' . $usuario->usuario);
        } catch (\Throwable $e) {
        }
        return response()->json(['message' => 'Usuario inactivado'], 200);
    }

    // Rol del usuario (FK directa)
    public function rol($id)
    {
        $usuario = Usuario::with('rol')->find($id);
        if (!$usuario) return response()->json(['error' => 'Usuario no encontrado'], 404);
        if (!$usuario->rol) return response()->json(['data' => null]);
        return (new RolResource($usuario->rol))->response();
    }

    public function setRol(Request $request, $id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) return response()->json(['error' => 'Usuario no encontrado'], 404);
        $validated = $request->validate([
            'id_rol_fk' => 'required|integer|exists:tbl_ms_rol,id_rol_pk',
        ]);
        $usuario->id_rol_fk = $validated['id_rol_fk'];
        $usuario->save();
        // Log de asignación de rol
        try {
            $rolNombre = \App\Models\Rol::where('id_rol_pk', $validated['id_rol_fk'])->value('rol');
            $this->bitacora->logFor('Usuarios', 'Actualizar', 'Asignación de rol a usuario ' . $usuario->usuario . ' -> ' . $rolNombre, $usuario->id_usuario_pk);
        } catch (\Throwable $e) {
        }
        return response()->json(['message' => 'Rol asignado']);
    }

    // Sincronizar múltiples roles (N:M). Mantiene id_rol_fk como rol principal por compatibilidad
    public function syncRoles(Request $request, $id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) return response()->json(['error' => 'Usuario no encontrado'], 404);
        $validated = $request->validate([
            'roles' => 'array',
            'roles.*' => 'integer|exists:tbl_ms_rol,id_rol_pk',
            'rol_principal' => 'nullable|integer|exists:tbl_ms_rol,id_rol_pk',
        ]);
        $roles = $validated['roles'] ?? [];
        $principal = $validated['rol_principal'] ?? (count($roles) ? $roles[0] : null);

        DB::transaction(function () use ($usuario, $roles, $principal) {
            // Sincronizar N:M
            try {
                $usuario->roles()->sync($roles);
            } catch (\Throwable $e) {
            }
            // Mantener rol principal para compatibilidad con UI existente
            $usuario->id_rol_fk = $principal;
            $usuario->save();
        });

        try {
            $this->bitacora->logFor('Usuarios', 'Actualizar', 'Sincronización de roles', $usuario->id_usuario_pk, [
                'tabla' => 'tbl_usuario_rol',
                'id_registro' => $usuario->id_usuario_pk,
                'despues' => ['roles' => $roles, 'rol_principal' => $principal],
            ]);
        } catch (\Throwable $e) {
        }

        return response()->json(['message' => 'Roles sincronizados', 'roles' => $roles, 'rol_principal' => $principal]);
    }

    // Obtener roles asignados y rol principal para precargar el modal
    public function getRoles($id)
    {
        $usuario = Usuario::with('roles')->find($id);
        if (!$usuario) return response()->json(['error' => 'Usuario no encontrado'], 404);
        $roles = ($usuario->roles ?? collect())->pluck('id_rol_pk')->map(fn($v) => (int) $v)->values()->all();
        $principal = $usuario->id_rol_fk ? (int) $usuario->id_rol_fk : (count($roles) ? (int) $roles[0] : null);
        return response()->json(['roles' => $roles, 'rol_principal' => $principal]);
    }

    // Reporte web (HTML) dinámico
    public function reporte(Request $request)
    {
        $query = Usuario::query();

        if (!$request->filled('estado') && $request->input('all') != 1) {
            $query->where('estado_usuario', 'ACTIVO');
        }
        if ($estado = $request->input('estado')) {
            $query->where('estado_usuario', $estado);
        }
        if ($q = $request->input('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('usuario', 'like', "%$q%")
                    ->orWhere('nombre_usuario', 'like', "%$q%")
                    ->orWhere('correo_electronico', 'like', "%$q%");
            });
        }
        // Orden
        $sortable = [
            'nombre_usuario' => 'nombre_usuario',
            'usuario' => 'usuario',
            'correo_electronico' => 'correo_electronico',
            'estado_usuario' => 'estado_usuario',
            'creado' => 'fecha_creacion',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            $query->orderBy('id_usuario_pk', 'desc');
        }

        $usuarios = $query->get();
        $total = $usuarios->count();
        $activos = $usuarios->where('estado_usuario', 'ACTIVO')->count();
        $inactivos = $usuarios->where('estado_usuario', 'INACTIVO')->count();
        $bloqueados = $usuarios->where('estado_usuario', 'BLOQUEADO')->count();

        $fecha = now()->format('d/m/Y');
        $modulo = 'usuarios';

        return view('admin.reporte-usuarios', compact('usuarios', 'total', 'activos', 'inactivos', 'bloqueados', 'fecha', 'modulo', 'sort', 'direction'));
    }
}
