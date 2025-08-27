@props([
    'href' => null,
    'viewName' => null,
    'class' => ''
])

@php
    $baseClasses = 'w-full text-left px-4 py-2 text-sm nunito-regular text-gray-700 hover:bg-blue-300 hover:text-gray-800 transition-colors duration-200 flex items-center gap-2 ' . $class;
@endphp

@php
    $realHref = $href ?? ($viewName ? route('admin.' . $viewName) : '#');
@endphp

@if($viewName)
    <a href="{{ $realHref }}" x-on:click.prevent="$store.navigation.navigate('{{ $realHref }}', '{{ $viewName }}'); $dispatch('header-link-click')" {{ $attributes->merge(['class' => $baseClasses]) }}>
        {{ $slot }}
    </a>
@elseif($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $baseClasses]) }}>
        {{ $slot }}
    </a>
@else
    <button type="button" {{ $attributes->merge(['class' => $baseClasses]) }}>
        {{ $slot }}
    </button>
@endif

