// Consolidated authentication module for login/register + theme + 2FA + email verify
// Load axios defaults if present
if (window.axios) {
    axios.defaults.withCredentials = true;
    axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
}

// Initial theme sync (before Alpine mounts to avoid FOUC)
(function syncInitialTheme() {
    try {
        const saved = localStorage.getItem("theme");
        const preferDark = saved
            ? saved === "dark"
            : window.matchMedia &&
              window.matchMedia("(prefers-color-scheme: dark)").matches;
        document.documentElement.classList.toggle("dark", preferDark);
    } catch (_) {}
})();

// Factory function to create the authPage data object for Alpine.js
function createAuthPage() {
    return {
        // Core Authentication State
        isLogin: true,
        showPassword: false,
        showConfirmPassword: false,
        username: "",
        password: "",
        confirmPassword: "",
        nombre_usuario: "", // From Daniel's version
        email: "",
        loading: false,
        formError: "",
        fieldErrors: {},

        // Theme State
        isDark: false,

        // 2FA State (from Development version)
        show2FAModal: false,
        totpCode: "",
        verifying2FA: false,
        totpError: "",
        needsRecovery: false, // Added in Daniel's version, kept in combined

        // Email Verification State (from Development version)
        showVerifyEmailModal: false,
        verifyEmailMessage: "",
        verifyEmailAddress: "",
        resendCooldown: 0,
        resendTimerId: null,

        // --- Initialization ---
        init() {
            // Ensure all properties are properly initialized
            this.formError = this.formError || "";
            this.fieldErrors = this.fieldErrors || {};
            try {
                this.initTheme();
            } catch (e) {
                console.warn("Theme initialization failed:", e);
            }
        },

        // --- Theme Management ---
        initTheme() {
            try {
                this.isDark =
                    document.documentElement.classList.contains("dark");
            } catch (_) {
                this.isDark = false;
            }
            this.applyTheme();
        },
        applyTheme() {
            try {
                document.documentElement.classList.toggle("dark", this.isDark);
            } catch (_) {}
        },
        toggleTheme() {
            this.isDark = !this.isDark;
            this.applyTheme();
            try {
                localStorage.setItem("theme", this.isDark ? "dark" : "light");
            } catch (_) {}
        },

        // --- Form Navigation & Reset ---
        switchMode() {
            this.isLogin = !this.isLogin;
            this.resetErrors();
            this.password = "";
            this.confirmPassword = "";
            this.needsRecovery = false; // Reset recovery state
            this.show2FAModal = false; // Close 2FA modal
            this.showVerifyEmailModal = false; // Close verify email modal
        },

        // --- Error Handling ---
        resetErrors() {
            this.formError = "";
            this.fieldErrors = {};
        },

        setFieldError(field, message) {
            if (!field || !message) return;
            this.fieldErrors = {
                ...this.fieldErrors,
                [field]: [message], // Ensure it's an array of messages
            };
            if (!this.formError) {
                this.formError =
                    "Hay información incorrecta. Verifica los datos e inténtalo de nuevo.";
            }
        },

        clearFieldError(field) {
            try {
                if (!field) return;
                if (!this.fieldErrors || !this.fieldErrors[field]) return;
                const { [field]: _, ...rest } = this.fieldErrors; // Destructure to remove field
                this.fieldErrors = rest;
                // If no more field errors, clear formError
                if (Object.keys(this.fieldErrors).length === 0) {
                    this.formError = "";
                }
            } catch (e) {
                console.warn("clearFieldError failed:", e);
            }
        },

        // --- Input Validations (From Daniel's version, integrated) ---
        isAlphaNumeric(value) {
            if (!value) return false;
            return /^[A-Za-z0-9]+$/.test(value);
        },

        usernameIssues(username) {
            const value = username || "";
            const issues = [];
            if (value.length === 0) {
                issues.push("El usuario es requerido.");
            } else if (!this.isAlphaNumeric(value)) {
                issues.push(
                    "Solo se permiten letras y números, sin espacios ni símbolos."
                );
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
            } else if (value.length > 0 && !this.isAlphaNumeric(value)) {
                issues.push(
                    "Solo se permiten letras y números, sin espacios ni símbolos."
                );
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

        passwordIssues(pw) {
            const value = pw || "";
            const issues = [];
            if (value.length < 8) {
                issues.push("Debe tener al menos 8 caracteres.");
            }
            if (/\s/.test(value)) {
                issues.push("No debe contener espacios.");
            }
            // Only require uppercase for registration (not login)
            if (!this.isLogin && !/[A-Z]/.test(value)) {
                issues.push("Debe incluir al menos una letra mayúscula.");
            }
            return issues;
        },

        validatePassword(pw) {
            return this.passwordIssues(pw).length === 0;
        },

        confirmPasswordIssues() {
            const issues = [];
            if (!this.isLogin && this.confirmPassword.length === 0) {
                issues.push("Debes confirmar tu contraseña.");
            } else if (
                this.confirmPassword.length > 0 &&
                this.password !== this.confirmPassword
            ) {
                issues.push("Las contraseñas no coinciden.");
            }
            return issues;
        },

        validateConfirmPassword() {
            return this.confirmPasswordIssues().length === 0;
        },

        // Helper to update field errors on input (combining both versions' intent)
        handleUsernameInput() {
            this.clearFieldError("usuario");
            const issues = this.usernameIssues(this.username);
            if (issues.length > 0) this.setFieldError("usuario", issues[0]);
        },
        handleNombreUsuarioInput() {
            this.clearFieldError("nombre_usuario");
            const issues = this.nombreUsuarioIssues(this.nombre_usuario);
            if (issues.length > 0)
                this.setFieldError("nombre_usuario", issues[0]);
        },
        handleEmailInput() {
            this.clearFieldError("correo_electronico"); // Use backend field name
            const issues = this.emailIssues(this.email);
            if (issues.length > 0)
                this.setFieldError("correo_electronico", issues[0]);
        },
        handlePasswordInput() {
            this.clearFieldError("contrasena"); // Use backend field name
            const issues = this.passwordIssues(this.password);
            if (issues.length > 0) this.setFieldError("contrasena", issues[0]);
        },
        handleConfirmPasswordInput() {
            this.clearFieldError("password_confirmation"); // Use backend field name
            const issues = this.confirmPasswordIssues();
            if (issues.length > 0)
                this.setFieldError("password_confirmation", issues[0]);
        },

        // --- Main Submission Logic ---
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

                    // Handle 2FA (from Development version)
                    if (data.status === "2fa_required") {
                        this.totpCode = "";
                        this.totpError = "";
                        this.needsRecovery = false;
                        this.show2FAModal = true;
                        this.loading = false;
                        return;
                    }

                    // Handle Email Verification (from Development version)
                    if (data.status === "email_verification_required") {
                        this.verifyEmailAddress =
                            data?.email || this.email || this.username; // Use provided email or existing
                        this.verifyEmailMessage =
                            "Debes verificar tu correo antes de iniciar sesión.";
                        this.showVerifyEmailModal = true;
                        this.loading = false;
                        return;
                    }

                    // Successful login
                    try {
                        window.showToast &&
                            window.showToast("Sesión iniciada", "success", {
                                duration: 1200,
                            });
                    } catch (_) {}
                    window.location.assign("/admin/dashboard");
                    return;
                } else {
                    // Registration
                    await axios.post("/api/register", {
                        usuario: this.username,
                        nombre_usuario: this.nombre_usuario,
                        correo_electronico: this.email,
                        contrasena: this.password,
                        // No need for confirmPassword here, backend handles validation
                    });

                    // Handle Email Verification after registration (from Development version)
                    // Assuming backend sends a 'verification_sent' status or similar for immediate email verification
                    this.verifyEmailAddress = this.email;
                    this.verifyEmailMessage =
                        "Te enviamos un correo para verificar tu cuenta. Revisa tu bandeja de entrada o spam.";
                    this.showVerifyEmailModal = true;

                    // If no email verification is needed immediately, redirect to profile
                    // window.location.assign("/admin/perfil");
                }
            } catch (err) {
                // Consolidated error handling
                try {
                    console.error(
                        "Authentication Error:",
                        err?.response?.status,
                        err?.response?.data || err?.message || err
                    );
                } catch (_) {}

                const resp = err?.response;
                if (resp?.status === 422) {
                    // Validation errors
                    this.fieldErrors = resp.data?.errors || {};
                    this.formError =
                        resp.data?.message ||
                        "Hay información incorrecta. Verifica los datos e inténtalo de nuevo.";
                } else if (resp?.status === 401) {
                    // Unauthorized (incorrect credentials)
                    this.formError =
                        resp.data?.message ||
                        resp.data?.error ||
                        "Credenciales incorrectas.";
                } else if (
                    resp?.status === 403 &&
                    resp?.data?.status === "email_verification_required"
                ) {
                    // Specific case for forbidden due to email verification (e.g., trying to login unverified)
                    this.verifyEmailAddress =
                        resp?.data?.email || this.email || this.username;
                    this.verifyEmailMessage =
                        resp?.data?.message ||
                        "Debes verificar tu correo antes de continuar.";
                    this.showVerifyEmailModal = true;
                } else {
                    // General errors
                    this.formError =
                        resp?.data?.error ||
                        resp?.data?.message ||
                        "Error de autenticación inesperado.";
                }
            } finally {
                this.loading = false;
            }
        },

        // --- Email Verification Modal Logic (from Development version) ---
        async resendVerification() {
            if (!this.verifyEmailAddress || this.resendCooldown > 0) return;
            this.loading = true; // Temporarily use general loading
            try {
                const resp = await axios.post("/api/email/resend", {
                    email: this.verifyEmailAddress,
                });
                try {
                    window.showToast &&
                        window.showToast("Correo reenviado", "success", {
                            duration: 1500,
                        });
                } catch (_) {}
                const cool = resp?.data?.retry_after_seconds;
                if (cool && Number.isFinite(+cool) && +cool > 0) {
                    this.startResendCooldown(+cool);
                } else {
                    this.startResendCooldown(60); // Default cooldown
                }
            } catch (err) {
                const retry = err?.response?.data?.retry_after_seconds;
                const msg =
                    err?.response?.data?.message ||
                    err?.response?.data?.error ||
                    "No se pudo reenviar el correo de verificación.";
                try {
                    window.showToast &&
                        window.showToast(msg, "error", { duration: 2000 });
                } catch (_) {}
                if (retry && Number.isFinite(+retry) && +retry > 0) {
                    this.startResendCooldown(+retry);
                }
            } finally {
                this.loading = false;
            }
        },

        startResendCooldown(seconds) {
            try {
                if (this.resendTimerId) clearInterval(this.resendTimerId);
                this.resendCooldown = Math.max(1, Math.floor(seconds));
                this.resendTimerId = setInterval(() => {
                    if (this.resendCooldown > 0) {
                        this.resendCooldown -= 1;
                    }
                    if (this.resendCooldown <= 0 && this.resendTimerId) {
                        clearInterval(this.resendTimerId);
                        this.resendTimerId = null;
                    }
                }, 1000);
            } catch (_) {}
        },

        closeVerifyEmailModal() {
            this.showVerifyEmailModal = false;
            this.verifyEmailMessage = "";
            this.verifyEmailAddress = "";
            if (this.resendTimerId) clearInterval(this.resendTimerId);
            this.resendCooldown = 0;
            this.resendTimerId = null;
        },

        // --- 2FA Modal Logic (from Development version) ---
        async submit2FA() {
            if (this.verifying2FA || !this.totpCode) return;
            this.verifying2FA = true;
            this.totpError = "";
            this.needsRecovery = false; // Reset on new attempt
            try {
                await axios.post("/api/2fa/verify", { code: this.totpCode });
                try {
                    window.showToast &&
                        window.showToast("2FA verificado", "success", {
                            duration: 1200,
                        });
                } catch (_) {}
                window.location.assign("/admin/dashboard");
            } catch (err) {
                this.needsRecovery = !!err?.response?.data?.needs_recovery; // Backend indicates if recovery is an option
                const msg =
                    err?.response?.data?.message ||
                    err?.response?.data?.error ||
                    "Código TOTP inválido.";
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
            // Optionally, if closing 2FA means returning to login, reset login state
            this.loading = false; // Ensure loading is off if modal closed manually
        },

        // --- External Actions ---
        handleGoogle() {
            alert("Redirigiendo a Google Sign-In… (Funcionalidad pendiente)");
        },
        handleRecover() {
            alert(
                "Redirigiendo a recuperar contraseña… (Funcionalidad pendiente)"
            );
            // Typically navigates to a password reset page
            // window.location.assign("/forgot-password");
        },
    };
}

// Expose factory globally
window.authPage = createAuthPage;

// Alpine registration (idempotent, ensures it runs if Alpine is loaded, or when it initializes)
function registerAuth() {
    try {
        if (!window.Alpine) {
            console.warn(
                "Alpine.js not loaded yet, trying again on alpine:init."
            );
            return;
        }
        if (!window.Alpine.data("authPage")) {
            // Only register if not already registered
            window.Alpine.data("authPage", createAuthPage);
        } else {
            console.log("Alpine.js data 'authPage' already registered.");
        }
    } catch (e) {
        console.error("Alpine.js registration failed:", e);
    }
}

// Attempt immediate registration if Alpine is already present
if (window.Alpine) {
    registerAuth();
}
// Register on Alpine's init event for when it loads later
document.addEventListener("alpine:init", registerAuth);

// Exported utility function for getting authentication token
// Note: With HttpOnly cookies, token is not accessible from JS
export function getToken() {
    return ""; // Token handled server-side via HttpOnly cookie
}
