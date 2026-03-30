<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRolRequest;
use App\Http\Requests\UpdateRolRequest;
use App\Http\Resources\RolResource;
use App\Http\Resources\UsuarioResource;
use App\Services\BitacoraService;
use Illuminate\Database\QueryException;

class RolController extends Controller
{
    public function __construct(private BitacoraService $bitacora) {}

    public function index(Request $request)
    {
        $query = Rol::query();
        if ($q = $request->input('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('rol', 'like', "%$q%")
                    ->orWhere('descripcion_rol', 'like', "%$q%");
            });
        }

        $sortable = ['rol' => 'rol', 'descripcion' => 'descripcion_rol', 'creado' => 'fecha_creacion'];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortable[$sort] ?? 'id_rol_pk', $direction);
        $perPage = (int)$request->input('per_page', 10);
        $roles = $query->paginate($perPage);
        return RolResource::collection($roles)->additional([
            'meta' => [
                'page' => $roles->currentPage(),
                'per_page' => $roles->perPage(),
                'total' => $roles->total(),
                'last_page' => $roles->lastPage(),
            ]
        ]);
    }


    public function create() {}


    public function store(StoreRolRequest $request)
    {
        $data = $request->validated();
        $data['creado_por'] = auth()->user()->usuario ?? 'system';
        $data['fecha_creacion'] = now();
        $rol = Rol::create($data);
        try {
            $this->bitacora->logFor('Roles', 'Insertar', 'Creación de rol ' . $rol->rol);
        } catch (\Throwable $e) {
        }
        return (new RolResource($rol))->response()->setStatusCode(201);
    }


    public function show($id)
    {
        $rol = Rol::find($id);
        if (!$rol) return response()->json(['error' => 'Rol no encontrado'], 404);
        return (new RolResource($rol))->response();
    }


    public function edit(string $id) {}


    public function update(UpdateRolRequest $request, $id)
    {
        $rol = Rol::find($id);
        if (!$rol) return response()->json(['error' => 'Rol no encontrado'], 404);
        $data = $request->validated();
        $data['modificado_por'] = auth()->user()->usuario ?? 'system';
        $data['fecha_modificacion'] = now();
        $rol->update($data);
        try {
            $this->bitacora->logFor('Roles', 'Actualizar', 'Actualización de rol ' . $rol->rol);
        } catch (\Throwable $e) {
        }
        return (new RolResource($rol))->response();
    }


    public function destroy($id)
    {
        $rol = Rol::find($id);
        if (!$rol) return response()->json(['error' => 'Rol no encontrado'], 404);
        try {
            $rol->delete();
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se puede eliminar este rol porque tiene usuarios o permisos asociados. Reasigna esos registros e intentalo de nuevo.'
            ], 422);
        }
        try {
            $this->bitacora->logFor('Roles', 'Eliminar', 'Eliminación de rol ' . $rol->rol);
        } catch (\Throwable $e) {
        }
        return response()->json(['message' => 'Rol eliminado']);
    }


    public function usuarios($id, Request $request)
    {
        $rol = Rol::find($id);
        if (!$rol) return response()->json(['error' => 'Rol no encontrado'], 404);


        $users = \App\Models\Usuario::with('persona')->where('id_rol_fk', $rol->id_rol_pk);

        if ($q = $request->input('q')) {
            $users->searchByIdentity($q);
        }

        $sortable = [
            'usuario' => 'usuario',
            'nombre' => 'nombre',
            'correo' => 'correo_electronico',
            'creado' => 'fecha_creacion',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        if (($sortable[$sort] ?? null) === 'nombre') {
            $users->orderByPersonaName($direction);
        } else {
            $users->orderBy($sortable[$sort] ?? 'id_usuario_pk', $direction);
        }

        if ($request->boolean('all', false)) {
            $collection = $users->get();
            return UsuarioResource::collection($collection);
        }

        $perPage = (int) $request->input('per_page', 10);
        $paginator = $users->paginate($perPage);
        return UsuarioResource::collection($paginator)->additional([
            'meta' => [
                'page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }
}
