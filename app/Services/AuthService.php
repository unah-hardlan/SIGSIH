<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\Parametro;
use App\Models\SesionUsuario;
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
        $this->prepareSessions($user, $limit);

        $ttl = $this->getTokenTtlSeconds();
        $payload = [
            'sub'  => $user->getKey(),
            'name' => $user->nombre,
            'iat'  => time(),
            'exp'  => time() + $ttl,
        ];

        $token = JWT::encode($payload, $secret, 'HS256');

        $this->storeSessionToken($user, $token);

        return [
            'token' => $token,
            'user'  => [
                'id'      => $user->getKey(),
                'usuario' => $user->usuario,
                'nombre'  => $user->nombre,
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
            'name' => $user->nombre,
            'iat'  => time(),
            'exp'  => time() + $ttl,
        ];

        $token = JWT::encode($payload, $secret, 'HS256');
        $limit = $this->determineSessionLimit($user);
        $this->prepareSessions($user, $limit);
        $this->storeSessionToken($user, $token);

        return [
            'token' => $token,
            'user'  => [
                'id'      => $user->getKey(),
                'usuario' => $user->usuario,
                'nombre'  => $user->nombre,
                'correo'  => $user->correo_electronico,
                'rol'     => $user->rol->rol ?? null,
            ]
        ];
    }

    private function determineSessionLimit(Usuario $user): int
    {
        $val = Parametro::where('parametro', 'auth.sessions_limit')->value('valor')
            ?? Parametro::where('parametro', 'SESIONES_SIMULTANEAS')->value('valor');
        if ($val !== null && $val !== '' && is_numeric($val)) {
            return max(0, (int) $val);
        }

        return 1;
    }

    private function prepareSessions(Usuario $user, int $limit): void
    {
        // 1. Eliminar sesiones expiradas del usuario.
        SesionUsuario::where('id_usuario_fk', $user->getKey())
            ->where('fecha_expiracion', '<', now())
            ->delete();

        // 2. Si se supera el límite, evictar las sesiones más antiguas.
        if ($limit > 0) {
            $count = SesionUsuario::where('id_usuario_fk', $user->getKey())->count();
            if ($count >= $limit) {
                $toEvict = $count - $limit + 1;
                $oldest = SesionUsuario::where('id_usuario_fk', $user->getKey())
                    ->orderBy('fecha_expiracion', 'asc')
                    ->limit($toEvict)
                    ->pluck('id_sesion_pk');
                SesionUsuario::whereIn('id_sesion_pk', $oldest)->delete();
            }
        }
    }

    private function storeSessionToken(Usuario $user, string $token): void
    {
        try {
            $tokenHash = hash('sha256', $token);
            $ttl     = $this->getTokenTtlSeconds();
            SesionUsuario::create([
                'id_usuario_fk'   => $user->getKey(),
                'token_refresh'   => $tokenHash,
                'ip_direccion'    => request()?->ip(),
                'user_agent'      => substr((string) (request()?->userAgent() ?? ''), 0, 500),
                'fecha_creacion'  => now(),
                'fecha_expiracion' => now()->addSeconds($ttl),
                'activo'          => 1,
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
