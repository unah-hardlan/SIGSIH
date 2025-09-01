window.authPage = function () {
  return {
    isLogin: true,
    username: '',
    password: '',
    nombre_usuario: '',
    email: '',
    confirmPassword: '',
    loading: false,
    showPassword: false,
    showConfirmPassword: false,
    isDark: false,
    init() {
      // Tema almacenado
      this.isDark = localStorage.getItem('theme') === 'dark';
      // Fuerza MAYÚSCULAS en usuario y evita espacios
      this.$watch('username', (v) => {
        if (typeof v === 'string') {
          const up = v.toUpperCase().replace(/\s+/g, '');
          if (up !== v) this.username = up.slice(0, 50);
          else if (v.length > 50) this.username = v.slice(0, 50);
        }
      });
      // Evitar espacios en password, limitar longitud
      this.$watch('password', (v) => {
        if (typeof v === 'string') {
          const noSpace = v.replace(/\s+/g, '');
          if (noSpace !== v) this.password = noSpace.slice(0, 100);
          else if (v.length > 100) this.password = v.slice(0, 100);
        }
      });
      this.$watch('confirmPassword', (v) => {
        if (typeof v === 'string') {
          const noSpace = v.replace(/\s+/g, '');
          if (noSpace !== v) this.confirmPassword = noSpace.slice(0, 100);
          else if (v.length > 100) this.confirmPassword = v.slice(0, 100);
        }
      });
    },
    toggleTheme() {
      this.isDark = !this.isDark;
      localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
    },
        validatePassword(pw) {
            if (!pw || pw.length < 8) return false;
            // Sin espacios
            return /^\S{8,100}$/.test(pw);
        },
    validateConfirmPassword() {
      return this.password && this.password === this.confirmPassword;
    },
    async handleSubmit() {
      const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      if (!this.username) { alert('Debe ingresar el usuario.'); return; }
      if (!this.password) { alert('Debe ingresar la contraseña.'); return; }
      if (/\s/.test(this.username) || /\s/.test(this.password)) { alert('Los espacios no están permitidos.'); return; }
      if (!this.validatePassword(this.password)) { alert('La contraseña no cumple la política.'); return; }
      this.loading = true;
      try {
        if (this.isLogin) {
          const res = await fetch('/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ usuario: this.username, contrasena: this.password })
          });
          const data = await res.json().catch(() => ({}));
          if (!res.ok) { alert(data.error || 'Usuario/contraseña inválidos'); return; }
          window.location.assign('/admin/dashboard');
        } else {
          if (!this.nombre_usuario || !this.email || !this.validateConfirmPassword()) {
            alert('Complete los campos requeridos.'); return;
          }
          const res = await fetch('/register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({
              usuario: this.username,
              nombre_usuario: this.nombre_usuario,
              correo_electronico: this.email,
              contrasena: this.password
            })
          });
          const data = await res.json().catch(() => ({}));
          if (!res.ok) { alert(data.error || 'No se pudo registrar.'); return; }
          window.location.assign('/admin/dashboard');
        }
      } finally { this.loading = false; }
    },
    handleRecover() { alert('Recuperación no implementada aún.'); },
    handleGoogle() { alert('Google Sign-In no implementado.'); }
  };
};document.addEventListener("alpine:init", () => {
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

        // Traduce mensajes comunes del servidor o de fallback al español
        translateMessage(msg) {
            if (!msg) return msg;
            const m = msg.toString();
            const map = {
                'The given data was invalid.': 'Los datos enviados son inválidos.',
                'Unauthenticated.': 'No autenticado.',
                'Unauthorized.': 'No autorizado.',
                'User already exists': 'El usuario ya existe.',
                'Invalid credentials': 'Credenciales inválidas.',
                'The provided credentials are incorrect.': 'Credenciales incorrectas.',
                'Validation failed': 'Validación fallida.',
                'Error': 'Error',
            };
            // Reemplazos directos
            if (map[m]) return map[m];

            // Manejar patrones como: "El usuario ya está en uso." -> "El usuario ya está en uso."
            const takenMatch = m.match(/El (.+) ya está en uso\./i);
            if (takenMatch) {
                const rawField = takenMatch[1];
                const field = rawField.replace(/_/g, ' ').trim();
                const fieldMap = {
                    'correo electronico': 'correo electrónico',
                    'correo': 'correo electrónico',
                    'email': 'correo electrónico',
                    'usuario': 'usuario',
                    'nombre usuario': 'nombre de usuario',
                    'nombre_usuario': 'nombre de usuario',
                    'contrasena': 'contraseña',
                    'password': 'contraseña',
                };
                const human = fieldMap[field.toLowerCase()] || field;
                return `El ${human} ya está en uso.`;
            }

            // Manejar "The given data was invalid." u otros mensajes compuestos
            const invalidMatch = m.match(/The given data was invalid\./i);
            if (invalidMatch) return 'Los datos enviados son inválidos.';

            return m;
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
                        let data = {};
                        try {
                            data = await response.json();
                        } catch (e) {
                            // no JSON
                        }
                        if (!response.ok) {
                            const msg = data.error || data.message || "Error de autenticación";
                            const err = new Error(msg);
                            err.status = response.status;
                            err.details = data.errors || null;
                            throw err;
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
                        // Si hay errores de validación (Laravel 422), mostrar detalles
                        if (error.status === 422 && error.details) {
                            const rawMsgs = Object.values(error.details).flat();
                            const msgs = rawMsgs.map(m => this.translateMessage(m)).join(" \n");
                            if (window.showToast) showToast(msgs, "error");
                            else alert(msgs);
                            this.loading = false;
                            return;
                        }

                        const translated = this.translateMessage(error.message) || this.translateMessage('Ocurrió un error al intentar iniciar sesión.');
                        if (window.showToast) {
                            showToast(translated, "error");
                        } else {
                            alert(translated);
                        }
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            } else {
                // Envío real al endpoint de registro
                fetch("/api/register", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    credentials: "same-origin",
                    body: JSON.stringify({
                        usuario: this.username,
                        nombre_usuario: this.nombre_usuario,
                        correo_electronico: this.email,
                        contrasena: this.password,
                    }),
                })
                    .then(async (response) => {
                        let data = {};
                        try {
                            data = await response.json();
                        } catch (e) {
                            // respuesta vacía o no JSON
                        }
                        if (!response.ok) {
                            const msg = data.error || data.message || "Error en el registro";
                            const err = new Error(msg);
                            err.status = response.status;
                            err.details = data.errors || null;
                            throw err;
                        }
                        return data;
                    })
                    .then((data) => {
                        if (data.token) {
                            localStorage.setItem("authToken", data.token);
                            if (window.showToast) {
                                showToast("Cuenta creada", "success", { duration: 1500 });
                                setTimeout(() => (window.location.href = "/admin/perfil"), 800);
                            } else {
                                window.location.href = "/admin/perfil";
                            }
                        } else {
                            if (window.showToast) {
                                showToast("Cuenta creada", "success", { duration: 1500 });
                                setTimeout(() => (window.location.href = "/admin/perfil"), 800);
                            } else {
                                window.location.href = "/admin/perfil";
                            }
                        }
                    })
                    .catch((error) => {
                        console.error("Error registro:", error);
                        // Validación (422) -> mostrar mensajes detallados
                        if (error.status === 422 && error.details) {
                            const rawMsgs = Object.values(error.details).flat();
                            const msgs = rawMsgs.map(m => this.translateMessage(m)).join(" \n");
                            if (window.showToast) showToast(msgs, "error");
                            else alert(msgs);
                            this.loading = false;
                            return;
                        }

                        const translated = this.translateMessage(error.message || 'Ocurrió un error al registrar la cuenta.');
                        if (error.status === 409) {
                            if (window.showToast) showToast(translated, "error");
                            else alert(translated);
                        } else {
                            if (window.showToast) showToast(translated, "error");
                            else alert(translated);
                        }
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            }
        },
        handleGoogle() {
            alert("Redirigiendo a Google…");
        },
        handleRecover() {
            alert("Redirigiendo a recuperar contraseña…");
        },
    }));
});
