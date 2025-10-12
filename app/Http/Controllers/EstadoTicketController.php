<?php

namespace App\Http\Controllers;

use App\Models\EstadoTicket;
use App\Http\Resources\EstadoTicketResource;
use App\Http\Requests\StoreEstadoTicketRequest;
use App\Http\Requests\UpdateEstadoTicketRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EstadoTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = EstadoTicket::query();

        // Filtro por nombre del estado
        if ($request->has('nombre')) {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }

        // Filtro por descripción
        if ($request->has('descripcion')) {
            $query->where('descripcion', 'like', '%' . $request->descripcion . '%');
        }

        $estadosTicket = $query->orderBy('nombre')
                              ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => EstadoTicketResource::collection($estadosTicket->items()),
            'pagination' => [
                'current_page' => $estadosTicket->currentPage(),
                'per_page' => $estadosTicket->perPage(),
                'total' => $estadosTicket->total(),
                'last_page' => $estadosTicket->lastPage(),
                'from' => $estadosTicket->firstItem(),
                'to' => $estadosTicket->lastItem()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEstadoTicketRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $estadoTicket = EstadoTicket::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'codigo' => $validated['codigo'],
            'es_final' => $validated['es_final'],
            'orden' => $validated['orden'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado de ticket creado exitosamente',
            'data' => new EstadoTicketResource($estadoTicket)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $estadoTicket = EstadoTicket::find($id);

        if (!$estadoTicket) {
            return response()->json([
                'success' => false,
                'message' => 'Estado de ticket no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new EstadoTicketResource($estadoTicket)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEstadoTicketRequest $request, string $id): JsonResponse
    {
        $estadoTicket = EstadoTicket::find($id);

        if (!$estadoTicket) {
            return response()->json([
                'success' => false,
                'message' => 'Estado de ticket no encontrado'
            ], 404);
        }

        $validated = $request->validated();
        $estadoTicket->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'codigo' => $validated['codigo'],
            'es_final' => $validated['es_final'],
            'orden' => $validated['orden'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado de ticket actualizado exitosamente',
            'data' => new EstadoTicketResource($estadoTicket)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $estadoTicket = EstadoTicket::find($id);

        if (!$estadoTicket) {
            return response()->json([
                'success' => false,
                'message' => 'Estado de ticket no encontrado'
            ], 404);
        }

        $estadoTicket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Estado de ticket eliminado exitosamente'
        ]);
    }
}
