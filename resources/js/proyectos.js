window.proyectosApiHandlers = {
    /**
     * Fetches the list of proyectos from the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async fetchProyectos(component) {
        component.loadingProyectos = true;
        try {
            const response = await fetch("/api/proyectos", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            // Assuming the API returns data in 'data' key or directly an array
            component.proyectos = Array.isArray(data?.data)
                ? data.data.map((item) => ({
                      id_proyecto_pk: item.id_proyecto_pk,
                      nombre_proyecto: item.nombre_proyecto,
                      fecha_inicio_proyecto: item.fecha_inicio_proyecto,
                      fecha_estimada_fin_proyecto:
                          item.fecha_estimada_fin_proyecto,
                      fecha_finalizacion_proyecto:
                          item.fecha_finalizacion_proyecto,
                      descripcion_proyecto: item.descripcion_proyecto,
                      id_orden_servicio_fk: item.id_orden_servicio_fk,
                      id_estado_proyecto_fk: item.id_estado_proyecto_fk,
                      orden_servicio: item.orden_servicio,
                      estado_proyecto: item.estado_proyecto,
                  }))
                : Array.isArray(data)
                ? data.map((item) => ({
                      id_proyecto_pk: item.id_proyecto_pk,
                      nombre_proyecto: item.nombre_proyecto,
                      fecha_inicio_proyecto: item.fecha_inicio_proyecto,
                      fecha_estimada_fin_proyecto:
                          item.fecha_estimada_fin_proyecto,
                      fecha_finalizacion_proyecto:
                          item.fecha_finalizacion_proyecto,
                      descripcion_proyecto: item.descripcion_proyecto,
                      id_orden_servicio_fk: item.id_orden_servicio_fk,
                      id_estado_proyecto_fk: item.id_estado_proyecto_fk,
                      orden_servicio: item.orden_servicio,
                      estado_proyecto: item.estado_proyecto,
                  }))
                : [];
        } catch (error) {
            console.error("Error fetching proyectos:", error);
            window.showToast &&
                window.showToast("Error al cargar proyectos", "error");
        } finally {
            component.loadingProyectos = false;
        }
    },

    /**
     * Submits a new proyecto to the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async submitProyecto(component) {
        const nombreTrim = String(
            component.newProyecto.nombre_proyecto || ""
        ).trim();
        const descripcionTrim = String(
            component.newProyecto.descripcion_proyecto || ""
        ).trim();
        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del proyecto es obligatorio",
                    "error"
                );
            return;
        }
        try {
            const payload = {
                nombre_proyecto: nombreTrim,
                fecha_inicio_proyecto:
                    component.newProyecto.fecha_inicio_proyecto,
                fecha_estimada_fin_proyecto:
                    component.newProyecto.fecha_estimada_fin_proyecto,
                fecha_finalizacion_proyecto:
                    component.newProyecto.fecha_finalizacion_proyecto,
                descripcion_proyecto: descripcionTrim,
                id_orden_servicio_fk:
                    component.newProyecto.id_orden_servicio_fk,
                id_estado_proyecto_fk:
                    component.newProyecto.id_estado_proyecto_fk,
            };
            const response = await fetch("/api/proyectos", {
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
                window.showToast("Proyecto creado exitosamente", "success");
            component.newProyecto = {
                nombre_proyecto: "",
                fecha_inicio_proyecto: "",
                fecha_estimada_fin_proyecto: "",
                fecha_finalizacion_proyecto: null,
                descripcion_proyecto: "",
                id_orden_servicio_fk: null,
                id_estado_proyecto_fk: null,
            };
            component.isProyectoModalOpen = false;
            await this.fetchProyectos(component);
        } catch (error) {
            console.error("Error creating proyecto:", error);
            window.showToast &&
                window.showToast("Error al crear el proyecto", "error");
        }
    },

    /**
     * Updates an existing proyecto via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async updateProyecto(component) {
        if (!component.itemToEdit || !component.itemToEdit.id_proyecto_pk)
            return;
        const nombreTrim = String(
            component.itemToEdit.nombre_proyecto || ""
        ).trim();
        const descripcionTrim = String(
            component.itemToEdit.descripcion_proyecto || ""
        ).trim();
        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del proyecto es obligatorio",
                    "error"
                );
            return;
        }
        try {
            const payload = {
                nombre_proyecto: nombreTrim,
                fecha_inicio_proyecto:
                    component.itemToEdit.fecha_inicio_proyecto,
                fecha_estimada_fin_proyecto:
                    component.itemToEdit.fecha_estimada_fin_proyecto,
                fecha_finalizacion_proyecto:
                    component.itemToEdit.fecha_finalizacion_proyecto,
                descripcion_proyecto: descripcionTrim,
                id_orden_servicio_fk: component.itemToEdit.id_orden_servicio_fk,
                id_estado_proyecto_fk:
                    component.itemToEdit.id_estado_proyecto_fk,
            };
            const response = await fetch(
                `/api/proyectos/${component.itemToEdit.id_proyecto_pk}`,
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
                            "Error al actualizar el proyecto",
                            "error"
                        );
                }
                throw data;
            }
            window.showToast &&
                window.showToast(
                    "Proyecto actualizado exitosamente",
                    "success"
                );
            component.isProyectoEditModalOpen = false;
            component.itemToEdit = null;
            await this.fetchProyectos(component);
        } catch (error) {
            console.error("Error updating proyecto:", error);
        }
    },

    /**
     * Deletes a proyecto via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async deleteProyecto(component) {
        if (!component.itemToDelete || !component.itemToDelete.id_proyecto_pk)
            return;
        try {
            const response = await fetch(
                `/api/proyectos/${component.itemToDelete.id_proyecto_pk}`,
                {
                    method: "DELETE",
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                }
            );
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast("Proyecto eliminado exitosamente", "success");
            component.isProyectoDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchProyectos(component);
        } catch (error) {
            console.error("Error deleting proyecto:", error);
            const errorMessage =
                error?.error || "Error al eliminar el proyecto";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },
};

window.ingresosApiHandlers = {
    /**
     * Fetches the list of ingresos from the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async fetchIngresos(component) {
        component.loadingIngresos = true;
        try {
            const response = await fetch("/api/ingresos", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            // Assuming the API returns data in 'data' key or directly an array
            component.ingresos = Array.isArray(data?.data)
                ? data.data.map((item) => ({
                      id_ingreso_pk: item.id_ingreso_pk,
                      nombre: item.nombre,
                      fecha: item.fecha,
                      monto: item.monto,
                      descripcion: item.descripcion,
                      id_proyecto_fk: item.id_proyecto_fk,
                      id_categoria_fk: item.id_categoria_fk,
                      proyecto: item.proyecto,
                      categoria: item.categoria,
                  }))
                : Array.isArray(data)
                ? data.map((item) => ({
                      id_ingreso_pk: item.id_ingreso_pk,
                      nombre: item.nombre,
                      fecha: item.fecha,
                      monto: item.monto,
                      descripcion: item.descripcion,
                      id_proyecto_fk: item.id_proyecto_fk,
                      id_categoria_fk: item.id_categoria_fk,
                      proyecto: item.proyecto,
                      categoria: item.categoria,
                  }))
                : [];
        } catch (error) {
            console.error("Error fetching ingresos:", error);
            window.showToast &&
                window.showToast("Error al cargar ingresos", "error");
        } finally {
            component.loadingIngresos = false;
        }
    },

    /**
     * Submits a new ingreso to the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async submitIngreso(component) {
        const nombreTrim = String(component.newIngreso.nombre || "").trim();
        const descripcionTrim = String(
            component.newIngreso.descripcion || ""
        ).trim();
        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del ingreso es obligatorio",
                    "error"
                );
            return;
        }
        try {
            const payload = {
                nombre: nombreTrim,
                fecha: component.newIngreso.fecha,
                monto: component.newIngreso.monto,
                descripcion: descripcionTrim,
                id_proyecto_fk: component.newIngreso.id_proyecto_fk,
                id_categoria_fk: component.newIngreso.id_categoria_fk,
            };
            const response = await fetch("/api/ingresos", {
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
                window.showToast("Ingreso creado exitosamente", "success");
            component.newIngreso = {
                id_proyecto_fk: null,
                nombre: "",
                fecha: "",
                monto: "",
                id_categoria_fk: null,
                descripcion: "",
            };
            component.isIngresoModalOpen = false;
            await this.fetchIngresos(component);
        } catch (error) {
            console.error("Error creating ingreso:", error);
            window.showToast &&
                window.showToast("Error al crear el ingreso", "error");
        }
    },

    /**
     * Updates an existing ingreso via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async updateIngreso(component) {
        if (!component.itemToEdit || !component.itemToEdit.id_ingreso_pk)
            return;
        const nombreTrim = String(component.itemToEdit.nombre || "").trim();
        const descripcionTrim = String(
            component.itemToEdit.descripcion || ""
        ).trim();
        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del ingreso es obligatorio",
                    "error"
                );
            return;
        }
        try {
            const payload = {
                nombre: nombreTrim,
                fecha: component.itemToEdit.fecha,
                monto: component.itemToEdit.monto,
                descripcion: descripcionTrim,
                id_proyecto_fk: component.itemToEdit.id_proyecto_fk,
                id_categoria_fk: component.itemToEdit.id_categoria_fk,
            };
            const response = await fetch(
                `/api/ingresos/${component.itemToEdit.id_ingreso_pk}`,
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
                            "Error al actualizar el ingreso",
                            "error"
                        );
                }
                throw data;
            }
            window.showToast &&
                window.showToast("Ingreso actualizado exitosamente", "success");
            component.isIngresoEditModalOpen = false;
            component.itemToEdit = null;
            await this.fetchIngresos(component);
        } catch (error) {
            console.error("Error updating ingreso:", error);
        }
    },

    /**
     * Deletes a ingreso via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async deleteIngreso(component) {
        if (!component.itemToDelete || !component.itemToDelete.id_ingreso_pk)
            return;
        try {
            const response = await fetch(
                `/api/ingresos/${component.itemToDelete.id_ingreso_pk}`,
                {
                    method: "DELETE",
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                }
            );
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast("Ingreso eliminado exitosamente", "success");
            component.isIngresoDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchIngresos(component);
        } catch (error) {
            console.error("Error deleting ingreso:", error);
            const errorMessage = error?.error || "Error al eliminar el ingreso";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },
};

window.gastosApiHandlers = {
    /**
     * Fetches the list of gastos from the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async fetchGastos(component) {
        component.loadingGastos = true;
        try {
            const response = await fetch("/api/gastos", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            // Assuming the API returns data in 'data' key or directly an array
            component.gastos = Array.isArray(data?.data)
                ? data.data.map((item) => ({
                      id_gasto_pk: item.id_gasto_pk,
                      nombre: item.nombre,
                      fecha: item.fecha,
                      monto: item.monto,
                      descripcion: item.descripcion,
                      id_proyecto_fk: item.id_proyecto_fk,
                      id_categoria_fk: item.id_categoria_fk,
                      proyecto: item.proyecto,
                      categoria: item.categoria,
                  }))
                : Array.isArray(data)
                ? data.map((item) => ({
                      id_gasto_pk: item.id_gasto_pk,
                      nombre: item.nombre,
                      fecha: item.fecha,
                      monto: item.monto,
                      descripcion: item.descripcion,
                      id_proyecto_fk: item.id_proyecto_fk,
                      id_categoria_fk: item.id_categoria_fk,
                      proyecto: item.proyecto,
                      categoria: item.categoria,
                  }))
                : [];
        } catch (error) {
            console.error("Error fetching gastos:", error);
            window.showToast &&
                window.showToast("Error al cargar gastos", "error");
        } finally {
            component.loadingGastos = false;
        }
    },

    /**
     * Submits a new gasto to the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async submitGasto(component) {
        const nombreTrim = String(component.newGasto.nombre || "").trim();
        const descripcionTrim = String(
            component.newGasto.descripcion || ""
        ).trim();
        if (!nombreTrim) {
            window.showToast &&
                window.showToast("El nombre del gasto es obligatorio", "error");
            return;
        }
        try {
            const payload = {
                nombre: nombreTrim,
                fecha: component.newGasto.fecha,
                monto: component.newGasto.monto,
                descripcion: descripcionTrim,
                id_proyecto_fk: component.newGasto.id_proyecto_fk,
                id_categoria_fk: component.newGasto.id_categoria_fk,
            };
            const response = await fetch("/api/gastos", {
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
                window.showToast("Gasto creado exitosamente", "success");
            component.newGasto = {
                id_proyecto_fk: null,
                nombre: "",
                fecha: "",
                monto: "",
                id_categoria_fk: null,
                descripcion: "",
            };
            component.isGastoModalOpen = false;
            await this.fetchGastos(component);
        } catch (error) {
            console.error("Error creating gasto:", error);
            window.showToast &&
                window.showToast("Error al crear el gasto", "error");
        }
    },

    /**
     * Updates an existing gasto via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async updateGasto(component) {
        if (!component.itemToEdit || !component.itemToEdit.id_gasto_pk) return;
        const nombreTrim = String(component.itemToEdit.nombre || "").trim();
        const descripcionTrim = String(
            component.itemToEdit.descripcion || ""
        ).trim();
        if (!nombreTrim) {
            window.showToast &&
                window.showToast("El nombre del gasto es obligatorio", "error");
            return;
        }
        try {
            const payload = {
                nombre: nombreTrim,
                fecha: component.itemToEdit.fecha,
                monto: component.itemToEdit.monto,
                descripcion: descripcionTrim,
                id_proyecto_fk: component.itemToEdit.id_proyecto_fk,
                id_categoria_fk: component.itemToEdit.id_categoria_fk,
            };
            const response = await fetch(
                `/api/gastos/${component.itemToEdit.id_gasto_pk}`,
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
                            "Error al actualizar el gasto",
                            "error"
                        );
                }
                throw data;
            }
            window.showToast &&
                window.showToast("Gasto actualizado exitosamente", "success");
            component.isGastoEditModalOpen = false;
            component.itemToEdit = null;
            await this.fetchGastos(component);
        } catch (error) {
            console.error("Error updating gasto:", error);
        }
    },

    /**
     * Deletes a gasto via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async deleteGasto(component) {
        if (!component.itemToDelete || !component.itemToDelete.id_gasto_pk)
            return;
        try {
            const response = await fetch(
                `/api/gastos/${component.itemToDelete.id_gasto_pk}`,
                {
                    method: "DELETE",
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                }
            );
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast("Gasto eliminado exitosamente", "success");
            component.isGastoDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchGastos(component);
        } catch (error) {
            console.error("Error deleting gasto:", error);
            const errorMessage = error?.error || "Error al eliminar el gasto";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },
};

window.catalogosApiHandlers = {
    /**
     * Fetches estados de proyecto from the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async fetchEstadosProyecto(component) {
        try {
            const response = await fetch("/api/estados-proyecto", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            component.catalogoEstadosProyecto = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching estados proyecto:", error);
        }
    },

    /**
     * Fetches ordenes de servicio from the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async fetchOrdenesServicio(component) {
        try {
            const response = await fetch("/api/ordenes-servicio", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            component.catalogoOrdenesServicio = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching ordenes servicio:", error);
        }
    },

    /**
     * Fetches proyectos from the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async fetchProyectos(component) {
        try {
            const response = await fetch("/api/proyectos", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            component.catalogoProyectos = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching proyectos:", error);
        }
    },

    /**
     * Fetches categorias de ingreso from the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async fetchCategoriasIngreso(component) {
        try {
            const response = await fetch("/api/categorias-ingreso", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            component.catalogoCategoriasIngreso = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching categorias ingreso:", error);
        }
    },

    /**
     * Fetches categorias de gasto from the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async fetchCategoriasGasto(component) {
        try {
            const response = await fetch("/api/categorias-gasto", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            component.catalogoCategoriasGasto = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching categorias gasto:", error);
        }
    },
};
