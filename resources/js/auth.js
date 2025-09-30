if (window.axios) {
    axios.defaults.withCredentials = true;
    axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
}

function createAuthPage() {
    return {
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

        init() {
            try {
                this.initTheme();
            } catch (_) {}
        },

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
            if (!field) return;
            if (!this.fieldErrors[field]) return;
            const { [field]: _, ...rest } = this.fieldErrors;
            this.fieldErrors = rest;
            if (Object.keys(this.fieldErrors).length === 0) {
                this.formError = "";
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

window.authPage = createAuthPage;

function registerWithAlpine() {
    try {
        if (window.Alpine && typeof window.Alpine.data === "function") {
            window.Alpine.data("authPage", () => window.authPage());
        }
    } catch (_) {}
}

registerWithAlpine();

document.addEventListener("alpine:init", registerWithAlpine);
