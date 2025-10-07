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
        loading: false,
        avatarFile: null,
        originalData: originalData,
        formData: { ...originalData },

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
    };
};
