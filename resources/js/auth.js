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
        formError: "",
        fieldErrors: {},
        // 2FA
        show2FAModal: false,
        totpCode: "",
        verifying2FA: false,
        totpError: "",

        // Lifecycle
        init() {
            try {
                this.initTheme();
            } catch (_) {}
        },

        // Theme (solo en memoria; sin persistir)
        initTheme() {
            try {
                this.isDark =
                    window.matchMedia &&
                    window.matchMedia("(prefers-color-scheme: dark)").matches;
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
        },

        switchMode() {
            this.isLogin = !this.isLogin;
            this.resetErrors();
            this.password = "";
            this.confirmPassword = "";
        },

        resetErrors() {
            this.formError = "";
            this.fieldErrors = {};
        },

        clearFieldError(field) {
            if (!field) return;
            if (!this.fieldErrors[field]) return;
            const { [field]: _, ...rest } = this.fieldErrors;
            this.fieldErrors = rest;
            if (Object.keys(this.fieldErrors).length === 0) {
                this.formError = "";
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
        validatePassword(pw) {
            return this.passwordIssues(pw).length === 0;
        },
        validateConfirmPassword() {
            return this.password === this.confirmPassword;
        },

        // Submit
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
                    if (data.status === "2fa_required") {
                        this.totpCode = "";
                        this.totpError = "";
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

                await axios.post("/api/register", {
                    usuario: this.username,
                    nombre_usuario: this.nombre_usuario,
                    correo_electronico: this.email,
                    contrasena: this.password,
                });

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

        async submit2FA() {
            if (this.verifying2FA || !this.totpCode) return;
            this.verifying2FA = true;
            this.totpError = "";
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

// 1) disponible como función global para x-data="authPage()"
window.authPage = createAuthPage;

// 2) registrar como componente Alpine para x-data="authPage"
function registerWithAlpine() {
    try {
        if (window.Alpine && typeof window.Alpine.data === "function") {
            window.Alpine.data("authPage", () => window.authPage());
        }
    } catch (_) {}
}

// Si Alpine ya está cargado:
registerWithAlpine();

// Si Alpine se cargará después:
document.addEventListener("alpine:init", registerWithAlpine);
