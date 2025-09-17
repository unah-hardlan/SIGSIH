<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\Parametro;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;


class AuthService
{
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
        $maxIntentos = (int) (\App\Models\Parametro::where('parametro','ADMIN.INTENTOS_INICIO_SESION')->value('valor')
            ?? \App\Models\Parametro::where('parametro','ADMIN_INTENTOS_INICIO SESION')->value('valor')
            ?? 3);

        $user = Usuario::where('usuario', $usuario)->first();
        if (!$user) {
            return ['error' => 'Usuario/contraseña inválidos', 'code' => 401];
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
            if ($attempts >= $maxIntentos) {
                $user->estado_usuario = 'BLOQUEADO';
                $user->save();
                return ['error' => 'Usuario bloqueado por múltiples intentos inválidos', 'code' => 423];
            }
            return ['error' => 'Usuario/contraseña inválidos', 'code' => 401];
        }

        // reset intentos al éxito
        cache()->forget($cacheKey);

        // Enforce concurrent sessions limit (limpiar expiradas antes de contar)
        $limit = (int) (Parametro::where('parametro', 'AUTH.LIMITE_SESIONES')->value('valor')
            ?? Parametro::where('parametro', 'auth.sessions_limit')->value('valor')
            ?? 1);
        $sessionsKey = 'user_sessions:' . $user->getKey();
        $sessions = cache()->get($sessionsKey, []);
        if (!is_array($sessions)) { $sessions = []; }
        $nowTs = time();
        // limpiar expiradas previas
        $sessions = array_filter($sessions, fn($exp) => (int)$exp > $nowTs);
        cache()->put($sessionsKey, $sessions, now()->addHours(1));
        if ($limit > 0 && count($sessions) >= $limit) {
            // Política: si se alcanzó el límite, expulsar la sesión que expira primero (más antigua)
            asort($sessions); // orden por exp asc
            $firstKey = array_key_first($sessions);
            if ($firstKey !== null) {
                unset($sessions[$firstKey]);
                cache()->put($sessionsKey, $sessions, now()->addHours(1));
            }
        }

        $payload = [
            'sub'  => $user->getKey(),
            'name' => $user->nombre_usuario,
            'iat'  => time(),
            'exp'  => time() + 3600,
        ];

        $token = JWT::encode($payload, $secret, 'HS256');

        // Registrar la sesión (token hash) en cache por 1h
        try {
            $tokenId = substr(hash('sha256', $token), 0, 32);
            $sessions[$tokenId] = time() + 3600;
            // Limpiar expiradas post-inserción
            $now = time();
            $sessions = array_filter($sessions, fn($exp) => $exp > $now);
            cache()->put($sessionsKey, $sessions, now()->addHours(1));
        } catch (\Throwable $e) {}

        return [
            'token' => $token,
            'user'  => [
                'id'      => $user->getKey(),
                'usuario' => $user->usuario,
                'nombre'  => $user->nombre_usuario,
                'correo'  => $user->correo_electronico,
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
            $maxIntentos = (int) (\App\Models\Parametro::where('parametro','ADMIN.INTENTOS_INICIO_SESION')->value('valor')
                ?? \App\Models\Parametro::where('parametro','ADMIN_INTENTOS_INICIO SESION')->value('valor')
                ?? 3);
            if ($attempts >= $maxIntentos) {
                $user->estado_usuario = 'BLOQUEADO';
                $user->save();
                return ['error' => 'Usuario bloqueado por múltiples intentos inválidos', 'code' => 423];
            }
            return ['error' => 'Usuario/contraseña inválidos', 'code' => 401];
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

        $payload = [
            'sub'  => $user->getKey(),
            'name' => $user->nombre_usuario,
            'iat'  => time(),
            'exp'  => time() + 3600,
        ];

        $token = JWT::encode($payload, $secret, 'HS256');

        return [
            'token' => $token,
            'user'  => [
                'id'      => $user->getKey(),
                'usuario' => $user->usuario,
                'nombre'  => $user->nombre_usuario,
                'correo'  => $user->correo_electronico,
            ]
        ];
    }
}
