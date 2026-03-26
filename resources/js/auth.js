if (window.axios) {
    axios.defaults.withCredentials = true;
    axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
}

(function syncInitialTheme() {
    try {
        const saved = localStorage.getItem("theme");
        const preferDark = saved
            ? saved === "dark"
            : window.matchMedia &&
            window.matchMedia("(prefers-color-scheme: dark)").matches;
        document.documentElement.classList.toggle("dark", preferDark);
    } catch (_) { }
})();

function createAuthPage() {
    return {
        isLogin: true,
        showPassword: false,
        showConfirmPassword: false,
        username: "",
        password: "",
        confirmPassword: "",
        email: "",
        loading: false,
        formError: "",
        fieldErrors: {},

        isDark: false,

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
            this.formError = this.formError || "";
            this.fieldErrors = this.fieldErrors || {};
            try {
                this.initTheme();
                this.initEmailVerifiedListener();
            } catch (e) {
                console.warn("Theme initialization failed:", e);
            }
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
            } catch (_) { }
        },

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
            } catch (_) { }
        },
        toggleTheme() {
            this.isDark = !this.isDark;
            this.applyTheme();
            try {
                localStorage.setItem("theme", this.isDark ? "dark" : "light");
            } catch (_) { }
        },

        switchMode() {
            this.isLogin = !this.isLogin;
            this.resetErrors();
            this.password = "";
            this.confirmPassword = "";
            this.needsRecovery = false;
            this.show2FAModal = false;
            this.showVerifyEmailModal = false;
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
                this.formError =
                    "Hay información incorrecta. Verifica los datos e inténtalo de nuevo.";
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

        usernameIssues(username) {
            const value = username || "";
            const issues = [];
            if (value.length === 0) {
                issues.push("El usuario es requerido.");
            } else if (!this.isLogin && value.length < 4) {
                issues.push("El usuario debe tener al menos 4 caracteres.");
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

        emailIssues(email) {
            const value = email || "";
            const issues = [];
            if (!this.isLogin && value.length === 0) {
                issues.push("El correo electrónico es requerido.");
            } else if (value.length > 0) {
                const emailRegex = /^[A-Za-z0-9._%+\-\u00C0-\u00FF]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/u;
                if (!emailRegex.test(value)) {
                    issues.push("Usa un correo válido con letras latinas (incluye ñ), números y símbolos estándar.");
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
            if (/[^\x21-\x7E\u00A1-\u00FF]/.test(value)) {
                issues.push("No se permiten caracteres de alfabetos no latinos (por ejemplo: 名前).");
            }

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

        handleUsernameInput() {
            this.clearFieldError("usuario");
            const issues = this.usernameIssues(this.username);
            if (issues.length > 0) this.setFieldError("usuario", issues[0]);
        },
        handleEmailInput() {
            this.clearFieldError("correo_electronico");
            const issues = this.emailIssues(this.email);
            if (issues.length > 0)
                this.setFieldError("correo_electronico", issues[0]);
        },
        handlePasswordInput() {
            this.clearFieldError("contrasena");
            const issues = this.passwordIssues(this.password);
            if (issues.length > 0) this.setFieldError("contrasena", issues[0]);
        },
        handleConfirmPasswordInput() {
            this.clearFieldError("password_confirmation");
            const issues = this.confirmPasswordIssues();
            if (issues.length > 0)
                this.setFieldError("password_confirmation", issues[0]);
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

                    if (data.success === false) {
                        this.formError = data.error || data.message || "Credenciales incorrectas.";
                        this.loading = false;
                        return;
                    }

                    if (data.status === "2fa_required") {
                        this.totpCode = "";
                        this.totpError = "";
                        this.needsRecovery = false;
                        this.show2FAModal = true;
                        this.loading = false;
                        return;
                    }

                    if (data.status === "email_verification_required") {
                        this.verifyEmailAddress =
                            data?.email || this.email || this.username;
                        this.verifyEmailMessage =
                            "Debes verificar tu correo antes de iniciar sesión.";
                        this.showVerifyEmailModal = true;
                        this.loading = false;
                        return;
                    }

                    try {
                        window.showToast &&
                            window.showToast("Sesión iniciada", "success", {
                                duration: 1200,
                            });
                    } catch (_) { }
                    // Marcar el momento exacto del login para que el interceptor
                    // de requests en app.js pueda ignorar SESSION_REMOVED_LIMIT
                    // generados por requests en vuelo del token anterior.
                    try { localStorage.setItem('__loginTs', String(Date.now())); } catch (_) { }
                    window.location.assign(data.redirect_url || "/admin/dashboard");
                    return;
                } else {
                    await axios.post("/api/register", {
                        usuario: this.username,
                        correo_electronico: this.email,
                        contrasena: this.password,
                    });

                    this.verifyEmailAddress = this.email;
                    this.verifyEmailMessage =
                        "Te enviamos un correo para verificar tu cuenta. Revisa tu bandeja de entrada o spam.";
                    this.showVerifyEmailModal = true;
                }
            } catch (err) {
                try {
                } catch (_) { }

                const resp = err?.response;
                if (resp?.status === 422) {
                    this.fieldErrors = resp.data?.errors || {};
                    this.formError =
                        resp.data?.message ||
                        "Hay información incorrecta. Verifica los datos e inténtalo de nuevo.";
                } else if (resp?.status === 429) {
                    const retry = Number(resp?.data?.retry_after_seconds || 0);
                    this.formError =
                        resp?.data?.message ||
                        "Demasiados intentos. Intenta nuevamente más tarde.";
                    if (Number.isFinite(retry) && retry > 0) {
                        this.formError += ` (Espera ${Math.ceil(retry / 60)} minuto(s))`;
                    }
                } else if (resp?.status === 401) {
                    this.formError =
                        resp.data?.message ||
                        resp.data?.error ||
                        "Credenciales incorrectas.";
                } else if (
                    resp?.status === 403 &&
                    resp?.data?.status === "email_verification_required"
                ) {
                    this.verifyEmailAddress =
                        resp?.data?.email || this.email || this.username;
                    this.verifyEmailMessage =
                        resp?.data?.message ||
                        "Debes verificar tu correo antes de continuar.";
                    this.showVerifyEmailModal = true;
                } else {
                    this.formError =
                        resp?.data?.error ||
                        resp?.data?.message ||
                        "Error de autenticación inesperado.";
                }
            } finally {
                this.loading = false;
            }
        },

        async resendVerification() {
            if (!this.verifyEmailAddress || this.resendCooldown > 0) return;
            this.loading = true;
            try {
                const resp = await axios.post("/api/email/resend", {
                    email: this.verifyEmailAddress,
                });
                try {
                    window.showToast &&
                        window.showToast("Correo reenviado", "success", {
                            duration: 1500,
                        });
                } catch (_) { }
                const cool = resp?.data?.retry_after_seconds;
                if (cool && Number.isFinite(+cool) && +cool > 0) {
                    this.startResendCooldown(+cool);
                } else {
                    this.startResendCooldown(60);
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
                } catch (_) { }
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
            } catch (_) { }
        },

        closeVerifyEmailModal() {
            this.showVerifyEmailModal = false;
            this.verifyEmailMessage = "";
            this.verifyEmailAddress = "";
            if (this.resendTimerId) clearInterval(this.resendTimerId);
            this.resendCooldown = 0;
            this.resendTimerId = null;
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
                } catch (_) { }
                try { localStorage.setItem('__loginTs', String(Date.now())); } catch (_) { }
                window.location.assign("/admin/dashboard");
            } catch (err) {
                this.needsRecovery = !!err?.response?.data?.needs_recovery;
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

            this.loading = false;
        },

        handleGoogle() {
            alert("Redirigiendo a Google Sign-In… (Funcionalidad pendiente)");
        },
        handleRecover() {
            alert(
                "Redirigiendo a recuperar contraseña… (Funcionalidad pendiente)"
            );
        },
    };
}

window.authPage = createAuthPage;

function registerAuth() {
    try {
        if (!window.Alpine) {
            console.warn(
                "Alpine.js not loaded yet, trying again on alpine:init."
            );
            return;
        }
        if (!window.Alpine.data("authPage")) {
            window.Alpine.data("authPage", createAuthPage);
        } else {
            console.log("Alpine.js data 'authPage' already registered.");
        }
    } catch (e) {
        console.error("Alpine.js registration failed:", e);
    }
}

if (window.Alpine) {
    registerAuth();
}

document.addEventListener("alpine:init", registerAuth);

window.getToken = function () {
    return "";
};
