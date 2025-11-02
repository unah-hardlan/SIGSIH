<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Configurar Perfil - SIGSIH' }}</title>

    @vite([
        'resources/css/app.css',
        'resources/css/global.css',
        'resources/css/theme.css'
    ])

    <script>
        (function() {
            try {
                const saved = localStorage.getItem('theme');
                const isDark = saved ? saved === 'dark' : (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', !!isDark);
            } catch (_) {}
        })();
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .dark {
            color-scheme: dark;
        }
        
        .smooth-transition {
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }
        
        input, select, textarea {
            transition: border-color 0.2s ease-in-out !important;
        }
        
        input:focus, select:focus, textarea:focus {
            transition: border-color 0.15s ease-in-out !important;
        }
        
        .hover-scale:hover {
            transform: scale(1.02);
        }
        
        .backdrop-blur-lg {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        
        input:focus, select:focus, textarea:focus {
            box-shadow: none !important;
        }
        
        .theme-toggle {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .theme-toggle:hover {
            transform: scale(1.05);
        }
        
        .theme-toggle:active {
            transform: scale(0.95);
        }
        
        .theme-toggle svg {
            transition: opacity 0.2s ease-in-out;
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-cyan-50 dark:from-gray-900 dark:via-gray-800 dark:to-indigo-900">
    <div class="min-h-screen flex items-center justify-center p-4">
        @yield('content')
    </div>

</body>

</html>