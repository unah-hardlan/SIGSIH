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
}
