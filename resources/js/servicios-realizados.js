window.serviciosRealizadosApiHandlers = {
    authHeaders() {
        return {
            "Content-Type": "application/json",
            Accept: "application/json",
        };
    },

    /**
     * Fetches the list of servicios realizados from the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async fetchServiciosRealizados(component) {
        component.loadingServiciosRealizados = true;
        try {
            const response = await fetch("/api/servicios-realizados", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            // Assuming the API returns data in 'data' key or directly an array
            component.serviciosRealizados = Array.isArray(data?.data)
                ? data.data.map((item) => ({
                      id_servicio_realizado_pk: item.id_servicio_realizado_pk,
                      nombre_servicio: item.nombre_servicio,
                      descripcion_servicio: item.descripcion_servicio,
                  }))
                : Array.isArray(data)
                ? data.map((item) => ({
                      id_servicio_realizado_pk: item.id_servicio_realizado_pk,
                      nombre_servicio: item.nombre_servicio,
                      descripcion_servicio: item.descripcion_servicio,
                  }))
                : [];
        } catch (error) {
            console.error("Error fetching servicios realizados:", error);
            window.showToast &&
                window.showToast(
                    "Error al cargar servicios realizados",
                    "error"
                );
        } finally {
            component.loadingServiciosRealizados = false;
        }
    },

    /**
     * Submits a new servicio realizado to the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async submitServicioRealizado(component) {
        const nombreTrim = String(component.nombre_servicio || "").trim();
        const descripcionTrim = String(
            component.descripcion_servicio || ""
        ).trim();
        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del servicio es obligatorio",
                    "error"
                );
            return;
        }
        try {
            const payload = {
                nombre_servicio: nombreTrim,
                descripcion_servicio: descripcionTrim,
            };
            const response = await fetch("/api/servicios-realizados", {
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
                    "Servicio realizado creado exitosamente",
                    "success"
                );
            component.nombre_servicio = "";
            component.descripcion_servicio = "";
            component.isServicioRealizadoModalOpen = false;
            await this.fetchServiciosRealizados(component);
        } catch (error) {
            console.error("Error creating servicio realizado:", error);
            window.showToast &&
                window.showToast(
                    "Error al crear el servicio realizado",
                    "error"
                );
        }
    },

    /**
     * Updates an existing servicio realizado via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async updateServicioRealizado(component) {
        if (
            !component.itemToEdit ||
            !component.itemToEdit.id_servicio_realizado_pk
        )
            return;
        const nombreTrim = String(
            component.itemToEdit.nombre_servicio || ""
        ).trim();
        const descripcionTrim = String(
            component.itemToEdit.descripcion_servicio || ""
        ).trim();
        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del servicio es obligatorio",
                    "error"
                );
            return;
        }
        try {
            const payload = {
                nombre_servicio: nombreTrim,
                descripcion_servicio: descripcionTrim,
            };
            const response = await fetch(
                `/api/servicios-realizados/${component.itemToEdit.id_servicio_realizado_pk}`,
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
            if (!response.ok) {
                // Mostrar errores de validación si existen
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
                            "Error al actualizar el servicio realizado",
                            "error"
                        );
                }
                throw data;
            }
            window.showToast &&
                window.showToast(
                    "Servicio realizado actualizado exitosamente",
                    "success"
                );
            component.isServicioRealizadoEditModalOpen = false;
            component.itemToEdit = null;
            await this.fetchServiciosRealizados(component);
        } catch (error) {
            console.error("Error updating servicio realizado:", error);
        }
    },

    /**
     * Deletes a servicio realizado via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async deleteServicioRealizado(component) {
        if (
            !component.itemToDelete ||
            !component.itemToDelete.id_servicio_realizado_pk
        )
            return;
        try {
            const response = await fetch(
                `/api/servicios-realizados/${component.itemToDelete.id_servicio_realizado_pk}`,
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
                    "Servicio realizado eliminado exitosamente",
                    "success"
                );
            component.isServicioRealizadoDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchServiciosRealizados(component);
        } catch (error) {
            console.error("Error deleting servicio realizado:", error);
            const errorMessage =
                error?.error || "Error al eliminar el servicio realizado";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },
};
