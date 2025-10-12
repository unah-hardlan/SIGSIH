window.estadosCalendarioApiHandlers = {
    /**
     * Fetches the list of estados calendario from the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async fetchEstadosCalendario(component) {
        component.loadingEstadosCalendario = true;
        try {
            const response = await fetch("/api/estados-calendario", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            component.estadosCalendario = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching estados calendario:", error);
            window.showToast &&
                window.showToast(
                    "Error al cargar estados de calendario",
                    "error"
                );
        } finally {
            component.loadingEstadosCalendario = false;
        }
    },

    /**
     * Submits a new estado calendario to the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async submitEstadoCalendario(component) {
        const payload = {
            codigo: String(component.codigo || "").trim(),
            nombre: String(component.nombre_estado_calendario || "").trim(),
            descripcion: String(
                component.descripcion_estado_calendario || ""
            ).trim(),
            es_final: component.es_final || false,
            orden: component.orden || 0,
        };

        if (!payload.nombre) {
            window.showToast &&
                window.showToast(
                    "El nombre del estado de calendario es obligatorio",
                    "error"
                );
            return;
        }

        try {
            const response = await fetch("/api/estados-calendario", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
                credentials: "same-origin",
                body: JSON.stringify(payload),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast(
                    "Estado de calendario creado exitosamente",
                    "success"
                );
            component.codigo = "";
            component.nombre_estado_calendario = "";
            component.descripcion_estado_calendario = "";
            component.es_final = false;
            component.orden = 0;
            component.isEstadoCalendarioModalOpen = false;
            await this.fetchEstadosCalendario(component);
        } catch (error) {
            console.error("Error creating estado calendario:", error);
            window.showToast &&
                window.showToast(
                    "Error al crear el estado de calendario",
                    "error"
                );
        }
    },

    /**
     * Updates an existing estado calendario via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async updateEstadoCalendario(component) {
        if (
            !component.itemToEdit ||
            !component.itemToEdit.id_estado_calendario_pk
        )
            return;

        const payload = {
            codigo: String(component.itemToEdit.codigo || "").trim(),
            nombre: String(
                component.itemToEdit.nombre_estado_calendario || ""
            ).trim(),
            descripcion: String(
                component.itemToEdit.descripcion_estado_calendario || ""
            ).trim(),
            es_final: component.itemToEdit.es_final || false,
            orden: component.itemToEdit.orden || 0,
        };

        if (!payload.nombre) {
            window.showToast &&
                window.showToast(
                    "El nombre del estado de calendario es obligatorio",
                    "error"
                );
            return;
        }

        try {
            const response = await fetch(
                `/api/estados-calendario/${component.itemToEdit.id_estado_calendario_pk}`,
                {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    credentials: "same-origin",
                    body: JSON.stringify(payload),
                }
            );
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast(
                    "Estado de calendario actualizado exitosamente",
                    "success"
                );
            component.isEstadoCalendarioEditModalOpen = false;
            component.itemToEdit = null;
            await this.fetchEstadosCalendario(component);
        } catch (error) {
            console.error("Error updating estado calendario:", error);
        }
    },

    /**
     * Deletes an estado calendario via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async deleteEstadoCalendario(component) {
        if (
            !component.itemToDelete ||
            !component.itemToDelete.id_estado_calendario_pk
        )
            return;

        try {
            const response = await fetch(
                `/api/estados-calendario/${component.itemToDelete.id_estado_calendario_pk}`,
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
                    "Estado de calendario eliminado exitosamente",
                    "success"
                );
            component.isEstadoCalendarioDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchEstadosCalendario(component);
        } catch (error) {
            console.error("Error deleting estado calendario:", error);
            const errorMessage =
                error?.error || "Error al eliminar el estado de calendario";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },
};
