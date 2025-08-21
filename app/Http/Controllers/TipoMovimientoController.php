<?php

namespace App\Http\Controllers;

use App\Models\TipoMovimiento;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTipoMovimientoRequest;
use App\Http\Requests\UpdateTipoMovimientoRequest;
use App\Http\Resources\TipoMovimientoResource;

class TipoMovimientoController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoMovimiento::query();
        if($q = $request->input('q')){
            $query->where(function($sub) use ($q){
                $sub->where('nombre_tipo_movimiento','like',"%$q%")
                    ->orWhere('descripcion_tipo_movimiento','like',"%$q%");
            });
        }
        $sortable = [
            'nombre' => 'nombre_tipo_movimiento',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','asc'))==='desc'?'desc':'asc';
        $query->orderBy($sortable[$sort] ?? 'id_tipo_movimiento_pk',$direction);

        if($request->boolean('all')){
            return TipoMovimientoResource::collection($query->get());
        }
        $perPage = (int)$request->input('per_page',15);
        $items = $query->paginate($perPage);
        return TipoMovimientoResource::collection($items)->additional([
            'meta'=>[
                'page'=>$items->currentPage(),
                'per_page'=>$items->perPage(),
                'total'=>$items->total(),
                'last_page'=>$items->lastPage(),
            ]
        ]);
    }

    public function store(StoreTipoMovimientoRequest $request)
    {
        $tipo = TipoMovimiento::create($request->validated());
        return (new TipoMovimientoResource($tipo))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $tipo = TipoMovimiento::find($id);
        if(!$tipo) return response()->json(['error'=>'TipoMovimiento no encontrado'],404);
        return (new TipoMovimientoResource($tipo))->response();
    }

    public function update(UpdateTipoMovimientoRequest $request, $id)
    {
        $tipo = TipoMovimiento::find($id);
        if(!$tipo) return response()->json(['error'=>'TipoMovimiento no encontrado'],404);
        $tipo->update($request->validated());
        return (new TipoMovimientoResource($tipo))->response();
    }

    public function destroy($id)
    {
        $tipo = TipoMovimiento::find($id);
        if(!$tipo) return response()->json(['error'=>'TipoMovimiento no encontrado'],404);
        $tipo->delete();
        return response()->json(['message'=>'TipoMovimiento eliminado']);
    }
}
