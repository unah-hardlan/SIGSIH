<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Http\Resources\ServicioResource;
use App\Http\Requests\StoreServicioRequest;
use App\Http\Requests\UpdateServicioRequest;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    public function index()
    {
        $servicios = Servicio::paginate(10);
        return ServicioResource::collection($servicios);
    }

    public function store(StoreServicioRequest $request)
    {
        $servicio = Servicio::create($request->validated());
        return new ServicioResource($servicio);
    }

    public function show(Servicio $servicio)
    {
        return new ServicioResource($servicio);
    }

    public function update(UpdateServicioRequest $request, Servicio $servicio)
    {
        $servicio->update($request->validated());
        return new ServicioResource($servicio);
    }

    public function destroy(Servicio $servicio)
    {
        $servicio->delete();
        return response()->json(['success' => true, 'message' => 'Servicio eliminado']);
    }
}
