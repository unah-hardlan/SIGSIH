<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- SPA Meta Tags --}}
    @if(request()->header('X-SPA-Page'))
    <meta name="spa-page" content="true">
    <meta name="spa-view" content="{{ request()->header('X-SPA-View') }}">
    @endif

    @vite(['resources/css/app.css', 'resources/css/global.css', 'resources/css/theme.css', 'resources/js/app.js', 'resources/js/sidebar.js', 'resources/js/session.js', 'resources/js/auth-guard.js', 'resources/js/toast.js', 'resources/js/tabla-responsive.js'])

    <link href="https://fonts.googleapis.com/css2?family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">

    <script defer>
        document.addEventListener('alpine:init', () => {
            // Hidratar desde localStorage para evitar parpadeo en header
            let cachedUser = null;
            try { cachedUser = JSON.parse(localStorage.getItem('authUser') || 'null'); } catch(_) {}
            Alpine.store('perfil', {
                firstTime: false,
                user: cachedUser,
                persona: null,
            });
            // Al cargar el layout, usa cache y luego (opcional) refresca
            (async () => {
                try {
                    // Sembrar desde cache si existe
                    let cachedPersona = null, cachedFirst = null;
                    try { cachedPersona = JSON.parse(localStorage.getItem('authPersona') || 'null'); } catch(_) {}
                    try { cachedFirst = JSON.parse(localStorage.getItem('firstTime') || 'null'); } catch(_) {}
                    // Si ya hay persona en cache, considera perfil completo
                    if (cachedPersona) {
                        Alpine.store('perfil').firstTime = false;
                        Alpine.store('perfil').persona = cachedPersona;
                    } else if (cachedFirst !== null) {
                        Alpine.store('perfil').firstTime = !!cachedFirst;
                    }

                    // Refresco opcional (silencioso)
                    const token = localStorage.getItem('authToken');
                    if (token) {
                        const res = await fetch('/api/me', { headers: { 'Authorization': `Bearer ${token}` } });
                        if(res.ok){
                            const data = await res.json();
                                            Alpine.store('perfil').firstTime = !!(data?.primer_ingreso && !data?.persona);
                            Alpine.store('perfil').user = data?.usuario || null;
                            Alpine.store('perfil').persona = data?.persona || null;
                            // Actualizar cache local
                            try { localStorage.setItem('authUser', JSON.stringify(Alpine.store('perfil').user)); } catch(_) {}
                            try { localStorage.setItem('authPersona', JSON.stringify(Alpine.store('perfil').persona)); } catch(_) {}
                            try { localStorage.setItem('firstTime', JSON.stringify(Alpine.store('perfil').firstTime)); } catch(_) {}
                        }
                    }
                } catch(e) {
                    // Silencioso
                }
            })();
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @livewireStyles
</head>

<body class="bg-gray-50 dark:bg-gray-900 min-h-screen flex flex-col" 
      x-data="{ 
          sidebarOpen: false, 
          isMobile: window.innerWidth < 768 
      }" 
      x-init="initResponsiveSidebar($data)">
    <div class="flex h-screen min-h-0 relative">
        <!-- Overlay para móviles SOLO -->
        <div x-show="sidebarOpen && isMobile" x-cloak
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black bg-opacity-50"
             style="z-index: 9998;">
        </div>

        @include('partials.admin-sidebar')

        <main class="flex-1 p-3 sm:p-6 overflow-y-auto h-screen bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
            @include('partials.admin-header')
            @hasSection('page-header')
            <div class="bg-white dark:bg-gray-900 p-4 rounded shadow mb-6">
                @yield('page-header')
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 p-3 sm:p-6 rounded-lg shadow">
                @if(isset($partialView))
                @include($partialView)
                @else
                @yield('content')
                @endif
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeSwitch = document.getElementById('theme-switch');
            const html = document.documentElement;

            // Verificar tema guardado
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                html.classList.add('dark');
                themeSwitch.checked = true;
            } else {
                html.classList.remove('dark');
                themeSwitch.checked = false;
            }

            // Escuchar cambios
            themeSwitch.addEventListener('change', function() {
                if (this.checked) {
                    html.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                } else {
                    html.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                }
            });
        });
    </script>

    @livewireScripts
</body>

</html>