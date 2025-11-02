@extends('cliente.layouts.standalone')
@section('content')
<!-- Toggle de tema sticky en esquina superior derecha -->
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
    <!-- Tarjeta principal -->
    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg shadow-2xl rounded-3xl border border-gray-200/20 dark:border-gray-700/20 overflow-hidden">
        <!-- Header de la tarjeta -->
        <div class="bg-gradient-to-r from-green-700 to-green-800 p-6 text-center">
            <div class="w-20 h-20 mx-auto mb-3 bg-white rounded-full flex items-center justify-center shadow-lg">
                <!-- Logo de la empresa -->
                <div class="w-15 h-15 rounded-full overflow-hidden bg-white flex items-center justify-center p-1">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SIGSIH" class="w-full h-full object-contain">
                </div>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Datos de Empresa</h1>
            <p class="text-green-100">Completa la información corporativa de tu empresa</p>
        </div>

        <!-- Contenido del formulario -->
        <div class="p-6">
            <form action="{{ route('cliente.configurar-empresa.store') }}" method="POST" id="empresa-form" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- Sección de Logo de Empresa -->
                <div class="text-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Logo de la Empresa</h3>
                    <div class="flex flex-col items-center">
                        <!-- Preview del logo -->
                        <div class="relative mb-4">
                            <div class="w-32 h-32 rounded-full border-4 border-gray-300 dark:border-gray-600 overflow-hidden bg-gray-100 dark:bg-gray-700">
                                <img id="logo-preview" class="w-full h-full object-cover hidden" alt="Preview">
                                <div id="logo-placeholder" class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Zona de drag and drop -->
                        <div id="logo-drop-zone" class="w-full max-w-sm border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center cursor-pointer hover:border-green-500 dark:hover:border-green-400 hover:bg-green-50 dark:hover:bg-green-900/10 transition-all duration-300 ease-in-out">
                            <input type="file" id="avatar" name="avatar" data-validate="avatar" accept="image/jpeg,image/jpg,image/png,image/webp" class="hidden" onchange="previewLogo(this)">
                            <label for="avatar" class="cursor-pointer">
                                <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium text-green-600 dark:text-green-400">Haz clic para subir</span> o arrastra el logo
                                </p>
                                <p class="text-xs text-gray-500 dark:text-white mt-1">PNG, JPG, WEBP hasta 5MB</p>
                            </label>
                        </div>
                        
                        @error('avatar')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Grid de campos de empresa -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nombre Comercial -->
                    <div class="space-y-1">
                        <label for="nombre_comercial" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Nombre Comercial <span class="text-red-500">*</span>
                        </label>
                        <input 
                            id="nombre_comercial" 
                            name="nombre_comercial" 
                            type="text" 
                            required 
                            data-validate="name"
                            class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200"
                            placeholder="Nombre comercial de la empresa"
                            value="{{ old('nombre_comercial') }}"
                        >
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1 hidden" data-client-error-for="nombre_comercial"></p>
                        @error('nombre_comercial')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Razón Social -->
                    <div class="space-y-1">
                        <label for="razon_social" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Razón Social
                        </label>
                        <input 
                            id="razon_social" 
                            name="razon_social" 
                            type="text" 
                            class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200"
                            placeholder="Razón social legal"
                            value="{{ old('razon_social') }}"
                        >
                        @error('razon_social')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- RTN -->
                    <div class="space-y-1">
                        <label for="rtn" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            RTN
                        </label>
                        <input 
                            id="rtn" 
                            name="rtn" 
                            type="text" 
                            data-validate="rtn"
                            class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200"
                            placeholder="Registro Tributario Nacional"
                            value="{{ old('rtn') }}"
                        >
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1 hidden" data-client-error-for="rtn"></p>
                        @error('rtn')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Horario de Atención -->
                    <div class="md:col-span-2 space-y-1">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Horario de Atención
                        </label>

                        <div class="space-y-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white/80 dark:bg-gray-800/60 px-4 py-4 flex items-center justify-center">
                            <input type="hidden" name="horario_atencion" id="horario_atencion" value="{{ old('horario_atencion') }}">

                            <!-- Días de la semana -->
                            <div class="space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Días</p>
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
                                            <span class="select-none rounded-lg border-2 border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 transition-all duration-150 hover:border-gray-400 cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:border-gray-500 dark:peer-checked:border-green-400 dark:peer-checked:bg-green-900/40 dark:peer-checked:text-green-300">
                                                {{ $dia['label'] }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <!-- Presets rápidos -->
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" class="horario-preset inline-flex items-center justify-center rounded-lg border border-gray-300 bg-gray-50 px-3 py-1 text-xs font-medium text-gray-600 transition-all duration-150 hover:border-green-500 hover:bg-green-50 hover:text-green-600 focus:outline-none dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-400 dark:hover:border-green-400 dark:hover:bg-green-900/20 dark:hover:text-green-300" data-preset="weekdays">Lunes a Viernes</button>
                                    <button type="button" class="horario-preset inline-flex items-center justify-center rounded-lg border border-gray-300 bg-gray-50 px-3 py-1 text-xs font-medium text-gray-600 transition-all duration-150 hover:border-green-500 hover:bg-green-50 hover:text-green-600 focus:outline-none dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-400 dark:hover:border-green-400 dark:hover:bg-green-900/20 dark:hover:text-green-300" data-preset="weekends">Lunes a Sábado</button>
                                    
                                </div>
                            </div>

                            <!-- Hora de apertura y cierre -->
                            <div class="space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Hora:</p>
                                <div class="flex flex-wrap gap-3 items-center">
                                    <div class="flex items-center gap-2">
                                        <input 
                                            type="time" 
                                            id="horario_inicio" 
                                            class="px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200"
                                            value="08:00"
                                        >
                                        <span class="text-sm text-gray-500 dark:text-gray-400">a. m.</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input 
                                            type="time" 
                                            id="horario_fin" 
                                            class="px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200"
                                            value="17:00"
                                        >
                                        <span class="text-sm text-gray-500 dark:text-gray-400">p. m.</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview del horario -->
                            <div class="pt-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Horario:</p>
                                <p id="horario-preview" class="text-sm text-gray-700 dark:text-gray-300 font-medium">—</p>
                            </div>
                        </div>
                        
                        @error('horario_atencion')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Descripción de la Empresa -->
                <div class="space-y-1">
                    <label for="descripcion_empresa" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Descripción de la Empresa
                    </label>
                    <textarea 
                        id="descripcion_empresa" 
                        name="descripcion_empresa" 
                        rows="3"
                        class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200 resize-y min-h-[80px] max-h-[280px]"
                        placeholder="Describe brevemente tu empresa y sus servicios"
                    >{{ old('descripcion_empresa') }}</textarea>
                    @error('descripcion_empresa')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email de Contacto con Verificación -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Email de Contacto
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="space-y-1">
                            <label for="email_contacto" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <input 
                                    id="email_contacto" 
                                    name="email_contacto" 
                                    type="email" 
                                    required 
                                    class="flex-1 px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200"
                                    placeholder="ejemplo@empresa.com"
                                    value="{{ old('email_contacto') }}"
                                >
                                <button 
                                    type="button" 
                                    id="btn-enviar-codigo"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    Enviar Código
                                </button>
                            </div>
                            @error('email_contacto')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Campo de verificación (oculto inicialmente) -->
                        <div id="verification-section" class="hidden space-y-1">
                            <label for="codigo_verificacion" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Código de Verificación <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <input 
                                    id="codigo_verificacion" 
                                    name="codigo_verificacion" 
                                    type="text" 
                                    maxlength="6"
                                    class="flex-1 px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200 text-center text-lg tracking-widest font-mono"
                                    placeholder="000000"
                                >
                                <button 
                                    type="button" 
                                    id="btn-verificar-codigo"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    Verificar
                                </button>
                            </div>
                            <p id="verification-timer" class="text-xs text-gray-500 dark:text-gray-400 mt-1"></p>
                            <p id="verification-error" class="text-sm text-red-600 dark:text-red-400 mt-1 hidden"></p>
                        </div>
                        
                        <!-- Indicador de verificación exitosa -->
                        <div id="verification-success" class="hidden items-center gap-2 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-green-800 dark:text-green-200">Email verificado correctamente</span>
                        </div>
                        
                        <!-- Campo oculto para indicar si el email está verificado -->
                        <input type="hidden" id="email_verificado" name="email_verificado" value="0">
                    </div>
                </div>

                <!-- Botones -->
                <div class="pt-6 flex gap-4">
                    <a href="{{ route('cliente.configurar-perfil') }}" 
                       class="flex-1 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold py-3 px-6 rounded-lg text-center hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors duration-200">
                        Volver
                    </a>
                    <button 
                        type="submit" 
                        class="flex-1 bg-gradient-to-r from-green-700 to-green-800 hover:from-green-800 hover:to-green-900 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        id="submit-btn"
                    >
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Guardar Datos de Empresa
                        </span>
                    </button>
                </div>

                <!-- Mensaje de error global -->
                @if(session('error'))
                    <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                    Error al guardar los datos de empresa
                                </h3>
                                <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                    <p>{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center mt-8">
        <p class="text-sm text-gray-500 dark:text-white">
            ¿Necesitas ayuda? <a href="#" class="text-green-600 dark:text-green-400 hover:underline">Contacta soporte</a>
        </p>
    </div>
</div>

<script>
function previewLogo(input) {
    const preview = document.getElementById('logo-preview');
    const placeholder = document.getElementById('logo-placeholder');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '';
        preview.classList.add('hidden');
        placeholder.classList.remove('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('empresa-form');
    const submitBtn = document.getElementById('submit-btn');
    
    // Validation setup
    const validators = {
        name: value => value.trim().length >= 2 || 'Debe tener al menos 2 caracteres',
        rtn: value => value.trim() === '' || /^[0-9-]{6,20}$/.test(value.trim()) || 'RTN inválido (solo números y guiones, 6-20 caracteres)',
        avatar: file => {
            if (!file) return true;
            const allowed = ['image/jpeg','image/jpg','image/png','image/webp'];
            if (!allowed.includes(file.type)) return 'Formato no permitido';
            if (file.size > 5 * 1024 * 1024) return 'La imagen debe ser menor a 5MB';
            return true;
        }
    };

    const touched = {};
    let triedSubmit = false;

    function showError(input, message) {
        const el = document.querySelector(`[data-client-error-for="${input.id}"]`);
        if (el) {
            el.textContent = message;
            el.classList.remove('hidden');
        }
        input.classList.add('border-red-500');
        input.classList.remove('border-gray-300');
    }

    function clearError(input) {
        const el = document.querySelector(`[data-client-error-for="${input.id}"]`);
        if (el) {
            el.textContent = '';
            el.classList.add('hidden');
        }
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
    }

    function validateInput(input) {
        const rule = input.dataset.validate;
        if (!rule) return true;
        let value;
        if (input.type === 'file') value = input.files[0] || null;
        else value = input.value || '';

        const res = validators[rule](value);
        if (res === true) {
            clearError(input);
            return true;
        } else {
            if (touched[input.id] || triedSubmit) showError(input, res);
            else clearError(input);
            return false;
        }
    }

    function validateAll() {
        const inputs = form.querySelectorAll('[data-validate]');
        let ok = true;
        inputs.forEach(i => {
            const v = validateInput(i);
            if (!v) ok = false;
        });
        submitBtn.disabled = !ok;
        return ok;
    }

    // attach listeners to data-validate fields
    form.querySelectorAll('[data-validate]').forEach(input => {
        touched[input.id] = false;
        const ev = input.type === 'file' ? 'change' : 'input';
        input.addEventListener(ev, () => {
            touched[input.id] = true;
            validateInput(input);
            validateAll();
        });
        input.addEventListener('blur', () => {
            touched[input.id] = true;
            validateInput(input);
            validateAll();
        });
    });

    form.addEventListener('submit', function(e) {
        triedSubmit = true;
        if (!validateAll()) {
            e.preventDefault();
            const firstInvalid = form.querySelector('[data-validate].border-red-500') || form.querySelector('[data-validate]');
            if (firstInvalid) firstInvalid.focus();
            return;
        }
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <span class="flex items-center justify-center">
                <svg class="animate-spin w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Guardando datos...
            </span>
        `;
    });

    // Configurar drag & drop para el logo
    setupLogoDragAndDrop();
    
    // Configurar horario de atención interactivo
    setupHorarioAtencion();
    
    // Configurar verificación de email
    setupEmailVerification();
});

// Función para configurar drag & drop del logo
function setupLogoDragAndDrop() {
    const dropZone = document.getElementById('logo-drop-zone');
    const fileInput = document.getElementById('avatar');

    // Verificar que los elementos existan
    if (!dropZone || !fileInput) {
        return;
    }

    // Prevenir comportamiento por defecto
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    // Highlight de la zona de drop
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    // Manejar el drop
    dropZone.addEventListener('drop', handleLogoDrop, false);

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight(e) {
        dropZone.classList.add('border-green-500', 'bg-green-50');
        dropZone.classList.remove('border-gray-300');
    }

    function unhighlight(e) {
        dropZone.classList.remove('border-green-500', 'bg-green-50');
        dropZone.classList.add('border-gray-300');
    }

    function handleLogoDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length > 0) {
            const file = files[0];
            
            // Validar tipo de archivo
            if (!file.type.match('image.*')) {
                alert('Por favor selecciona solo archivos de imagen.');
                return;
            }

            // Validar tamaño (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('El archivo es muy grande. Máximo 5MB.');
                return;
            }

            // Asignar archivo al input
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;

            // Mostrar preview
            previewLogo(fileInput);
        }
    }
}

// Función para mostrar preview del logo
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const preview = document.getElementById('logo-preview');
            const placeholder = document.getElementById('logo-placeholder');
            
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Función para configurar el horario de atención interactivo
function setupHorarioAtencion() {
    const hiddenInput = document.getElementById('horario_atencion');
    const dayCheckboxes = document.querySelectorAll('[data-day-checkbox]');
    const horarioInicio = document.getElementById('horario_inicio');
    const horarioFin = document.getElementById('horario_fin');
    const horarioPreview = document.getElementById('horario-preview');
    const presetButtons = document.querySelectorAll('.horario-preset');
    
    // Función para actualizar el preview y el campo oculto
    function updateHorario() {
        const selectedDays = Array.from(dayCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
        
        const inicio = horarioInicio.value;
        const fin = horarioFin.value;
        
        if (selectedDays.length === 0 || !inicio || !fin) {
            horarioPreview.textContent = '—';
            hiddenInput.value = '';
            return;
        }
        
        // Generar cadena de días (compactar rangos)
        let daysString = '';
        const dayOrder = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
        const sortedDays = selectedDays.sort((a, b) => dayOrder.indexOf(a) - dayOrder.indexOf(b));
        
        // Detectar rangos consecutivos
        if (sortedDays.length === 7) {
            daysString = 'L-D';
        } else if (sortedDays.length === 5 && sortedDays.join('') === 'LMXJV') {
            daysString = 'L-V';
        } else if (sortedDays.length === 6 && sortedDays.join('') === 'LMXJVS') {
            daysString = 'L-S';
        } else {
            // Días individuales o rangos cortos
            let ranges = [];
            let start = 0;
            for (let i = 1; i <= sortedDays.length; i++) {
                if (i === sortedDays.length || dayOrder.indexOf(sortedDays[i]) - dayOrder.indexOf(sortedDays[i-1]) > 1) {
                    if (i - start > 2) {
                        ranges.push(sortedDays[start] + '-' + sortedDays[i-1]);
                    } else {
                        for (let j = start; j < i; j++) {
                            ranges.push(sortedDays[j]);
                        }
                    }
                    start = i;
                }
            }
            daysString = ranges.join(', ');
        }
        
        // Formatear horario con AM/PM
        const inicioFormatted = formatTime(inicio);
        const finFormatted = formatTime(fin);
        
        const horarioString = `${daysString} ${inicioFormatted}-${finFormatted}`;
        horarioPreview.textContent = horarioString;
        hiddenInput.value = horarioString;
    }
    
    // Función para formatear hora en formato 12h con AM/PM
    function formatTime(time24) {
        const [hours, minutes] = time24.split(':').map(Number);
        const period = hours >= 12 ? 'PM' : 'AM';
        const hours12 = hours % 12 || 12;
        return `${hours12}:${minutes.toString().padStart(2, '0')} ${period}`;
    }
    
    // Event listeners para checkboxes de días
    dayCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateHorario);
    });
    
    // Event listeners para inputs de hora
    horarioInicio.addEventListener('change', updateHorario);
    horarioFin.addEventListener('change', updateHorario);
    
    // Event listeners para botones de preset
    presetButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const preset = this.dataset.preset;
            
            dayCheckboxes.forEach(cb => {
                if (preset === 'all') {
                    cb.checked = true;
                } else if (preset === 'weekdays') {
                    cb.checked = ['L', 'M', 'X', 'J', 'V'].includes(cb.value);
                } else if (preset === 'weekends') {
                    cb.checked = ['L', 'M', 'X', 'J', 'V', 'S'].includes(cb.value);
                } else if (preset === 'none') {
                    cb.checked = false;
                }
            });
            
            updateHorario();
        });
    });
    
    // Hidratar desde el valor antiguo si existe
    const oldValue = hiddenInput.value;
    if (oldValue) {
        parseAndSetHorario(oldValue);
    }
    
    // Función para parsear y establecer un horario existente
    function parseAndSetHorario(horarioString) {
        // Formato esperado: "L-V 8:00 AM-5:00 PM" o similar
        const match = horarioString.match(/^([LMXJVSD, -]+)\s+(\d{1,2}:\d{2}\s*(?:AM|PM)?)-(\d{1,2}:\d{2}\s*(?:AM|PM)?)$/i);
        
        if (!match) return;
        
        const [, daysStr, inicioStr, finStr] = match;
        
        // Parsear días
        const dayMap = {'L': 'L', 'M': 'M', 'X': 'X', 'J': 'J', 'V': 'V', 'S': 'S', 'D': 'D'};
        dayCheckboxes.forEach(cb => cb.checked = false);
        
        if (daysStr.includes('-')) {
            // Rango de días
            const rangeParts = daysStr.split('-').map(s => s.trim());
            if (rangeParts.length === 2) {
                const dayOrder = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
                const startIdx = dayOrder.indexOf(rangeParts[0]);
                const endIdx = dayOrder.indexOf(rangeParts[1]);
                
                if (startIdx !== -1 && endIdx !== -1) {
                    for (let i = startIdx; i <= endIdx; i++) {
                        const checkbox = Array.from(dayCheckboxes).find(cb => cb.value === dayOrder[i]);
                        if (checkbox) checkbox.checked = true;
                    }
                }
            }
        } else {
            // Días individuales separados por coma
            daysStr.split(',').forEach(day => {
                const d = day.trim();
                const checkbox = Array.from(dayCheckboxes).find(cb => cb.value === d);
                if (checkbox) checkbox.checked = true;
            });
        }
        
        // Parsear horas (convertir de 12h a 24h)
        horarioInicio.value = convertTo24Hour(inicioStr.trim());
        horarioFin.value = convertTo24Hour(finStr.trim());
        
        updateHorario();
    }
    
    // Convertir formato 12h a 24h
    function convertTo24Hour(time12) {
        const match = time12.match(/(\d{1,2}):(\d{2})\s*(AM|PM)?/i);
        if (!match) return '08:00';
        
        let [, hours, minutes, period] = match;
        hours = parseInt(hours);
        
        if (period && period.toUpperCase() === 'PM' && hours !== 12) {
            hours += 12;
        } else if (period && period.toUpperCase() === 'AM' && hours === 12) {
            hours = 0;
        }
        
        return `${hours.toString().padStart(2, '0')}:${minutes}`;
    }
    
    // Inicializar preview
    updateHorario();
}

// Función para configurar la verificación de email
function setupEmailVerification() {
    const emailInput = document.getElementById('email_contacto');
    const btnEnviarCodigo = document.getElementById('btn-enviar-codigo');
    const btnVerificarCodigo = document.getElementById('btn-verificar-codigo');
    const verificationSection = document.getElementById('verification-section');
    const verificationSuccess = document.getElementById('verification-success');
    const codigoInput = document.getElementById('codigo_verificacion');
    const verificationTimer = document.getElementById('verification-timer');
    const verificationError = document.getElementById('verification-error');
    const emailVerificadoInput = document.getElementById('email_verificado');
    const submitBtn = document.getElementById('submit-btn');
    
    let codigoEnviado = null;
    let timerInterval = null;
    let intentosRestantes = 3;
    
    // Deshabilitar submit hasta que el email esté verificado
    submitBtn.disabled = true;
    
    // Validar email antes de enviar código
    btnEnviarCodigo.addEventListener('click', async function() {
        const email = emailInput.value.trim();
        
        if (!email || !emailInput.validity.valid) {
            alert('Por favor, ingrese un email válido');
            emailInput.focus();
            return;
        }
        
        // Deshabilitar botón y cambiar texto
        btnEnviarCodigo.disabled = true;
        btnEnviarCodigo.textContent = 'Enviando...';
        
        try {
            const response = await fetch('/api/email-contacto/enviar-codigo', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ email: email })
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                // Código enviado exitosamente
                codigoEnviado = true; // Marcar que se envió el código
                
                // Mostrar sección de verificación
                verificationSection.classList.remove('hidden');
                verificationSuccess.classList.add('hidden');
                verificationError.classList.add('hidden');
                emailInput.readOnly = true;
                btnEnviarCodigo.textContent = 'Código Enviado';
                
                // Iniciar temporizador con el tiempo de expiración del servidor
                const expiresIn = data.expires_in || 300; // Default 5 minutos
                startTimer(expiresIn);
                
                // Focus en el input del código
                codigoInput.focus();
                
                intentosRestantes = 3;
            } else {
                // Error al enviar
                throw new Error(data.message || 'Error al enviar el código');
            }
        } catch (error) {
            console.error('Error:', error);
            alert(error.message || 'Error al enviar el código. Por favor, intenta nuevamente.');
            btnEnviarCodigo.disabled = false;
            btnEnviarCodigo.textContent = 'Enviar Código';
        }
    });
    
    // Verificar código ingresado
    btnVerificarCodigo.addEventListener('click', async function() {
        const codigoIngresado = codigoInput.value.trim();
        const email = emailInput.value.trim();
        
        if (!codigoIngresado || codigoIngresado.length !== 6) {
            showVerificationError('Por favor, ingrese el código de 6 dígitos');
            return;
        }
        
        btnVerificarCodigo.disabled = true;
        btnVerificarCodigo.textContent = 'Verificando...';
        verificationError.classList.add('hidden');
        
        try {
            const response = await fetch('/api/email-contacto/verificar-codigo', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ 
                    email: email,
                    codigo: codigoIngresado 
                })
            });
            
            const data = await response.json();
            
            if (response.ok && data.success && data.verified) {
                // Código correcto
                clearInterval(timerInterval);
                verificationSection.classList.add('hidden');
                verificationSuccess.classList.remove('hidden');
                verificationSuccess.classList.add('flex');
                emailVerificadoInput.value = '1';
                submitBtn.disabled = false;
            } else {
                // Código incorrecto o error
                const message = data.message || 'Código incorrecto';
                showVerificationError(message);
                
                const attemptsRemaining = data.attempts_remaining;
                
                if (attemptsRemaining !== undefined && attemptsRemaining > 0) {
                    codigoInput.value = '';
                    codigoInput.focus();
                    btnVerificarCodigo.disabled = false;
                    btnVerificarCodigo.textContent = 'Verificar';
                } else if (response.status === 429 || attemptsRemaining === 0) {
                    // Agotó intentos o código expirado
                    codigoInput.disabled = true;
                    btnVerificarCodigo.disabled = true;
                    
                    // Permitir reenviar código después de 3 segundos
                    setTimeout(() => {
                        resetVerification();
                    }, 3000);
                } else {
                    btnVerificarCodigo.disabled = false;
                    btnVerificarCodigo.textContent = 'Verificar';
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showVerificationError('Error al verificar el código. Por favor, intenta nuevamente.');
            btnVerificarCodigo.disabled = false;
            btnVerificarCodigo.textContent = 'Verificar';
        }
    });
    
    // Permitir verificar con Enter
    codigoInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            btnVerificarCodigo.click();
        }
    });
    
    // Solo permitir números en el código
    codigoInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    
    function startTimer(seconds) {
        let timeLeft = seconds;
        
        updateTimerDisplay(timeLeft);
        
        timerInterval = setInterval(() => {
            timeLeft--;
            updateTimerDisplay(timeLeft);
            
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                showVerificationError('El código ha expirado. Solicita uno nuevo');
                codigoInput.disabled = true;
                btnVerificarCodigo.disabled = true;
                
                setTimeout(() => {
                    resetVerification();
                }, 3000);
            }
        }, 1000);
    }
    
    function updateTimerDisplay(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = seconds % 60;
        verificationTimer.textContent = `Código válido por ${minutes}:${secs.toString().padStart(2, '0')}`;
    }
    
    function showVerificationError(message) {
        verificationError.textContent = message;
        verificationError.classList.remove('hidden');
    }
    
    function resetVerification() {
        clearInterval(timerInterval);
        verificationSection.classList.add('hidden');
        verificationSuccess.classList.add('hidden');
        verificationError.classList.add('hidden');
        emailInput.readOnly = false;
        codigoInput.value = '';
        codigoInput.disabled = false;
        btnEnviarCodigo.disabled = false;
        btnEnviarCodigo.textContent = 'Enviar Código';
        btnVerificarCodigo.disabled = false;
        btnVerificarCodigo.textContent = 'Verificar';
        emailVerificadoInput.value = '0';
        submitBtn.disabled = true;
        codigoEnviado = null;
        intentosRestantes = 3;
    }
    
    // Resetear verificación si cambia el email
    emailInput.addEventListener('input', function() {
        if (emailVerificadoInput.value === '1') {
            resetVerification();
        }
    });
}

// Función para toggle del tema con animación suave
function toggleTheme() {
    const html = document.documentElement;
    const button = document.querySelector('.theme-toggle');
    const isDark = html.classList.contains('dark');
    
    // Animación del botón
    button.style.transform = 'scale(0.9)';
    
    setTimeout(() => {
        if (isDark) {
            html.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            html.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
        
        // Restaurar escala del botón
        button.style.transform = 'scale(1)';
    }, 100);
}
</script>
@endsection
