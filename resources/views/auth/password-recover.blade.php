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
                    <div x-show="statusMessage" x-transition
                        class="mt-3 text-xs font-semibold px-3 py-2 rounded-md"
                        :class="statusType === 'success'
                            ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200'
                            : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200'">
                        <span x-text="statusMessage"></span>
                    </div>
                </div>

                <form @submit.prevent="handleRecover" autocomplete="off">
                    <div class="mb-4">
                        <input type="text" name="recoverIdentifier" x-model="recoverIdentifier" required
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
                            :disabled="loading || !recoverIdentifier">
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
                            <span x-show="!loading">Buscar</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        window.passwordRecoverPage = function() {
            return {
                recoverIdentifier: '',
                loading: false,
                isDark: false,
                statusMessage: '',
                statusType: 'info',
                init() {
                    // Tema almacenado
                    this.isDark = localStorage.getItem('theme') === 'dark';
                },
                toggleTheme() {
                    this.isDark = !this.isDark;
                    localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                },
                async handleRecover() {
                    if (!this.recoverIdentifier) return;

                    this.statusMessage = '';
                    this.loading = true;
                    try {
                        const response = await axios.post('/password/email', {
                            identifier: this.recoverIdentifier
                        });

                        const successMessage = response.data?.message || 'Si la cuenta existe, encontrarás las instrucciones en tu bandeja de entrada.';

                        if (window.showToast) {
                            window.showToast(successMessage, 'success');
                        } else {
                            alert(successMessage);
                        }

                        this.statusType = 'success';
                        this.statusMessage = successMessage;

                    } catch (error) {
                        const message = error.response?.data?.message || 'Error al enviar las instrucciones';
                        if (window.showToast) {
                            window.showToast(message, 'error');
                        } else {
                            alert(message);
                        }
                        this.statusType = 'error';
                        this.statusMessage = message;
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