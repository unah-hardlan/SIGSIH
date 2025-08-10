<header class="flex items-center justify-between h-16 px-6">
    <!-- Botón colapsar sidebar -->
    <button @click="sidebarOpen = !sidebarOpen"
        class="p-2 rounded-md text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <!-- Logo -->
    <div class="flex items-center gap-3">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-24 ml-24">
        <!-- Removed 'Mi App' text -->
    </div>

    <!-- Acciones derecha -->
    <div class="flex items-center gap-6">
        <!-- Notificaciones -->
        <button class="relative text-gray-500 hover:text-blue-600">
            <i class="fas fa-bell text-xl"></i>
            <span class="absolute top-0 right-0 inline-block w-2 h-2 bg-red-600 rounded-full"></span>
        </button>

        <!-- Usuario y perfil (icono antes del nombre, icono con menú) -->
        <div class="flex items-center gap-2 mr-2">
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-sm font-bold focus:outline-none">
                    JP
                </button>
                <div x-show="open" @click.away="open = false"
                    class="absolute right-0 mt-2 w-40 bg-white shadow rounded-md py-1 z-50">
                    <button @click="loadView('perfil'); open = false" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">Editar perfil</button>
                    <a href="/login" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Cerrar sesión</a>
                </div>
            </div>
            <div class="flex flex-col items-start">
                <span class="font-semibold text-gray-800 text-sm">Juan Pérez</span>
                <span class="text-xs text-gray-500">Técnico</span>
            </div>
        </div>
    </div>
</header>