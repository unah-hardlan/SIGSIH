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

<div class="w-full max-w-2xl mx-auto">
    <!-- Tarjeta principal -->
    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg shadow-2xl rounded-3xl border border-gray-200/20 dark:border-gray-700/20 overflow-hidden">
        <!-- Header de la tarjeta -->
        <div class="bg-gradient-to-r from-blue-800 to-blue-900 p-8 text-center">
            <div class="w-20 h-20 mx-auto mb-4 bg-white/20 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">¡Bienvenido!</h1>
            <p class="text-blue-100">Completa tu perfil para comenzar a usar SIGSIH</p>
        </div>

        <!-- Contenido del formulario -->
        <div class="p-8">
            <form action="{{ route('cliente.configurar-perfil.store') }}" method="POST" id="profile-form" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- Sección de Avatar -->
                <div class="text-center mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Foto de Perfil</h3>
                    <div class="flex flex-col items-center">
                        <!-- Preview del avatar -->
                        <div class="relative mb-4">
                            <div class="w-32 h-32 rounded-full border-4 border-gray-300 dark:border-gray-600 overflow-hidden bg-gray-100 dark:bg-gray-700">
                                <img id="avatar-preview" class="w-full h-full object-cover hidden" alt="Preview">
                                <div id="avatar-placeholder" class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Zona de drag and drop -->
                        <div id="avatar-drop-zone" class="w-full max-w-sm border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center cursor-pointer hover:border-blue-500 dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-all duration-300 ease-in-out">
                            <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/jpg,image/png,image/webp" class="hidden" onchange="previewImage(this)">
                            <label for="avatar" class="cursor-pointer">
                                <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium text-blue-600 dark:text-blue-400">Haz clic para subir</span> o arrastra una imagen
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">PNG, JPG, WEBP hasta 2MB (no GIF ni videos)</p>
                            </label>
                        </div>
                        
                        @error('avatar')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Grid de campos -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Primer Nombre -->
                    <div class="space-y-2">
                        <label for="primer_nombre" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Primer Nombre <span class="text-red-500">*</span>
                        </label>
                        <input 
                            id="primer_nombre" 
                            name="primer_nombre" 
                            type="text" 
                            required 
                            class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200"
                            placeholder="Tu primer nombre"
                            value="{{ old('primer_nombre') }}"
                        >
                        @error('primer_nombre')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Segundo Nombre -->
                    <div class="space-y-2">
                        <label for="segundo_nombre" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Segundo Nombre
                        </label>
                        <input 
                            id="segundo_nombre" 
                            name="segundo_nombre" 
                            type="text" 
                            class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200"
                            placeholder="Tu segundo nombre (opcional)"
                            value="{{ old('segundo_nombre') }}"
                        >
                        @error('segundo_nombre')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Primer Apellido -->
                    <div class="space-y-2">
                        <label for="primer_apellido" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Primer Apellido <span class="text-red-500">*</span>
                        </label>
                        <input 
                            id="primer_apellido" 
                            name="primer_apellido" 
                            type="text" 
                            required 
                            class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200"
                            placeholder="Tu primer apellido"
                            value="{{ old('primer_apellido') }}"
                        >
                        @error('primer_apellido')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Segundo Apellido -->
                    <div class="space-y-2">
                        <label for="segundo_apellido" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Segundo Apellido
                        </label>
                        <input 
                            id="segundo_apellido" 
                            name="segundo_apellido" 
                            type="text" 
                            class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200"
                            placeholder="Tu segundo apellido (opcional)"
                            value="{{ old('segundo_apellido') }}"
                        >
                        @error('segundo_apellido')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- DNI -->
                    <div class="space-y-2">
                        <label for="dni" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            DNI <span class="text-red-500">*</span>
                        </label>
                        <input 
                            id="dni" 
                            name="dni" 
                            type="text" 
                            required 
                            class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200"
                            placeholder="Número de documento"
                            value="{{ old('dni') }}"
                        >
                        @error('dni')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Género -->
                    <div class="space-y-2">
                        <label for="id_genero_fk" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Género <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="id_genero_fk" 
                            name="id_genero_fk" 
                            required 
                            class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200"
                        >
                            <option value="">Selecciona tu género</option>
                            @foreach($generos as $genero)
                                <option value="{{ $genero->id_genero_pk }}" {{ old('id_genero_fk') == $genero->id_genero_pk ? 'selected' : '' }}>
                                    {{ $genero->genero }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_genero_fk')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botón de envío -->
                <div class="pt-6">
                    <button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-blue-700 to-blue-800 hover:from-blue-800 hover:to-blue-900 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
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

                <!-- Mensaje de error global -->
                @if(session('error'))
                    <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4">
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

    <!-- Footer -->
    <div class="text-center mt-8">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            ¿Necesitas ayuda? <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Contacta soporte</a>
        </p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profile-form');
    const submitBtn = document.getElementById('submit-btn');
    
    form.addEventListener('submit', function() {
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
});

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