<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    /**
     * Obtener el estado actual de 2FA del cliente
     */
    public function status(Request $request): JsonResponse
    {
        /** @var Usuario $user */
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'is_enabled' => (bool) $user->two_factor_enabled,
                'confirmed_at' => $user->two_factor_confirmed_at,
                'has_recovery_codes' => !empty($user->two_factor_recovery_codes)
            ]
        ]);
    }

    /**
     * Iniciar configuración de 2FA (igual que admin)
     */
    public function startSetup(Request $request): JsonResponse
    {
        $request->validate(['current_password' => 'required|string']);
        /** @var Usuario $user */
        $user = $request->user();
        $hash = $user?->contrasena;
        if (!$hash || !Hash::check($request->string('current_password'), $hash)) {
            return response()->json(['message' => 'La contraseña actual es incorrecta'], 403);
        }
        
        $g2fa = new Google2FA();
        $secret = $g2fa->generateSecretKey();
        $user->two_factor_secret = encrypt($secret);
        $user->save();

        $label = 'SIGSIH Cliente:' . ($user->correo_electronico ?: $user->usuario);
        $otpauth = $g2fa->getQRCodeUrl('SIGSIH', $label, $secret);

        return response()->json([
            'otpauth_url' => $otpauth,
        ]);
    }

    /**
     * Confirmar configuración de 2FA (igual que admin)
     */
    public function confirmSetup(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'current_password' => 'required|string',
        ]);
        /** @var Usuario $user */
        $user = $request->user();
        $hash = $user?->contrasena;
        if (!$hash || !Hash::check($request->string('current_password'), $hash)) {
            return response()->json(['message' => 'La contraseña actual es incorrecta'], 403);
        }
        if (!$user->two_factor_secret) {
            return response()->json(['message' => 'No hay secreto 2FA que confirmar'], 400);
        }

        $g2fa = new Google2FA();
        $secret = decrypt($user->two_factor_secret);
        $valid = $g2fa->verifyKey($secret, $request->string('code'), 1);
        if (!$valid) {
            return response()->json(['message' => 'Código inválido'], 422);
        }

        $codes = collect(range(1, 8))->map(fn() => Str::random(10))->implode(',');
        $user->two_factor_recovery_codes = encrypt($codes);
        $user->two_factor_enabled = true;
        $user->two_factor_confirmed_at = now();
        $user->save();

        return response()->json([
            'message' => '2FA activado',
            'recovery_codes' => explode(',', $codes),
        ]);
    }

    /**
     * Deshabilitar 2FA (igual que admin)
     */
    public function disable(Request $request): JsonResponse
    {
        $request->validate(['current_password' => 'required|string']);
        /** @var Usuario $user */
        $user = $request->user();
        $hash = $user?->contrasena;
        if (!$hash || !Hash::check($request->string('current_password'), $hash)) {
            return response()->json(['message' => 'La contraseña actual es incorrecta'], 403);
        }
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_enabled = false;
        $user->two_factor_confirmed_at = null;
        $user->save();
        return response()->json(['message' => '2FA desactivado']);
    }

    /**
     * Generar nuevos códigos de recuperación
     */
    public function recoveryCodes(Request $request): JsonResponse
    {
        /** @var Usuario $user */
        $user = $request->user();
        
        if (!$user->two_factor_enabled) {
            return response()->json([
                'success' => false,
                'message' => '2FA no está habilitado.'
            ], 400);
        }

        try {
            // Generar nuevos códigos de recuperación
            $recoveryCodes = collect(range(1, 8))->map(fn() => Str::random(10))->toArray();
            
            // Guardar los nuevos códigos
            $user->two_factor_recovery_codes = encrypt(implode(',', $recoveryCodes));
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Nuevos códigos de recuperación generados.',
                'data' => [
                    'recovery_codes' => $recoveryCodes
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar códigos de recuperación: ' . $e->getMessage()
            ], 500);
        }
    }
}