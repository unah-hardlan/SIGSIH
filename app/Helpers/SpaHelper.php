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

    /**
     * Convierte un número a letras en español
     *
     * @param float $numero
     * @return string
     */
    public static function numeroALetras($numero)
    {
        $numero = round($numero, 2);
        $parteEntera = floor($numero);
        $parteDecimal = round(($numero - $parteEntera) * 100);

        $letras = self::convertirParteEntera($parteEntera);
        
        if ($parteDecimal > 0) {
            $letras .= " con " . $parteDecimal . " centavos";
        }

        return $letras;
    }

    private static function convertirParteEntera($numero)
    {
        if ($numero == 0) {
            return "cero";
        }

        $unidades = ["", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
        $decenas = ["", "", "veinte", "treinta", "cuarenta", "cincuenta", "sesenta", "setenta", "ochenta", "noventa"];
        $centenas = ["", "ciento", "doscientos", "trescientos", "cuatrocientos", "quinientos", "seiscientos", "setecientos", "ochocientos", "novecientos"];

        $resultado = "";

        if ($numero >= 1000000) {
            $millones = floor($numero / 1000000);
            $numero %= 1000000;
            $resultado .= self::convertirParteEntera($millones) . " millón" . ($millones > 1 ? "es" : "") . " ";
        }

        if ($numero >= 1000) {
            $miles = floor($numero / 1000);
            $numero %= 1000;
            if ($miles == 1) {
                $resultado .= "mil ";
            } else {
                $resultado .= self::convertirParteEntera($miles) . " mil ";
            }
        }

        if ($numero >= 100) {
            $centena = floor($numero / 100);
            $numero %= 100;
            $resultado .= $centenas[$centena] . " ";
        }

        if ($numero >= 20) {
            $decena = floor($numero / 10);
            $unidad = $numero % 10;
            $resultado .= $decenas[$decena];
            if ($unidad > 0) {
                $resultado .= " y " . $unidades[$unidad];
            }
            $resultado .= " ";
        } elseif ($numero >= 10) {
            $especiales = ["diez", "once", "doce", "trece", "catorce", "quince", "dieciseis", "diecisiete", "dieciocho", "diecinueve"];
            $resultado .= $especiales[$numero - 10] . " ";
            $numero = 0;
        } elseif ($numero > 0) {
            $resultado .= $unidades[$numero] . " ";
        }

        return trim($resultado);
    }
}
