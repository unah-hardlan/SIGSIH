<?php

namespace App\Http\Controllers;

use App\Models\EstadoFactura;
use App\Http\Resources\EstadoFacturaResource;
use App\Http\Requests\StoreEstadoFacturaRequest;
use App\Http\Requests\UpdateEstadoFacturaRequest;
use Illuminate\Http\Request;

class EstadoFacturaController extends Controller
{
    public function index()
    {
        $estados = EstadoFactura::paginate(10);
        return EstadoFacturaResource::collection($estados);
    }

    public function store(StoreEstadoFacturaRequest $request)
    {
        $estado = EstadoFactura::create($request->validated());
        return new EstadoFacturaResource($estado);
    }

    public function show(EstadoFactura $estadoFactura)
    {
        return new EstadoFacturaResource($estadoFactura);
    }

    public function update(UpdateEstadoFacturaRequest $request, EstadoFactura $estadoFactura)
    {
        $estadoFactura->update($request->validated());
        return new EstadoFacturaResource($estadoFactura);
    }

    public function destroy(EstadoFactura $estadoFactura)
    {
        $estadoFactura->delete();
        return response()->json(['success' => true, 'message' => 'Estado de factura eliminado']);
    }
}
