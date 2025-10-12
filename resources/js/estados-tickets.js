window.estadosTicketsApiHandlers = {
    /**
     * Fetches the list of estados tickets from the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async fetchEstadosTickets(component) {
        component.loadingEstadosTickets = true;
        try {
            const response = await fetch("/api/estados-ticket", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            component.estadosTickets = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching estados tickets:", error);
            window.showToast &&
                window.showToast("Error al cargar estados de tickets", "error");
        } finally {
            component.loadingEstadosTickets = false;
        }
    },

    /**
     * Submits a new estado ticket to the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async submitEstadoTicket(component) {
        const payload = {
            id: String(component.id_estado || "").trim(),
            nombre: String(component.nombre || "").trim(),
            descripcion: String(component.descripcion_estado || "").trim(),
            es_final: Boolean(component.es_final),
            orden: Number(component.orden || 0),
            codigo: String(component.codigo || "").trim(),
        };

        if (!payload.nombre) {
            window.showToast &&
                window.showToast(
                    "El nombre del estado de ticket es obligatorio",
                    "error"
                );
            return;
        }

        try {
            const response = await fetch("/api/estados-ticket", {
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
                    "Estado de ticket creado exitosamente",
                    "success"
                );
            component.id_estado = "";
            component.nombre_estado = "";
            component.descripcion_estado = "";
            component.isModalOpenEstadoTicket = false;
            await this.fetchEstadosTickets(component);
        } catch (error) {
            console.error("Error creating estado ticket:", error);
            window.showToast &&
                window.showToast("Error al crear el estado de ticket", "error");
        }
    },

    /**
     * Updates an existing estado ticket via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async updateEstadoTicket(component) {
        if (!component.itemToEdit || !component.itemToEdit.id) return;

        const payload = {
            id: String(component.itemToEdit.id || "").trim(),
            nombre: String(component.itemToEdit.nombre || "").trim(),
            descripcion: String(component.itemToEdit.descripcion || "").trim(),
        };

        if (!payload.nombre) {
            window.showToast &&
                window.showToast(
                    "El nombre del estado de ticket es obligatorio",
                    "error"
                );
            return;
        }

        try {
            const response = await fetch(
                `/api/estados-ticket/${component.itemToEdit.id}`,
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
                    "Estado de ticket actualizado exitosamente",
                    "success"
                );
            component.isEditModalOpenEstadoTicket = false;
            component.itemToEdit = null;
            await this.fetchEstadosTickets(component);
        } catch (error) {
            console.error("Error updating estado ticket:", error);
            console.error("Validation errors:", error.errors);
        }
    },

    /**
     * Deletes an estado ticket via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async deleteEstadoTicket(component) {
        if (!component.itemToDelete || !component.itemToDelete.id) return;

        try {
            const response = await fetch(
                `/api/estados-ticket/${component.itemToDelete.id}`,
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
                    "Estado de ticket eliminado exitosamente",
                    "success"
                );
            component.isDeleteModalOpenEstadoTicket = false;
            component.itemToDelete = null;
            await this.fetchEstadosTickets(component);
        } catch (error) {
            console.error("Error deleting estado ticket:", error);
            const errorMessage =
                error?.error || "Error al eliminar el estado de ticket";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },
};
