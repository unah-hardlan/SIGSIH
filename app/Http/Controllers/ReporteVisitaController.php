<?php

namespace App\Http\Controllers;

use App\Models\ReporteVisita;
use Illuminate\Http\Request;
use App\Http\Requests\StoreReporteVisitaRequest;
use App\Http\Requests\UpdateReporteVisitaRequest;
use App\Http\Resources\ReporteVisitaResource;

class ReporteVisitaController extends Controller
{
    public function index(Request $request)
    {
        $query = ReporteVisita::query()->with(['tipoVisita','servicioRealizado','accionRealizada']);
        if($tipo = $request->input('id_tipo_visita_fk')){ $query->where('id_tipo_visita_fk',$tipo); }
        if($serv = $request->input('id_servicio_realizado_fk')){ $query->where('id_servicio_realizado_fk',$serv); }
        if($acc = $request->input('id_accion_realizada_fk')){ $query->where('id_accion_realizada_fk',$acc); }
        if($orden = $request->input('id_orden_servicio_fk')){ $query->where('id_orden_servicio_fk',$orden); }
        if($desde = $request->input('desde')){ $query->whereDate('fecha_reporte','>=',$desde); }
        if($hasta = $request->input('hasta')){ $query->whereDate('fecha_reporte','<=',$hasta); }
        if($q = $request->input('q')){ $query->where('observaciones','like',"%$q%"); }

        $sortable = [ 'fecha' => 'fecha_reporte' ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','desc'))==='asc'?'asc':'desc';
        $query->orderBy($sortable[$sort] ?? 'id_reportes_pk',$direction);

        if($request->boolean('all')){ return ReporteVisitaResource::collection($query->get()); }
        $perPage = (int)$request->input('per_page',15);
        $items = $query->paginate($perPage);
        return ReporteVisitaResource::collection($items)->additional([
            'meta'=>[
                'page'=>$items->currentPage(),
                'per_page'=>$items->perPage(),
                'total'=>$items->total(),
                'last_page'=>$items->lastPage(),
            ]
        ]);
    }

    public function store(StoreReporteVisitaRequest $request)
    {
        $rep = ReporteVisita::create($request->validated());
        $rep->load(['tipoVisita','servicioRealizado','accionRealizada']);
        return (new ReporteVisitaResource($rep))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $rep = ReporteVisita::with(['tipoVisita','servicioRealizado','accionRealizada'])->find($id);
        if(!$rep) return response()->json(['error'=>'Reporte no encontrado'],404);
        return (new ReporteVisitaResource($rep))->response();
    }

    public function update(UpdateReporteVisitaRequest $request, $id)
    {
        $rep = ReporteVisita::find($id);
        if(!$rep) return response()->json(['error'=>'Reporte no encontrado'],404);
        $rep->update($request->validated());
        $rep->load(['tipoVisita','servicioRealizado','accionRealizada']);
        return (new ReporteVisitaResource($rep))->response();
    }

    public function destroy($id)
    {
        $rep = ReporteVisita::find($id);
        if(!$rep) return response()->json(['error'=>'Reporte no encontrado'],404);
        $rep->delete();
        return response()->json(['message'=>'Reporte eliminado']);
    }
}
