@extends('cliente.layouts.standalone')
@section('content')
<div id="toast-container" class="fixed top-4 left-4 z-50 space-y-3 max-w-md"></div>

<div class="fixed top-4 right-4 z-50">
    <button 
        onclick="toggleTheme()" 
        class="theme-toggle inline-flex items-class="w-full bg-gradient-to-r from-blue-700 to-blue-800 hover:from-blue-800 hover:to-blue-900 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-[1.01] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none serif"enter justify-center w-12 h-12 rounded-full backdrop-blur-lg shadow-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white"
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
        <div class="bg-gradient-to-r from-blue-800 to-blue-900 p-6 text-center">
            <div class="w-20 h-20 mx-auto mb-3 bg-white rounded-full flex items-center justify-center shadow-lg">
                <div class="w-15 h-15 rounded-full overflow-hidden bg-white flex items-center justify-center p-1">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SIGSIH" class="w-full h-full object-contain">
                </div>
            </div>
            <h1 class="text-2xl font-bold text-white mb-2 serif">¡Bienvenido!</h1>
            <p class="text-blue-100 text-base serif">Completa tu perfil para comenzar a usar nuestro sistema de Soporte técnico.</p>
        </div>

        <div class="p-6">
            <form action="{{ route('cliente.configurar-perfil.store') }}" method="POST" id="profile-form" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="text-center mb-6">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3 serif">Foto de Perfil</h3>
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
                                <p class="text-xs text-gray-600 dark:text-gray-400 serif">
                                    <span class="font-medium text-blue-600 dark:text-blue-400">Clic para subir</span> o arrastra
                                </p>
                                <p class="text-xs text-gray-500 dark:text-white serif">PNG, JPG, WEBP (5MB máx)</p>
                            </label>
                        </div>
                        
                        @error('avatar')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <label for="primer_nombre" class="block text-base font-bold text-gray-700 dark:text-gray-300 serif">
                            Primer Nombre <span class="text-red-500">*</span>
                        </label>
                        <input 
                            id="primer_nombre" 
                            name="primer_nombre" 
                            type="text" 
                            required 
                            data-validate="name"
                            class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200 text-sm serif"
                            placeholder="Ej. John"
                            value="{{ old('primer_nombre') }}"
                        >
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1 hidden" data-client-error-for="primer_nombre"></p>
                        @error('primer_nombre')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="segundo_nombre" class="block text-base font-bold text-gray-700 dark:text-gray-300 serif">
                            Segundo Nombre
                        </label>
                        <input 
                            id="segundo_nombre" 
                            name="segundo_nombre" 
                            type="text" 
                            class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200 text-sm serif"
                            placeholder="Ej. Dae"
                            value="{{ old('segundo_nombre') }}"
                        >
                        @error('segundo_nombre')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="primer_apellido" class="block text-base font-bold text-gray-700 dark:text-gray-300 serif">
                            Primer Apellido <span class="text-red-500">*</span>
                        </label>
                        <input 
                            id="primer_apellido" 
                            name="primer_apellido" 
                            type="text" 
                            required 
                            data-validate="name"
                            class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200 serif text-sm"
                            placeholder="Ej. Anderson"
                            value="{{ old('primer_apellido') }}"
                        >
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1 hidden" data-client-error-for="primer_apellido"></p>
                        @error('primer_apellido')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="segundo_apellido" class="block text-base font-bold text-gray-700 dark:text-gray-300 serif">
                            Segundo Apellido
                        </label>
                        <input 
                            id="segundo_apellido" 
                            name="segundo_apellido" 
                            type="text" 
                            class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200 serif text-sm"
                            placeholder="Ej. Smith"
                            value="{{ old('segundo_apellido') }}"
                        >
                        @error('segundo_apellido')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="dni" class="block text-base font-bold text-gray-700 dark:text-gray-300 serif">
                            DNI <span class="text-red-500">*</span>
                        </label>
                        <input 
                            id="dni" 
                            name="dni" 
                            type="text" 
                            required 
                            data-validate="dni"
                            class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200 serif text-sm"
                            placeholder="Ej. 1234-2000-56789"
                            value="{{ old('dni') }}"
                        >
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1 hidden" data-client-error-for="dni"></p>
                        <p class="text-sm text-green-600 dark:text-green-400 mt-1 hidden" data-dni-success>
                            <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            DNI disponible
                        </p>
                        <p class="text-sm text-blue-600 dark:text-blue-400 mt-1 hidden" data-dni-loading>
                            <svg class="animate-spin w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Validando DNI...
                        </p>
                        @error('dni')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="id_genero_fk" class="block text-base font-bold text-gray-700 dark:text-gray-300 serif">
                            Género <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="id_genero_fk" 
                            name="id_genero_fk" 
                            required 
                            data-validate="select"
                            class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200 serif text-sm"
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

                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white serif">
                        Email de Contacto
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="space-y-1">
                            <label for="email_contacto" class="block text-base font-bold text-gray-700 dark:text-gray-300 serif">
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
                                        class="flex-1 px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200 serif text-sm"
                                        placeholder="ejemplo123@correo.com"
                                        value="{{ old('email_contacto') }}"
                                    >
                                    <button 
                                        type="button" 
                                        id="btn-enviar-codigo"
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed sm:w-auto w-full serif"
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
                            <label for="codigo_verificacion" class="block text-base font-bold text-gray-700 dark:text-gray-300 serif">
                                Código de Verificación <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <input 
                                    id="codigo_verificacion" 
                                    name="codigo_verificacion" 
                                    type="text" 
                                    maxlength="6"
                                    class="flex-1 px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition-colors duration-200 text-center text-sm tracking-widest serif"
                                    placeholder="000000"
                                >
                                <button 
                                    type="button" 
                                    id="btn-verificar-codigo"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed serif"
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
                            <span class="text-sm font-medium text-green-800 dark:text-green-200 serif">Email verificado correctamente</span>
                        </div>
                        
                        <input type="hidden" id="email_verificado" name="email_verificado" value="0">
                    </div>
                </div>

                <div class="pt-4">
                    <button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-blue-700 to-blue-800 hover:from-blue-800 hover:to-blue-900 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-[1.01] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        id="submit-btn"
                    >
                        <span class="flex items-center justify-center serif">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Completar mi Perfil
                        </span>
                    </button>
                </div>

                <div class="pt-1 border-t border-gray-200 dark:border-gray-700">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 text-center">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 serif">¿Tu cuenta pertenece a una empresa?</h3>
                        <p class="text-base text-gray-600 dark:text-gray-400 mb-3 serif">
                            Si representas a una empresa, puedes completar los datos corporativos como nombre comercial, RTN y logo empresarial.
                        </p>
                        <a href="{{ route('cliente.configurar-empresa') }}" 
                           class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-blue-200 dark:border-blue-600 rounded-md text-blue-700 dark:text-blue-300 font-medium hover:bg-blue-300 dark:hover:bg-blue-900/30 transition-colors duration-200 text-sm serif">
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
                                <h3 class="text-sm font-bold text-red-800 dark:text-red-200 serif">
                                    Error al completar el perfil
                                </h3>
                                <div class="mt-2 text-sm text-red-700 dark:text-red-300 serif">
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
        <p class="text-sm text-gray-500 dark:text-white serif">
            ¿Necesitas ayuda? <a href="mailto:edw.lagos@gmail.com" class="text-blue-600 dark:text-blue-400 hover:underline">Contacta soporte</a>
        </p>
    </div>
</div>

<script src="{{ asset('js/email-verification.js') }}" defer></script>
<script src="{{ asset('js/theme-toggle.js') }}" defer></script>
<script src="{{ asset('js/configurar-perfil.js') }}" defer></script>
@endsection
