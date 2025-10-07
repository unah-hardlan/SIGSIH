<!DOCTYPE html>
<html lang="es" x-data="passwordResetPage({ token: '{{ $token }}', email: '{{ $email }}' })">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/theme.css', 'resources/css/global.css', 'resources/css/app.css'])
    <title>Restablecer Contraseña – SIGSIH</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ Vite::asset('resources/js/toast.js') }}" defer></script>

    <!-- Dark mode logic removed -->

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="min-h-screen transition-colors duration-300 bg-gray-50 text-gray-800">
    <div class="min-h-screen flex flex-col items-center justify-center p-4 bg-gray-50">
        <!-- Dark mode toggle removed -->

        <div class="w-full max-w-sm mx-auto">
            <div class="bg-white rounded-lg border border-gray-600 p-4 transition-colors">
                <div class="text-center mb-4">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-2 bg-gray-100 border-2 border-white transition-colors">
                        <img src="{{ $appLogoUrl ?? asset('images/logo.png') }}" alt="Logo" class="app-logo" style="--app-logo-max: {{ ($appLogoHeight ?? 96) }}px;">
                    </div>
                    <h2 class="text-lg font-bold text-gray-800 serif-boldy">
                        Restablece tu contraseña
                    </h2>
                    <p class="text-sm text-gray-600 mt-1 nunito-regular">
                        Crea una nueva contraseña para ingresar nuevamente a SIGSIH.
                    </p>
                </div>

                <form @submit.prevent="handleReset" autocomplete="off">
                    <input type="hidden" name="token" :value="token">

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1 nunito-regular">Correo electrónico</label>
                        <input type="email" name="email" x-model="email" required
                            @input="clearFieldError('email')"
                            class="auth-input w-full px-3 py-2 rounded border transition-colors bg-gray-100 text-gray-800 nunito-regular text-xs"
                            :class="{ 'border-red-500 focus:border-red-500': fieldErrors.email || (email && !validateEmail(email)) }"
                            placeholder="tu@correo.com">
                        <!-- Error del servidor -->
                        <template x-if="fieldErrors.email">
                            <p class="mt-1 text-xs text-red-600 nunito-regular"
                                x-text="fieldErrors.email[0]"></p>
                        </template>
                        <!-- Validación en tiempo real -->
                        <template x-if="email && !fieldErrors.email && emailIssues(email).length > 0">
                            <ul class="mt-1 text-xs nunito-regular space-y-1 validation-error">
                                <template x-for="issue in emailIssues(email)" :key="issue">
                                    <li class="flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle text-[10px]"></i>
                                        <span x-text="issue"></span>
                                    </li>
                                </template>
                            </ul>
                        </template>
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1 nunito-regular">Nueva contraseña</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password" required maxlength="100"
                                @input="clearFieldError('password')"
                                class="auth-input w-full px-3 py-2 rounded border transition-colors bg-white text-gray-800 nunito-regular text-xs"
                                :class="{ 'border-red-500 focus:border-red-500': fieldErrors.password || (password && !validatePassword(password)) }"
                                placeholder="Ingresa la nueva contraseña">
                            <button type="button"
                                class="absolute right-2 top-2 text-gray-400 hover:text-gray-600 text-xs"
                                @click="showPassword = !showPassword">
                                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="w-4 h-4"></i>
                            </button>
                        </div>
                        <template x-if="password">
                            <ul x-show="passwordIssues(password).length"
                                class="mt-1 text-xs nunito-regular space-y-1 validation-error">
                                <template x-for="issue in passwordIssues(password)" :key="issue">
                                    <li class="flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle text-[10px]"></i>
                                        <span x-text="issue"></span>
                                    </li>
                                </template>
                            </ul>
                        </template>
                        <!-- Error del servidor -->
                        <template x-if="fieldErrors.password">
                            <p class="mt-1 text-xs text-red-600 nunito-regular"
                                x-text="fieldErrors.password[0]"></p>
                        </template>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1 nunito-regular">Confirmar contraseña</label>
                        <div class="relative">
                            <input :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" x-model="passwordConfirmation" required maxlength="100"
                                class="auth-input w-full px-3 py-2 rounded border transition-colors bg-white text-gray-800 nunito-regular text-xs"
                                :class="{ 'border-red-500 focus:border-red-500': passwordConfirmation && !validateConfirmPassword() }"
                                placeholder="Confirma la nueva contraseña">
                            <button type="button"
                                class="absolute right-2 top-2 text-gray-400 hover:text-gray-600 text-xs"
                                @click="showConfirmPassword = !showConfirmPassword">
                                <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="w-4 h-4"></i>
                            </button>
                        </div>
                        <!-- Validación en tiempo real -->
                        <template x-if="passwordConfirmation && confirmPasswordIssues().length > 0">
                            <ul class="mt-1 text-xs nunito-regular space-y-1 validation-error">
                                <template x-for="issue in confirmPasswordIssues()" :key="issue">
                                    <li class="flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle text-[10px]"></i>
                                        <span x-text="issue"></span>
                                    </li>
                                </template>
                            </ul>
                        </template>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('login') }}"
                            class="flex-1 bg-gray-300 text-gray-700 py-2 rounded font-semibold hover:bg-gray-400 transition-colors nunito-regular text-sm text-center">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="flex-1 bg-blue-600 text-white py-2 rounded font-semibold hover:bg-blue-700 focus:ring-2 focus:ring-blue-600 focus:ring-opacity-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed nunito-regular text-sm"
                            :disabled="loading || !formValid">
                            <span x-show="loading" class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2z" />
                                </svg>
                                Guardando...
                            </span>
                            <span x-show="!loading">Restablecer</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const PASSWORD_RESET_LOGIN_URL = "{{ route('login') }}";
        window.passwordResetPage = function (initial) {
            return {
                token: initial.token,
                email: initial.email ?? '',
                password: '',
                passwordConfirmation: '',
                loading: false,
                showPassword: false,
                showConfirmPassword: false,
                fieldErrors: {},
                formValid() {
                    return this.email && this.password && this.passwordConfirmation && this.password === this.passwordConfirmation;
                },
                init() {},
                clearFieldError(field) {
                    if (this.fieldErrors[field]) {
                        delete this.fieldErrors[field];
                    }
                },
                emailIssues(email) {
                    const value = email || "";
                    const issues = [];
                    if (value.length === 0) {
                        issues.push("El correo electrónico es requerido.");
                    } else {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(value)) {
                            issues.push("Ingresa un correo electrónico válido.");
                        }
                    }
                    return issues;
                },
                validateEmail(email) {
                    return this.emailIssues(email).length === 0;
                },
                passwordIssues(pw) {
                    const value = pw || "";
                    const issues = [];
                    if (value.length < 8) issues.push("Debe tener al menos 8 caracteres.");
                    if (/\s/.test(value)) issues.push("No debe contener espacios.");
                    if (!/[A-Z]/.test(value)) issues.push("Debe incluir al menos una letra mayúscula.");
                    return issues;
                },
                validatePassword(pw) {
                    return this.passwordIssues(pw).length === 0;
                },
                confirmPasswordIssues() {
                    const issues = [];
                    if (this.passwordConfirmation.length === 0) {
                        issues.push("Debes confirmar tu contraseña.");
                    } else if (this.passwordConfirmation.length > 0 && this.password !== this.passwordConfirmation) {
                        issues.push("Las contraseñas no coinciden.");
                    }
                    return issues;
                },
                validateConfirmPassword() {
                    return this.confirmPasswordIssues().length === 0;
                },
                async handleReset() {
                    if (!this.formValid()) return;
                    this.loading = true;
                    try {
                        await axios.post('/password/reset', {
                            token: this.token,
                            email: this.email,
                            password: this.password,
                            password_confirmation: this.passwordConfirmation
                        });
                        if (window.showToast) {
                            window.showToast('Tu contraseña se restableció correctamente. Ahora puedes iniciar sesión.', 'success');
                        } else {
                            alert('Tu contraseña se restableció correctamente. Ahora puedes iniciar sesión.');
                        }
                        setTimeout(() => {
                            window.location.href = PASSWORD_RESET_LOGIN_URL;
                        }, 2000);
                    } catch (error) {
                        const resp = error?.response;
                        if (resp?.status === 422) {
                            this.fieldErrors = resp.data?.errors || {};
                            const message = resp.data?.message || 'Hay errores de validación.';
                            if (window.showToast) {
                                window.showToast(message, 'error');
                            }
                        } else {
                            const message = resp?.data?.message || 'No se pudo restablecer la contraseña';
                            if (window.showToast) {
                                window.showToast(message, 'error');
                            } else {
                                alert(message);
                            }
                        }
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>
    @livewireScripts
</body>

</html>
