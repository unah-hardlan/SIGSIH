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
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Intentar primero header Authorization Bearer, luego cookie httpOnly
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

            // Verificar que la sesión (token hash) siga activa según registro de sesiones concurrentes
            try {
                $tokenId = substr(hash('sha256', $token), 0, 32);
                $sessionsKey = 'user_sessions:' . $user->getKey();
                $sessions = cache()->get($sessionsKey, []);
                $hasKey = cache()->has($sessionsKey);
                // Si el key no existe (cache fue limpiada), no apliquemos enforcement para evitar expulsar a todos tras un cache:clear
                if ($hasKey && (!is_array($sessions) || !isset($sessions[$tokenId]))) {
                    return response()->json([
                        'error' => 'Tu sesión fue cerrada porque se superó el límite de sesiones concurrentes. Se mantuvo la más reciente.',
                        'code'  => 'SESSION_REMOVED_LIMIT',
                    ], 401);
                }
                // Si expiró según registro (aunque JWT siga vigente) forzar error
                if (is_array($sessions) && isset($sessions[$tokenId])) {
                    $expStored = (int) $sessions[$tokenId];
                    if ($expStored < time()) {
                    // limpiar y rechazar
                    unset($sessions[$tokenId]);
                    $ttlSeconds = max(60, (int) config('session.lifetime', 60) * 60);
                    cache()->put($sessionsKey, $sessions, now()->addSeconds($ttlSeconds));
                    return response()->json([
                        'error' => 'Sesión expirada',
                        'code'  => 'SESSION_EXPIRED',
                    ], 401);
                    }
                    // Touch the cache TTL so the sessions map survives as long as session lifetime
                    $ttlSeconds = max(60, (int) config('session.lifetime', 60) * 60);
                    cache()->put($sessionsKey, $sessions, now()->addSeconds($ttlSeconds));
                }
            } catch (\Throwable $e) {
                // En caso de falla de cache continuar (fail-open) para no bloquear falsamente, pero podría loguearse
            }

            $request->setUserResolver(fn() => $user);
            Auth::setUser($user);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token inválido: ' . $e->getMessage()], 401);
        }

        return $next($request);
    }
}
