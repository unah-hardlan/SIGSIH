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
                                <p class="text-xs text-gray-500 dark:text-white mt-1">PNG, JPG, WEBP hasta 2MB</p>
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
                    <div class="space-y-1">
                        <label for="horario_atencion" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Horario de Atención
                        </label>
                        <div class="relative">
                            <input 
                                id="horario_atencion" 
                                name="horario_atencion" 
                                type="text" 
                                class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200"
                                placeholder="Ej: L-V 8:00-17:00, S 9:00-12:00"
                                value="{{ old('horario_atencion') }}"
                                onblur="validateHorario(this)"
                                oninput="clearHorarioError()"
                            >
                            <div id="horario-validation-icon" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                                <!-- Icono de éxito -->
                                <svg class="w-5 h-5 text-green-500 success-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <!-- Icono de error -->
                                <svg class="w-5 h-5 text-red-500 error-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </div>
                        <div id="horario-help" class="text-xs text-gray-500 dark:text-white">
                            <strong>Formatos válidos:</strong> L-V 8:00-17:00 | L-S 9:00-18:00 | L-V 8:00-12:00, 14:00-18:00
                        </div>
                        <div id="horario-error" class="text-sm text-red-600 dark:text-red-400 hidden"></div>
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
                        class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200"
                        placeholder="Describe brevemente tu empresa y sus servicios"
                    >{{ old('descripcion_empresa') }}</textarea>
                    @error('descripcion_empresa')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
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
            if (file.size > 2 * 1024 * 1024) return 'La imagen debe ser menor a 2MB';
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
});

// Función para configurar drag & drop del logo
function setupLogoDragAndDrop() {
    const dropZone = document.getElementById('logo-drop-zone');
    const fileInput = document.getElementById('avatar');

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

            // Validar tamaño (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('El archivo es muy grande. Máximo 2MB.');
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

// Función para validar formato de horario
function validateHorario(input) {
    const value = input.value.trim();
    const errorDiv = document.getElementById('horario-error');
    const iconContainer = document.getElementById('horario-validation-icon');
    const successIcon = iconContainer.querySelector('.success-icon');
    const errorIcon = iconContainer.querySelector('.error-icon');
    
    // Si está vacío, no mostrar error (campo opcional)
    if (!value) {
        hideHorarioValidation();
        return true;
    }
    
    // Patrones de validación para horarios
    const patterns = [
        // L-V 8:00-17:00
        /^[LMXJVSD]-[LMXJVSD]\s+\d{1,2}:\d{2}-\d{1,2}:\d{2}$/,
        // L-V 8:00-12:00, 14:00-18:00 (con pausa)
        /^[LMXJVSD]-[LMXJVSD]\s+\d{1,2}:\d{2}-\d{1,2}:\d{2},\s*\d{1,2}:\d{2}-\d{1,2}:\d{2}$/,
        // L 8:00-17:00 (día individual)
        /^[LMXJVSD]\s+\d{1,2}:\d{2}-\d{1,2}:\d{2}$/,
        // 24 horas
        /^24\s*horas?$/i,
        // Cerrado
        /^cerrado$/i
    ];
    
    const isValid = patterns.some(pattern => pattern.test(value));
    
    if (isValid) {
        showHorarioSuccess();
        return true;
    } else {
        showHorarioError('Formato de horario inválido. Ejemplos: "L-V 8:00-17:00", "L-S 9:00-18:00", "24 horas"');
        return false;
    }
}

function showHorarioSuccess() {
    const errorDiv = document.getElementById('horario-error');
    const iconContainer = document.getElementById('horario-validation-icon');
    const successIcon = iconContainer.querySelector('.success-icon');
    const errorIcon = iconContainer.querySelector('.error-icon');
    const input = document.getElementById('horario_atencion');
    
    errorDiv.classList.add('hidden');
    iconContainer.classList.remove('hidden');
    successIcon.classList.remove('hidden');
    errorIcon.classList.add('hidden');
    
    input.classList.remove('border-red-500');
    input.classList.add('border-green-500');
}

function showHorarioError(message) {
    const errorDiv = document.getElementById('horario-error');
    const iconContainer = document.getElementById('horario-validation-icon');
    const successIcon = iconContainer.querySelector('.success-icon');
    const errorIcon = iconContainer.querySelector('.error-icon');
    const input = document.getElementById('horario_atencion');
    
    errorDiv.textContent = message;
    errorDiv.classList.remove('hidden');
    iconContainer.classList.remove('hidden');
    successIcon.classList.add('hidden');
    errorIcon.classList.remove('hidden');
    
    input.classList.remove('border-green-500');
    input.classList.add('border-red-500');
}

function hideHorarioValidation() {
    const errorDiv = document.getElementById('horario-error');
    const iconContainer = document.getElementById('horario-validation-icon');
    const input = document.getElementById('horario_atencion');
    
    errorDiv.classList.add('hidden');
    iconContainer.classList.add('hidden');
    input.classList.remove('border-green-500', 'border-red-500');
}

function clearHorarioError() {
    const input = document.getElementById('horario_atencion');
    if (input.classList.contains('border-red-500')) {
        hideHorarioValidation();
    }
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
