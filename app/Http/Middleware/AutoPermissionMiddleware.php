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


        if (
            $request->is('api/me') ||
            $request->is('api/login') ||
            $request->is('api/logout') ||
            $request->is('api/register') ||
            $request->is('api/dashboard/*') ||
            $request->is('api/2fa/*') ||

            $request->is('api/notifications') ||
            $request->is('api/notifications/*')
        ) {
            return $next($request);
        }


        try {
            if (($user instanceof Usuario) && $user->rol && mb_strtolower($user->rol->rol) === 'administrador') {
                return $next($request);
            }
        } catch (\Throwable $e) {
        }

        $method = strtoupper($request->method());


        if (in_array($method, ['GET', 'HEAD'], true)) {
            $path = trim($request->path(), '/');

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
            }

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


        $route = $request->route();
        $controller = $route ? ($route->getActionName() ?? '') : '';
        $controllerBase = class_basename(is_string($controller) ? explode('@', (string) $controller)[0] : (string) $controller);
        $controllerBase = str_ends_with($controllerBase, 'Controller') ? substr($controllerBase, 0, -10) : $controllerBase;
        $synonyms = [
            'Auth' => ['Login'],
            'Usuario' => ['Usuarios', 'Usuario'],
            'Rol' => ['Roles', 'Rol', 'Permisos', 'Configuración de accesos', 'Configuracion de accesos'],
            'Permiso' => ['Permisos', 'Permiso', 'Configuración de accesos', 'Configuracion de accesos'],
            'Parametro' => ['Parámetros', 'Parametros', 'Parámetro', 'Parametro'],
            'Objeto' => ['Objetos', 'Objeto'],
            'Bitacora' => ['Bitácora', 'Bitacora'],
            'Profile' => ['Profile', 'Perfil'],


            'Genero' => ['Género', 'Genero', 'Géneros', 'Generos'],
            'Persona' => ['Persona', 'Personas', 'Gestión de personas', 'Gestion de personas'],
            'Solicitud' => ['Solicitud', 'Solicitudes', 'Gestión de solicitudes', 'Gestion de solicitudes'],
            'Proyecto' => ['Proyectos', 'Gestión de proyectos', 'Gestion de proyectos'],
            'Ticket' => ['Tickets', 'Gestión de tickets', 'Gestion de tickets'],
            'Dashboard' => ['Dashboard'],

            'MantenimientoGeneral' => ['Mantenimiento del sistema', 'Mantenimiento'],
            'GestionPersonas' => ['Gestión de personas', 'Gestion de personas'],
            'GestionDb' => ['Gestión de base de datos', 'Gestion de base de datos'],
            'Origen' => ['Origen Kardex', 'Origenes', 'Origen'],

            'EstadoTicket' => ['Estados de Tickets'],
            'EstadoCai' => ['Estados CAI'],
            'EstadoProyecto' => ['Estados de Proyecto'],
            'EstadoSolicitud' => ['Estados de Solicitud'],
            'EstadoCalendario' => ['Estados del Calendario'],
            'EstadoFactura' => ['Estados de Factura', 'Administración de Facturas', 'Administracion de Facturas', 'Facturas', 'Gestión de Facturas', 'Gestion de Facturas'],

            'TipoMovimiento' => ['Tipo de Movimiento'],
            'TipoObjeto' => ['Tipo de Objeto'],
            'TipoProducto' => ['Tipo de Producto'],
            'TipoVisita' => ['Tipo de Visita'],
            'TipoMantenimiento' => ['Tipo de Mantenimiento'],

            'Servicio' => ['Servicio Factura', 'Servicios Factura'],
            'ServicioRealizado' => ['Servicios Realizados'],
            'AccionRealizada' => ['Acciones Realizadas'],
            'Perfil' => ['Perfiles', 'Perfil'],
            'Categoria' => ['Categorías de Ingresos y Gastos', 'Categorias de Ingresos y Gastos'],

            'ReporteVisita' => ['Reportes', 'Gestión de reportes', 'Gestion de reportes'],

            'DetalleFactura' => ['Facturas', 'Gestión de Facturas', 'Gestion de Facturas'],

            'EmpresasCliente' => ['Empresas', 'Gestión de Empresas', 'Gestion de Empresas'],
            'Cotizacion' => ['Cotizaciones', 'Gestión de Cotizaciones', 'Gestion de Cotizaciones'],
            'ItemCotizacion' => ['Cotizaciones', 'Gestión de Cotizaciones', 'Gestion de Cotizaciones'],
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

            $firstTitle = Str::of($first)->replace(['-', '_'], ' ')->title();
            $candidates[] = (string) $firstTitle;
        }

        $accion = match ($method) {
            'POST' => 'insercion',
            'PUT', 'PATCH' => 'actualizacion',
            'DELETE' => 'eliminacion',
            default => 'consultar',
        };




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
