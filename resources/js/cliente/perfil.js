// resources/js/cliente/perfil.js
// Script extraído desde perfil.blade.php para manejar el modal y la lógica de edición de perfil

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
        empresaAvatarFile: null,
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
            } catch (_) {}
            return {
                nombre_comercial: "",
                razon_social: "",
                rtn: "",
                descripcion_empresa: "",
                horario_atencion: "",
            };
        })(),

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
                alert("Archivo demasiado grande (máx 2MB)");
                return;
            }
            if (!file.type.startsWith("image/")) {
                alert("Solo imágenes");
                return;
            }
            this.empresaAvatarFile = file;
        },

        handleAvatarChange(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert("El archivo es demasiado grande. Máximo 2MB.");
                    return;
                }
                if (!file.type.startsWith("image/")) {
                    alert("Solo se permiten archivos de imagen.");
                    return;
                }
                this.avatarFile = file;
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
                alert("El primer nombre es requerido");
                return;
            }
            if (!this.formData.primer_apellido?.trim()) {
                alert("El primer apellido es requerido");
                return;
            }
            if (!this.formData.dni?.trim()) {
                alert("El DNI es requerido");
                return;
            }
            if (!updateUrl) {
                alert("No se encontró la URL de actualización.");
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
                    alert("Perfil actualizado correctamente");
                    window.location.reload();
                } else {
                    alert(result.message || "Error al actualizar el perfil");
                }
            } catch (e) {
                console.error("Error:", e);
                alert(
                    "Error al actualizar el perfil. Por favor, intenta nuevamente."
                );
            } finally {
                this.loading = false;
            }
        },
        async updateEmpresa() {
            if (!this.empresaForm.nombre_comercial.trim()) {
                alert("Nombre comercial requerido");
                return;
            }
            const empresaUrl = el?.dataset?.empresaUpdateUrl;
            if (!empresaUrl) {
                alert("No se encontró URL de empresa");
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
                    alert("Empresa actualizada correctamente");
                    window.location.reload();
                } else {
                    alert(json.message || "Error al actualizar empresa");
                }
            } catch (err) {
                console.error(err);
                alert("Error al actualizar empresa");
            } finally {
                this.empresaLoading = false;
            }
        },
    };
};
