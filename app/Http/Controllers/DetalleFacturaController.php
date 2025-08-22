<?php

namespace App\Http\Controllers;

use App\Models\DetalleFactura;
use App\Http\Resources\DetalleFacturaResource;
use App\Http\Requests\StoreDetalleFacturaRequest;
use App\Http\Requests\UpdateDetalleFacturaRequest;
use Illuminate\Http\Request;

class DetalleFacturaController extends Controller
{
    public function index()
    {
        $detalles = DetalleFactura::with(['factura', 'servicio'])->paginate(10);
        return DetalleFacturaResource::collection($detalles);
    }

    public function store(StoreDetalleFacturaRequest $request)
    {
        $detalle = DetalleFactura::create($request->validated());
        $detalle->load(['factura', 'servicio']);
        return new DetalleFacturaResource($detalle);
    }

    public function show(DetalleFactura $detalleFactura)
    {
        $detalleFactura->load(['factura', 'servicio']);
        return new DetalleFacturaResource($detalleFactura);
    }

    public function update(UpdateDetalleFacturaRequest $request, DetalleFactura $detalleFactura)
    {
        $detalleFactura->update($request->validated());
        $detalleFactura->load(['factura', 'servicio']);
        return new DetalleFacturaResource($detalleFactura);
    }

    public function destroy(DetalleFactura $detalleFactura)
    {
        $detalleFactura->delete();
        return response()->json(['success' => true, 'message' => 'Detalle de factura eliminado']);
    }
}
