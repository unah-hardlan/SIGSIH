<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Parametro;

class StorePersonaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Leer un solo parámetro de formato: "FORMATO DNI".
        // Soporta:
        //  - máscara tipo 0000-0000-00000 (0=digito, otros simbolos literales)
        //  - un número mínimo de dígitos, ej. "8" => ^\d{8,}$
        $format = Parametro::where('parametro', 'FORMATO DNI')->value('valor');
        $dniRegex = $this->buildDniRegex($format);
        $dniRule = 'regex:/' . $dniRegex . '/';
        return [
            'primer_nombre' => 'required|string|max:50',
            'segundo_nombre' => 'nullable|string|max:50',
            'primer_apellido' => 'required|string|max:50',
            'segundo_apellido' => 'nullable|string|max:50',
            'dni' => 'required|string|max:20|' . $dniRule . '|unique:tbl_persona,dni',
            'id_genero_fk' => 'required|integer|exists:tbl_genero,id_genero_pk',
            'id_usuario_fk' => 'nullable|integer|exists:tbl_ms_usuario,id_usuario_pk',
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
        // Solo dígitos: interpretar como mínimo de dígitos
        if (preg_match('/^\\d+$/', $format)) {
            $min = max(1, (int)$format);
            return '^\\d{' . $min . ',}$';
        }
        // Interpretar máscara donde '0' significa un dígito y otros caracteres son literales
        $regex = '^';
        $len = strlen($format);
        for ($i = 0; $i < $len; $i++) {
            $ch = $format[$i];
            if ($ch === '0') {
                $regex .= '\\d';
            } else {
                // escapar caracteres regex especiales
                if (preg_match('/[.\\+*?\[^\]$(){}=!<>|:-]/', $ch)) {
                    $regex .= '\\' . $ch;
                } else {
                    $regex .= $ch;
                }
            }
        }
        $regex .= '$';
        // Asegurar escape de '/'
        return str_replace('/', '\/', $regex);
    }
}
