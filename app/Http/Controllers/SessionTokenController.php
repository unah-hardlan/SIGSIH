<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class SessionTokenController extends Controller
{
    public function __construct(private AuthService $auth) {}

    /**
     * Issue a short-lived JWT for the currently authenticated user (web cookie auth).
     */
    public function issue(): JsonResponse
    {
        // Reutilizar el token ya emitido (cookie httpOnly), para no crear nuevas sesiones en cada llamado
        $cookieToken = request()->cookie('auth_token');
        if (!$cookieToken) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Validar token y usuario asociado
        try {
            $secret = config('jwt.secret');
            if (!$secret) return response()->json(['message' => 'JWT Secret no configurado'], 500);
            $decoded = \Firebase\JWT\JWT::decode($cookieToken, new \Firebase\JWT\Key($secret, 'HS256'));
            $userId = (int) ($decoded->sub ?? 0);
            if ($userId <= 0) return response()->json(['message' => 'Unauthenticated'], 401);
            $usuarioModel = \App\Models\Usuario::find($userId);
            if (!$usuarioModel) return response()->json(['message' => 'Unauthenticated'], 401);

            // Responder con el mismo token y datos básicos del usuario
            return response()->json([
                'token' => $cookieToken,
                'user'  => [
                    'id'      => $usuarioModel->getKey(),
                    'usuario' => $usuarioModel->usuario,
                    'nombre'  => $usuarioModel->nombre_usuario,
                    'correo'  => $usuarioModel->correo_electronico,
                    'rol'     => $usuarioModel->rol->rol ?? null,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
    }
}
