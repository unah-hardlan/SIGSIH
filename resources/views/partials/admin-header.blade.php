<!DOCTYPE html>
<html lang="en">

<body>
    <header class="flex items-center justify-between h-16 px-3 sm:px-6 bg-white dark:bg-gray-900"
        data-user-id="{{ Auth::user()->id_usuario_pk ?? 0 }}">
        <!-- Botón colapsar sidebar -->
        <button @click="sidebarOpen = !sidebarOpen"
            class="p-1 sm:p-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-0 focus:ring-transparent md:hidden">
            <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

        <!-- Botón colapsar sidebar desktop -->
        <button @click="sidebarOpen = !sidebarOpen"
            class="hidden md:block p-1 sm:p-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-0 focus:ring-transparent">
            <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

        <!-- Logo -->
        <div class="flex items-center gap-2 ml-2 sm:ml-4 md:ml-16 lg:ml-24 sm:gap-3">
            <img src="{{ $appLogoUrl ?? asset('images/logo.png') }}" alt="Logo"
                class="app-logo ml-2 sm:ml-20 md:ml-16 lg:ml-24"
                style="--app-logo-max: {{ ($appLogoHeight ?? 96) }}px;">
            <!-- Optional app name display -->
            {{-- <span class="hidden sm:block text-xl nunito-bold ml-2">{{ $appName ?? '' }}</span> --}}
        </div>

        <!-- Acciones derecha -->
        <div class="flex items-center gap-3 md:gap-6 z-50">
            <label class="switch">
                <input id="theme-switch" type="checkbox" aria-label="Alternar tema">
                <span class="slider"></span>
            </label>
            <!-- Notificaciones -->
            <div x-data="notificationsDropdown()" x-init="init()" class="relative">
                <button @click="toggle()" class="relative text-gray-500 dark:text-gray-400 hover:text-blue-600">
                    <i class="fas fa-bell text-base sm:text-lg"></i>
                    <template x-if="unread > 0">
                        <span
                            class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[18px] h-4 px-1 bg-red-600 text-white text-[10px] rounded-full"
                            x-text="unread"></span>
                    </template>
                </button>
                <div x-show="open" x-cloak @click.away="open = false"
                    class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 shadow-lg rounded-md py-2 border border-blue-300 backdrop-blur-md max-h-[32rem] overflow-hidden">
                    <div
                        class="flex items-center justify-between px-4 py-2 border-b text-gray-700 dark:text-gray-300 serif-bold text-sm">
                        <span>Notificaciones</span>
                        <button class="text-xs text-blue-600 hover:underline" @click="markAll()"
                            x-show="unread>0">Marcar todas</button>
                    </div>
                    <div class="max-h-80 overflow-y-auto custom-scrollbar">
                        <ul>
                            <template x-if="items.length === 0">
                                <li class="px-4 py-3 text-sm text-gray-500">Sin notificaciones</li>
                            </template>
                            <template x-for="n in items" :key="n.id">
                                <li
                                    class="px-4 py-2 hover:bg-blue-200/80 dark:hover:bg-blue-700/80 text-sm nunito-regular text-gray-800 dark:text-gray-200 transition-colors duration-200">
                                    <div class="flex gap-2 items-start">
                                        <button @click.stop="go(n)" class="flex-1 text-left">
                                            <div class="flex gap-2">
                                                <i
                                                    :class="['fas', n.icon || 'fa-bell', 'mt-0.5', n.severity==='critical'?'text-red-600':(n.severity==='warn'?'text-yellow-500':'text-blue-500')]"></i>
                                                <div class="flex-1">
                                                    <div class="serif-bold" x-text="n.title"></div>
                                                    <div class="text-xs text-gray-600 dark:text-gray-400"
                                                        x-text="n.body"></div>
                                                    <div class="text-[10px] text-gray-400"
                                                        x-text="formatTime(n.created_at)"></div>
                                                </div>
                                            </div>
                                        </button>
                                        <div class="flex flex-col items-center gap-2">
                                            <button title="Eliminar" @click.stop="openDeleteModal(n)"
                                                class="text-gray-400 hover:text-red-600 p-1 rounded focus:outline-none">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <span class="w-2 h-2 rounded-full bg-blue-500 mt-1"
                                                x-show="!n.read_at"></span>
                                        </div>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </div>
                    <!-- <div class="px-4 py-3 mt-2 text-xs nunito-regular text-blue-600 dark:text-blue-400 hover:underline cursor-pointer border-t"
                         @click="$dispatch('navigate', {url:'/admin/notificaciones', viewName:'notificaciones'})">Ver
                        todas</div> -->
                    <!-- Delete confirmation modal for notifications (scoped to notificationsDropdown() Alpine data) -->
                    <div x-show="deleteModalOpen" x-cloak x-transition.opacity.duration.200ms
                        class="fixed inset-0 flex items-center justify-center z-[10000] transition-all duration-200 ease-in-out bg-black/40"
                        style="-webkit-backdrop-filter: blur(4px); backdrop-filter: blur(4px);"
                        @click.self="cancelDeleteModal()" @keydown.window.escape="cancelDeleteModal()">
                        <div x-show="deleteModalOpen" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-5 w-11/12 max-w-sm mx-auto"
                            @click.stop>

                            <p class="mt-1 text-lg nunito-bold text-gray-800 dark:text-gray-200">¿Eliminar notificación?
                            </p>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Esta acción no se puede deshacer.
                            </p>
                            <div class="mt-5 flex justify-end gap-2">
                                <button type="button" @click="cancelDeleteModal()"
                                    class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded text-sm text-gray-800 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-500 transition-all">Cancelar</button>
                                <button type="button" @click="confirmDeleteModal()"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm transition-all">Eliminar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usuario y perfil (icono antes del nombre, icono con menú) -->
            <div class="flex items-center gap-1 sm:gap-2 mr-1 sm:mr-2">
                <div x-data="{ open: false, logoutConfirm: false }" class="relative"
                    x-effect="document.body.classList.toggle('overflow-hidden', logoutConfirm)">
                    <button @click="open = !open"
                        class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs font-bold focus:outline-none focus:ring-0 focus:ring-transparent">
                        <template x-if="$store.perfil.persona?.avatar_path">
                            <img :src="`${window.location.origin}/storage/${$store.perfil.persona.avatar_path}`"
                                alt="Foto de perfil" class="w-full h-full rounded-full object-cover">
                        </template>
                        <template x-if="!$store.perfil.persona?.avatar_path">
                            <span>{{ substr(strtoupper(Auth::user()->usuario ?? 'U'), 0, 2) }}</span>
                        </template>
                    </button>
                    <div x-show="open" x-cloak @click.away="open = false" @header-link-click="open = false"
                        class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 border border-blue-200 dark:border-gray-700 shadow-lg rounded-md py-1 backdrop-blur-md/0">
                        <x-admin.header-menu-link view-name="perfil" class="">
                            <i class="fas fa-user-edit text-blue-500 dark:text-white"></i>
                            Editar perfil
                        </x-admin.header-menu-link>
                        <x-admin.header-menu-link
                            @click="logoutConfirm = true; document.dispatchEvent(new CustomEvent('logout-modal-show'))">
                            <i class="fas fa-sign-out-alt text-red-500"></i>
                            Cerrar sesión
                        </x-admin.header-menu-link>
                    </div>
                    <!-- Inline logout confirmation modal (Alpine-only, avoids Blade component issues) -->
                    <div x-show="logoutConfirm" x-cloak x-transition.opacity.duration.300ms
                        class="fixed inset-0 flex items-center justify-center z-[10000] transition-all duration-300 ease-in-out bg-black/60 dark:bg-black/80 backdrop-blur-md"
                        style="-webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px); background:rgba(0,0,0,0.65);"
                        @click.self="logoutConfirm=false" @keydown.window.escape="logoutConfirm=false">
                        <div x-show="logoutConfirm" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-6 w-11/12 max-w-sm mx-auto"
                            @click.stop>

                            <p class="mt-3 text-lg nunito-bold text-gray-800 dark:text-gray-200">¿Estás seguro de que
                                deseas cerrar sesión?</p>
                            <div class="mt-5 flex justify-end gap-2">
                                <button type="button"
                                    @click="logoutConfirm = false; document.dispatchEvent(new CustomEvent('logout-modal-hide'))"
                                    class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded text-sm md:text-base text-gray-800 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-500 transition-all serif-regular">Cancelar</button>
                                <button type="button"
                                    @click="logoutConfirm = false; document.dispatchEvent(new CustomEvent('logout-modal-hide')); (window.appLogout && window.appLogout())"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm md:text-base transition-all serif-regular">Confirmar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hidden sm:flex flex-col items-start">
                    <span
                        class="serif-bold text-gray-800 dark:text-gray-200 text-sm">{{ Auth::user()->usuario ?? 'Usuario' }}</span>
                    <span
                        class="text-xs nunito-regular text-gray-500 dark:text-gray-400">{{ Auth::user()->rol?->rol ?? '' }}</span>
                </div>

            </div>
        </div>
    </header>

</body>

</html>