<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Parametro;

class UpdateUsuarioRequest extends FormRequest
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
            $this->merge(['usuario' => strtoupper(trim($this->input('usuario')))]);
        }
        if ($this->has('correo_electronico')) {
            $this->merge(['correo_electronico' => trim($this->input('correo_electronico'))]);
        }
    }

    public function rules(): array
    {
        $id = $this->route('usuario') ?? $this->route('id');

        $correoMuestra = Parametro::whereIn('parametro', ['ADMIN.CORREO', 'ADMIN_CORREO'])
            ->orderByRaw("FIELD(parametro,'ADMIN.CORREO','ADMIN_CORREO')")
            ->value('valor');
        $usuarioMuestra = Parametro::whereIn('parametro', ['ADMIN.USUARIO', 'ADMIN_CUSER'])
            ->orderByRaw("FIELD(parametro,'ADMIN.USUARIO','ADMIN_CUSER')")
            ->value('valor');
        $passwordMuestra = Parametro::whereIn('parametro', ['ADMIN.PASSWORD', 'ADMIN_CPASS'])
            ->orderByRaw("FIELD(parametro,'ADMIN.PASSWORD','ADMIN_CPASS')")
            ->value('valor');

        $emailRule = "sometimes|email|max:100|unique:tbl_ms_usuario,correo_electronico,{$id},id_usuario_pk";
        if ($correoMuestra && str_contains($correoMuestra, '@')) {
            $correoDominio = substr(strrchr($correoMuestra, '@'), 1);
            if ($correoDominio) {
                $this->correoDominioUsed = strtolower($correoDominio);
                $emailRule .= '|regex:/^[^@\s]+@' . preg_quote($this->correoDominioUsed, '/') . '$/i';
            }
        }

        $usuarioMin = 3;
        if ($usuarioMuestra) {
            $usuarioMin = max(3, strlen($usuarioMuestra));
        }
        $this->usuarioMinUsed = $usuarioMin;
        $usuarioRegex = '/^[A-Z0-9_]{' . $usuarioMin . ',50}$/';
        if ($usuarioMuestra && preg_match('/^\^.*\$$/', $usuarioMuestra)) {
            $usuarioRegex = $usuarioMuestra;
        }

        $minPass = 8;
        $needUpper = $needLower = $needDigit = $needSymbol = false;
        $symbolExamples = [];
        if ($passwordMuestra) {
            $minPass = max(8, strlen($passwordMuestra));
            $needUpper = (bool) preg_match('/[A-Z]/', $passwordMuestra);
            $needLower = (bool) preg_match('/[a-z]/', $passwordMuestra);
            $needDigit = (bool) preg_match('/\d/', $passwordMuestra);
            $needSymbol = (bool) preg_match('/[^A-Za-z0-9]/', $passwordMuestra);
            if ($needSymbol) {
                preg_match_all('/[^A-Za-z0-9]/', $passwordMuestra, $m);
                $symbolExamples = array_values(array_unique($m[0] ?? []));
            }
        }
        if ($needSymbol && empty($symbolExamples)) {
            $symbolExamples = ['!', '@', '#', '$', '%', '&', '*', '?', '.', '_', '-', '+', '='];
        }
        $this->passwordMinUsed = $minPass;
        $passwordRules = ['sometimes', 'string', 'min:' . $minPass, 'max:100', 'regex:/^\S+$/'];
        $passwordRules[] = function ($attribute, $value, $fail) {
            $usuarioInput = strtoupper($this->input('usuario', ''));
            if ($usuarioInput && strtoupper($value) === $usuarioInput) {
                $fail('La contraseña no puede ser igual al usuario.');
            }
        };
        $passwordRules[] = function ($attribute, $value, $fail) {
            $prohibidas = ['CONTRASENA', 'CONTRASEÑA', 'PASSWORD'];
            if (in_array(strtoupper($value), $prohibidas, true)) {
                $fail('La contraseña no puede ser una palabra muy común.');
            }
        };
        if ($needUpper) $passwordRules[] = function ($a, $v, $f) {
            if (!preg_match('/[A-Z]/', $v)) $f('La contraseña debe incluir al menos una letra mayúscula.');
        };
        if ($needLower) $passwordRules[] = function ($a, $v, $f) {
            if (!preg_match('/[a-z]/', $v)) $f('La contraseña debe incluir al menos una letra minúscula.');
        };
        if ($needDigit) $passwordRules[] = function ($a, $v, $f) {
            if (!preg_match('/\d/', $v)) $f('La contraseña debe incluir al menos un número.');
        };
        if ($needSymbol) $passwordRules[] = function ($a, $v, $f) use ($symbolExamples) {
            if (!preg_match('/[^A-Za-z0-9]/', $v)) {
                $f('La contraseña debe incluir al menos un símbolo. Puedes usar por ejemplo: ' . implode(' ', $symbolExamples));
            }
        };

        return [
            'usuario' => ['sometimes', 'string', 'max:50', 'regex:' . $usuarioRegex, "unique:tbl_ms_usuario,usuario,{$id},id_usuario_pk"],
            'correo_electronico' => $emailRule,
            'contrasena' => $passwordRules,
            'estado_usuario' => 'sometimes|string|max:20',
            'id_rol_fk' => 'sometimes|integer|exists:tbl_ms_rol,id_rol_pk',
            'primer_ingreso' => 'sometimes|boolean',
            'fecha_ultima_conexion' => 'sometimes|date',
            'fecha_vencimiento' => 'sometimes|date',
        ];
    }

    public function messages(): array
    {
        return [
            'correo_electronico.unique' => 'Ese correo electrónico ya está registrado.',
            'correo_electronico.regex' => $this->correoDominioUsed
                ? 'El correo debe pertenecer al dominio: ' . $this->correoDominioUsed
                : 'El correo no cumple el formato requerido.',
            'usuario.regex' => 'El usuario debe tener mínimo ' . $this->usuarioMinUsed . ' caracteres y sólo MAYÚSCULAS, números o _.',
            'contrasena.min' => 'La contraseña debe tener al menos ' . $this->passwordMinUsed . ' caracteres.',
            'contrasena.max' => 'La contraseña no puede superar 100 caracteres.',
            'contrasena.regex' => 'La contraseña no puede contener espacios.',
        ];
    }

    public function attributes(): array
    {
        return [
            'contrasena' => 'contraseña',
            'correo_electronico' => 'correo electrónico'
        ];
    }
}
