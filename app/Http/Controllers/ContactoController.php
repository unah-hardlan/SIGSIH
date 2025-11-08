<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use Illuminate\Http\Request;
use App\Http\Resources\ContactoResource;

class ContactoController extends Controller
{
    public function index()
    {
        $query = Contacto::with(['cliente.empresa']);

        
        if ($q = request('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('tipo_contacto', 'like', "%$q%")
                    ->orWhere('valor_contacto', 'like', "%$q%")
                    ->orWhereHas('cliente.empresa', function ($empresaQuery) use ($q) {
                        $empresaQuery->where('nombre_comercial', 'like', "%$q%")
                                     ->orWhere('razon_social', 'like', "%$q%");
                    });
            });
        }

        if ($tipo = request('tipo')) {
            $query->where('tipo_contacto', 'like', "%$tipo%");
        }

        if ($cliente = request('cliente')) {
            $query->where('id_cliente_fk', $cliente);
        }

        
        $sortable = [
            'tipo' => 'tipo_contacto',
            'valor' => 'valor_contacto',
            'cliente' => 'id_cliente_fk',
            'id' => 'id_contacto_pk',
        ];
        $sort = request('sort');
        $direction = strtolower(request('direction','asc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            
            $query->orderBy('id_contacto_pk', 'desc');
        }

        $perPage = (int) request('per_page', 15);
        $contactos = $query->paginate($perPage);

        return ContactoResource::collection($contactos)->additional([
            'meta' => [
                'page' => $contactos->currentPage(),
                'per_page' => $contactos->perPage(),
                'total' => $contactos->total(),
                'last_page' => $contactos->lastPage(),
            ]
        ]);
    }

    public function create() {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_contacto' => 'required|string|max:50',
            'valor_contacto' => 'required|string|max:255',
            'id_cliente_fk' => 'required|integer|exists:tbl_cliente,id_cliente_pk',
        ]);

        $contacto = Contacto::create($validated);
        return (new ContactoResource($contacto->load(['cliente.empresa'])))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $contacto = Contacto::with(['cliente.empresa'])->find($id);
        if (!$contacto) {
            return response()->json(['error' => 'Contacto no encontrado'], 404);
        }
        return (new ContactoResource($contacto))->response();
    }

    public function edit(string $id) {}

    public function update(Request $request, $id)
    {
        $contacto = Contacto::find($id);
        if (!$contacto) {
            return response()->json(['error' => 'Contacto no encontrado'], 404);
        }

        $validated = $request->validate([
            'tipo_contacto' => 'sometimes|required|string|max:50',
            'valor_contacto' => 'sometimes|required|string|max:255',
            'id_cliente_fk' => 'sometimes|required|integer|exists:tbl_cliente,id_cliente_pk',
        ]);

        $contacto->update($validated);
        return (new ContactoResource($contacto->load(['cliente.empresa'])))->response();
    }

    public function destroy($id)
    {
        $contacto = Contacto::find($id);
        if (!$contacto) {
            return response()->json(['error' => 'Contacto no encontrado'], 404);
        }

        $contacto->delete();
        return response()->json(['message' => 'Contacto eliminado correctamente'], 200);
    }
}
