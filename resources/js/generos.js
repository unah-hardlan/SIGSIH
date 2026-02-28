window.generosApiHandlers = {
    authHeaders() {
        return {
            "Content-Type": "application/json",
            Accept: "application/json",
        };
    },


    async fetchGeneros(component) {
        component.loadingGeneros = true;
        try {
            const response = await fetch("/api/generos", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;

            component.generos = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                    ? data
                    : [];
        } catch (error) {
            console.error("Error fetching generos:", error);
            window.showToast &&
                window.showToast("Error al cargar géneros", "error");
        } finally {
            component.loadingGeneros = false;
        }
    },


    async submitGenero(component) {
        const generoTrim = String(component.genero || "").trim();
        if (!generoTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del género es obligatorio",
                    "error"
                );
            return;
        }
        if (
            component.generos.some(
                (g) => g.genero.toLowerCase() === generoTrim.toLowerCase()
            )
        ) {
            window.showToast &&
                window.showToast("El género ya existe", "error");
            return;
        }
        try {
            const payload = {
                genero: generoTrim,
            };
            const response = await fetch("/api/generos", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
                credentials: "same-origin",
                body: JSON.stringify(payload),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast("Género creado exitosamente", "success");
            component.genero = "";
            component.isGeneroModalOpen = false;
            await this.fetchGeneros(component);
        } catch (error) {
            console.error("Error creating genero:", error);
            window.showToast &&
                window.showToast("Error al crear el género", "error");
        }
    },


    async updateGenero(component) {
        if (!component.itemToEdit || !component.itemToEdit.id_genero_pk) return;
        const generoTrim = String(component.itemToEdit.genero || "").trim();
        if (!generoTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del género es obligatorio",
                    "error"
                );
            return;
        }
        if (
            component.generos.some(
                (g) =>
                    g.genero.toLowerCase() === generoTrim.toLowerCase() &&
                    g.id_genero_pk !== component.itemToEdit.id_genero_pk
            )
        ) {
            window.showToast &&
                window.showToast(
                    "Ya existe otro género con ese nombre",
                    "error"
                );
            return;
        }
        try {
            const payload = {
                genero: generoTrim,
            };
            const response = await fetch(
                `/api/generos/${component.itemToEdit.id_genero_pk}`,
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
                            "Error al actualizar el género",
                            "error"
                        );
                }
                throw data;
            }
            window.showToast &&
                window.showToast("Género actualizado exitosamente", "success");
            component.isGeneroEditModalOpen = false;
            component.itemToEdit = null;
            await this.fetchGeneros(component);
        } catch (error) {
            console.error("Error updating genero:", error);
        }
    },


    async deleteGenero(component) {
        if (!component.itemToDelete || !component.itemToDelete.id_genero_pk)
            return;
        try {
            const response = await fetch(
                `/api/generos/${component.itemToDelete.id_genero_pk}`,
                {
                    method: "DELETE",
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                }
            );
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast("Género eliminado exitosamente", "success");
            component.isGeneroDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchGeneros(component);
        } catch (error) {
            console.error("Error deleting genero:", error);
            const errorMessage = error?.error || "Error al eliminar el género";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },
};
