function perfilPage() {
    return {
        success: false,
        saving: false,
        hasChanges: false,
        errorBanner: "",
        fieldErrors: {},
        dniFormat: "",
        dniPattern: null,
        originalForm: {},
        originalAvatar: "",
        passwordForm: {
            current_password: "",
            password: "",
            password_confirmation: "",
        },
        changingPassword: false,
        passwordSuccess: false,
        passwordError: "",

        form: {
            primer_nombre: "",
            segundo_nombre: "",
            primer_apellido: "",
            segundo_apellido: "",
            dni: "",
            id_genero_fk: "",
        },

        generos: [],
        email: "",
        displayName: "Mi Perfil",
        avatarUrl: "",
        personaAvatar: "",
        removing: false,

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
        pendingAction: null,
        modalTitle: "",
        modalDescription: "",
        modalError: "",
        showConfirmModal: false,
        confirmTitle: "",
        confirmDescription: "",
        _confirmResolver: null,

        init() {
            this.$nextTick(() => {
                this.initializeProfileData();
            });
        },

        get displayNameComputed() {
            const n1 = (this.form?.primer_nombre || "").trim();
            const a1 = (this.form?.primer_apellido || "").trim();
            const fallback = (this.displayName || "").trim() || "Mi Perfil";
            const name = [n1, a1].filter(Boolean).join(" ").trim();
            return name || fallback;
        },

        initializeProfileData() {
            try {
                if (window.Alpine && window.Alpine.store) {
                    const store = Alpine.store("perfil");
                    if (store?.user) {
                        this.displayName = store.persona?.primer_nombre
                            ? store.persona.primer_nombre +
                            " " +
                            (store.persona.primer_apellido || "")
                            : store.user.nombre ||
                            store.user.usuario ||
                            "Mi Perfil";
                        this.email = store.user.correo_electronico || "";
                    }
                    if (store?.persona) {
                        const p = store.persona;
                        this.form = {
                            primer_nombre: p.primer_nombre || "",
                            segundo_nombre: p.segundo_nombre || "",
                            primer_apellido: p.primer_apellido || "",
                            segundo_apellido: p.segundo_apellido || "",
                            dni: p.dni || "",
                            id_genero_fk: p.id_genero_fk || "",
                        };
                        this.personaAvatar = p.avatar_path
                            ? p.avatar_path.startsWith("http")
                                ? p.avatar_path
                                : window.location.origin +
                                "/storage/" +
                                p.avatar_path
                            : "";
                    }
                }
            } catch (_) {
                /* noop */
            }

            this.cargarDatos();
            this.cargarCatalogos();
            this.cargarParametros();
        },

        async cargarParametros() {
            try {
                // Buscar parámetro FORMATO DNI (usa api index con filtro q)
                const res = await fetch(
                    "/api/parametros?q=" +
                    encodeURIComponent("FORMATO DNI") +
                    "&per_page=1",
                    { credentials: "same-origin" }
                );
                if (res.ok) {
                    const data = await res.json();
                    const first = Array.isArray(data?.data)
                        ? data.data[0]
                        : null;
                    const valor =
                        first && (first.valor || first["valor"])
                            ? first.valor || first["valor"]
                            : "";
                    this.dniFormat = (valor || "").toString().trim();
                }
            } catch (_) {
                /* noop: usar fallback */
            }
            if (!this.dniFormat) {
                this.dniFormat = "0000-0000-00000";
            }
            this.dniPattern = this.buildDniRegex(this.dniFormat);
        },

        buildDniRegex(format) {
            try {
                const f = (format || "").toString().trim();
                if (!f) {
                    return new RegExp("^(?:\\d{13}|\\d{4}-\\d{4}-\\d{5})$");
                }
                if (/^\d+$/.test(f)) {
                    const n = Math.max(1, parseInt(f, 10) || 1);
                    return new RegExp("^\\d{" + n + ",}$");
                }
                const esc = (s) => s.replace(/[.*+?^${}()|[\]\\\-:]/g, "\\$&");
                let regex = "^";
                for (let i = 0; i < f.length; i++) {
                    const ch = f[i];
                    if (ch === "0") regex += "\\d";
                    else regex += esc(ch);
                }
                regex += "$";
                return new RegExp(regex);
            } catch (_) {
                return new RegExp("^(?:\\d{13}|\\d{4}-\\d{4}-\\d{5})$");
            }
        },

        validarFormulario() {
            const errs = {};
            const msgs = [];
            if (
                !this.form.primer_nombre ||
                !this.form.primer_nombre.toString().trim()
            ) {
                errs.primer_nombre = ["El primer nombre es obligatorio."];
                msgs.push(errs.primer_nombre[0]);
            }
            if (
                !this.form.primer_apellido ||
                !this.form.primer_apellido.toString().trim()
            ) {
                errs.primer_apellido = ["El primer apellido es obligatorio."];
                msgs.push(errs.primer_apellido[0]);
            }
            if (!this.form.id_genero_fk || this.form.id_genero_fk === "") {
                errs.id_genero_fk = ["El género es obligatorio."];
                msgs.push(errs.id_genero_fk[0]);
            }
            const dni = (this.form.dni || "").toString().trim();
            if (!dni) {
                errs.dni = ["El DNI es obligatorio."];
                msgs.push(errs.dni[0]);
            } else {
                const re =
                    this.dniPattern instanceof RegExp
                        ? this.dniPattern
                        : this.buildDniRegex(this.dniFormat);
                if (!re.test(dni)) {
                    if (/^\d+$/.test(this.dniFormat)) {
                        errs.dni = [
                            "El DNI no cumple con el formato. Debe contener al menos " +
                            this.dniFormat +
                            " dígitos.",
                        ];
                    } else {
                        errs.dni = [
                            "El DNI no cumple con el formato. El formato es: " +
                            this.dniFormat +
                            ".",
                        ];
                    }
                    msgs.push(errs.dni[0]);
                }
            }
            this.fieldErrors = errs;
            this.errorBanner = msgs.join(" \u2022 ");
            return msgs.length === 0;
        },

        async cargarCatalogos() {
            try {
                const res = await fetch("/api/catalogos/generos", { credentials: "same-origin" });
                if (res.ok) {
                    const data = await res.json();
                    this.generos = data.data || [];
                }
            } catch (e) {
                console.error("Error al cargar géneros", e);
            }
        },

        async cargarDatos() {
            try {
                const res = await fetch("/api/me", {
                    credentials: "same-origin",
                });
                if (res.ok) {
                    const data = await res.json();
                    this.displayName = data?.persona?.primer_nombre
                        ? data.persona.primer_nombre +
                        " " +
                        (data.persona.primer_apellido || "")
                        : data?.usuario?.nombre || data?.usuario?.usuario || "Mi Perfil";
                    this.email = data?.usuario?.correo_electronico || "";
                    if (data?.persona) {
                        this.form = {
                            primer_nombre: data.persona.primer_nombre || "",
                            segundo_nombre: data.persona.segundo_nombre || "",
                            primer_apellido: data.persona.primer_apellido || "",
                            segundo_apellido:
                                data.persona.segundo_apellido || "",
                            dni: data.persona.dni || "",
                            id_genero_fk: data.persona.id_genero_fk || "",
                        };
                        this.personaAvatar = data.persona.avatar_path
                            ? data.persona.avatar_path.startsWith("http")
                                ? data.persona.avatar_path
                                : window.location.origin +
                                "/storage/" +
                                data.persona.avatar_path
                            : "";
                        this.originalForm = JSON.parse(
                            JSON.stringify(this.form)
                        );
                        this.originalAvatar = this.personaAvatar || "";
                        this.errorBanner = "";
                        this.fieldErrors = {};
                        this.checkForChanges();
                    }
                    if (window.Alpine && Alpine.store) {
                        const store = Alpine.store("perfil");
                        if (store) {
                            store.persona = data?.persona || store.persona;
                            if (typeof store.firstTime !== "undefined")
                                store.firstTime = !!(
                                    data?.primer_ingreso && !data?.persona
                                );
                        }
                    }

                    this.twoFAEnabled = !!data?.two_factor_enabled;
                }
            } catch (_) {
                /* noop */
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
                const res = await fetch("/api/2fa/setup/start", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
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
                const res = await fetch("/api/2fa/setup/confirm", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
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
            } catch (_) {
                this.twoFASetup.error = "Error al confirmar 2FA";
            } finally {
                this.twoFASetup.confirming = false;
            }
        },

        async disable2FA() {
            this.openPasswordModal({
                action: "disable2fa",
                title: "Desactivar 2FA",
                description: "Confirma tu contraseña para desactivar 2FA.",
            });
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
        },

        openConfirmModal({ title, description }) {
            this.confirmTitle = title || "Confirmar acción";
            this.confirmDescription = description || "¿Deseas continuar?";
            this.showConfirmModal = true;
            return new Promise((resolve) => {
                this._confirmResolver = resolve;
            });
        },

        resolveConfirmModal(result) {
            const resolver = this._confirmResolver;
            this._confirmResolver = null;
            this.showConfirmModal = false;
            if (typeof resolver === "function") {
                resolver(!!result);
            }
        },

        openPasswordModal({ action, title, description }) {
            this.pendingAction = action;
            this.modalTitle = title;
            this.modalDescription = description;
            this.modalError = "";
            this.showPasswordModal = true;
        },
        cancelPasswordModal() {
            this.showPasswordModal = false;
            this.pendingAction = null;
            this.modalError = "";
        },
        async submitPasswordModal() {
            if (!this.twoFASetup.currentPassword) {
                this.modalError = "La contraseña es requerida";
                return;
            }
            this.modalError = "";
            this.showPasswordModal = false;
            const action = this.pendingAction;
            this.pendingAction = null;
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
                const res = await fetch("/api/2fa/disable", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
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
            } catch (e) {
                this.modalError = e.message || "Error al desactivar 2FA";
                this.openPasswordModal({
                    action: "disable2fa",
                    title: "Desactivar 2FA",
                    description: "Confirma tu contraseña para desactivar 2FA.",
                });
            }
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

        async guardar() {
            try {
                this.saving = true;
                this.errorBanner = "";
                this.fieldErrors = {};
                if (!this.validarFormulario()) {
                    return;
                }
                const res = await fetch("/api/perfil/persona", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    credentials: "same-origin",
                    body: JSON.stringify(this.form),
                });
                if (!res.ok) {
                    const payload = await res.json().catch(() => ({}));
                    this.fieldErrors = {};
                    this.errorBanner = "";
                    if (res.status === 422 && payload && payload.errors) {
                        this.fieldErrors = payload.errors;
                        const all = Object.values(payload.errors)
                            .map((arr) =>
                                Array.isArray(arr) ? arr[0] : String(arr)
                            )
                            .filter(Boolean);
                        this.errorBanner = all.join(" \u2022 ");
                    } else {
                        this.errorBanner =
                            payload.message || "Error al guardar";
                    }
                    return;
                }
                const data = await res.json();

                if (window.Alpine && Alpine.store) {
                    const store = Alpine.store("perfil");
                    if (store) {
                        store.firstTime = false;
                        store.persona = data?.persona || store.persona;
                    }
                }

                this.success = true;
                this.errorBanner = "";
                this.fieldErrors = {};
                this.originalForm = JSON.parse(JSON.stringify(this.form));
                this.originalAvatar = this.personaAvatar || "";
                this.hasChanges = false;
                setTimeout(() => {
                    this.success = false;
                }, 2000);
            } catch (e) {
                this.errorBanner = e?.message || "Error al guardar";
            } finally {
                this.saving = false;
            }
        },

        async removeAvatar() {
            try {
                if (this.removing) return;
                const confirmed = await this.openConfirmModal({
                    title: "Eliminar foto de perfil",
                    description:
                        "¿Está seguro de que desea eliminar la foto de perfil? Esta acción no se puede deshacer.",
                });
                if (!confirmed) {
                    return;
                }
                this.removing = true;
                const res = await fetch("/api/perfil/avatar", {
                    method: "DELETE",
                    credentials: "same-origin",
                });
                if (res.ok) {
                    this.avatarUrl = "";
                    this.personaAvatar = "";
                    if (window.Alpine && Alpine.store) {
                        const store = Alpine.store("perfil");
                        if (store?.persona) store.persona.avatar_path = null;
                    }
                    this.originalAvatar = "";
                    this.checkForChanges();
                    this.hasChanges = false;
                }
            } finally {
                this.removing = false;
            }
        },

        async onAvatarChange(ev) {
            const file = ev.target.files?.[0];
            if (!file) return;

            const allowedTypes = ["image/jpeg", "image/png", "image/webp"];
            if (!allowedTypes.includes((file.type || "").toLowerCase())) {
                window.showToast?.(
                    "Solo se permiten imagenes JPG, PNG o WEBP.",
                    "warning"
                );
                ev.target.value = "";
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                window.showToast?.(
                    "La imagen no puede superar 2MB.",
                    "warning"
                );
                ev.target.value = "";
                return;
            }

            const confirmed = await this.openConfirmModal({
                title: "Cambiar foto de perfil",
                description:
                    "¿Desea cambiar la foto de perfil actual? La imagen anterior será reemplazada.",
            });
            if (!confirmed) {
                ev.target.value = "";
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                this.avatarUrl = e.target.result;
            };
            reader.readAsDataURL(file);

            const fd = new FormData();
            fd.append("avatar", file);
            const res = await fetch("/api/perfil/avatar", {
                method: "POST",
                credentials: "same-origin",
                body: fd,
            });

            if (res.ok) {
                const data = await res.json();
                const finalUrl = data?.url || this.avatarUrl;
                this.avatarUrl = finalUrl;

                if (window.Alpine && Alpine.store) {
                    const store = Alpine.store("perfil");
                    if (store) {
                        if (!store.persona) store.persona = {};
                        if (data?.path) store.persona.avatar_path = data.path;
                    }
                }

                this.personaAvatar = finalUrl;
                this.originalAvatar = finalUrl;
                this.checkForChanges();
                this.hasChanges = false;
            }
        },

        async actualizarAvatar(nuevaFoto) {
            try {
                const formData = new FormData();
                formData.append("avatar", nuevaFoto);

                const res = await fetch("/api/perfil/avatar", {
                    method: "POST",
                    credentials: "same-origin",
                    body: formData,
                });

                if (!res.ok) {
                    const err = await res
                        .json()
                        .catch(() => ({ message: "Error" }));
                    window.showToast?.(
                        err.message || "Error al actualizar la foto de perfil",
                        "error"
                    );
                    return;
                }

                const data = await res.json();
                if (window.Alpine && Alpine.store) {
                    const store = Alpine.store("perfil");
                    if (store?.persona)
                        store.persona.avatar_path = data.avatar_path;
                }

                this.personaAvatar = data.avatar_path.startsWith("http")
                    ? data.avatar_path
                    : `${window.location.origin}/storage/${data.avatar_path}`;

                window.showToast?.("Foto de perfil actualizada correctamente", "success");
            } catch (e) {
                console.error("Error al actualizar la foto de perfil:", e);
                window.showToast?.("Error al actualizar la foto de perfil", "error");
            }
        },

        checkForChanges() {
            const currentForm = JSON.stringify(this.form);
            const originalForm = JSON.stringify(this.originalForm);
            const hasFormChanges = currentForm !== originalForm;

            let hasAvatarChanges = false;
            if (
                this.avatarUrl &&
                this.avatarUrl !== (this.personaAvatar || "")
            ) {
                hasAvatarChanges = true;
            }
            if (!this.personaAvatar && (this.originalAvatar || "") !== "") {
                hasAvatarChanges = true;
            }
            this.hasChanges = hasFormChanges || hasAvatarChanges;
        },

        async cambiarPassword() {
            try {
                if (
                    this.passwordForm.password !==
                    this.passwordForm.password_confirmation
                ) {
                    this.passwordError = "Las contraseñas no coinciden";
                    window.showToast?.("Las contraseñas no coinciden", "warning");
                    return;
                }
                if ((this.passwordForm.password || "").length < 8) {
                    this.passwordError =
                        "La nueva contraseña debe tener al menos 8 caracteres";
                    window.showToast?.(
                        "La nueva contraseña debe tener al menos 8 caracteres",
                        "warning"
                    );
                    return;
                }
                this.changingPassword = true;
                this.passwordError = "";
                const csrf =
                    document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute("content") || "";
                const res = await fetch("/api-web/me/password", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        ...(csrf ? { "X-CSRF-TOKEN": csrf } : {}),
                    },
                    credentials: "same-origin",
                    body: JSON.stringify({
                        current_password: this.passwordForm.current_password,
                        password: this.passwordForm.password,
                        password_confirmation:
                            this.passwordForm.password_confirmation,
                    }),
                });
                if (!res.ok) {
                    let message = "Error al cambiar la contraseña";
                    try {
                        const err = await res.json();
                        if (err?.message) message = err.message;
                        else if (err?.errors) {
                            const first = Object.values(err.errors)[0];
                            if (Array.isArray(first) && first.length)
                                message = first[0];
                        }
                    } catch (_) { }
                    this.passwordError = message;
                    window.showToast?.(message, "error");
                    return;
                }
                this.passwordForm = {
                    current_password: "",
                    password: "",
                    password_confirmation: "",
                };
                this.passwordSuccess = true;
                this.passwordError = "";
                setTimeout(() => {
                    this.passwordSuccess = false;
                }, 3000);
            } catch (_) {
                const msg = "Error al cambiar la contraseña";
                this.passwordError = msg;
                window.showToast?.(msg, "error");
            } finally {
                this.changingPassword = false;
            }
        },

        onFormChange() {
            this.errorBanner = "";
            this.fieldErrors = {};
            this.checkForChanges();
        },
    };
}

window.perfilPage = perfilPage;
