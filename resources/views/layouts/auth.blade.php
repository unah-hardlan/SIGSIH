<!DOCTYPE html>
<html lang="es" x-data="authPage()" x-init="init()" class="dark" :class="{ 'dark': true }">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/theme.css', 'resources/css/global.css', 'resources/css/app.css', 'resources/css/auth.css'])
    <title>Iniciar Sesión – Hardlan</title>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
    (function() {
        try {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } catch (_) {}
    })();

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
                needsRecovery: false,
                showVerifyEmailModal: false,
                verifyEmailMessage: "",
                verifyEmailAddress: "",
                resendCooldown: 0,
                resendTimerId: null,
                showCloseTabScreen: false,
                showAwaitVerificationScreen: false,

                init() {
                    this.initTheme();
                    this.initEmailVerifiedListener();
                },

                initTheme() {
                    try {
                        this.isDark = document.documentElement.classList.contains('dark');
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
                    this.needsRecovery = false;
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

                usernameIssues(username) {
                    const value = username || "";
                    const issues = [];
                    if (value.length === 0) {
                        issues.push("El usuario es requerido.");
                    } else if (!/^[A-Za-z0-9]+$/.test(value)) {
                        issues.push("Solo se permiten letras y números, sin espacios ni símbolos.");
                    } else if (value.length > 50) {
                        issues.push("Máximo 50 caracteres permitidos.");
                    }
                    return issues;
                },

                validateUsername(username) {
                    return this.usernameIssues(username).length === 0;
                },

                nombreUsuarioIssues(nombre) {
                    const value = nombre || "";
                    const issues = [];
                    if (!this.isLogin && value.length === 0) {
                        issues.push("El nombre de usuario es requerido.");
                    } else if (value.length > 0 && !/^[A-Za-z0-9]+$/.test(value)) {
                        issues.push("Solo se permiten letras y números, sin espacios ni símbolos.");
                    }
                    return issues;
                },

                validateNombreUsuario(nombre) {
                    return this.nombreUsuarioIssues(nombre).length === 0;
                },

                emailIssues(email) {
                    const value = email || "";
                    const issues = [];
                    if (!this.isLogin && value.length === 0) {
                        issues.push("El correo electrónico es requerido.");
                    } else if (value.length > 0) {
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

                confirmPasswordIssues() {
                    const issues = [];
                    if (!this.isLogin && this.confirmPassword.length === 0) {
                        issues.push("Debes confirmar tu contraseña.");
                    } else if (this.confirmPassword.length > 0 && this.password !== this
                        .confirmPassword) {
                        issues.push("Las contraseñas no coinciden.");
                    }
                    return issues;
                },

                passwordIssues(pw) {
                    const value = pw || "";
                    const issues = [];
                    if (value.length < 8) issues.push("Debe tener al menos 8 caracteres.");
                    if (/\s/.test(value)) issues.push("No debe contener espacios.");
                    if (!this.isLogin && !/[A-Z]/.test(value)) issues.push(
                        "Debe incluir al menos una letra mayúscula.");
                    return issues;
                },

                validatePassword(pw) {
                    return this.passwordIssues(pw).length === 0;
                },

                validateConfirmPassword() {
                    return this.confirmPasswordIssues().length === 0;
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
                            const data = res?.data || {};
                            if (data.status === 'email_verification_required') {
                                this.verifyEmailAddress = (data && data.email) ? data.email : (this
                                    .email || this.username);
                                this.verifyEmailMessage = data.message ||
                                    'Debes verificar tu correo antes de iniciar sesión.';
                                this.showVerifyEmailModal = true;
                                this.loading = false;
                                return;
                            }
                            if (data.status === '2fa_required') {
                                this.totpCode = "";
                                this.totpError = "";
                                this.needsRecovery = false;
                                this.show2FAModal = true;
                                this.loading = false;
                                return;
                            }
                            try {
                                window.showToast && window.showToast('Sesión iniciada', 'success', {
                                    duration: 1200
                                });
                            } catch (_) {}
                            window.location.assign("/admin/dashboard");
                            return;
                        } else {
                            const regRes = await axios.post("/api/register", {
                                usuario: this.username,
                                nombre_usuario: this.nombre_usuario,
                                correo_electronico: this.email,
                                contrasena: this.password,
                            });
                            const regData = regRes?.data || {};
                            if (regData.status === 'verification_sent') {
                                this.verifyEmailAddress = this.email;
                                this.verifyEmailMessage =
                                    'Te enviamos un correo para verificar tu cuenta. Revisa tu bandeja de entrada o spam.';
                                this.showVerifyEmailModal = true;
                                this.loading = false;
                                return;
                            }
                            window.location.assign("/admin/perfil");
                        }
                    } catch (err) {
                        const resp = err?.response;
                        if (resp?.status === 422) {
                            this.fieldErrors = resp.data?.errors || {};
                            this.formError = resp.data?.message ||
                                "Hay información incorrecta. Verifica los datos e inténtalo de nuevo.";
                        } else if (resp?.status === 401) {
                            this.formError = resp?.data?.message || resp?.data?.error ||
                                'Credenciales incorrectas.';
                        } else if (resp?.status === 403 && resp?.data?.status ===
                            'email_verification_required') {
                            this.verifyEmailAddress = (resp?.data?.email) || this.email || this
                                .username;
                            this.verifyEmailMessage = resp?.data?.message ||
                                'Debes verificar tu correo antes de continuar.';
                            this.showVerifyEmailModal = true;
                        } else {
                            this.formError = resp?.data?.error || resp?.data?.message ||
                                "Error de autenticación";
                        }
                    } finally {
                        this.loading = false;
                    }
                },

                async submit2FA() {
                    if (this.verifying2FA || !this.totpCode) return;
                    this.verifying2FA = true;
                    this.totpError = "";
                    this.needsRecovery = false;
                    try {
                        await axios.post('/api/2fa/verify', {
                            code: this.totpCode
                        });
                        try {
                            window.showToast && window.showToast('2FA verificado', 'success', {
                                duration: 1200
                            });
                        } catch (_) {}
                        window.location.assign('/admin/dashboard');
                    } catch (err) {
                        this.needsRecovery = !!err?.response?.data?.needs_recovery;
                        const msg = err?.response?.data?.message || err?.response?.data?.error ||
                            'Código inválido';
                        this.totpError = msg;
                    } finally {
                        this.verifying2FA = false;
                    }
                },

                close2FA() {
                    this.show2FAModal = false;
                    this.totpCode = "";
                    this.totpError = "";
                    this.needsRecovery = false;
                },

                async resendVerification() {
                    if (!this.verifyEmailAddress) return;
                    try {
                        const resp = await axios.post('/api/email/resend', {
                            email: this.verifyEmailAddress
                        });
                        try {
                            window.showToast && window.showToast('Correo reenviado', 'success', {
                                duration: 1500
                            });
                        } catch (_) {}
                        const cool = resp?.data?.retry_after_seconds;
                        this.startResendCooldown((cool && Number.isFinite(+cool) && +cool > 0) ? +
                            cool : 60);
                    } catch (err) {
                        const retry = err?.response?.data?.retry_after_seconds;
                        const msg = err?.response?.data?.message || err?.response?.data?.error ||
                            'No se pudo reenviar';
                        try {
                            window.showToast && window.showToast(msg, 'error', {
                                duration: 2000
                            });
                        } catch (_) {}
                        if (retry && Number.isFinite(+retry) && +retry > 0) {
                            this.startResendCooldown(+retry);
                        }
                    }
                },
                startResendCooldown(seconds) {
                    try {
                        if (this.resendTimerId) clearInterval(this.resendTimerId);
                        this.resendCooldown = Math.max(1, Math.floor(seconds));
                        this.resendTimerId = setInterval(() => {
                            if (this.resendCooldown > 0) this.resendCooldown -= 1;
                            if (this.resendCooldown <= 0 && this.resendTimerId) {
                                clearInterval(this.resendTimerId);
                                this.resendTimerId = null;
                            }
                        }, 1000);
                    } catch (_) {}
                },

                initEmailVerifiedListener() {
                    try {
                        window.addEventListener('storage', (e) => {
                            if (!e) return;
                            if (e.key === 'email_verified' && e.newValue) {
                                this.showVerifyEmailModal = false;
                                this.showAwaitVerificationScreen = false;
                                this.showCloseTabScreen = true;
                            }
                        });
                    } catch (_) {}
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
    <script src="{{ Vite::asset('resources/js/login-guard.js') }}" defer></script>
    <script src="{{ Vite::asset('resources/js/toast.js') }}" defer></script>

</head>

<body class="min-h-screen relative transition-colors duration-300 bg-gray-50 dark:bg-[#171C25] text-gray-800 dark:text-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-[#171C25]">
        <!-- Removed theme switcher -->

        <div class="w-full max-w-sm mx-auto">
            <div
                class="bg-white dark:bg-gray-900 rounded-lg border border-gray-600 dark:border-gray-600 p-4 transition-colors shadow-2xl">
                <div class="text-center mb-4">
                    <div
                        class="inline-flex items-center justify-center w-24 h-24 rounded-full mb-2 bg-gray-100 dark:bg-white border-2 border-white dark:border-gray-500 transition-colors">
                        <img src="{{ $appLogoUrl ?? asset('images/logo-hardlan-blue.svg') }}" alt="Logo"
                            class="app-logo w-20 h-20 object-contain" style="--app-logo-max: 80px;">
                    </div>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 serif-bold">
                        <span x-text="isLogin ? '¡Bienvenido a Hardlan!' : 'Crear cuenta'">¡Bienvenido a Hardlan!</span>
                    </h2>
                    <div x-show="isLogin"
                        class="text-xs text-gray-700 dark:text-gray-200 mt-1 nunito-regular tracking-wide">
                        El lugar donde tu soporte TI es más fácil.
                    </div>
                    <hr class="mt-3 mb-4 border-gray-400 dark:border-gray-700" />
                    <p class="text-xs text-gray-700 dark:text-gray-300 mt-1 mb-4 nunito font-medium">
                        <span
                            x-text="isLogin ? 'Por favor inicia sesión para continuar' : 'Únete a Hardlan y accede a todos nuestros servicios'"
                            class="nunito-regular">Por favor inicia sesión para continuar</span>
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
                                @input="clearFieldError('nombre_usuario')"
                                class="auth-input w-full px-3 py-2 rounded transition-colors bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 nunito-regular text-xs"
                                :class="{ 'border-red-500 focus:border-red-500': fieldErrors.nombre_usuario || (!isLogin && nombre_usuario && !validateNombreUsuario(nombre_usuario)) }"
                                placeholder="Usuario" />
                            <template x-if="fieldErrors.nombre_usuario">
                                <p class="mt-1 text-xs text-red-600 dark:text-red-300 nunito-regular"
                                    x-text="fieldErrors.nombre_usuario[0]"></p>
                            </template>
                            <template
                                x-if="!isLogin && nombre_usuario && !fieldErrors.nombre_usuario && nombreUsuarioIssues(nombre_usuario).length > 0">
                                <ul class="mt-1 text-xs nunito-regular space-y-1 validation-error">
                                    <template x-for="issue in nombreUsuarioIssues(nombre_usuario)" :key="issue">
                                        <li class="flex items-center gap-1">
                                            <i class="fas fa-exclamation-circle text-[10px]"></i>
                                            <span x-text="issue"></span>
                                        </li>
                                    </template>
                                </ul>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-regular">Correo
                                electrónico</label>
                            <input type="email" name="email" x-model="email" :required="!isLogin"
                                @input="clearFieldError('correo_electronico')"
                                class="auth-input w-full px-3 py-2 rounded transition-colors bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 nunito-regular text-xs"
                                :class="{ 'border-red-500 focus:border-red-500': fieldErrors.correo_electronico || (!isLogin && email && !validateEmail(email)) }"
                                placeholder="correo@ejemplo.com" />
                            <template x-if="fieldErrors.correo_electronico">
                                <p class="mt-1 text-xs text-red-600 dark:text-red-300 nunito-regular"
                                    x-text="fieldErrors.correo_electronico[0]"></p>
                            </template>
                            <template
                                x-if="!isLogin && email && !fieldErrors.correo_electronico && emailIssues(email).length > 0">
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

                        <div class="mb-2">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-regular">Contraseña</label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password"
                                    :required="!isLogin" maxlength="100" @input="clearFieldError('contrasena')"
                                    class="auth-input w-full px-3 py-2 rounded transition-colors bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 nunito-regular text-xs"
                                    :class="{ 'border-red-500 focus:border-red-500': fieldErrors.contrasena || (!isLogin && password && !validatePassword(password)) }"
                                    placeholder="••••••••" />
                                <button type="button"
                                    class="absolute right-2 top-2 text-gray-400 dark:text-gray-300 hover:text-gray-600 text-xs"
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
                                    class="auth-input w-full px-3 py-2 rounded transition-colors bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 nunito-regular text-xs"
                                    :class="{ 'border-red-500 focus:border-red-500': !isLogin && confirmPassword && !validateConfirmPassword() }"
                                    placeholder="••••••••" />
                                <button type="button"
                                    class="absolute right-2 top-2 text-gray-400 dark:text-gray-300 hover:text-gray-600 text-xs"
                                    @click="showConfirmPassword = !showConfirmPassword">
                                    <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"
                                        class="w-4 h-4"></i>
                                </button>
                            </div>
                            <template x-if="!isLogin && confirmPassword && confirmPasswordIssues().length > 0">
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
                    </div>

                    <div :class="{ 'mb-4': isLogin, 'mb-2': !isLogin }">
                        <label
                            class="block text-sm font-medium  text-gray-700 dark:text-gray-300 mb-1 nunito-regular">Usuario</label>
                        <input type="text" name="username" x-model="username" required maxlength="50"
                            @input="clearFieldError('usuario')"
                            class="auth-input w-full px-3 py-2 rounded transition-colors bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 nunito-regular text-xs"
                            :class="{ 'border-red-500 focus:border-red-500': fieldErrors.usuario || (username && !validateUsername(username)) }"
                            placeholder="Usuario123" />
                        <template x-if="fieldErrors.usuario">
                            <p class="mt-1 text-xs text-red-600 dark:text-red-300 nunito-regular"
                                x-text="fieldErrors.usuario[0]"></p>
                        </template>
                        <template x-if="username && !fieldErrors.usuario && usernameIssues(username).length > 0">
                            <ul class="mt-1 text-xs nunito-regular space-y-1 validation-error">
                                <template x-for="issue in usernameIssues(username)" :key="issue">
                                    <li class="flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle text-[10px]"></i>
                                        <span x-text="issue"></span>
                                    </li>
                                </template>
                            </ul>
                        </template>
                    </div>

                    <div x-show="isLogin" class="mb-2">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-regular">Contraseña</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password"
                                required maxlength="100" pattern="^\S{8,100}$" title="Mínimo 8 caracteres, sin espacios"
                                @input="clearFieldError('contrasena')"
                                class="auth-input w-full px-3 py-2 rounded transition-colors bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 nunito-regular text-xs"
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
                            Recuperar contraseña o cuenta bloqueada
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
                            class="ml-1 text-emerald-700 dark:text-green-400 hover:text-green-700 bm-2 border-b border-dotted border-emerald-700 dark:border-green-400 focus:outline-none nunito-regular font-medium"
                            @click="switchMode()">
                            <span x-text="isLogin ? 'Regístrate' : 'Inicia sesión'">Regístrate</span>
                        </button>
                    </p>
                </form>
            </div>
        </div>
    </div>


    <div x-show="typeof $data !== 'undefined' && $data.showVerifyEmailModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div
            class="w-full max-w-sm bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-xl">
            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">Verifica tu correo</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1"
                x-text="typeof $data !== 'undefined' && $data.verifyEmailMessage ? $data.verifyEmailMessage : ''"></p>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-2"
                x-show="typeof $data !== 'undefined' && $data.verifyEmailAddress">
                Enviado a: <span class="font-mono"
                    x-text="typeof $data !== 'undefined' ? $data.verifyEmailAddress : ''"></span>
            </p>
            <div class="mt-4 flex items-center gap-2 justify-end">
                <button type="button" :disabled="typeof $data !== 'undefined' && $data.resendCooldown > 0"
                    @click="$data && $data.resendVerification ? resendVerification() : null"
                    class="px-3 py-2 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="typeof $data !== 'undefined' && $data.resendCooldown > 0">
                        Reenviar (espera <span x-text="$data ? $data.resendCooldown : 0"></span>s)
                    </span>
                    <span x-show="!(typeof $data !== 'undefined' && $data.resendCooldown > 0)">Reenviar</span>
                </button>
                <button type="button" @click="showVerifyEmailModal=false; showAwaitVerificationScreen=true"
                    class="px-3 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white text-sm">
                    Entendido
                </button>
            </div>
            <div class="mt-4 text-center text-xs text-blue-700 dark:text-blue-300" id="verify-email-autoreload" style="display:none">
                Esta página se recargará automáticamente en <span id="verify-email-autoreload-count">10</span> segundos...
            </div>
            <script>
            document.addEventListener('alpine:init', () => {
                Alpine.effect(() => {
                    if (Alpine.store && typeof $data !== 'undefined' && $data.showVerifyEmailModal) {
                        let count = 10;
                        const el = document.getElementById('verify-email-autoreload-count');
                        const msg = document.getElementById('verify-email-autoreload');
                        if (msg) msg.style.display = '';
                        const timer = setInterval(() => {
                            count--;
                            if (el) el.textContent = count;
                            if (count <= 0) {
                                clearInterval(timer);
                                location.reload();
                            }
                        }, 1000);
                    }
                });
            });
            </script>
        </div>
    </div>

    <div x-show="showAwaitVerificationScreen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-sm bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-5 shadow-xl text-center">
            <div class="mb-3 text-blue-600">
                <i class="fas fa-envelope-circle-check text-3xl"></i>
            </div>
            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">Revisa tu correo</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Te enviamos un enlace para verificar tu cuenta. Esta pestaña se actualizará automáticamente cuando completes la verificación.</p>
        </div>
    </div>

    <div x-show="showCloseTabScreen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
        <div class="w-full max-w-sm bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-5 shadow-2xl text-center">
            <div class="mb-3 text-green-600">
                <i class="fas fa-circle-check text-3xl"></i>
            </div>
            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">Correo verificado</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Ya puedes cerrar esta pestaña.</p>
            <div class="mt-4 flex items-center gap-2 justify-center">
    <a href="{{ route('login') }}" class="px-3 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white text-sm">Ir al login</a>
</div>
        </div>
    </div>

    <div x-show="show2FAModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div
            class="w-full max-w-sm bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-xl">
            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">Verificación en dos pasos</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                Abre tu app de autenticación (Google Authenticator, Microsoft Authenticator, Authy)
                e ingresa el código de 6 dígitos. También puedes usar un código de recuperación.
            </p>
            <div x-show="needsRecovery" x-cloak
                class="mt-3 px-3 py-2 rounded border border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-500 dark:bg-amber-900/20 dark:text-amber-200 text-xs space-y-1">
                <div class="flex items-start gap-2">
                    <i class="fas fa-key mt-[1px]"></i>
                    <p class="leading-snug">
                        Demasiados intentos incorrectos. Usa uno de tus códigos de recuperación o comunícate con el
                        administrador para regenerarlos.
                    </p>
                </div>
                <p class="leading-snug">
                    Introduce el código exactamente como aparece (con guiones si los tiene). Se consumirá al usarlo.
                </p>
            </div>
            <div class="mt-3">
                <input type="text" inputmode="numeric" pattern="^\\d{6}$" maxlength="10" x-model="totpCode"
                    class="auth-input w-full px-3 py-2 rounded border bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 text-sm"
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