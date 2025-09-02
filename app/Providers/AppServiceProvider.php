<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\Parametro;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Cargar el helper SpaHelper
        require_once app_path('Helpers/SpaHelper.php');
    // Helper de fechas (para la directiva Blade)
    require_once app_path('Helpers/DateHelper.php');

        if (!file_exists(public_path('storage'))) {
            try {
                \Illuminate\Support\Facades\Artisan::call('storage:link');
            } catch (\Exception $e) {
            }
        }

    // Compartir nombre del sistema y logo dinámicos en todas las vistas
    View::composer('*', function ($view) {
            $appName = Cache::remember('appName', 300, function () {
                return optional(Parametro::where('parametro', 'app.name')->first())->valor
                    ?? config('app.name', 'SIGSIH');
            });

            $logoUrl = Cache::remember('appLogoUrl', 300, function () {
                $logoParam = optional(Parametro::where('parametro', 'app.logo_path')->first())->valor;
                if ($logoParam) {
                    if (preg_match('#^(https?://|/)#i', $logoParam)) {
                        return $logoParam;
                    }
                    return asset('storage/' . ltrim($logoParam, '/'));
                }
                return asset('images/logo.png');
            });

            $logoHeight = Cache::remember('appLogoHeight', 300, function () {
                $h = optional(Parametro::where('parametro', 'app.logo_height')->first())->valor;
                $h = is_numeric($h) ? (int) $h : 96;
                return max(24, min(256, $h));
            });

            $dateFormat = Cache::remember('appDateFormat', 300, function () {
                return optional(Parametro::where('parametro', 'app.date_format')->first())->valor ?: 'Y-m-d';
            });
            $timezone = Cache::remember('appTimezone', 300, function () {
                return optional(Parametro::where('parametro', 'app.timezone')->first())->valor ?: config('app.timezone', 'UTC');
            });
            try {
                config(['app.timezone' => $timezone]);
                date_default_timezone_set($timezone);
            } catch (\Throwable $e) {}

            $view->with('appName', $appName)
                 ->with('appLogoUrl', $logoUrl)
                 ->with('appLogoHeight', $logoHeight)
                 ->with('appDateFormat', $dateFormat)
                 ->with('appTimezone', $timezone);
        });

        // Directiva Blade para formatear fechas: @fecha($value) o @fecha($value, 'd/m/Y')
        Blade::directive('fecha', function ($expression) {
            return "<?php echo \\App\\Helpers\\DateHelper::format(...([$expression])); ?>";
        });
    }
}
