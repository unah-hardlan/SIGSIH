<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Http\Resources\SolicitudResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SolicitudController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Solicitud::with(['cliente', 'estadoSolicitud', 'contacto']);

        // Filtros opcionales
        if ($request->has('id_cliente_fk')) {
            $query->where('id_cliente_fk', $request->id_cliente_fk);
        }

        if ($request->has('id_estado_solicitud_fk')) {
            $query->where('id_estado_solicitud_fk', $request->id_estado_solicitud_fk);
        }

        if ($request->has('numero_solicitud_acf')) {
            $query->where('numero_solicitud_acf', 'like', '%' . $request->numero_solicitud_acf . '%');
        }

        if ($request->has('numero_solicitud_cliente')) {
            $query->where('numero_solicitud_cliente', 'like', '%' . $request->numero_solicitud_cliente . '%');
        }

        $solicitudes = $query->paginate(15);

        return SolicitudResource::collection($solicitudes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_cliente_fk' => 'required|integer|exists:tbl_ms_usuario,id_usuario_pk',
            'numero_solicitud_acf' => 'required|integer|unique:tbl_solicitud,numero_solicitud_acf',
            'numero_solicitud_cliente' => 'nullable|integer',
            'descripcion_problema' => 'required|string|max:500',
            'id_estado_solicitud_fk' => 'required|integer|exists:tbl_estado_solicitud,id_estado_solicitud_pk',
            'id_contacto_fk' => 'required|integer|exists:tbl_contacto,id_contacto_pk'
        ]);

        $solicitud = Solicitud::create($validatedData);
        $solicitud->load(['cliente', 'estadoSolicitud', 'contacto']);

        return new SolicitudResource($solicitud);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $solicitud = Solicitud::with(['cliente', 'estadoSolicitud', 'contacto'])->findOrFail($id);
        return new SolicitudResource($solicitud);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $solicitud = Solicitud::findOrFail($id);

        $validatedData = $request->validate([
            'id_cliente_fk' => 'sometimes|required|integer|exists:tbl_ms_usuario,id_usuario_pk',
            'numero_solicitud_acf' => 'sometimes|required|integer|unique:tbl_solicitud,numero_solicitud_acf,' . $id . ',id_solicitud_pk',
            'numero_solicitud_cliente' => 'nullable|integer',
            'descripcion_problema' => 'sometimes|required|string|max:500',
            'id_estado_solicitud_fk' => 'sometimes|required|integer|exists:tbl_estado_solicitud,id_estado_solicitud_pk',
            'id_contacto_fk' => 'sometimes|required|integer|exists:tbl_contacto,id_contacto_pk'
        ]);

        $solicitud->update($validatedData);
        $solicitud->load(['cliente', 'estadoSolicitud', 'contacto']);

        return new SolicitudResource($solicitud);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $solicitud = Solicitud::findOrFail($id);
        $solicitud->delete();

        return response()->json([
            'message' => 'Solicitud eliminada correctamente'
        ], Response::HTTP_OK);
    }
}
