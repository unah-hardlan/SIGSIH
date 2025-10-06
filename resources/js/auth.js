if (window.axios) {
    axios.defaults.withCredentials = true;
    axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
}

(function () {
    try {
        const saved = localStorage.getItem("theme");
        const isDark = saved
            ? saved === "dark"
            : window.matchMedia &&
              window.matchMedia("(prefers-color-scheme: dark)").matches;
        if (isDark) {
            document.documentElement.classList.add("dark");
        } else {
            document.documentElement.classList.remove("dark");
        }
    } catch (_) {}
})();

document.addEventListener("alpine:init", () => {
    if (!window.authPage) {
        console.warn("authPage not loaded, creating fallback");
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

            init() {
                this.initTheme();
            },

            initTheme() {
                try {
                    this.isDark =
                        document.documentElement.classList.contains("dark");
                    document.documentElement.classList.toggle(
                        "dark",
                        this.isDark
                    );
                } catch (_) {}
            },

            toggleTheme() {
                this.isDark = !this.isDark;
                document.documentElement.classList.toggle("dark", this.isDark);
                try {
                    localStorage.setItem(
                        "theme",
                        this.isDark ? "dark" : "light"
                    );
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
                } else if (value.length > 0 && !/^[A-Za-z0-9]+$/.test(value)) {
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

            passwordIssues(pw) {
                const value = pw || "";
                const issues = [];
                if (value.length < 8)
                    issues.push("Debe tener al menos 8 caracteres.");
                if (/\s/.test(value)) issues.push("No debe contener espacios.");
                if (!this.isLogin && !/[A-Z]/.test(value))
                    issues.push("Debe incluir al menos una letra mayúscula.");
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
                        this.formError =
                            resp.data?.message || "Hay errores de validación.";
                    } else {
                        this.formError =
                            resp?.data?.error ||
                            resp?.data?.message ||
                            "Error de autenticación";
                    }
                } finally {
                    this.loading = false;
                }
            },

            handleGoogle() {
                alert("Redirigiendo a Google Sign-In…");
            },
        });
    }

    Alpine.data("authPage", window.authPage);
});

function createAuthPage() {
    return {
        // State
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
        // 2FA
        show2FAModal: false,
        totpCode: "",
        verifying2FA: false,
        totpError: "",
    needsRecovery: false,
    // Verify email
    showVerifyEmailModal: false,
    verifyEmailMessage: "",
    verifyEmailAddress: "",
    resendCooldown: 0,
    resendTimerId: null,

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

        initTheme() {
            try {
                // Leer el estado actual del DOM en lugar de recalcular
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

        switchMode() {
            this.isLogin = !this.isLogin;
            this.resetErrors();
            this.password = "";
            this.confirmPassword = "";
            this.needsRecovery = false;
        },

        resetErrors() {
            this.formError = "";
            this.fieldErrors = {};
        },

        setFieldError(field, message) {
            if (!field || !message) return;
            this.fieldErrors = {
                ...this.fieldErrors,
                [field]: [message],
            };
            if (!this.formError) {
                this.formError = "Hay errores de validación.";
            }
        },

        clearFieldError(field) {
            try {
                if (!field) return;
                if (!this.fieldErrors || !this.fieldErrors[field]) return;
                const { [field]: _, ...rest } = this.fieldErrors;
                this.fieldErrors = rest;
                if (Object.keys(this.fieldErrors).length === 0) {
                    this.formError = "";
                }
            } catch (e) {
                console.warn("clearFieldError failed:", e);
            }
        },

        isAlphaNumeric(value) {
            if (!value) return false;
            return /^[A-Za-z0-9]+$/.test(value);
        },

        handleNombreUsuarioInput() {
            this.clearFieldError("nombre_usuario");
            const value = (this.nombre_usuario || "").trim();
            this.nombre_usuario = value;
            if (value && !this.isAlphaNumeric(value)) {
                this.setFieldError(
                    "nombre_usuario",
                    "El nombre de usuario sólo puede contener letras y números."
                );
            }
        },

        handleUsernameInput() {
            this.clearFieldError("usuario");
            const value = (this.username || "").trim();
            this.username = value;
            if (value && !this.isAlphaNumeric(value)) {
                this.setFieldError(
                    "usuario",
                    "El usuario sólo puede contener letras y números."
                );
            }
        },

        // Validaciones
        passwordIssues(pw) {
            try {
                const value = pw || "";
                const issues = [];
                if (value.length < 8) {
                    issues.push("Debe tener al menos 8 caracteres.");
                }
                if (/\s/.test(value)) {
                    issues.push("No debe contener espacios.");
                }
                const requireUppercase = !this.isLogin;
                if (requireUppercase && !/[A-Z]/.test(value)) {
                    issues.push("Debe incluir al menos una letra mayúscula.");
                }
                return issues;
            } catch (e) {
                console.warn("passwordIssues failed:", e);
                return [];
            }
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
                    const data = res?.data || {};
                    if (data.status === "email_verification_required") {
                        this.verifyEmailAddress = data?.email || this.email || this.username;
                        this.verifyEmailMessage = "Debes verificar tu correo antes de iniciar sesión.";
                        this.showVerifyEmailModal = true;
                        this.loading = false;
                        return;
                    }
                    this.needsRecovery = false;
                    if (data.status === "2fa_required") {
                        this.totpCode = "";
                        this.totpError = "";
                        this.needsRecovery = false;
                        this.show2FAModal = true;
                        this.loading = false;
                        return;
                    }

                    try {
                        window.showToast &&
                            window.showToast("Sesión iniciada", "success", {
                                duration: 1200,
                            });
                    } catch (_) {}
                    window.location.assign("/admin/dashboard");
                    return;
                }

                const regRes = await axios.post("/api/register", {
                    usuario: this.username,
                    nombre_usuario: this.nombre_usuario,
                    correo_electronico: this.email,
                    contrasena: this.password,
                });
                const regData = regRes?.data || {};
                if (regData?.status === 'verification_sent') {
                    // Mostrar modal sin recargar
                    this.verifyEmailAddress = this.email;
                    this.verifyEmailMessage = "Te enviamos un correo para verificar tu cuenta. Revisa tu bandeja de entrada o spam.";
                    this.showVerifyEmailModal = true;
                    return;
                }
                window.location.assign("/admin/perfil");
            } catch (err) {
                try {
                    console.error(
                        "Error auth:",
                        err?.response?.status,
                        err?.response?.data || err?.message || err
                    );
                } catch (_) {}
                const resp = err?.response;
                if (resp?.status === 422) {
                    this.fieldErrors = resp.data?.errors || {};
                    this.formError =
                        resp.data?.message || "Hay errores de validación.";
                } else if (resp?.status === 401) {
                    this.formError =
                        resp.data?.message ||
                        resp.data?.error ||
                        "Credenciales incorrectas.";
                } else if (resp?.status === 403 && resp?.data?.status === 'email_verification_required') {
                    this.verifyEmailAddress = resp?.data?.email || this.email || this.username;
                    this.verifyEmailMessage = resp?.data?.message || 'Debes verificar tu correo antes de continuar.';
                    this.showVerifyEmailModal = true;
                } else {
                    this.formError =
                        resp?.data?.error ||
                        resp?.data?.message ||
                        "Error de autenticación";
                }
            } finally {
                this.loading = false;
            }
        },

        async resendVerification() {
            if (!this.verifyEmailAddress) return;
            try {
                const resp = await axios.post('/api/email/resend', { email: this.verifyEmailAddress });
                try { window.showToast && window.showToast('Correo reenviado', 'success', { duration: 1500 }); } catch (_) {}
                // Start cooldown using server value if present
                const cool = resp?.data?.retry_after_seconds;
                if (cool && Number.isFinite(+cool) && +cool > 0) {
                    this.startResendCooldown(+cool);
                } else {
                    this.startResendCooldown(60);
                }
            } catch (err) {
                const retry = err?.response?.data?.retry_after_seconds;
                const msg = err?.response?.data?.message || err?.response?.data?.error || 'No se pudo reenviar';
                try { window.showToast && window.showToast(msg, 'error', { duration: 2000 }); } catch (_) {}
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

        async submit2FA() {
            if (this.verifying2FA || !this.totpCode) return;
            this.verifying2FA = true;
            this.totpError = "";
            this.needsRecovery = false;
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
                this.needsRecovery = !!err?.response?.data?.needs_recovery;
                const msg =
                    err?.response?.data?.message ||
                    err?.response?.data?.error ||
                    "Código inválido";
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

        // Extras
        handleGoogle() {
            alert("Redirigiendo a Google Sign-In…");
        },
        handleRecover() {
            alert("Redirigiendo a recuperar contraseña…");
        },
    };
}

// Make authPage available globally
window.authPage = createAuthPage;

// Register with Alpine.js
function registerWithAlpine() {
    try {
        if (window.Alpine && typeof window.Alpine.data === "function") {
            window.Alpine.data("authPage", createAuthPage);
        }
    } catch (e) {
        console.warn("Alpine.js registration failed:", e);
    }
}

// Try to register immediately if Alpine is already loaded
if (window.Alpine) {
    registerWithAlpine();
}

// Also register when Alpine initializes
document.addEventListener("alpine:init", () => {
    try {
        window.Alpine.data("authPage", createAuthPage);
    } catch (e) {
        console.warn("Alpine.js init registration failed:", e);
    }
});
