window.categoriasApiHandlers = {
    async fetchCategorias(component) {
        component.loadingCategorias = true;
        try {
            const response = await fetch("/api/categorias", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;

            component.categorias = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching categorías:", error);
            window.showToast &&
                window.showToast("Error al cargar las categorías", "error");
        } finally {
            component.loadingCategorias = false;
        }
    },

    async submitCategoria(component) {
        const nombreTrim = String(component.nombre_categoria || "").trim();
        const descripcionTrim = String(
            component.descripcion_categoria || ""
        ).trim();

        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre de la categoría es obligatorio",
                    "error"
                );
            return;
        }

        if (
            component.categorias.some(
                (cat) =>
                    cat.nombre_categoria.toLowerCase() ===
                    nombreTrim.toLowerCase()
            )
        ) {
            window.showToast &&
                window.showToast("La categoría ya existe", "error");
            return;
        }

        try {
            const payload = {
                nombre_categoria: nombreTrim,
                descripcion_categoria: descripcionTrim,
            };

            const response = await fetch("/api/categorias", {
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
                window.showToast("Categoría creada exitosamente", "success");

            component.nombre_categoria = "";
            component.descripcion_categoria = "";
            component.isCategoriaModalOpen = false;
            await this.fetchCategorias(component);
        } catch (error) {
            console.error("Error creating categoría:", error);
            window.showToast &&
                window.showToast("Error al crear la categoría", "error");
        }
    },

    async updateCategoria(component) {
        if (!component.itemToEdit || !component.itemToEdit.id_categoria_pk)
            return;

        const nombreTrim = String(
            component.itemToEdit.nombre_categoria || ""
        ).trim();
        const descripcionTrim = String(
            component.itemToEdit.descripcion_categoria || ""
        ).trim();

        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre de la categoría es obligatorio",
                    "error"
                );
            return;
        }

        if (
            component.categorias.some(
                (cat) =>
                    cat.nombre_categoria.toLowerCase() ===
                        nombreTrim.toLowerCase() &&
                    cat.id_categoria_pk !== component.itemToEdit.id_categoria_pk
            )
        ) {
            window.showToast &&
                window.showToast(
                    "Ya existe otra categoría con ese nombre",
                    "error"
                );
            return;
        }

        try {
            const payload = {
                nombre_categoria: nombreTrim,
                descripcion_categoria: descripcionTrim,
            };

            const response = await fetch(
                `/api/categorias/${component.itemToEdit.id_categoria_pk}`,
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
                            "Error al actualizar la categoría",
                            "error"
                        );
                }
                throw data;
            }
            window.showToast &&
                window.showToast(
                    "Categoría actualizada exitosamente",
                    "success"
                );

            component.isCategoriaEditModalOpen = false;
            component.itemToEdit = null;
            await this.fetchCategorias(component);
        } catch (error) {
            console.error("Error updating categoría:", error);
        }
    },

    async deleteCategoria(component) {
        if (!component.itemToDelete || !component.itemToDelete.id_categoria_pk)
            return;

        try {
            const response = await fetch(
                `/api/categorias/${component.itemToDelete.id_categoria_pk}`,
                {
                    method: "DELETE",
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                }
            );
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast("Categoría eliminada exitosamente", "success");

            component.isCategoriaDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchCategorias(component);
        } catch (error) {
            console.error("Error deleting categoría:", error);
            const errorMessage =
                error?.message ||
                error?.error ||
                "Error al eliminar la categoría";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },
};
