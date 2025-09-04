<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRolRequest;
use App\Http\Requests\UpdateRolRequest;
use App\Http\Resources\RolResource;
use App\Http\Resources\UsuarioResource;
use App\Services\BitacoraService;

class RolController extends Controller
{
    public function __construct(private BitacoraService $bitacora) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Rol::query();
        if($q = $request->input('q')){
            $query->where(function($sub) use ($q){
                $sub->where('rol','like',"%$q%")
                    ->orWhere('descripcion_rol','like',"%$q%");
            });
        }
        // Si se deseara filtrar por algún estado futuro, aquí se colocaría (no hay campo estado definido)
        $sortable = ['rol'=>'rol','descripcion'=>'descripcion_rol','creado'=>'fecha_creacion'];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','asc'))==='desc' ? 'desc':'asc';
        $query->orderBy($sortable[$sort] ?? 'id_rol_pk', $direction);
        $perPage = (int)$request->input('per_page',10);
        $roles = $query->paginate($perPage);
        return RolResource::collection($roles)->additional([
            'meta'=>[
                'page'=>$roles->currentPage(),
                'per_page'=>$roles->perPage(),
                'total'=>$roles->total(),
                'last_page'=>$roles->lastPage(),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRolRequest $request)
    {
        $data = $request->validated();
        $data['creado_por'] = auth()->user()->usuario ?? 'system';
        $data['fecha_creacion'] = now();
        $rol = Rol::create($data);
    try { $this->bitacora->logFor('Roles', 'Insertar', 'Creación de rol '.$rol->rol); } catch (\Throwable $e) {}
        return (new RolResource($rol))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $rol = Rol::find($id);
        if(!$rol) return response()->json(['error'=>'Rol no encontrado'],404);
        return (new RolResource($rol))->response();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRolRequest $request, $id)
    {
        $rol = Rol::find($id);
        if(!$rol) return response()->json(['error'=>'Rol no encontrado'],404);
        $data = $request->validated();
        $data['modificado_por'] = auth()->user()->usuario ?? 'system';
        $data['fecha_modificacion'] = now();
        $rol->update($data);
    try { $this->bitacora->logFor('Roles', 'Actualizar', 'Actualización de rol '.$rol->rol); } catch (\Throwable $e) {}
        return (new RolResource($rol))->response();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $rol = Rol::find($id);
        if(!$rol) return response()->json(['error'=>'Rol no encontrado'],404);
        $rol->delete(); // eliminación física según requisitos actuales
    try { $this->bitacora->logFor('Roles', 'Eliminar', 'Eliminación de rol '.$rol->rol); } catch (\Throwable $e) {}
        return response()->json(['message'=>'Rol eliminado']);
    }

    /**
     * Listar usuarios asignados a un rol específico (FK directa).
     */
    public function usuarios($id, Request $request)
    {
    $rol = Rol::find($id);
    if(!$rol) return response()->json(['error' => 'Rol no encontrado'], 404);

    // Usuarios con FK directa al rol
    $users = \App\Models\Usuario::where('id_rol_fk', $rol->id_rol_pk);

        if ($q = $request->input('q')) {
            $users->where(function ($sub) use ($q) {
                $sub->where('usuario', 'like', "%$q%")
                    ->orWhere('nombre_usuario', 'like', "%$q%")
                    ->orWhere('correo_electronico', 'like', "%$q%");
            });
        }

        $sortable = [
            'usuario' => 'usuario',
            'nombre' => 'nombre_usuario',
            'correo' => 'correo_electronico',
            'creado' => 'fecha_creacion',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        $users->orderBy($sortable[$sort] ?? 'id_usuario_pk', $direction);

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
