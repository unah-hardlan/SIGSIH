<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrigenRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    protected function prepareForValidation(): void
    {
        if ($this->has('activo')) {
            $this->merge([
                'activo' => filter_var($this->input('activo'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }
    public function rules(): array
    {
        
        $route = $this->route();
        $paramCandidates = [
            'origen',        
            'origene',       
            'origenes',      
            'id',
        ];
        $routeParam = null;
        foreach ($paramCandidates as $name) {
            $val = $route?->parameter($name);
            if (!is_null($val)) { $routeParam = $val; break; }
        }
        if (is_null($routeParam)) {
            
            foreach ((array)($route?->parameters() ?? []) as $val) {
                if (is_object($val) && method_exists($val, 'getKey')) { $routeParam = $val->getKey(); break; }
                if (is_scalar($val) && is_numeric($val)) { $routeParam = $val; break; }
            }
        }
        $id = is_object($routeParam) && method_exists($routeParam, 'getKey') ? $routeParam->getKey() : $routeParam;
        return [
            'nombre_origen' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                
                Rule::unique('tbl_origen', 'nombre_origen')->ignore($id, 'id_origen_pk'),
            ],
            'descripcion_origen' => 'sometimes|nullable|string|max:255',
            'activo' => 'sometimes|boolean',
        ];
    }
}
