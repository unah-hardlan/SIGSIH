<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreUsuarioRequest;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\HistorialContrasena;
use App\Models\Parametro;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use function response;
use App\Services\BitacoraService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use App\Notifications\PasswordResetNotification;


class AuthController extends Controller
{
    public function __construct(private AuthService $authService, private BitacoraService $bitacora) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        // Normalizar: mayúsculas y trim, rechazar espacios
        $usuario = strtoupper(trim($data['usuario']));
        $password = $data['contrasena'];
        if (preg_match('/\s/', $usuario) || preg_match('/\s/', $password)) {
            return response()->json(['error' => 'Usuario/contraseña inválidos'], 401);
        }
        // Primero solo verifica credenciales; decide 2FA sin emitir token aún
        $cred = $this->authService->verifyCredentialsOnly($usuario, $password);
        if (isset($cred['error'])) {
            return response()->json(['error' => $cred['error']], $cred['code']);
        }
        /** @var \App\Models\Usuario $user */
        $user = $cred['user'];

        // Si 2FA está habilitado, emite challenge y no setea auth_token todavía
        if ($user->two_factor_enabled) {
            $challengeId = (string) \Illuminate\Support\Str::uuid();
            Cache::put('2fa:challenge:' . $challengeId, $user->getKey(), now()->addMinutes(5));
            try {
                $this->bitacora->logFor('Login', '2FA Challenge', 'Inicio de 2FA', $user->getKey());
            } catch (\Throwable $e) {
            }
            $secure = $request->isSecure() || str_starts_with((string) config('app.url'), 'https://');
            $sameSite = app()->environment('production') ? 'Strict' : 'Lax';
            return response()->json(['status' => '2fa_required'])
                ->cookie('2fa_challenge', $challengeId, 5, '/', null, $secure, true, false, $sameSite);
        }

        // Si no hay 2FA, emitir token final como antes
        $result = $this->authService->attempt($usuario, $password);
        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['code']);
        }
        // Registrar en bitácora
        try {
            $this->bitacora->logFor('Login', 'Login', 'Inicio de sesión', $result['user']['id'] ?? null);
        } catch (\Throwable $e) {
        }
        // Mantener token sólo para cookie, no exponerlo al frontend
        $token = $result['token'] ?? null;
        $payload = $result;
        unset($payload['token']);
        // Añadir redirect según rol
        try {
            $rolNombre = strtolower($result['user']['rol'] ?? ($user->rol->rol ?? ''));
            if (in_array($rolNombre, ['cliente','client','usuario','user'])) {
                $payload['redirect_url'] = route('cliente.perfil');
            } else {
                $payload['redirect_url'] = route('admin.dashboard');
            }
        } catch (\Throwable $e) {
            // fallback admin
            $payload['redirect_url'] = route('admin.dashboard');
        }
        $response = response()->json($payload, 200);
        if ($token) {
            $secure = $request->isSecure() || str_starts_with((string) config('app.url'), 'https://');
            $sameSite = app()->environment('production') ? 'Strict' : 'Lax';
            $response->cookie('auth_token', $token, 60, '/', null, $secure, true, false, $sameSite);
        }
        return $response;
    }

    public function logout(): JsonResponse
    {
        // Intentar identificar usuario desde Authorization Bearer para loguear correctamente y liberar slot de sesión.
        $userId = null;
        $tokenId = null;
        try {
            $auth = request()->header('Authorization');
            if ($auth && str_starts_with($auth, 'Bearer ')) {
                $token = substr($auth, 7);
                $payload = JWT::decode($token, new Key(config('jwt.secret'), 'HS256'));
                $userId = (int) ($payload->sub ?? null);
                $tokenId = substr(hash('sha256', $token), 0, 32);
            }
        } catch (\Throwable $e) {
        }
        // Remover token actual del registro de sesiones concurrentes
        try {
            if ($userId && $tokenId) {
                $sessionsKey = 'user_sessions:' . $userId;
                $sessions = cache()->get($sessionsKey, []);
                if (is_array($sessions) && isset($sessions[$tokenId])) {
                    unset($sessions[$tokenId]);
                    cache()->put($sessionsKey, $sessions, now()->addHours(1));
                }
            }
        } catch (\Throwable $e) {
        }
        try {
            $this->bitacora->logFor('Login', 'Logout', 'Cierre de sesión', $userId);
        } catch (\Throwable $e) {
        }
        $req = request();
        $secure = ($req && $req->isSecure()) || str_starts_with((string) config('app.url'), 'https://');
        $sameSite = app()->environment('production') ? 'Strict' : 'Lax';
        return response()->json(['ok' => true])->cookie('auth_token', null, -1, '/', null, $secure, true, false, $sameSite);
    }

    // Registro que crea un usuario usando las mismas reglas que StoreUsuarioRequest.
    // Si el usuario ya existe responde 409, si se crea devuelve el mismo formato que login.
    public function register(StoreUsuarioRequest $request): JsonResponse
    {
        $data = $request->validated();

        // verificar existencia por usuario o correo
        $exists = Usuario::where('usuario', $data['usuario'])
            ->orWhere('correo_electronico', $data['correo_electronico'])
            ->first();
        if ($exists) {
            return response()->json(['error' => 'El usuario o correo ya existe'], 409);
        }

        // Asignar SIEMPRE rol Cliente (ignorando lo enviado) para registro público
        $rolPk = Rol::where('rol', 'Cliente')->value('id_rol_pk');
        if (!$rolPk) {
            // fallback al primer rol disponible
            $rolPk = Rol::orderBy('id_rol_pk')->value('id_rol_pk');
        }
        if ($rolPk) {
            $data['id_rol_fk'] = $rolPk;
        } else {
            return response()->json([
                'error' => 'No hay un rol por defecto disponible. Configure al menos un rol.'
            ], 422);
        }

        $usuario = new Usuario();
        $usuario->fill($data);
        // Forzar usuario en mayúsculas al persistir
        $usuario->usuario = strtoupper(trim($usuario->usuario));
        $usuario->contrasena = Hash::make($data['contrasena']);
        // Forzar primer_ingreso = 1 (nuevo usuario debe completar perfil)
        $usuario->primer_ingreso = 1;
        $usuario->save();

        $tokenResult = $this->authService->tokenForUser($usuario);
        if (isset($tokenResult['error'])) {
            return response()->json(['error' => $tokenResult['error']], $tokenResult['code']);
        }
        $token = $tokenResult['token'] ?? null;
        $payload = $tokenResult;
        unset($payload['token']);
    // Añadir sugerencia de redirección (frontend puede usarlo)
    $payload['redirect_url'] = route('cliente.perfil');
    $response = response()->json($payload, 201);
        if ($token) {
            $secure = $request->isSecure() || str_starts_with((string) config('app.url'), 'https://');
            $sameSite = app()->environment('production') ? 'Strict' : 'Lax';
            $response->cookie('auth_token', $token, 60, '/', null, $secure, true, false, $sameSite);
        }
        return $response;
    }

    public function showPasswordRecoverForm()
    {
        return view('auth.password-recover');
    }

    public function sendPasswordResetEmail(Request $request): JsonResponse
    {
        $data = $request->validate([
            'identifier' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
        ]);

        $rawIdentifier = $data['identifier'] ?? $data['email'] ?? null;
        $identifier = is_string($rawIdentifier) ? trim($rawIdentifier) : '';

        if ($identifier === '') {
            return response()->json([
                'message' => 'Debes ingresar tu correo electrónico o nombre de usuario.'
            ], 422);
        }

        $normalizedEmail = null;
        $query = Usuario::query();
        $upperIdentifier = strtoupper($identifier);
        $lowerIdentifier = strtolower($identifier);

        $usuario = $query
            ->where(function ($q) use ($upperIdentifier, $lowerIdentifier) {
                $q->whereRaw('LOWER(correo_electronico) = ?', [$lowerIdentifier])
                    ->orWhere('usuario', $upperIdentifier);
            })
            ->first();

        if ($usuario && $usuario->correo_electronico) {
            $normalizedEmail = strtolower($usuario->correo_electronico);
        }

        if (!$usuario) {
            return response()->json([
                'message' => 'No encontramos ninguna cuenta que coincida con los datos proporcionados.'
            ], 404);
        }

        if (!$normalizedEmail) {
            return response()->json([
                'message' => 'El usuario no tiene un correo electrónico registrado. Comunícate con el administrador.'
            ], 422);
        }

        $cooldownMinutes = $this->getParametroInt([
            'AUTH.PASSWORD_RESET.COOLDOWN_MINUTES',
            'auth.password_reset.cooldown_minutes'
        ], 5);
        $maxPerDay = $this->getParametroInt([
            'AUTH.PASSWORD_RESET.MAX_PER_DAY',
            'auth.password_reset.max_per_day'
        ], 5);
        $expireMinutes = $this->getParametroInt([
            'AUTH.PASSWORD_RESET.EXPIRE_MINUTES',
            'auth.password_reset.expire_minutes'
        ], (int) config('auth.passwords.users.expire', 60));

        if ($expireMinutes > 0) {
            config(['auth.passwords.users.expire' => $expireMinutes]);
        }

        if ($cooldownMinutes > 0) {
            config(['auth.passwords.users.throttle' => max(1, $cooldownMinutes) * 60]);
        }

        if ($cooldownMinutes > 0) {
            $table = config('auth.passwords.users.table', 'password_reset_tokens');
            $lastRequest = DB::table($table)
                ->where('email', $normalizedEmail)
                ->value('created_at');

            if ($lastRequest) {
                $nextAllowed = Carbon::parse($lastRequest)->addMinutes($cooldownMinutes);
                if (now()->lt($nextAllowed)) {
                    $secondsRemaining = now()->diffInSeconds($nextAllowed);
                    $minutesRemaining = max(1, (int) ceil($secondsRemaining / 60));

                    return response()->json([
                        'message' => "Debes esperar {$minutesRemaining} minuto(s) antes de solicitar otro correo de recuperación."
                    ], 429);
                }
            }
        }

        $rateLimiterKey = null;
        $rateLimiterTtl = 60 * 60 * 24;

        if ($maxPerDay > 0) {
            $rateLimiterKey = 'password-reset:max-per-day:' . sha1($normalizedEmail);

            if (RateLimiter::tooManyAttempts($rateLimiterKey, $maxPerDay)) {
                $secondsRemaining = RateLimiter::availableIn($rateLimiterKey);
                $minutesRemaining = max(1, (int) ceil($secondsRemaining / 60));

                return response()->json([
                    'message' => "Alcanzaste el límite de solicitudes de recuperación. Intenta nuevamente en {$minutesRemaining} minuto(s)."
                ], 429);
            }
        }
        /** @var \Illuminate\Auth\Passwords\PasswordBroker $broker */
        $broker = Password::broker();

        try {
            $token = $broker->createToken($usuario);
            $usuario->notify(new PasswordResetNotification($token, $normalizedEmail));

            if ($rateLimiterKey) {
                RateLimiter::hit($rateLimiterKey, $rateLimiterTtl);
            }

            try {
                $this->bitacora->logFor(
                    'Password Reset',
                    'Solicitud',
                    'Solicitud de recuperación de contraseña',
                    $usuario->getKey()
                );
            } catch (\Throwable $e) {
            }

            return response()->json([
                'message' => 'Se han enviado las instrucciones de recuperación a tu correo electrónico.'
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'No se pudo enviar el correo de recuperación en este momento. Inténtalo más tarde.'
            ], 500);
        }
    }

    public function showPasswordResetForm(Request $request, string $token)
    {
        return view('auth.password-reset', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = strtolower($data['email']);

        $usuario = Usuario::whereRaw('LOWER(correo_electronico) = ?', [$email])->first();

        if (!$usuario || !$usuario->correo_electronico) {
            return response()->json([
                'message' => 'El token de recuperación no es válido o ha expirado.'
            ], 400);
        }
        /** @var \Illuminate\Auth\Passwords\PasswordBroker $broker */
        $broker = Password::broker();

        if (!$broker->tokenExists($usuario, $data['token'])) {
            return response()->json([
                'message' => 'El token de recuperación no es válido o ha expirado.'
            ], 400);
        }

        $uid = $usuario->getKey();
        $historialQuery = HistorialContrasena::where('id_usuario_fk', $uid)
            ->orderByDesc('fecha_creacion')
            ->orderByDesc('id_hist_pk')
            ->limit(5);

        $hashes = $historialQuery->pluck('contrasena');
        foreach ($hashes as $hash) {
            if (!is_string($hash) || $hash === '') { continue; }
            $hashStr = (string) $hash;
            $isKnownHash = preg_match('/^\$(2y|argon2id|argon2i)\$/', $hashStr) === 1;
            $reused = false;
            if ($isKnownHash) {
                try {
                    $reused = Hash::check($data['password'], $hashStr);
                } catch (\Throwable $e) {
                    $reused = false;
                }
            } else {
                $reused = hash_equals($hashStr, (string) $data['password']);
            }
            if ($reused) {
                return response()->json([
                    'message' => 'No puedes reutilizar una de tus últimas 5 contraseñas.'
                ], 422);
            }
        }

        // Asignación segura: el mutator del modelo se encargará de hashear si es necesario
        $usuario->contrasena = $data['password'];
        $usuario->primer_ingreso = 0;
        $usuario->save();

        try {
            $hashed = $usuario->contrasena;
            HistorialContrasena::create([
                'contrasena' => $hashed,
                'id_usuario_fk' => $uid,
                'creado_por' => $usuario->usuario ?? 'system',
                'fecha_creacion' => now(),
            ]);

            $idsToKeep = HistorialContrasena::where('id_usuario_fk', $uid)
                ->orderByDesc('fecha_creacion')
                ->orderByDesc('id_hist_pk')
                ->limit(5)
                ->pluck('id_hist_pk');

            HistorialContrasena::where('id_usuario_fk', $uid)
                ->whereNotIn('id_hist_pk', $idsToKeep)
                ->delete();
        } catch (\Throwable $e) {
        }
        $broker->deleteToken($usuario);

        try {
            $this->bitacora->logFor(
                'Password Reset',
                'Actualización',
                'Restablecimiento de contraseña vía recuperación',
                $uid
            );
        } catch (\Throwable $e) {
        }

        return response()->json([
            'message' => 'Tu contraseña ha sido restablecida con éxito.'
        ]);
    }

    private function getParametroInt(array $keys, int $default): int
    {
        foreach ($keys as $key) {
            $value = Parametro::where('parametro', $key)->value('valor');
            if ($value === null || $value === '') {
                continue;
            }

            if (is_numeric($value)) {
                return (int) $value;
            }

            if (is_string($value)) {
                $filtered = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                if ($filtered !== '' && is_numeric($filtered)) {
                    return (int) $filtered;
                }
            }
        }

        return $default;
    }
}
