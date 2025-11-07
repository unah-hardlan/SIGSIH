<style>
    .header-fixed {
        position: absolute;
        width: 100%;
        top: 0;
        left: 0;
        z-index: 1000;
    }

    .logo-container {
        position: absolute;
        top: 15px;
        z-index: 1000;
    }

    @media (max-width: 767px) {
        .logo-container {
            left: 50%;
            transform: translateX(-75%);
        }
    }

    @media (min-width: 768px) {
        .logo-container {
            left: 50%;
            transform: translateX(-50%);
        }
    }
</style>
<div class="header-fixed">
    <header class="relative flex items-center justify-between h-16 px-3 sm:px-6 bg-gray-50 dark:bg-gray-900 mt-1">

        <div class="md:hidden flex-shrink-0">
            <button @click="sidebarOpen = !sidebarOpen"
                class="p-1 sm:p-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        </div>

        <div class="logo-container">
            <img src="{{ $appLogoUrl ?? asset('images/logo.png') }}" alt="Logo"
                class="h-9 sm:h-10 lg:h-10">
        </div>

        <div class="absolute right-3 md:static md:ml-auto flex items-center gap-3 md:gap-6 z-30">
            <label class="switch">
                <input id="theme-switch" type="checkbox" aria-label="Alternar tema">
                <span class="slider"></span>
            </label>

            <div x-data="{
            open: false,
            items: [],
            unread: 0,
            deleteModalOpen: false,
            notificationToDelete: null,
            toggle() {
                this.open = !this.open;
            },
            async init() {
                await this.fetchNotifications();
            },
            async fetchNotifications() {
                try {
                    const response = await fetch('/api/notificaciones');
                    if (!response.ok) return;
                    const data = await response.json();
                    // Ruta cookie-auth devuelve { data: [...], meta: { unread } }
                    this.items = Array.isArray(data.data) ? data.data : [];
                    this.unread = (data.meta && typeof data.meta.unread === 'number') ? data.meta.unread : (this.items.filter(n => !n.read_at).length);
                } catch (error) {
                    console.error('Error fetching notifications:', error);
                }
            },
            async markAll() {
                try {
                    const response = await fetch('/api/notificaciones/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content
                        }
                    });
                    if (response.ok) {
                        this.items.forEach(n => n.read_at = new Date());
                        this.unread = 0;
                    }
                } catch (error) {
                    console.error('Error marking all as read:', error);
                }
            },
            go(notification) {
                if (!notification.read_at) {
                    fetch(`/api/notificaciones/${notification.id}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content
                        }
                    });
                }
                const targetUrl = notification.url || notification.action_url || '#';
                if (targetUrl && targetUrl !== '#') {
                    window.location.href = targetUrl;
                }
                this.open = false;
            },
            openDeleteModal(notification) {
                this.notificationToDelete = notification;
                this.deleteModalOpen = true;
            },
            cancelDeleteModal() {
                this.deleteModalOpen = false;
                this.notificationToDelete = null;
            },
            async confirmDeleteModal() {
                if (!this.notificationToDelete) return;
                try {
                    const response = await fetch(`/api/notificaciones/${this.notificationToDelete.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content
                        }
                    });
                    if (response.ok) {
                        this.items = this.items.filter(n => n.id !== this.notificationToDelete.id);
                        if (!this.notificationToDelete.read_at) {
                            this.unread = Math.max(0, this.unread - 1);
                        }
                    }
                } catch (error) {
                    console.error('Error deleting notification:', error);
                } finally {
                    this.cancelDeleteModal();
                }
            },
            formatTime(timestamp) {
                const date = new Date(timestamp);
                const now = new Date();
                const diff = Math.floor((now - date) / 1000);
                
                if (diff < 60) return 'Ahora';
                if (diff < 3600) return `${Math.floor(diff / 60)}m`;
                if (diff < 86400) return `${Math.floor(diff / 3600)}h`;
                return `${Math.floor(diff / 86400)}d`;
            }
        }" x-init="init()" class="relative">
                <button @click="toggle()" class="relative text-gray-500 dark:text-gray-400 hover:text-blue-600 mt-1">
                    <i class="fas fa-bell text-base sm:text-lg"></i>
                    <template x-if="unread > 0">
                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[18px] h-4 px-1 bg-red-600 text-white text-[10px] rounded-full" x-text="unread"></span>
                    </template>
                </button>
                <div x-show="open" x-cloak @click.away="open = false" class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 shadow-lg rounded-md py-2 border border-blue-300 backdrop-blur-md max-h-[32rem] overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-2 border-b text-gray-700 dark:text-gray-300 serif-bold text-sm">
                        <span>Notificaciones</span>
                        <button class="text-xs text-blue-600 hover:underline" @click="markAll()" x-show="unread>0">Marcar todas</button>
                    </div>
                    <div class="max-h-80 overflow-y-auto custom-scrollbar">
                        <ul>
                            <template x-if="items.length === 0">
                                <li class="px-4 py-3 text-sm text-gray-500">Sin notificaciones</li>
                            </template>
                            <template x-for="n in items" :key="n.id">
                                <li class="px-4 py-2 hover:bg-blue-200/80 dark:hover:bg-blue-700/80 text-sm nunito-regular text-gray-800 dark:text-gray-200 transition-colors duration-200">
                                    <div class="flex gap-2 items-start">
                                        <button @click.stop="go(n)" class="flex-1 text-left">
                                            <div class="flex gap-2">
                                                <i :class="['fas', n.icon || 'fa-bell', 'mt-0.5', n.severity==='critical'?'text-red-600':(n.severity==='warn'?'text-yellow-500':'text-blue-500')]"></i>
                                                <div class="flex-1">
                                                    <div class="serif-bold" x-text="n.title"></div>
                                                    <div class="text-xs text-gray-600 dark:text-gray-400" x-text="n.body"></div>
                                                    <div class="text-[10px] text-gray-400" x-text="formatTime(n.created_at)"></div>
                                                </div>
                                            </div>
                                        </button>
                                        <div class="flex flex-col items-center gap-2">
                                            <button title="Eliminar" @click.stop="openDeleteModal(n)"
                                                class="text-gray-400 hover:text-red-600 p-1 rounded focus:outline-none">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <span class="w-2 h-2 rounded-full bg-blue-500 mt-1" x-show="!n.read_at"></span>
                                        </div>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </div>
                    
                    <!-- Modal de confirmación de eliminación -->
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
                            <p class="mt-1 text-lg nunito-bold text-gray-800 dark:text-gray-200">¿Eliminar notificación?</p>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Esta acción no se puede deshacer.</p>
                            <div class="mt-5 flex justify-end gap-2">
                                <button type="button" @click="cancelDeleteModal()"
                                    class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded text-sm text-gray-800 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-500 transition-all serif-regular">Cancelar</button>
                                <button type="button" @click="confirmDeleteModal()"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm transition-all serif-regular">Eliminar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div x-data="{ profileOpen: false, logoutConfirm: false }"
                x-effect="document.body.classList.toggle('overflow-hidden', logoutConfirm)"
                class="flex items-center gap-1 sm:gap-2 mr-1 sm:mr-2">
                <div class="relative">
                    <button @click="profileOpen = !profileOpen"
                        class="w-8 h-8 sm:w-7 sm:h-7 rounded-full {{ $clienteAvatar ? 'p-0' : 'bg-blue-500 text-white' }} flex items-center justify-center text-[10px] sm:text-xs font-bold tracking-wide shadow focus:outline-none dark:ring-blue-600/40 hover:shadow-md transition overflow-hidden">
                        @if($clienteAvatar)
                        <img src="{{ asset('storage/' . $clienteAvatar) }}" alt="Avatar de {{ $clienteUsuario }}" class="w-full h-full object-cover">
                        @else
                        <span>{{ $clienteIniciales }}</span>
                        @endif
                    </button>
                    <div x-show="profileOpen" x-cloak @click.away="profileOpen = false" class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 border border-blue-200 dark:border-gray-700 shadow-lg rounded-md py-1 backdrop-blur-md/0">
                        <a href="{{ route('cliente.perfil') }}" data-spa-link @click="profileOpen = false" class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-100 dark:hover:bg-blue-700/50 transition-colors">
                            <i class="fas fa-user-edit text-blue-500 dark:text-white"></i> Perfil
                        </a>
                        <button @click="logoutConfirm = true; profileOpen = false" class="w-full flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-100 dark:hover:bg-blue-700/50 transition-colors">
                            <i class="fas fa-sign-out-alt text-red-500"></i> Cerrar sesión
                        </button>
                    </div>
                </div>
                <div class="hidden sm:flex flex-col items-start">
                    <span class="serif-bold text-gray-800 dark:text-gray-200 text-xs">{{ $clienteUsuario }}</span>
                    <span class="text-xs nunito-regular text-gray-500 dark:text-gray-400">Cliente</span>
                </div>

                <template x-teleport="body">
                    <div x-show="logoutConfirm" x-cloak x-transition.opacity.duration.300ms
                        class="fixed inset-0 flex items-center justify-center z-[99999] bg-black/50 backdrop-blur-sm"
                        @click.self="logoutConfirm=false" @keydown.window.escape="logoutConfirm=false">
                        <div x-show="logoutConfirm"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-6 w-11/12 max-w-sm mx-auto"
                            @click.stop>
                            <p class="mt-1 text-lg nunito-bold text-gray-800 dark:text-gray-200">¿Cerrar sesión?</p>
                            <div class="mt-5 flex justify-end gap-2">
                                <button type="button" @click="logoutConfirm = false"
                                    class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded text-sm md:text-base text-gray-800 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-500 transition-all serif-regular">
                                    Cancelar
                                </button>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm md:text-base transition-all serif-regular">
                                        Confirmar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </header>
</div>