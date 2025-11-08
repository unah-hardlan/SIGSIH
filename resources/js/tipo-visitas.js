window.tipoVisitasApiHandlers = {
    /**
    
     * @param {object} component 
     */
    async fetchTipoVisitas(component) {
        component.loadingTipoVisitas = true;
        try {
            const params = new URLSearchParams();
            if (component.filtroTipoVisita) {
                params.set("q", component.filtroTipoVisita);
            }
            if (component.ordenarPor) {
                params.set("sort", component.ordenarPor);
            }
            params.set("all", "true");

            const response = await fetch(
                `/api/tipos-visita?${params.toString()}`,
                {
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                }
            );
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;

            component.tipoVisitas = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching tipos visita:", error);
            window.showToast &&
                window.showToast("Error al cargar tipos de visita", "error");
        } finally {
            component.loadingTipoVisitas = false;
        }
    },

    /**
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async submitTipoVisita(component) {
        const nombreTrim = String(component.nombre_tipo_visita || "").trim();
        const descripcionTrim = String(
            component.descripcion_tipo_visita || ""
        ).trim();
        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del tipo de visita es obligatorio",
                    "error"
                );
            return;
        }
        if (
            component.tipoVisitas.some(
                (tv) =>
                    tv.nombre_tipo_visita.toLowerCase() ===
                    nombreTrim.toLowerCase()
            )
        ) {
            window.showToast &&
                window.showToast("El tipo de visita ya existe", "error");
            return;
        }
        try {
            const payload = {
                nombre_tipo_visita: nombreTrim,
                descripcion_tipo_visita: descripcionTrim,
            };
            const response = await fetch("/api/tipos-visita", {
                method: "POST",
                headers: this.authHeaders(),
                credentials: "same-origin",
                body: JSON.stringify(payload),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast(
                    "Tipo de visita creado exitosamente",
                    "success"
                );
            component.nombre_tipo_visita = "";
            component.descripcion_tipo_visita = "";
            component.isTipoVisitaModalOpen = false;
            await this.fetchTipoVisitas(component);
        } catch (error) {
            console.error("Error creating tipo visita:", error);
            window.showToast &&
                window.showToast("Error al crear el tipo de visita", "error");
        }
    },

    /**
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async updateTipoVisita(component) {
        if (!component.itemToEdit || !component.itemToEdit.id_tipo_visita_pk)
            return;

        const nombreTrim = String(
            component.edit_nombre_tipo_visita || ""
        ).trim();
        const descripcionTrim = String(
            component.edit_descripcion_tipo_visita || ""
        ).trim();

        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del tipo de visita es obligatorio",
                    "error"
                );
            return;
        }
        if (
            component.tipoVisitas.some(
                (tv) =>
                    tv.nombre_tipo_visita.toLowerCase() ===
                        nombreTrim.toLowerCase() &&
                    tv.id_tipo_visita_pk !==
                        component.itemToEdit.id_tipo_visita_pk
            )
        ) {
            window.showToast &&
                window.showToast(
                    "Ya existe otro tipo de visita con ese nombre",
                    "error"
                );
            return;
        }
        try {
            const payload = {
                nombre_tipo_visita: nombreTrim,
                descripcion_tipo_visita: descripcionTrim,
            };
            const response = await fetch(
                `/api/tipos-visita/${component.itemToEdit.id_tipo_visita_pk}`,
                {
                    method: "PUT",
                    headers: this.authHeaders(),
                    credentials: "same-origin",
                    body: JSON.stringify(payload),
                }
            );
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast(
                    "Tipo de visita actualizado exitosamente",
                    "success"
                );
            component.isTipoVisitaEditModalOpen = false;
            component.itemToEdit = null;
            await this.fetchTipoVisitas(component);
        } catch (error) {
            console.error("Error updating tipo visita:", error);
            window.showToast &&
                window.showToast(
                    "Error al actualizar el tipo de visita",
                    "error"
                );
        }
    },

    /**
     * Deletes a tipo visita via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async deleteTipoVisita(component) {
        if (
            !component.itemToDelete ||
            !component.itemToDelete.id_tipo_visita_pk
        )
            return;
        try {
            const response = await fetch(
                `/api/tipos-visita/${component.itemToDelete.id_tipo_visita_pk}`,
                {
                    method: "DELETE",
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                }
            );
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast(
                    "Tipo de visita eliminado exitosamente",
                    "success"
                );
            component.isTipoVisitaDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchTipoVisitas(component);
        } catch (error) {
            console.error("Error deleting tipo visita:", error);
            window.showToast &&
                window.showToast(
                    "Error al eliminar el tipo de visita",
                    "error"
                );
        }
    },
};
