window.tipoMovimientosApiHandlers = {
    authHeaders() {
        return {
            "Content-Type": "application/json",
            Accept: "application/json",
        };
    },

    async fetchTipoMovimientos(component) {
        component.loadingTipoMovimientos = true;
        try {
            const params = new URLSearchParams();
            if (component.filtroTipoMovimiento) {
                params.set("q", component.filtroTipoMovimiento);
            }
            if (component.ordenarPor) {
                params.set("sort", component.ordenarPor);
            }
            // Para asegurar que obtenemos todos los resultados para el frontend
            params.set("all", "true");

            const response = await fetch(
                `/api/tipos-movimiento?${params.toString()}`,
                {
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                }
            );
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;

            component.tipoMovimientos = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching tipos movimiento:", error);
            window.showToast &&
                window.showToast(
                    "Error al cargar tipos de movimiento",
                    "error"
                );
        } finally {
            component.loadingTipoMovimientos = false;
        }
    },

    /**
     * Submits a new tipo movimiento to the API.
     */
    async submitTipoMovimiento(component) {
        const nombreTrim = String(
            component.nombre_tipo_movimiento || ""
        ).trim();
        const descripcionTrim = String(
            component.descripcion_tipo_movimiento || ""
        ).trim();
        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del tipo de movimiento es obligatorio",
                    "error"
                );
            return;
        }
        if (
            component.tipoMovimientos.some(
                (tm) =>
                    tm.nombre_tipo_movimiento.toLowerCase() ===
                    nombreTrim.toLowerCase()
            )
        ) {
            window.showToast &&
                window.showToast("El tipo de movimiento ya existe", "error");
            return;
        }
        try {
            const payload = {
                nombre_tipo_movimiento: nombreTrim,
                descripcion_tipo_movimiento: descripcionTrim,
            };
            const response = await fetch("/api/tipos-movimiento", {
                method: "POST",
                headers: this.authHeaders(),
                credentials: "same-origin",
                body: JSON.stringify(payload),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast(
                    "Tipo de movimiento creado exitosamente",
                    "success"
                );
            component.nombre_tipo_movimiento = "";
            component.descripcion_tipo_movimiento = "";
            component.isTipoMovimientoModalOpen = false;
            await this.fetchTipoMovimientos(component);
        } catch (error) {
            console.error("Error creating tipo movimiento:", error);
            window.showToast &&
                window.showToast(
                    "Error al crear el tipo de movimiento",
                    "error"
                );
        }
    },

    /**
     * Updates an existing tipo movimiento via the API.
     */
    async updateTipoMovimiento(component) {
        if (
            !component.itemToEdit ||
            !component.itemToEdit.id_tipo_movimiento_pk
        )
            return;

        const nombreTrim = String(
            component.itemToEdit.nombre_tipo_movimiento || ""
        ).trim();
        const descripcionTrim = String(
            component.itemToEdit.descripcion_tipo_movimiento || ""
        ).trim();
        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del tipo de movimiento es obligatorio",
                    "error"
                );
            return;
        }
        if (
            component.tipoMovimientos.some(
                (tm) =>
                    tm.nombre_tipo_movimiento.toLowerCase() ===
                        nombreTrim.toLowerCase() &&
                    tm.id_tipo_movimiento_pk !==
                        component.itemToEdit.id_tipo_movimiento_pk
            )
        ) {
            window.showToast &&
                window.showToast(
                    "Ya existe otro tipo de movimiento con ese nombre",
                    "error"
                );
            return;
        }
        try {
            const payload = {
                nombre_tipo_movimiento: nombreTrim,
                descripcion_tipo_movimiento: descripcionTrim,
            };
            const response = await fetch(
                `/api/tipos-movimiento/${component.itemToEdit.id_tipo_movimiento_pk}`,
                {
                    method: "PUT",
                    headers: this.authHeaders(),
                    credentials: "same-origin",
                    body: JSON.stringify(payload),
                }
            );
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
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
                            "Error al actualizar el tipo de movimiento",
                            "error"
                        );
                }
                throw data;
            }
            window.showToast &&
                window.showToast(
                    "Tipo de movimiento actualizado exitosamente",
                    "success"
                );
            component.isTipoMovimientoEditModalOpen = false;
            component.itemToEdit = null;
            await this.fetchTipoMovimientos(component);
        } catch (error) {
            console.error("Error updating tipo movimiento:", error);
        }
    },

    /**
     * Deletes a tipo movimiento via the API.
     */
    async deleteTipoMovimiento(component) {
        if (
            !component.itemToDelete ||
            !component.itemToDelete.id_tipo_movimiento_pk
        )
            return;
        try {
            const response = await fetch(
                `/api/tipos-movimiento/${component.itemToDelete.id_tipo_movimiento_pk}`,
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
                    "Tipo de movimiento eliminado exitosamente",
                    "success"
                );
            component.isTipoMovimientoDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchTipoMovimientos(component);
        } catch (error) {
            console.error("Error deleting tipo movimiento:", error);
            window.showToast &&
                window.showToast(
                    "Error al eliminar el tipo de movimiento",
                    "error"
                );
        }
    },
};

