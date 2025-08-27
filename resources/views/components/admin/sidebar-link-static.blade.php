@props([
    'href' => '#',
    'active' => false,
    'viewName' => '',
    'class' => ''
])

@php
    if (!empty($href) && $href !== '#') {
        $realHref = $href;
    } elseif (!empty($viewName)) {
        $routeName = 'admin.' . $viewName;
        try {
            if (\Illuminate\Support\Facades\Route::has($routeName)) {
                $realHref = route($routeName);
            } else {
                $realHref = '#';
            }
        } catch (\Throwable $e) {
            $realHref = '#';
        }
    } else {
        $realHref = '#';
    }

    $linkClass = trim('sidebar-link-static border border-blue-300 rounded-md p-5 flex items-center gap-2 transition-colors transition-shadow duration-300 ease-in-out hover:shadow-2xl hover:border-blue-600 ' . $class);
@endphp

<a href="{{ $realHref }}"
   @click.prevent="if ('{{ $realHref }}' !== '#') { $store.navigation.navigate('{{ $realHref }}', '{{ $viewName }}'); }"
   {{ $attributes->merge([
       'class' => $linkClass
   ]) }}>
    {{ $slot }}
</a>
