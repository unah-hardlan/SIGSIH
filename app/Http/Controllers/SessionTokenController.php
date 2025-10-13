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
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Asegurar que sea instancia de Usuario
        $usuarioModel = null;
        if ($user instanceof Usuario) {
            $usuarioModel = $user;
        } elseif ($user instanceof User) {
            // Intentar resolver por correo
            $email = method_exists($user, 'getAttribute') ? (string) $user->getAttribute('email') : '';
            if ($email !== '') {
                $usuarioModel = Usuario::whereRaw('LOWER(correo_electronico) = ?', [strtolower($email)])->first();
            }
        }
        if (!$usuarioModel) {
            // Fallback: intentar por ID si coincide con PK
            $authId = auth()->id();
            if ($authId) {
                $usuarioModel = Usuario::find($authId);
            }
        }
        if (!$usuarioModel) {
            return response()->json(['message' => 'No se pudo resolver el usuario de aplicación'], 422);
        }

        $result = $this->auth->tokenForUser($usuarioModel);
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['code'] ?? 500);
        }

        return response()->json([
            'token' => $result['token'] ?? null,
            'user'  => $result['user'] ?? null,
        ]);
    }
}
