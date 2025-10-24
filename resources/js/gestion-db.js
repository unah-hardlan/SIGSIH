document.addEventListener('alpine:init', () => {
    const factory = () => ({
        tab: localStorage.getItem('dbTab') || 'respaldo',
        showModal: false,
        modalMsg: '',
        openModal(msg) {
            this.modalMsg = msg;
            this.showModal = true;
            document.documentElement.classList.add('overflow-hidden');
        },
        closeModal() {
            this.showModal = false;
            document.documentElement.classList.remove('overflow-hidden');
        },
        estadoConexion: 'inicial',
        // Ruta sugerida por defecto (puedes cambiarla antes de respaldar)
        path: '',
        isBackingUp: false,
        backupMsg: '',
        downloadUrl: '',
        driver: 'mysql',
        respaldoExitoso: false,
        mensajeRespaldo: '',
        confirmPassword: '',
        lastBackupAt: null,
        init() {
            const pad = n => String(n).padStart(2, '0');
            const d = new Date();
            const ts = `${d.getFullYear()}${pad(d.getMonth() + 1)}${pad(d.getDate())}-${pad(d.getHours())}${pad(d.getMinutes())}${pad(d.getSeconds())}`;
            if (!this.path) {
                this.path = 'C\\backups\\backup-' + ts + '.sql';
            }
        },
        async doBackup() {
            this.isBackingUp = true;
            this.backupMsg = '';
            try {
                const body = {
                    path: this.path,
                    confirm_password: this.confirmPassword
                };
                const r = await fetch('/api/db/backup', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    credentials: 'include',
                    body: JSON.stringify(body)
                });
                const data = await r.json().catch(() => ({}));
                // Soportar "soft error" (HTTP 200 con ok=false)
                if (data && data.ok === false && (data.code === 'INVALID_CONFIRM_PASSWORD' || data.code === 'MISSING_CONFIRM_PASSWORD')) {
                    const msg = (data && data.errors && data.errors.confirm_password && data.errors.confirm_password[0]) || data.error || 'Contraseña incorrecta';
                    this.backupMsg = msg;
                    this.respaldoExitoso = false;
                    this.mensajeRespaldo = (data.code === 'MISSING_CONFIRM_PASSWORD') ? 'Falta contraseña' : 'Contraseña incorrecta';
                    return;
                }
                if (!r.ok) {
                    // Manejo explícito de validaciones (422) o forbidden (403)
                    if (r.status === 422 || r.status === 403) {
                        const msg = (data && data.errors && data.errors.confirm_password && data.errors.confirm_password[0]) || data.error || 'Contraseña incorrecta';
                        this.backupMsg = msg;
                        this.respaldoExitoso = false;
                        this.mensajeRespaldo = 'Contraseña incorrecta';
                        return; // Evitar lanzar excepción para no llenar consola
                    }
                    // Otros errores
                    const msg = data.message || data.error || 'Fallo realizando respaldo';
                    this.backupMsg = msg;
                    this.respaldoExitoso = false;
                    this.mensajeRespaldo = 'Error al respaldar';
                    return;
                }
                this.backupMsg = `Respaldo listo: ${data.path || ''}`;
                this.respaldoExitoso = true;
                this.mensajeRespaldo = 'Respaldo exitoso';
                this.lastBackupAt = new Date();
                if (data.download_url) {
                    this.downloadUrl = data.download_url;
                }
            } finally {
                this.isBackingUp = false;
            }
        }
    });

    Alpine.data('__backupDb', factory);
    Alpine.data('gestionDb', factory);

    // Expose to window for backwards compatibility with inline templates
    window.__backupDb = factory;
    window.gestionDb = factory;
});
