<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTicketRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'fecha_creacion' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $tz = $this->header('X-Timezone') ?: config('app.timezone', 'UTC');
                    try {
                        $selectedDate = Carbon::parse($value, $tz)->toDateString();
                        $todayDate = Carbon::now($tz)->toDateString();
                        if ($selectedDate < $todayDate) {
                            $fail('La fecha de creación no puede ser anterior al día actual');
                        }
                    } catch (\Throwable $e) {
                        $fail('La fecha de creación debe ser una fecha válida');
                    }
                },
            ],
            'descripcion_ticket' => 'required|string|max:500',
            'id_estado_ticket_fk' => 'required|integer|exists:tbl_estado_ticket,id_estado_ticket_pk',
            'id_tecnico_fk' => 'required|integer|exists:tbl_persona,id_persona_pk',
            'id_cliente_fk' => 'required|integer|exists:tbl_cliente,id_cliente_pk'
        ];
    }


    public function messages(): array
    {
        return [
            'fecha_creacion.required' => 'La fecha de creación es obligatoria',
            'fecha_creacion.date' => 'La fecha de creación debe ser una fecha válida',
            'descripcion_ticket.required' => 'La descripción del ticket es obligatoria',
            'descripcion_ticket.max' => 'La descripción no puede exceder 500 caracteres',
            'id_estado_ticket_fk.required' => 'El estado del ticket es obligatorio',
            'id_estado_ticket_fk.exists' => 'El estado del ticket especificado no existe',
            'id_tecnico_fk.required' => 'El técnico asignado es obligatorio',
            'id_tecnico_fk.exists' => 'La persona especificada para técnico no existe',
            'id_cliente_fk.required' => 'El cliente es obligatorio',
            'id_cliente_fk.exists' => 'El cliente especificado no existe'
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $validator->errors()
        ], 422));
    }
}
