<?php

namespace App\Http\Controllers;

use App\Models\Kardex;
use Illuminate\Http\Request;
use App\Http\Requests\StoreKardexRequest;
use App\Http\Requests\UpdateKardexRequest;
use App\Http\Resources\KardexResource;

class KardexController extends Controller
{
    public function index(Request $request)
    {
        // Esta línea es crucial: carga todas las relaciones necesarias
        $query = Kardex::query()->with(['producto', 'tipoMovimiento', 'origen']);

        // Obtener parámetros de ordenamiento
        $ordenarPor = $request->input('ordenarPor', 'fecha_movimiento');
        $ordenarDirection = $request->input('ordenarDirection', 'desc');

        // Validar columna y dirección
        $allowedColumns = ['fecha_movimiento', 'cantidad'];
        $allowedDirections = ['asc', 'desc'];
        if (!in_array($ordenarPor, $allowedColumns)) {
            $ordenarPor = 'fecha_movimiento';
        }
        if (!in_array($ordenarDirection, $allowedDirections)) {
            $ordenarDirection = 'desc';
        }

        $query->orderBy($ordenarPor, $ordenarDirection);

        $kardexItems = $query->get();

        return KardexResource::collection($kardexItems);
    }

    public function store(StoreKardexRequest $request)
    {
        $kardex = Kardex::create($request->validated());
        $kardex->load(['producto', 'tipoMovimiento', 'origen']);
        return (new KardexResource($kardex))->response()->setStatusCode(201);
    }

    public function show(Kardex $kardex)
    {
        $kardex->load(['producto', 'tipoMovimiento']);
        return new KardexResource($kardex);
    }

    public function update(UpdateKardexRequest $request, Kardex $kardex)
    {
        $kardex->update($request->validated());
        $kardex->load(['producto', 'tipoMovimiento']);
        return new KardexResource($kardex);
    }

    public function destroy(Kardex $kardex)
    {
        $kardex->delete();
        return response()->json(null, 204);
    }
}