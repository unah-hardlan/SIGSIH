<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\Parametro;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Notifications\PasswordResetNotification;


class AuthService
{
    /**
     * Resolve JWT/session TTL in seconds based on config('session.lifetime') minutes.
     */
    private function getTokenTtlSeconds(): int
    {
        $minutes = (int) config('session.lifetime', 60);
        // Boundaries: at least 60 seconds to avoid pathological values
        return max(60, $minutes * 60);
    }

    /**
     * Autentica un usuario y genera un token JWT.
     *
     * @param string $usuario
     * @param string $contrasena
     * @return array{token:string,user:array}|array{error:string,code:int}
     */
    public function attempt(string $usuario, string $contrasena): array
    {
        // Normalizar entrada: trim y mayúsculas para usuario
        $usuario = strtoupper(trim($usuario));
        // Rechazar espacios en usuario/contraseña
        if (preg_match('/\s/', $usuario) || preg_match('/\s/', $contrasena)) {
            return ['error' => 'Usuario/contraseña inválidos', 'code' => 401];
        }

        $secret = config('jwt.secret');
        if (!$secret) {
            return ['error' => 'JWT_SECRET no está configurado', 'code' => 500];
        }

        // Limitar intentos: leer de parámetros globales y bloquear al superar límite
        $maxIntentos = (int) (\App\Models\Parametro::where('parametro', 'ADMIN.INTENTOS_INICIO_SESION')->value('valor')
            ?? \App\Models\Parametro::where('parametro', 'ADMIN_INTENTOS_INICIO SESION')->value('valor')
            ?? 3);

        $user = Usuario::where('usuario', $usuario)->first();
        if (!$user) {
            return ['error' => 'Usuario/contraseña inválidos', 'code' => 401];
        }
        // Bloquear si requiere verificación de correo y no verificado
        $requireVerify = (bool) (Parametro::where('parametro', 'AUTH.REQUIERE_VERIFICACION_CORREO')->value('valor')
            ?? Parametro::where('parametro', 'auth.require_email_verification')->value('valor')
            ?? false);
        if ($requireVerify && is_null($user->email_verified_at)) {
            return ['error' => 'Correo no verificado', 'code' => 403];
        }

        // Campo estado_usuario se usa para bloqueo
        if (strtoupper((string)$user->estado_usuario) === 'BLOQUEADO') {
            return ['error' => 'Usuario bloqueado por intentos inválidos', 'code' => 423];
        }

        // Llevar conteo de intentos temporales en cache por usuario
        $cacheKey = 'login_attempts:' . $user->getKey();
        $attempts = cache()->get($cacheKey, 0);

        $valid = Hash::check($contrasena, $user->contrasena);
        if (!$valid) {
            $attempts++;
            // guardar por 15 minutos
            cache()->put($cacheKey, $attempts, now()->addMinutes(15));
            // Si alcanzó o excedió el máximo, bloquear
            if ($attempts >= $maxIntentos) {
                $user->estado_usuario = 'BLOQUEADO';
                $user->save();
                // Al bloquear, generar token de recuperación y notificar por correo
                try {
                    $token = Password::createToken($user);
                    $user->notify(new PasswordResetNotification($token, (string)$user->correo_electronico));
                } catch (\Throwable $e) {
                    // No interrumpir el flujo por fallos en el envío; sólo loguear
                    report($e);
                }
                // Devolver mensaje indicando opción para desbloquear
                return ['error' => 'Usuario bloqueado por múltiples intentos inválidos. Desbloquear cuenta: se ha enviado un correo para restablecer la contraseña.', 'code' => 423];
            }
            // Calcular intentos restantes y devolver mensaje dinámico
            $remaining = max(0, $maxIntentos - $attempts);
            $msg = 'Usuario/contraseña inválidos';
            if ($remaining > 0) {
                $msg .= ". Quedan {$remaining} intento" . ($remaining === 1 ? '' : 's') . " antes de bloquear la cuenta.";
            }
            return ['error' => $msg, 'code' => 401];
        }

        // reset intentos al éxito
        cache()->forget($cacheKey);

        // Enforce concurrent sessions limit (limpiar expiradas antes de contar)
        // Permitir parámetros diferenciados por rol:
        //  - AUTH.LIMITE_SESIONES.ADMIN
        //  - AUTH.LIMITE_SESIONES.CLIENTE
        // Fallback: AUTH.LIMITE_SESIONES o auth.sessions_limit
        $limit = $this->determineSessionLimit($user);
        $sessions = $this->prepareSessions($user, $limit);

        $ttl = $this->getTokenTtlSeconds();
        $payload = [
            'sub'  => $user->getKey(),
            'name' => $user->nombre_usuario,
            'iat'  => time(),
            'exp'  => time() + $ttl,
        ];

        $token = JWT::encode($payload, $secret, 'HS256');

        // Registrar la sesión (token hash) en cache por 1h
        $this->storeSessionToken($user, $sessions, $token);

        return [
            'token' => $token,
            'user'  => [
                'id'      => $user->getKey(),
                'usuario' => $user->usuario,
                'nombre'  => $user->nombre_usuario,
                'correo'  => $user->correo_electronico,
                'rol'     => $user->rol->rol ?? null,
            ]
        ];
    }

    /**
     * Verifica credenciales y devuelve el usuario o error sin emitir token ni registrar sesión.
     */
    public function verifyCredentialsOnly(string $usuario, string $contrasena): array
    {
        $usuario = strtoupper(trim($usuario));
        if (preg_match('/\s/', $usuario) || preg_match('/\s/', $contrasena)) {
            return ['error' => 'Usuario/contraseña inválidos', 'code' => 401];
        }
        $user = Usuario::where('usuario', $usuario)->first();
        if (!$user) {
            return ['error' => 'Usuario/contraseña inválidos', 'code' => 401];
        }
        if (strtoupper((string)$user->estado_usuario) === 'BLOQUEADO') {
            return ['error' => 'Usuario bloqueado por intentos inválidos', 'code' => 423];
        }
        $cacheKey = 'login_attempts:' . $user->getKey();
        $attempts = cache()->get($cacheKey, 0);
        $valid = Hash::check($contrasena, $user->contrasena);
        if (!$valid) {
            $attempts++;
            cache()->put($cacheKey, $attempts, now()->addMinutes(15));
            $maxIntentos = (int) (\App\Models\Parametro::where('parametro', 'ADMIN.INTENTOS_INICIO_SESION')->value('valor')
                ?? \App\Models\Parametro::where('parametro', 'ADMIN_INTENTOS_INICIO SESION')->value('valor')
                ?? 3);
            if ($attempts >= $maxIntentos) {
                $user->estado_usuario = 'BLOQUEADO';
                $user->save();
                try {
                    $token = Password::createToken($user);
                    $user->notify(new PasswordResetNotification($token, (string)$user->correo_electronico));
                } catch (\Throwable $e) {
                    report($e);
                }
                return ['error' => 'Usuario bloqueado por múltiples intentos inválidos. Desbloquear cuenta: se ha enviado un correo para restablecer la contraseña.', 'code' => 423];
            }
            $remaining = max(0, $maxIntentos - $attempts);
            $msg = 'Usuario/contraseña inválidos';
            if ($remaining > 0) {
                $msg .= ".                  Quedan {$remaining} intento" . ($remaining === 1 ? '' : 's') . " antes de bloquear la cuenta.";
            }
            return ['error' => $msg, 'code' => 401];
        }
        cache()->forget($cacheKey);
        return ['user' => $user];
    }

    /**
     * Genera un token JWT para un usuario existente y devuelve la misma forma que attempt().
     *
     * @param Usuario $user
     * @return array{token:string,user:array}|array{error:string,code:int}
     */
    public function tokenForUser(Usuario $user): array
    {
        $secret = config('jwt.secret');
        if (!$secret) {
            return ['error' => 'JWT_SECRET no está configurado', 'code' => 500];
        }

        $ttl = $this->getTokenTtlSeconds();
        $payload = [
            'sub'  => $user->getKey(),
            'name' => $user->nombre_usuario,
            'iat'  => time(),
            'exp'  => time() + $ttl,
        ];

        $token = JWT::encode($payload, $secret, 'HS256');
        $limit = $this->determineSessionLimit($user);
        $sessions = $this->prepareSessions($user, $limit);
        $this->storeSessionToken($user, $sessions, $token);

        return [
            'token' => $token,
            'user'  => [
                'id'      => $user->getKey(),
                'usuario' => $user->usuario,
                'nombre'  => $user->nombre_usuario,
                'correo'  => $user->correo_electronico,
                'rol'     => $user->rol->rol ?? null,
            ]
        ];
    }

    private function determineSessionLimit(Usuario $user): int
    {
        $rolNombre = strtolower($user->rol->rol ?? '');
        $limitParamKeys = [];
        if ($rolNombre !== '') {
            if (in_array($rolNombre, ['administrador', 'admin'])) {
                $limitParamKeys[] = 'AUTH.LIMITE_SESIONES.ADMIN';
            } elseif (in_array($rolNombre, ['cliente', 'client', 'usuario', 'user'])) {
                $limitParamKeys[] = 'AUTH.LIMITE_SESIONES.CLIENTE';
            } else {
                $limitParamKeys[] = 'AUTH.LIMITE_SESIONES.' . strtoupper($rolNombre);
            }
        }
        $limitParamKeys[] = 'AUTH.LIMITE_SESIONES';
        $limitParamKeys[] = 'auth.sessions_limit';

        foreach ($limitParamKeys as $k) {
            $val = Parametro::where('parametro', $k)->value('valor');
            if ($val !== null && $val !== '') {
                if (is_numeric($val)) {
                    return max(0, (int) $val);
                }
                $filtered = filter_var($val, FILTER_SANITIZE_NUMBER_INT);
                if ($filtered !== '' && is_numeric($filtered)) {
                    return max(0, (int) $filtered);
                }
            }
        }

        return 1;
    }

    private function prepareSessions(Usuario $user, int $limit): array
    {
        $sessionsKey = 'user_sessions:' . $user->getKey();
        $sessions = cache()->get($sessionsKey, []);
        if (!is_array($sessions)) {
            $sessions = [];
        }

        $nowTs = time();
        $sessions = array_filter($sessions, fn($exp) => (int) $exp > $nowTs);

        if ($limit > 0) {
            while (count($sessions) >= $limit) {
                asort($sessions);
                $firstKey = array_key_first($sessions);
                if ($firstKey === null) {
                    break;
                }
                unset($sessions[$firstKey]);
            }
        }

        // Mantener el registro por el tiempo de vida del token
        $ttl = $this->getTokenTtlSeconds();
        cache()->put($sessionsKey, $sessions, now()->addSeconds($ttl));

        return $sessions;
    }

    private function storeSessionToken(Usuario $user, array $sessions, string $token): void
    {
        try {
            $tokenId = substr(hash('sha256', $token), 0, 32);
            $ttl = $this->getTokenTtlSeconds();
            $sessions[$tokenId] = time() + $ttl;
            $now = time();
            $sessions = array_filter($sessions, fn($exp) => $exp > $now);
            $sessionsKey = 'user_sessions:' . $user->getKey();
            cache()->put($sessionsKey, $sessions, now()->addSeconds($ttl));
        } catch (\Throwable $e) {
            // noop: evitar que un fallo en cache bloquee el login
        }
    }
}
