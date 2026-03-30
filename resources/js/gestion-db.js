document.addEventListener("alpine:init", () => {
    const factory = () => ({
        tab: localStorage.getItem("dbTab") || "respaldo",
        showModal: false,
        modalMsg: "",
        modalAction: "backup",
        selectedBackupId: null,
        openModal(msg) {
            this.modalMsg = msg;
            this.showModal = true;
            document.documentElement.classList.add("overflow-hidden");
        },
        closeModal() {
            this.showModal = false;
            document.documentElement.classList.remove("overflow-hidden");
        },
        estadoConexion: "inicial",

        path: "",
        isBackingUp: false,
        backupMsg: "",
        downloadUrl: "",
        driver: "mysql",
        respaldoExitoso: false,
        mensajeRespaldo: "",
        confirmPassword: "",
        lastBackupAt: null,
        backups: [],
        backupsMeta: {
            max_backups: 10,
            total_activos: 0,
            will_delete_oldest_on_next: false,
            oldest_backup_name: null,
        },
        loadingBackups: false,
        warningMsg: "",
        restoringId: null,
        deletingId: null,
        init() {
            const pad = (n) => String(n).padStart(2, "0");
            const d = new Date();
            const ts = `${d.getFullYear()}${pad(d.getMonth() + 1)}${pad(
                d.getDate()
            )}-${pad(d.getHours())}${pad(d.getMinutes())}${pad(
                d.getSeconds()
            )}`;
            if (!this.path) {
                this.path = "C\\backups\\backup-" + ts + ".sql";
            }
            this.fetchBackups();
        },
        openBackupModal() {
            this.modalAction = "backup";
            this.selectedBackupId = null;
            this.openModal("¿Deseas confirmar el respaldo de la base de datos?");
        },
        openRestoreModal(backupId) {
            this.modalAction = "restore";
            this.selectedBackupId = backupId;
            this.openModal("La restauración sobrescribirá datos actuales. ¿Deseas continuar?");
        },
        async executeModalAction() {
            this.closeModal();
            if (this.modalAction === "restore" && this.selectedBackupId) {
                await this.restoreBackup(this.selectedBackupId);
                return;
            }
            await this.doBackup();
        },
        async doBackup() {
            this.isBackingUp = true;
            this.backupMsg = "";
            try {
                const body = {
                    path: this.path,
                    confirm_password: this.confirmPassword,
                };
                const r = await fetch("/api/db/backup", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    credentials: "include",
                    body: JSON.stringify(body),
                });
                const data = await r.json().catch(() => ({}));

                if (
                    data &&
                    data.ok === false &&
                    (data.code === "INVALID_CONFIRM_PASSWORD" ||
                        data.code === "MISSING_CONFIRM_PASSWORD")
                ) {
                    const msg =
                        (data &&
                            data.errors &&
                            data.errors.confirm_password &&
                            data.errors.confirm_password[0]) ||
                        data.error ||
                        "Contraseña incorrecta";
                    this.backupMsg = msg;
                    this.respaldoExitoso = false;
                    this.mensajeRespaldo =
                        data.code === "MISSING_CONFIRM_PASSWORD"
                            ? "Falta contraseña"
                            : "Contraseña incorrecta";
                    return;
                }
                if (!r.ok) {
                    if (r.status === 422 || r.status === 403) {
                        const msg =
                            (data &&
                                data.errors &&
                                data.errors.confirm_password &&
                                data.errors.confirm_password[0]) ||
                            data.error ||
                            "Contraseña incorrecta";
                        this.backupMsg = msg;
                        this.respaldoExitoso = false;
                        this.mensajeRespaldo = "Contraseña incorrecta";
                        return;
                    }

                    const msg =
                        data.message ||
                        data.error ||
                        "Fallo realizando respaldo";
                    this.backupMsg = msg;
                    this.respaldoExitoso = false;
                    this.mensajeRespaldo = "Error al respaldar";
                    return;
                }
                this.backupMsg = `Respaldo listo: ${data.path || ""}`;
                this.respaldoExitoso = true;
                this.mensajeRespaldo = "Respaldo exitoso";
                this.lastBackupAt = new Date();
                this.warningMsg = data.will_delete_oldest_on_next
                    ? `Al generar un nuevo respaldo, se eliminará el más antiguo: ${data.oldest_backup_name || "(sin nombre)"
                    }`
                    : "";
                if (data.download_url) {
                    this.downloadUrl = data.download_url;
                }
                await this.fetchBackups();
            } finally {
                this.isBackingUp = false;
                this.confirmPassword = "";
            }
        },
        async fetchBackups() {
            this.loadingBackups = true;
            try {
                const r = await fetch('/api/db/backups', {
                    headers: {
                        Accept: 'application/json',
                    },
                    credentials: 'include',
                });
                const data = await r.json().catch(() => ({}));
                if (!r.ok) {
                    this.backups = [];
                    return;
                }
                this.backups = Array.isArray(data?.data) ? data.data : [];
                this.backupsMeta = {
                    ...this.backupsMeta,
                    ...(data?.meta || {}),
                };
                this.warningMsg = this.backupsMeta.will_delete_oldest_on_next
                    ? `Al generar un nuevo respaldo, se eliminará el más antiguo: ${this.backupsMeta.oldest_backup_name || "(sin nombre)"
                    }`
                    : "";
            } finally {
                this.loadingBackups = false;
            }
        },
        async restoreBackup(backupId) {
            this.restoringId = backupId;
            try {
                const r = await fetch(`/api/db/backups/${backupId}/restore`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                    },
                    credentials: 'include',
                    body: JSON.stringify({ confirm_password: this.confirmPassword }),
                });
                const data = await r.json().catch(() => ({}));
                if (data && data.ok === false && data.code) {
                    this.backupMsg = data.error || 'Contraseña incorrecta';
                    this.mensajeRespaldo = 'No se pudo restaurar';
                    this.respaldoExitoso = false;
                    return;
                }
                if (!r.ok) {
                    this.backupMsg = data.message || data.error || 'No se pudo restaurar';
                    this.mensajeRespaldo = 'No se pudo restaurar';
                    this.respaldoExitoso = false;
                    return;
                }
                this.backupMsg = data.message || 'Restauración completada';
                this.mensajeRespaldo = 'Restauración exitosa';
                this.respaldoExitoso = true;
            } finally {
                this.restoringId = null;
                this.confirmPassword = '';
            }
        },
        async deleteBackup(backupId) {
            this.deletingId = backupId;
            try {
                const r = await fetch(`/api/db/backups/${backupId}`, {
                    method: 'DELETE',
                    headers: {
                        Accept: 'application/json',
                    },
                    credentials: 'include',
                });
                const data = await r.json().catch(() => ({}));
                if (!r.ok) {
                    this.backupMsg = data.message || data.error || 'No se pudo eliminar el respaldo';
                    this.mensajeRespaldo = 'Error al eliminar';
                    this.respaldoExitoso = false;
                    return;
                }
                this.backupMsg = data.message || 'Respaldo eliminado';
                this.mensajeRespaldo = 'Respaldo eliminado';
                this.respaldoExitoso = true;
                await this.fetchBackups();
            } finally {
                this.deletingId = null;
            }
        },
    });

    Alpine.data("__backupDb", factory);
    Alpine.data("gestionDb", factory);

    window.__backupDb = factory;
    window.gestionDb = factory;
});
