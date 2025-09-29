<div {{ $attributes->merge(['class' => 'rounded-lg shadow p-6 mb-8 bg-transparent']) }}>
    <div
        class="top-0 z-10 pb-4 mb-4 border-b border-gray-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 w-full">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            {{ $titulo }}
        </h2>

        <div class="flex flex-col sm:flex-row gap-2 flex-1 md:ml-6">
            {{ $filtros }}
        </div>

        @isset($boton)
        <div>
            {{ $boton }}
        </div>
        @endisset
    </div>

    <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        {{ $slot }}
    </div>
</div>