<!DOCTYPE html>
<html lang="es" x-data="passwordRecoverPage()" x-init="init()" :class="{ 'dark': isDark }">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/theme.css', 'resources/css/global.css', 'resources/css/app.css'])
    <title>Recuperar Contraseña – SIGSIH</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ Vite::asset('resources/js/toast.js') }}" defer></script>
</head>

<body class="min-h-screen transition-colors duration-300 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-950">
        <div class="fixed top-4 right-4">
            <label @click.prevent="toggleTheme()" class="switch cursor-pointer rounded-full border border-gray-300 dark:border-gray-500">
                <input type="checkbox" class="hidden" :checked="isDark">
                <span class="slider"></span>
            </label>
        </div>

        <div class="w-full max-w-sm mx-auto">
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4 transition-colors shadow-lg">
                <div class="text-center mb-4">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-2 bg-gray-100 dark:bg-gray-700 border-2 border-white dark:border-gray-500 transition-colors">
                        <img src="{{ $appLogoUrl ?? asset('images/logo.png') }}" alt="Logo" class="app-logo" style="--app-logo-max: {{ ($appLogoHeight ?? 96) }}px;">
                    </div>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 serif-boldy">
                        Encuentra tu cuenta
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 nunito-regular">
                        Ingresa tu correo electrónico o nombre de usuario para buscar tu cuenta.
                    </p>
                </div>

                <form @submit.prevent="handleSearch" autocomplete="off" x-show="currentStep === 'search'">
                    <div class="mb-4">
                        <input type="text" name="recoverEmail" x-model="recoverEmail" required
                            class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 focus:border-transparent transition-colors bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 nunito-regular text-xs"
                            placeholder="Correo electrónico o nombre de usuario" />
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('login') }}"
                            class="flex-1 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 py-2 rounded font-semibold hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors nunito-regular text-sm text-center">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="flex-1 bg-blue-600 text-white py-2 rounded font-semibold hover:bg-blue-700 focus:ring-2 focus:ring-blue-600 focus:ring-opacity-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed nunito-regular text-sm"
                            :disabled="loading || !recoverEmail">
                            <span x-show="loading" class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2z" />
                                </svg>
                                Buscando...
                            </span>
                            <span x-show="!loading">Buscar</span>
                        </button>
                    </div>
                </form>

                <!-- Pantalla 2: Cuenta encontrada -->
                <div x-show="currentStep === 'confirm'" x-transition>
                    <div class="text-center mb-4">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-3 bg-green-100 dark:bg-green-900">
                            <i class="fas fa-user-check text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 serif-boldy mb-2">
                            Cuenta encontrada
                        </h3>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-4">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400 nunito-regular">Usuario:</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-100" x-text="foundAccount?.usuario"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400 nunito-regular">Nombre:</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-100" x-text="foundAccount?.nombre_completo || 'Sin especificar'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400 nunito-regular">Email:</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-100" x-text="foundAccount?.email"></span>
                            </div>
                        </div>
                    </div>

                    <p class="text-xs text-gray-600 dark:text-gray-300 mb-4 nunito-regular text-center">
                        ¿Es esta tu cuenta? Enviaremos las instrucciones de recuperación al correo registrado.
                    </p>

                    <div class="flex gap-2">
                        <button @click="currentStep = 'search'; foundAccount = null"
                            class="flex-1 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 py-2 rounded font-semibold hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors nunito-regular text-sm">
                            No es mi cuenta
                        </button>
                        <button @click="handleSendEmail"
                            class="flex-1 bg-blue-600 text-white py-2 rounded font-semibold hover:bg-blue-700 focus:ring-2 focus:ring-blue-600 focus:ring-opacity-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed nunito-regular text-sm"
                            :disabled="loading">
                            <span x-show="loading" class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2z" />
                                </svg>
                                Enviando...
                            </span>
                            <span x-show="!loading">Enviar instrucciones</span>
                        </button>
                    </div>
                </div>

                <!-- Pantalla 3: Confirmación de envío -->
                <div x-show="currentStep === 'success'" x-transition class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4 bg-green-100 dark:bg-green-900">
                        <i class="fas fa-envelope-circle-check text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 serif-boldy mb-2">
                        Instrucciones enviadas
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 nunito-regular">
                        Se han enviado las instrucciones de recuperación a:
                    </p>
                    <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-3 mb-4">
                        <p class="font-semibold text-blue-800 dark:text-blue-200 text-sm" x-text="sentToEmail"></p>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-6 nunito-regular">
                        Revisa tu bandeja de entrada y la carpeta de spam. El enlace expirará en 24 horas.
                    </p>
                    <a href="{{ route('login') }}"
                        class="inline-block bg-blue-600 text-white px-6 py-2 rounded font-semibold hover:bg-blue-700 focus:ring-2 focus:ring-blue-600 focus:ring-opacity-50 transition-colors nunito-regular text-sm">
                        Volver al inicio de sesión
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.passwordRecoverPage = function () {
            return {
                recoverEmail: '',
                loading: false,
                isDark: false,
                currentStep: 'search', // 'search', 'confirm', 'success'
                foundAccount: null,
                sentToEmail: '',
                init() {
                    // Tema almacenado
                    this.isDark = localStorage.getItem('theme') === 'dark';
                    
                    // Configurar CSRF token para axios
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    if (token) {
                        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
                    }
                },
                toggleTheme() {
                    this.isDark = !this.isDark;
                    localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                },
                async handleSearch() {
                    if (!this.recoverEmail) return;
                    
                    this.loading = true;
                    try {
                        const response = await axios.post('/password/search', {
                            email: this.recoverEmail
                        });
                        
                        if (response.data.found) {
                            this.foundAccount = response.data.account;
                            this.currentStep = 'confirm';
                        }
                        
                    } catch (error) {
                        let message = 'Error al buscar la cuenta';
                        
                        if (error.response?.status === 404) {
                            message = error.response.data.message || 'No se encontró ninguna cuenta con esos datos.';
                        } else if (error.response?.data?.message) {
                            message = error.response.data.message;
                        }
                        
                        if (window.showToast) {
                            window.showToast(message, 'error');
                        } else {
                            alert(message);
                        }
                    } finally {
                        this.loading = false;
                    }
                },
                async handleSendEmail() {
                    if (!this.foundAccount) return;
                    
                    this.loading = true;
                    try {
                        const response = await axios.post('/password/email', {
                            user_id: this.foundAccount.id
                        });
                        
                        this.sentToEmail = response.data.email || this.foundAccount.email;
                        this.currentStep = 'success';
                        
                    } catch (error) {
                        const message = error.response?.data?.message || 'Error al enviar las instrucciones';
                        if (window.showToast) {
                            window.showToast(message, 'error');
                        } else {
                            alert(message);
                        }
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
    @livewireScripts
</body>

</html>