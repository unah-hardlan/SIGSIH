<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\Usuario;

class JwtRefresh
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $jwtSecret = config('jwt.secret');
        if (!$jwtSecret) {
            return $response; // no secret => no refresh
        }

        $token = $request->cookie('auth_token') ?: $request->bearerToken();
        if (!$token) {
            return $response;
        }

        try {
            $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));
            if (!isset($decoded->exp) || !isset($decoded->sub)) {
                return $response;
            }
            $remaining = $decoded->exp - time();
            if ($remaining > 0 && $remaining < 300) { // <5 min
                $user = Usuario::find($decoded->sub);
                if (!$user) return $response;

                $newPayload = [
                    'sub'  => $user->id_usuario_pk,
                    'name' => $user->nombre_usuario,
                    'iat'  => time(),
                    'exp'  => time() + 3600,
                ];
                $newToken = JWT::encode($newPayload, $jwtSecret, 'HS256');
                // set cookie
                $response->headers->setCookie(cookie(
                    'auth_token',
                    $newToken,
                    60,
                    '/',
                    null,
                    false,
                    true,
                    false,
                    'Lax'
                ));
                // expose header so frontend could optionally update localStorage
                $response->headers->set('X-New-JWT', $newToken);
            }
        } catch (\Throwable $e) {
            // ignore errors
        }

        return $response;
    }
}
