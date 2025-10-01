@php
    $authUser = Auth::user();
    $clienteUsuario = $authUser->usuario ?? 'Usuario';
    $clienteIniciales = strtoupper(substr($clienteUsuario, 0, 2));
@endphp
<header class="flex items-center justify-between h-16 px-3 sm:px-6 bg-white dark:bg-gray-900">
    <!-- Botón colapsar sidebar móvil -->
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

    <!-- Logo -->
    <div class="flex items-center gap-2 ml-2 sm:ml-4 md:ml-16 lg:ml-24 sm:gap-3">
        <img src="{{ $appLogoUrl ?? asset('images/logo.png') }}" alt="Logo" class="app-logo ml-2 sm:ml-20 md:ml-16 lg:ml-24"
             style="--app-logo-max: {{ ($appLogoHeight ?? 72) }}px;">
    </div>

    <!-- Acciones derecha -->
    <div class="flex items-center gap-3 md:gap-6 z-50" x-data="{ open:false, logoutConfirm:false }">
        <!-- Switch de tema (mismo id que admin para reutilizar script) -->
        <label class="switch">
            <input id="theme-switch" type="checkbox" aria-label="Alternar tema">
            <span class="slider"></span>
        </label>

        <!-- Notificaciones -->
        <div x-data="{ openNotif: false }" class="relative">
            <button @click="openNotif = !openNotif" class="relative text-gray-500 dark:text-gray-400 hover:text-blue-600">
                <i class="fas fa-bell text-base sm:text-lg"></i>
                <span class="absolute top-0 right-0 inline-block w-2 h-2 bg-red-600 rounded-full"></span>
            </button>
            <div x-show="openNotif" x-cloak @click.away="openNotif = false"
                class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 shadow-lg rounded-md py-2 border border-blue-300 backdrop-blur-md">
                <div class="px-4 py-2 border-b text-gray-700 dark:text-gray-300 serif-bold text-sm">Notificaciones</div>
                <ul>
                    <li class="px-4 py-2 hover:bg-blue-200/80 dark:hover:bg-blue-700/80 text-sm nunito-regular text-gray-800 dark:text-gray-200 cursor-pointer transition-colors duration-200">
                        Tu solicitud ha sido procesada
                        <span class="block text-xs nunito-regular text-gray-500 dark:text-gray-400">Hace 5 minutos</span>
                    </li>
                    <li class="px-4 py-2 hover:bg-blue-200/80 dark:hover:bg-blue-700/80 text-sm nunito-regular text-gray-800 dark:text-gray-200 cursor-pointer transition-colors duration-200">
                        Servicio programado para mañana
                        <span class="block text-xs nunito-regular text-gray-500 dark:text-gray-400">Hace 2 horas</span>
                    </li>
                </ul>
                <div class="px-4 py-2 text-xs nunito-regular text-blue-600 dark:text-blue-400 hover:underline cursor-pointer">Ver todas</div>
            </div>
        </div>

        <!-- Usuario -->
    <div class="flex items-center gap-1 sm:gap-2 mr-1 sm:mr-2">
            <div class="relative">
                <button @click="open = !open"
                    class="w-11 h-9 sm:w-10 sm:h-10 rounded-full bg-blue-500 text-white flex items-center justify-center text-sm sm:text-base font-bold tracking-wide shadow focus:outline-none dark:ring-blue-600/40 hover:shadow-md transition">
                    <span>{{ $clienteIniciales }}</span>
                </button>
                <div x-show="open" x-cloak @click.away="open = false"
                    class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 border border-blue-200 dark:border-gray-700 shadow-lg rounded-md py-1 backdrop-blur-md/0">
                    <a href="{{ route('cliente.perfil') }}" class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-100 dark:hover:bg-blue-700/50 transition-colors">
                        <i class="fas fa-user-edit text-blue-500 dark:text-white"></i>
                        Perfil
                    </a>
                    <button @click="logoutConfirm = true; open = false" class="w-full flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-100 dark:hover:bg-blue-700/50 transition-colors">
                        <i class="fas fa-sign-out-alt text-red-500"></i>
                        Cerrar sesión
                    </button>
                </div>
            </div>
            <div class="hidden sm:flex flex-col items-start">
                <span class="serif-bold text-gray-800 dark:text-gray-200 text-sm">{{ $clienteUsuario }}</span>
                <span class="text-xs nunito-regular text-gray-500 dark:text-gray-400">Cliente</span>
            </div>
        </div>

        <!-- Modal logout -->
        <div x-show="logoutConfirm" x-cloak x-transition.opacity.duration.300ms
             class="fixed inset-0 bg-black/40 dark:bg-black/60 flex items-center justify-center z-50 transition-all duration-300 ease-in-out">
            <div x-show="logoutConfirm" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-6 w-11/12 max-w-sm mx-auto" @click.stop>
                <p class="mt-1 text-lg nunito-bold text-gray-800 dark:text-gray-200">¿Cerrar sesión?</p>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" @click="logoutConfirm = false" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded text-sm md:text-base text-gray-800 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-500 transition-all serif-regular">Cancelar</button>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm md:text-base transition-all serif-regular">Confirmar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>