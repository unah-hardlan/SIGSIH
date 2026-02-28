<!doctype html>
<html lang="es" class="overflow-y-scroll bg-gray-50 dark:bg-gray-900">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hardlan - Cliente')</title>

    @if(request()->header('X-SPA-Page'))
    <meta name="spa-page" content="true">
    <meta name="spa-view" content="{{ request()->header('X-SPA-View') }}">
    @endif

    @vite([
    'resources/css/app.css',
    'resources/css/global.css',
    'resources/css/theme.css',
    'resources/js/cliente.js',
    'resources/js/sidebar-cliente.js',
    'resources/js/session.js',
    'resources/js/toast.js',
    'resources/js/tabla-responsive.js',
    'resources/js/spa-cliente.js',
    ])

    <script>
        (function() {
            try {
                const saved = localStorage.getItem('theme');
                const isDark = saved ? saved === 'dark' : (window.matchMedia && window.matchMedia(
                    '(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', !!isDark);
            } catch (_) {}
        })();
    </script>

    <link href="https://fonts.googleapis.com/css2?family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <script type="application/json" id="cliente-auth-bootstrap">
        @json(['user' => $authUser ?? null, 'persona' => $authPersona ?? null])
    </script>
    <script defer>
        document.addEventListener('alpine:init', () => {
            let initial = {
                user: null,
                persona: null
            };
            try {
                const el = document.getElementById('cliente-auth-bootstrap');
                if (el && el.textContent) initial = JSON.parse(el.textContent);
            } catch (_) {}
            Alpine.store('clientePerfil', {
                user: initial.user,
                persona: initial.persona,
            });
            Alpine.store('clienteLogout', {
                modalOpen: false
            });
        });
    </script>

    @livewireStyles
    @stack('styles')
    <style>
    .client-sidebar { z-index: 9999; }
    .modal-underlay { z-index: 10000; }
    body.sidebar-on-top .modal-underlay { z-index: 9998 !important; }
    body.sidebar-on-top .client-sidebar { z-index: 10005 !important; }

    .sidebar-backdrop { z-index: 10000; }
    body.sidebar-on-top .sidebar-backdrop { z-index: 10002 !important; }

    body.sidebar-on-top .client-sidebar { background-color: rgba(243,244,246,0.98); }
    html.dark body.sidebar-on-top .client-sidebar { background-color: rgba(15,23,42,0.95); }

    .client-sidebar { transition: background-color 160ms ease, filter 160ms ease; }

    body.sidebar-on-top .site-main { filter: blur(6px); -webkit-filter: blur(6px); }
    body.sidebar-on-top .client-sidebar { border-radius: 0 !important; }
    </style>
</head>

<div id="spa-loading-overlay" class="modal-underlay hidden fixed inset-0 z-[9999] items-center justify-center bg-gray-200/60 dark:bg-gray-900/60 backdrop-blur-sm">
    <div class="w-16 h-16 border-4 border-gray-300 dark:border-gray-600 border-t-blue-500 dark:border-t-blue-400 rounded-full animate-spin"></div>
</div>
<body :class="(isMobile && sidebarOpen) ? 'sidebar-on-top overflow-hidden' : ''" class="font-sans bg-gray-50 dark:bg-gray-900 min-h-screen flex flex-col"
    x-data="{sidebarOpen:false,isMobile:window.innerWidth<768}"
    x-init="initResponsiveSidebar && initResponsiveSidebar($data); sidebarOpen=!isMobile"
    @closemobilesidebar.window="if(isMobile){sidebarOpen=false}">
        <div class="flex min-h-screen relative bg-gray-50 dark:bg-gray-900">
        <div x-show="sidebarOpen && isMobile"
            @click="sidebarOpen = false"
            class="sidebar-backdrop fixed inset-0 bg-black/20 backdrop-blur-sm md:hidden"
            x-cloak></div>

        @include('cliente.partials.sidebar')

        <main class="site-main flex-1 min-h-screen p-3 sm:p-6 text-gray-900 dark:text-white dark:bg-gray-900">
            @include('cliente.partials.header')
            @hasSection('page-header')
            <div class="bg-white dark:bg-gray-900 p-4 rounded mb-6">
                @yield('page-header')
            </div>
            @endif

            <div class="p-3 sm:p-6 rounded-lg">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        (function() {
            const html = document.documentElement;

            function applyThemeFromStorage() {
                const isDark = html.classList.contains('dark');
                html.classList.toggle('dark', isDark);
                const sw = document.getElementById('theme-switch');
                if (sw) sw.checked = isDark;
            }

            function onToggle(e) {
                const isDark = !!e.target.checked;
                html.classList.toggle('dark', isDark);
                try {
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                } catch (_) {}
            }

            function bindSwitch() {
                const sw = document.getElementById('theme-switch');
                if (!sw || sw.__themeBound) return;
                sw.addEventListener('change', onToggle);
                sw.__themeBound = true;
            }

            function initTheme() {
                applyThemeFromStorage();
                bindSwitch();
            }
            document.addEventListener('DOMContentLoaded', initTheme);
            document.addEventListener('app:view-loaded', initTheme);
            window.addEventListener('storage', (e) => {
                if (e.key === 'theme') applyThemeFromStorage();
            });
        })();
    </script>

    @livewireScripts
    @stack('scripts')

    {{-- Idle logout config for client area --}}
    @php
    $idleMinutes = (int) config('session.lifetime', 120);
    $warnSeconds = (int) (env('WARN_BEFORE_LOGOUT_SECONDS', 30));
    @endphp
    <script type="application/json" id="idle-logout-config">
        @json(['minutes' => $idleMinutes, 'warnSeconds' => $warnSeconds])
    </script>
</body>

</html>