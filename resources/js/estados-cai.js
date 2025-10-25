window.estadosCaiApiHandlers = {
    /**
     * Fetches the list of estados CAI from the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async fetchEstadosCai(component) {
        component.loadingEstadosCai = true;
        try {
            const response = await fetch("/api/estados-cai", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            // Assuming the API returns data in 'data' key or directly an array
            component.estadosCai = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching estados cai:", error);
            window.showToast &&
                window.showToast(
                    "Error al cargar estados CAI",
                    "error"
                );
        } finally {
            component.loadingEstadosCai = false;
        }
    },

    /**
     * Submits a new estado CAI to the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async submitEstadoCai(component) {
        const nombreTrim = String(component.nombre_estado_cai || "").trim();
        const descripcionTrim = String(component.descripcion_estado_cai || "").trim();
        const codigoTrim = String(component.codigo_estado_cai || "").trim();
        
        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del estado CAI es obligatorio",
                    "error"
                );
            return;
        }
        
        if (
            component.estadosCai.some(
                (ec) =>
                    ec.nombre_estado_cai.toLowerCase() ===
                    nombreTrim.toLowerCase()
            )
        ) {
            window.showToast &&
                window.showToast("El estado CAI ya existe", "error");
            return;
        }
        
        try {
            const payload = {
                codigo_estado_cai: codigoTrim || null,
                nombre_estado_cai: nombreTrim,
                descripcion_estado_cai: descripcionTrim || null,
                es_final: component.es_final || false,
                orden: parseInt(component.orden) || 0,
            };
            const response = await fetch("/api/estados-cai", {
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
                window.showToast(
                    "Estado CAI creado exitosamente",
                    "success"
                );
            component.codigo_estado_cai = "";
            component.nombre_estado_cai = "";
            component.descripcion_estado_cai = "";
            component.es_final = false;
            component.orden = 0;
            component.isEstadoCaiModalOpen = false;
            await this.fetchEstadosCai(component);
        } catch (error) {
            console.error("Error creating estado cai:", error);
            window.showToast &&
                window.showToast(
                    "Error al crear el estado CAI",
                    "error"
                );
        }
    },

    /**
     * Updates an existing estado CAI via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async updateEstadoCai(component) {
        if (!component.itemToEdit || !component.itemToEdit.id_estado_cai_pk)
            return;
            
        const nombreTrim = String(component.itemToEdit.nombre_estado_cai || "").trim();
        const descripcionTrim = String(component.itemToEdit.descripcion_estado_cai || "").trim();
        const codigoTrim = String(component.itemToEdit.codigo_estado_cai || "").trim();
        
        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del estado CAI es obligatorio",
                    "error"
                );
            return;
        }
        
        if (
            component.estadosCai.some(
                (ec) =>
                    ec.nombre_estado_cai.toLowerCase() ===
                        nombreTrim.toLowerCase() &&
                    ec.id_estado_cai_pk !==
                        component.itemToEdit.id_estado_cai_pk
            )
        ) {
            window.showToast &&
                window.showToast(
                    "Ya existe otro estado CAI con ese nombre",
                    "error"
                );
            return;
        }
        
        try {
            const payload = {
                codigo_estado_cai: codigoTrim || null,
                nombre_estado_cai: nombreTrim,
                descripcion_estado_cai: descripcionTrim || null,
                es_final: component.itemToEdit.es_final || false,
                orden: parseInt(component.itemToEdit.orden) || 0,
            };
            const response = await fetch(
                `/api/estados-cai/${component.itemToEdit.id_estado_cai_pk}`,
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
                // Mostrar errores de validación si existen
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
                            "Error al actualizar el estado CAI",
                            "error"
                        );
                }
                throw data;
            }
            window.showToast &&
                window.showToast(
                    "Estado CAI actualizado exitosamente",
                    "success"
                );
            component.isEditEstadoCaiModalOpen = false;
            component.itemToEdit = {
                id_estado_cai_pk: null,
                codigo_estado_cai: '',
                nombre_estado_cai: '',
                descripcion_estado_cai: '',
                es_final: false,
                orden: 0
            };
            await this.fetchEstadosCai(component);
        } catch (error) {
            console.error("Error updating estado cai:", error);
        }
    },

    /**
     * Deletes an estado CAI via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async deleteEstadoCai(component) {
        if (!component.itemToDelete || !component.itemToDelete.id_estado_cai_pk)
            return;
            
        try {
            const response = await fetch(
                `/api/estados-cai/${component.itemToDelete.id_estado_cai_pk}`,
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
                    "Estado CAI eliminado exitosamente",
                    "success"
                );
            component.isDeleteEstadoCaiModalOpen = false;
            component.itemToDelete = null;
            await this.fetchEstadosCai(component);
        } catch (error) {
            console.error("Error deleting estado cai:", error);
            const errorMessage =
                error?.error || "Error al eliminar el estado CAI";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },
};