<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\Usuario;
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
                $sessionsKey = 'user_sessions:' . $user->getKey();
                $sessions = cache()->get($sessionsKey, []);
                $hasKey = cache()->has($sessionsKey);
                
                if ($hasKey && (!is_array($sessions) || !isset($sessions[$tokenId]))) {
                    return response()->json([
                        'error' => 'Tu sesión fue cerrada porque se superó el límite de sesiones concurrentes. Se mantuvo la más reciente.',
                        'code'  => 'SESSION_REMOVED_LIMIT',
                    ], 401);
                }
                
                if (is_array($sessions) && isset($sessions[$tokenId])) {
                    $expStored = (int) $sessions[$tokenId];
                    if ($expStored < time()) {
                    
                    unset($sessions[$tokenId]);
                    $ttlSeconds = max(60, (int) config('session.lifetime', 60) * 60);
                    cache()->put($sessionsKey, $sessions, now()->addSeconds($ttlSeconds));
                    return response()->json([
                        'error' => 'Sesión expirada',
                        'code'  => 'SESSION_EXPIRED',
                    ], 401);
                    }
                    
                    $ttlSeconds = max(60, (int) config('session.lifetime', 60) * 60);
                    cache()->put($sessionsKey, $sessions, now()->addSeconds($ttlSeconds));
                }
            } catch (\Throwable $e) {
                
            }

            $request->setUserResolver(fn() => $user);
            Auth::setUser($user);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token inválido: ' . $e->getMessage()], 401);
        }

        return $next($request);
    }
}
