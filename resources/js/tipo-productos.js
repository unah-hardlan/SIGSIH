window.tipoProductosApiHandlers = {
    authHeaders() {
        return {
            "Content-Type": "application/json",
            Accept: "application/json",
        };
    },

    /**
     * MODIFICADO: Ahora construye una URL dinámica con los parámetros de filtro (q) y ordenamiento (sort).
     */
    async fetchTipoProductos(component) {
        component.loadingTipoProductos = true;
        try {
            const params = new URLSearchParams();
            if (component.filtroTipoProducto) {
                params.set("q", component.filtroTipoProducto);
            }
            if (component.ordenarPor) {
                params.set("sort", component.ordenarPor);
            }
            // Para asegurar que obtenemos todos los resultados para el frontend
            params.set("all", "true");

            const response = await fetch(
                `/api/tipos-producto?${params.toString()}`,
                {
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                }
            );
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;

            component.tipoProductos = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching tipos producto:", error);
            window.showToast &&
                window.showToast("Error al cargar tipos de producto", "error");
        } finally {
            component.loadingTipoProductos = false;
        }
    },

    /**
     * Submits a new tipo producto to the API.
     */
    async submitTipoProducto(component) {
        const nombreTrim = String(component.nombre_tipo_producto || "").trim();
        const descripcionTrim = String(
            component.descripcion_tipo_producto || ""
        ).trim();
        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del tipo de producto es obligatorio",
                    "error"
                );
            return;
        }
        if (
            component.tipoProductos.some(
                (tp) =>
                    tp.nombre_tipo_producto.toLowerCase() ===
                    nombreTrim.toLowerCase()
            )
        ) {
            window.showToast &&
                window.showToast("El tipo de producto ya existe", "error");
            return;
        }
        try {
            const payload = {
                nombre_tipo_producto: nombreTrim,
                descripcion_tipo_producto: descripcionTrim,
            };
            const response = await fetch("/api/tipos-producto", {
                method: "POST",
                headers: this.authHeaders(),
                credentials: "same-origin",
                body: JSON.stringify(payload),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast(
                    "Tipo de producto creado exitosamente",
                    "success"
                );
            component.nombre_tipo_producto = "";
            component.descripcion_tipo_producto = "";
            component.isTipoProductoModalOpen = false;
            await this.fetchTipoProductos(component);
        } catch (error) {
            console.error("Error creating tipo producto:", error);
            window.showToast &&
                window.showToast("Error al crear el tipo de producto", "error");
        }
    },

    /**
     * Updates an existing tipo producto via the API.
     */
    async updateTipoProducto(component) {
        if (!component.itemToEdit || !component.itemToEdit.id_tipo_producto_pk)
            return;

        const nombreTrim = String(
            component.itemToEdit.nombre_tipo_producto || ""
        ).trim();
        const descripcionTrim = String(
            component.itemToEdit.descripcion_tipo_producto || ""
        ).trim();
        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del tipo de producto es obligatorio",
                    "error"
                );
            return;
        }
        if (
            component.tipoProductos.some(
                (tp) =>
                    tp.nombre_tipo_producto.toLowerCase() ===
                        nombreTrim.toLowerCase() &&
                    tp.id_tipo_producto_pk !==
                        component.itemToEdit.id_tipo_producto_pk
            )
        ) {
            window.showToast &&
                window.showToast(
                    "Ya existe otro tipo de producto con ese nombre",
                    "error"
                );
            return;
        }
        try {
            const payload = {
                nombre_tipo_producto: nombreTrim,
                descripcion_tipo_producto: descripcionTrim,
            };
            const response = await fetch(
                `/api/tipos-producto/${component.itemToEdit.id_tipo_producto_pk}`,
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
                            "Error al actualizar el tipo de producto",
                            "error"
                        );
                }
                throw data;
            }
            window.showToast &&
                window.showToast(
                    "Tipo de producto actualizado exitosamente",
                    "success"
                );
            component.isTipoProductoEditModalOpen = false;
            component.itemToEdit = null;
            await this.fetchTipoProductos(component);
        } catch (error) {
            console.error("Error updating tipo producto:", error);
        }
    },

    /**
     * Deletes a tipo producto via the API.
     */
    async deleteTipoProducto(component) {
        if (
            !component.itemToDelete ||
            !component.itemToDelete.id_tipo_producto_pk
        )
            return;
        try {
            const response = await fetch(
                `/api/tipos-producto/${component.itemToDelete.id_tipo_producto_pk}`,
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
                    "Tipo de producto eliminado exitosamente",
                    "success"
                );
            component.isTipoProductoDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchTipoProductos(component);
        } catch (error) {
            console.error("Error deleting tipo producto:", error);
            const errorMessage =
                error?.error || "Error al eliminar el tipo de producto";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },
};
