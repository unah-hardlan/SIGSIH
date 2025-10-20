window.caiApiHandlers = {
    /**
     * Fetches the list of CAI from the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async fetchCai(component) {
        // Prevenir llamadas concurrentes
        if (component.loadingCai) {
            return;
        }
        
        component.loadingCai = true;
        try {
            const response = await fetch("/api/cai", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            
            // Assuming the API returns data in 'data' key or directly an array
            component.cais = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
                
            // También cargar los estados CAI para los selects
            await this.fetchEstadosCai(component);
        } catch (error) {
            console.error("Error fetching CAI:", error);
            window.showToast &&
                window.showToast(
                    "Error al cargar CAI",
                    "error"
                );
        } finally {
            component.loadingCai = false;
        }
    },

    /**
     * Fetches the list of Estado CAI for selects.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async fetchEstadosCai(component) {
        try {
            // Agregar timestamp único y forzar recarga completa
            const timestamp = Date.now() + Math.random();
            const response = await fetch(`/api/estados-cai?_t=${timestamp}&_bust=${Math.random()}`, {
                method: 'GET',
                headers: { 
                    Accept: "application/json",
                    "Cache-Control": "no-cache, no-store, must-revalidate",
                    "Pragma": "no-cache",
                    "Expires": "0"
                },
                credentials: "same-origin",
                cache: 'no-store' // Forzar no usar caché
            });
            const data = await response.json().catch(() => ({}));
            
            if (!response.ok) throw data;
            
            console.log('Estados CAI fetched (fresh):', data);
            console.log('Response headers:', [...response.headers.entries()]);
            
            component.estadosCai = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
                
            console.log('Estados CAI processed (should be fresh):', component.estadosCai);
        } catch (error) {
            console.error("❌ Error fetching Estados CAI:", error);
        }
    },

    /**
     * Submits a new CAI to the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async submitCai(component) {
        const codigoTrim = String(component.codigo || "").trim();
        const rangoInicioTrim = String(component.rango_inicio || "").trim();
        const rangoFinTrim = String(component.rango_fin || "").trim();
        const fechaLimite = component.fecha_limite;
        const estadoCaiId = component.id_estado_cai_fk;
        
        if (!codigoTrim) {
            window.showToast &&
                window.showToast(
                    "El código del CAI es obligatorio",
                    "error"
                );
            return;
        }
        
        if (!rangoInicioTrim || !rangoFinTrim) {
            window.showToast &&
                window.showToast(
                    "Los rangos de inicio y fin son obligatorios",
                    "error"
                );
            return;
        }
        
        if (!fechaLimite) {
            window.showToast &&
                window.showToast(
                    "La fecha límite es obligatoria",
                    "error"
                );
            return;
        }
        
        if (!estadoCaiId) {
            window.showToast &&
                window.showToast(
                    "El estado CAI es obligatorio",
                    "error"
                );
            return;
        }
        
        if (
            component.cais.some(
                (c) =>
                    c.codigo.toLowerCase() === codigoTrim.toLowerCase()
            )
        ) {
            window.showToast &&
                window.showToast("El CAI ya existe", "error");
            return;
        }
        
        try {
            const payload = {
                codigo: codigoTrim,
                rango_inicio: rangoInicioTrim,
                rango_fin: rangoFinTrim,
                consecutivo_actual: parseInt(component.consecutivo_actual) || 0,
                fecha_limite: fechaLimite,
                id_estado_cai_fk: parseInt(estadoCaiId),
            };
            const response = await fetch("/api/cai", {
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
                    "CAI creado exitosamente",
                    "success"
                );
            component.codigo = "";
            component.rango_inicio = "";
            component.rango_fin = "";
            component.consecutivo_actual = 0;
            component.fecha_limite = "";
            component.id_estado_cai_fk = "";
            component.isCaiModalOpen = false;
            await this.fetchCai(component);
        } catch (error) {
            console.error("Error creating CAI:", error);
            window.showToast &&
                window.showToast(
                    "Error al crear el CAI",
                    "error"
                );
        }
    },

    /**
     * Updates an existing CAI via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async updateCai(component) {
        if (!component.itemToEdit || (!component.itemToEdit.id && !component.itemToEdit.id_cai_pk))
            return;
            
        // Leer valores directamente desde los campos del formulario
        const codigoTrim = String(document.getElementById('edit_codigo')?.value || "").trim();
        const rangoInicioTrim = String(document.getElementById('edit_rango_inicio')?.value || "").trim();
        const rangoFinTrim = String(document.getElementById('edit_rango_fin')?.value || "").trim();
        const consecutivoActual = parseInt(document.getElementById('edit_consecutivo_actual')?.value) || 0;
        const fechaLimite = document.getElementById('edit_fecha_limite')?.value;
        const estadoCaiId = parseInt(document.getElementById('edit_id_estado_cai_fk')?.value);
        
        if (!codigoTrim) {
            window.showToast &&
                window.showToast(
                    "El código del CAI es obligatorio",
                    "error"
                );
            return;
        }
        
        if (!rangoInicioTrim || !rangoFinTrim) {
            window.showToast &&
                window.showToast(
                    "Los rangos de inicio y fin son obligatorios",
                    "error"
                );
            return;
        }
        
        if (!fechaLimite) {
            window.showToast &&
                window.showToast(
                    "La fecha límite es obligatoria",
                    "error"
                );
            return;
        }
        
        if (!estadoCaiId) {
            window.showToast &&
                window.showToast(
                    "El estado CAI es obligatorio",
                    "error"
                );
            return;
        }
        
        if (
            component.cais.some(
                (c) =>
                    c.codigo.toLowerCase() === codigoTrim.toLowerCase() &&
                    (c.id_cai_pk || c.id) !== (component.itemToEdit.id_cai_pk || component.itemToEdit.id)
            )
        ) {
            window.showToast &&
                window.showToast(
                    "Ya existe otro CAI con ese código",
                    "error"
                );
            return;
        }
        
        try {
            const payload = {
                codigo: codigoTrim,
                rango_inicio: rangoInicioTrim,
                rango_fin: rangoFinTrim,
                consecutivo_actual: consecutivoActual,
                fecha_limite: fechaLimite,
                id_estado_cai_fk: estadoCaiId,
            };

            const response = await fetch(
                `/api/cai/${component.itemToEdit.id_cai_pk || component.itemToEdit.id}`,
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
                            "Error al actualizar el CAI",
                            "error"
                        );
                }
                throw data;
            }
            window.showToast &&
                window.showToast(
                    "CAI actualizado exitosamente",
                    "success"
                );
            component.isEditCaiModalOpen = false;
            component.itemToEdit = null;
            await this.fetchCai(component);
        } catch (error) {
            console.error("Error updating CAI:", error);
        }
    },

    /**
     * Deletes a CAI via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async deleteCai(component) {
        if (!component.itemToDelete || (!component.itemToDelete.id && !component.itemToDelete.id_cai_pk))
            return;
            
        try {
            const response = await fetch(
                `/api/cai/${component.itemToDelete.id_cai_pk || component.itemToDelete.id}`,
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
                    "CAI eliminado exitosamente",
                    "success"
                );
            component.isDeleteCaiModalOpen = false;
            component.itemToDelete = null;
            await this.fetchCai(component);
        } catch (error) {
            console.error("Error deleting CAI:", error);
            const errorMessage =
                error?.error || "Error al eliminar el CAI";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },
};