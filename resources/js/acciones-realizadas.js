window.accionesRealizadasApiHandlers = {
    async fetchAccionesRealizadas(component) {
        component.loadingAccionesRealizadas = true;
        try {
            const params = new URLSearchParams();
            if (component.filtroAccionRealizada) {
                params.set("q", component.filtroAccionRealizada);
            }
            if (component.ordenarPor) {
                params.set("sort", component.ordenarPor);
            }

            const response = await fetch(
                `/api/acciones-realizadas?${params.toString()}`,
                {
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                }
            );

            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;

            component.accionesRealizadas = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching acciones realizadas:", error);
            window.showToast &&
                window.showToast("Error al cargar las acciones", "error");
        } finally {
            component.loadingAccionesRealizadas = false;
        }
    },

    // Crear nueva acción realizada
    async submitAccionRealizada(component) {
        const nombreTrim = String(component.nombre || "").trim();
        const descripcionTrim = String(component.descripcion || "").trim();

        if (!nombreTrim) {
            window.showToast &&
                window.showToast("El nombre de la acción es obligatorio", "error");
            return;
        }

        if (
            component.accionesRealizadas.some(
                (accion) =>
                    accion.nombre.toLowerCase() === nombreTrim.toLowerCase()
            )
        ) {
            window.showToast &&
                window.showToast("Esa acción ya existe", "error");
            return;
        }

        try {
            const payload = {
                nombre_accion: nombreTrim,
                descripcion_accion: descripcionTrim,
            };

            const response = await fetch("/api/acciones-realizadas", {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
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
                window.showToast("Acción creada exitosamente", "success");

            component.nombre = "";
            component.descripcion = "";
            component.isAccionRealizadaModalOpen = false;
            await this.fetchAccionesRealizadas(component);
        } catch (error) {
            console.error("Error creating acción realizada:", error);
            window.showToast &&
                window.showToast("Error al crear la acción", "error");
        }
    },

    // Actualizar acción realizada
    async updateAccionRealizada(component) {
        if (
            !component.itemToEdit ||
            !component.itemToEdit.id_accion_realizada_pk
        )
            return;

        const nombreTrim = String(component.itemToEdit.nombre || "").trim();
        const descripcionTrim = String(component.itemToEdit.descripcion || "").trim();

        if (!nombreTrim) {
            window.showToast &&
                window.showToast("El nombre de la acción es obligatorio", "error");
            return;
        }

        if (
            component.accionesRealizadas.some(
                (accion) =>
                    accion.nombre.toLowerCase() === nombreTrim.toLowerCase() &&
                    accion.id_accion_realizada_pk !==
                        component.itemToEdit.id_accion_realizada_pk
            )
        ) {
            window.showToast &&
                window.showToast("Ya existe otra acción con ese nombre", "error");
            return;
        }

        try {
            const payload = {
                nombre_accion: nombreTrim,
                descripcion_accion: descripcionTrim,
            };

            const response = await fetch(
                `/api/acciones-realizadas/${component.itemToEdit.id_accion_realizada_pk}`,
                {
                    method: "PUT",
                    headers: {
                        Accept: "application/json",
                        "Content-Type": "application/json",
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
                        window.showToast("Error al actualizar la acción", "error");
                }
                throw data;
            }

            window.showToast &&
                window.showToast("Acción actualizada exitosamente", "success");

            component.isAccionRealizadaEditModalOpen = false;
            component.itemToEdit = null;
            await this.fetchAccionesRealizadas(component);
        } catch (error) {
            console.error("Error updating acción realizada:", error);
        }
    },

    // Eliminar acción realizada
    async deleteAccionRealizada(component) {
        if (
            !component.itemToDelete ||
            !component.itemToDelete.id_accion_realizada_pk
        )
            return;

        try {
            const response = await fetch(
                `/api/acciones-realizadas/${component.itemToDelete.id_accion_realizada_pk}`,
                {
                    method: "DELETE",
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                }
            );

            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;

            window.showToast &&
                window.showToast("Acción eliminada exitosamente", "success");

            component.isAccionRealizadaDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchAccionesRealizadas(component);
        } catch (error) {
            console.error("Error deleting acción realizada:", error);
            const errorMessage =
                error?.message || error?.error || "Error al eliminar la acción";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },
};
