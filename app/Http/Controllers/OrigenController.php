<?php

namespace App\Http\Controllers;

use App\Models\Origen;
use App\Models\Kardex;
use Illuminate\Http\Request;
use App\Http\Resources\OrigenResource;
use App\Http\Requests\StoreOrigenRequest;
use App\Http\Requests\UpdateOrigenRequest;

class OrigenController extends Controller
{
    public function index(Request $request)
    {
        $query = Origen::query();
        if ($q = $request->input('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('nombre_origen', 'like', "%$q%")
                    ->orWhere('descripcion_origen', 'like', "%$q%");
            });
        }
        $sortable = [
            'nombre' => 'nombre_origen',
            'activo' => 'activo',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortable[$sort] ?? 'id_origen_pk', $direction);

        if ($request->boolean('all')) {
            return OrigenResource::collection($query->get());
        }

        $perPage = (int) $request->input('per_page', 15);
        $items = $query->paginate($perPage);
        return OrigenResource::collection($items)->additional([
            'meta' => [
                'page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage(),
            ],
        ]);
    }

    public function store(StoreOrigenRequest $request)
    {
        $origen = Origen::create($request->validated());
        return (new OrigenResource($origen))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $origen = Origen::find($id);
        if (!$origen) return response()->json(['error' => 'Origen no encontrado'], 404);
        return (new OrigenResource($origen))->response();
    }

    public function update(UpdateOrigenRequest $request, $id)
    {
        $origen = Origen::find($id);
        if (!$origen) return response()->json(['error' => 'Origen no encontrado'], 404);
        $origen->update($request->validated());
        return (new OrigenResource($origen))->response();
    }

    public function destroy($id)
    {
        $origen = Origen::find($id);
        if (!$origen) return response()->json(['error' => 'Origen no encontrado'], 404);

        // Evitar eliminar si existe en kardex
        if (Kardex::where('id_origen_fk', $id)->exists()) {
            return response()->json(['error' => 'No se puede eliminar: existen movimientos en Kardex que referencian este origen'], 422);
        }

        $origen->delete();
        return response()->json(['message' => 'Origen eliminado correctamente']);
    }
}
