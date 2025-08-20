<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Http\Resources\UsuarioResource;

class UsuarioController extends Controller
{
    public function index()
    {
        $query = Usuario::query();

        // Si no se especifica estado ni ?all=1, sólo activos
        if (!request()->has('estado') && request('all') != 1) {
            $query->where('estado_usuario', 'ACTIVO');
        }

        if ($estado = request('estado')) {
            $query->where('estado_usuario', $estado);
        }
        if ($q = request('q')) {
            $query->where(function($sub) use ($q) {
                $sub->where('usuario', 'like', "%$q%")
                    ->orWhere('nombre_usuario', 'like', "%$q%")
                    ->orWhere('correo_electronico', 'like', "%$q%" );
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
        $direction = strtolower(request('direction','asc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            // orden por defecto
            $query->orderBy('id_usuario_pk', 'desc');
        }

        $perPage = (int) request('per_page', 15);
        $usuarios = $query->paginate($perPage);

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
        $data['contrasena'] = Hash::make($data['contrasena']);
        $usuario = Usuario::create($data);
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
        if (isset($data['contrasena'])) {
            $data['contrasena'] = Hash::make($data['contrasena']);
        }
        $usuario->update($data);
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
        return response()->json(['message' => 'Usuario inactivado'], 200);
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
            $query->where(function($sub) use ($q) {
                $sub->where('usuario', 'like', "%$q%")
                    ->orWhere('nombre_usuario', 'like', "%$q%")
                    ->orWhere('correo_electronico', 'like', "%$q%" );
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
        $direction = strtolower($request->input('direction','asc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            $query->orderBy('id_usuario_pk', 'desc');
        }

        $usuarios = $query->get();
        $total = $usuarios->count();
        $activos = $usuarios->where('estado_usuario','ACTIVO')->count();
        $inactivos = $usuarios->where('estado_usuario','INACTIVO')->count();

        $fecha = now()->format('d/m/Y');
        $modulo = 'usuarios';

        return view('admin.reporte-usuarios', compact('usuarios','total','activos','inactivos','fecha','modulo','sort','direction'));
    }
}
