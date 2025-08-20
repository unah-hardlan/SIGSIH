// Asegura que Axios envíe/reciba cookies cuando corresponda
if (window.axios) {
    axios.defaults.withCredentials = true;
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
}

document.addEventListener('alpine:init', () => {
    Alpine.data('authPage', () => ({
        isLogin: true,
        showPassword: false,
        showConfirmPassword: false,
        username: '',
        password: '',
        confirmPassword: '',
        nombre_usuario: '',
        email: '',
        loading: false,
        isDark: false,

        initTheme() {
            this.isDark = localStorage.getItem('sigTheme') === 'dark';
        },
        toggleTheme() {
            this.isDark = !this.isDark;
            localStorage.setItem('sigTheme', this.isDark ? 'dark' : 'light');
        },
        validatePassword(pw) {
            return (pw || '').length >= 8;
        },
        validateConfirmPassword() {
            return this.password === this.confirmPassword;
        },

        async handleSubmit() {
            this.loading = true;

            try {
                if (this.isLogin) {
                    const res = await axios.post('/api/login', {
                        usuario: this.username,
                        contrasena: this.password
                    });

                    // Guarda el token para las llamadas a /api/* (Authorization: Bearer)
                    if (res.data && res.data.token) {
                        localStorage.setItem('authToken', res.data.token);
                    }

                    // La cookie HttpOnly 'auth_token' la dejó el backend en Set-Cookie.
                    // Redirige al dashboard protegido.
                    window.location.assign('/admin/dashboard');
                    return;
                } else {
                    const res = await axios.post('/api/usuarios', {
                        usuario: this.username,
                        nombre_usuario: this.nombre_usuario,
                        correo_electronico: this.email,
                        contrasena: this.password,
                    });

                    const alerta = document.createElement('div');
                    alerta.id = 'registro-alerta';
                    alerta.className =
                        'fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-100 border border-green-400 text-green-800 px-6 py-3 rounded-xl shadow-lg z-50';
                    alerta.innerText = '✅ ¡Cuenta creada con éxito! Ahora inicia sesión.';
                    document.body.appendChild(alerta);
                    setTimeout(() => {
                        document.body.removeChild(alerta);
                        this.isLogin = true;
                        this.loading = false;
                    }, 1800);
                }
            } catch (err) {
                console.error('Error auth:', err?.response?.data || err);
                const msg = err?.response?.data?.error || err?.response?.data?.message || 'Error de autenticación';
                alert(msg);
                this.loading = false;
            }
        },

        handleGoogle() { alert('Redirigiendo a Google Sign-In…'); },
        handleRecover() { alert('Redirigiendo a recuperar contraseña…'); },
    }));
});
