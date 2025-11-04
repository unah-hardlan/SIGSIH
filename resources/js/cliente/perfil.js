function obtenerPersonaData() {
    try {
        const el = document.getElementById("persona-json");
        if (el && el.textContent) {
            return JSON.parse(el.textContent);
        }
    } catch (e) {
        console.warn("No se pudo parsear persona-json:", e);
    }
    return {
        primer_nombre: "",
        segundo_nombre: "",
        primer_apellido: "",
        segundo_apellido: "",
        dni: "",
        id_genero_fk: "",
    };
}

window.perfilData = function (el) {
    const originalData = obtenerPersonaData();
    const updateUrl = el?.dataset?.updateUrl || "";

    return {
        showEditModal: false,
        showEmpresaModal: false,
        loading: false,
        empresaLoading: false,
        avatarFile: null,
        avatarPreviewUrl: null,
        empresaAvatarFile: null,
        empresaAvatarPreviewUrl: null,
        originalData: originalData,
        formData: { ...originalData },
        empresaForm: (function () {
            try {
                const elEmp = document.getElementById("empresa-json");
                if (elEmp && elEmp.textContent) {
                    const data = JSON.parse(elEmp.textContent);
                    return {
                        nombre_comercial: data.nombre_comercial || "",
                        razon_social: data.razon_social || "",
                        rtn: data.rtn || "",
                        descripcion_empresa: data.descripcion_empresa || "",
                        horario_atencion: data.horario_atencion || "",
                    };
                }
            } catch (_) { }
            return {
                nombre_comercial: "",
                razon_social: "",
                rtn: "",
                descripcion_empresa: "",
                horario_atencion: "",
            };
        })(),

        twoFAEnabled: false,
        show2FASetup: false,
        twoFASetup: {
            loading: false,
            otpauthUrl: "",
            qrUrl: "",
            code: "",
            error: "",
            confirming: false,
            recoveryCodes: [],
            currentPassword: "",
        },
        showPasswordModal: false,
        modalError: "",
        passwordModal: {
            title: "",
            description: "",
            password: "",
            loading: false,
        },
        pendingAction: null,

        openEditModal() {
            this.showEditModal = true;
            if (!this._scrollLocked) {
                this._scrollY = window.scrollY || window.pageYOffset;
                this._prev = {
                    position: document.body.style.position,
                    top: document.body.style.top,
                    width: document.body.style.width,
                    overflow: document.body.style.overflow,
                    height: document.body.style.height,
                };
                document.body.style.position = "fixed";
                document.body.style.top = `-${this._scrollY}px`;
                document.body.style.width = "100%";
                document.body.style.overflow = "hidden";
                document.body.style.height = "100vh";
                this._scrollLocked = true;
            }
            requestAnimationFrame(() => {
                const modal = document.querySelector(".modal-content");
                if (modal) modal.scrollTop = 0;
            });
        },

        closeEditModal() {
            this.showEditModal = false;
            if (this._scrollLocked) {
                document.body.style.position = this._prev.position || "";
                document.body.style.top = this._prev.top || "";
                document.body.style.width = this._prev.width || "";
                document.body.style.overflow = this._prev.overflow || "";
                document.body.style.height = this._prev.height || "";
                window.scrollTo(0, this._scrollY || 0);
                this._scrollLocked = false;
            }
            this.formData = { ...this.originalData };
            this.avatarFile = null;
        },

        openEmpresaModal() {
            this.showEmpresaModal = true;
            if (!this._scrollLocked) {
                this._scrollY = window.scrollY || window.pageYOffset;
                this._prev = {
                    position: document.body.style.position,
                    top: document.body.style.top,
                    width: document.body.style.width,
                    overflow: document.body.style.overflow,
                    height: document.body.style.height,
                };
                document.body.style.position = "fixed";
                document.body.style.top = `-${this._scrollY}px`;
                document.body.style.width = "100%";
                document.body.style.overflow = "hidden";
                document.body.style.height = "100vh";
                this._scrollLocked = true;
            }
        },
        closeEmpresaModal() {
            this.showEmpresaModal = false;
            if (this._scrollLocked) {
                document.body.style.position = this._prev.position || "";
                document.body.style.top = this._prev.top || "";
                document.body.style.width = this._prev.width || "";
                document.body.style.overflow = this._prev.overflow || "";
                document.body.style.height = this._prev.height || "";
                window.scrollTo(0, this._scrollY || 0);
                this._scrollLocked = false;
            }
            this.empresaAvatarFile = null;
        },
        handleEmpresaAvatar(e) {
            const file = e.target.files[0];
            if (!file) return;
            if (file.size > 2 * 1024 * 1024) {
                window.showToast?.("Archivo demasiado grande (máx 2MB)", "error");
                return;
            }
            if (!file.type.startsWith("image/")) {
                window.showToast?.("Solo se permiten imágenes", "warning");
                return;
            }
            this.empresaAvatarFile = file;
            try {
                if (this.empresaAvatarPreviewUrl) URL.revokeObjectURL(this.empresaAvatarPreviewUrl);
                this.empresaAvatarPreviewUrl = URL.createObjectURL(file);
            } catch (_) { }
        },

        handleAvatarChange(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    window.showToast?.("El archivo es demasiado grande. Máximo 2MB.", "error");
                    return;
                }
                if (!file.type.startsWith("image/")) {
                    window.showToast?.("Solo se permiten archivos de imagen.", "warning");
                    return;
                }
                this.avatarFile = file;
                try {
                    if (this.avatarPreviewUrl) URL.revokeObjectURL(this.avatarPreviewUrl);
                    this.avatarPreviewUrl = URL.createObjectURL(file);
                } catch (_) { }
            }
        },

        formatDNI(event) {
            let value = event.target.value.replace(/\D/g, "");
            if (value.length >= 4) {
                value = value.substring(0, 4) + "-" + value.substring(4);
            }
            if (value.length >= 9) {
                value = value.substring(0, 9) + "-" + value.substring(9);
            }
            if (value.length > 15) {
                value = value.substring(0, 15);
            }
            this.formData.dni = value;
        },

        async updateProfile() {
            if (!this.formData.primer_nombre?.trim()) {
                window.showToast?.("El primer nombre es requerido", "warning");
                return;
            }
            if (!this.formData.primer_apellido?.trim()) {
                window.showToast?.("El primer apellido es requerido", "warning");
                return;
            }
            if (!this.formData.dni?.trim()) {
                window.showToast?.("El DNI es requerido", "warning");
                return;
            }
            if (!updateUrl) {
                window.showToast?.("No se encontró la URL de actualización.", "error");
                return;
            }

            this.loading = true;
            try {
                const formData = new FormData();
                Object.keys(this.formData).forEach((key) => {
                    if (
                        this.formData[key] !== null &&
                        this.formData[key] !== ""
                    ) {
                        formData.append(key, this.formData[key]);
                    }
                });
                if (this.avatarFile) {
                    formData.append("avatar", this.avatarFile);
                }
                const csrfMeta = document.querySelector(
                    'meta[name="csrf-token"]'
                );
                const csrf = csrfMeta ? csrfMeta.getAttribute("content") : null;
                if (csrf) formData.append("_token", csrf);
                formData.append("_method", "PUT");

                const response = await fetch(updateUrl, {
                    method: "POST",
                    body: formData,
                });
                const result = await response.json();
                if (result.success) {
                    window.showToast?.("Perfil actualizado correctamente", "success");
                    // Refrescar encabezado sin recargar página
                    try {
                        const headerName = document.getElementById("perfil-header-nombre");
                        if (headerName) {
                            const nombres = [
                                this.formData.primer_nombre,
                                this.formData.segundo_nombre,
                                this.formData.primer_apellido,
                                this.formData.segundo_apellido,
                            ]
                                .filter(Boolean)
                                .join(" ");
                            headerName.textContent = nombres || headerName.textContent;
                        }

                        if (this.avatarPreviewUrl) {
                            const avatarBox = document.getElementById("perfil-header-avatar");
                            if (avatarBox) {
                                avatarBox.innerHTML = `
                                    <img src="${this.avatarPreviewUrl}" alt="Avatar" class="w-20 h-20 rounded-full object-cover border border-blue-200 dark:border-blue-300" />
                                `;
                            }
                        }
                    } catch (_) { }

                    // Actualizar estado local y cerrar modal
                    this.originalData = { ...this.formData };
                    this.closeEditModal();
                    this.avatarFile = null;
                    // Mantener la vista previa actual para que el usuario la vea; no la revocamos de inmediato
                } else {
                    window.showToast?.(result.message || "Error al actualizar el perfil", "error");
                }
            } catch (e) {
                console.error("Error:", e);
                window.showToast?.(
                    "Error al actualizar el perfil. Por favor, intenta nuevamente.",
                    "error"
                );
            } finally {
                this.loading = false;
            }
        },
        async updateEmpresa() {
            if (!this.empresaForm.nombre_comercial.trim()) {
                window.showToast?.("Nombre comercial requerido", "warning");
                return;
            }
            const empresaUrl = el?.dataset?.empresaUpdateUrl;
            if (!empresaUrl) {
                window.showToast?.("No se encontró URL de empresa", "error");
                return;
            }
            this.empresaLoading = true;
            try {
                const fd = new FormData();
                Object.entries(this.empresaForm).forEach(([k, v]) => {
                    if (v !== null && v !== "") fd.append(k, v);
                });
                if (this.empresaAvatarFile)
                    fd.append("avatar", this.empresaAvatarFile);
                const csrfMeta = document.querySelector(
                    'meta[name="csrf-token"]'
                );
                const csrf = csrfMeta ? csrfMeta.getAttribute("content") : null;
                if (csrf) fd.append("_token", csrf);
                fd.append("_method", "PUT");
                const resp = await fetch(empresaUrl, {
                    method: "POST",
                    body: fd,
                });
                const json = await resp.json();
                if (json.success) {
                    window.showToast?.("Empresa actualizada correctamente", "success");
                    try {
                        const headerName = document.getElementById("perfil-header-nombre");
                        if (headerName) headerName.textContent = this.empresaForm.nombre_comercial;
                        if (this.empresaAvatarPreviewUrl) {
                            const avatarBox = document.getElementById("perfil-header-avatar");
                            if (avatarBox) {
                                avatarBox.innerHTML = `
                                    <img src="${this.empresaAvatarPreviewUrl}" alt="Logo" class="w-20 h-20 rounded-full object-cover border border-blue-200 dark:border-blue-300" />
                                `;
                            }
                        }
                    } catch (_) { }
                    this.closeEmpresaModal();
                } else {
                    window.showToast?.(json.message || "Error al actualizar empresa", "error");
                }
            } catch (err) {
                console.error(err);
                window.showToast?.("Error al actualizar empresa", "error");
            } finally {
                this.empresaLoading = false;
            }
        },

        async load2FAStatus() {
            try {
                const response = await fetch("/cliente/2fa/status", {
                    method: "GET",
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                        Accept: "application/json",
                        "Content-Type": "application/json",
                    },
                    credentials: "same-origin",
                });
                const result = await response.json();
                if (result.success) {
                    this.twoFAEnabled = !!result.data?.is_enabled;
                }
            } catch (error) {
                console.error("Error loading 2FA status:", error);
            }
        },

        async start2FA() {
            if (!this.twoFASetup.currentPassword) {
                this.openPasswordModal({
                    action: "start2fa",
                    title: "Activar 2FA",
                    description:
                        "Ingresa tu contraseña actual para activar 2FA.",
                });
                return;
            }
            try {
                this.twoFASetup.loading = true;
                this.twoFASetup.error = "";
                const res = await fetch("/cliente/2fa/setup/start", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                    credentials: "same-origin",
                    body: JSON.stringify({
                        current_password: this.twoFASetup.currentPassword,
                    }),
                });
                if (!res.ok) {
                    const err = await res
                        .json()
                        .catch(() => ({
                            message: "No se pudo iniciar el setup 2FA",
                        }));
                    throw new Error(
                        err.message || "No se pudo iniciar el setup 2FA"
                    );
                }
                const data = await res.json();
                this.twoFASetup.otpauthUrl = data?.otpauth_url || "";
                const enc = encodeURIComponent(this.twoFASetup.otpauthUrl);
                this.twoFASetup.qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=192x192&data=${enc}`;
                this.show2FASetup = true;
                this.lockScroll();
            } catch (e) {
                this.modalError = e.message || "Error al iniciar 2FA";
                this.showPasswordModal = true;
                return;
            } finally {
                this.twoFASetup.loading = false;
            }
        },

        async confirm2FA() {
            if (!this.twoFASetup.currentPassword) {
                this.openPasswordModal({
                    action: "confirm2fa",
                    title: "Confirmar 2FA",
                    description:
                        "Ingresa tu contraseña actual para confirmar 2FA.",
                });
                return;
            }
            try {
                this.twoFASetup.confirming = true;
                this.twoFASetup.error = "";
                const res = await fetch("/cliente/2fa/setup/confirm", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                    credentials: "same-origin",
                    body: JSON.stringify({
                        code: this.twoFASetup.code,
                        current_password: this.twoFASetup.currentPassword,
                    }),
                });
                if (!res.ok) {
                    const err = await res
                        .json()
                        .catch(() => ({ message: "Error" }));
                    this.twoFASetup.error = err.message || "Código inválido";
                    return;
                }
                const data = await res.json();
                this.twoFASetup.recoveryCodes = Array.isArray(
                    data?.recovery_codes
                )
                    ? data.recovery_codes
                    : [];
                this.twoFAEnabled = true;
                this.twoFASetup.code = "";
                this.twoFASetup.currentPassword = "";
            } catch (e) {
                this.twoFASetup.error = e.message || "Error al confirmar 2FA";
            } finally {
                this.twoFASetup.confirming = false;
            }
        },

        async disable2FA() {
            if (!this.twoFASetup.currentPassword) {
                this.openPasswordModal({
                    action: "disable2fa",
                    title: "Desactivar 2FA",
                    description: "Confirma tu contraseña para desactivar 2FA.",
                });
                return;
            }
            await this._performDisable2FA();
        },

        openPasswordModal(config) {
            this.pendingAction = config?.action || null;
            this.passwordModal.title = config?.title || "";
            this.passwordModal.description = config?.description || "";
            this.passwordModal.password = "";
            this.passwordModal.loading = false;
            this.modalError = "";
            this.showPasswordModal = true;
            this.lockScroll();
        },

        closePasswordModal() {
            this.showPasswordModal = false;
            this.pendingAction = null;
            this.passwordModal = {
                title: "",
                description: "",
                password: "",
                loading: false,
            };
            this.modalError = "";
            this.unlockScroll();
        },

        lockScroll() {
            if (!this._scrollLocked) {
                this._scrollY = window.scrollY || window.pageYOffset;
                this._prev = {
                    position: document.body.style.position,
                    top: document.body.style.top,
                    width: document.body.style.width,
                    overflow: document.body.style.overflow,
                    height: document.body.style.height,
                };
                document.body.style.position = "fixed";
                document.body.style.top = `-${this._scrollY}px`;
                document.body.style.width = "100%";
                document.body.style.overflow = "hidden";
                document.body.style.height = "100vh";
                this._scrollLocked = true;
            }
        },

        unlockScroll() {
            if (this._scrollLocked) {
                document.body.style.position = this._prev.position || "";
                document.body.style.top = this._prev.top || "";
                document.body.style.width = this._prev.width || "";
                document.body.style.overflow = this._prev.overflow || "";
                document.body.style.height = this._prev.height || "";
                window.scrollTo(0, this._scrollY || 0);
                this._scrollLocked = false;
            }
        },

        async submitPasswordModal() {
            if (!this.passwordModal.password.trim()) return;
            this.passwordModal.loading = true;
            this.modalError = "";

            this.twoFASetup.currentPassword = this.passwordModal.password;

            const action = this.pendingAction;
            this.closePasswordModal();

            if (action === "start2fa") {
                await this.start2FA();
            } else if (action === "disable2fa") {
                await this._performDisable2FA();
            } else if (action === "confirm2fa") {
                await this.confirm2FA();
            }
        },

        async _performDisable2FA() {
            try {
                const res = await fetch("/cliente/2fa/disable", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                    credentials: "same-origin",
                    body: JSON.stringify({
                        current_password: this.twoFASetup.currentPassword,
                    }),
                });
                if (!res.ok) {
                    const err = await res
                        .json()
                        .catch(() => ({
                            message: "No se pudo desactivar 2FA",
                        }));
                    throw new Error(err.message || "No se pudo desactivar 2FA");
                }
                this.twoFAEnabled = false;
                this.show2FASetup = false;
                this.twoFASetup = {
                    loading: false,
                    otpauthUrl: "",
                    qrUrl: "",
                    code: "",
                    error: "",
                    confirming: false,
                    recoveryCodes: [],
                    currentPassword: "",
                };
                this.unlockScroll();
            } catch (e) {
                this.modalError = e.message || "Error al desactivar 2FA";
                this.openPasswordModal({
                    action: "disable2fa",
                    title: "Desactivar 2FA",
                    description: "Confirma tu contraseña para desactivar 2FA.",
                });
            }
        },

        cancel2FA() {
            this.show2FASetup = false;
            this.twoFASetup = {
                loading: false,
                otpauthUrl: "",
                qrUrl: "",
                code: "",
                error: "",
                confirming: false,
                recoveryCodes: [],
                currentPassword: "",
            };
            this.unlockScroll();
        },

        copyOtpUrl() {
            if (!this.twoFASetup.otpauthUrl) return;
            navigator.clipboard?.writeText?.(this.twoFASetup.otpauthUrl);
        },

        copyRecoveryCodes() {
            const txt = (this.twoFASetup.recoveryCodes || []).join("\n");
            if (!txt) return;
            navigator.clipboard?.writeText?.(txt);
        },

        async generateRecoveryCodes() {
            try {
                this.loading = true;
                const response = await fetch("/cliente/2fa/recovery-codes", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                        Accept: "application/json",
                        "Content-Type": "application/json",
                    },
                    credentials: "same-origin",
                });
                const result = await response.json();
                if (result.success) {
                    this.twoFASetup.recoveryCodes = result.data.recovery_codes;
                } else {
                    alert(
                        result.message ||
                        "Error al generar códigos de recuperación"
                    );
                }
            } catch (error) {
                console.error("Error generating recovery codes:", error);
                alert("Error al generar códigos de recuperación");
            } finally {
                this.loading = false;
            }
        },

        init() {
            this.load2FAStatus();
        },
    };
};
