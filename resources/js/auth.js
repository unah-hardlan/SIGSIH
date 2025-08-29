// Asegura que Axios envíe/reciba cookies cuando corresponda
if (window.axios) {
    axios.defaults.withCredentials = true;
    axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
}

document.addEventListener("alpine:init", () => {
    Alpine.data("authPage", () => ({
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
        initTheme() {
            this.isDark = localStorage.getItem("sigTheme") === "dark";
            this.applyTheme();
        },
        applyTheme() {
            try {
                if (this.isDark) document.documentElement.classList.add("dark");
                else document.documentElement.classList.remove("dark");
                localStorage.setItem(
                    "sigTheme",
                    this.isDark ? "dark" : "light"
                );
            } catch (e) {
                // ignore
            }
        },
        toggleTheme() {
            this.isDark = !this.isDark;
            this.applyTheme();
        },
        validatePassword(pw) {
            return (pw || "").length >= 8;
        },
        validateConfirmPassword() {
            return this.password === this.confirmPassword;
        },

        async handleSubmit() {
            this.loading = true;

            try {
                if (this.isLogin) {
                    const res = await axios.post("/api/login", {
                        usuario: this.username,
                        contrasena: this.password,
                    });

                    // Guarda el token para las llamadas a /api/* (Authorization: Bearer)
                    if (res.data && res.data.token) {
                        localStorage.setItem("authToken", res.data.token);
                        if (res.data.user) {
                            try { localStorage.setItem("authUser", JSON.stringify(res.data.user)); } catch (_) {}
                        }
                        try {
                            const me = await axios.get('/api/me', {
                                headers: { Authorization: `Bearer ${res.data.token}` }
                            });
                                const persona = me?.data?.persona || null;
                                const firstTime = !!(me?.data?.primer_ingreso && !persona);
                            try { localStorage.setItem('authPersona', JSON.stringify(persona)); } catch(_){}
                            try { localStorage.setItem('firstTime', JSON.stringify(firstTime)); } catch(_){}
                        } catch(_) {}
                    }

                    // La cookie HttpOnly 'auth_token' la dejó el backend en Set-Cookie.
                    // Redirige al dashboard protegido.
                    window.location.assign("/admin/dashboard");
                    return;
                } else {
                    const res = await axios.post("/api/register", {
                        usuario: this.username,
                        nombre_usuario: this.nombre_usuario,
                        correo_electronico: this.email,
                        contrasena: this.password,
                    });

                    if (res.data && res.data.token) {
                        localStorage.setItem("authToken", res.data.token);
                        if (res.data.user) {
                            try { localStorage.setItem("authUser", JSON.stringify(res.data.user)); } catch (_) {}
                        }
                        try {
                            const me = await axios.get('/api/me', {
                                headers: { Authorization: `Bearer ${res.data.token}` }
                            });
                                const persona = me?.data?.persona || null;
                                const firstTime = !!(me?.data?.primer_ingreso && !persona);
                            try { localStorage.setItem('authPersona', JSON.stringify(persona)); } catch(_){}
                            try { localStorage.setItem('firstTime', JSON.stringify(firstTime)); } catch(_){}
                        } catch(_) {}
                    }

                    // Ir a completar perfil
                    window.location.assign("/admin/perfil");
                    return;
                }
            } catch (err) {
                console.error("Error auth:", err?.response?.data || err);
                const msg =
                    err?.response?.data?.error ||
                    err?.response?.data?.message ||
                    "Error de autenticación";
                alert(msg);
                this.loading = false;
            }
        },

        handleGoogle() {
            alert("Redirigiendo a Google Sign-In…");
        },
        handleRecover() {
            alert("Redirigiendo a recuperar contraseña…");
        },
    }));
});
