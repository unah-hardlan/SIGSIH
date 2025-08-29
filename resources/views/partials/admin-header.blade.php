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
            <div x-data="{ open: false, logoutConfirm: false }" class="relative">
                <button @click="open = !open"
                    class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs font-bold focus:outline-none">
                    <span x-text="(($store.perfil.user?.nombre_usuario || $store.perfil.user?.usuario || 'U')+' ').trim().split(' ').map(p=>p[0]).slice(0,2).join('').toUpperCase()">U</span>
                </button>
                <div x-show="open" @click.away="open = false" @header-link-click="open = false"
                    class="absolute right-0 mt-2 w-40 bg-white border border-blue-200 shadow rounded-md py-1">
                    <x-admin.header-menu-link view-name="perfil"
                        class="">
                        <i class="fas fa-user-edit text-blue-500"></i>
                        Editar perfil
                    </x-admin.header-menu-link>
                    <x-admin.header-menu-link @click="logoutConfirm = true">
                        <i class="fas fa-sign-out-alt text-red-500"></i>
                        Cerrar sesión
                    </x-admin.header-menu-link>
                </div>
                <!-- Inline logout confirmation modal (Alpine-only, avoids Blade component issues) -->
                <div x-show="logoutConfirm" x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-50 flex items-center justify-center px-4">
                    <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="logoutConfirm = false"></div>
                    <div class="bg-white rounded-lg shadow-xl p-6 w-11/12 max-w-sm mx-auto z-50" @click.stop>
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg md:text-xl font-semibold text-gray-700">Cerrar sesión</h3>
                            <button class="text-gray-500 text-lg" @click="logoutConfirm = false"><i class="fas fa-times"></i></button>
                        </div>
                        <p class="mt-3 text-base text-gray-600">Confirma que deseas cerrar tu sesión.</p>
                        <div class="mt-5 flex justify-end gap-2">
                            <button type="button" @click="logoutConfirm = false" class="transition duration-200 ease-in-out px-4 py-2 bg-gray-300 rounded text-sm md:text-base hover:bg-gray-400">Cancelar</button>
                            <button type="button" @click="logoutConfirm = false; (window.appLogout && window.appLogout())" class="transition duration-200 ease-in-out px-4 py-2 bg-red-600 text-white rounded text-sm md:text-base hover:bg-red-700">Confirmar</button>
                        </div>
</div>
                </div>
            </div>
            <div class="hidden sm:flex flex-col items-start">
                <span class="serif-bold text-gray-800 text-sm" x-text="$store.perfil.user?.nombre_usuario || $store.perfil.user?.usuario || 'Usuario'">Usuario</span>
                <span class="text-xs nunito-regular text-gray-500" x-text="$store.perfil.persona?.cargo || ''"></span>
            </div>
        </div>
    </div>
</header>

</body>
</html>