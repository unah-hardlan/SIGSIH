<aside x-data="{}" x-init="$store.clienteLogout = $store.clienteLogout || { modalOpen: false }" x-show="sidebarOpen" :class="{
        // COMPORTAMIENTO MÓVIL (Fixed, cubre altura)
        'fixed inset-y-0 left-0 w-72 min-w-[18rem] h-full': isMobile,
        
        // COMPORTAMIENTO DESKTOP (Contenido, con márgenes, altura limitada)
        'w-72 min-w-[18rem] my-4 max-h-[90vh] ml-4': !isMobile && sidebarOpen,
        'w-20 min-w-[5rem] my-4 max-h-[90vh] ml-4': !isMobile && !sidebarOpen,

        'brightness-50 pointer-events-none': $store.clienteLogout?.modalOpen,
        'brightness-100 pointer-events-auto': !$store.clienteLogout?.modalOpen,
    }"
    class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white flex flex-col flex-shrink-0 p-4 shadow-lg border border-[#b6b6b6] dark:border-gray-800 rounded-xl transition-all duration-300 ease-in-out overflow-y-auto"
    style="scrollbar-width: thin; scrollbar-color: #D1D5DB #FFFFFF; -webkit-overflow-scrolling: touch; z-index: 9999;">

    @php
        $navItems = [
            ['route' => 'cliente.perfil', 'icon' => 'fas fa-user', 'label' => 'Perfil'],
            ['route' => 'cliente.solicitudes', 'icon' => 'fas fa-clipboard-question', 'label' => 'Solicitudes'],
            ['route' => 'cliente.cotizaciones', 'icon' => 'fas fa-file-invoice', 'label' => 'Cotizaciones'],
            ['route' => 'cliente.ordenes', 'icon' => 'fas fa-clipboard-list', 'label' => 'Órdenes de Servicio'],
            ['route' => 'cliente.facturas', 'icon' => 'fas fa-file-invoice-dollar', 'label' => 'Facturación'],
        ];
        // Enlaces con forma de píldora
        $linkBase = 'flex items-center gap-3 rounded-full transition-colors duration-200 group relative';
        
        $activeClasses = 'text-white bg-blue-600 shadow-md font-bold';
        
        $inactiveClasses = 'text-gray-800 dark:text-white hover:bg-gray-200 hover:text-gray-900';
    @endphp

    <nav class="flex-1 flex flex-col py-4 pr-3">
        <ul class="font-sans space-y-3 flex-1 pl-2">
            @foreach($navItems as $item)
            <li>
                <a href="{{ route($item['route']) }}" data-spa-link class="{{$linkBase}} px-4 py-3 {{ request()->routeIs($item['route']) ? $activeClasses : $inactiveClasses }}">
                    <!-- NO HAY INDICADOR VERTICAL -->
                    
                    {{-- Lógica para colores de ícono/texto --}}
                    @php
                        $iconColor = request()->routeIs($item['route']) ? 'text-white' : 'text-gray-600 dark:text-white group-hover:text-gray-700 dark:group-hover:text-gray-300';
                        $textColor = request()->routeIs($item['route']) ? 'text-white' : 'text-gray-800 dark:text-white group-hover:text-gray-900 dark:group-hover:text-gray-300';
                    @endphp

                    <i class="{{ $item['icon'] }} w-5 text-center"></i>
                    <span :class="!sidebarOpen && 'hidden'" class="font-medium {{ $textColor }}">{{ $item['label'] }}</span>
                </a>
            </li>
            @endforeach
        </ul>
    </nav>
</aside>