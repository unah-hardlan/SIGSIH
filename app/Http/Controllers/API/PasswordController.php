<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\HistorialContrasena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    /**
     * Cambiar contraseña del usuario autenticado con validación de historial.
     */
    public function updateMyPassword(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $N = 5; // cantidad de contraseñas a evitar reutilizar

        $request->validate([
            'current_password' => ['required', 'string', 'regex:/^\S+$/'],
            'password' => ['required', 'string', 'min:8', 'max:100', 'regex:/^\S+$/', 'confirmed'],
        ]);

        // Validar contraseña actual (columna es 'contrasena')
        if (! Hash::check($request->input('current_password'), $user->contrasena)) {
            return response()->json(['message' => 'La contraseña actual no coincide.'], 422);
        }

        // Validar que la nueva no esté en el historial de las últimas N
        $hashes = HistorialContrasena::where('id_usuario_fk', $user->id_usuario_pk)
            ->orderByDesc('fecha_creacion')
            ->orderByDesc('id_hist_pk')
            ->limit($N)
            ->pluck('contrasena');

        foreach ($hashes as $hash) {
            if (Hash::check($request->input('password'), $hash)) {
                return response()->json(['message' => 'No puedes reutilizar una de tus últimas '.$N.' contraseñas.'], 422);
            }
        }

        // Guardar nueva contraseña y registrar en historial
        $newHash = Hash::make($request->input('password'));
        $user->contrasena = $newHash;
        $user->save();

        HistorialContrasena::create([
            'contrasena' => $newHash,
            'id_usuario_fk' => $user->id_usuario_pk,
            'creado_por' => $user->usuario ?? 'system',
            'fecha_creacion' => now(),
        ]);

        // Mantener solo las últimas N en historial
        $idsToKeep = HistorialContrasena::where('id_usuario_fk', $user->id_usuario_pk)
            ->orderByDesc('fecha_creacion')->orderByDesc('id_hist_pk')
            ->limit($N)
            ->pluck('id_hist_pk');

        HistorialContrasena::where('id_usuario_fk', $user->id_usuario_pk)
            ->whereNotIn('id_hist_pk', $idsToKeep)
            ->delete();

        return response()->json(['ok' => true, 'message' => 'Contraseña actualizada.']);
    }
}
