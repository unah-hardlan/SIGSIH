window.tipoObjetosApiHandlers = {
    getToken() { return null; },
    authHeaders() { return { "Content-Type": "application/json", Accept: "application/json" }; },
    async fetchTipoObjetos(component) {
        try {
            component.loadingTipos = true;
            const resp = await fetch("/api/tipos-objeto?all=1", {
                method: "GET",
                headers: this.authHeaders(),
                credentials: "include",
            });
            const data = await resp.json().catch(() => ({}));

            if (!resp.ok) throw data;
            let items = [];
            if (Array.isArray(data)) items = data;
            else if (Array.isArray(data.data)) items = data.data;
            else if (data.data && Array.isArray(data.data)) items = data.data;

            component.tipos = items.map((i) => ({
                id: i.id,
                nombre: i.nombre,
                descripcion: i.descripcion,
                creado_por: i.creado_por,
                fecha_creacion: i.fecha_creacion,
                modificado_por: i.modificado_por,
                fecha_modificacion: i.fecha_modificacion,
            }));
        } catch (err) {
            console.error("Error fetching tipos objeto:", err);
            window.showToast &&
                window.showToast("Error al cargar tipos de objeto", "error");
        } finally {
            component.loadingTipos = false;
        }
    },
    async storeTipoObjeto(component) {
        if (!component.tipoToEdit.nombre?.trim()) {
            window.showToast &&
                window.showToast("El nombre es obligatorio", "error");
            return;
        }

        try {
            const payload = {
                nombre_tipo_objeto: component.tipoToEdit.nombre.trim(),
                descripcion_tipo_objeto:
                    component.tipoToEdit.descripcion?.trim() || "",
            };

            const resp = await fetch("/api/tipos-objeto", {
                method: "POST",
                headers: this.authHeaders(),
                credentials: "include",
                body: JSON.stringify(payload),
            });

            const data = await resp.json().catch(() => ({}));
            if (!resp.ok) {
                if (data && data.errors) {
                    Object.values(data.errors).forEach((errArr) => {
                        if (Array.isArray(errArr)) {
                            errArr.forEach((msg) => {
                                window.showToast &&
                                    window.showToast(msg, "error");
                            });
                        }
                    });
                } else {
                    window.showToast &&
                        window.showToast(
                            "Error al crear el tipo de objeto",
                            "error"
                        );
                }
                throw data;
            }

            window.showToast &&
                window.showToast(
                    "Tipo de objeto creado exitosamente",
                    "success"
                );
            component.isTipoModalOpen = false;
            component.tipoToEdit = { nombre: "", descripcion: "" };
            await this.fetchTipoObjetos(component);
        } catch (err) {
            console.error("Error creating tipo objeto:", err);
        }
    },
    async updateTipoObjeto(component) {
        if (!component.tipoToEdit.id) return;
        if (!component.tipoToEdit.nombre?.trim()) {
            window.showToast &&
                window.showToast("El nombre es obligatorio", "error");
            return;
        }

        try {
            const payload = {
                nombre_tipo_objeto: component.tipoToEdit.nombre.trim(),
                descripcion_tipo_objeto:
                    component.tipoToEdit.descripcion?.trim() || "",
            };

            const resp = await fetch(
                `/api/tipos-objeto/${component.tipoToEdit.id}`,
                {
                    method: "PUT",
                    headers: this.authHeaders(),
                    credentials: "include",
                    body: JSON.stringify(payload),
                }
            );

            const data = await resp.json().catch(() => ({}));
            if (!resp.ok) {
                if (data && data.errors) {
                    Object.values(data.errors).forEach((errArr) => {
                        if (Array.isArray(errArr)) {
                            errArr.forEach((msg) => {
                                window.showToast &&
                                    window.showToast(msg, "error");
                            });
                        }
                    });
                } else {
                    window.showToast &&
                        window.showToast(
                            "Error al actualizar el tipo de objeto",
                            "error"
                        );
                }
                throw data;
            }

            window.showToast &&
                window.showToast(
                    "Tipo de objeto actualizado exitosamente",
                    "success"
                );
            component.isTipoEditModalOpen = false;
            component.tipoToEdit = { nombre: "", descripcion: "" };
            await this.fetchTipoObjetos(component);
        } catch (err) {
            console.error("Error updating tipo objeto:", err);
        }
    },
    async deleteTipoObjeto(component) {
        if (!component.tipoToDelete.id) return;

        try {
            const resp = await fetch(
                `/api/tipos-objeto/${component.tipoToDelete.id}`,
                {
                    method: "DELETE",
                    headers: this.authHeaders(),
                    credentials: "include",
                }
            );

            const data = await resp.json().catch(() => ({}));
            if (!resp.ok) {
                window.showToast &&
                    window.showToast(
                        data.error || "Error al eliminar el tipo de objeto",
                        "error"
                    );
                throw data;
            }

            window.showToast &&
                window.showToast(
                    "Tipo de objeto eliminado exitosamente",
                    "success"
                );
            component.isTipoDeleteModalOpen = false;
            component.tipoToDelete = { nombre: "", descripcion: "" };
            await this.fetchTipoObjetos(component);
        } catch (err) {
            console.error("Error deleting tipo objeto:", err);
        }
    },
};
