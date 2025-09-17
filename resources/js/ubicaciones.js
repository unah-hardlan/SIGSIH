window.paisesApiHandlers = {
    /**
     * Fetches the list of countries from the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async fetchPaises(component) {
        component.loadingPaises = true;
        try {
            const response = await fetch("/api/paises", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            // Assuming the API returns data in 'data' key or directly an array
            component.paises = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching paises:", error);
            window.showToast &&
                window.showToast("Error al cargar países", "error");
        } finally {
            component.loadingPaises = false;
        }
    },

    /**
     * Submits a new country to the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async submitPais(component) {
        const nombreTrim = String(component.nombre_pais || "").trim();
        if (!nombreTrim) {
            window.showToast &&
                window.showToast("El nombre del país es obligatorio", "error");
            return;
        }
        if (
            component.paises.some(
                (p) => p.nombre_pais.toLowerCase() === nombreTrim.toLowerCase()
            )
        ) {
            window.showToast && window.showToast("El país ya existe", "error");
            return;
        }
        try {
            const payload = {
                nombre_pais: nombreTrim,
            };
            const response = await fetch("/api/paises", {
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
                window.showToast("País creado exitosamente", "success");
            component.nombre_pais = "";
            component.isPaisModalOpen = false;
            await this.fetchPaises(component); // Use 'this' to call other methods within the same handler object
        } catch (error) {
            console.error("Error creating pais:", error);
            window.showToast &&
                window.showToast("Error al crear el país", "error");
        }
    },

    /**
     * Updates an existing country via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async updatePais(component) {
        if (!component.itemToEdit || !component.itemToEdit.id) return;
        const nombreTrim = String(component.itemToEdit.nombre || "").trim();
        if (!nombreTrim) {
            window.showToast &&
                window.showToast("El nombre del país es obligatorio", "error");
            return;
        }
        if (
            component.paises.some(
                (p) =>
                    p.nombre_pais.toLowerCase() === nombreTrim.toLowerCase() &&
                    p.id_pais_pk !== component.itemToEdit.id
            )
        ) {
            window.showToast &&
                window.showToast("Ya existe otro país con ese nombre", "error");
            return;
        }
        try {
            const payload = {
                nombre_pais: nombreTrim,
            };
            const response = await fetch(
                `/api/paises/${component.itemToEdit.id}`,
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
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast("País actualizado exitosamente", "success");
            component.isPaisEditModalOpen = false;
            component.itemToEdit = null;
            await this.fetchPaises(component);
        } catch (error) {
            console.error("Error updating pais:", error);
            window.showToast &&
                window.showToast("Error al actualizar el país", "error");
        }
    },

    /**
     * Deletes a country via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async deletePais(component) {
        if (!component.itemToDelete || !component.itemToDelete.id) return;
        try {
            const response = await fetch(
                `/api/paises/${component.itemToDelete.id}`,
                {
                    method: "DELETE",
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                }
            );
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast("País eliminado exitosamente", "success");
            component.isPaisDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchPaises(component);
        } catch (error) {
            console.error("Error deleting pais:", error);
            window.showToast &&
                window.showToast("Error al eliminar el país", "error");
        }
    },
};
