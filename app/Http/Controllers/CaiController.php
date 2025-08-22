<?php

namespace App\Http\Controllers;

use App\Models\Cai;
use App\Http\Resources\CaiResource;
use App\Http\Requests\StoreCaiRequest;
use App\Http\Requests\UpdateCaiRequest;
use Illuminate\Http\Request;

class CaiController extends Controller
{
    public function index()
    {
        $cais = Cai::with('estadoCai')->paginate(10);
        return CaiResource::collection($cais);
    }

    public function store(StoreCaiRequest $request)
    {
        $cai = Cai::create($request->validated());
        $cai->load('estadoCai');
        return new CaiResource($cai);
    }

    public function show(Cai $cai)
    {
        $cai->load('estadoCai');
        return new CaiResource($cai);
    }

    public function update(UpdateCaiRequest $request, Cai $cai)
    {
        $cai->update($request->validated());
        $cai->load('estadoCai');
        return new CaiResource($cai);
    }

    public function destroy(Cai $cai)
    {
        $cai->delete();
        return response()->json(['success' => true, 'message' => 'CAI eliminado']);
    }
}
