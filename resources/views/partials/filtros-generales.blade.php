{{-- resources/views/partials/filtros-generales.blade.php --}}
@props([
    'searchModel' => 'search',
    'filtrosSelect' => [],
    'ordenarOptions' => [],
    'ordenarModel' => 'ordenarPor',
    'ordenarDirectionModel' => null, // Prop opcional para el modelo de dirección (asc/desc)
])

<div class="flex flex-col sm:flex-row flex-wrap gap-4 w-full items-center">
    
    <!-- Búsqueda General -->
    <div class="relative w-full sm:w-auto sm:flex-grow">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fas fa-search text-gray-400"></i>
        </div>
        <input type="text"
               x-model.debounce.500ms="{{ $searchModel }}"
               placeholder="Buscar..."
               class="border border-gray-500 rounded px-3 py-2 pl-10 w-full text-sm font-semibold nunito-bold dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
    </div>

    <!-- Filtros Select Personalizados (si se proporcionan) -->
    @foreach ($filtrosSelect as $variable => $data)
        <select x-model="{{ $variable }}" class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 md:w-64 sm:min-w-[14rem] md:min-w-[16rem] shrink-0 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
            <option value="">{{ 'Todos los ' . strtolower($data['label']) }}</option>
            @foreach ($data['options'] as $opcion)
                <option value="{{ $opcion }}">{{ $opcion }}</option>
            @endforeach
        </select>
    @endforeach

    <!-- Ordenamiento -->
    @if (count($ordenarOptions))
        <div class="flex items-center gap-2 w-full sm:w-auto">
            
            <select x-model="{{ $ordenarModel }}" class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200 flex-grow">
                @foreach ($ordenarOptions as $valor => $texto)
                    <option value="{{ $valor }}">Ordenar por {{ $texto }}</option>
                @endforeach
            </select>
            
            {{-- Botón para cambiar la dirección del ordenamiento --}}
            @if(isset($ordenarDirectionModel))
                <button
                    @click="{{ $ordenarDirectionModel }} = ({{ $ordenarDirectionModel }} === 'asc' ? 'desc' : 'asc')"
                    x-show="{{ $ordenarModel }}"
                    title="Cambiar dirección de ordenamiento"
                    class="px-3 py-2 border border-gray-500 rounded text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition shrink-0">
                    <i class="fas" :class="{ 'fa-sort-up': {{ $ordenarDirectionModel }} === 'asc', 'fa-sort-down': {{ $ordenarDirectionModel }} === 'desc' }"></i>
                </button>
            @endif
            
        </div>
    @endif
</div>