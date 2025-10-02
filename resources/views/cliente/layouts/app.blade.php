<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $appName ?? (config('app.name','SIGSIH').' - Cliente') }}</title>

    {{-- Meta para navegación SPA opcional (reutilizable) --}}
    @if(request()->header('X-SPA-Page'))
    <meta name="spa-page" content="true">
    <meta name="spa-view" content="{{ request()->header('X-SPA-View') }}">
    @endif

    @vite([
        'resources/css/app.css',
        'resources/css/global.css',
        'resources/css/theme.css',
        'resources/js/app.js',
        'resources/js/sidebar.js',
        'resources/js/session.js',
        'resources/js/auth-guard.js',
        'resources/js/toast.js',
        'resources/js/tabla-responsive.js'
    ])

    <!-- Script de tema (idéntico al admin para consistencia) -->
    <script>
        (function() {
            try {
                const saved = localStorage.getItem('theme');
                const isDark = saved ? saved === 'dark' : (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', !!isDark);
            } catch (_) {}
        })();
    </script>

    <link href="https://fonts.googleapis.com/css2?family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Bootstrap auth info para Alpine (cliente) -->
    <script type="application/json" id="cliente-auth-bootstrap">
        @json(['user' => $authUser ?? null, 'persona' => $authPersona ?? null])
    </script>
    <script defer>
        document.addEventListener('alpine:init', () => {
            let initial = { user: null, persona: null };
            try {
                const el = document.getElementById('cliente-auth-bootstrap');
                if (el && el.textContent) initial = JSON.parse(el.textContent);
            } catch(_) {}
            Alpine.store('clientePerfil', {
                user: initial.user,
                persona: initial.persona,
            });
            // Store para controlar oscurecimiento del sidebar cuando modal logout abierto
            Alpine.store('clienteLogout', { modalOpen: false });
        });
    </script>

    @livewireStyles
    @stack('styles')
</head>

<body class="bg-gray-50 dark:bg-gray-900 min-h-screen flex flex-col" x-data="{sidebarOpen:false,isMobile:window.innerWidth<768}" x-init="initResponsiveSidebar && initResponsiveSidebar($data); sidebarOpen=!isMobile" @closemobilesidebar.window="if(isMobile){sidebarOpen=false}">
    <div class="flex min-h-screen relative">
        <!-- Overlay móvil -->
    <div x-show="sidebarOpen && isMobile" x-cloak x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="sidebarOpen=false" class="fixed inset-0 bg-black bg-opacity-50" style="z-index:9990"></div>

        @include('cliente.partials.sidebar')

        <main class="flex-1 min-h-screen p-3 sm:p-6 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
            @include('cliente.partials.header')
            @hasSection('page-header')
            <div class="bg-white dark:bg-gray-900 p-4 rounded mb-6">
                @yield('page-header')
            </div>
            @endif

            <div class="bg-white dark:bg-gray-900 p-3 sm:p-6 rounded-lg">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        (function() {
            const html = document.documentElement;
            function applyThemeFromStorage(){
                const isDark = html.classList.contains('dark');
                html.classList.toggle('dark', isDark);
                const sw = document.getElementById('theme-switch');
                if (sw) sw.checked = isDark;
            }
            function onToggle(e){
                const isDark = !!e.target.checked;
                html.classList.toggle('dark', isDark);
                try { localStorage.setItem('theme', isDark ? 'dark':'light'); } catch(_){ }
            }
            function bindSwitch(){
                const sw = document.getElementById('theme-switch');
                if (!sw || sw.__themeBound) return;
                sw.addEventListener('change', onToggle); sw.__themeBound = true;
            }
            function initTheme(){ applyThemeFromStorage(); bindSwitch(); }
            document.addEventListener('DOMContentLoaded', initTheme);
            document.addEventListener('app:view-loaded', initTheme);
            window.addEventListener('storage', (e)=>{ if (e.key==='theme') applyThemeFromStorage(); });
        })();
    </script>

    @livewireScripts
    @stack('scripts')
</body>

</html>