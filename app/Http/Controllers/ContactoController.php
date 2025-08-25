<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use Illuminate\Http\Request;
use App\Http\Resources\ContactoResource;

class ContactoController extends Controller
{
    public function index()
    {
        $query = Contacto::with(['persona']);

        // Filtros
        if ($q = request('q')) {
            $query->where(function($sub) use ($q) {
                $sub->where('tipo_contacto', 'like', "%$q%")
                    ->orWhere('valor_contacto', 'like', "%$q%")
                    ->orWhereHas('persona', function($personaQuery) use ($q) {
                        $personaQuery->where('nombre_persona', 'like', "%$q%");
                    });
            });
        }

        if ($tipo = request('tipo')) {
            $query->where('tipo_contacto', 'like', "%$tipo%");
        }

        if ($persona = request('persona')) {
            $query->where('id_persona_fk', $persona);
        }

        // Ordenamiento dinámico
        $sortable = [
            'tipo' => 'tipo_contacto',
            'valor' => 'valor_contacto',
            'persona' => 'id_persona_fk',
            'id' => 'id_contacto_pk',
        ];
        $sort = request('sort');
        $direction = strtolower(request('direction','asc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            // orden por defecto
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
        $request->validate([
            'tipo_contacto' => 'required|string|max:50',
            'valor_contacto' => 'required|string|max:255',
            'id_persona_fk' => 'required|integer|exists:tbl_persona,id_persona_pk',
        ]);

        $contacto = Contacto::create($request->all());
        return (new ContactoResource($contacto->load(['persona'])))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $contacto = Contacto::with(['persona'])->find($id);
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

        $request->validate([
            'tipo_contacto' => 'sometimes|required|string|max:50',
            'valor_contacto' => 'sometimes|required|string|max:255',
            'id_persona_fk' => 'sometimes|required|integer|exists:tbl_persona,id_persona_pk',
        ]);

        $contacto->update($request->all());
        return (new ContactoResource($contacto->load(['persona'])))->response();
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
