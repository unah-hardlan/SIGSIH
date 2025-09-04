<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Persona;
use App\Models\Usuario;
use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdatePersonaRequest;
use App\Services\BitacoraService;
use function response;

class ProfileController extends Controller
{
    public function __construct(private BitacoraService $bitacora) {}
    // Devuelve si falta completar perfil y la persona (si existe)
    public function me()
    {
        /** @var Usuario $user */
        $user = Auth::user();
        $persona = Persona::where('id_usuario_fk', $user->getKey())->first();
        return response()->json([
            'primer_ingreso' => (bool) $user->primer_ingreso,
            'persona' => $persona,
            'usuario' => [
                'usuario' => $user->usuario,
                'nombre_usuario' => $user->nombre_usuario,
                'correo_electronico' => $user->correo_electronico,
            ],
        ]);
    }

    // Crea o actualiza persona del usuario autenticado y marca primer_ingreso = 0
    public function savePersona(Request $request)
    {
        $user = Auth::user();
        $existing = Persona::where('id_usuario_fk', $user->getKey())->first();

        // Validar usando reglas de StorePersonaRequest pero sin exigir dni único si ya existe y permitir id_usuario_fk auto
        $rules = [
            'primer_nombre' => 'required|string|max:50',
            'segundo_nombre' => 'nullable|string|max:50',
            'primer_apellido' => 'required|string|max:50',
            'segundo_apellido' => 'nullable|string|max:50',
            'dni' => 'required|string|max:20',
            'cargo' => 'nullable|string|max:50',
            'id_tipo_persona_fk' => 'required|integer|exists:tbl_tipo_persona,id_tipo_persona_pk',
            'id_genero_fk' => 'required|integer|exists:tbl_genero,id_genero_pk',
            'id_perfil_fk' => 'required|integer|exists:tbl_perfil,id_perfil_pk',
        ];

        if ($existing) {
            // Ignorar la persona actual en la regla unique
            $rules['dni'] .= '|unique:tbl_persona,dni,' . $existing->getKey() . ',id_persona_pk';
        } else {
            $rules['dni'] .= '|unique:tbl_persona,dni';
        }

        $validated = $request->validate($rules);

        $persona = Persona::updateOrCreate(
            ['id_usuario_fk' => $user->getKey()],
            array_merge($validated, ['id_usuario_fk' => $user->getKey()])
        );

        // Marcar primer ingreso como completado
        $user->primer_ingreso = 0;
        $user->save();

        // Bitácora: creación/actualización de perfil
        try {
            $nombre = trim(($persona->primer_nombre ?? '').' '.($persona->primer_apellido ?? ''));
            $accion = $existing ? 'Actualizar' : 'Insertar';
            $msg = ($existing ? 'Actualización de perfil' : 'Creación de perfil') . ($nombre ? (': '.$nombre) : '');
            $this->bitacora->logFor('Perfil', $accion, $msg, $user->getKey());
        } catch (\Throwable $e) {}

        return response()->json(['ok' => true, 'persona' => $persona]);
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);
        $user = Auth::user();
        $persona = Persona::firstOrCreate(['id_usuario_fk' => $user->getKey()], []);
        $oldPath = $persona->avatar_path;
        $path = $request->file('avatar')->store('avatars', 'public');
        $persona->avatar_path = $path;
        $persona->save();
        // Borrar el archivo anterior si existe
        if ($oldPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
        }

        // Bitácora: subida de avatar
        try { $this->bitacora->logFor('Perfil', 'Actualizar', 'Actualizó foto de perfil', $user->getKey()); } catch (\Throwable $e) {}

        return response()->json([
            'ok' => true,
            'path' => $path,
            'url' => Storage::url($path)
        ]);
    }

    public function deleteAvatar(Request $request)
    {
        $user = Auth::user();
        $persona = Persona::where('id_usuario_fk', $user->getKey())->first();
        if (!$persona) {
            return response()->json(['ok' => true]);
        }
        $oldPath = $persona->avatar_path;
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }
        $persona->avatar_path = null;
        $persona->save();
    // Bitácora: eliminación de avatar
    try { $this->bitacora->logFor('Perfil', 'Actualizar', 'Eliminó foto de perfil', $user->getKey()); } catch (\Throwable $e) {}
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

        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($request->current_password, $user->contrasena)) {
            return response()->json([
                'ok' => false,
                'message' => 'La contraseña actual es incorrecta'
            ], 400);
        }

        // Cambiar la contraseña
        $user->contrasena = Hash::make($request->password);
        $user->save();

    // Bitácora: cambio de contraseña (sin datos sensibles)
    try { $this->bitacora->logFor('Perfil', 'Actualizar', 'Cambio de contraseña', $user->getKey()); } catch (\Throwable $e) {}

        return response()->json([
            'ok' => true,
            'message' => 'Contraseña cambiada exitosamente'
        ]);
    }
}
