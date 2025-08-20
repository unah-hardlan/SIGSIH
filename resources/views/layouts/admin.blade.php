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

    @vite(['resources/css/app.css', 'resources/css/global.css', 'resources/js/app.js', 'resources/js/sidebar.js', 'resources/js/session.js', 'resources/js/auth-guard.js', 'resources/js/toast.js'])

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">

    <script defer>
        document.addEventListener('alpine:init', () => {
            Alpine.store('perfil', {
                firstTime: false,
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @livewireStyles
</head>

<body class="bg-gray-900 min-h-screen flex flex-col" x-data="{ sidebarOpen: true }">
    <div class="flex h-screen min-h-0">
        @include('partials.admin-sidebar')

        <main class="flex-1 p-6 overflow-y-auto h-screen bg-white text-gray-900">
            @include('partials.admin-header')
            @hasSection('page-header')
            <div class="bg-white p-4 rounded shadow mb-6">
                @yield('page-header')
            </div>
            @endif

            <div class="bg-white p-6 rounded-lg shadow">
                @if(isset($partialView))
                @include($partialView)
                @else
                @yield('content')
                @endif
            </div>
        </main>
    </div>
    @livewireScripts
</body>

</html>