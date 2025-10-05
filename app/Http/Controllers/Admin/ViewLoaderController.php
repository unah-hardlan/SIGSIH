<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Services\PermissionService;

class ViewLoaderController extends Controller
{
    public function load(Request $request)
    {
        $view = $request->get('view');

        if (!$view || !preg_match('/^[a-zA-Z0-9_-]+$/', $view)) {
            return response('Invalid view', 400);
        }

        $validViews = [
            'dashboard',
            'gestion-usuarios',
            'parametros',
            'configuracion-acceso',
            'gestion-empresas',
            'cotizaciones',
            'solicitudes',
            'gestion-ordenes',
            'calificaciones-servicio',
            'vista-proyectos',
            'proyectos',
            'tickets',
            'agencias',
            'calendario',
            'facturas',
            'cai',
            'reportes',
            'reportes-header',
            'reporte-usuarios',
            'reporte-agencias',
            'reporte-calendario',
            'reporte-facturas',
            'reporte-parametros',
            'reporte-configuracion-accesos',
            'reporte-empresas',
            'reporte-solicitudes',
            'reporte-tickets',
            'productos',
            'kardex',
            'catalogo-genero',
            'catalogo-estados-solicitud',
            'catalogo-categorias-ingresos-gastos',
            'catalogo-estados-proyecto',
            'catalogo-estados-tickets',
            'catalogo-ubicaciones',
            'catalogo-estados-calendario',
            'catalogo-admin-facturas',
            'catalogo-estados-cai',
            'catalogo-tipo-visita',
            'catalogo-tipo-persona',
            'catalogo-perfil',
            'catalogo-tipo-producto',
            'catalogo-tipo-movimiento',
            'catalogo-servicios-realizados',
            'catalogo-acciones-realizadas',
            'catalogo-servicios-factura',
            'catalogo-tipo-objeto',
            'gestion-personas',
            'perfil',
            'bitacora',
            'gestion-db',
            'mantenimiento-general',
            'reporte-cai',
            'reporte-bitacora',
            'reporte-gestion-personas',
        ];

        if (!in_array($view, $validViews)) {
            return $this->denyAccessResponse($view, __('La vista solicitada no está disponible.'));
        }

        // Enforce permisos for specific admin views (consultar)
        $user = Auth::user();
        if ($user) {
            // Admin bypass
            try {
                if (mb_strtolower($user->rol?->rol ?? '') !== 'administrador') {
                    $viewObjetoMap = [
                        'parametros' => ['Parámetros','Parametros'],
                        'configuracion-acceso' => ['Permisos','Configuración de accesos','Configuracion de accesos'],
                        'gestion-usuarios' => ['Usuarios'],
                        'bitacora' => ['Bitácora','Bitacora'],
                        // Nuevos módulos
                        'gestion-personas' => ['Gestión de personas','Gestion de personas'],
                        'mantenimiento-general' => ['Mantenimiento del sistema'],
                        'gestion-db' => ['Gestión de base de datos','Gestion de base de datos'],
                    ];
                    $viewLabels = [
                        'parametros' => 'Parámetros',
                        'configuracion-acceso' => 'Configuración de accesos',
                        'gestion-usuarios' => 'Gestión de usuarios',
                        'bitacora' => 'Bitácora',
                        'gestion-personas' => 'Gestión de personas',
                        'mantenimiento-general' => 'Mantenimiento del sistema',
                        'gestion-db' => 'Gestión de base de datos',
                    ];
                    if (isset($viewObjetoMap[$view])) {
                        $perm = app(PermissionService::class);
                        if (!$perm->can($user, $viewObjetoMap[$view], 'consultar')) {
                            return $this->denyAccessResponse($view, null, $viewLabels[$view] ?? null);
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Si falla relación u otro error, negar por seguridad
                return $this->denyAccessResponse($view);
            }
        }

        // Primero verificar si existe una vista parcial específica
        $partialView = "admin.partials.{$view}";
        if (View::exists($partialView)) {
            return $this->renderPartial($partialView);
        }

        // Si no existe vista parcial, intentar cargar la vista completa y extraer contenido
        $fullView = "admin.{$view}";
        if (!View::exists($fullView)) {
            return response('View not found', 404);
        }

        try {
            $fullHtml = view($fullView)->render();

            // Extraer solo el contenido principal usando regex
            if (preg_match('/<div class="bg-white p-6 rounded-lg shadow">(.*?)<\/div>\s*<\/main>/s', $fullHtml, $matches)) {
                return $matches[1];
            }

            // Fallback: buscar cualquier div con clase bg-white
            if (preg_match('/<div[^>]*class="[^"]*bg-white[^"]*"[^>]*>(.*?)<\/div>/s', $fullHtml, $matches)) {
                return $matches[1];
            }

            return $fullHtml;
        } catch (\Exception $e) {
            return response('Error loading view: ' . $e->getMessage(), 500);
        }
    }

    private function renderPartial(string $view, array $data = []): string
    {
        $headerHtml = view('partials.admin-header')->render();
        $contentHtml = view($view, $data)->render();

        return $headerHtml . '<div class="p-6 rounded-lg shadow bg-white dark:bg-gray-900">' . $contentHtml . '</div>';
    }

    private function denyAccessResponse(string $view, ?string $customMessage = null, ?string $label = null)
    {
        $message = $customMessage ?? __('No cuentas con los permisos necesarios para acceder a esta sección.');
        $targetLabel = $label ?? $this->resolveViewLabel($view);

        $content = $this->renderPartial('admin.partials.access-denied', [
            'code' => 403,
            'title' => __('Acceso restringido'),
            'message' => $message,
            'targetLabel' => $targetLabel,
            'actionUrl' => route('admin.dashboard'),
            'actionText' => __('Ir al panel principal'),
            'helpText' => __('Comunícate con un administrador si consideras que deberías tener acceso.'),
        ]);

        return response($content, 403);
    }

    private function resolveViewLabel(string $view): string
    {
        $customLabels = [
            'gestion-usuarios' => 'Gestión de usuarios',
            'configuracion-acceso' => 'Configuración de accesos',
            'gestion-empresas' => 'Gestión de empresas',
            'gestion-ordenes' => 'Gestión de órdenes',
            'vista-proyectos' => 'Vista de proyectos',
            'gestion-personas' => 'Gestión de personas',
            'mantenimiento-general' => 'Mantenimiento general',
            'gestion-db' => 'Gestión de base de datos',
        ];

        if (isset($customLabels[$view])) {
            return $customLabels[$view];
        }

        return \Illuminate\Support\Str::of($view)
            ->replace(['-', '_'], ' ')
            ->trim()
            ->title();
    }
}
