<?php

namespace App\Helpers;

class SpaHelper
{
    /**
     * Genera una URL para navegación SPA
     */
    public static function route($viewName, $params = [])
    {
        return route('admin.' . $viewName, $params);
    }
    
    /**
     * Verifica si una vista está activa
     */
    public static function isActive($viewName)
    {
        $currentPath = request()->path();
        $expectedPath = 'admin/' . $viewName;
        return $currentPath === $expectedPath;
    }
    
    /**
     * Obtiene el nombre de la vista actual
     */
    public static function getCurrentView()
    {
        $path = request()->path();
        $match = preg_match('/admin\/(.+)$/', $path, $matches);
        return $match ? $matches[1] : 'dashboard';
    }
    
    /**
     * Lista de todas las vistas válidas
     */
    public static function getValidViews()
    {
        return [
            'dashboard', 'gestion-usuarios', 'parametros', 'configuracion-acceso',
            'gestion-empresas', 'cotizaciones', 'solicitudes', 'gestion-ordenes',
            'vista-proyectos', 'proyectos', 'tickets', 'agencias', 'calendario',
            'facturas', 'cai', 'reportes', 'productos', 'kardex', 'gestion-personas',
            'perfil', 'bitacora', 'gestion-db', 'mantenimiento-general'
        ];
    }

    /**
     * Determina si la petición es una solicitud SPA.
     *
     * @param \Illuminate\Http\Request|null $request
     * @return bool
     */
    public static function isSpaRequest($request = null): bool
    {
        $request = $request ?? request();
        return $request->ajax() 
            || $request->wantsJson() 
            || $request->header('X-SPA-Request') === 'true'
            || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * Renderiza una vista compatible con SPA para el cliente.
     * Si es una petición SPA, solo devuelve el contenido.
     * Si no, devuelve la vista completa con el layout.
     *
     * @param string $view
     * @param array $data
     * @param \Illuminate\Http\Request|null $request
     * @return \Illuminate\View\View|string
     */
    public static function clienteView(string $view, array $data = [], $request = null)
    {
        $request = $request ?? request();
        
        if (self::isSpaRequest($request)) {
            // Para peticiones SPA, renderizar solo el contenido principal
            return view($view, $data)->render();
        }
        
        // Para peticiones normales, devolver la vista completa
        return view($view, $data);
    }

    /**
     * Invalida el caché del lado del cliente para una ruta específica.
     * Útil después de operaciones que modifican datos.
     *
     * @param string|array $routes
     * @return void
     */
    public static function invalidateClientCache($routes)
    {
        $routes = is_array($routes) ? $routes : [$routes];
        
        foreach ($routes as $route) {
            header("X-SPA-Invalidate-Cache: {$route}", false);
        }
    }
}
