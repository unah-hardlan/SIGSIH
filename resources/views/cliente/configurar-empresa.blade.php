@extends('cliente.layouts.standalone')
@section('content')
<div id="toast-container" class="fixed top-4 left-4 z-50 space-y-3 max-w-md"></div>

<div class="fixed top-4 right-4 z-50">
    <button 
        onclick="toggleTheme()" 
        class="theme-toggle inline-flex items-center justify-center w-12 h-12 rounded-full backdrop-blur-lg shadow-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white"
        title="Cambiar tema"
    >
        <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
        </svg>
        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
    </button>
</div>

<div class="w-full max-w-4xl mx-auto">
    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg shadow-2xl rounded-3xl border border-gray-400/60 dark:border-gray-700/20 overflow-hidden">
        <div class="bg-gradient-to-r from-green-700 to-green-800 p-6 text-center">
            <div class="w-20 h-20 mx-auto mb-3 bg-white rounded-full flex items-center justify-center shadow-lg">
                <div class="w-15 h-15 rounded-full overflow-hidden bg-white flex items-center justify-center p-1">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SIGSIH" class="w-full h-full object-contain">
                </div>
            </div>
            <h1 class="text-2xl font-bold text-white mb-2 font-nunito">Datos de Empresa</h1>
            <p class="text-base text-green-100 font-nunito">Completa la información corporativa de tu empresa</p>
        </div>

        <div class="p-6">
            <form action="{{ route('cliente.configurar-empresa.store') }}" method="POST" id="empresa-form" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="text-center mb-6">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3 font-nunito">Logo de la Empresa</h3>
                    <div class="flex flex-col items-center">
                        <div class="relative mb-3">
                            <div class="w-24 h-24 rounded-full border-3 border-gray-300 dark:border-gray-600 overflow-hidden bg-gray-100 dark:bg-gray-700">
                                <img id="logo-preview" class="w-full h-full object-cover hidden" alt="Preview">
                                <div id="logo-placeholder" class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div id="logo-drop-zone" class="w-full max-w-xs border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center cursor-pointer hover:border-green-500 dark:hover:border-green-400 hover:bg-green-50 dark:hover:bg-green-900/10 transition-all duration-300 ease-in-out">
                            <input type="file" id="avatar" name="avatar" data-validate="avatar" accept="image/jpeg,image/jpg,image/png,image/webp" class="hidden" onchange="previewLogo(this)">
                            <label for="avatar" class="cursor-pointer">
                                <svg class="w-6 h-6 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="text-xs text-gray-600 dark:text-gray-400 font-nunito">
                                    <span class="font-medium text-green-600 dark:text-green-400">Clic para subir</span> o arrastra
                                </p>
                                <p class="text-xs text-gray-500 dark:text-white font-nunito">PNG, JPG, WEBP (5MB máx)</p>
                            </label>
                        </div>
                        
                        @error('avatar')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="nombre_comercial" class="block text-base font-bold text-gray-700 dark:text-gray-300 font-nunito">
                            Nombre Comercial <span class="text-red-500">*</span>
                        </label>
                        <input 
                            id="nombre_comercial" 
                            name="nombre_comercial" 
                            type="text" 
                            required 
                            data-validate="name"
                            class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200 text-sm font-nunito"
                            placeholder="Nombre comercial de la empresa"
                            value="{{ old('nombre_comercial') }}"
                        >
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1 hidden" data-client-error-for="nombre_comercial"></p>
                        @error('nombre_comercial')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="razon_social" class="block text-base font-bold text-gray-700 dark:text-gray-300 font-nunito">
                            Razón Social <span class="text-red-500">*</span>
                        </label>
                        <input 
                            id="razon_social" 
                            name="razon_social" 
                            type="text" 
                            required
                            data-validate="name"
                            class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200 text-sm font-nunito"
                            placeholder="Razón social legal"
                            value="{{ old('razon_social') }}"
                        >
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1 hidden" data-client-error-for="razon_social"></p>
                        @error('razon_social')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="rtn" class="block text-base font-bold text-gray-700 dark:text-gray-300 font-nunito">
                            Identificación Fiscal (RTN / NIT / RUC / Cédula Jurídica) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            id="rtn" 
                            name="rtn" 
                            type="text" 
                            required
                            data-validate="rtn"
                            class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200 text-sm font-nunito"
                            placeholder="Registro Tributario Nacional"
                            value="{{ old('rtn') }}"
                        >
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1 hidden" data-client-error-for="rtn"></p>
                        @error('rtn')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2 space-y-1">
                        <label class="block text-base font-bold text-gray-700 dark:text-gray-300 font-nunito">
                            Horario de Atención <span class="text-red-500">*</span>
                        </label>

                        <div class="space-y-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white/80 dark:bg-gray-800/60 px-4 py-4">
                            <input type="hidden" name="horario_atencion" id="horario_atencion" value="{{ old('horario_atencion') }}">

                            <div class="space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 font-nunito">Días</p>
                                <div class="flex flex-wrap gap-2">
                                    @php
                                        $dias = [
                                            ['code' => 'L', 'label' => 'Lun'],
                                            ['code' => 'M', 'label' => 'Mar'],
                                            ['code' => 'X', 'label' => 'Mié'],
                                            ['code' => 'J', 'label' => 'Jue'],
                                            ['code' => 'V', 'label' => 'Vie'],
                                            ['code' => 'S', 'label' => 'Sáb'],
                                            ['code' => 'D', 'label' => 'Dom'],
                                        ];
                                    @endphp
                                    @foreach($dias as $dia)
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="hidden peer" data-day-checkbox value="{{ $dia['code'] }}">
                                            <span class="select-none rounded-lg border-2 border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 transition-all duration-150 hover:border-gray-400 cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:border-gray-500 dark:peer-checked:border-green-400 dark:peer-checked:bg-green-900/40 dark:peer-checked:text-green-300 font-nunito">
                                                {{ $dia['label'] }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" class="horario-preset inline-flex items-center justify-center rounded-lg border border-gray-300 bg-gray-50 px-3 py-1 text-xs font-medium text-gray-600 transition-all duration-150 hover:border-green-500 hover:bg-green-50 hover:text-green-600 focus:outline-none dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-400 dark:hover:border-green-400 dark:hover:bg-green-900/20 dark:hover:text-green-300 font-nunito" data-preset="weekdays">Lunes a Viernes</button>
                                    <button type="button" class="horario-preset inline-flex items-center justify-center rounded-lg border border-gray-300 bg-gray-50 px-3 py-1 text-xs font-medium text-gray-600 transition-all duration-150 hover:border-green-500 hover:bg-green-50 hover:text-green-600 focus:outline-none dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-400 dark:hover:border-green-400 dark:hover:bg-green-900/20 dark:hover:text-green-300 font-nunito" data-preset="weekends">Lunes a Sábado</button>
                                    <button type="button" class="horario-preset inline-flex items-center justify-center rounded-lg border border-gray-300 bg-gray-50 px-3 py-1 text-xs font-medium text-gray-600 transition-all duration-150 hover:border-green-500 hover:bg-green-50 hover:text-green-600 focus:outline-none dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-400 dark:hover:border-green-400 dark:hover:bg-green-900/20 dark:hover:text-green-300 font-nunito" data-preset="all">Todos los días</button>
                                    <button type="button" class="horario-preset inline-flex items-center justify-center rounded-lg border border-gray-300 bg-gray-50 px-3 py-1 text-xs font-medium text-gray-600 transition-all duration-150 hover:border-green-500 hover:bg-green-50 hover:text-green-600 focus:outline-none dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-400 dark:hover:border-green-400 dark:hover:bg-green-900/20 dark:hover:text-green-300 font-nunito" data-preset="none">Limpiar</button>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 font-nunito">Horario</p>
                                <div class="flex flex-wrap gap-3 items-center">
                                    <div class="flex items-center gap-2">
                                        <label class="text-sm text-gray-600 dark:text-gray-400 font-nunito">De:</label>
                                        <input 
                                            type="time" 
                                            id="horario_inicio" 
                                            class="px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200 text-sm font-nunito"
                                            value="08:00"
                                        >
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label class="text-sm text-gray-600 dark:text-gray-400 font-nunito">A:</label>
                                        <input 
                                            type="time" 
                                            id="horario_fin" 
                                            class="px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200 text-sm font-nunito"
                                            value="16:00"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1 font-nunito">Vista previa:</p>
                                <p id="horario-preview" class="text-sm text-gray-700 dark:text-gray-300 font-medium font-nunito">—</p>
                            </div>
                        </div>
                        
                        @error('horario_atencion')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="descripcion_empresa" class="block text-base font-bold text-gray-700 dark:text-gray-300 font-nunito">
                        Descripción de la Empresa <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="descripcion_empresa" 
                        name="descripcion_empresa" 
                        rows="4"
                        required
                        maxlength="500"
                        class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200 resize-y min-h-[100px] max-h-[300px] text-sm font-nunito"
                        placeholder="Describe brevemente tu empresa y sus servicios (máximo 500 caracteres)"
                    >{{ old('descripcion_empresa') }}</textarea>
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1 hidden" data-client-error-for="descripcion_empresa"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-nunito">
                            <span id="descripcion_count">0</span>/500 caracteres
                        </p>
                    </div>
                    @error('descripcion_empresa')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-3">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white font-nunito">
                        Ubicación de la Empresa
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <label for="pais_id" class="block text-base font-bold text-gray-700 dark:text-gray-300 font-nunito">
                                País <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="pais_id" 
                                name="id_pais_fk" 
                                required
                                class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200 text-sm font-nunito"
                                data-old-value="{{ old('id_pais_fk') }}"
                            >
                                <option value="">Seleccionar país</option>
                                @if(isset($paises))
                                    @foreach($paises as $pais)
                                        <option value="{{ $pais->id_pais_pk }}" {{ old('id_pais_fk') == $pais->id_pais_pk ? 'selected' : '' }}>
                                            {{ $pais->nombre_pais }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <p class="text-sm text-red-600 dark:text-red-400 mt-1 hidden" data-client-error-for="id_pais_fk"></p>
                            @error('id_pais_fk')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="departamento_id" class="block text-base font-bold text-gray-700 dark:text-gray-300 font-nunito">
                                Departamento <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="departamento_id" 
                                name="id_departamento_fk" 
                                required
                                disabled
                                class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200 disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:text-gray-500 dark:disabled:text-gray-400 text-sm font-nunito"
                                data-old-value="{{ old('id_departamento_fk') }}"
                            >
                                <option value="">Seleccionar departamento</option>
                            </select>
                            <p class="text-sm text-red-600 dark:text-red-400 mt-1 hidden" data-client-error-for="id_departamento_fk"></p>
                            @error('id_departamento_fk')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="ciudad_id" class="block text-base font-bold text-gray-700 dark:text-gray-300 font-nunito">
                                Ciudad <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="ciudad_id" 
                                name="id_ciudad_fk" 
                                required
                                disabled
                                class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200 disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:text-gray-500 dark:disabled:text-gray-400 text-sm font-nunito"
                                data-old-value="{{ old('id_ciudad_fk') }}"
                            >
                                <option value="">Seleccionar ciudad</option>
                            </select>
                            <p class="text-sm text-red-600 dark:text-red-400 mt-1 hidden" data-client-error-for="id_ciudad_fk"></p>
                            @error('id_ciudad_fk')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white font-nunito">
                        Dirección
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1 md:col-span-2">
                            <label for="calle" class="block text-base font-bold text-gray-700 dark:text-gray-300 font-nunito">
                                Calle <span class="text-red-500">*</span>
                            </label>
                            <input 
                                id="calle" 
                                name="calle" 
                                type="text" 
                                required
                                maxlength="100"
                                class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200 text-sm font-nunito"
                                placeholder="Ej. Avenida Principal, Blvd. Morazán"
                                value="{{ old('calle') }}"
                            >
                            @error('calle')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="numero" class="block text-base font-bold text-gray-700 dark:text-gray-300 font-nunito">
                                Número <span class="text-red-500">*</span>
                            </label>
                            <input 
                                id="numero" 
                                name="numero" 
                                type="text" 
                                required
                                maxlength="20"
                                class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200 text-sm font-nunito"
                                placeholder="Ej. Casa 24, #125B"
                                value="{{ old('numero') }}"
                            >
                            @error('numero')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1 md:col-span-2">
                            <label for="colonia" class="block text-base font-bold text-gray-700 dark:text-gray-300 font-nunito">
                                Colonia / Barrio <span class="text-red-500">*</span>
                            </label>
                            <input 
                                id="colonia" 
                                name="colonia" 
                                type="text" 
                                required
                                maxlength="100"
                                class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200 text-sm font-nunito"
                                placeholder="Ej. Bosques del Alba"
                                value="{{ old('colonia') }}"
                            >
                            @error('colonia')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="codigo_postal" class="block text-base font-bold text-gray-700 dark:text-gray-300 font-nunito">
                                Código Postal <span class="text-red-500">*</span>
                            </label>
                            <input 
                                id="codigo_postal" 
                                name="codigo_postal" 
                                type="text" 
                                required
                                maxlength="10"
                                class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200 text-sm font-nunito"
                                placeholder="Ej. 11101"
                                value="{{ old('codigo_postal') }}"
                            >
                            @error('codigo_postal')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1 md:col-span-3">
                            <label for="referencia" class="block text-base font-bold text-gray-700 dark:text-gray-300 font-nunito">
                                Referencia <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="referencia" 
                                name="referencia" 
                                rows="3"
                                required
                                class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200 text-sm font-nunito"
                                placeholder="Ej. Frente a la gasolinera X, edificio gris de 2 pisos"
                            >{{ old('referencia') }}</textarea>
                            @error('referencia')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white font-nunito">
                        Email de Contacto
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="space-y-1">
                            <label for="email_contacto" class="block text-base font-bold text-gray-700 dark:text-gray-300 font-nunito">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-2">
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <input 
                                        id="email_contacto" 
                                        name="email_contacto" 
                                        type="email" 
                                        required 
                                        maxlength="255"
                                        class="flex-1 px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200 text-sm font-nunito"
                                        placeholder="ejemplo@empresa.com"
                                        value="{{ old('email_contacto') }}"
                                    >
                                    <button 
                                        type="button" 
                                        id="btn-enviar-codigo"
                                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed sm:w-auto w-full font-nunito"
                                    >
                                        Enviar Código
                                    </button>
                                </div>
                                @error('email_contacto')
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div id="verification-section" class="hidden space-y-1">
                            <label for="codigo_verificacion" class="block text-base font-bold text-gray-700 dark:text-gray-300 font-nunito">
                                Código de Verificación <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <input 
                                    id="codigo_verificacion" 
                                    name="codigo_verificacion" 
                                    type="text" 
                                    maxlength="6"
                                    class="flex-1 px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200 text-center text-sm tracking-widest font-nunito"
                                    placeholder="000000"
                                >
                                <button 
                                    type="button" 
                                    id="btn-verificar-codigo"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed font-nunito"
                                >
                                    Verificar
                                </button>
                            </div>
                            <p id="verification-error" class="text-sm text-red-600 dark:text-red-400 mt-1 hidden"></p>
                        </div>
                        
                        <div id="verification-success" class="hidden items-center gap-2 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-green-800 dark:text-green-200 font-nunito">Email verificado correctamente</span>
                        </div>
                        
                        <input type="hidden" id="email_verificado" name="email_verificado" value="0">
                    </div>
                </div>

                <div class="pt-4 flex gap-4">
                    <a href="{{ route('cliente.configurar-perfil') }}" 
                       class="flex-1 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold py-3 px-6 rounded-lg text-center hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors duration-200 font-nunito">
                        Volver
                    </a>
                    <button 
                        type="submit" 
                        class="flex-1 bg-gradient-to-r from-green-700 to-green-800 hover:from-green-800 hover:to-green-900 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-[1.01] focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        id="submit-btn"
                    >
                        <span class="flex items-center justify-center font-nunito">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Guardar Datos de Empresa
                        </span>
                    </button>
                </div>

                @if(session('error'))
                    <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-bold text-red-800 dark:text-red-200 font-nunito">
                                    Error al guardar los datos de empresa
                                </h3>
                                <div class="mt-2 text-sm text-red-700 dark:text-red-300 font-nunito">
                                    <p>{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="text-center mt-8">
        <p class="text-sm text-gray-500 dark:text-white font-nunito">
            ¿Necesitas ayuda? <a href="mailto:edw.lagos@gmail.com" class="text-green-600 dark:text-green-400 hover:underline">Contacta soporte</a>
        </p>
    </div>
</div>

<script src="{{ asset('js/location-selector-static.js') }}" defer></script>
<script src="{{ asset('js/email-verification.js') }}" defer></script>
<script src="{{ asset('js/theme-toggle.js') }}" defer></script>
<script src="{{ asset('js/configurar-empresa.js') }}" defer></script>
@endsection