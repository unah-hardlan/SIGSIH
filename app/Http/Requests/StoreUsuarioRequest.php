<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Parametro;

class StoreUsuarioRequest extends FormRequest
{
    protected int $usuarioMinUsed = 3;
    protected ?string $correoDominioUsed = null;
    protected int $passwordMinUsed = 8;
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('usuario')) {
            // No forzamos mayúsculas aquí para poder detectar el error y mostrar mensaje adecuado
            $this->merge(['usuario' => trim($this->input('usuario'))]);
        }
        if ($this->has('correo_electronico')) {
            $this->merge(['correo_electronico' => trim($this->input('correo_electronico'))]);
        }
    }

    public function rules(): array
    {
        // Obtener parámetros configurables (con fallback a legacy si aplica)
        $correoMuestra = Parametro::whereIn('parametro',[ 'ADMIN.CORREO','ADMIN_CORREO'])
            ->orderByRaw("FIELD(parametro,'ADMIN.CORREO','ADMIN_CORREO')")
            ->value('valor');
        $usuarioMuestra = Parametro::whereIn('parametro',[ 'ADMIN.USUARIO','ADMIN_CUSER'])
            ->orderByRaw("FIELD(parametro,'ADMIN.USUARIO','ADMIN_CUSER')")
            ->value('valor');
        $passwordMuestra = Parametro::whereIn('parametro',[ 'ADMIN.PASSWORD','ADMIN_CPASS'])
            ->orderByRaw("FIELD(parametro,'ADMIN.PASSWORD','ADMIN_CPASS')")
            ->value('valor');

        // Regla para correo limitado por dominio si la muestra contiene '@'
        $emailRule = 'required|email|max:100|unique:tbl_ms_usuario,correo_electronico';
        $correoDominio = null;
        if ($correoMuestra) {
            $correoMuestra = trim($correoMuestra);
            if (str_contains($correoMuestra,'@')) {
                $correoDominio = substr(strrchr($correoMuestra,'@'),1);
            }
        }
        if ($correoDominio) {
            $correoDominio = strtolower($correoDominio);
            $this->correoDominioUsed = $correoDominio;
            $emailRule .= '|regex:/^[^@\s]+@'.preg_quote($correoDominio,'/').'$/i';
        }

        // Regla dinámica para usuario usando muestra (solo mayúsculas, dígitos y _). Longitud mínima = len muestra (>=3)
        $usuarioMin = 3;
        if ($usuarioMuestra) { $usuarioMin = max(3, strlen($usuarioMuestra)); }
    $this->usuarioMinUsed = $usuarioMin;
    $usuarioRegex = '/^[A-Z0-9_]{'.$usuarioMin.',50}$/';
        // Si la muestra parece ya una regex (tiene ^ y $) úsala directamente
        if ($usuarioMuestra && preg_match('/^\^.*\$$/',$usuarioMuestra)) {
            $usuarioRegex = $usuarioMuestra;
        }

        // Reglas de contraseña según muestra: categorías detectadas
    $minPass = 8;
        $needUpper = $needLower = $needDigit = $needSymbol = false;
        $symbolExamples = [];
        if ($passwordMuestra) {
            $minPass = max(8, strlen($passwordMuestra));
            $needUpper = (bool) preg_match('/[A-Z]/',$passwordMuestra);
            $needLower = (bool) preg_match('/[a-z]/',$passwordMuestra);
            $needDigit = (bool) preg_match('/\d/',$passwordMuestra);
            $needSymbol = (bool) preg_match('/[^A-Za-z0-9]/',$passwordMuestra);
            if ($needSymbol) {
                preg_match_all('/[^A-Za-z0-9]/',$passwordMuestra,$m);
                $symbolExamples = array_values(array_unique($m[0] ?? []));
            }
        }
        if ($needSymbol && empty($symbolExamples)) {
            $symbolExamples = ['!','@','#','$','%','&','*','?','.','_','-','+','='];
        }

    $this->passwordMinUsed = $minPass;
    $passwordRules = ['required','string','min:'.$minPass,'max:100','regex:/^\S+$/'];
        // Contraseña no igual al usuario
        $passwordRules[] = function($attribute,$value,$fail){
            $usuarioInput = strtoupper($this->input('usuario',''));
            if ($usuarioInput && strtoupper($value) === $usuarioInput) {
                $fail('La contraseña no puede ser igual al usuario.');
            }
        };
        // Palabras comunes prohibidas
        $passwordRules[] = function($attribute,$value,$fail){
            $prohibidas = ['CONTRASENA','CONTRASEÑA','PASSWORD'];
            if (in_array(strtoupper($value), $prohibidas, true)) {
                $fail('La contraseña no puede ser una palabra muy común.');
            }
        };
        if ($needUpper) $passwordRules[] = function($a,$v,$f){ if(!preg_match('/[A-Z]/',$v)) $f('La contraseña debe incluir al menos una letra mayúscula.'); };
        if ($needLower) $passwordRules[] = function($a,$v,$f){ if(!preg_match('/[a-z]/',$v)) $f('La contraseña debe incluir al menos una letra minúscula.'); };
        if ($needDigit) $passwordRules[] = function($a,$v,$f){ if(!preg_match('/\d/',$v)) $f('La contraseña debe incluir al menos un número.'); };
        if ($needSymbol) $passwordRules[] = function($a,$v,$f) use ($symbolExamples){
            if(!preg_match('/[^A-Za-z0-9]/',$v)) {
                $f('La contraseña debe incluir al menos un símbolo. Puedes usar por ejemplo: '.implode(' ', $symbolExamples));
            }
        };

        return [
            'usuario' => [
                'required','string','max:50',
                function($attribute,$value,$fail) use ($usuarioMin,$usuarioRegex) {
                    if (!preg_match($usuarioRegex, $value)) {
                        if (strlen($value) < $usuarioMin) {
                            $fail('El usuario debe tener al menos '.$usuarioMin.' caracteres.');
                        }
                        if ($value !== strtoupper($value)) {
                            $fail('El usuario debe estar en MAYÚSCULAS.');
                        }
                        if (preg_match('/[^A-Za-z0-9_]/',$value)) {
                            $fail('Sólo se permiten letras mayúsculas, números y _.');
                        }
                    }
                },
                'unique:tbl_ms_usuario,usuario'
            ],
            'nombre_usuario' => 'required|string|max:100',
            'correo_electronico' => $emailRule,
            'contrasena' => $passwordRules,
            'estado_usuario' => 'nullable|string|max:20',
            // El rol se asignará automáticamente (Cliente) en el registro público si no se envía
            'id_rol_fk' => 'sometimes|integer|exists:tbl_ms_rol,id_rol_pk',
            'primer_ingreso' => 'nullable|boolean',
            'fecha_ultima_conexion' => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        $mensajes = [
            'usuario.required' => 'Debe indicar un usuario.',
            'usuario.max' => 'El usuario no puede superar 50 caracteres.',
            'usuario.unique' => 'Ese usuario ya está registrado.',
            'contrasena.required' => 'La contraseña es obligatoria.',
            'contrasena.min' => 'La contraseña debe tener al menos '.$this->passwordMinUsed.' caracteres.',
            'contrasena.max' => 'La contraseña no puede superar 100 caracteres.',
            'contrasena.regex' => 'La contraseña no puede contener espacios.',
            'correo_electronico.regex' => $this->correoDominioUsed
                ? 'El correo debe pertenecer al dominio: '.$this->correoDominioUsed
                : 'El correo no cumple el formato requerido.',
        ];
        return $mensajes;
    }

    public function attributes(): array
    {
        return [
            'contrasena' => 'contraseña'
        ];
    }
}
