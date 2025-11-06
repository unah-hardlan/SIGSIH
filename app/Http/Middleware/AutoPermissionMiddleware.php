<?php

namespace App\Http\Middleware;

use App\Models\Usuario;
use App\Services\PermissionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AutoPermissionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (strtoupper($request->method()) === 'OPTIONS') {
            return $next($request);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        // Allow some endpoints without permisos (self, 2FA flows, and dashboard datasets)
        if (
            $request->is('api/me') ||
            $request->is('api/login') ||
            $request->is('api/logout') ||
            $request->is('api/register') ||
            $request->is('api/dashboard/*') ||
            $request->is('api/2fa/*') ||
            // Notificaciones in-app: disponibles para cualquier usuario autenticado
            $request->is('api/notifications') ||
            $request->is('api/notifications/*')
        ) {
            return $next($request);
        }

        // Admin bypass by rol name
        try {
            if (($user instanceof Usuario) && $user->rol && mb_strtolower($user->rol->rol) === 'administrador') {
                return $next($request);
            }
        } catch (\Throwable $e) {
        }

        $method = strtoupper($request->method());

        // Allow listing roles/objetos/tipos-objeto if user can view "Permisos" (to cargar matriz de permisos)
        if (in_array($method, ['GET', 'HEAD'], true)) {
            $path = trim($request->path(), '/');
            // Catálogos de estados usados como filtros: permitir si el usuario tiene permiso de leer el módulo principal
            $datasetStates = [
                'api/estados-solicitud' => ['Solicitudes', 'Gestión de Solicitudes', 'Gestion de Solicitudes'],
                'api/estados-proyecto' => ['Proyectos', 'Gestión de proyectos', 'Gestion de proyectos'],
                'api/estados-ticket' => ['Tickets', 'Gestión de tickets', 'Gestion de tickets'],
                'api/estados-calendario' => ['Calendario', 'Gestión de Calendario', 'Gestion de Calendario'],
                'api/estados-orden-servicio' => ['Órdenes de Servicios', 'Ordenes de Servicios', 'Ordenes de Servicio'],
            ];
            if (isset($datasetStates[$path])) {
                $perm = app(PermissionService::class);
                if ($perm->can($user, $datasetStates[$path], 'consultar')) {
                    return $next($request);
                }
                // Si no tiene permiso sobre el módulo principal, continuar al chequeo normal
            }
            // Datasets financieros usados por Proyectos: permitir lectura si el usuario puede leer Proyectos
            if (preg_match('#^api/(ingresos|gastos)(/.*)?$#i', $path)) {
                $perm = app(PermissionService::class);
                if ($perm->can($user, ['Proyectos', 'Gestión de proyectos', 'Gestion de proyectos'], 'consultar')) {
                    return $next($request);
                }
            }
            if (preg_match('#^api/(roles|objetos|tipos-objeto)(/.*)?$#i', $path)) {
                $perm = app(PermissionService::class);
                if ($perm->can($user, ['Permisos', 'Configuración de accesos', 'Configuracion de accesos'], 'consultar')) {
                    return $next($request);
                }
                // fall-through to standard check sobre el mismo recurso
                // Permitir listar roles si el usuario puede consultar Usuarios (para asignación de roles)
                if (preg_match('#^api/roles#i', $path)) {
                    $perm = app(PermissionService::class);
                    if ($perm->can($user, ['Usuarios'], 'consultar')) {
                        return $next($request);
                    }
                }
            }
            if (preg_match('#^api/usuarios(?:/|$)#i', $path)) {
                $perm = app(PermissionService::class);
                if ($perm->can($user, ['Configuración de accesos', 'Configuracion de accesos'], 'consultar')) {
                    return $next($request);
                }
            }
        }

        if ($method === 'PUT') {
            $path = trim($request->path(), '/');
            if (preg_match('#^api/usuarios/\d+/(rol|roles)$#i', $path)) {
                $perm = app(PermissionService::class);
                if ($perm->can($user, ['Configuración de accesos', 'Configuracion de accesos'], 'actualizacion')) {
                    return $next($request);
                }
            }
        }

        // Infer candidates for objeto name from controller and path
        $route = $request->route();
        $controller = $route ? ($route->getActionName() ?? '') : '';
        $controllerBase = class_basename(is_string($controller) ? explode('@', (string) $controller)[0] : (string) $controller);
        $controllerBase = str_ends_with($controllerBase, 'Controller') ? substr($controllerBase, 0, -10) : $controllerBase;
        $synonyms = [
            'Auth' => ['Login'],
            'Usuario' => ['Usuarios', 'Usuario'],
            'Rol' => ['Roles', 'Rol'],
            'Permiso' => ['Permisos', 'Permiso', 'Configuración de accesos', 'Configuracion de accesos'],
            'Parametro' => ['Parámetros', 'Parametros', 'Parámetro', 'Parametro'],
            'Objeto' => ['Objetos', 'Objeto'],
            'Bitacora' => ['Bitácora', 'Bitacora'],
            'Profile' => ['Profile', 'Perfil'],
            // 'Perfil' removido del catálogo de objetos administrables
            // 'TipoPersona' removido
            'Genero' => ['Género', 'Genero', 'Géneros', 'Generos'],
            'Persona' => ['Persona', 'Personas', 'Gestión de personas', 'Gestion de personas'],
            'Solicitud' => ['Solicitud', 'Solicitudes', 'Gestión de solicitudes', 'Gestion de solicitudes'],
            'Proyecto' => ['Proyectos', 'Gestión de proyectos', 'Gestion de proyectos'],
            'Ticket' => ['Tickets', 'Gestión de tickets', 'Gestion de tickets'],
            'Dashboard' => ['Dashboard'],
            // Nuevos
            'MantenimientoGeneral' => ['Mantenimiento del sistema', 'Mantenimiento'],
            'GestionPersonas' => ['Gestión de personas', 'Gestion de personas'],
            'GestionDb' => ['Gestión de base de datos', 'Gestion de base de datos'],
            'Origen' => ['Origen Kardex', 'Origenes', 'Origen'],
            // Catálogo: Estados
            'EstadoTicket' => ['Estados de Tickets'],
            'EstadoCai' => ['Estados CAI'],
            'EstadoProyecto' => ['Estados de Proyecto'],
            'EstadoSolicitud' => ['Estados de Solicitud'],
            'EstadoCalendario' => ['Estados del Calendario'],
            'EstadoFactura' => ['Estados de Factura', 'Administración de Facturas', 'Administracion de Facturas', 'Facturas', 'Gestión de Facturas', 'Gestion de Facturas'],
            // Catálogo: Tipos
            'TipoMovimiento' => ['Tipo de Movimiento'],
            'TipoObjeto' => ['Tipo de Objeto'],
            'TipoProducto' => ['Tipo de Producto'],
            'TipoVisita' => ['Tipo de Visita'],
            'TipoMantenimiento' => ['Tipo de Mantenimiento'],
            // Catálogo: Servicios / Acciones / Perfiles / Categorías
            'Servicio' => ['Servicio Factura', 'Servicios Factura'],
            'ServicioRealizado' => ['Servicios Realizados'],
            'AccionRealizada' => ['Acciones Realizadas'],
            'Perfil' => ['Perfiles', 'Perfil'],
            'Categoria' => ['Categorías de Ingresos y Gastos', 'Categorias de Ingresos y Gastos'],
            // Reportes (visitas)
            'ReporteVisita' => ['Reportes', 'Gestión de reportes', 'Gestion de reportes'],
            // Facturación
            'DetalleFactura' => ['Facturas', 'Gestión de Facturas', 'Gestion de Facturas'],
            // Clientes: Empresas, Solicitudes, Cotizaciones, Órdenes de Servicio y auxiliares
            'EmpresasCliente' => ['Empresas', 'Gestión de Empresas', 'Gestion de Empresas'],
            'Cotizacion' => ['Cotizaciones', 'Gestión de Cotizaciones', 'Gestion de Cotizaciones'],
            'OrdenServicio' => ['Órdenes de Servicios', 'Ordenes de Servicios', 'Ordenes de Servicio'],
            'Contacto' => ['Contactos', 'Solicitudes'],
            'NombresEmpresa' => ['Empresas'],
            'OficinasEmpresa' => ['Empresas'],
            'Direcciones' => ['Empresas'],
            'Paises' => ['Empresas'],
            'Departamentos' => ['Empresas'],
            'Ciudades' => ['Empresas'],
        ];
        $candidates = $synonyms[$controllerBase] ?? [];
        $first = explode('/', trim($request->path(), '/'))[1] ?? '';
        if ($first) {
            // Convertir kebab/underscore a título sin guiones para mejorar el match con objetos
            $firstTitle = Str::of($first)->replace(['-', '_'], ' ')->title();
            $candidates[] = (string) $firstTitle;
        }

        $accion = match ($method) {
            'POST' => 'insercion',
            'PUT', 'PATCH' => 'actualizacion',
            'DELETE' => 'eliminacion',
            default => 'consultar',
        };

        // Proyectos controla Movimientos (Ingresos/Gastos):
        // si el usuario tiene el permiso equivalente en Proyectos,
        // permitir la acción solicitada sobre ingresos/gastos.
        $pathForAction = trim($request->path(), '/');
        if (preg_match('#^api/(ingresos|gastos)(/.*)?$#i', $pathForAction)) {
            $perm = app(PermissionService::class);
            if ($perm->can($user, ['Proyectos', 'Gestión de proyectos', 'Gestion de proyectos'], $accion)) {
                return $next($request);
            }
        }

        $perm = app(PermissionService::class);
        if (!$perm->can($user, $candidates, $accion)) {
            return response()->json(['error' => 'Permiso denegado', 'objeto' => $candidates[0] ?? 'desconocido', 'accion' => $accion], 403);
        }

        return $next($request);
    }
}
