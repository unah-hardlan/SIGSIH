window.estadosCalendarioApiHandlers = { 
    async fetchEstadosCalendario(component) {
        component.loadingEstadosCalendario = true;
        try {
            const params = new URLSearchParams();
            if (component.filtroEstadoCalendario) {
                params.set("q", component.filtroEstadoCalendario);
            }
            if (component.ordenarPor) {
                params.set("sort", component.ordenarPor);
            }
            params.set("all", "true");

            const response = await fetch(
                `/api/estados-calendario?${params.toString()}`,
                {
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                }
            );

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

        if (
            component.estadosCalendario.some(
                (ec) => ec.nombre.toLowerCase() === nombreTrim.toLowerCase()
            )
        ) {
            window.showToast &&
                window.showToast("El estado de calendario ya existe", "error");
            return;
        }

        if (
            component.estadosCalendario.some(
                (ec) => ec.codigo.toLowerCase() === codigoTrim.toLowerCase()
            )
        ) {
            window.showToast && window.showToast("El código ya existe", "error");
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
                    Accept: "application/json",
                    "Content-Type": "application/json"
                },
                credentials: "same-origin",
                body: JSON.stringify(payload),
            });

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

            await window.estadosCalendarioApiHandlers.fetchEstadosCalendario(component);
        } catch (error) {
            console.error("Error creating estado calendario:", error);
            window.showToast &&
                window.showToast(
                    "Error al crear el estado de calendario",
                    "error"
                );
        }
    },

    async updateEstadoCalendario(component) {
        if (!component.itemToEdit || !component.itemToEdit.id_estado_calendario_pk)
            return;

        const nombreTrim = String(component.itemToEdit.nombre || "").trim();
        const codigoTrim = String(component.itemToEdit.codigo || "").trim();
        const descripcionTrim = String(component.itemToEdit.descripcion || "").trim();

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

        if (
            component.estadosCalendario.some(
                (ec) =>
                    ec.nombre.toLowerCase() === nombreTrim.toLowerCase() &&
                    ec.id_estado_calendario_pk !== component.itemToEdit.id_estado_calendario_pk
            )
        ) {
            window.showToast &&
                window.showToast(
                    "Ya existe otro estado de calendario con ese nombre",
                    "error"
                );
            return;
        }

        if (
            component.estadosCalendario.some(
                (ec) =>
                    ec.codigo.toLowerCase() === codigoTrim.toLowerCase() &&
                    ec.id_estado_calendario_pk !== component.itemToEdit.id_estado_calendario_pk
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
                        Accept: "application/json",
                        "Content-Type": "application/json"
                    },
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

            await window.estadosCalendarioApiHandlers.fetchEstadosCalendario(component);
        } catch (error) {
            console.error("Error updating estado calendario:", error);
        }
    },

    async deleteEstadoCalendario(component) {
        if (!component.itemToDelete || !component.itemToDelete.id_estado_calendario_pk)
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

            await window.estadosCalendarioApiHandlers.fetchEstadosCalendario(component);
        } catch (error) {
            console.error("Error deleting estado calendario:", error);
            const errorMessage =
                error?.message || error?.error || "Error al eliminar el estado de calendario";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },
};
