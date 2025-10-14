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
    $query = Solicitud::with(['cliente.empresa', 'estadoSolicitud', 'contacto']);

        // Filtros opcionales
        if ($request->filled('id_cliente_fk')) {
            $query->where('id_cliente_fk', $request->id_cliente_fk);
        }

        if ($request->filled('id_estado_solicitud_fk')) {
            $query->where('id_estado_solicitud_fk', $request->id_estado_solicitud_fk);
        }

        if ($request->filled('numero_solicitud_acf')) {
            $query->where('numero_solicitud_acf', (int) $request->numero_solicitud_acf);
        }

        if ($request->filled('numero_solicitud_cliente')) {
            $query->where('numero_solicitud_cliente', (int) $request->numero_solicitud_cliente);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($sub) use ($search) {
                $sub->where('descripcion_problema', 'like', "%{$search}%")
                    ->orWhereHas('cliente.empresa', function ($empresaQuery) use ($search) {
                        $empresaQuery->where('nombre_comercial', 'like', "%{$search}%")
                                     ->orWhere('razon_social', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = (int) $request->input('per_page', 15);
        $solicitudes = $query->paginate(max(1, $perPage));

        return SolicitudResource::collection($solicitudes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_cliente_fk' => 'required|integer|exists:tbl_cliente,id_cliente_pk',
            'descripcion_problema' => 'required|string|max:500',
            'id_estado_solicitud_fk' => 'required|integer|exists:tbl_estado_solicitud,id_estado_solicitud_pk',
            'id_contacto_fk' => 'required|integer|exists:tbl_contacto,id_contacto_pk'
        ]);

        // Autogenerar números correlativos
        $maxAcf = \App\Models\Solicitud::max('numero_solicitud_acf');
        $nextAcf = (int)($maxAcf ?? 0) + 1;

        // Cliente puede tener su propio correlativo; si no, usar global similar al ACF
        $maxCli = \App\Models\Solicitud::where('id_cliente_fk', $validated['id_cliente_fk'])->max('numero_solicitud_cliente');
        $nextCli = (int)($maxCli ?? 0) + 1;

        $solicitud = new \App\Models\Solicitud();
        $solicitud->fill($validated);
        $solicitud->numero_solicitud_acf = $nextAcf;
        $solicitud->numero_solicitud_cliente = $nextCli;
        $solicitud->save();

    return new SolicitudResource($solicitud->load(['cliente.empresa', 'estadoSolicitud', 'contacto']));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
    $solicitud = Solicitud::with(['cliente.empresa', 'estadoSolicitud', 'contacto'])->findOrFail($id);
        return new SolicitudResource($solicitud);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $solicitud = Solicitud::with(['cliente.empresa', 'estadoSolicitud', 'contacto'])->findOrFail($id);

        $validatedData = $request->validate([
            'id_cliente_fk' => 'sometimes|required|integer|exists:tbl_cliente,id_cliente_pk',
            'descripcion_problema' => 'sometimes|required|string|max:500',
            'id_estado_solicitud_fk' => 'sometimes|required|integer|exists:tbl_estado_solicitud,id_estado_solicitud_pk',
            'id_contacto_fk' => 'sometimes|required|integer|exists:tbl_contacto,id_contacto_pk'
        ]);

        $solicitud->update($validatedData);
        return new SolicitudResource($solicitud->load(['cliente.empresa', 'estadoSolicitud', 'contacto']));
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
