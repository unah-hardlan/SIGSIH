<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

class JwtWebAuth
{

    public function handle(Request $request, Closure $next): Response
    {

        $token = $request->cookie('auth_token') ?: $request->bearerToken();

        if (!$token) {
            return $this->kick($request);
        }

        $jwtSecret = config('jwt.secret');
        if (!$jwtSecret) {
            return $this->kick($request);
        }

        try {
            $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));
            if (!isset($decoded->exp) || time() >= (int)$decoded->exp) {
                return $this->kick($request, true);
            }
            $sub = $decoded->sub ?? null;
            if (!$sub) {
                return $this->kick($request, true);
            }
            $user = Usuario::find($sub);
            if (!$user) {
                return $this->kick($request, true);
            }
            Auth::setUser($user);
        } catch (\Throwable $e) {
            return $this->kick($request, true);
        }

        $response = $next($request);

        // Evita que vistas autenticadas queden en caché del navegador.
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }


    protected function kick(Request $request, bool $forgetCookie = false): Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $response = redirect()->route('login');

        if ($forgetCookie) {
            $response->headers->setCookie(Cookie::forget('auth_token'));
        }

        return $response;
    }
}
