<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;

class ClienteSidebarComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $navItems = [
            ['route' => 'cliente.perfil', 'icon' => 'fas fa-user', 'label' => 'Perfil'],
            ['route' => 'cliente.solicitudes', 'icon' => 'fas fa-clipboard-question', 'label' => 'Solicitudes'],
            ['route' => 'cliente.cotizaciones', 'icon' => 'fas fa-file-invoice', 'label' => 'Cotizaciones'],
            ['route' => 'cliente.ordenes', 'icon' => 'fas fa-clipboard-list', 'label' => 'Órdenes de Servicio'],
            ['route' => 'cliente.facturas', 'icon' => 'fas fa-file-invoice-dollar', 'label' => 'Facturación'],
        ];
        
        $linkBase = 'flex items-center gap-3 rounded-full transition-colors duration-200 group relative px-4 py-3';
        $activeClasses = 'bg-blue-600 text-white shadow-md font-bold';
        $inactiveClasses = 'text-gray-800 dark:text-gray-200';

        $view->with([
            'navItems' => $navItems,
            'linkBase' => $linkBase,
            'activeClasses' => $activeClasses,
            'inactiveClasses' => $inactiveClasses,
        ]);
    }
}
