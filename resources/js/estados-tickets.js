
window.estadosTicketsApiHandlers = {

    authHeaders() {
        return {
            "Content-Type": "application/json",
            Accept: "application/json",
        };
    },

    async fetchEstadosTickets(component) {
        component.loadingEstadosTickets = true;
        try {
            const params = new URLSearchParams();
            if (component.filtroEstadoTicket) {
                params.set("q", component.filtroEstadoTicket);
            }
            if (component.ordenarPor) {
                params.set("sort", component.ordenarPor);
            }
            params.set("all", "true");

            const response = await fetch(
                `/api/estados-ticket?${params.toString()}`,
                {
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                }
            );

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


    async submitEstadoTicket(component) {
        const nombreTrim = String(component.nombre || "").trim();
        const codigoTrim = String(component.codigo || "").trim();
        const descripcionTrim = String(component.descripcion || "").trim();

        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del estado de ticket es obligatorio",
                    "error"
                );
            return;
        }

        if (!codigoTrim) {
            window.showToast &&
                window.showToast(
                    "El código del estado de ticket es obligatorio",
                    "error"
                );
            return;
        }

        if (
            component.estadosTickets.some(
                (et) => et.nombre.toLowerCase() === nombreTrim.toLowerCase()
            )
        ) {
            window.showToast &&
                window.showToast("El estado de ticket ya existe", "error");
            return;
        }

        if (
            component.estadosTickets.some(
                (et) => et.codigo.toLowerCase() === codigoTrim.toLowerCase()
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

            const response = await fetch("/api/estados-ticket", {
                method: "POST",
                headers: this.authHeaders(),
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
                    "Estado de ticket creado exitosamente",
                    "success"
                );
            component.codigo = "";
            component.nombre = "";
            component.descripcion = "";
            component.es_final = false;
            component.orden = "";
            component.isEstadoTicketModalOpen = false;
            await this.fetchEstadosTickets(component);
        } catch (error) {
            console.error("Error creating estado ticket:", error);
            window.showToast &&
                window.showToast("Error al crear el estado de ticket", "error");
        }
    },


    async updateEstadoTicket(component) {
        if (!component.itemToEdit || !component.itemToEdit.id_estado_ticket_pk)
            return;

        const nombreTrim = String(component.itemToEdit.nombre || "").trim();
        const codigoTrim = String(component.itemToEdit.codigo || "").trim();
        const descripcionTrim = String(
            component.itemToEdit.descripcion || ""
        ).trim();

        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del estado de ticket es obligatorio",
                    "error"
                );
            return;
        }
        if (!codigoTrim) {
            window.showToast &&
                window.showToast(
                    "El código del estado de ticket es obligatorio",
                    "error"
                );
            return;
        }
        if (
            component.estadosTickets.some(
                (et) =>
                    et.nombre.toLowerCase() === nombreTrim.toLowerCase() &&
                    et.id_estado_ticket_pk !==
                    component.itemToEdit.id_estado_ticket_pk
            )
        ) {
            window.showToast &&
                window.showToast(
                    "Ya existe otro estado de ticket con ese nombre",
                    "error"
                );
            return;
        }
        if (
            component.estadosTickets.some(
                (et) =>
                    et.codigo.toLowerCase() === codigoTrim.toLowerCase() &&
                    et.id_estado_ticket_pk !==
                    component.itemToEdit.id_estado_ticket_pk
            )
        ) {
            window.showToast &&
                window.showToast(
                    "Ya existe otro estado de ticket con ese código",
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
                `/api/estados-ticket/${component.itemToEdit.id_estado_ticket_pk}`,
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
                            "Error al actualizar el estado de ticket",
                            "error"
                        );
                }
                throw data;
            }
            window.showToast &&
                window.showToast(
                    "Estado de ticket actualizado exitosamente",
                    "success"
                );
            component.isEstadoTicketEditModalOpen = false;
            component.itemToEdit = null;
            await this.fetchEstadosTickets(component);
        } catch (error) {
            console.error("Error updating estado ticket:", error);
        }
    },


    async deleteEstadoTicket(component) {
        if (
            !component.itemToDelete ||
            !component.itemToDelete.id_estado_ticket_pk
        )
            return;

        try {
            const response = await fetch(
                `/api/estados-ticket/${component.itemToDelete.id_estado_ticket_pk}`,
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
            component.isEstadoTicketDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchEstadosTickets(component);
        } catch (error) {
            console.error("Error deleting estado ticket:", error);
            const errorMessage =
                error?.message ||
                error?.error ||
                "Error al eliminar el estado de ticket";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },
};
