// ===== Config Axios =====
if (window.axios) {
    axios.defaults.withCredentials = true;
    axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
}

// ===== Factory del componente (sin localStorage) =====
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
    generalError: "",
    validationErrors: {},
    // 2FA
    show2FAModal: false,
    totpCode: "",
    verifying2FA: false,
    totpError: "",

        // Lifecycle
        init() {
            try { this.initTheme(); } catch (_) { }
        },

        // Theme (solo en memoria; sin persistir)
        initTheme() {
            try {
                this.isDark = window.matchMedia &&
                    window.matchMedia("(prefers-color-scheme: dark)").matches;
            } catch (_) {
                this.isDark = false;
            }
            this.applyTheme();
        },
        applyTheme() {
            try { document.documentElement.classList.toggle("dark", this.isDark); } catch (_) { }
        },
        toggleTheme() {
            this.isDark = !this.isDark;
            this.applyTheme();
        },

        toggleMode() {
            this.isLogin = !this.isLogin;
            this.generalError = "";
            this.validationErrors = {};
        },

        clearFieldError(field) {
            if (!field) return;
            if (this.validationErrors[field]) {
                const { [field]: _, ...rest } = this.validationErrors;
                this.validationErrors = rest;
                if (Object.keys(this.validationErrors).length === 0) {
                    this.generalError = "";
                }
            }
        },

        // Validaciones
        passwordIssues(pw) {
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
        },
        validatePassword(pw) { return this.passwordIssues(pw).length === 0; },
        validateConfirmPassword() { return this.password === this.confirmPassword; },

        // Submit
        async handleSubmit() {
            if (this.loading) return;
            this.loading = true;
            this.generalError = "";
            this.validationErrors = {};

            try {
                if (this.isLogin) {
                    const res = await axios.post("/api/login", {
                        usuario: this.username,
                        contrasena: this.password,
                    });
                    const data = res?.data || {};
                    if (data.status === "2fa_required") {
                        this.totpCode = "";
                        this.totpError = "";
                        this.show2FAModal = true;
                        this.loading = false;
                        return;
                    }

                    try { window.showToast && window.showToast("Sesión iniciada", "success", { duration: 1200 }); } catch (_) { }
                    window.location.assign("/admin/dashboard");
                    return;

                } else {
                    await axios.post("/api/register", {
                        usuario: this.username,
                        nombre_usuario: this.nombre_usuario,
                        correo_electronico: this.email,
                        contrasena: this.password,
                    });

                    // Tras registro, redirige a completar perfil (sin guardar nada en el navegador)
                    window.location.assign("/admin/perfil");
                    return;
                }
            } catch (err) {
                try { console.error("Error auth:", err?.response?.status, err?.response?.data || err?.message || err); } catch (_) { }
                const response = err?.response;
                const payload = response?.data || {};
                const errors = payload?.errors;

                if (errors && typeof errors === "object") {
                    this.validationErrors = errors;
                    let extracted = "";
                    for (const value of Object.values(errors)) {
                        if (Array.isArray(value) && value.length) {
                            extracted = value[0];
                            break;
                        }
                    }
                    this.generalError = extracted || payload?.message || "Errores de validación.";
                } else {
                    this.generalError = payload?.error || payload?.message || "Error de autenticación";
                }
            } finally {
                this.loading = false;
            }
        },

        async submit2FA() {
            if (this.verifying2FA || !this.totpCode) return;
            this.verifying2FA = true;
            this.totpError = "";
            try {
                await axios.post("/api/2fa/verify", { code: this.totpCode });
                try { window.showToast && window.showToast("2FA verificado", "success", { duration: 1200 }); } catch (_) { }
                window.location.assign("/admin/dashboard");
            } catch (err) {
                const msg = err?.response?.data?.message || err?.response?.data?.error || "Código inválido";
                this.totpError = msg;
            } finally {
                this.verifying2FA = false;
            }
        },

        close2FA() {
            this.show2FAModal = false;
            this.totpCode = "";
            this.totpError = "";
        },

        // Extras
        handleGoogle() { alert("Redirigiendo a Google Sign-In…"); },
        handleRecover() { alert("Redirigiendo a recuperar contraseña…"); },
    };
}

// 1) disponible como función global para x-data="authPage()"
window.authPage = createAuthPage;

// 2) registrar como componente Alpine para x-data="authPage"
function registerWithAlpine() {
    try {
        if (window.Alpine && typeof window.Alpine.data === "function") {
            window.Alpine.data("authPage", () => window.authPage());
        }
    } catch (_) { }
}

// Si Alpine ya está cargado:
registerWithAlpine();

// Si Alpine se cargará después:
document.addEventListener("alpine:init", registerWithAlpine);
