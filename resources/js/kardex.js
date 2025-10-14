window.kardexApiHandlers = {
    async fetchKardex(component) {
        component.loadingKardex = true;
        try {
            const response = await fetch("/api/kardex", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            component.kardex = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching kardex:", error);
            window.showToast &&
                window.showToast("Error al cargar movimientos", "error");
        } finally {
            component.loadingKardex = false;
        }
    },

    async submitKardex(component) {
        if (!component.newMovimiento.id_producto_fk) {
            window.showToast &&
                window.showToast("El producto es obligatorio", "error");
            return;
        }
        if (!component.newMovimiento.id_tipo_movimiento_fk) {
            window.showToast &&
                window.showToast(
                    "El tipo de movimiento es obligatorio",
                    "error"
                );
            return;
        }
        if (!component.newMovimiento.cantidad) {
            window.showToast &&
                window.showToast("La cantidad es obligatoria", "error");
            return;
        }
        if (!component.newMovimiento.fecha_movimiento) {
            window.showToast &&
                window.showToast("La fecha es obligatoria", "error");
            return;
        }
        if (
            !component.newMovimiento.motivo ||
            !component.newMovimiento.motivo.trim()
        ) {
            window.showToast &&
                window.showToast("El motivo es obligatorio", "error");
            return;
        }

        try {
            const payload = {
                id_origen_fk: component.newMovimiento.id_origen_fk || null,
                id_producto_fk: parseInt(
                    component.newMovimiento.id_producto_fk
                ),
                id_tipo_movimiento_fk: parseInt(
                    component.newMovimiento.id_tipo_movimiento_fk
                ),
                cantidad: parseFloat(component.newMovimiento.cantidad),
                fecha_movimiento: component.newMovimiento.fecha_movimiento,
                motivo: component.newMovimiento.motivo.trim(),
            };

            const response = await fetch("/api/kardex", {
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
                window.showToast("Movimiento creado exitosamente", "success");
            component.newMovimiento = {
                id_origen_fk: null,
                id_producto_fk: "",
                id_tipo_movimiento_fk: "",
                cantidad: "",
                fecha_movimiento: "",
                motivo: "",
            };
            component.isKardexModalOpen = false;
            await this.fetchKardex(component);
        } catch (error) {
            console.error("Error creating kardex:", error);
            window.showToast &&
                window.showToast("Error al crear el movimiento", "error");
        }
    },

    async updateKardex(component) {
        if (!component.itemToEdit || !component.itemToEdit.id_kardex_pk) return;

        if (!component.itemToEdit.id_producto_fk) {
            window.showToast &&
                window.showToast("El producto es obligatorio", "error");
            return;
        }
        if (!component.itemToEdit.id_tipo_movimiento_fk) {
            window.showToast &&
                window.showToast(
                    "El tipo de movimiento es obligatorio",
                    "error"
                );
            return;
        }
        if (!component.itemToEdit.cantidad) {
            window.showToast &&
                window.showToast("La cantidad es obligatoria", "error");
            return;
        }
        if (!component.itemToEdit.fecha_movimiento) {
            window.showToast &&
                window.showToast("La fecha es obligatoria", "error");
            return;
        }
        if (
            !component.itemToEdit.motivo ||
            !component.itemToEdit.motivo.trim()
        ) {
            window.showToast &&
                window.showToast("El motivo es obligatorio", "error");
            return;
        }

        try {
            const payload = {
                id_origen_fk: component.itemToEdit.id_origen_fk || null,
                id_producto_fk: parseInt(component.itemToEdit.id_producto_fk),
                id_tipo_movimiento_fk: parseInt(
                    component.itemToEdit.id_tipo_movimiento_fk
                ),
                cantidad: parseFloat(component.itemToEdit.cantidad),
                fecha_movimiento: component.itemToEdit.fecha_movimiento,
                motivo: component.itemToEdit.motivo.trim(),
            };

            const response = await fetch(
                `/api/kardex/${component.itemToEdit.id_kardex_pk}`,
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
                            "Error al actualizar el movimiento",
                            "error"
                        );
                }
                throw data;
            }
            window.showToast &&
                window.showToast(
                    "Movimiento actualizado exitosamente",
                    "success"
                );
            component.isKardexEditModalOpen = false;
            component.itemToEdit = null;
            await this.fetchKardex(component);
        } catch (error) {
            console.error("Error updating kardex:", error);
        }
    },

    async deleteKardex(component) {
        if (!component.itemToDelete || !component.itemToDelete.id_kardex_pk)
            return;

        try {
            const response = await fetch(
                `/api/kardex/${component.itemToDelete.id_kardex_pk}`,
                {
                    method: "DELETE",
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                }
            );

            // El controller retorna 204 sin contenido, así que no parsear JSON
            if (!response.ok) {
                throw new Error("Error al eliminar");
            }

            window.showToast &&
                window.showToast(
                    "Movimiento eliminado exitosamente",
                    "success"
                );
            component.isKardexDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchKardex(component);
        } catch (error) {
            console.error("Error deleting kardex:", error);
            window.showToast &&
                window.showToast("Error al eliminar el movimiento", "error");
        }
    },
};

// Handlers para cargar catálogos
window.catalogosKardexHandlers = {
    async fetchProductos(component) {
        try {
            const response = await fetch("/api/productos", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            component.catalogoProductos = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching productos:", error);
        }
    },

    async fetchTiposMovimiento(component) {
        try {
            const response = await fetch("/api/tipos-movimiento", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            component.catalogoTiposMovimiento = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching tipos movimiento:", error);
        }
    },

    async fetchOrigenes(component) {
        try {
            const response = await fetch("/api/origenes", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            component.catalogoOrigenes = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching origenes:", error);
        }
    },
};
