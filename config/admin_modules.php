<?php

return [
    'module_order' => [
        'seguridad',
        'clientes',
        'proyectos',
        'tickets',
        'calendario',
        'facturacion',
        'reportes',
        'inventario',
        'administracion',
        'mantenimiento',
        'catalogo',
    ],

    'modules' => [
        'seguridad' => [
            'label' => 'Seguridad',
            'objects' => ['Seguridad'],
        ],
        'clientes' => [
            'label' => 'Clientes',
            'objects' => ['Clientes'],
        ],
        'proyectos' => [
            'label' => 'Proyectos',
            'objects' => ['Proyectos'],
        ],
        'tickets' => [
            'label' => 'Tickets',
            'objects' => ['Tickets'],
        ],
        'calendario' => [
            'label' => 'Calendario',
            'objects' => ['Calendario'],
        ],
        'facturacion' => [
            'label' => 'Facturación',
            'objects' => ['Facturación', 'Facturacion'],
        ],
        'reportes' => [
            'label' => 'Reportes',
            'objects' => ['Reportes'],
        ],
        'inventario' => [
            'label' => 'Inventario',
            'objects' => ['Inventario'],
        ],
        'administracion' => [
            'label' => 'Administración',
            'objects' => ['Administración', 'Administracion'],
        ],
        'mantenimiento' => [
            'label' => 'Mantenimiento',
            'objects' => ['Mantenimiento'],
        ],
        'catalogo' => [
            'label' => 'Catalogo',
            'objects' => ['Catalogo', 'Catálogo'],
        ],
        'dashboard' => [
            'label' => 'Dashboard',
            'objects' => ['Dashboard'],
        ],
    ],

    'views' => [
        'dashboard' => [
            'label' => 'Dashboard',
            'module' => 'dashboard',
            'blade' => 'admin.partials.dashboard',
            'type' => 'partial',
            'objects' => [],
        ],


        'gestion-usuarios' => [
            'label' => 'Gestión de usuarios',
            'module' => 'seguridad',
            'blade' => 'admin.partials.gestion-usuarios',
            'type' => 'partial',
            'objects' => ['Gestión de Usuarios', 'Gestion de Usuarios', 'Usuarios'],
        ],
        'parametros' => [
            'label' => 'Parámetros',
            'module' => 'seguridad',
            'blade' => 'admin.partials.parametros',
            'type' => 'partial',
            'objects' => ['Parámetros', 'Parametros'],
        ],
        'configuracion-acceso' => [
            'label' => 'Configuración de accesos',
            'module' => 'seguridad',
            'blade' => 'admin.partials.configuracion-acceso',
            'type' => 'partial',
            'objects' => ['Configuración de accesos', 'Configuracion de accesos', 'Permisos'],
        ],


        'gestion-empresas' => [
            'label' => 'Gestión de empresas',
            'module' => 'clientes',
            'blade' => 'admin.partials.gestion-empresas',
            'type' => 'partial',
            'objects' => ['Empresas', 'Gestión de Empresas', 'Gestion de Empresas'],
        ],
        'cotizaciones' => [
            'label' => 'Gestión de cotizaciones',
            'module' => 'clientes',
            'blade' => 'admin.partials.cotizaciones',
            'type' => 'partial',
            'objects' => ['Cotizaciones', 'Gestión de Cotizaciones', 'Gestion de Cotizaciones'],
        ],
        'solicitudes' => [
            'label' => 'Gestión de solicitudes',
            'module' => 'clientes',
            'blade' => 'admin.partials.solicitudes',
            'type' => 'partial',
            'objects' => ['Solicitudes', 'Gestión de Solicitudes', 'Gestion de Solicitudes'],
        ],
        'gestion-ordenes' => [
            'label' => 'Gestión de órdenes de servicio',
            'module' => 'clientes',
            'blade' => 'admin.partials.gestion-ordenes',
            'type' => 'partial',
            'objects' => ['Órdenes de Servicios', 'Ordenes de Servicios', 'Ordenes de Servicio'],
        ],
        'calificaciones-servicio' => [
            'label' => 'Calificaciones de servicio',
            'module' => 'clientes',
            'blade' => 'admin.partials.calificaciones-servicio',
            'type' => 'partial',
            'objects' => ['Calificaciones de Servicio', 'Calificaciones Servicio', 'Calificación de Servicio'],
        ],


        'proyectos' => [
            'label' => 'Gestión de proyectos',
            'module' => 'proyectos',
            'blade' => 'admin.partials.proyectos',
            'type' => 'partial',
            'objects' => ['Gestión de proyectos', 'Gestion de proyectos', 'Proyectos'],
        ],
        'vista-proyectos' => [
            'label' => 'Vista de proyectos',
            'module' => 'proyectos',
            'blade' => 'admin.partials.vista-proyectos',
            'type' => 'partial',
            'objects' => ['Vista de proyectos', 'Proyectos (Vista)'],
        ],


        'tickets' => [
            'label' => 'Gestión de tickets',
            'module' => 'tickets',
            'blade' => 'admin.partials.tickets',
            'type' => 'partial',
            'objects' => ['Gestión de tickets', 'Gestion de tickets', 'Tickets'],
        ],


        'agencias' => [
            'label' => 'Gestión de agencias',
            'module' => 'calendario',
            'blade' => 'admin.partials.agencias',
            'type' => 'partial',
            'objects' => ['Agencias'],
        ],
        'calendario' => [
            'label' => 'Calendario',
            'module' => 'calendario',
            'blade' => 'admin.partials.calendario',
            'type' => 'partial',
            'objects' => ['Calendario', 'Gestión de Calendario', 'Gestion de Calendario'],
        ],


        'facturas' => [
            'label' => 'Gestión de facturas',
            'module' => 'facturacion',
            'blade' => 'admin.partials.facturas',
            'type' => 'partial',
            'objects' => ['Facturas', 'Gestión de Facturas', 'Gestion de Facturas'],
        ],
        'cai' => [
            'label' => 'Gestión de CAI',
            'module' => 'facturacion',
            'blade' => 'admin.partials.cai',
            'type' => 'partial',
            'objects' => ['CAI'],
        ],


        'reportes' => [
            'label' => 'Gestión de reportes',
            'module' => 'reportes',
            'blade' => 'admin.partials.reportes',
            'type' => 'partial',
            'objects' => ['Reportes', 'Gestión de Reportes', 'Gestion de Reportes'],
        ],


        'productos' => [
            'label' => 'Gestión de productos',
            'module' => 'inventario',
            'blade' => 'admin.partials.productos',
            'type' => 'partial',
            'objects' => ['Productos'],
        ],
        'kardex' => [
            'label' => 'Gestión de kardex',
            'module' => 'inventario',
            'blade' => 'admin.partials.kardex',
            'type' => 'partial',
            'objects' => ['Kardex'],
        ],


        'gestion-personas' => [
            'label' => 'Gestión de personas',
            'module' => 'administracion',
            'blade' => 'admin.partials.gestion-personas',
            'type' => 'partial',
            'objects' => ['Gestión de personas', 'Gestion de personas'],
        ],
        'perfil' => [
            'label' => 'Mi perfil',
            'module' => 'administracion',
            'blade' => 'admin.partials.perfil',
            'type' => 'partial',
            'objects' => ['Mi perfil', 'Perfil', 'Perfil de usuario', 'Mi cuenta', 'Profile'],
        ],
        'bitacora' => [
            'label' => 'Bitácora',
            'module' => 'administracion',
            'blade' => 'admin.partials.bitacora',
            'type' => 'partial',
            'objects' => ['Bitácora', 'Bitacora'],
        ],
        'gestion-db' => [
            'label' => 'Gestión de base de datos',
            'module' => 'administracion',
            'blade' => 'admin.partials.gestion-db',
            'type' => 'partial',
            'objects' => ['Gestión de base de datos', 'Gestion de base de datos'],
        ],


        'mantenimiento-general' => [
            'label' => 'Mantenimiento del sistema',
            'module' => 'mantenimiento',
            'blade' => 'admin.partials.mantenimiento-general',
            'type' => 'partial',
            'objects' => ['Mantenimiento del Sistema', 'Mantenimiento del sistema'],
        ],


        'catalogo-acciones-realizadas' => [
            'label' => 'Acciones realizadas',
            'module' => 'catalogo',
            'blade' => 'admin.partials.catalogo-acciones-realizadas',
            'type' => 'partial',
            'objects' => ['Acciones Realizadas'],
        ],
        'catalogo-admin-facturas' => [
            'label' => 'Administración de facturas',
            'module' => 'catalogo',
            'blade' => 'admin.partials.catalogo-admin-facturas',
            'type' => 'partial',
            'objects' => ['Administración de Facturas', 'Administracion de Facturas'],
        ],
        'catalogo-categorias-ingresos-gastos' => [
            'label' => 'Categorías de ingresos y gastos',
            'module' => 'catalogo',
            'blade' => 'admin.partials.catalogo-categorias-ingresos-gastos',
            'type' => 'partial',
            'objects' => ['Categorías de Ingresos y Gastos', 'Categorias de Ingresos y Gastos'],
        ],
        'catalogo-estados-cai' => [
            'label' => 'Estados CAI',
            'module' => 'catalogo',
            'blade' => 'admin.partials.catalogo-estados-cai',
            'type' => 'partial',
            'objects' => ['Estados CAI'],
        ],
        'catalogo-estados-proyecto' => [
            'label' => 'Estados de proyecto',
            'module' => 'catalogo',
            'blade' => 'admin.partials.catalogo-estados-proyecto',
            'type' => 'partial',
            'objects' => ['Estados de Proyecto'],
        ],
        'catalogo-estados-solicitud' => [
            'label' => 'Estados de solicitud',
            'module' => 'catalogo',
            'blade' => 'admin.partials.catalogo-estados-solicitud',
            'type' => 'partial',
            'objects' => ['Estados de Solicitud'],
        ],
        'catalogo-estados-tickets' => [
            'label' => 'Estados de tickets',
            'module' => 'catalogo',
            'blade' => 'admin.partials.catalogo-estados-tickets',
            'type' => 'partial',
            'objects' => ['Estados de Tickets'],
        ],
        'catalogo-estados-calendario' => [
            'label' => 'Estados del calendario',
            'module' => 'catalogo',
            'blade' => 'admin.partials.catalogo-estados-calendario',
            'type' => 'partial',
            'objects' => ['Estados del Calendario'],
        ],
        'catalogo-genero' => [
            'label' => 'Género',
            'module' => 'catalogo',
            'blade' => 'admin.partials.catalogo-genero',
            'type' => 'partial',
            'objects' => ['Género', 'Genero'],
        ],
        'catalogo-perfil' => [
            'label' => 'Perfiles',
            'module' => 'catalogo',
            'blade' => 'admin.partials.catalogo-perfil',
            'type' => 'partial',
            'objects' => ['Perfiles', 'Perfil'],
        ],
        'catalogo-servicios-factura' => [
            'label' => 'Servicios factura',
            'module' => 'catalogo',
            'blade' => 'admin.partials.catalogo-servicios-factura',
            'type' => 'partial',
            'objects' => ['Servicio Factura', 'Servicios Factura'],
        ],
        'catalogo-servicios-realizados' => [
            'label' => 'Servicios realizados',
            'module' => 'catalogo',
            'blade' => 'admin.partials.catalogo-servicios-realizados',
            'type' => 'partial',
            'objects' => ['Servicios Realizados', 'Servicio Realizado'],
        ],
        'catalogo-tipo-movimiento' => [
            'label' => 'Tipo de movimiento',
            'module' => 'catalogo',
            'blade' => 'admin.partials.catalogo-tipo-movimiento',
            'type' => 'partial',
            'objects' => ['Tipo de Movimiento'],
        ],
        'catalogo-tipo-objeto' => [
            'label' => 'Tipo de objeto',
            'module' => 'catalogo',
            'blade' => 'admin.partials.catalogo-tipo-objeto',
            'type' => 'partial',
            'objects' => ['Tipo de Objeto'],
        ],
        'catalogo-tipo-persona' => [
            'label' => 'Tipo de persona',
            'module' => 'catalogo',
            'blade' => 'admin.partials.catalogo-tipo-persona',
            'type' => 'partial',
            'objects' => ['Tipo de Persona', 'Tipo de Personas'],
        ],
        'catalogo-tipo-producto' => [
            'label' => 'Tipo de producto',
            'module' => 'catalogo',
            'blade' => 'admin.partials.catalogo-tipo-producto',
            'type' => 'partial',
            'objects' => ['Tipo de Producto'],
        ],
        'catalogo-tipo-visita' => [
            'label' => 'Tipo de visita',
            'module' => 'catalogo',
            'blade' => 'admin.partials.catalogo-tipo-visita',
            'type' => 'partial',
            'objects' => ['Tipo de Visita'],
        ],
        'catalogo-origen-kardex' => [
            'label' => 'Origen Kardex',
            'module' => 'catalogo',
            'blade' => 'admin.partials.catalogo-origen-kardex',
            'type' => 'partial',
            'objects' => ['Origen Kardex', 'Origenes', 'Origen'],
        ],
        'catalogo-tipo-mantenimiento' => [
            'label' => 'Tipo de Mantenimiento',
            'module' => 'catalogo',
            'blade' => 'admin.partials.catalogo-tipo-mantenimiento',
            'type' => 'partial',
            'objects' => ['Tipo de Mantenimiento'],
        ],
        'catalogo-ubicaciones' => [
            'label' => 'Ubicaciones',
            'module' => 'catalogo',
            'blade' => 'admin.partials.catalogo-ubicaciones',
            'type' => 'partial',
            'objects' => ['Ubicaciones'],
        ],


        'reporte-usuarios' => [
            'label' => 'Reporte de usuarios',
            'module' => 'reportes',
            'blade' => 'admin.reporte-usuarios',
            'type' => 'full',
            'objects' => ['Reportes'],
        ],
        'reporte-agencias' => [
            'label' => 'Reporte de agencias',
            'module' => 'reportes',
            'blade' => 'admin.reporte-agencias',
            'type' => 'full',
            'objects' => ['Reportes'],
        ],
        'reporte-calendario' => [
            'label' => 'Reporte de calendario',
            'module' => 'reportes',
            'blade' => 'admin.reporte-calendario',
            'type' => 'full',
            'objects' => ['Reportes'],
        ],
        'reporte-facturas' => [
            'label' => 'Reporte de facturas',
            'module' => 'reportes',
            'blade' => 'admin.reporte-facturas',
            'type' => 'full',
            'objects' => ['Reportes'],
        ],
        'reporte-parametros' => [
            'label' => 'Reporte de parámetros',
            'module' => 'reportes',
            'blade' => 'admin.reporte-parametros',
            'type' => 'full',
            'objects' => ['Reportes'],
        ],
        'reporte-configuracion-accesos' => [
            'label' => 'Reporte de configuración de accesos',
            'module' => 'reportes',
            'blade' => 'admin.reporte-configuracion-accesos',
            'type' => 'full',
            'objects' => ['Reportes'],
        ],
        'reporte-empresas' => [
            'label' => 'Reporte de empresas',
            'module' => 'reportes',
            'blade' => 'admin.reporte-empresas',
            'type' => 'full',
            'objects' => ['Reportes'],
        ],
        'reporte-solicitudes' => [
            'label' => 'Reporte de solicitudes',
            'module' => 'reportes',
            'blade' => 'admin.reporte-solicitudes',
            'type' => 'full',
            'objects' => ['Reportes'],
        ],
        'reporte-tickets' => [
            'label' => 'Reporte de tickets',
            'module' => 'reportes',
            'blade' => 'admin.reporte-tickets',
            'type' => 'full',
            'objects' => ['Reportes'],
        ],
        'reporte-cai' => [
            'label' => 'Reporte de CAI',
            'module' => 'reportes',
            'blade' => 'admin.reporte-cai',
            'type' => 'full',
            'objects' => ['Reportes'],
        ],
        'reporte-bitacora' => [
            'label' => 'Reporte de bitácora',
            'module' => 'reportes',
            'blade' => 'admin.reporte-bitacora',
            'type' => 'full',
            'objects' => ['Reportes'],
        ],
        'reporte-gestion-personas' => [
            'label' => 'Reporte de gestión de personas',
            'module' => 'reportes',
            'blade' => 'admin.reporte-gestion-personas',
            'type' => 'full',
            'objects' => ['Reportes'],
        ],

        'reportes-header' => [
            'label' => 'Encabezado de reportes',
            'module' => 'reportes',
            'blade' => 'admin.reportes-header',
            'type' => 'full',
            'objects' => ['Reportes'],
        ],
    ],
];
