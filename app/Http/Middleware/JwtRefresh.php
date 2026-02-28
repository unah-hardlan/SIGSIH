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
            return $response; 
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
            
            $sessionMinutes = (int) config('session.lifetime', 60);
            $ttlSeconds = max(60, $sessionMinutes * 60);
            $threshold = max(60, min(300, (int) floor($ttlSeconds * 0.25)));
            if ($remaining > 0 && $remaining < $threshold) {
                $user = Usuario::find($decoded->sub);
                if (!$user) return $response;

                
                $newPayload = [
                    'sub'  => $user->id_usuario_pk,
                    'name' => $user->nombre_usuario,
                    'iat'  => time(),
                    'exp'  => time() + $ttlSeconds,
                ];
                $newToken = JWT::encode($newPayload, $jwtSecret, 'HS256');
                
                try {
                    $sessionsKey = 'user_sessions:' . $user->getKey();
                    $sessions = cache()->get($sessionsKey, []);
                    if (is_array($sessions)) {
                        $oldId = substr(hash('sha256', $token), 0, 32);
                        $newId = substr(hash('sha256', $newToken), 0, 32);
                        $now = time();
                        
                        $sessions = array_filter($sessions, fn($exp) => (int)$exp > $now);
                        
                        $sessions[$newId] = $now + $ttlSeconds;
                        
                        if (isset($sessions[$oldId])) {
                            unset($sessions[$oldId]);
                        }
                        cache()->put($sessionsKey, $sessions, now()->addSeconds($ttlSeconds));
                    }
                } catch (\Throwable $e) {
                    
                }
                
                $secure = $request->isSecure() || str_starts_with((string) config('app.url'), 'https://');
                $sameSite = app()->environment('production') ? 'Strict' : 'Lax';
                $response->headers->setCookie(cookie(
                    'auth_token',
                    $newToken,
                    $sessionMinutes,
                    '/',
                    null,
                    $secure,
                    true,
                    false,
                    $sameSite
                ));
                
                $response->headers->set('X-New-JWT', $newToken);
            }
        } catch (\Throwable $e) {
            
        }

        return $response;
    }
}
