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
    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg shadow-2xl rounded-3xl border border-gray-200/20 dark:border-gray-700/20 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-800 to-blue-900 p-6 text-center">
            <div class="w-20 h-20 mx-auto mb-3 bg-white rounded-full flex items-center justify-center shadow-lg">
                <div class="w-15 h-15 rounded-full overflow-hidden bg-white flex items-center justify-center p-1">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SIGSIH" class="w-full h-full object-contain">
                </div>
            </div>
            <h1 class="text-2xl font-bold text-white mb-2">¡Bienvenido!</h1>
            <p class="text-blue-100 text-sm">Completa tu perfil para comenzar a usar Hardlan.</p>
        </div>

        <div class="p-6">
            <form action="{{ route('cliente.configurar-perfil.store') }}" method="POST" id="profile-form" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="text-center mb-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Foto de Perfil</h3>
                    <div class="flex flex-col items-center">
                        <div class="relative mb-3">
                            <div class="w-24 h-24 rounded-full border-3 border-gray-300 dark:border-gray-600 overflow-hidden bg-gray-100 dark:bg-gray-700">
                                <img id="avatar-preview" class="w-full h-full object-cover hidden" alt="Preview">
                                <div id="avatar-placeholder" class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div id="avatar-drop-zone" class="w-full max-w-xs border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center cursor-pointer hover:border-blue-500 dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-all duration-300 ease-in-out">
                            <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/jpg,image/png,image/webp" class="hidden" onchange="previewImage(this)">
                            <label for="avatar" class="cursor-pointer">
                                <svg class="w-6 h-6 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    <span class="font-medium text-blue-600 dark:text-blue-400">Clic para subir</span> o arrastra
                                </p>
                                <p class="text-xs text-gray-500 dark:text-white">PNG, JPG, WEBP (5MB máx)</p>
                            </label>
                        </div>
                        
                        @error('avatar')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <label for="primer_nombre" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Primer Nombre <span class="text-red-500">*</span>
                        </label>
                        <input 
                            id="primer_nombre" 
                            name="primer_nombre" 
                            type="text" 
                            required 
                            data-validate="name"
                            class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200 text-sm"
                            placeholder="Tu primer nombre"
                            value="{{ old('primer_nombre') }}"
                        >
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1 hidden" data-client-error-for="primer_nombre"></p>
                        @error('primer_nombre')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="segundo_nombre" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Segundo Nombre
                        </label>
                        <input 
                            id="segundo_nombre" 
                            name="segundo_nombre" 
                            type="text" 
                            class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200"
                            placeholder="Tu segundo nombre (opcional)"
                            value="{{ old('segundo_nombre') }}"
                        >
                        @error('segundo_nombre')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="primer_apellido" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Primer Apellido <span class="text-red-500">*</span>
                        </label>
                        <input 
                            id="primer_apellido" 
                            name="primer_apellido" 
                            type="text" 
                            required 
                            data-validate="name"
                            class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200"
                            placeholder="Tu primer apellido"
                            value="{{ old('primer_apellido') }}"
                        >
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1 hidden" data-client-error-for="primer_apellido"></p>
                        @error('primer_apellido')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="segundo_apellido" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Segundo Apellido
                        </label>
                        <input 
                            id="segundo_apellido" 
                            name="segundo_apellido" 
                            type="text" 
                            class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200"
                            placeholder="Tu segundo apellido (opcional)"
                            value="{{ old('segundo_apellido') }}"
                        >
                        @error('segundo_apellido')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="dni" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            DNI <span class="text-red-500">*</span>
                        </label>
                        <input 
                            id="dni" 
                            name="dni" 
                            type="text" 
                            required 
                            data-validate="dni"
                            class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200"
                            placeholder="Número de documento"
                            value="{{ old('dni') }}"
                        >
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1 hidden" data-client-error-for="dni"></p>
                        @error('dni')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="id_genero_fk" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Género <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="id_genero_fk" 
                            name="id_genero_fk" 
                            required 
                            data-validate="select"
                            class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200"
                        >
                            <option value="">Selecciona tu género</option>
                            @foreach($generos as $genero)
                                <option value="{{ $genero->id_genero_pk }}" {{ old('id_genero_fk') == $genero->id_genero_pk ? 'selected' : '' }}>
                                    {{ $genero->genero }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1 hidden" data-client-error-for="id_genero_fk"></p>
                        @error('id_genero_fk')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Sección de Contacto con Verificación -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Email de Contacto
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="space-y-1">
                            <label for="email_contacto" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <input 
                                    id="email_contacto" 
                                    name="email_contacto" 
                                    type="email" 
                                    required 
                                    class="flex-1 px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200"
                                    placeholder="ejemplo@correo.com"
                                    value="{{ old('email_contacto') }}"
                                >
                                <button 
                                    type="button" 
                                    id="btn-enviar-codigo"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed sm:w-auto w-full"
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
                                    class="flex-1 px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200 text-center text-lg tracking-widest font-mono"
                                    placeholder="000000"
                                >
                                <button 
                                    type="button" 
                                    id="btn-verificar-codigo"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    Verificar
                                </button>
                            </div>
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

                <div class="pt-4">
                    <button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-blue-700 to-blue-800 hover:from-blue-800 hover:to-blue-900 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-[1.01] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        id="submit-btn"
                    >
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Completar mi Perfil
                        </span>
                    </button>
                </div>

                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 text-center">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">¿Tu cuenta pertenece a una empresa?</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">
                            Si representas a una empresa, puedes completar los datos corporativos como nombre comercial, RTN y logo empresarial.
                        </p>
                        <a href="{{ route('cliente.configurar-empresa') }}" 
                           class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-blue-300 dark:border-blue-600 rounded-md text-blue-700 dark:text-blue-300 font-medium hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors duration-200 text-sm">
                            <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Configurar empresa
                        </a>
                    </div>
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
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                    Error al completar el perfil
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

    <div class="text-center mt-8">
        <p class="text-sm text-gray-500 dark:text-white">
            ¿Necesitas ayuda? <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Contacta soporte</a>
        </p>
    </div>
</div>

<script>
// Función para mostrar notificaciones toast
function showToast(message, type = 'info', duration = 5000) {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    
    const colors = {
        success: 'bg-green-50 dark:bg-green-950/90 border-green-500 dark:border-green-400 text-green-800 dark:text-green-300',
        error: 'bg-red-50 dark:bg-red-950/90 border-red-500 dark:border-red-400 text-red-800 dark:text-red-300',
        warning: 'bg-yellow-50 dark:bg-yellow-950/90 border-yellow-500 dark:border-yellow-400 text-yellow-800 dark:text-yellow-300',
        info: 'bg-blue-50 dark:bg-blue-950/90 border-blue-500 dark:border-blue-400 text-blue-800 dark:text-blue-300'
    };
    
    const icons = {
        success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
        error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
        warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
        info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
    };
    
    toast.className = `flex items-start gap-3 p-4 rounded-lg border-l-4 shadow-lg transform transition-all duration-300 ease-in-out ${colors[type] || colors.info}`;
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(-100%)';
    
    toast.innerHTML = `
        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            ${icons[type] || icons.info}
        </svg>
        <div class="flex-1 text-sm font-medium">
            ${message}
        </div>
        <button onclick="this.parentElement.remove()" class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    
    container.appendChild(toast);
    
    // Animación de entrada
    setTimeout(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
    }, 10);
    
    // Auto-cerrar
    if (duration > 0) {
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(-100%)';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profile-form');
    const submitBtn = document.getElementById('submit-btn');

    const validators = {
        name: value => value.trim().length >= 2 || 'Debe tener al menos 2 caracteres',
    dni: value => /^[0-9-]{6,20}$/.test(value.trim()) || 'DNI inválido (solo números y guiones, 6-20 caracteres)',
        select: value => value !== '' || 'Este campo es obligatorio',
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
            if (touched[input.id] || triedSubmit) {
                showError(input, res);
            } else {
                clearError(input);
            }
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

    // Avatar preview
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatar-preview');
    const avatarPlaceholder = document.getElementById('avatar-placeholder');
    function previewImage(input) {
        const file = input.files && input.files[0];
        if (!file) {
            avatarPreview.classList.add('hidden');
            avatarPlaceholder.classList.remove('hidden');
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            avatarPreview.src = e.target.result;
            avatarPreview.classList.remove('hidden');
            avatarPlaceholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
    window.previewImage = previewImage;

    validateAll();

    form.addEventListener('submit', function(e) {
        triedSubmit = true;
        if (!validateAll()) {
            e.preventDefault();
            // focus first invalid field
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
                Guardando perfil...
            </span>
        `;
    });

    // Configuración de verificación de email
    setupEmailVerification();
});

function setupEmailVerification() {
    const emailInput = document.getElementById('email_contacto');
    const btnEnviarCodigo = document.getElementById('btn-enviar-codigo');
    const btnVerificarCodigo = document.getElementById('btn-verificar-codigo');
    const verificationSection = document.getElementById('verification-section');
    const verificationSuccess = document.getElementById('verification-success');
    const codigoInput = document.getElementById('codigo_verificacion');
    const verificationError = document.getElementById('verification-error');
    const emailVerificadoInput = document.getElementById('email_verificado');
    const submitBtn = document.getElementById('submit-btn');
    
    let codigoEnviado = null;
    let intentosRestantes = 3;
    
    // Deshabilitar submit hasta que el email esté verificado
    submitBtn.disabled = true;
    
    // Validar email antes de enviar código
    btnEnviarCodigo.addEventListener('click', async function() {
        const email = emailInput.value.trim();
        
        if (!email || !emailInput.validity.valid) {
            showToast('Por favor, ingrese un email válido', 'warning');
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
                
                // Focus en el input del código
                codigoInput.focus();
                
                intentosRestantes = 3;
            } else {
                // Error al enviar
                const errorMessage = data.message || 'Error al enviar el código';
                showToast(errorMessage, 'error', 7000);
                btnEnviarCodigo.disabled = false;
                btnEnviarCodigo.textContent = 'Enviar Código';
            }
        } catch (error) {
            console.error('Error:', error);
            showToast(error.message || 'Error al enviar el código. Por favor, intenta nuevamente.', 'error');
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
    
    function showVerificationError(message) {
        verificationError.textContent = message;
        verificationError.classList.remove('hidden');
    }
    
    function resetVerification() {
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

function toggleTheme() {
    const html = document.documentElement;
    const button = document.querySelector('.theme-toggle');
    const isDark = html.classList.contains('dark');
    
    button.style.transform = 'scale(0.9)';
    
    setTimeout(() => {
        if (isDark) {
            html.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            html.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
        
        button.style.transform = 'scale(1)';
    }, 100);
}
</script>
@endsection
