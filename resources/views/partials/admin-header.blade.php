<!DOCTYPE html>
<html lang="en">
<body>
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
    <div class="flex items-center gap-2 sm:gap-4 md:gap-6 z-50">
        <label class="switch">
            <input id="theme-switch" type="checkbox" aria-label="Alternar tema">
            <span class="slider"></span>
        </label>
        <!-- Notificaciones -->
        <div x-data="{ openNotif: false }" class="relative">
            <button @click="openNotif = !openNotif" class="relative text-gray-500 hover:text-blue-600">
                <i class="fas fa-bell text-base sm:text-lg"></i>
                <span class="absolute top-0 right-0 inline-block w-2 h-2 bg-red-600 rounded-full"></span>
            </button>
            <div x-show="openNotif" @click.away="openNotif = false"
                class="absolute right-0 mt-2 w-64 bg-white shadow-lg rounded-md py-2 border border-blue-300 backdrop-blur-md">
                <div class="px-4 py-2 border-b text-gray-700 serif-bold text-sm">Notificaciones</div>
                <ul>
                    <li class="px-4 py-2 hover:bg-blue-200/80 text-sm nunito-regular text-gray-800 cursor-pointer transition-colors duration-200">
                        Nuevo reporte recibido
                        <span class="block text-xs nunito-regular text-gray-500">Hace 5 minutos</span>
                    </li>
                    <li class="px-4 py-2 hover:bg-blue-200/80 text-sm nunito-regular text-gray-800 cursor-pointer transition-colors duration-200">
                        Actualización de perfil completada
                        <span class="block text-xs nunito-regular text-gray-500">Hace 1 hora</span>
                    </li>
                    <li class="px-4 py-2 hover:bg-blue-200/80 text-sm nunito-regular text-gray-800 cursor-pointer transition-colors duration-200">
                        Mensaje de soporte técnico
                        <span class="block text-xs nunito-regular text-gray-500">Ayer</span>
                    </li>
                </ul>
                <div class="px-4 py-2 text-xs nunito-regular text-blue-600 hover:underline cursor-pointer">Ver todas</div>
            </div>
        </div>

        <!-- Usuario y perfil (icono antes del nombre, icono con menú) -->
        <div class="flex items-center gap-1 sm:gap-2 mr-1 sm:mr-2">
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs font-bold focus:outline-none">
                    JP
                </button>
                <div x-show="open" @click.away="open = false"
                    class="absolute right-0 mt-2 w-40 bg-white border border-blue-300 shadow rounded-md py-1">
                    <button @click="loadView('perfil'); open = false"
                        class="w-full text-left px-4 py-2 text-sm nunito-regular text-gray-700 hover:bg-blue-200/80 transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-user-edit text-blue-500"></i>
                        Editar perfil
                    </button>
                    <button type="button" onclick="window.appLogout && window.appLogout()"
                        class="w-full text-left px-4 py-2 text-sm nunito-regular text-gray-700 hover:bg-blue-200/80 transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-sign-out-alt text-red-500"></i>
                        Cerrar sesión
                    </button>
                </div>
            </div>
            <div class="hidden sm:flex flex-col items-start">
                <span class="serif-bold text-gray-800 text-sm">Juan Pérez</span>
                <span class="text-xs nunito-regular text-gray-500">Técnico</span>
            </div>
        </div>
    </div>
</header>

</body>
</html>