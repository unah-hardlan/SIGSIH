<!-- Sidebar móvil overlay -->
<div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
    class="fixed inset-0 z-40 bg-black/50 md:hidden"
    x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">
</div>

<!-- Sidebar -->
<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-0"
    :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">
    
    <!-- Logo del sidebar -->
    <div class="flex items-center justify-center h-16 px-4 border-b border-gray-200 dark:border-gray-700">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-auto">
        <span class="ml-2 text-lg font-semibold text-gray-800 dark:text-gray-200">SIGSIH</span>
    </div>

    <!-- Navegación -->
    <nav class="flex-1 px-4 py-6 space-y-2">
        <!-- Mi Perfil -->
        <a href="{{ route('cliente.perfil') }}" 
            class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors
                {{ request()->routeIs('cliente.perfil') 
                    ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' 
                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <i class="fas fa-user w-5 text-center"></i>
            <span>Perfil</span>
        </a>

        <!-- Cotizaciones -->
        <a href="{{ route('cliente.cotizaciones') }}" 
            class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors
                {{ request()->routeIs('cliente.cotizaciones') 
                    ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' 
                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <i class="fas fa-calculator w-5 text-center"></i>
            <span>Cotizaciones</span>
        </a>

        <!-- Órdenes de Servicio -->
        <a href="{{ route('cliente.ordenes') }}" 
            class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors
                {{ request()->routeIs('cliente.ordenes') 
                    ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' 
                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <i class="fas fa-clipboard-list w-5 text-center"></i>
            <span>Órdenes de Servicio</span>
        </a>

        <!-- Facturas -->
        <a href="{{ route('cliente.facturas') }}" 
            class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors
                {{ request()->routeIs('cliente.facturas') 
                    ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' 
                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <i class="fas fa-file-invoice-dollar w-5 text-center"></i>
            <span>Facturación</span>
        </a>
    </nav>

    <!-- Footer del sidebar -->
    @php
        $authUser = Auth::user();
        $clienteUsuario = $authUser->usuario ?? 'Usuario';
        $clienteIniciales = strtoupper(substr($clienteUsuario, 0, 2));
    @endphp
    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs font-bold">
                {{ $clienteIniciales }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                    {{ $clienteUsuario }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Cliente</p>
            </div>
        </div>
    </div>
</aside>