<?php

namespace App\Http\Controllers;

use App\Models\Direccion;
use App\Models\Ciudad;
use App\Http\Resources\DireccionResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DireccionesController extends Controller
{
    
    protected function normalize(string $value): string
    {
        $v = trim($value);
        
        $replacements = [
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ü' => 'U', 'Ñ' => 'N',
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n',
        ];
        $v = strtr($v, $replacements);
        return mb_strtolower($v, 'UTF-8');
    }

    
    protected function getPaisNombreByCiudadId(?int $ciudadId): ?string
    {
        if (!$ciudadId) return null;
        $ciudad = Ciudad::with('departamento.pais')->find($ciudadId);
        return $ciudad?->departamento?->pais?->nombre_pais;
    }

    
    protected function validarCodigoPostalPorPais(string $codigoPostal, ?string $paisNombre): array
    {
        $cp = trim($codigoPostal);
        if ($cp === '') {
            return [true, '']; 
        }

        $paisNorm = $paisNombre ? $this->normalize($paisNombre) : '';

        
        $map = [
            'honduras'    => '/^\d{5}$/',
            'guatemala'   => '/^\d{5}$/',
            'costa rica'  => '/^\d{5}$/',
            'el salvador' => '/^\d{4}$/',
            'nicaragua'   => '/^\d{5}$/',
            'panama'      => '/^\d{6}$/',
            'panamá'      => '/^\d{6}$/',
            'belice'      => '/^[A-Za-z0-9\-\s]{3,10}$/i',
            'belize'      => '/^[A-Za-z0-9\-\s]{3,10}$/i',
        ];

        
        $hints = [
            'honduras'    => '5 dígitos (ej. 11101)',
            'guatemala'   => '5 dígitos (ej. 01001)',
            'costa rica'  => '5 dígitos (ej. 10101)',
            'el salvador' => '4 dígitos (ej. 1101)',
            'nicaragua'   => '5 dígitos',
            'panama'      => '6 dígitos',
            'panamá'      => '6 dígitos',
            'belice'      => '3-10 caracteres alfanuméricos',
            'belize'      => '3-10 caracteres alfanuméricos',
        ];

        
        $pattern = $map[$paisNorm] ?? '/^(?=.{3,10}$)[A-Za-z0-9\-\s]+$/';
        $hint = $hints[$paisNorm] ?? '3-10 caracteres alfanuméricos';

        $valido = (bool) preg_match($pattern, $cp);
        return [$valido, $hint];
    }
    
    public function index(Request $request): JsonResponse
    {
        $query = Direccion::with(['ciudad.departamento.pais', 'agencia']);

        
        if ($request->has('id_ciudad_fk')) {
            $query->where('id_ciudad_fk', $request->id_ciudad_fk);
        }

        $direcciones = $query->orderBy('id_direccion_pk', 'asc')
                           ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => DireccionResource::collection($direcciones->items()),
            'pagination' => [
                'current_page' => $direcciones->currentPage(),
                'per_page' => $direcciones->perPage(),
                'total' => $direcciones->total(),
                'last_page' => $direcciones->lastPage(),
                'from' => $direcciones->firstItem(),
                'to' => $direcciones->lastItem()
            ]
        ]);
    }

    
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_ciudad_fk' => 'required|exists:tbl_ciudad,id_ciudad_pk',
            'calle' => 'required|string|max:100',
            'numero' => 'required|string|max:20',
            'colonia' => 'required|string|max:100',
            'codigo_postal' => 'nullable|string|max:10',
            'referencia' => 'nullable|string',
            'agencia_id' => 'nullable|exists:tbl_agencias,id_agencias_pk'
        ]);

        
        if (array_key_exists('codigo_postal', $validated) && $validated['codigo_postal'] !== null) {
            $paisNombre = $this->getPaisNombreByCiudadId((int) $validated['id_ciudad_fk']);
            [$ok, $hint] = $this->validarCodigoPostalPorPais((string) $validated['codigo_postal'], $paisNombre);
            if (!$ok) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => [
                        'codigo_postal' => [
                            'Código postal inválido para ' . ($paisNombre ?: 'el país seleccionado') . '. Formato esperado: ' . $hint . '.'
                        ]
                    ]
                ], 422);
            }
        }

        $direccion = Direccion::create($validated);
    $direccion->load(['ciudad.departamento.pais', 'agencia']);

        return response()->json([
            'success' => true,
            'message' => 'Dirección creada exitosamente',
            'data' => new DireccionResource($direccion)
        ], 201);
    }

    
    public function show(string $id): JsonResponse
    {
        $direccion = Direccion::with(['ciudad.departamento.pais', 'agencia'])->find($id);

        if (!$direccion) {
            return response()->json([
                'success' => false,
                'message' => 'Dirección no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new DireccionResource($direccion)
        ]);
    }

    
    public function update(Request $request, string $id): JsonResponse
    {
        $direccion = Direccion::find($id);

        if (!$direccion) {
            return response()->json([
                'success' => false,
                'message' => 'Dirección no encontrada'
            ], 404);
        }

        $validated = $request->validate([
            'id_ciudad_fk' => 'sometimes|required|exists:tbl_ciudad,id_ciudad_pk',
            'calle' => 'sometimes|required|string|max:100',
            'numero' => 'sometimes|required|string|max:20',
            'colonia' => 'sometimes|required|string|max:100',
            'codigo_postal' => 'nullable|string|max:10',
            'referencia' => 'nullable|string',
            'agencia_id' => 'nullable|exists:tbl_agencias,id_agencias_pk'
        ]);

        
        $ciudadId = array_key_exists('id_ciudad_fk', $validated)
            ? (int) $validated['id_ciudad_fk']
            : (int) $direccion->id_ciudad_fk;

        if ($request->has('codigo_postal')) {
            $cp = $request->input('codigo_postal');
            if ($cp !== null) {
                $paisNombre = $this->getPaisNombreByCiudadId($ciudadId);
                [$ok, $hint] = $this->validarCodigoPostalPorPais((string) $cp, $paisNombre);
                if (!$ok) {
                    return response()->json([
                        'message' => 'The given data was invalid.',
                        'errors' => [
                            'codigo_postal' => [
                                'Código postal inválido para ' . ($paisNombre ?: 'el país seleccionado') . '. Formato esperado: ' . $hint . '.'
                            ]
                        ]
                    ], 422);
                }
            }
        }

        $direccion->update($validated);
    $direccion->load(['ciudad.departamento.pais', 'agencia']);

        return response()->json([
            'success' => true,
            'message' => 'Dirección actualizada exitosamente',
            'data' => new DireccionResource($direccion)
        ]);
    }

    
    public function destroy(string $id): JsonResponse
    {
        $direccion = Direccion::find($id);

        if (!$direccion) {
            return response()->json([
                'success' => false,
                'message' => 'Dirección no encontrada'
            ], 404);
        }

        
        if ($direccion->agencias()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la dirección porque está asociada a una o más agencias'
            ], 400);
        }

        try {
            $direccion->delete();

            return response()->json([
                'success' => true,
                'message' => 'Dirección eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la dirección: ' . $e->getMessage()
            ], 500);
        }
    }
}
