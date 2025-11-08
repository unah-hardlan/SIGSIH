<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite(['resources/css/global.css', 'resources/css/app.css'])
    <title>Verificación de correo – SIGSIH</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="min-h-screen bg-gray-50 text-gray-800">
    @if(($status ?? '') === 'verified' || ($status ?? '') === 'already_verified')
    <script>
        try { localStorage.setItem('email_verified', String(Date.now())); } catch(_) {}
    </script>
    @endif
    <div class="min-h-screen flex flex-col items-center justify-center p-4 bg-gray-50">

        <div class="w-full max-w-sm mx-auto">
            <div class="bg-white rounded-lg border border-gray-300 p-4 shadow-lg">
                <div class="text-center mb-4">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-2 bg-gray-100 border-2 border-white">
                        <img src="{{ $appLogoUrl ?? asset('images/logo.png') }}" alt="Logo" class="app-logo" style="--app-logo-max: {{ ($appLogoHeight ?? 96) }}px;">
                    </div>
                    <h2 class="text-lg font-bold text-gray-800 serif-boldy">{{ $title ?? 'Resultado de verificación' }}</h2>
                    <p class="text-sm text-gray-600 mt-1 nunito-regular">{{ $message ?? '' }}</p>
                </div>

                @php
                    $icon = 'fa-circle-info';
                    $bg = 'bg-blue-50';
                    $border = 'border-blue-200';
                    $text = 'text-blue-700';
                    if (($status ?? '') === 'verified') { $icon = 'fa-circle-check'; $bg='bg-green-50'; $border='border-green-200'; $text='text-green-700'; }
                    elseif (($status ?? '') === 'already_verified') { $icon='fa-circle-check'; $bg='bg-emerald-50'; $border='border-emerald-200'; $text='text-emerald-700'; }
                    elseif (in_array(($status ?? ''), ['invalid', 'invalid_token'])) { $icon='fa-circle-exclamation'; $bg='bg-red-50'; $border='border-red-200'; $text='text-red-700'; }
                    elseif (($status ?? '') === 'not_found') { $icon='fa-user-xmark'; $bg='bg-amber-50'; $border='border-amber-200'; $text='text-amber-700'; }
                @endphp

                <div class="mb-4 px-3 py-2 rounded border {{ $border }} {{ $bg }} {{ $text }} text-xs nunito-regular flex items-center gap-2">
                    <i class="fas {{ $icon }}"></i>
                    <span>{{ $message ?? '' }}</span>
                </div>

                <div class="flex gap-2">
                    @if(($status ?? '') === 'verified' || ($status ?? '') === 'already_verified')
                        <a href="{{ route('login') }}" class="flex-1 inline-flex items-center justify-center bg-blue-600 text-white py-2 rounded font-semibold hover:bg-blue-700 text-sm">Ir a iniciar sesión</a>
                    @else
                        <a href="{{ route('login') }}" class="flex-1 inline-flex items-center justify-center border border-gray-300 text-gray-700 py-2 rounded font-semibold hover:bg-gray-50 text-sm">Volver al login</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
