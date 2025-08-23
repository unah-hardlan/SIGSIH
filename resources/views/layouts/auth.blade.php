<!DOCTYPE html>
<html lang="es" x-data="authPage()" x-init="init()" :class="{ 'dark': isDark }">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión – SIGSIH</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {}
            },
            variants: {
                extend: {}
            },
            plugins: []
        }
    </script>

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }
        
        [x-cloak] { display: none !important; }
    </style>

    @livewireStyles

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="/js/login-guard.js" defer></script>
    <script src="{{ Vite::asset('resources/js/toast.js') }}" defer></script>
</head>

<body class="min-h-screen transition-colors duration-300 bg-gray-50 dark:bg-gray-900">
    <div class="fixed top-4 right-4">
        <!-- Botón de modo claro/oscuro -->
        <button @click="toggleTheme()" class="p-2 rounded-full bg-white dark:bg-gray-800 shadow focus:outline-none">
            <i :class="isDark ? 'fas fa-sun text-yellow-400' : 'fas fa-moon text-gray-700'" class="w-6 h-6"></i>
        </button>
    </div>

    <div class="min-h-screen flex items-center justify-center">
        <div class="w-full max-w-sm">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-4 sm:p-6 transition-colors">
                <!-- LOGO -->
                <div class="text-center mb-6">
                    <div
                        class="inline-flex items-center justify-center w-36 h-36 rounded-full mb-3 bg-gray-100 dark:bg-white border-4 border-white dark:border-gray-200 transition-colors">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-32 h-32 object-contain">
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                        <span x-text="isLogin ? 'Bienvenido de nuevo' : 'Crear cuenta'"></span>
                    </h2>
                    <p class="text-gray-600 dark:text-gray-300 mt-2">
                        <span
                            x-text="isLogin ? 'Por favor inicia sesión para continuar' : 'Regístrate para comenzar'"></span>
                    </p>
                </div>

                <form @submit.prevent="handleSubmit" autocomplete="off">
                    <!-- Nombre de usuario (solo registro) -->
                    <div x-show="!isLogin" x-cloak class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nombre de
                            Usuario</label>
                        <input type="text" name="nombre_usuario" x-model="nombre_usuario" :required="!isLogin"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 focus:border-transparent transition-colors bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100"
                            placeholder="Juan Orlando" />
                    </div>

                    <!-- Usuario -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Usuario</label>
                        <input type="text" name="username" x-model="username" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 focus:border-transparent focus:border-gray-300 dark:focus:border-gray-600 transition-colors bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100"
                            placeholder="John Doe" />
                    </div>

                    <!-- Correo (solo registro) -->
                    <div x-show="!isLogin" x-cloak class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Correo
                            electrónico</label>
                        <input type="email" name="email" x-model="email" :required="!isLogin"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 focus:border-transparent transition-colors bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100"
                            placeholder="correo@ejemplo.com" />
                    </div>

                    <!-- Contraseña -->
                    <div class="mb-2">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contraseña</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password"
                                required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-60 focus:border-transparent transition-colors bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100"
                                placeholder="••••••••" />
                            <button type="button"
                                class="absolute right-3 top-3 text-gray-400 dark:text-gray-300 hover:text-gray-600"
                                @click="showPassword = !showPassword">
                                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="w-6 h-6"></i>
                            </button>
                        </div>
                        <p x-show="password && !validatePassword(password)" class="mt-2 text-sm text-red-600">
                            La contraseña debe tener al menos 8 caracteres
                        </p>
                    </div>

                    <!-- Recuperar contraseña (solo login) -->
                    <div x-show="isLogin" class="mb-6 text-right">
                        <button type="button" @click="handleRecover()"
                            class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 font-medium focus:outline-none">
                            ¿Olvidaste tu contraseña?
                        </button>
                    </div>

                    <!-- Confirmar contraseña (solo registro) -->
                    <div x-show="!isLogin" x-cloak class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirmar
                            Contraseña</label>
                        <div class="relative">
                            <input :type="showConfirmPassword ? 'text' : 'password'" name="confirmPassword"
                                x-model="confirmPassword" :required="!isLogin"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 focus:border-transparent transition-colors bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100"
                                placeholder="••••••••" />
                            <button type="button"
                                class="absolute right-3 top-3 text-gray-400 dark:text-gray-300 hover:text-gray-600"
                                @click="showConfirmPassword = !showConfirmPassword">
                                <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="w-6 h-6"></i>
                            </button>
                        </div>
                        <p x-show="confirmPassword && !validateConfirmPassword()" class="mt-2 text-sm text-red-600">
                            Las contraseñas no coinciden
                        </p>
                    </div>

                    <!-- Botón principal -->
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 focus:ring-4 focus:ring-blue-600 focus:ring-opacity-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="loading || (!username) || (password && !validatePassword(password)) || (!isLogin && confirmPassword && !validateConfirmPassword())">
                        <span x-show="loading" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2z" />
                            </svg>
                            Procesando...
                        </span>
                        <span x-show="!loading" x-text="isLogin ? 'Iniciar sesión' : 'Crear cuenta'"></span>
                    </button>

                    <!-- Separador -->
                    <div class="my-4 flex items-center">
                        <hr class="flex-grow border-gray-300 dark:border-gray-600" />
                        <span class="mx-2 text-gray-400 dark:text-gray-500">o</span>
                        <hr class="flex-grow border-gray-300 dark:border-gray-600" />
                    </div>

                    <!-- Botón Google -->
                    <button type="button" @click="handleGoogle()"
                        class="w-full bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 py-3 rounded-lg font-medium flex items-center justify-center transition-colors mb-4">
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 533.5 544.3">
                            <path fill="#4285F4"
                                d="M533.5 278.4c0-17.4-1.4-34.1-4-50.4H272v95.5h147.5c-6.4 34.7-25.5 64.1-54.5 83.8v69.7h87.9c51.6-47.6 81.6-117.8 81.6-198.6z" />
                            <path fill="#34A853"
                                d="M272 544.3c73.7 0 135.7-24.4 180.8-66.4l-87.9-69.7c-24.5 16.4-55.9 26-92.9 26-71.4 0-132-48.1-153.5-112.8H26.9v70.8C72 485.4 165.3 544.3 272 544.3z" />
                            <path fill="#FBBC05"
                                d="M118.5 324.6c-10.8-32.4-10.8-67.4 0-99.8V154h-91.6C8.6 204.5 0 238.8 0 272c0 33.2 8.6 67.5 26.9 99.9l91.6-47.3z" />
                            <path fill="#EA4335"
                                d="M272 107.7c39.9 0 75.7 13.7 104 40.5l78-78C409.3 24.6 345.3 0 272 0 165.3 0 72 58.9 26.9 154l91.6 70.8C140 155.8 200.6 107.7 272 107.7z" />
                        </svg>
                        Iniciar sesión con Google
                    </button>

                    <!-- Switch registro/login -->
                    <p class="mt-2 text-center text-gray-600 dark:text-gray-400">
                        <span x-text="isLogin ? '¿No tienes una cuenta?' : '¿Ya tienes cuenta?'"></span>
                        <button type="button"
                            class="ml-1 text-green-600 dark:text-green-400 hover:text-green-700 font-semibold"
                            @click="isLogin = !isLogin">
                            <span x-text="isLogin ? 'Regístrate' : 'Inicia sesión'"></span>
                        </button>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <!-- Tu script de lógica -->
    <script src="{{ asset('js/auth.js') }}"></script>
    @livewireScripts
</body>

</html>