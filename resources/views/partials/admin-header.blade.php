<header class="flex items-center justify-between h-16 px-3 sm:px-6">
    <!-- Botón colapsar sidebar -->
    <button @click="sidebarOpen = !sidebarOpen"
        class="p-1 sm:p-2 rounded-md text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 md:hidden">
        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <!-- Botón colapsar sidebar desktop -->
    <button @click="sidebarOpen = !sidebarOpen"
        class="hidden md:block p-1 sm:p-2 rounded-md text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <!-- Logo -->
    <div class="flex items-center gap-2 sm:gap-3">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-16 sm:h-20 md:h-24 lg:h-28 ml-2 sm:ml-8 md:ml-16 lg:ml-24">
        <!-- Removed 'Mi App' text -->
    </div>

    <!-- Acciones derecha -->
    <div class="flex items-center gap-2 sm:gap-4 md:gap-6">
        <!-- Notificaciones -->
        <button class="relative text-gray-500 hover:text-blue-600">
            <i class="fas fa-bell text-base sm:text-lg"></i>
            <span class="absolute top-0 right-0 inline-block w-2 h-2 bg-red-600 rounded-full"></span>
        </button>

        <!-- Usuario y perfil (icono antes del nombre, icono con menú) -->
        <div class="flex items-center gap-1 sm:gap-2 mr-1 sm:mr-2">
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs font-bold focus:outline-none">
                    JP
                </button>
                <div x-show="open" @click.away="open = false"
                    class="absolute right-0 mt-2 w-40 bg-white shadow rounded-md py-1 z-50">
                    <button @click="loadView('perfil'); open = false"
                        class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">Editar perfil</button>
                    <button type="button" onclick="window.appLogout && window.appLogout()"
                        class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">Cerrar sesión</button>
                </div>
            </div>
            <div class="hidden sm:flex flex-col items-start">
                <span class="font-semibold text-gray-800 text-sm">Juan Pérez</span>
                <span class="text-xs text-gray-500">Técnico</span>
            </div>
        </div>
    </div>
</header>