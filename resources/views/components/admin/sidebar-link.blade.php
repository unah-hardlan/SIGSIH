@props([
'href' => '#',
'active' => false,
'viewName'
])

@php
$realHref = $href === '#' ? route('admin.' . $viewName) : $href;

$isActive = $active || App\Helpers\SpaHelper::isActive($viewName);
@endphp

<a href="{{ $realHref }}"
    @click.prevent="(function(){ try { if (window.Alpine && Alpine.store && Alpine.store('perfil') && Alpine.store('perfil').firstTime && '{{ $viewName }}' !== 'perfil') { window.showToast && window.showToast('Debe completar su perfil antes de continuar.', 'warning'); return; } $store.navigation.navigate('{{ $realHref }}', '{{ $viewName }}'); if ($store.navigation) { $store.navigation.currentView = '{{ $viewName }}' } ; setTimeout(() => { if (window.innerWidth < 768) { const bodyEl = document.querySelector('body[x-data]'); if (bodyEl && bodyEl._x_dataStack && bodyEl._x_dataStack[0]) { bodyEl._x_dataStack[0].sidebarOpen = false; } } }, 50); } catch(_) { window.location.href='{{ $realHref }}'; } })()"
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