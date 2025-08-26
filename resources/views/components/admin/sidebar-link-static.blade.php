@props([
    'href' => '#',
    'active' => false,
    'viewName' => '',
    'class' => ''
])

@php
    // Construir la URL real basada en el viewName
    $realHref = $href === '#' ? route('admin.' . $viewName) : $href;
    
    // No se aplica lógica de activo aquí (componente estático)
@endphp

<a href="{{ $realHref }}"
   @click.prevent="$store.navigation.navigate('{{ $realHref }}', '{{ $viewName }}')"
    {{ $attributes->merge([
        'class' =>
          'sidebar-link border border-blue-300 rounded-md p-5 flex items-center gap-2 transition-colors transition-shadow duration-300 ease-in-out hover:shadow-2xl hover:border-blue-600'
    ]) }}>
    {{ $slot }}
</a>
