<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Parametro;

class UpdatePersonaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('persona') ?? $this->route('id');
        $format = Parametro::where('parametro', 'FORMATO DNI')->value('valor');
        $dniRegex = $this->buildDniRegex($format);
        $dniRule = 'regex:/' . $dniRegex . '/';
        return [
            // Siempre obligatorios
            'primer_nombre' => 'required|string|max:50',
            'segundo_nombre' => 'sometimes|nullable|string|max:50',
            'primer_apellido' => 'required|string|max:50',
            'segundo_apellido' => 'sometimes|nullable|string|max:50',
            'dni' => 'sometimes|required|string|max:20|' . $dniRule . '|unique:tbl_persona,dni,' . $id . ',id_persona_pk',
            'id_genero_fk' => 'sometimes|required|integer|exists:tbl_genero,id_genero_pk',
            'id_usuario_fk' => 'sometimes|nullable|integer|exists:tbl_ms_usuario,id_usuario_pk',
        ];
    }

    public function messages(): array
    {
        $format = \App\Models\Parametro::where('parametro', 'FORMATO DNI')->value('valor');
        $hint = '';
        if (is_string($format) && trim($format) !== '') {
            $format = trim($format);
            if (preg_match('/^\\d+$/', $format)) {
                $hint = ' Debe contener al menos ' . $format . ' dígitos.';
            } else {
                $hint = ' El formato es: ' . $format . '.';
            }
        }
        return [
            'dni.regex' => 'El DNI no cumple con el formato.' . $hint,
            'dni.unique' => 'El DNI ya está registrado.',
            'dni.required' => 'El DNI es obligatorio.',
            'primer_nombre.required' => 'El primer nombre es obligatorio.',
            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'id_genero_fk.required' => 'El género es obligatorio.',
        ];
    }

    public function attributes(): array
    {
        return [
            'dni' => 'DNI',
            'primer_nombre' => 'primer nombre',
            'segundo_nombre' => 'segundo nombre',
            'primer_apellido' => 'primer apellido',
            'segundo_apellido' => 'segundo apellido',
            'id_genero_fk' => 'género',
        ];
    }

    /**
     * Convierte la máscara o número mínimo en una expresión regular usable.
     */
    private function buildDniRegex($format): string
    {
        $fallback = '^(?:\\d{13}|\\d{4}-\\d{4}-\\d{5})$';
        if (!is_string($format) || trim($format) === '') {
            return str_replace('/', '\/', $fallback);
        }
        $format = trim($format);
        if (preg_match('/^\\d+$/', $format)) {
            $min = max(1, (int)$format);
            return '^\\d{' . $min . ',}$';
        }
        $regex = '^';
        $len = strlen($format);
        for ($i = 0; $i < $len; $i++) {
            $ch = $format[$i];
            if ($ch === '0') {
                $regex .= '\\d';
            } else {
                if (preg_match('/[.\\+*?\[^\]$(){}=!<>|:-]/', $ch)) {
                    $regex .= '\\' . $ch;
                } else {
                    $regex .= $ch;
                }
            }
        }
        $regex .= '$';
        return str_replace('/', '\/', $regex);
    }
}
