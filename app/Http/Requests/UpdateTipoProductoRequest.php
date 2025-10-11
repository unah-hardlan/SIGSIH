<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\TipoProducto;

class UpdateTipoProductoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        // Try several ways to determine the current resource ID from route parameters.
        $id = null;

        // First try the most common parameter names
        $routeParam = $this->route('tipo_producto') ?? $this->route('tipo') ?? $this->route('tipo_producto_id') ?? $this->route('id');
        if ($routeParam) {
            if (is_object($routeParam)) {
                if (method_exists($routeParam, 'getKey')) {
                    $id = $routeParam->getKey();
                } elseif (isset($routeParam->id_tipo_producto_pk)) {
                    $id = $routeParam->id_tipo_producto_pk;
                } elseif (isset($routeParam->id)) {
                    $id = $routeParam->id;
                }
            } else {
                // numeric string or int
                if (is_numeric($routeParam) || ctype_digit((string)$routeParam)) {
                    $id = (int)$routeParam;
                }
            }
        }

        // If not found yet, scan all route parameters for a numeric ID or model instance
        if (empty($id)) {
            foreach ($this->route()->parameters() as $p) {
                if (is_null($p)) continue;
                if (is_numeric($p) || ctype_digit((string)$p)) {
                    $id = (int)$p;
                    break;
                }
                if (is_object($p)) {
                    if ($p instanceof TipoProducto && method_exists($p, 'getKey')) {
                        $id = $p->getKey();
                        break;
                    }
                    if (method_exists($p, 'getKey')) {
                        $id = $p->getKey();
                        break;
                    }
                    if (isset($p->id_tipo_producto_pk)) {
                        $id = $p->id_tipo_producto_pk;
                        break;
                    }
                    if (isset($p->id)) {
                        $id = $p->id;
                        break;
                    }
                }
            }
        }

        // Use the fluent Rule API to ignore the current record's primary key when validating uniqueness
        $uniqueRule = Rule::unique('tbl_tipo_producto', 'nombre_tipo_producto');
        if (!empty($id)) {
            $uniqueRule = $uniqueRule->ignore($id, 'id_tipo_producto_pk');
        }

        return [
            'nombre_tipo_producto' => ['sometimes', 'required', 'string', 'max:50', $uniqueRule],
            'descripcion_tipo_producto' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_tipo_producto.unique' => 'Ya existe un tipo de producto con ese nombre.',
        ];
    }
}
