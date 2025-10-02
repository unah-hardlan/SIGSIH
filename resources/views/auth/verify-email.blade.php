<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite(['resources/css/theme.css', 'resources/css/global.css', 'resources/css/app.css'])
    <title>Verificación de correo – SIGSIH</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
    <script>
        (function(){
            try {
                const saved = localStorage.getItem('theme');
                const isDark = saved ? saved === 'dark' : (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', !!isDark);
            } catch (_) {}
        })();
    </script>
</head>
<body class="min-h-screen transition-colors duration-300 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-950">
        <div class="fixed top-4 right-4">
            <label @click.prevent="document.documentElement.classList.toggle('dark'); try{localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark':'light')}catch(_){}" class="switch cursor-pointer rounded-full border border-gray-400 dark:border-gray-500">
                <input type="checkbox" class="hidden" :checked="document.documentElement.classList.contains('dark')">
                <span class="slider"></span>
            </label>
        </div>

        <div class="w-full max-w-sm mx-auto">
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-500 dark:border-gray-600 p-4 transition-colors shadow-lg">
                <div class="text-center mb-4">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-2 bg-gray-100 dark:bg-white border-2 border-white dark:border-gray-500 transition-colors">
                        <img src="{{ $appLogoUrl ?? asset('images/logo.png') }}" alt="Logo" class="app-logo" style="--app-logo-max: {{ ($appLogoHeight ?? 96) }}px;">
                    </div>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 serif-boldy">{{ $title ?? 'Resultado de verificación' }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 nunito-regular">{{ $message ?? '' }}</p>
                </div>

                @php
                    $icon = 'fa-circle-info';
                    $bg = 'bg-blue-50 dark:bg-blue-900/30';
                    $border = 'border-blue-200 dark:border-blue-600';
                    $text = 'text-blue-700 dark:text-blue-200';
                    if (($status ?? '') === 'verified') { $icon = 'fa-circle-check'; $bg='bg-green-50 dark:bg-green-900/30'; $border='border-green-200 dark:border-green-600'; $text='text-green-700 dark:text-green-200'; }
                    elseif (($status ?? '') === 'already_verified') { $icon='fa-circle-check'; $bg='bg-emerald-50 dark:bg-emerald-900/30'; $border='border-emerald-200 dark:border-emerald-600'; $text='text-emerald-700 dark:text-emerald-200'; }
                    elseif (in_array(($status ?? ''), ['invalid', 'invalid_token'])) { $icon='fa-circle-exclamation'; $bg='bg-red-50 dark:bg-red-900/30'; $border='border-red-200 dark:border-red-600'; $text='text-red-700 dark:text-red-200'; }
                    elseif (($status ?? '') === 'not_found') { $icon='fa-user-xmark'; $bg='bg-amber-50 dark:bg-amber-900/30'; $border='border-amber-200 dark:border-amber-600'; $text='text-amber-700 dark:text-amber-200'; }
                @endphp

                <div class="mb-4 px-3 py-2 rounded border {{ $border }} {{ $bg }} {{ $text }} text-xs nunito-regular flex items-center gap-2">
                    <i class="fas {{ $icon }}"></i>
                    <span>{{ $message ?? '' }}</span>
                </div>

                <div class="flex gap-2">
                    @if(($status ?? '') === 'verified' || ($status ?? '') === 'already_verified')
                        <a href="{{ route('login') }}" class="flex-1 inline-flex items-center justify-center bg-blue-600 text-white py-2 rounded font-semibold hover:bg-blue-700 text-sm">Ir a iniciar sesión</a>
                    @else
                        <a href="{{ route('login') }}" class="flex-1 inline-flex items-center justify-center border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 py-2 rounded font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 text-sm">Volver al login</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
