<?php

namespace App\Http\Controllers;

use App\Models\AccionRealizada;
use App\Http\Requests\StoreAccionRealizadaRequest;
use App\Http\Requests\UpdateAccionRealizadaRequest;
use App\Http\Resources\AccionRealizadaResource;
use Illuminate\Http\Request;

class AccionRealizadaController extends Controller
{
    public function index(Request $request)
    {
        $query = AccionRealizada::query();

        if ($q = $request->input('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('nombre_accion', 'like', "%$q%")
                    ->orWhere('descripcion_accion', 'like', "%$q%");
            });
        }

        $sortable = [
            'nombre' => 'nombre_accion',
            'id_accion_realizada_pk' => 'id_accion_realizada_pk'
        ];

        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            $query->orderBy('nombre_accion', 'asc');
        }
        
        $acciones = $query->get();

        return AccionRealizadaResource::collection($acciones);
    }

    public function store(StoreAccionRealizadaRequest $request)
    {
        $accion = AccionRealizada::create($request->validated());
        
        return (new AccionRealizadaResource($accion))
                ->response()
                ->setStatusCode(201);
    }

    public function show(AccionRealizada $acciones_realizada)
    {
        return new AccionRealizadaResource($acciones_realizada);
    }

    public function update(UpdateAccionRealizadaRequest $request, AccionRealizada $acciones_realizada)
    {
        $acciones_realizada->update($request->validated());
        
        return new AccionRealizadaResource($acciones_realizada);
    }

    public function destroy(AccionRealizada $acciones_realizada)
    {
        $acciones_realizada->delete();
        
        return response()->json(null, 204);
    }
}