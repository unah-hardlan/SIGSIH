<?php

namespace App\Http\Controllers;

use App\Models\EstadoFactura;
use App\Http\Resources\EstadoFacturaResource;
use App\Http\Requests\StoreEstadoFacturaRequest;
use App\Http\Requests\UpdateEstadoFacturaRequest;
use Illuminate\Http\Request;

class EstadoFacturaController extends Controller
{
    public function index(Request $request)
    {
        $query = EstadoFactura::query();
        
        if($q = $request->input('q')){
            $query->where(function($sub) use ($q){
                $sub->where('nombre_estado','like',"%$q%")
                    ->orWhere('descripcion_estado_factura','like',"%$q%");
            });
        }
        
        $sortable = [
            'nombre' => 'nombre_estado',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','asc'))==='desc'?'desc':'asc';
        $query->orderBy($sortable[$sort] ?? 'id_estado_factura_pk',$direction);

        if($request->boolean('all')){
            return EstadoFacturaResource::collection($query->get());
        }
        
        $perPage = (int)$request->input('per_page',15);
        $items = $query->paginate($perPage);
        return EstadoFacturaResource::collection($items)->additional([
            'meta'=>[
                'page'=>$items->currentPage(),
                'per_page'=>$items->perPage(),
                'total'=>$items->total(),
                'last_page'=>$items->lastPage(),
            ]
        ]);
    }

    public function store(StoreEstadoFacturaRequest $request)
    {
        $estado = EstadoFactura::create($request->validated());
        return (new EstadoFacturaResource($estado))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $estado = EstadoFactura::find($id);
        if(!$estado) return response()->json(['error'=>'Estado de factura no encontrado'],404);
        return (new EstadoFacturaResource($estado))->response();
    }

    public function update(UpdateEstadoFacturaRequest $request, $id)
    {
        $estado = EstadoFactura::find($id);
        if(!$estado) return response()->json(['error'=>'Estado de factura no encontrado'],404);
        $estado->update($request->validated());
        return (new EstadoFacturaResource($estado))->response();
    }

    public function destroy($id)
    {
        $estado = EstadoFactura::find($id);
        if(!$estado) return response()->json(['error'=>'Estado de factura no encontrado'],404);
        $estado->delete();
        return response()->json(['message'=>'Estado de factura eliminado']);
    }
}