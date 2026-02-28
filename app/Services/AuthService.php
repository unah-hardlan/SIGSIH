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

    private function getTokenTtlSeconds(): int
    {
        $minutes = (int) config('session.lifetime', 60);

        return max(60, $minutes * 60);
    }


    public function attempt(string $usuario, string $contrasena): array
    {

        $usuario = strtoupper(trim($usuario));

        if (preg_match('/\s/', $usuario) || preg_match('/\s/', $contrasena)) {
            return ['success' => false, 'error' => 'Usuario/contraseña inválidos', 'code' => 200];
        }

        $secret = config('jwt.secret');
        if (!$secret) {
            return ['success' => false, 'error' => 'JWT_SECRET no está configurado', 'code' => 500];
        }


        $maxIntentos = (int) (\App\Models\Parametro::where('parametro', 'ADMIN.INTENTOS_INICIO_SESION')->value('valor')
            ?? \App\Models\Parametro::where('parametro', 'ADMIN_INTENTOS_INICIO SESION')->value('valor')
            ?? 3);

        $user = Usuario::where('usuario', $usuario)->first();
        if (!$user) {
            return ['success' => false, 'error' => 'Usuario/contraseña inválidos', 'code' => 200];
        }

        $requireVerify = (bool) (Parametro::where('parametro', 'AUTH.REQUIERE_VERIFICACION_CORREO')->value('valor')
            ?? Parametro::where('parametro', 'auth.require_email_verification')->value('valor')
            ?? false);
        if ($requireVerify && is_null($user->email_verified_at)) {
            return ['success' => false, 'error' => 'Correo no verificado', 'code' => 200];
        }


        if (strtoupper((string)$user->estado_usuario) === 'BLOQUEADO') {
            return ['success' => false, 'error' => 'Usuario bloqueado por intentos inválidos', 'code' => 200];
        }


        $cacheKey = 'login_attempts:' . $user->getKey();
        $attempts = cache()->get($cacheKey, 0);

        $valid = Hash::check($contrasena, $user->contrasena);
        if (!$valid) {
            $attempts++;

            cache()->put($cacheKey, $attempts, now()->addMinutes(15));

            if ($attempts >= $maxIntentos) {
                $user->estado_usuario = 'BLOQUEADO';
                $user->save();

                try {
                    $token = Password::createToken($user);
                    $user->notify(new PasswordResetNotification($token, (string)$user->correo_electronico));
                } catch (\Throwable $e) {

                    report($e);
                }

                return ['success' => false, 'error' => 'Usuario bloqueado por múltiples intentos inválidos. Desbloquear cuenta: se ha enviado un correo para restablecer la contraseña.', 'code' => 200];
            }

            $remaining = max(0, $maxIntentos - $attempts);
            $msg = 'Usuario/contraseña inválidos';
            if ($remaining > 0) {
                $msg .= ". Quedan {$remaining} intento" . ($remaining === 1 ? '' : 's') . " antes de bloquear la cuenta.";
            }
            return ['success' => false, 'error' => $msg, 'code' => 200];
        }


        cache()->forget($cacheKey);






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


    public function verifyCredentialsOnly(string $usuario, string $contrasena): array
    {
        $usuario = strtoupper(trim($usuario));
        if (preg_match('/\s/', $usuario) || preg_match('/\s/', $contrasena)) {
            return ['success' => false, 'error' => 'Usuario/contraseña inválidos', 'code' => 200];
        }
        $user = Usuario::where('usuario', $usuario)->first();
        if (!$user) {
            return ['success' => false, 'error' => 'Usuario/contraseña inválidos', 'code' => 200];
        }
        if (strtoupper((string)$user->estado_usuario) === 'BLOQUEADO') {
            return ['success' => false, 'error' => 'Usuario bloqueado por intentos inválidos', 'code' => 200];
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
                return ['success' => false, 'error' => 'Usuario bloqueado por múltiples intentos inválidos. Desbloquear cuenta: se ha enviado un correo para restablecer la contraseña.', 'code' => 200];
            }
            $remaining = max(0, $maxIntentos - $attempts);
            $msg = 'Usuario/contraseña inválidos';
            if ($remaining > 0) {
                $msg .= ".                  Quedan {$remaining} intento" . ($remaining === 1 ? '' : 's') . " antes de bloquear la cuenta.";
            }
            return ['success' => false, 'error' => $msg, 'code' => 200];
        }
        cache()->forget($cacheKey);
        return ['user' => $user];
    }


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
        $val = Parametro::where('parametro', 'auth.sessions_limit')->value('valor');
        if ($val !== null && $val !== '' && is_numeric($val)) {
            return max(0, (int) $val);
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

        // NO se escribe el cache aquí. La escritura ocurre únicamente en
        // storeSessionToken(), cuando el nuevo token ya está incluido.
        // Escribir aquí crearía una ventana donde $hasKey=true pero el
        // nuevo token aún no existe → falso SESSION_REMOVED_LIMIT.

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
            // Si el almacenamiento falla, eliminar el cache key completamente.
            // Esto garantiza que $hasKey=false en JwtMiddleware y el usuario
            // no recibe SESSION_REMOVED_LIMIT en su próxima petición.
            try {
                cache()->forget('user_sessions:' . $user->getKey());
            } catch (\Throwable $ignored) {
            }
        }
    }
}
