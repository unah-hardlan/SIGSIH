<aside x-data="{}" x-init="$store.clienteLogout = $store.clienteLogout || { modalOpen: false }" x-show="sidebarOpen" :class="{
        'fixed inset-y-0 left-0 w-72 min-w-[18rem] h-full': isMobile,
        
        'sticky top-4 w-72 min-w-[18rem] max-h-[92vh] ml-4': !isMobile && sidebarOpen,
        'sticky top-4 w-20 min-w-[5rem] max-h-[92vh] ml-4': !isMobile && !sidebarOpen,

        'brightness-50 pointer-events-none': $store.clienteLogout?.modalOpen,
        'brightness-100 pointer-events-auto': !$store.clienteLogout?.modalOpen,
    }"
    class=" text-gray-800 dark:text-white flex flex-col flex-shrink-0 p-4 shadow-lg border border-gray-500/40 dark:border-gray-700 rounded-xl transition-all duration-300 ease-in-out overflow-y-auto"
    style="scrollbar-width: thin; scrollbar-color: #D1D5DB #FFFFFF; -webkit-overflow-scrolling: touch; z-index: 9999;">

    @php
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
    @endphp

    <nav class="flex-1 flex flex-col py-4 pr-3">
        <ul class="font-sans space-y-3 flex-1 pl-2 pb-6">
            @foreach($navItems as $item)
            <li>
                
                <a href="{{ route($item['route']) }}" 
                   data-spa-link 
                   class="{{ $linkBase }} {{ request()->routeIs($item['route'].'*') ? $activeClasses : $inactiveClasses }}">
                    
                    <i class="{{ $item['icon'] }} w-5 text-center"></i>
                    
                    <span :class="!sidebarOpen && 'hidden'" class="font-medium">{{ $item['label'] }}</span>
                </a>
            </li>
            @endforeach
        </ul>
    </nav>

    <div class="sticky bottom-0 px-4 py-3 border-t border-gray-100 dark:border-gray-700 z-10">
        <div class="text-xs text-gray-500 dark:text-gray-400 text-center">© {{ date('Y') }} Hardlan</div>
    </div>
</aside>