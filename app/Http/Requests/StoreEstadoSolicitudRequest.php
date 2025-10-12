<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEstadoSolicitudRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Aquí puedes poner lógica de autorización si es necesario.
        // Por ahora, `true` permite que cualquier usuario autenticado lo use.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100|unique:tbl_estado_solicitud,nombre',
            'codigo' => 'required|string|max:50|unique:tbl_estado_solicitud,codigo',
            'descripcion' => 'nullable|string|max:255',
            'es_final' => 'required|boolean',
            'orden' => 'required|integer',
        ];
    }
}