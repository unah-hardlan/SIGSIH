// Pre-render theme script - Se ejecuta inmediatamente para evitar flash
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

// Fallback auth component para casos donde auth.js no carga
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

            init() {
                this.initTheme();
            },

            initTheme() {
                try {
                    // Leer el estado actual del DOM en lugar de recalcular
                    this.isDark =
                        document.documentElement.classList.contains("dark");
                    // Solo aplicar si hay discrepancia (por seguridad)
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
