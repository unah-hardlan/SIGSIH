<?php

namespace App\Http\Controllers;

use App\Models\EstadoCai;
use App\Http\Resources\EstadoCaiResource;
use App\Http\Requests\StoreEstadoCaiRequest;
use App\Http\Requests\UpdateEstadoCaiRequest;
use Illuminate\Http\Request;

class EstadoCaiController extends Controller
{
    public function index()
    {
        $estados = EstadoCai::paginate(10);
        return EstadoCaiResource::collection($estados);
    }

    public function store(StoreEstadoCaiRequest $request)
    {
        $estado = EstadoCai::create($request->validated());
        return new EstadoCaiResource($estado);
    }

    public function show(EstadoCai $estadoCai)
    {
        return new EstadoCaiResource($estadoCai);
    }

    public function update(UpdateEstadoCaiRequest $request, EstadoCai $estadoCai)
    {
        $estadoCai->update($request->validated());
        return new EstadoCaiResource($estadoCai);
    }

    public function destroy(EstadoCai $estadoCai)
    {
        $estadoCai->delete();
        return response()->json(['success' => true, 'message' => 'Estado CAI eliminado']);
    }
}
