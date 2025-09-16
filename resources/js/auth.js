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

        // Validaciones
        validatePassword(pw) { return (pw || "").length >= 8 && !(/\s/.test(pw)); },
        validateConfirmPassword() { return this.password === this.confirmPassword; },

        // Submit
        async handleSubmit() {
            if (this.loading) return;
            this.loading = true;

            try {
                if (this.isLogin) {
                    await axios.post("/api/login", {
                        usuario: this.username,
                        contrasena: this.password,
                    });

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
                const msg = err?.response?.data?.error || err?.response?.data?.message || "Error de autenticación";
                alert(msg);
            } finally {
                this.loading = false;
            }
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
