<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $appName ?? 'SIGSIH' }}</title>

    {{-- SPA Meta Tags --}}
    @if(request()->header('X-SPA-Page'))
    <meta name="spa-page" content="true">
    <meta name="spa-view" content="{{ request()->header('X-SPA-View') }}">
    @endif

    @vite(['resources/css/app.css', 'resources/css/global.css', 'resources/css/theme.css', 'resources/js/app.js',
    'resources/js/sidebar.js', 'resources/js/session.js', 'resources/js/auth-guard.js', 'resources/js/toast.js',
    'resources/js/tabla-responsive.js'])

    <link href="https://fonts.googleapis.com/css2?family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <script type="application/json" id="auth-bootstrap">
        @json(['firstTime' => $authFirstTime ?? false, 'user' => $authUser ?? null, 'persona' => $authPersona ?? null])
    </script>
    <script defer>
        document.addEventListener('alpine:init', () => {
            let initial = {
                firstTime: false,
                user: null,
                persona: null
            };
            try {
                const el = document.getElementById('auth-bootstrap');
                if (el && el.textContent) initial = JSON.parse(el.textContent);
            } catch (_) {
                /* noop */
            }
            Alpine.store('perfil', {
                firstTime: !!initial.firstTime,
                user: initial.user || null,
                persona: initial.persona || null,
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @livewireStyles
</head>

<body class="bg-gray-50 dark:bg-gray-900 min-h-screen flex flex-col" x-data="{ 
          sidebarOpen: false, 
          isMobile: window.innerWidth < 768 
      }" x-init="initResponsiveSidebar($data)">
    <div class="flex min-h-screen relative">
        <!-- Overlay para móviles SOLO -->
        <div x-show="sidebarOpen && isMobile" x-cloak x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50"
            style="z-index: 9998;">
        </div>

        @include('partials.admin-sidebar')

        <main
            class="flex-1 min-h-screen p-3 sm:p-6 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
            @include('partials.admin-header')
            @hasSection('page-header')
            <div class="bg-white dark:bg-gray-900 p-4 rounded shadow mb-6">
                @yield('page-header')
            </div>
            @endif

            <div class="bg-white dark:bg-gray-900 p-3 sm:p-6 rounded-lg shadow">
                @if(isset($partialView))
                @include($partialView)
                @else
                @yield('content')
                @endif
            </div>
        </main>
    </div>

    <script>
        (function() {
            const html = document.documentElement;

            function applyThemeFromStorage() {
                const saved = localStorage.getItem('theme');
                const isDark = saved === 'dark';
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
                if (!sw) return;
                // Evitar múltiples bindings al reinsertar el header
                if (sw.__themeBound) return;
                sw.addEventListener('change', onToggle);
                sw.__themeBound = true;
            }

            function initTheme() {
                applyThemeFromStorage();
                bindSwitch();
            }

            document.addEventListener('DOMContentLoaded', initTheme);
            // Re-vincular después de navegación SPA
            document.addEventListener('app:view-loaded', initTheme);
            // Sincronizar entre pestañas
            window.addEventListener('storage', (e) => {
                if (e.key === 'theme') applyThemeFromStorage();
            });
        })();
    </script>

    @livewireScripts
</body>

</html>