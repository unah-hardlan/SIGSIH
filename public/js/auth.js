document.addEventListener("alpine:init", () => {
    Alpine.data("authPage", () => ({
        isLogin: true,
        showPassword: false,
        showConfirmPassword: false,
        username: "",
        // Coincide con x-model="nombre_usuario" del formulario (registro)
        nombre_usuario: "",
        password: "",
        confirmPassword: "",
        name: "",
        email: "",
        loading: false,
        isDark: false,

        init() {
            this.isLogin = true;
            this.initTheme();
        },

        initTheme() {
            this.isDark = localStorage.getItem("sigTheme") === "dark";
        },
        toggleTheme() {
            this.isDark = !this.isDark;
            localStorage.setItem("sigTheme", this.isDark ? "dark" : "light");
        },
        validatePassword(password) {
            return password.length >= 8;
        },
        validateConfirmPassword() {
            return this.password === this.confirmPassword;
        },
        handleSubmit() {
            this.loading = true;
            if (this.isLogin) {
                fetch("/api/login", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    // MUY IMPORTANTE: para que el navegador acepte la cookie Set-Cookie del backend
                    credentials: "same-origin",
                    body: JSON.stringify({
                        usuario: this.username,
                        contrasena: this.password,
                    }),
                })
                    .then(async (response) => {
                        let data;
                        try {
                            data = await response.json();
                        } catch (e) {
                            data = {};
                        }
                        if (!response.ok) {
                            throw new Error(
                                data.error ||
                                    data.message ||
                                    "Error de autenticación"
                            );
                        }
                        return data;
                    })
                    .then((data) => {
                        if (data.token) {
                            localStorage.setItem("authToken", data.token);
                            if (window.showToast) {
                                showToast("Bienvenido", "success", {
                                    duration: 1800,
                                });
                                setTimeout(
                                    () =>
                                        window.location.replace(
                                            "/admin/dashboard"
                                        ),
                                    700
                                );
                            } else {
                                window.location.replace("/admin/dashboard");
                            }
                        } else {
                            if (window.showToast) {
                                showToast(
                                    "Usuario o contraseña incorrectos",
                                    "error"
                                );
                            } else {
                                alert("Usuario o contraseña incorrectos");
                            }
                        }
                    })
                    .catch((error) => {
                        console.error("Error login:", error);
                        if (window.showToast) {
                            showToast(
                                error.message ||
                                    "Ocurrió un error al intentar iniciar sesión.",
                                "error"
                            );
                        } else {
                            alert(
                                error.message ||
                                    "Ocurrió un error al intentar iniciar sesión."
                            );
                        }
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            } else {
                const alerta = document.createElement("div");
                alerta.id = "registro-alerta";
                alerta.className =
                    "fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-100 border border-green-400 text-green-800 px-6 py-3 rounded-xl shadow-lg z-50";
                alerta.innerText = "✅ ¡Cuenta creada con éxito!";
                document.body.appendChild(alerta);
                setTimeout(() => {
                    document.body.removeChild(alerta);
                    window.location.href = "/admin/perfil";
                }, 2500);
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
