@props([
    'titulo' => '',
    'data' => [],
    'columns' => [],
    'mobileTemplate' => null,
    'searchable' => true,
    'sortable' => true
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow p-6 mb-8']) }}>
    <!-- Header -->
    <div class="sticky top-0 z-10 bg-white dark:bg-gray-900 pb-4 mb-4 border-b border-gray-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 w-full">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            {{ $titulo }}
        </h2>

        @isset($filtros)
        <div class="flex flex-col sm:flex-row gap-2 flex-1 md:ml-6">
            {{ $filtros }}
        </div>
        @endisset

        @isset($boton)
        <div>
            {{ $boton }}
        </div>
        @endisset
    </div>

    <!-- Desktop: Tabla normal -->
    <div class="hidden md:block overflow-x-auto">
        {{ $slot }}
    </div>

    <!-- Mobile: Vista de tarjetas -->
    <div class="block md:hidden">
        @if($mobileTemplate)
            {{ $mobileTemplate }}
        @else
            <div class="space-y-3">
                <!-- Aquí se renderizarían las tarjetas automáticamente -->
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-mobile-alt text-3xl mb-2"></i>
                    <p class="font-medium">Vista móvil optimizada</p>
                    <p class="text-sm">Use el slot 'mobileTemplate' para personalizar esta vista</p>
                </div>
            </div>
        @endif
    </div>
</div>
