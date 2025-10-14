window.estadosCalendarioApiHandlers = {
    authHeaders() {
        return {
            "Content-Type": "application/json",
            Accept: "application/json",
        };
    },

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
            // El controller retorna data dentro de 'data' key
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
        const nombreTrim = String(component.nombre || "").trim();
        const codigoTrim = String(component.codigo || "").trim();
        const descripcionTrim = String(component.descripcion || "").trim();

        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del estado de calendario es obligatorio",
                    "error"
                );
            return;
        }

        if (!codigoTrim) {
            window.showToast &&
                window.showToast(
                    "El código del estado de calendario es obligatorio",
                    "error"
                );
            return;
        }

        // Validar duplicados por nombre
        if (
            component.estadosCalendario.some(
                (ec) => ec.nombre.toLowerCase() === nombreTrim.toLowerCase()
            )
        ) {
            window.showToast &&
                window.showToast("El estado de calendario ya existe", "error");
            return;
        }

        // Validar duplicados por código
        if (
            component.estadosCalendario.some(
                (ec) => ec.codigo.toLowerCase() === codigoTrim.toLowerCase()
            )
        ) {
            window.showToast &&
                window.showToast("El código ya existe", "error");
            return;
        }

        try {
            const payload = {
                codigo: codigoTrim,
                nombre: nombreTrim,
                descripcion: descripcionTrim,
                es_final: component.es_final || false,
                orden: parseInt(component.orden) || 0,
            };

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
                    return;
                }
                throw data;
            }
            window.showToast &&
                window.showToast(
                    "Estado de calendario creado exitosamente",
                    "success"
                );
            component.codigo = "";
            component.nombre = "";
            component.descripcion = "";
            component.es_final = false;
            component.orden = "";
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

        const nombreTrim = String(component.itemToEdit.nombre || "").trim();
        const codigoTrim = String(component.itemToEdit.codigo || "").trim();
        const descripcionTrim = String(
            component.itemToEdit.descripcion || ""
        ).trim();

        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del estado de calendario es obligatorio",
                    "error"
                );
            return;
        }

        if (!codigoTrim) {
            window.showToast &&
                window.showToast(
                    "El código del estado de calendario es obligatorio",
                    "error"
                );
            return;
        }

        // Validar duplicados por nombre
        if (
            component.estadosCalendario.some(
                (ec) =>
                    ec.nombre.toLowerCase() === nombreTrim.toLowerCase() &&
                    ec.id_estado_calendario_pk !==
                        component.itemToEdit.id_estado_calendario_pk
            )
        ) {
            window.showToast &&
                window.showToast(
                    "Ya existe otro estado de calendario con ese nombre",
                    "error"
                );
            return;
        }

        // Validar duplicados por código
        if (
            component.estadosCalendario.some(
                (ec) =>
                    ec.codigo.toLowerCase() === codigoTrim.toLowerCase() &&
                    ec.id_estado_calendario_pk !==
                        component.itemToEdit.id_estado_calendario_pk
            )
        ) {
            window.showToast &&
                window.showToast(
                    "Ya existe otro estado de calendario con ese código",
                    "error"
                );
            return;
        }

        try {
            const payload = {
                codigo: codigoTrim,
                nombre: nombreTrim,
                descripcion: descripcionTrim,
                es_final: component.itemToEdit.es_final || false,
                orden: parseInt(component.itemToEdit.orden) || 0,
            };

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
                            "Error al actualizar el estado de calendario",
                            "error"
                        );
                }
                throw data;
            }
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
                error?.message ||
                error?.error ||
                "Error al eliminar el estado de calendario";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },
};
