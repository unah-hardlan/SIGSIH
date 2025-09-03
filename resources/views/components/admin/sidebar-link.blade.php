@props([
    'href' => '#',
    'active' => false,
    'viewName'
])

@php
    // Construir la URL real basada en el viewName
    $realHref = $href === '#' ? route('admin.' . $viewName) : $href;
    
    // Determinar si este enlace está activo
    $isActive = $active || App\Helpers\SpaHelper::isActive($viewName);
@endphp

<a href="{{ $realHref }}"
    @click.prevent="$store.navigation.navigate('{{ $realHref }}', '{{ $viewName }}'); if ($store.navigation) { $store.navigation.currentView = '{{ $viewName }}' }"
    aria-current="{{ $isActive ? 'page' : 'false' }}"
    x-data="{ initialActive: {{ $isActive ? 'true' : 'false' }} }"
    :class="(($store.navigation && $store.navigation.currentView)
        ? $store.navigation.currentView === '{{ $viewName }}'
        : initialActive)
        ? 'bg-gray-800 text-blue-400 dark:bg-gray-700 dark:text-blue-400'
        : 'text-gray-300 hover:bg-gray-800 hover:text-blue-400 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-blue-400'"
    {{ $attributes->merge(['class' => 'sidebar-link group flex items-center gap-2 py-2 px-4 rounded transition-colors focus:outline-none']) }}>
   {{ $slot }}
</a>
