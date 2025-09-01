<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

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
            return response('View not allowed', 403);
        }

        // Primero verificar si existe una vista parcial específica
        $partialView = "admin.partials.{$view}";
        if (View::exists($partialView)) {
            // Incluir header para vistas parciales
            $headerHtml = view('partials.admin-header')->render();
            $contentHtml = view($partialView)->render();

            return $headerHtml . '<div class="p-6 rounded-lg shadow">' . $contentHtml . '</div>';
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
}
