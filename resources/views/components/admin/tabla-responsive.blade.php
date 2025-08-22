@props([
    'titulo' => '',
    'headers' => [],
    'data' => [],
    'actions' => null,
    'cardTemplate' => null
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow p-6 mb-8']) }}>
    <!-- Header con título y filtros -->
    <div class="sticky top-0 z-10 bg-white pb-4 mb-4 border-b border-gray-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 w-full">
        <h2 class="text-2xl font-bold text-gray-800">
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

    <!-- Vista Desktop: Tabla normal -->
    <div class="hidden md:block">
        <div class="overflow-x-auto">
            {{ $slot }}
        </div>
    </div>

    <!-- Vista Móvil: Cards -->
    <div class="block md:hidden">
        @if($cardTemplate)
            {{ $cardTemplate }}
        @else
            <div class="space-y-4">
                @if(is_array($data) && count($data) > 0)
                    @foreach($data as $item)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            @foreach($headers as $key => $header)
                                @if(is_array($item) && isset($item[$key]))
                                    <div class="flex justify-between items-center py-1 border-b border-gray-200 last:border-b-0">
                                        <span class="text-sm font-medium text-gray-600">{{ $header }}:</span>
                                        <span class="text-sm text-gray-900">{{ $item[$key] }}</span>
                                    </div>
                                @elseif(is_object($item) && property_exists($item, $key))
                                    <div class="flex justify-between items-center py-1 border-b border-gray-200 last:border-b-0">
                                        <span class="text-sm font-medium text-gray-600">{{ $header }}:</span>
                                        <span class="text-sm text-gray-900">{{ $item->$key }}</span>
                                    </div>
                                @endif
                            @endforeach
                            
                            @if($actions)
                                <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200">
                                    {!! is_callable($actions) ? $actions($item) : $actions !!}
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-3xl mb-2"></i>
                        <p>No hay datos disponibles</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
