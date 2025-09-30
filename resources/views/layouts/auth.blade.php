<!DOCTYPE html>
<html lang="es" x-data="authPage()" x-init="init()" :class="{ 'dark': isDark }">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/theme.css', 'resources/css/global.css', 'resources/css/app.css'])
    <title>Iniciar Sesión – SIGSIH</title>

    <style>
        [x-cloak] {
            display: none !important
        }
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    @livewireStyles

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        // Pre-render theme script - Se ejecuta inmediatamente para evitar flash
        (function() {
            try {
                const saved = localStorage.getItem('theme');
                const isDark = saved ? saved === 'dark' : (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
                if (isDark) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (_) {}
        })();
        
        // Fallback auth component para casos donde auth.js no carga
        document.addEventListener('alpine:init', () => {
            if (!window.authPage) {
                console.warn('authPage not loaded, creating fallback');
                window.authPage = () => ({
                    isLogin: true,
                    showPassword: false,
                    showConfirmPassword: false,
                    username: "",
                    password: "",
                    confirmPassword: "",
                    nombre_usuario: "",
                    email: "",
                    loading: false,
                    isDark: false,
                    formError: "",
                    fieldErrors: {},
                    show2FAModal: false,
                    totpCode: "",
                    verifying2FA: false,
                    totpError: "",
                    
                    init() {
                        this.initTheme();
                    },
                    
                    initTheme() {
                        try {
                            // Leer el estado actual del DOM en lugar de recalcular
                            this.isDark = document.documentElement.classList.contains('dark');
                            // Solo aplicar si hay discrepancia (por seguridad)
                            document.documentElement.classList.toggle("dark", this.isDark);
                        } catch (_) {}
                    },
                    
                    toggleTheme() {
                        this.isDark = !this.isDark;
                        document.documentElement.classList.toggle("dark", this.isDark);
                        try {
                            localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                        } catch (_) {}
                    },
                    
                    switchMode() {
                        this.isLogin = !this.isLogin;
                        this.formError = "";
                        this.fieldErrors = {};
                        this.password = "";
                        this.confirmPassword = "";
                    },
                    
                    resetErrors() {
                        this.formError = "";
                        this.fieldErrors = {};
                    },
                    
                    clearFieldError(field) {
                        if (this.fieldErrors[field]) {
                            delete this.fieldErrors[field];
                        }
                    },
                    
                    passwordIssues(pw) {
                        const value = pw || "";
                        const issues = [];
                        if (value.length < 8) issues.push("Debe tener al menos 8 caracteres.");
                        if (/\s/.test(value)) issues.push("No debe contener espacios.");
                        if (!this.isLogin && !/[A-Z]/.test(value)) issues.push("Debe incluir al menos una letra mayúscula.");
                        return issues;
                    },
                    
                    validatePassword(pw) {
                        return this.passwordIssues(pw).length === 0;
                    },
                    
                    validateConfirmPassword() {
                        return this.password === this.confirmPassword;
                    },
                    
                    async handleSubmit() {
                        if (this.loading) return;
                        this.loading = true;
                        this.resetErrors();
                        
                        try {
                            if (this.isLogin) {
                                const res = await axios.post("/api/login", {
                                    usuario: this.username,
                                    contrasena: this.password,
                                });
                                window.location.assign("/admin/dashboard");
                            } else {
                                await axios.post("/api/register", {
                                    usuario: this.username,
                                    nombre_usuario: this.nombre_usuario,
                                    correo_electronico: this.email,
                                    contrasena: this.password,
                                });
                                window.location.assign("/admin/perfil");
                            }
                        } catch (err) {
                            const resp = err?.response;
                            if (resp?.status === 422) {
                                this.fieldErrors = resp.data?.errors || {};
                                this.formError = resp.data?.message || "Hay errores de validación.";
                            } else {
                                this.formError = resp?.data?.error || resp?.data?.message || "Error de autenticación";
                            }
                        } finally {
                            this.loading = false;
                        }
                    },
                    
                    handleGoogle() {
                        alert("Redirigiendo a Google Sign-In…");
                    }
                });
            }
            
            Alpine.data('authPage', window.authPage);
        });
    </script>
    
    <script src="{{ Vite::asset('resources/js/auth.js') }}" defer></script>
    
    <script src="/js/login-guard.js" defer></script>
    <script src="{{ Vite::asset('resources/js/toast.js') }}" defer></script>

</head>

<body class="min-h-screen transition-colors duration-300 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-950">
        <div class="fixed top-4 right-4">
            <label @click.prevent="toggleTheme()"
                class="switch cursor-pointer rounded-full border border-gray-300 dark:border-gray-500">
                <input type="checkbox" class="hidden" :checked="isDark">
                <span class="slider"></span>
            </label>
        </div>

        <div class="w-full max-w-sm mx-auto">
            <div
                class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4 transition-colors shadow-lg">
                <div class="text-center mb-4">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-2 bg-gray-100 dark:bg-white border-2 border-white dark:border-gray-500 transition-colors">
                        <img src="{{ $appLogoUrl ?? asset('images/logo.png') }}" alt="Logo" class="app-logo"
                            style="--app-logo-max: {{ ($appLogoHeight ?? 96) }}px;">
                    </div>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 serif-boldy">
                        <span x-text="isLogin ? 'Bienvenido de nuevo' : 'Crear cuenta'">Bienvenido de nuevo</span>
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 nunito-regular">
                        <span x-text="isLogin ? 'Por favor inicia sesión para continuar' : 'Completa tus datos'">Por
                            favor inicia sesión para continuar</span>
                    </p>
                </div>

                <template x-if="formError">
                    <div
                        class="mb-3 px-3 py-2 rounded border border-red-200 bg-red-50 text-red-700 dark:bg-red-900/30 dark:border-red-500 dark:text-red-200 text-xs nunito-regular">
                        <i class="fas fa-circle-exclamation mr-1"></i>
                        <span x-text="formError"></span>
                    </div>
                </template>

                <form @submit.prevent="handleSubmit" autocomplete="off">
                    <div x-show="!isLogin" x-cloak class="grid grid-cols-1 gap-y-2">
                        <div class="mb-2">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-regular">Nombre
                                de Usuario</label>
                            <input type="text" name="nombre_usuario" x-model="nombre_usuario" :required="!isLogin"
                                pattern="^[A-Za-z0-9]+$"
                                title="Sólo letras y números"
                                @input="handleNombreUsuarioInput"
                                class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 focus:border-transparent transition-colors bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 nunito-regular text-xs"
                                :class="{ 'border-red-500 focus:border-red-500': fieldErrors.nombre_usuario }"
                                placeholder="Usuario" />
                            <template x-if="fieldErrors.nombre_usuario">
                                <p class="mt-1 text-xs text-red-600 dark:text-red-300 nunito-regular"
                                    x-text="fieldErrors.nombre_usuario[0]"></p>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-regular">Correo
                                electrónico</label>
                            <input type="email" name="email" x-model="email" :required="!isLogin"
                                @input="clearFieldError('correo_electronico')"
                                class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 focus:border-transparent transition-colors bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 nunito-regular text-xs"
                                :class="{ 'border-red-500 focus:border-red-500': fieldErrors.correo_electronico }"
                                placeholder="correo@ejemplo.com" />
                            <template x-if="fieldErrors.correo_electronico">
                                <p class="mt-1 text-xs text-red-600 dark:text-red-300 nunito-regular"
                                    x-text="fieldErrors.correo_electronico[0]"></p>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-regular">Contraseña</label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password"
                                    :required="!isLogin" maxlength="100" pattern="^(?=.*[A-Z])\S{8,100}$"
                                    title="Mínimo 8 caracteres, sin espacios y al menos una letra mayúscula"
                                    @input="clearFieldError('contrasena')"
                                    class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 focus:border-transparent transition-colors bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 nunito-regular text-xs"
                                    :class="{ 'border-red-500 focus:border-red-500': fieldErrors.contrasena }"
                                    placeholder="••••••••" />
                                <button type="button"
                                    class="absolute right-2 top-2 text-gray-400 dark:text-gray-300 hover:text-gray-600 text-xs"
                                    @click="showPassword = !showPassword">
                                    <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="w-4 h-4"></i>
                                </button>
                            </div>
                            <template x-if="password">
                                <ul x-show="passwordIssues(password).length"
                                    class="mt-1 text-xs text-red-600 nunito-regular space-y-1">
                                    <template x-for="issue in passwordIssues(password)" :key="issue">
                                        <li class="flex items-center gap-1">
                                            <i class="fas fa-exclamation-circle text-[10px]"></i>
                                            <span x-text="issue"></span>
                                        </li>
                                    </template>
                                </ul>
                            </template>
                            <template x-if="fieldErrors.contrasena">
                                <p class="mt-1 text-xs text-red-600 dark:text-red-300 nunito-regular"
                                    x-text="fieldErrors.contrasena[0]"></p>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-regular">Confirmar
                                Contraseña</label>
                            <div class="relative">
                                <input :type="showConfirmPassword ? 'text' : 'password'" name="confirmPassword"
                                    x-model="confirmPassword" :required="!isLogin" maxlength="100"
                                    class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 focus:border-transparent transition-colors bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 nunito-regular text-xs"
                                    placeholder="••••••••" />
                                <button type="button"
                                    class="absolute right-2 top-2 text-gray-400 dark:text-gray-300 hover:text-gray-600 text-xs"
                                    @click="showConfirmPassword = !showConfirmPassword">
                                    <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"
                                        class="w-4 h-4"></i>
                                </button>
                            </div>
                            <p x-show="confirmPassword && !validateConfirmPassword()"
                                class="mt-1 text-xs text-red-600 nunito-regular">
                                Las contraseñas no coinciden
                            </p>
                        </div>
                    </div>

                    <div :class="{ 'mb-4': isLogin, 'mb-2': !isLogin }">
                        <label
                            class="block text-sm font-medium  text-gray-700 dark:text-gray-300 mb-1 nunito-regular">Usuario</label>
                        <input type="text" name="username" x-model="username" required maxlength="50" pattern="^[A-Za-z0-9]+$"
                            @input="handleUsernameInput"
                            title="Sólo letras y números"
                            class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 focus:border-transparent transition-colors bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 nunito-regular text-xs"
                            :class="{ 'border-red-500 focus:border-red-500': fieldErrors.usuario }"
                            placeholder="John Doe" />
                        <template x-if="fieldErrors.usuario">
                            <p class="mt-1 text-xs text-red-600 dark:text-red-300 nunito-regular"
                                x-text="fieldErrors.usuario[0]"></p>
                        </template>
                    </div>

                    <div x-show="isLogin" class="mb-2">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-regular">Contraseña</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password"
                                required maxlength="100" pattern="^\S{8,100}$" title="Mínimo 8 caracteres, sin espacios"
                                @input="clearFieldError('contrasena')"
                                class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 focus:border-transparent transition-colors bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 nunito-regular text-xs"
                                :class="{ 'border-red-500 focus:border-red-500': fieldErrors.contrasena }"
                                placeholder="••••••••" />
                            <button type="button"
                                class="absolute right-2 top-2 text-gray-400 dark:text-gray-300 hover:text-gray-600 text-xs"
                                @click="showPassword = !showPassword">
                                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="w-4 h-4"></i>
                            </button>
                        </div>
                        <p x-show="password && !validatePassword(password)"
                            class="mt-1 text-xs text-red-600 nunito-regular">
                            Mínimo 8 caracteres, sin espacios
                        </p>
                        <template x-if="fieldErrors.contrasena">
                            <p class="mt-1 text-xs text-red-600 dark:text-red-300 nunito-regular"
                                x-text="fieldErrors.contrasena[0]"></p>
                        </template>
                    </div>

                    <div x-show="isLogin" class="mb-4 text-right">
                        <a href="{{ route('password.request') }}"
                            class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 font-medium focus:outline-none nunito-regular">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 rounded font-semibold hover:bg-blue-700 focus:ring-2 focus:ring-blue-600 focus:ring-opacity-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed nunito-regular text-sm"
                        :disabled="loading || (!username) || (password && !validatePassword(password)) || (!isLogin && confirmPassword && !validateConfirmPassword())">
                        <span x-show="loading" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2z" />
                            </svg>
                            Procesando...
                        </span>
                        <span x-show="!loading" x-text="isLogin ? 'Iniciar sesión' : 'Crear cuenta'">Iniciar
                            sesión</span>
                    </button>

                    <div class="my-3 flex items-center">
                        <hr class="flex-grow border-gray-300 dark:border-gray-600" />
                        <span class="mx-2 text-xs text-gray-400 dark:text-gray-500 nunito-regular">o</span>
                        <hr class="flex-grow border-gray-300 dark:border-gray-600" />
                    </div>
                    
                    <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400 nunito-regular">
                        <span x-text="isLogin ? '¿No tienes una cuenta?' : '¿Ya tienes cuenta?'">¿No tienes una
                            cuenta?</span>
                        <button type="button"
                            class="ml-1 text-green-600 dark:text-green-400 hover:text-green-700 font-semibold"
                            @click="switchMode()">
                            <span x-text="isLogin ? 'Regístrate' : 'Inicia sesión'">Regístrate</span>
                        </button>
                    </p>
                </form>
            </div>
        </div>
    </div>

    @livewireScripts

    <div x-show="show2FAModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div
            class="w-full max-w-sm bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-xl">
            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">Verificación en dos pasos</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                Abre tu app de autenticación (Google Authenticator, Microsoft Authenticator, Authy)
                e ingresa el código de 6 dígitos. También puedes usar un código de recuperación.
            </p>
            <div class="mt-3">
                <input type="text" inputmode="numeric" pattern="^\\d{6}$" maxlength="10" x-model="totpCode"
                    class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 text-sm"
                    placeholder="Código de 6 dígitos o recuperación" />
                <p x-show="totpError" class="mt-1 text-xs text-red-600" x-text="totpError"></p>
            </div>
            <div class="mt-4 flex items-center gap-2">
                <button type="button" @click="submit2FA" :disabled="verifying2FA || !totpCode"
                    class="flex-1 bg-blue-600 text-white py-2 rounded font-semibold hover:bg-blue-700 disabled:opacity-50 text-sm">
                    <span x-show="verifying2FA" class="inline-flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2z" />
                        </svg>
                        Verificando...
                    </span>
                    <span x-show="!verifying2FA">Verificar</span>
                </button>
                <button type="button" @click="close2FA"
                    class="px-3 py-2 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 text-sm">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</body>

</html>