<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite(['resources/css/global.css', 'resources/css/app.css'])
    <title>Verificación de correo – SIGSIH</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="min-h-screen bg-[#171C25] text-gray-100">
    <script>
    (function() {
        try {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } catch (_) {}
    })();
    </script>
    @if(($status ?? '') === 'verified' || ($status ?? '') === 'already_verified')
    <script>
        try { localStorage.setItem('email_verified', String(Date.now())); } catch(_) {}
    </script>
    @endif
    <div class="min-h-screen flex flex-col items-center justify-center p-4 bg-[#171C25]">

        <div class="w-full max-w-md mx-auto">
            <div class="bg-gray-900 rounded-xl border border-gray-600 p-6 shadow-xl">
                <div class="text-center mb-5">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full mb-3 bg-white border-2 border-gray-500">
                        <img src="{{ $appLogoUrl ?? asset('images/logo.png') }}" alt="Logo" class="app-logo" style="--app-logo-max: {{ ($appLogoHeight ?? 110) }}px;">
                    </div>
                    <h2 class="text-lg font-bold text-gray-100 serif-boldy">{{ $title ?? 'Resultado de verificación' }}</h2>
                    <p class="text-sm text-gray-300 mt-1 nunito-regular">{{ $message ?? '' }}</p>
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

                @php
                    $icon = 'fa-circle-info';
                    $bg = 'bg-blue-900';
                    $border = 'border-blue-700';
                    $text = 'text-blue-200';
                    if (($status ?? '') === 'verified') { $icon = 'fa-circle-check'; $bg='bg-green-900'; $border='border-green-700'; $text='text-green-200'; }
                    elseif (($status ?? '') === 'already_verified') { $icon='fa-circle-check'; $bg='bg-emerald-900'; $border='border-emerald-700'; $text='text-emerald-200'; }
                    elseif (in_array(($status ?? ''), ['invalid', 'invalid_token'])) { $icon='fa-circle-exclamation'; $bg='bg-red-900'; $border='border-red-700'; $text='text-red-200'; }
                    elseif (($status ?? '') === 'not_found') { $icon='fa-user-xmark'; $bg='bg-amber-900'; $border='border-amber-700'; $text='text-amber-200'; }
                @endphp

                <div class="mb-4 px-3 py-2 rounded border {{ $border }} {{ $bg }} {{ $text }} text-xs nunito-regular flex items-center gap-2">
                    <i class="fas {{ $icon }}"></i>
                    <span>{{ $message ?? '' }}</span>
                </div>
                @if(($status ?? '') === 'verified' || ($status ?? '') === 'already_verified')
                <div id="autoclose-msg" class="mb-4 px-3 py-2 rounded border border-blue-700 bg-blue-900 text-blue-200 text-xs nunito-regular flex items-center gap-2 justify-center">
                    <i class="fas fa-clock"></i>
                    <span>Esta ventana se cerrará en <span id="autoclose-count">5</span> segundos...</span>
                </div>
                <script>
                (function() {
                    let count = 5;
                    const el = document.getElementById('autoclose-count');
                    const msg = document.getElementById('autoclose-msg');
                    const timer = setInterval(() => {
                        count--;
                        if (el) el.textContent = count;
                        if (count <= 0) {
                            clearInterval(timer);
                            if (msg) msg.textContent = 'Cerrando...';
                            setTimeout(() => {
                                let closed = false;
                                try { window.close(); closed = true; } catch(_){}
                                if(!closed){ try{ const w=window.open('','_self'); w&&w.close&&w.close(); closed=true; }catch(_){} }
                                if(!closed){ try{ window.top&&window.top.close&&window.top.close(); closed=true; }catch(_){} }
                                if(!closed){ try{ window.showToast&&window.showToast('No se puede cerrar automáticamente. Cierra esta pestaña.', 'info', {duration:2500}); }catch(_){} }
                            }, 400);
                        }
                    }, 1000);
                })();
                </script>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
