<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreUsuarioRequest;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\HistorialContrasena;
use App\Models\Cliente;
use App\Models\Parametro;
use App\Models\SesionUsuario;
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
use Illuminate\Support\Str;
use App\Notifications\PasswordResetNotification;
use App\Notifications\VerifyEmailNotification;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;


class AuthController extends Controller
{
    public function __construct(private AuthService $authService, private BitacoraService $bitacora) {}

    /**
     * Punto de entrada raíz dinámico según el rol del usuario autenticado.
     * Sustituye a AuthRedirectController::home().
     */
    public function home(Request $request)
    {
        return $this->redirectForUser();
    }

    /**
     * Redirección utilizada luego de un login efectuado desde el frontend.
     * Sustituye a AuthRedirectController::postAuth().
     */
    public function postAuth(Request $request)
    {
        return $this->redirectForUser();
    }

    /**
     * Render de la vista de login (antes LoginViewController).
     */
    public function showLoginView()
    {
        return view('auth.login');
    }

    /**
     * Lógica de decisión de destino post‑auth. Centralizada aquí para evitar
     * múltiples controladores pequeños orientados sólo a redirecciones.
     */
    private function redirectForUser()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }
        $rolNombre = strtolower($user->rol->rol ?? '');
        if (in_array($rolNombre, ['cliente', 'client', 'usuario', 'user'])) {
            $persona = \App\Models\Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();
            if (!$persona || empty($persona->primer_nombre) || empty($persona->primer_apellido) || empty($persona->dni) || empty($persona->id_genero_fk)) {
                return redirect()->route('cliente.configurar-perfil');
            }
            return redirect()->route('cliente.perfil');
        }
        return redirect()->route('admin.dashboard');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $usuario = strtoupper(trim($data['usuario']));
        $password = $data['contrasena'];
        $loginBaseKey = $this->progressiveBaseKey($request, 'login', $usuario);
        if ($lockedResponse = $this->progressiveLockResponse($loginBaseKey, 'inicio de sesión')) {
            return $lockedResponse;
        }

        if (preg_match('/\s/', $usuario) || preg_match('/\s/', $password)) {
            $this->registerProgressiveFailure($loginBaseKey);
            return response()->json(['success' => false, 'error' => 'Usuario/contraseña inválidos'], 200);
        }

        $cred = $this->authService->verifyCredentialsOnly($usuario, $password);
        if (isset($cred['error'])) {
            $this->registerProgressiveFailure($loginBaseKey);
            return response()->json(['success' => false, 'error' => $cred['error']], 200);
        }

        $user = $cred['user'];

        if ((bool) ($user->pendiente_cambio_contrasena ?? false)) {
            $resetUrl = $this->buildForcedResetUrl($user);
            return response()->json([
                'status' => 'password_reset_required',
                'message' => 'Debes cambiar tu contraseña para poder ingresar al sistema.',
                'reset_url' => $resetUrl,
            ], 403);
        }


        $requireVerify = (bool) (\App\Models\Parametro::where('parametro', 'AUTH.REQUIERE_VERIFICACION_CORREO')->value('valor')
            ?? \App\Models\Parametro::where('parametro', 'auth.require_email_verification')->value('valor')
            ?? false);
        if ($requireVerify && !$user->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'email_verification_required',
                'message' => 'Debes verificar tu correo antes de iniciar sesión.',
                'email' => (string) $user->correo_electronico,
            ], 403);
        }


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


        $result = $this->authService->attempt($usuario, $password);
        if (isset($result['error'])) {
            $this->registerProgressiveFailure($loginBaseKey);
            return response()->json(['success' => false, 'error' => $result['error']], 200);
        }

        $this->clearProgressiveFailures($loginBaseKey);

        $token = $result['token'] ?? null;
        $payload = $result;
        unset($payload['token']);

        try {
            if ((bool) ($user->pendiente_cambio_contrasena ?? false)) {
                $rolNombre = strtolower($result['user']['rol'] ?? ($user->rol->rol ?? ''));
                $payload['force_password_change'] = true;
                $payload['redirect_url'] = in_array($rolNombre, ['cliente', 'client', 'usuario', 'user'])
                    ? route('cliente.perfil')
                    : route('admin.perfil');
            } else {
                $rolNombre = strtolower($result['user']['rol'] ?? ($user->rol->rol ?? ''));
                if (in_array($rolNombre, ['cliente', 'client', 'usuario', 'user'])) {

                    $persona = \App\Models\Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();

                    if (
                        !$persona ||
                        empty($persona->primer_nombre) ||
                        empty($persona->primer_apellido) ||
                        empty($persona->dni) ||
                        empty($persona->id_genero_fk)
                    ) {
                        $payload['redirect_url'] = route('cliente.configurar-perfil');
                    } else {
                        $payload['redirect_url'] = route('cliente.perfil');
                    }
                } else {
                    $payload['redirect_url'] = route('admin.dashboard');
                }
            }
        } catch (\Throwable $e) {

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

    public function logout(): \Illuminate\Http\Response|JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $tokenId = null;
        $userId  = null;
        try {
            $req   = request();
            // Leer token desde header Bearer o desde cookie httpOnly
            $raw   = null;
            $auth  = $req->header('Authorization');
            if ($auth && str_starts_with($auth, 'Bearer ')) {
                $raw = substr($auth, 7);
            } elseif ($req->cookie('auth_token')) {
                $raw = $req->cookie('auth_token');
            }
            if ($raw) {
                $payload = JWT::decode($raw, new Key(config('jwt.secret'), 'HS256'));
                $userId  = (int) ($payload->sub ?? null);
                $tokenId = substr(hash('sha256', $raw), 0, 32);
            }
        } catch (\Throwable $e) {
        }

        try {
            if ($tokenId) {
                SesionUsuario::where('id_sesion_pk', $tokenId)->delete();
            }
        } catch (\Throwable $e) {
        }
        $req = request();
        $secure = ($req && $req->isSecure()) || str_starts_with((string) config('app.url'), 'https://');
        $sameSite = app()->environment('production') ? 'Strict' : 'Lax';
        $cacheHeaders = [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];
        if ($req && ($req->expectsJson() || $req->wantsJson())) {
            return response()->json(['ok' => true, 'redirect' => route('login')], 200, $cacheHeaders)
                ->cookie('auth_token', null, -1, '/', null, $secure, true, false, $sameSite);
        }
        $redirect = redirect()->route('login');
        $redirect->withHeaders($cacheHeaders);
        $redirect->cookie('auth_token', null, -1, '/', null, $secure, true, false, $sameSite);
        return $redirect;
    }


    public function register(StoreUsuarioRequest $request): JsonResponse
    {
        $data = $request->validated();
        $registerIdentifier = (string) ($data['correo_electronico'] ?? ($data['usuario'] ?? ''));
        $registerBaseKey = $this->progressiveBaseKey($request, 'register', $registerIdentifier);
        if ($lockedResponse = $this->progressiveLockResponse($registerBaseKey, 'registro')) {
            return $lockedResponse;
        }

        $exists = Usuario::where('usuario', $data['usuario'])
            ->orWhere('correo_electronico', $data['correo_electronico'])
            ->first();
        if ($exists) {
            $this->registerProgressiveFailure($registerBaseKey);
            return response()->json(['error' => 'El usuario o correo ya existe'], 409);
        }

        $rolPk = Rol::where('rol', 'Cliente')->value('id_rol_pk');
        if (!$rolPk) {
            $rolPk = Rol::orderBy('id_rol_pk')->value('id_rol_pk');
        }
        if ($rolPk) {
            $data['id_rol_fk'] = $rolPk;
        } else {
            $this->registerProgressiveFailure($registerBaseKey);
            return response()->json([
                'error' => 'No hay un rol por defecto disponible. Configure al menos un rol.'
            ], 422);
        }

        $usuario = new Usuario();
        $usuario->fill($data);
        $usuario->usuario = strtoupper(trim($usuario->usuario));
        $usuario->contrasena = Hash::make($data['contrasena']);
        $usuario->primer_ingreso = 1;
        $usuario->save();


        try {
            Cliente::firstOrCreate(
                ['id_cliente_pk' => $usuario->id_usuario_pk],
                [
                    'tipo_cliente' => 'persona',
                    'estado_cliente' => 'activo',
                    'fecha_registro' => now(),
                ]
            );
        } catch (\Throwable $e) {
        }


        try {
            $adminRoleId = Rol::where('rol', 'Administrador')->value('id_rol_pk');
            if ($adminRoleId) {
                $admins = Usuario::where('id_rol_fk', (int)$adminRoleId)->get();
                if ($admins->count() > 0) {
                    $payload = [
                        'title' => 'Nuevo usuario registrado',
                        'body'  => sprintf('Usuario: %s (%s)', $usuario->usuario, (string) $usuario->correo_electronico),
                        'url'   => '/admin/usuarios',
                        'icon'  => 'fa-user-plus',
                        'severity' => 'info',
                        'module' => 'usuarios',
                        'meta'  => [
                            'id_usuario_pk' => $usuario->id_usuario_pk,
                            'nombre' => $usuario->nombre,
                        ],
                    ];
                    Notification::send($admins, new SystemNotification($payload));
                }
            }
        } catch (\Throwable $e) {
        }


        $requireVerify = (bool) (Parametro::where('parametro', 'AUTH.REQUIERE_VERIFICACION_CORREO')->value('valor')
            ?? Parametro::where('parametro', 'auth.require_email_verification')->value('valor')
            ?? false);
        if ($requireVerify) {
            $usuario->email_verification_token = bin2hex(random_bytes(20));
            $usuario->email_verification_sent_at = now();
            $usuario->save();
            try {
                $usuario->notify(new VerifyEmailNotification($usuario->email_verification_token));
            } catch (\Throwable $e) {
            }
            $this->clearProgressiveFailures($registerBaseKey);
            return response()->json(['status' => 'verification_sent'], 201);
        }


        $tokenResult = $this->authService->tokenForUser($usuario);
        if (isset($tokenResult['error'])) {
            $this->registerProgressiveFailure($registerBaseKey);
            return response()->json(['error' => $tokenResult['error']], $tokenResult['code']);
        }
        $token = $tokenResult['token'] ?? null;
        $payload = $tokenResult;
        unset($payload['token']);
        $payload['redirect_url'] = route('cliente.perfil');
        $response = response()->json($payload, 201);
        if ($token) {
            $secure = $request->isSecure() || str_starts_with((string) config('app.url'), 'https://');
            $sameSite = app()->environment('production') ? 'Strict' : 'Lax';
            $response->cookie('auth_token', $token, 60, '/', null, $secure, true, false, $sameSite);
        }
        $this->clearProgressiveFailures($registerBaseKey);
        return $response;
    }


    public function resendVerification(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        $email = strtolower($request->input('email'));

        $user = \App\Models\Usuario::whereRaw('LOWER(correo_electronico) = ?', [$email])->first();
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        if ($user->hasVerifiedEmail()) {
            return response()->json(['status' => 'already_verified']);
        }


        $cooldownMinutes = $this->getParametroInt([
            'AUTH.VERIFY_EMAIL.COOLDOWN_MINUTES',
            'auth.verify_email.cooldown_minutes',
        ], 5);
        $maxPerDay = $this->getParametroInt([
            'AUTH.VERIFY_EMAIL.MAX_PER_DAY',
            'auth.verify_email.max_per_day',
        ], 5);


        if ($cooldownMinutes > 0 && $user->email_verification_sent_at) {
            $nextAllowed = \Illuminate\Support\Carbon::parse($user->email_verification_sent_at)->addMinutes($cooldownMinutes);
            if (now()->lt($nextAllowed)) {
                $secondsRemaining = now()->diffInSeconds($nextAllowed);
                $minutesRemaining = max(1, (int) ceil($secondsRemaining / 60));
                return response()->json([
                    'message' => "Debes esperar {$minutesRemaining} minuto(s) antes de solicitar otro correo de verificación.",
                    'retry_after_seconds' => $secondsRemaining,
                ], 429);
            }
        }


        $rateLimiterKey = null;
        $rateLimiterTtl = 60 * 60 * 24;
        if ($maxPerDay > 0) {
            $rateLimiterKey = 'verify-email:max-per-day:' . sha1($email);
            if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($rateLimiterKey, $maxPerDay)) {
                $secondsRemaining = \Illuminate\Support\Facades\RateLimiter::availableIn($rateLimiterKey);
                $minutesRemaining = max(1, (int) ceil($secondsRemaining / 60));
                return response()->json([
                    'message' => "Alcanzaste el límite de reenvíos por hoy. Intenta nuevamente en {$minutesRemaining} minuto(s).",
                    'retry_after_seconds' => $secondsRemaining,
                ], 429);
            }
        }


        $user->email_verification_token = bin2hex(random_bytes(20));
        $user->email_verification_sent_at = now();
        $user->save();
        try {
            $user->notify(new VerifyEmailNotification($user->email_verification_token));
        } catch (\Throwable $e) {
        }

        if ($rateLimiterKey) {
            \Illuminate\Support\Facades\RateLimiter::hit($rateLimiterKey, $rateLimiterTtl);
        }

        return response()->json([
            'status' => 'verification_sent',
            'retry_after_seconds' => max(1, (int) $cooldownMinutes) * 60,
        ]);
    }


    public function verifyEmail(Request $request)
    {

        if (!($request->expectsJson() || $request->wantsJson())) {
            return redirect()->route('verify.email.page', [
                'token' => $request->query('token'),
                'email' => $request->query('email'),
            ]);
        }
        $request->validate(['token' => 'required|string', 'email' => 'required|email']);
        $email = strtolower($request->input('email'));

        $user = \App\Models\Usuario::whereRaw('LOWER(correo_electronico) = ?', [$email])->first();
        if (!$user) return response()->json(['message' => 'Usuario no encontrado'], 404);
        if ($user->hasVerifiedEmail()) return response()->json(['status' => 'already_verified']);
        if (!hash_equals((string)$user->email_verification_token, (string)$request->input('token'))) {
            return response()->json(['message' => 'Token inválido'], 422);
        }
        $user->markEmailAsVerified();
        return response()->json(['status' => 'verified']);
    }


    public function verifyEmailPage(Request $request)
    {
        $status = 'error';
        $title = 'Hubo un problema';
        $message = 'El enlace de verificación no es válido.';

        $token = (string) $request->query('token', '');
        $email = strtolower((string) $request->query('email', ''));
        if ($token === '' || $email === '') {

            return redirect()->route('login');
        }


        $user = \App\Models\Usuario::whereRaw('LOWER(correo_electronico) = ?', [$email])->first();
        if (!$user) {
            return view('auth.verify-email', [
                'status' => 'not_found',
                'title' => 'Usuario no encontrado',
                'message' => 'No pudimos encontrar una cuenta asociada a este correo.',
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            return view('auth.verify-email', [
                'status'  => 'already_verified',
                'title'   => 'Tu correo ya está verificado',
                'message' => 'Ahora puedes iniciar sesión con normalidad.',
            ]);
        }

        if (!hash_equals((string) $user->email_verification_token, $token)) {
            return view('auth.verify-email', [
                'status'  => 'invalid_token',
                'title'   => 'Enlace no válido',
                'message' => 'El enlace de verificación es inválido o ya fue utilizado. Solicita uno nuevo desde el inicio de sesión.',
            ]);
        }


        $user->markEmailAsVerified();
        return view('auth.verify-email', [
            'status'  => 'verified',
            'title'   => '¡Correo verificado!',
            'message' => 'Tu correo fue verificado correctamente. Ya puedes iniciar sesión.',
        ]);
    }

    public function showPasswordRecoverForm()
    {
        return view('auth.password-recover');
    }

    public function sendPasswordResetEmail(Request $request): JsonResponse
    {
        $data = $request->validate([
            'identifier' => ['nullable', 'string', 'max:255', 'regex:/^(?!.*\s)[A-Za-z0-9._%+\-\x{00C0}-\x{00FF}@]+$/u'],
            'email' => ['nullable', 'string', 'max:255', 'regex:/^(?!.*\s)[A-Za-z0-9._%+\-\x{00C0}-\x{00FF}@]+$/u'],
        ], [
            'identifier.regex' => 'El identificador no puede contener espacios ni caracteres de alfabetos no latinos (por ejemplo: 名前).',
            'email.regex' => 'El correo no puede contener espacios ni caracteres de alfabetos no latinos (por ejemplo: 名前).',
        ]);

        $rawIdentifier = $data['identifier'] ?? $data['email'] ?? null;
        $identifier = is_string($rawIdentifier) ? trim($rawIdentifier) : '';
        $recoveryBaseKey = $this->progressiveBaseKey($request, 'password-recovery', $identifier);
        if ($lockedResponse = $this->progressiveLockResponse($recoveryBaseKey, 'recuperación de contraseña')) {
            return $lockedResponse;
        }

        if ($identifier === '') {
            $this->registerProgressiveFailure($recoveryBaseKey);
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
            $this->registerProgressiveFailure($recoveryBaseKey);
            return response()->json([
                'message' => 'No encontramos ninguna cuenta que coincida con los datos proporcionados.'
            ], 404);
        }

        if (!$normalizedEmail) {
            $this->registerProgressiveFailure($recoveryBaseKey);
            return response()->json([
                'message' => 'El usuario no tiene un correo electrónico registrado. Comunícate con el administrador.'
            ], 422);
        }

        $cooldownMinutes = $this->getParametroInt([
            'AUTH.PASSWORD_RESET.COOLDOWN_MINUTES',
            'auth.password_reset.cooldown_minutes'
        ], 5);
        $maxPerDay = 0;
        $expireMinutes = $this->getParametroInt([
            'AUTH.PASSWORD_RESET.EXPIRE_MINUTES',
            'auth.password_reset.expire_minutes'
        ], (int) config('auth.passwords.users.expire', 60));

        if ($expireMinutes > 0) {
            config(['auth.passwords.users.expire' => $expireMinutes]);
        }

        if ($cooldownMinutes > 0) {

            $cooldownMinutes = min(25, max(1, $cooldownMinutes));
            config(['auth.passwords.users.throttle' => $cooldownMinutes * 60]);
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
                    $horas = intdiv($minutesRemaining, 60);
                    $minutos = $minutesRemaining % 60;
                    $tiempo = '';
                    if ($horas > 0) $tiempo .= $horas . ' hora' . ($horas > 1 ? 's' : '');
                    if ($horas > 0 && $minutos > 0) $tiempo .= ' y ';
                    if ($minutos > 0) $tiempo .= $minutos . ' minuto' . ($minutos > 1 ? 's' : '');
                    return response()->json([
                        'message' => "Debes esperar {$tiempo} antes de solicitar otro correo de recuperación."
                    ], 429);
                }
            }
        }

        $rateLimiterKey = null;
        $rateLimiterTtl = 60 * 60 * 24;

        if ($maxPerDay > 0) {
        }

        /** @var \Illuminate\Auth\Passwords\PasswordBroker $broker */
        $broker = Password::broker();

        try {
            $token = $this->createPasswordResetToken((string) $usuario->correo_electronico);
            $usuario->notify(new PasswordResetNotification($token, $normalizedEmail));

            if ($rateLimiterKey) {
                RateLimiter::hit($rateLimiterKey, $rateLimiterTtl);
            }

            $this->clearProgressiveFailures($recoveryBaseKey);

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

    private function progressiveBaseKey(Request $request, string $action, ?string $identifier = null): string
    {
        $normalized = strtolower(trim((string) $identifier));
        return 'rl:progressive:' . $action . ':' . sha1($request->ip() . '|' . $normalized);
    }

    private function progressiveLockResponse(string $baseKey, string $actionLabel): ?JsonResponse
    {
        $lockKey = $baseKey . ':lock';
        if (!RateLimiter::tooManyAttempts($lockKey, 1)) {
            return null;
        }

        $secondsRemaining = max(1, RateLimiter::availableIn($lockKey));
        $minutesRemaining = max(1, (int) ceil($secondsRemaining / 60));
        return response()->json([
            'message' => "Demasiados intentos de {$actionLabel}. Debes esperar {$minutesRemaining} minuto(s) antes de volver a intentar.",
            'retry_after_seconds' => $secondsRemaining,
        ], 429);
    }

    private function registerProgressiveFailure(string $baseKey): void
    {
        $failsKey = $baseKey . ':fails';
        $lockKey = $baseKey . ':lock';

        $attempts = RateLimiter::attempts($failsKey) + 1;
        RateLimiter::hit($failsKey, 60 * 60 * 24);

        if ($attempts < 3) {
            return;
        }

        $step = min(8, $attempts - 2);
        $lockSeconds = min(60 * 60 * 24, 30 * (2 ** ($step - 1)));
        RateLimiter::hit($lockKey, $lockSeconds);
    }

    private function clearProgressiveFailures(string $baseKey): void
    {
        RateLimiter::clear($baseKey . ':fails');
        RateLimiter::clear($baseKey . ':lock');
    }

    public function showPasswordResetForm(Request $request, string $token)
    {
        return view('auth.password-reset', [
            'token' => $token,
            'email' => $request->query('email'),
            'forced' => (bool) $request->query('forced', false),
        ]);
    }

    public function forcedPasswordResetRedirect(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // If admin forced a password change, use the pendiente flag.
        if (!(bool) ($user->pendiente_cambio_contrasena ?? false)) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->to($this->buildForcedResetUrl($user));
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|max:100|confirmed|regex:/^(?!.*\s)[\x21-\x7E\xA1-\xFF]+$/',
        ]);

        $email = strtolower($data['email']);

        $usuario = Usuario::whereRaw('LOWER(correo_electronico) = ?', [$email])->first();

        if (!$usuario || !$usuario->correo_electronico) {
            return response()->json([
                'message' => 'El token de recuperación no es válido o ha expirado.'
            ], 400);
        }

        $broker = Password::broker();

        /** @var \Illuminate\Auth\Passwords\PasswordBroker $broker */
        $broker = Password::broker();
        if (!$broker->tokenExists($usuario, $data['token'])) {
            return response()->json([
                'message' => 'El token de recuperación no es válido o ha expirado.'
            ], 400);
        }

        $policyError = $this->validateResetPasswordBusinessRules((string) $data['password'], (string) ($usuario->usuario ?? ''));
        if ($policyError !== null) {
            return response()->json([
                'message' => $policyError,
            ], 422);
        }

        $uid = $usuario->getKey();
        $historialQuery = HistorialContrasena::where('id_usuario_fk', $uid)
            ->orderByDesc('fecha_creacion')
            ->orderByDesc('id_hist_pk')
            ->limit(5);

        $hashes = $historialQuery->pluck('contrasena');
        foreach ($hashes as $hash) {
            if (!is_string($hash) || $hash === '') {
                continue;
            }
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



        $usuario->contrasena = $data['password'];
        // Clear both flags: primer_ingreso (if any) and pendiente_cambio_contrasena
        // because the user successfully set a new password via recovery flow.
        $usuario->primer_ingreso = 0;
        $usuario->pendiente_cambio_contrasena = 0;
        $usuario->estado_usuario = 'ACTIVO';
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
        /** @var \Illuminate\Auth\Passwords\PasswordBroker $broker */
        $broker = Password::broker();
        $broker->deleteToken($usuario);


        try {
            cache()->forget('login_attempts:' . $usuario->getKey());
        } catch (\Throwable $e) {
        }

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

    private function buildForcedResetUrl($usuario): string
    {
        $userModel = $usuario instanceof Usuario
            ? $usuario
            : Usuario::find(method_exists($usuario, 'getAuthIdentifier') ? $usuario->getAuthIdentifier() : ($usuario->id_usuario_pk ?? $usuario->id ?? null));

        if (!$userModel || !$userModel->correo_electronico) {
            return route('password.request');
        }

        $email = (string) $userModel->correo_electronico;
        $token = $this->createPasswordResetToken($email);

        return route('password.reset.form', [
            'token' => $token,
            'email' => $email,
            'forced' => 1,
        ]);
    }

    private function createPasswordResetToken(string $email): string
    {
        $table = config('auth.passwords.users.table', 'password_reset_tokens');
        $token = Str::random(64);

        DB::table($table)->updateOrInsert(
            ['email' => strtolower(trim($email))],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        return $token;
    }

    private function validateResetPasswordBusinessRules(string $password, string $username): ?string
    {
        $upperPassword = strtoupper($password);

        if ($username !== '' && $upperPassword === strtoupper($username)) {
            return 'La contraseña no puede ser igual al usuario.';
        }

        if (in_array($upperPassword, ['CONTRASENA', 'CONTRASEÑA', 'PASSWORD'], true)) {
            return 'La contraseña no puede ser una palabra muy común.';
        }

        // Reglas base equivalentes al flujo de creación de cuenta.
        if (!preg_match('/[A-Z]/', $password)) {
            return 'La contraseña debe incluir al menos una letra mayúscula.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            return 'La contraseña debe incluir al menos una letra minúscula.';
        }
        if (!preg_match('/\d/', $password)) {
            return 'La contraseña debe incluir al menos un número.';
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return 'La contraseña debe incluir al menos un símbolo.';
        }

        return null;
    }
}
