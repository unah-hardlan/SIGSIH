window.productosApiHandlers = {
    authHeaders() {
        return {
            "Content-Type": "application/json",
            Accept: "application/json",
        };
    },

    /**
     * MODIFICADO: Ahora construye una URL dinámica con los parámetros de filtro (q) y
     * ordenamiento (sort) para enviarlos a la API.
     */
    async fetchProductos(component) {
        component.loadingProductos = true;
        try {
            const params = new URLSearchParams();
            if (component.filtroProducto) {
                params.set("q", component.filtroProducto);
            }
            if (component.ordenarPor) {
                params.set("sort", component.ordenarPor);
            }

            params.set("all", "true");

            const response = await fetch(
                `/api/productos?${params.toString()}`,
                {
                    headers: this.authHeaders(),
                    credentials: "include",
                }
            );
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;

            component.productos = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching productos:", error);
            window.showToast &&
                window.showToast("Error al cargar los productos", "error");
        } finally {
            component.loadingProductos = false;
        }
    },

    async submitProducto(component) {
        const skuTrim = String(component.sku || "").trim();
        const nombreTrim = String(component.nombre_producto || "").trim();
        const descripcionTrim = String(
            component.descripcion_producto || ""
        ).trim();
        const precioUnitario = parseFloat(component.precio_unitario) || 0;
        const precioCosto = component.precio_costo
            ? parseFloat(component.precio_costo)
            : null;
        const precioVenta = parseFloat(component.precio_venta) || 0;
        const stockMinimo = parseInt(component.stock_minimo) || 0;
        const tipoProducto = component.id_tipo_producto_fk;

        if (!skuTrim) {
            window.showToast &&
                window.showToast("El SKU del producto es obligatorio", "error");
            return;
        }
        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del producto es obligatorio",
                    "error"
                );
            return;
        }
        if (precioUnitario <= 0) {
            window.showToast &&
                window.showToast(
                    "El precio unitario debe ser mayor a 0",
                    "error"
                );
            return;
        }
        if (precioVenta <= 0) {
            window.showToast &&
                window.showToast(
                    "El precio de venta debe ser mayor a 0",
                    "error"
                );
            return;
        }
        if (stockMinimo < 0) {
            window.showToast &&
                window.showToast(
                    "El stock mínimo no puede ser negativo",
                    "error"
                );
            return;
        }
        if (!tipoProducto) {
            window.showToast &&
                window.showToast(
                    "Debe seleccionar un tipo de producto",
                    "error"
                );
            return;
        }
        if (
            component.productos.some(
                (prod) => prod.sku.toLowerCase() === skuTrim.toLowerCase()
            )
        ) {
            window.showToast && window.showToast("El SKU ya existe", "error");
            return;
        }
        if (
            component.productos.some(
                (prod) =>
                    prod.nombre_producto.toLowerCase() ===
                    nombreTrim.toLowerCase()
            )
        ) {
            window.showToast &&
                window.showToast("El nombre del producto ya existe", "error");
            return;
        }

        try {
            const payload = {
                sku: skuTrim,
                nombre_producto: nombreTrim,
                descripcion_producto: descripcionTrim,
                precio_unitario: precioUnitario,
                precio_costo: precioCosto,
                precio_venta: precioVenta,
                stock_minimo: stockMinimo,
                id_tipo_producto_fk: tipoProducto,
            };

            const response = await fetch("/api/productos", {
                method: "POST",
                headers: this.authHeaders(),
                credentials: "include",
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
                window.showToast("Producto creado exitosamente", "success");

            component.sku = "";
            component.nombre_producto = "";
            component.descripcion_producto = "";
            component.precio_unitario = "";
            component.precio_costo = "";
            component.precio_venta = "";
            component.stock_minimo = "";
            component.id_tipo_producto_fk = "";
            component.isProductoModalOpen = false;
            await this.fetchProductos(component);
        } catch (error) {
            console.error("Error creating producto:", error);
            window.showToast &&
                window.showToast("Error al crear el producto", "error");
        }
    },

    async updateProducto(component) {
        if (!component.itemToEdit || !component.itemToEdit.id_producto_pk)
            return;

        const skuTrim = String(component.itemToEdit.sku || "").trim();
        const nombreTrim = String(
            component.itemToEdit.nombre_producto || ""
        ).trim();
        const descripcionTrim = String(
            component.itemToEdit.descripcion_producto || ""
        ).trim();
        const precioUnitario =
            parseFloat(component.itemToEdit.precio_unitario) || 0;
        const precioCosto = component.itemToEdit.precio_costo
            ? parseFloat(component.itemToEdit.precio_costo)
            : null;
        const precioVenta = parseFloat(component.itemToEdit.precio_venta) || 0;
        const stockMinimo = parseInt(component.itemToEdit.stock_minimo) || 0;
        const tipoProducto = component.itemToEdit.id_tipo_producto_fk;

        if (!skuTrim) {
            window.showToast &&
                window.showToast("El SKU del producto es obligatorio", "error");
            return;
        }
        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del producto es obligatorio",
                    "error"
                );
            return;
        }
        if (precioUnitario <= 0) {
            window.showToast &&
                window.showToast(
                    "El precio unitario debe ser mayor a 0",
                    "error"
                );
            return;
        }
        if (precioVenta <= 0) {
            window.showToast &&
                window.showToast(
                    "El precio de venta debe ser mayor a 0",
                    "error"
                );
            return;
        }
        if (stockMinimo < 0) {
            window.showToast &&
                window.showToast(
                    "El stock mínimo no puede ser negativo",
                    "error"
                );
            return;
        }
        if (!tipoProducto) {
            window.showToast &&
                window.showToast(
                    "Debe seleccionar un tipo de producto",
                    "error"
                );
            return;
        }
        if (
            component.productos.some(
                (prod) =>
                    prod.sku.toLowerCase() === skuTrim.toLowerCase() &&
                    prod.id_producto_pk !== component.itemToEdit.id_producto_pk
            )
        ) {
            window.showToast &&
                window.showToast(
                    "Ya existe otro producto con ese SKU",
                    "error"
                );
            return;
        }
        if (
            component.productos.some(
                (prod) =>
                    prod.nombre_producto.toLowerCase() ===
                        nombreTrim.toLowerCase() &&
                    prod.id_producto_pk !== component.itemToEdit.id_producto_pk
            )
        ) {
            window.showToast &&
                window.showToast(
                    "Ya existe otro producto con ese nombre",
                    "error"
                );
            return;
        }

        try {
            const payload = {
                sku: skuTrim,
                nombre_producto: nombreTrim,
                descripcion_producto: descripcionTrim,
                precio_unitario: precioUnitario,
                precio_costo: precioCosto,
                precio_venta: precioVenta,
                stock_minimo: stockMinimo,
                id_tipo_producto_fk: tipoProducto,
            };

            const response = await fetch(
                `/api/productos/${component.itemToEdit.id_producto_pk}`,
                {
                    method: "PUT",
                    headers: this.authHeaders(),
                    credentials: "include",
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
                            "Error al actualizar el producto",
                            "error"
                        );
                }
                throw data;
            }
            window.showToast &&
                window.showToast(
                    "Producto actualizado exitosamente",
                    "success"
                );

            component.isProductoEditModalOpen = false;
            component.itemToEdit = null;
            await this.fetchProductos(component);
        } catch (error) {
            console.error("Error updating producto:", error);
        }
    },

    async deleteProducto(component) {
        if (!component.itemToDelete || !component.itemToDelete.id_producto_pk)
            return;

        try {
            const response = await fetch(
                `/api/productos/${component.itemToDelete.id_producto_pk}`,
                {
                    method: "DELETE",
                    headers: this.authHeaders(),
                    credentials: "include",
                }
            );
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast("Producto eliminado exitosamente", "success");

            component.isProductoDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchProductos(component);
        } catch (error) {
            console.error("Error deleting producto:", error);
            const errorMessage =
                error?.message ||
                error?.error ||
                "Error al eliminar el producto";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },
};

window.tipoProductosApiHandlers = window.tipoProductosApiHandlers || {
    async fetchTipoProductos(component) {
        if (component.loadingTipoProductos) return;
        component.loadingTipoProductos = true;
        try {
            const response = await fetch("/api/tipos-producto?all=true");
            const data = await response.json();
            component.tipoProductos = data.data || [];
        } catch (e) {
            console.error("Error fetching tipo de productos", e);
            window.showToast &&
                window.showToast("Error al cargar tipos de producto", "error");
        } finally {
            component.loadingTipoProductos = false;
        }
    },
};
