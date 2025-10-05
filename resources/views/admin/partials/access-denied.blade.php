@php
    $code = $code ?? 403;
    $title = $title ?? __('Acceso restringido');
    $message = $message ?? __('No cuentas con los permisos necesarios para acceder a esta sección.');
    $targetLabel = $targetLabel ?? null;
    $helpText = $helpText ?? __('Comunícate con un administrador si consideras que deberías tener acceso.');
    $actionUrl = $actionUrl ?? route('admin.dashboard');
    $actionText = $actionText ?? __('Ir al panel principal');
@endphp

<div class="flex flex-col items-center justify-center py-16 text-center space-y-6">
    <div class="flex items-center justify-center w-24 h-24 rounded-full bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-200 shadow-inner text-3xl font-semibold">
        {{ $code }}
    </div>

    <div class="space-y-3 max-w-2xl">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
            {{ $title }}
        </h1>
        <p class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed">
            {{ $message }}@if($targetLabel)
                <span class="font-semibold text-gray-900 dark:text-gray-100"> {{ $targetLabel }}</span>.
            @endif
        </p>
        <p class="text-gray-500 dark:text-gray-400">
            {{ $helpText }}
        </p>
    </div>

    <div class="flex flex-wrap items-center justify-center gap-4">
        <a href="{{ $actionUrl }}"
           class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900 transition">
            <i class="fas fa-home mr-2"></i>
            {{ $actionText }}
        </a>
    </div>
</div>
