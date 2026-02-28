<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Persona;
use App\Models\Usuario;
use App\Models\HistorialContrasena;
use App\Models\Parametro;
use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdatePersonaRequest;
use App\Services\BitacoraService;
use function response;

class ProfileController extends Controller
{
    public function __construct(private BitacoraService $bitacora) {}

    private function getUserId($user)
    {
        $id = Auth::id();
        if (!$id && is_object($user)) {
            $id = method_exists($user, 'getAuthIdentifier') ? $user->getAuthIdentifier() : null;
            if (!$id) {
                $id = method_exists($user, 'getKey') ? $user->getKey() : ($user->id_usuario_pk ?? $user->id ?? null);
            }
        }
        return $id;
    }

    public function me()
    {

        $user = Auth::user();
        $uid = $this->getUserId($user);
        $persona = $uid ? Persona::where('id_usuario_fk', $uid)->first() : null;
        return response()->json([
            'primer_ingreso' => (bool) $user->primer_ingreso,
            'two_factor_enabled' => (bool) ($user->two_factor_enabled ?? false),
            'persona' => $persona,
            'usuario' => [
                'usuario' => $user->usuario,
                'nombre_usuario' => $user->nombre_usuario,
                'correo_electronico' => $user->correo_electronico,
            ],
        ]);
    }


    public function sanctumUser(Request $request)
    {
        return $request->user();
    }


    public function savePersona(Request $request)
    {
        $user = Auth::user();
        $uid = $this->getUserId($user);
        $existing = $uid ? Persona::where('id_usuario_fk', $uid)->first() : null;



        $format = Parametro::where('parametro', 'FORMATO DNI')->value('valor');
        $dniRegex = $this->buildDniRegex($format);
        $dniRule = 'regex:/' . $dniRegex . '/';

        $rules = [
            'primer_nombre' => 'required|string|max:50',
            'segundo_nombre' => 'nullable|string|max:50',
            'primer_apellido' => 'required|string|max:50',
            'segundo_apellido' => 'nullable|string|max:50',
            'dni' => 'required|string|max:20|' . $dniRule,
            'id_genero_fk' => 'required|integer|exists:tbl_genero,id_genero_pk',
        ];

        if ($existing) {

            $rules['dni'] .= '|unique:tbl_persona,dni,' . $existing->getKey() . ',id_persona_pk';
        } else {
            $rules['dni'] .= '|unique:tbl_persona,dni';
        }


        $messages = [
            'dni.regex' => 'El DNI no cumple con el formato.' . (is_string($format) && trim($format) !== '' && !preg_match('/^\\d+$/', trim($format)) ? ' El formato es: ' . trim($format) . '.' : ''),
            'dni.unique' => 'El DNI ya está registrado.',
            'dni.required' => 'El DNI es obligatorio.',
            'primer_nombre.required' => 'El primer nombre es obligatorio.',
            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'id_genero_fk.required' => 'El género es obligatorio.',
        ];
        $attributes = [
            'dni' => 'DNI',
            'primer_nombre' => 'primer nombre',
            'segundo_nombre' => 'segundo nombre',
            'primer_apellido' => 'primer apellido',
            'segundo_apellido' => 'segundo apellido',
            'id_genero_fk' => 'género',
        ];
        $validated = $request->validate($rules, $messages, $attributes);

        $persona = Persona::updateOrCreate(
            ['id_usuario_fk' => $uid],
            array_merge($validated, ['id_usuario_fk' => $uid])
        );


        if ($uid) {
            Usuario::where('id_usuario_pk', $uid)->update(['primer_ingreso' => 0]);
        }


        try {
            $nombre = trim(($persona->primer_nombre ?? '') . ' ' . ($persona->primer_apellido ?? ''));
            $accion = $existing ? 'Actualizar' : 'Insertar';
            $msg = ($existing ? 'Actualización de perfil' : 'Creación de perfil') . ($nombre ? (': ' . $nombre) : '');
            $this->bitacora->logFor('Perfil', $accion, $msg, $uid);
        } catch (\Throwable $e) {
        }

        return response()->json(['ok' => true, 'persona' => $persona]);
    }

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

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);
        $user = Auth::user();
        $uid = $this->getUserId($user);
        $persona = Persona::firstOrCreate(['id_usuario_fk' => $uid], []);
        $oldPath = $persona->avatar_path;
        $path = $request->file('avatar')->store('avatars', 'public');
        $persona->avatar_path = $path;
        $persona->save();

        if ($oldPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
        }


        try {
            $this->bitacora->logFor('Perfil', 'Actualizar', 'Actualizó foto de perfil', $uid);
        } catch (\Throwable $e) {
        }

        return response()->json([
            'ok' => true,
            'path' => $path,
            'url' => Storage::url($path)
        ]);
    }

    public function deleteAvatar(Request $request)
    {
        $user = Auth::user();
        $uid = $this->getUserId($user);
        $persona = $uid ? Persona::where('id_usuario_fk', $uid)->first() : null;
        if (!$persona) {
            return response()->json(['ok' => true]);
        }
        $oldPath = $persona->avatar_path;
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }
        $persona->avatar_path = null;
        $persona->save();

        try {
            $this->bitacora->logFor('Perfil', 'Actualizar', 'Eliminó foto de perfil', $uid);
        } catch (\Throwable $e) {
        }
        return response()->json(['ok' => true]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $uid = $this->getUserId($user);

        $currentHash = is_object($user) && isset($user->contrasena)
            ? $user->contrasena
            : ($uid ? Usuario::where('id_usuario_pk', $uid)->value('contrasena') : null);

        $currentOk = false;
        if ($currentHash) {
            $hashStr = (string) $currentHash;
            $isKnownHash = preg_match('/^\$(2y|argon2id|argon2i)\$/', $hashStr) === 1;
            if ($isKnownHash) {
                try {
                    $currentOk = Hash::check($request->current_password, $hashStr);
                } catch (\Throwable $e) {
                    $currentOk = false;
                }
            } else {

                $currentOk = hash_equals($hashStr, (string) $request->current_password);
            }
        }

        if (!$currentOk) {
            return response()->json([
                'ok' => false,
                'message' => 'La contraseña actual es incorrecta'
            ], 400);
        }


        $N = 5;
        $hashes = $uid
            ? HistorialContrasena::where('id_usuario_fk', $uid)
            ->orderByDesc('fecha_creacion')
            ->orderByDesc('id_hist_pk')
            ->limit($N)
            ->pluck('contrasena')
            : collect();

        foreach ($hashes as $hash) {
            if (!is_string($hash) || $hash === '') {
                continue;
            }
            $hashStr = (string) $hash;
            $isKnownHash = preg_match('/^\$(2y|argon2id|argon2i)\$/', $hashStr) === 1;
            $reused = false;
            if ($isKnownHash) {
                try {
                    $reused = Hash::check($request->password, $hashStr);
                } catch (\Throwable $e) {
                    $reused = false;
                }
            } else {

                $reused = hash_equals($hashStr, (string) $request->password);
            }
            if ($reused) {
                return response()->json([
                    'ok' => false,
                    'message' => 'No puedes reutilizar una de tus últimas ' . $N . ' contraseñas.'
                ], 422);
            }
        }


        $hashed = Hash::make($request->password);
        if ($uid) {
            Usuario::where('id_usuario_pk', $uid)->update(['contrasena' => $hashed]);


            try {
                HistorialContrasena::create([
                    'contrasena' => $hashed,
                    'id_usuario_fk' => $uid,
                    'creado_por' => ($user->usuario ?? 'system'),
                    'fecha_creacion' => now(),
                ]);


                $idsToKeep = HistorialContrasena::where('id_usuario_fk', $uid)
                    ->orderByDesc('fecha_creacion')
                    ->orderByDesc('id_hist_pk')
                    ->limit($N)
                    ->pluck('id_hist_pk');
                HistorialContrasena::where('id_usuario_fk', $uid)
                    ->whereNotIn('id_hist_pk', $idsToKeep)
                    ->delete();
            } catch (\Throwable $e) {
            }
        }


        try {
            $this->bitacora->logFor('Perfil', 'Actualizar', 'Cambio de contraseña', $uid);
        } catch (\Throwable $e) {
        }

        return response()->json([
            'ok' => true,
            'message' => 'Contraseña cambiada exitosamente'
        ]);
    }
}
