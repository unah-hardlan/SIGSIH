<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrigenRequest extends FormRequest
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
        return [
            'nombre_origen' => 'required|string|max:50|unique:tbl_origen,nombre_origen',
            'descripcion_origen' => 'nullable|string|max:255',
            'activo' => 'sometimes|boolean',
        ];
    }
}
