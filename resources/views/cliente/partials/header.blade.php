@php
    $authUser = Auth::user();
    $clienteUsuario = $authUser->usuario ?? 'Usuario';
    $clienteIniciales = strtoupper(substr($clienteUsuario, 0, 2));
@endphp
<header class="flex items-center justify-between h-16 px-3 sm:px-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
    <!-- Botón colapsar sidebar -->
    <button @click="sidebarOpen = !sidebarOpen"
        class="p-1 sm:p-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 md:hidden">
        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <!-- Botón colapsar sidebar desktop -->
    <button @click="sidebarOpen = !sidebarOpen"
        class="hidden md:block p-1 sm:p-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <!-- Logo y título -->
    <div class="flex items-center gap-2 ml-2 sm:ml-4">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-auto">
        <span class="hidden sm:block text-lg font-semibold text-gray-800 dark:text-gray-200">Portal Cliente</span>
    </div>

    <!-- Acciones derecha -->
    <div class="flex items-center gap-3 md:gap-6">
        <!-- Toggle tema -->
        <button @click="toggleTheme()" 
            class="p-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <i x-show="theme === 'light'" class="fas fa-moon text-sm"></i>
            <i x-show="theme === 'dark'" class="fas fa-sun text-sm"></i>
        </button>

        <!-- Notificaciones -->
        <div x-data="{ openNotif: false }" class="relative">
            <button @click="openNotif = !openNotif" class="relative text-gray-500 dark:text-gray-400 hover:text-blue-600 p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-bell text-sm"></i>
                <span class="absolute top-1 right-1 inline-block w-2 h-2 bg-red-500 rounded-full"></span>
            </button>
            <div x-show="openNotif" x-cloak @click.away="openNotif = false"
                class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 shadow-lg rounded-md py-2 border border-gray-200 dark:border-gray-700 z-50">
                <div class="px-4 py-2 border-b text-gray-700 dark:text-gray-300 font-semibold text-sm">Notificaciones</div>
                <ul>
                    <li class="px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm text-gray-800 dark:text-gray-200 cursor-pointer">
                        Tu solicitud ha sido procesada
                        <span class="block text-xs text-gray-500 dark:text-gray-400">Hace 5 minutos</span>
                    </li>
                    <li class="px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm text-gray-800 dark:text-gray-200 cursor-pointer">
                        Servicio programado para mañana
                        <span class="block text-xs text-gray-500 dark:text-gray-400">Hace 2 horas</span>
                    </li>
                </ul>
                <div class="px-4 py-2 text-xs text-blue-600 dark:text-blue-400 hover:underline cursor-pointer">Ver todas</div>
            </div>
        </div>

        <!-- Usuario -->
        <div x-data="{ open: false, logoutConfirm: false }" class="relative">
            <button @click="open = !open"
                class="flex items-center gap-2 p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <div class="w-7 h-7 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs font-bold">
                    {{ $clienteIniciales }}
                </div>
                <div class="hidden sm:flex flex-col items-start">
                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $clienteUsuario }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Cliente</span>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-500 dark:text-gray-400"></i>
            </button>
            
            <div x-show="open" x-cloak @click.away="open = false"
                class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-lg rounded-md py-1 z-50">
                <a href="{{ route('cliente.perfil') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-user text-blue-500"></i>
                    Mi perfil
                </a>
                <button @click="logoutConfirm = true; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-sign-out-alt text-red-500"></i>
                    Cerrar sesión
                </button>
            </div>

            <!-- Modal confirmación logout -->
            <div x-show="logoutConfirm" x-cloak x-transition.opacity.duration.300ms
                class="fixed inset-0 bg-black/40 dark:bg-black/60 flex items-center justify-center z-50">
                <div x-show="logoutConfirm" 
                    x-transition:enter="transition ease-out duration-300" 
                    x-transition:enter-start="opacity-0 scale-95" 
                    x-transition:enter-end="opacity-100 scale-100"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-6 w-11/12 max-w-sm mx-auto" 
                    @click.stop>
                    <div class="text-center">
                        <i class="fas fa-sign-out-alt text-red-500 text-3xl mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Cerrar sesión</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">¿Estás seguro de que deseas cerrar sesión?</p>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="logoutConfirm = false" 
                            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded text-sm text-gray-800 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                            Cancelar
                        </button>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" 
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm transition-colors">
                                Confirmar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>