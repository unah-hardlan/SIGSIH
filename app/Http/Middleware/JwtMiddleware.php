<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\Usuario;
use App\Models\SesionUsuario;
use Illuminate\Support\Facades\Auth;

class JwtMiddleware
{

    public function handle(Request $request, Closure $next): Response
    {

        $token = $request->bearerToken();
        if (!$token) {
            $token = $request->cookie('auth_token');
        }

        if (!$token) {
            return response()->json(['error' => 'Token no proporcionado'], 401);
        }

        try {
            $secret = config('jwt.secret');
            if (!$secret) {
                return response()->json(['error' => 'JWT Secret no configurado'], 500);
            }

            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            $user = Usuario::find($decoded->sub);
            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }


            try {
                $tokenId = substr(hash('sha256', $token), 0, 32);
                $sesion  = SesionUsuario::find($tokenId);

                if (!$sesion) {
                    return response()->json([
                        'error' => 'Tu sesión fue cerrada porque se superó el límite de sesiones concurrentes. Se mantuvo la más reciente.',
                        'code'  => 'SESSION_REMOVED_LIMIT',
                    ], 401);
                }

                if ($sesion->fecha_expiracion < now()) {
                    $sesion->delete();
                    return response()->json([
                        'error' => 'Sesión expirada',
                        'code'  => 'SESSION_EXPIRED',
                    ], 401);
                }

                // Renovar expiración en cada request válido (sliding window).
                $ttlSeconds = max(60, (int) config('session.lifetime', 60) * 60);
                $sesion->fecha_expiracion = now()->addSeconds($ttlSeconds);
                $sesion->save();

            $request->setUserResolver(fn() => $user);
            Auth::setUser($user);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token inválido: ' . $e->getMessage()], 401);
        }

        return $next($request);
    }
}
