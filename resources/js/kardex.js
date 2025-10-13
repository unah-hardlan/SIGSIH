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
                window.showToast("Error al cargar el kardex", "error");
        } finally {
            component.loadingKardex = false;
        }
    },

    async submitKardex(component) {
        const payload = {
            id_origen_fk: component.newMovimiento.id_origen_fk || null,
            id_producto_fk: component.newMovimiento.id_producto_fk,
            id_tipo_movimiento_fk:
                component.newMovimiento.id_tipo_movimiento_fk,
            cantidad: parseFloat(component.newMovimiento.cantidad) || 0,
            fecha_movimiento: component.newMovimiento.fecha_movimiento,
            motivo: String(component.newMovimiento.motivo || "").trim(),
        };

        if (!payload.id_producto_fk) {
            window.showToast("Debe seleccionar un producto", "error");
            return;
        }
        if (!payload.id_tipo_movimiento_fk) {
            window.showToast("Debe seleccionar un tipo de movimiento", "error");
            return;
        }
        if (payload.cantidad <= 0) {
            window.showToast("La cantidad debe ser mayor a 0", "error");
            return;
        }
        if (!payload.fecha_movimiento) {
            window.showToast("La fecha es obligatoria", "error");
            return;
        }
        if (!payload.motivo) {
            window.showToast("El motivo es obligatorio", "error");
            return;
        }

        try {
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
            window.showToast("Error al crear el movimiento", "error");
        }
    },

    async updateKardex(component) {
        if (!component.itemToEdit || !component.itemToEdit.id_kardex_pk) return;

        const payload = {
            id_origen_fk: component.itemToEdit.id_origen_fk || null,
            id_producto_fk: component.itemToEdit.id_producto_fk,
            id_tipo_movimiento_fk: component.itemToEdit.id_tipo_movimiento_fk,
            cantidad: parseFloat(component.itemToEdit.cantidad) || 0,
            fecha_movimiento: component.itemToEdit.fecha_movimiento,
            motivo: String(component.itemToEdit.motivo || "").trim(),
        };

        if (!payload.id_producto_fk) {
            window.showToast("Debe seleccionar un producto", "error");
            return;
        }
        if (!payload.id_tipo_movimiento_fk) {
            window.showToast("Debe seleccionar un tipo de movimiento", "error");
            return;
        }
        if (payload.cantidad <= 0) {
            window.showToast("La cantidad debe ser mayor a 0", "error");
            return;
        }
        if (!payload.fecha_movimiento) {
            window.showToast("La fecha es obligatoria", "error");
            return;
        }
        if (!payload.motivo) {
            window.showToast("El motivo es obligatorio", "error");
            return;
        }

        try {
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
                        if (Array.isArray(errArr))
                            errArr.forEach(
                                (msg) =>
                                    window.showToast &&
                                    window.showToast(msg, "error")
                            );
                    });
                } else {
                    window.showToast(
                        "Error al actualizar el movimiento",
                        "error"
                    );
                }
                throw data;
            }

            window.showToast("Movimiento actualizado exitosamente", "success");
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
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;

            window.showToast("Movimiento eliminado exitosamente", "success");
            component.isKardexDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchKardex(component);
        } catch (error) {
            console.error("Error deleting kardex:", error);
            const errorMessage =
                error?.message ||
                error?.error ||
                "Error al eliminar el movimiento";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },
};
