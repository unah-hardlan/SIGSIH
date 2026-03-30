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
use App\Models\Parametro;
use App\Models\HistorialContrasena;
use App\Services\BitacoraService;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    public function __construct(private BitacoraService $bitacora)
    {

        $this->middleware('permiso:usuarios,consultar')->only(['index', 'show']);
        $this->middleware('permiso:usuarios,insercion')->only(['store']);
        $this->middleware('permiso:usuarios,actualizacion')->only(['update', 'setRol', 'syncRoles', 'resetPasswordGenerica']);
        $this->middleware('permiso:usuarios,eliminacion')->only(['destroy']);
        $this->middleware('permiso:usuarios,consultar')->only(['rol', 'getRoles']);
    }


    private function isUserAdmin($user): bool
    {
        if (!$user) return false;
        if (!$user instanceof Usuario) {
            $userId = method_exists($user, 'getAuthIdentifier')
                ? $user->getAuthIdentifier()
                : ($user->id_usuario_pk ?? $user->id ?? null);
            $user = $userId ? Usuario::find($userId) : null;
            if (!$user) return false;
        }
        static $adminRoleId = null;
        if ($adminRoleId === null) {
            $adminRoleId = Rol::whereRaw('LOWER(rol)=?', ['administrador'])->value('id_rol_pk');
        }
        if (!$adminRoleId) return false;
        if ((int)$user->id_rol_fk === (int)$adminRoleId) return true;
        if ($user->relationLoaded('roles')) {
            return $user->roles->pluck('id_rol_pk')->contains((int)$adminRoleId);
        }
        return $user->roles()->where('id_rol_pk', $adminRoleId)->exists();
    }


    private function remainingAdminsCountExcluding(int $excludeUserId): int
    {
        $adminRoleId = Rol::whereRaw('LOWER(rol)=?', ['administrador'])->value('id_rol_pk');
        if (!$adminRoleId) return 0;
        return Usuario::where('id_usuario_pk', '!=', $excludeUserId)
            ->where(function ($q) use ($adminRoleId) {
                $q->where('id_rol_fk', $adminRoleId)
                    ->orWhereHas('roles', function ($r) use ($adminRoleId) {
                        $r->where('id_rol_pk', $adminRoleId);
                    });
            })->count();
    }

    private function logBlockedAttempt(string $accion, string $descripcion, ?int $idUsuario = null): void
    {
        try {
            $this->bitacora->logFor('Usuarios', $accion, $descripcion, $idUsuario);
        } catch (\Throwable $e) {
        }
    }

    private function isSelfTarget(int $targetUserId): bool
    {
        $auth = auth()->user();
        $authId = (int) ($auth->id_usuario_pk ?? $auth->id ?? 0);
        return $authId > 0 && $authId === $targetUserId;
    }

    private function genericPasswordValue(): string
    {
        $param = Parametro::whereIn('parametro', [
            'USUARIOS.PASSWORD_GENERICA',
            'USUARIOS.PASSWORD.GENERICA',
            'ADMIN.PASSWORD',
            'ADMIN_CPASS',
        ])->orderByRaw("FIELD(parametro,'USUARIOS.PASSWORD_GENERICA','USUARIOS.PASSWORD.GENERICA','ADMIN.PASSWORD','ADMIN_CPASS')")
            ->value('valor');

        $pwd = is_string($param) ? trim($param) : '';
        return $pwd !== '' ? $pwd : 'Temporal123!';
    }
    public function index()
    {
        $query = Usuario::with(['rol', 'persona']);


        if (!request()->has('estado') && request('all') != 1) {
            $query->where('estado_usuario', 'ACTIVO');
        }

        if ($estado = request('estado')) {
            $query->where('estado_usuario', $estado);
        }
        if ($q = request('q')) {
            $query->searchByIdentity($q);
        }


        $sortable = [
            'nombre' => 'nombre',
            'usuario' => 'usuario',
            'correo_electronico' => 'correo_electronico',
            'estado_usuario' => 'estado_usuario',
            'creado' => 'fecha_creacion',
        ];
        $sort = request('sort');
        $direction = strtolower(request('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && isset($sortable[$sort])) {
            if ($sortable[$sort] === 'nombre') {
                $query->orderByPersonaName($direction);
            } else {
                $query->orderBy($sortable[$sort], $direction);
            }
        } else {

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
        // Cuando el usuario se crea desde el módulo de administración, no
        // forzamos el primer ingreso por defecto. El flujo de registro
        // público (AuthController::register) y el restablecimiento
        // genérico (resetPasswordGenerica) son los únicos que deberán marcar
        // primer_ingreso = 1 para obligar al cambio de contraseña.
        if (!array_key_exists('primer_ingreso', $data)) {
            $data['primer_ingreso'] = 0;
        }
        $usuario = Usuario::create($data);
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
        if ($this->isSelfTarget((int) $usuario->id_usuario_pk)) {
            $this->logBlockedAttempt('Bloquear', 'Intento de autoedición bloqueado para usuario ' . $usuario->id_usuario_pk, $usuario->id_usuario_pk);
            return response()->json(['error' => 'No puedes realizar esta acción sobre tu propio usuario'], 422);
        }
        $data = $request->validated();
        $antes = $usuario->getOriginal();
        $usuario->update($data);
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
        if ($this->isSelfTarget((int) $usuario->id_usuario_pk)) {
            $this->logBlockedAttempt('Bloquear', 'Intento de autoinactivación bloqueado para usuario ' . $usuario->id_usuario_pk, $usuario->id_usuario_pk);
            return response()->json(['error' => 'No puedes realizar esta acción sobre tu propio usuario'], 422);
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
        if ($this->isSelfTarget((int) $usuario->id_usuario_pk)) {
            $this->logBlockedAttempt('Bloquear', 'Intento de autoasignación de rol bloqueado para usuario ' . $usuario->id_usuario_pk, $usuario->id_usuario_pk);
            return response()->json(['error' => 'No puedes realizar esta acción sobre tu propio usuario'], 422);
        }
        $validated = $request->validate([
            'id_rol_fk' => 'required|integer|exists:tbl_ms_rol,id_rol_pk',
        ]);
        $authUser = auth()->user();
        if (!$this->isUserAdmin($authUser)) {
            $this->logBlockedAttempt('Bloquear', 'Intento no autorizado de asignar rol por usuario ' . $authUser?->id_usuario_pk, $usuario->id_usuario_pk);
            return response()->json(['error' => 'No autorizado para asignar roles'], 403);
        }

        $beforePrimary = (int) $usuario->id_rol_fk;
        $beforePivot = $usuario->roles()->pluck('id_rol_pk')->map(fn($v) => (int)$v)->values()->all();
        $adminRoleId = Rol::whereRaw('LOWER(rol)=?', ['administrador'])->value('id_rol_pk');
        $oldIsAdmin = $this->isUserAdmin($usuario);
        $newIsAdmin = ((int)$validated['id_rol_fk'] === (int)$adminRoleId);
        if ($oldIsAdmin && !$newIsAdmin) {
            $remaining = $this->remainingAdminsCountExcluding($usuario->id_usuario_pk);
            if ($remaining === 0) {
                $this->logBlockedAttempt('Bloquear', 'Intento de dejar al sistema sin administradores al cambiar rol de usuario ' . $usuario->id_usuario_pk, $usuario->id_usuario_pk);
                return response()->json(['error' => 'No se puede remover el último administrador'], 422);
            }
        }
        $usuario->id_rol_fk = $validated['id_rol_fk'];
        $usuario->save();
        try {
            $rolNombre = \App\Models\Rol::where('id_rol_pk', $validated['id_rol_fk'])->value('rol');
            $this->bitacora->logFor('Usuarios', 'Actualizar', 'Asignación de rol a usuario ' . $usuario->usuario . ' -> ' . $rolNombre, $usuario->id_usuario_pk);
        } catch (\Throwable $e) {
        }

        $afterPrimary = (int) $usuario->id_rol_fk;

        $afterPivot = $usuario->roles()->pluck('id_rol_pk')->map(fn($v) => (int)$v)->values()->all();
        $changed = ($beforePrimary !== $afterPrimary) || (implode(',', $beforePivot) !== implode(',', $afterPivot));
        $reauth = false;
        if ($changed) {
            try {
                \App\Models\SesionUsuario::where('id_usuario_fk', $usuario->getKey())->delete();
                $this->bitacora->logFor('Usuarios', 'Seguridad', 'Invalidación de sesiones por cambio de rol', $usuario->id_usuario_pk, [
                    'antes' => ['principal' => $beforePrimary, 'pivot' => $beforePivot],
                    'despues' => ['principal' => $afterPrimary, 'pivot' => $afterPivot],
                ]);
                $reauth = true;
            } catch (\Throwable $e) {
            }
        }
        return response()->json([
            'message' => 'Rol asignado',
            'reauth_required' => $reauth,
        ]);
    }


    public function syncRoles(Request $request, $id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) return response()->json(['error' => 'Usuario no encontrado'], 404);
        if ($this->isSelfTarget((int) $usuario->id_usuario_pk)) {
            $this->logBlockedAttempt('Bloquear', 'Intento de autosincronización de roles bloqueado para usuario ' . $usuario->id_usuario_pk, $usuario->id_usuario_pk);
            return response()->json(['error' => 'No puedes realizar esta acción sobre tu propio usuario'], 422);
        }
        $validated = $request->validate([
            'roles' => 'array',
            'roles.*' => 'integer|exists:tbl_ms_rol,id_rol_pk',
            'rol_principal' => 'nullable|integer|exists:tbl_ms_rol,id_rol_pk',
        ]);
        $roles = $validated['roles'] ?? [];
        $principal = $validated['rol_principal'] ?? (count($roles) ? $roles[0] : null);
        $authUser = auth()->user();
        if (!$this->isUserAdmin($authUser)) {
            $this->logBlockedAttempt('Bloquear', 'Intento no autorizado de sincronizar roles por usuario ' . $authUser?->id_usuario_pk, $usuario->id_usuario_pk);
            return response()->json(['error' => 'No autorizado para sincronizar roles'], 403);
        }

        $beforePrimary = (int) $usuario->id_rol_fk;
        $beforePivot = $usuario->roles()->pluck('id_rol_pk')->map(fn($v) => (int)$v)->values()->all();
        $adminRoleId = Rol::whereRaw('LOWER(rol)=?', ['administrador'])->value('id_rol_pk');
        $oldIsAdmin = $this->isUserAdmin($usuario);
        $newIsAdmin = false;
        if ($adminRoleId) {
            $idsInt = array_map('intval', $roles);
            $newIsAdmin = in_array((int)$adminRoleId, $idsInt, true) || ((int)$principal === (int)$adminRoleId);
        }
        if ($oldIsAdmin && !$newIsAdmin) {
            $remaining = $this->remainingAdminsCountExcluding($usuario->id_usuario_pk);
            if ($remaining === 0) {
                $this->logBlockedAttempt('Bloquear', 'Intento de dejar al sistema sin administradores al sincronizar roles de usuario ' . $usuario->id_usuario_pk, $usuario->id_usuario_pk);
                return response()->json(['error' => 'No se puede remover el último administrador'], 422);
            }
        }
        DB::transaction(function () use ($usuario, $roles, $principal) {
            try {
                $usuario->roles()->sync($roles);
            } catch (\Throwable $e) {
            }
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

        $afterPrimary = (int) $usuario->id_rol_fk;
        $afterPivot = $usuario->roles()->pluck('id_rol_pk')->map(fn($v) => (int)$v)->values()->all();
        $changed = ($beforePrimary !== $afterPrimary) || (implode(',', $beforePivot) !== implode(',', $afterPivot));
        $reauth = false;
        if ($changed) {
            try {
                \App\Models\SesionUsuario::where('id_usuario_fk', $usuario->getKey())->delete();
                $this->bitacora->logFor('Usuarios', 'Seguridad', 'Invalidación de sesiones por cambio de roles', $usuario->id_usuario_pk, [
                    'antes' => ['principal' => $beforePrimary, 'pivot' => $beforePivot],
                    'despues' => ['principal' => $afterPrimary, 'pivot' => $afterPivot],
                ]);
                $reauth = true;
            } catch (\Throwable $e) {
            }
        }
        return response()->json([
            'message' => 'Roles sincronizados',
            'roles' => $roles,
            'rol_principal' => $principal,
            'reauth_required' => $reauth,
        ]);
    }


    public function getRoles($id)
    {
        $usuario = Usuario::with('roles')->find($id);
        if (!$usuario) return response()->json(['error' => 'Usuario no encontrado'], 404);
        $roles = ($usuario->roles ?? collect())->pluck('id_rol_pk')->map(fn($v) => (int) $v)->values()->all();
        $principal = $usuario->id_rol_fk ? (int) $usuario->id_rol_fk : (count($roles) ? (int) $roles[0] : null);
        return response()->json(['roles' => $roles, 'rol_principal' => $principal]);
    }

    public function resetPasswordGenerica($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        if ($this->isSelfTarget((int) $usuario->id_usuario_pk)) {
            $this->logBlockedAttempt('Bloquear', 'Intento de autorestablecimiento de contraseña bloqueado para usuario ' . $usuario->id_usuario_pk, $usuario->id_usuario_pk);
            return response()->json(['error' => 'No puedes realizar esta acción sobre tu propio usuario'], 422);
        }

        $genericPassword = $this->genericPasswordValue();
        $usuario->contrasena = $genericPassword;
        // Marcar que el usuario debe cambiar la contraseña porque el admin la
        // restableció de forma genérica. No tocar `primer_ingreso` aquí.
        $usuario->pendiente_cambio_contrasena = 1;
        $usuario->save();

        try {
            HistorialContrasena::create([
                'contrasena' => (string) $usuario->contrasena,
                'id_usuario_fk' => $usuario->id_usuario_pk,
                'creado_por' => auth()->user()->usuario ?? 'system',
                'fecha_creacion' => now(),
            ]);
        } catch (\Throwable $e) {
        }

        try {
            $this->bitacora->logFor('Usuarios', 'Seguridad', 'Restablecimiento de contraseña genérica para usuario ' . $usuario->usuario, $usuario->id_usuario_pk, [
                'tabla' => 'tbl_ms_usuario',
                'id_registro' => $usuario->id_usuario_pk,
                'despues' => ['pendiente_cambio_contrasena' => 1],
            ]);
        } catch (\Throwable $e) {
        }

        return response()->json([
            'message' => 'Contraseña restablecida correctamente',
            'usuario' => $usuario->usuario,
            'password_generica' => $genericPassword,
            'must_change_password' => true,
        ]);
    }


    public function tecnicosCatalog()
    {
        $roles = \App\Models\Rol::query()->where('rol', 'like', '%tecn%')->get(['id_rol_pk', 'rol']);
        if ($roles->isEmpty()) {
            return response()->json(['data' => [], 'meta' => ['count' => 0]]);
        }
        $roleIds = $roles->pluck('id_rol_pk')->all();
        // Solo usuarios ACTIVOS con rol técnico en id_rol_fk
        $userIdsPrimary = Usuario::whereIn('id_rol_fk', $roleIds)
            ->where('estado_usuario', 'ACTIVO')
            ->pluck('id_usuario_pk')->all();
        // Solo usuarios ACTIVOS con rol técnico en tbl_usuario_rol
        $userIdsPivot = \Illuminate\Support\Facades\DB::table('tbl_usuario_rol')
            ->whereIn('tbl_usuario_rol.id_rol_fk', $roleIds)
            ->join('tbl_ms_usuario', 'tbl_usuario_rol.id_usuario_fk', '=', 'tbl_ms_usuario.id_usuario_pk')
            ->where('tbl_ms_usuario.estado_usuario', 'ACTIVO')
            ->pluck('id_usuario_fk')->all();
        $userIds = collect($userIdsPrimary)->merge($userIdsPivot)->unique()->values()->all();
        if (empty($userIds)) {
            return response()->json(['data' => [], 'meta' => ['count' => 0]]);
        }
        $personas = \App\Models\Persona::whereIn('id_usuario_fk', $userIds)
            ->orderBy('primer_nombre')
            ->orderBy('primer_apellido')
            ->get(['id_persona_pk as id', 'primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido', 'id_usuario_fk']);
        return response()->json(['data' => $personas, 'meta' => ['count' => $personas->count()]]);
    }


    public function reporte(Request $request)
    {
        $query = Usuario::query()->with(['rol', 'persona']);

        if (!$request->filled('estado') && $request->input('all') != 1) {
            $query->where('estado_usuario', 'ACTIVO');
        }
        if ($estado = $request->input('estado')) {
            $query->where('estado_usuario', $estado);
        }
        if ($q = $request->input('q')) {
            $query->searchByIdentity($q);
        }

        $sortable = [
            'nombre' => 'nombre',
            'usuario' => 'usuario',
            'correo_electronico' => 'correo_electronico',
            'estado_usuario' => 'estado_usuario',
            'creado' => 'fecha_creacion',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && isset($sortable[$sort])) {
            if ($sortable[$sort] === 'nombre') {
                $query->orderByPersonaName($direction);
            } else {
                $query->orderBy($sortable[$sort], $direction);
            }
        } else {
            $query->orderBy('id_usuario_pk', 'desc');
        }

        $usuarios = $query->get();
        $total = $usuarios->count();
        $activos = $usuarios->where('estado_usuario', 'ACTIVO')->count();
        $inactivos = $usuarios->where('estado_usuario', 'INACTIVO')->count();
        $bloqueados = $usuarios->where('estado_usuario', 'BLOQUEADO')->count();

        $fecha = \App\Helpers\DateHelper::nowFormatted('d/m/Y');
        $modulo = 'usuarios';

        return view('admin.reporte-usuarios', compact('usuarios', 'total', 'activos', 'inactivos', 'bloqueados', 'fecha', 'modulo', 'sort', 'direction'));
    }
}
