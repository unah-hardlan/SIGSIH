<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{

    public const HOME = '/home';


    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {

            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('auth-login', function (Request $request) {
            $identifier = strtoupper((string) $request->input('usuario', ''));
            $key = 'login:' . sha1($request->ip() . '|' . $identifier);

            return Limit::perMinute(5)
                ->by($key)
                ->response(function (Request $request, array $headers) {
                    $retryAfter = (int) ($headers['Retry-After'] ?? 60);
                    return response()->json([
                        'message' => 'Has excedido el máximo de intentos de inicio de sesión. Intenta de nuevo en unos segundos.',
                        'retry_after_seconds' => $retryAfter,
                    ], 429, $headers);
                });
        });

        RateLimiter::for('auth-register', function (Request $request) {
            $identifier = strtolower((string) ($request->input('correo_electronico') ?? $request->input('email') ?? ''));
            $key = 'register:' . sha1($request->ip() . '|' . $identifier);

            return Limit::perHour(3)
                ->by($key)
                ->response(function (Request $request, array $headers) {
                    $retryAfter = (int) ($headers['Retry-After'] ?? 3600);
                    return response()->json([
                        'message' => 'Has alcanzado el límite de registros permitidos por esta hora.',
                        'retry_after_seconds' => $retryAfter,
                    ], 429, $headers);
                });
        });

        RateLimiter::for('auth-password-recovery', function (Request $request) {
            $identifier = strtolower((string) ($request->input('identifier') ?? $request->input('email') ?? ''));
            $key = 'password-recovery:' . sha1($request->ip() . '|' . $identifier);

            return Limit::perDay(3)
                ->by($key)
                ->response(function (Request $request, array $headers) {
                    $retryAfter = (int) ($headers['Retry-After'] ?? 86400);
                    return response()->json([
                        'message' => 'Has superado el límite diario de recuperación de contraseña.',
                        'retry_after_seconds' => $retryAfter,
                    ], 429, $headers);
                });
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
