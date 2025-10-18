@props([
'searchModel' => 'search',
'filtrosSelect' => [], // clave => ['label' => 'Texto bonito', 'options' => [...] ]
'ordenarOptions' => [], // ['campo' => 'Nombre bonito']
'ordenarModel' => 'ordenarPor', // permite usar un modelo distinto para ordenar
])

<input type="text" x-model="{{ $searchModel }}" placeholder="Buscar..."
    class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-48 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200" />

@foreach ($filtrosSelect as $variable => $data)
<select x-model="{{ $variable }}" class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 md:w-64 sm:min-w-[14rem] md:min-w-[16rem] shrink-0 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
    <option value="">{{ 'Todos los ' . strtolower($data['label']) }}</option>
    @foreach ($data['options'] as $opcion)
    <option value="{{ $opcion }}">{{ $opcion }}</option>
    @endforeach
</select>
@endforeach

@if (count($ordenarOptions))
<select x-model="{{ $ordenarModel }}" class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 md:w-64 sm:min-w-[14rem] md:min-w-[16rem] shrink-0 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
    @foreach ($ordenarOptions as $valor => $texto)
    <option value="{{ $valor }}">Ordenar por {{ $texto }}</option>
    @endforeach
</select>
@endif