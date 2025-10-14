window.estadosSolicitudApiHandlers = {
    authHeaders() {
        return {
            "Content-Type": "application/json",
            Accept: "application/json",
        };
    },

    /**
     * Fetches the list of estados de solicitud from the API.
     */
    async fetchEstadosSolicitud(component) {
        component.loadingEstadosSolicitud = true;
        try {
            const response = await fetch("/api/estados-solicitud", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            component.estadosSolicitud = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching estados de solicitud:", error);
            window.showToast?.("Error al cargar los estados", "error");
        } finally {
            component.loadingEstadosSolicitud = false;
        }
    },

    /**
     * Submits a new estado de solicitud to the API.
     */
    async submitEstadoSolicitud(component) {
        const nombreTrim = String(component.nombre || "").trim();
        const codigoTrim = String(component.codigo || "").trim();

        if (!nombreTrim || !codigoTrim) {
            window.showToast?.(
                "El nombre y el código son obligatorios",
                "error"
            );
            return;
        }

        // Validar duplicados
        if (
            component.estadosSolicitud.some(
                (es) => es.nombre.toLowerCase() === nombreTrim.toLowerCase()
            )
        ) {
            window.showToast?.("El nombre del estado ya existe", "error");
            return;
        }
        if (
            component.estadosSolicitud.some(
                (es) => es.codigo.toLowerCase() === codigoTrim.toLowerCase()
            )
        ) {
            window.showToast?.("El código del estado ya existe", "error");
            return;
        }

        try {
            const payload = {
                codigo: codigoTrim,
                nombre: nombreTrim,
                descripcion: String(component.descripcion || "").trim(),
                es_final: component.es_final || false,
                orden: parseInt(component.orden) || 0,
            };

            const response = await fetch("/api/estados-solicitud", {
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

            window.showToast?.("Estado creado exitosamente", "success");
            component.codigo = "";
            component.nombre = "";
            component.descripcion = "";
            component.es_final = false;
            component.orden = "";
            component.isEstadoSolicitudModalOpen = false;
            await this.fetchEstadosSolicitud(component);
        } catch (error) {
            console.error("Error creating estado de solicitud:", error);
            window.showToast?.("Error al crear el estado", "error");
        }
    },

    /**
     * Updates an existing estado de solicitud via the API.
     */
    async updateEstadoSolicitud(component) {
        if (!component.itemToEdit?.id_estado_solicitud_pk) return;

        const nombreTrim = String(component.itemToEdit.nombre || "").trim();
        const codigoTrim = String(component.itemToEdit.codigo || "").trim();

        if (!nombreTrim || !codigoTrim) {
            window.showToast?.(
                "El nombre y el código son obligatorios",
                "error"
            );
            return;
        }

        // Validar duplicados, excluyendo el item actual
        if (
            component.estadosSolicitud.some(
                (es) =>
                    es.nombre.toLowerCase() === nombreTrim.toLowerCase() &&
                    es.id_estado_solicitud_pk !==
                        component.itemToEdit.id_estado_solicitud_pk
            )
        ) {
            window.showToast?.("Ya existe otro estado con ese nombre", "error");
            return;
        }
        if (
            component.estadosSolicitud.some(
                (es) =>
                    es.codigo.toLowerCase() === codigoTrim.toLowerCase() &&
                    es.id_estado_solicitud_pk !==
                        component.itemToEdit.id_estado_solicitud_pk
            )
        ) {
            window.showToast?.("Ya existe otro estado con ese código", "error");
            return;
        }

        try {
            const payload = {
                codigo: codigoTrim,
                nombre: nombreTrim,
                descripcion: String(
                    component.itemToEdit.descripcion || ""
                ).trim(),
                es_final: component.itemToEdit.es_final || false,
                orden: parseInt(component.itemToEdit.orden) || 0,
            };

            const response = await fetch(
                `/api/estados-solicitud/${component.itemToEdit.id_estado_solicitud_pk}`,
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

            if (!response.ok) throw await response.json().catch(() => ({}));

            window.showToast?.("Estado actualizado exitosamente", "success");
            component.isEstadoSolicitudEditModalOpen = false;
            component.itemToEdit = null;
            await this.fetchEstadosSolicitud(component);
        } catch (error) {
            console.error("Error updating estado de solicitud:", error);
            window.showToast?.("Error al actualizar el estado", "error");
        }
    },

    /**
     * Deletes an estado de solicitud via the API.
     */
    async deleteEstadoSolicitud(component) {
        if (!component.itemToDelete?.id_estado_solicitud_pk) return;

        try {
            const response = await fetch(
                `/api/estados-solicitud/${component.itemToDelete.id_estado_solicitud_pk}`,
                {
                    method: "DELETE",
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                }
            );

            if (!response.ok) throw await response.json().catch(() => ({}));

            window.showToast?.("Estado eliminado exitosamente", "success");
            component.isEstadoSolicitudDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchEstadosSolicitud(component);
        } catch (error) {
            console.error("Error deleting estado de solicitud:", error);
            const errorMessage =
                error?.message || "Error al eliminar el estado";
            window.showToast?.(errorMessage, "error");
        }
    },
};
