<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\Parametro;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use App\Models\Persona;

class AppServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        
    }

    
    public function boot(): void
    {
        
        require_once app_path('Helpers/SpaHelper.php');
        
        require_once app_path('Helpers/DateHelper.php');

        if (!file_exists(public_path('storage'))) {
            try {
                \Illuminate\Support\Facades\Artisan::call('storage:link');
            } catch (\Exception $e) {
            }
        }

        
        View::composer('*', function ($view) {
            $appName = Cache::remember('appName', 300, function () {
                $v = optional(Parametro::where('parametro', 'APP.NOMBRE')->first())->valor;
                if (!$v) {
                    $v = optional(Parametro::where('parametro', 'app.name')->first())->valor;
                }
                return $v ?? config('app.name', 'SIGSIH');
            });

            $logoUrl = Cache::remember('appLogoUrl', 300, function () {
                $logoParam = optional(Parametro::where('parametro', 'APP.LOGO_RUTA')->first())->valor
                    ?? optional(Parametro::where('parametro', 'app.logo_path')->first())->valor;
                if ($logoParam) {
                    if (preg_match('#^(https?://|/)#i', $logoParam)) {
                        return $logoParam;
                    }
                    return asset('storage/' . ltrim($logoParam, '/'));
                }
                return asset('images/logo.png');
            });

            $logoHeight = Cache::remember('appLogoHeight', 300, function () {
                $h = optional(Parametro::where('parametro', 'APP.LOGO_ALTO')->first())->valor
                    ?? optional(Parametro::where('parametro', 'app.logo_height')->first())->valor;
                $h = is_numeric($h) ? (int) $h : 96;
                return max(24, min(256, $h));
            });

            $dateFormat = Cache::remember('appDateFormat', 300, function () {
                $fmt = optional(Parametro::where('parametro', 'APP.FORMATO_FECHA')->first())->valor
                    ?? optional(Parametro::where('parametro', 'app.date_format')->first())->valor;
                return $fmt ?: 'Y-m-d';
            });
            $timezone = Cache::remember('appTimezone', 300, function () {
                $tz = optional(Parametro::where('parametro', 'APP.ZONA_HORARIA')->first())->valor
                    ?? optional(Parametro::where('parametro', 'app.timezone')->first())->valor;
                return $tz ?: config('app.timezone', 'UTC');
            });
            try {
                config(['app.timezone' => $timezone]);
                date_default_timezone_set($timezone);
            } catch (\Throwable $e) {
            }

            $view->with('appName', $appName)
                ->with('appLogoUrl', $logoUrl)
                ->with('appLogoHeight', $logoHeight)
                ->with('appDateFormat', $dateFormat)
                ->with('appTimezone', $timezone);
        });

        
        Blade::directive('fecha', function ($expression) {
            return "<?php echo \\App\\Helpers\\DateHelper::format(...([$expression])); ?>";
        });

        
        
        Blade::if('perm', function ($objects, string $action) {
            try {
                $user = auth()->user();
                $candidates = is_array($objects) ? $objects : [$objects];
                return app(\App\Services\PermissionService::class)->can($user, $candidates, $action);
            } catch (\Throwable $e) {
                return false;
            }
        });

        
        View::composer('cliente.partials.header', \App\Http\View\Composers\ClienteHeaderComposer::class);
        View::composer('cliente.partials.sidebar', \App\Http\View\Composers\ClienteSidebarComposer::class);

        
        View::composer('*', function ($view) {
            try {
                $user = Auth::user();
                if ($user) {
                    $uid = method_exists($user, 'getAuthIdentifier') ? $user->getAuthIdentifier() : ($user->id_usuario_pk ?? $user->id ?? null);
                    $persona = $uid ? Persona::where('id_usuario_fk', $uid)->first() : null;
                    $authUser = [
                        'usuario' => $user->usuario ?? null,
                        'nombre_usuario' => $user->nombre_usuario ?? null,
                        'correo_electronico' => $user->correo_electronico ?? null,
                    ];
                    $view->with('authUser', $authUser)
                        ->with('authPersona', $persona)
                        ->with('authFirstTime', (bool) ($user->primer_ingreso ?? false) && !$persona);
                } else {
                    $view->with('authUser', null)
                        ->with('authPersona', null)
                        ->with('authFirstTime', false);
                }
            } catch (\Throwable $e) {
                $view->with('authUser', null)
                    ->with('authPersona', null)
                    ->with('authFirstTime', false);
            }
        });
    }
}
