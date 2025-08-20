<?php

namespace App\Http\Controllers;

use App\Models\Parametro;
use Illuminate\Http\Request;
use App\Http\Resources\ParametroResource;
use App\Http\Requests\StoreParametroRequest;
use App\Http\Requests\UpdateParametroRequest;

class ParametroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Parametro::query();
        if ($q = $request->input('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('parametro', 'like', "%$q%")
                    ->orWhere('valor', 'like', "%$q%");
            });
        }
        $sortable = [ 'parametro' => 'parametro', 'valor' => 'valor', 'creado' => 'fecha_creacion' ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && isset($sortable[$sort])) { $query->orderBy($sortable[$sort], $direction); } else { $query->orderBy('id_parametro_pk','desc'); }
        $perPage = (int)$request->input('per_page',10);
        $parametros = $query->paginate($perPage);
        return ParametroResource::collection($parametros)->additional([
            'meta' => [
                'page' => $parametros->currentPage(),
                'per_page' => $parametros->perPage(),
                'total' => $parametros->total(),
                'last_page' => $parametros->lastPage(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreParametroRequest $request)
    {
        $data = $request->validated();
        // Forzar asociación al usuario autenticado si la columna es NOT NULL en BD
        if (empty($data['id_usuario_fk'])) {
            $data['id_usuario_fk'] = auth()->user()->id_usuario_pk ?? auth()->id();
        }
        $data['creado_por'] = auth()->user()->usuario ?? 'system';
        $data['fecha_creacion'] = now();
        $parametro = Parametro::create($data);
        return (new ParametroResource($parametro))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $parametro = Parametro::find($id);
        if (!$parametro) return response()->json(['error'=>'Parámetro no encontrado'],404);
        return (new ParametroResource($parametro))->response();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParametroRequest $request, $id)
    {
        $parametro = Parametro::find($id);
        if (!$parametro) return response()->json(['error'=>'Parámetro no encontrado'],404);
        $data = $request->validated();
        if (empty($data['id_usuario_fk'])) {
            $data['id_usuario_fk'] = auth()->user()->id_usuario_pk ?? auth()->id();
        }
        $data['modificado_por'] = auth()->user()->usuario ?? 'system';
        $data['fecha_modificacion'] = now();
        $parametro->update($data);
        return (new ParametroResource($parametro))->response();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $parametro = Parametro::find($id);
        if (!$parametro) return response()->json(['error'=>'Parámetro no encontrado'],404);
        $parametro->delete();
        return response()->json(['message'=>'Parámetro eliminado']);
    }

    public function reporte(Request $request)
    {
        $query = Parametro::query();
        if ($q = $request->query('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('parametro','like',"%$q%")
                    ->orWhere('valor','like',"%$q%");
            });
        }
        $sortable = [ 'parametro' => 'parametro', 'valor' => 'valor', 'creado' => 'fecha_creacion' ];
        $sort = $request->query('sort');
        $direction = strtolower($request->query('direction','asc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && isset($sortable[$sort])) { $query->orderBy($sortable[$sort], $direction); } else { $query->orderBy('id_parametro_pk','desc'); }
        $parametros = $query->get();
        $total = $parametros->count();
        $fecha = $request->query('fecha', now()->format('d-M-Y'));
        $modulo = $request->query('modulo','PARAMETROS');
        return view('admin.reporte-parametros', compact('fecha','modulo','parametros','total'));
    }
}
