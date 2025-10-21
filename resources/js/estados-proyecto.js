window.estadosProyectoApiHandlers = {
    async fetchEstadosProyecto(component) {
        component.loadingEstadosProyecto = true;
        try {
            const params = new URLSearchParams();
            if (component.filtroEstadoProyecto)
                params.set("q", component.filtroEstadoProyecto);
            if (component.ordenarPor) params.set("sort", component.ordenarPor);
            const response = await fetch(
                `/api/estados-proyecto?${params.toString()}`,
                {
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                }
            );
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;

            component.estadosProyecto = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching estados de proyecto:", error);
            window.showToast &&
                window.showToast(
                    "Error al cargar estados de proyecto",
                    "error"
                );
        } finally {
            component.loadingEstadosProyecto = false;
        }
    },

    async submitEstadoProyecto(component) {
        const nombreTrim = String(component.nombre || "").trim();
        const codigoTrim = String(component.codigo || "").trim();
        const descripcionTrim = String(component.descripcion || "").trim();

        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del estado de proyecto es obligatorio",
                    "error"
                );
            return;
        }

        if (!codigoTrim) {
            window.showToast &&
                window.showToast(
                    "El código del estado de proyecto es obligatorio",
                    "error"
                );
            return;
        }

        if (
            component.estadosProyecto.some(
                (ep) => ep.nombre.toLowerCase() === nombreTrim.toLowerCase()
            )
        ) {
            window.showToast &&
                window.showToast("El estado de proyecto ya existe", "error");
            return;
        }

        if (
            component.estadosProyecto.some(
                (ep) => ep.codigo.toLowerCase() === codigoTrim.toLowerCase()
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

            const response = await fetch("/api/estados-proyecto", {
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
                    "Estado de proyecto creado exitosamente",
                    "success"
                );
            component.codigo = "";
            component.nombre = "";
            component.descripcion = "";
            component.es_final = false;
            component.orden = "";
            component.isEstadoProyectoModalOpen = false;
            await this.fetchEstadosProyecto(component);
        } catch (error) {
            console.error("Error creating estado de proyecto:", error);
            window.showToast &&
                window.showToast(
                    "Error al crear el estado de proyecto",
                    "error"
                );
        }
    },

    async updateEstadoProyecto(component) {
        if (
            !component.itemToEdit ||
            !component.itemToEdit.id_estado_proyecto_pk
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
                    "El nombre del estado de proyecto es obligatorio",
                    "error"
                );
            return;
        }

        if (!codigoTrim) {
            window.showToast &&
                window.showToast(
                    "El código del estado de proyecto es obligatorio",
                    "error"
                );
            return;
        }

        if (
            component.estadosProyecto.some(
                (ep) =>
                    ep.nombre.toLowerCase() === nombreTrim.toLowerCase() &&
                    ep.id_estado_proyecto_pk !==
                        component.itemToEdit.id_estado_proyecto_pk
            )
        ) {
            window.showToast &&
                window.showToast(
                    "Ya existe otro estado de proyecto con ese nombre",
                    "error"
                );
            return;
        }

        if (
            component.estadosProyecto.some(
                (ep) =>
                    ep.codigo.toLowerCase() === codigoTrim.toLowerCase() &&
                    ep.id_estado_proyecto_pk !==
                        component.itemToEdit.id_estado_proyecto_pk
            )
        ) {
            window.showToast &&
                window.showToast(
                    "Ya existe otro estado de proyecto con ese código",
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
                `/api/estados-proyecto/${component.itemToEdit.id_estado_proyecto_pk}`,
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
                            "Error al actualizar el estado de proyecto",
                            "error"
                        );
                }
                throw data;
            }
            window.showToast &&
                window.showToast(
                    "Estado de proyecto actualizado exitosamente",
                    "success"
                );
            component.isEstadoProyectoEditModalOpen = false;
            component.itemToEdit = null;
            await this.fetchEstadosProyecto(component);
        } catch (error) {
            console.error("Error updating estado de proyecto:", error);
        }
    },

    async deleteEstadoProyecto(component) {
        if (
            !component.itemToDelete ||
            !component.itemToDelete.id_estado_proyecto_pk
        )
            return;

        try {
            const response = await fetch(
                `/api/estados-proyecto/${component.itemToDelete.id_estado_proyecto_pk}`,
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
                    "Estado de proyecto eliminado exitosamente",
                    "success"
                );
            component.isEstadoProyectoDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchEstadosProyecto(component);
        } catch (error) {
            console.error("Error deleting estado de proyecto:", error);
            const errorMessage =
                error?.message ||
                error?.error ||
                "Error al eliminar el estado de proyecto";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },
};
