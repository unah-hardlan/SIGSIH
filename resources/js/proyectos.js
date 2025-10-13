window.proyectosApiHandlers = {
    authHeaders() {
        return {
            "Content-Type": "application/json",
            Accept: "application/json",
        };
    },

    async fetchProyectos(component) {
        component.loadingProyectos = true;
        try {
            const response = await fetch("/api/proyectos", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            component.proyectos = Array.isArray(data?.data) ? data.data : [];
        } catch (error) {
            console.error("Error fetching proyectos:", error);
            window.showToast &&
                window.showToast("Error al cargar proyectos", "error");
        } finally {
            component.loadingProyectos = false;
        }
    },

    async submitProyecto(component) {
        const nombreTrim = String(component.nombre_proyecto || "").trim();
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
                fecha_inicio_proyecto: component.fecha_inicio_proyecto,
                fecha_estimada_fin_proyecto:
                    component.fecha_estimada_fin_proyecto,
                fecha_finalizacion_proyecto:
                    component.fecha_finalizacion_proyecto || null,
                descripcion_proyecto: String(
                    component.descripcion_proyecto || ""
                ).trim(),
                id_orden_servicio_fk: component.id_orden_servicio_fk || null,
                id_estado_proyecto_fk: component.id_estado_proyecto_fk || null,
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
            component.nombre_proyecto = "";
            component.fecha_inicio_proyecto = "";
            component.fecha_estimada_fin_proyecto = "";
            component.fecha_finalizacion_proyecto = "";
            component.descripcion_proyecto = "";
            component.id_orden_servicio_fk = "";
            component.id_estado_proyecto_fk = "";
            component.isProyectoModalOpen = false;
            await this.fetchProyectos(component);
        } catch (error) {
            console.error("Error creating proyecto:", error);
            window.showToast &&
                window.showToast("Error al crear el proyecto", "error");
        }
    },

    async updateProyecto(component) {
        if (!component.itemToEdit || !component.itemToEdit.id_proyecto_pk)
            return;
        const nombreTrim = String(
            component.itemToEdit.nombre_proyecto || ""
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
                    component.itemToEdit.fecha_finalizacion_proyecto || null,
                descripcion_proyecto: String(
                    component.itemToEdit.descripcion_proyecto || ""
                ).trim(),
                id_orden_servicio_fk:
                    component.itemToEdit.id_orden_servicio_fk || null,
                id_estado_proyecto_fk:
                    component.itemToEdit.id_estado_proyecto_fk || null,
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
    authHeaders() {
        return {
            "Content-Type": "application/json",
            Accept: "application/json",
        };
    },

    async fetchIngresos(component) {
        component.loadingIngresos = true;
        try {
            const response = await fetch("/api/ingresos", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            component.ingresos = Array.isArray(data?.data) ? data.data : [];
        } catch (error) {
            console.error("Error fetching ingresos:", error);
            window.showToast &&
                window.showToast("Error al cargar ingresos", "error");
        } finally {
            component.loadingIngresos = false;
        }
    },

    async submitIngreso(component) {
        const nombreTrim = String(component.nombre_ingreso || "").trim();
        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del ingreso es obligatorio",
                    "error"
                );
            return;
        }
        if (!component.id_proyecto_fk_ingreso) {
            window.showToast &&
                window.showToast("El proyecto es obligatorio", "error");
            return;
        }
        if (!component.id_categoria_fk_ingreso) {
            window.showToast &&
                window.showToast("La categoría es obligatoria", "error");
            return;
        }
        try {
            const payload = {
                nombre_ingreso: nombreTrim,
                fecha_ingreso: component.fecha_ingreso,
                monto_ingreso: parseFloat(component.monto_ingreso) || 0,
                descripcion_ingreso: String(
                    component.descripcion_ingreso || ""
                ).trim(),
                id_proyecto_fk: component.id_proyecto_fk_ingreso,
                id_categoria_fk: component.id_categoria_fk_ingreso,
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
                window.showToast("Ingreso creado exitosamente", "success");
            component.nombre_ingreso = "";
            component.fecha_ingreso = "";
            component.monto_ingreso = "";
            component.descripcion_ingreso = "";
            component.id_proyecto_fk_ingreso = "";
            component.id_categoria_fk_ingreso = "";
            component.isIngresoModalOpen = false;
            await this.fetchIngresos(component);
        } catch (error) {
            console.error("Error creating ingreso:", error);
            window.showToast &&
                window.showToast("Error al crear el ingreso", "error");
        }
    },

    async updateIngreso(component) {
        if (!component.ingresoToEdit || !component.ingresoToEdit.id_ingresos_pk)
            return;
        const nombreTrim = String(
            component.ingresoToEdit.nombre_ingreso || ""
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
                nombre_ingreso: nombreTrim,
                fecha_ingreso: component.ingresoToEdit.fecha_ingreso,
                monto_ingreso:
                    parseFloat(component.ingresoToEdit.monto_ingreso) || 0,
                descripcion_ingreso: String(
                    component.ingresoToEdit.descripcion_ingreso || ""
                ).trim(),
                id_proyecto_fk: component.ingresoToEdit.id_proyecto_fk,
                id_categoria_fk: component.ingresoToEdit.id_categoria_fk,
            };
            const response = await fetch(
                `/api/ingresos/${component.ingresoToEdit.id_ingresos_pk}`,
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
                            "Error al actualizar el ingreso",
                            "error"
                        );
                }
                throw data;
            }
            window.showToast &&
                window.showToast("Ingreso actualizado exitosamente", "success");
            component.isIngresoEditModalOpen = false;
            component.ingresoToEdit = null;
            await this.fetchIngresos(component);
        } catch (error) {
            console.error("Error updating ingreso:", error);
        }
    },

    async deleteIngreso(component) {
        if (
            !component.ingresoToDelete ||
            !component.ingresoToDelete.id_ingresos_pk
        )
            return;
        try {
            const response = await fetch(
                `/api/ingresos/${component.ingresoToDelete.id_ingresos_pk}`,
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
            component.ingresoToDelete = null;
            await this.fetchIngresos(component);
        } catch (error) {
            console.error("Error deleting ingreso:", error);
            const errorMessage = error?.error || "Error al eliminar el ingreso";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },
};

window.gastosApiHandlers = {
    authHeaders() {
        return {
            "Content-Type": "application/json",
            Accept: "application/json",
        };
    },

    async fetchGastos(component) {
        component.loadingGastos = true;
        try {
            const response = await fetch("/api/gastos", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            component.gastos = Array.isArray(data?.data) ? data.data : [];
        } catch (error) {
            console.error("Error fetching gastos:", error);
            window.showToast &&
                window.showToast("Error al cargar gastos", "error");
        } finally {
            component.loadingGastos = false;
        }
    },

    async submitGasto(component) {
        const nombreTrim = String(component.nombre_gasto || "").trim();
        if (!nombreTrim) {
            window.showToast &&
                window.showToast("El nombre del gasto es obligatorio", "error");
            return;
        }
        if (!component.id_proyecto_fk_gasto) {
            window.showToast &&
                window.showToast("El proyecto es obligatorio", "error");
            return;
        }
        if (!component.id_categoria_fk_gasto) {
            window.showToast &&
                window.showToast("La categoría es obligatoria", "error");
            return;
        }
        try {
            const payload = {
                nombre: nombreTrim,
                fecha: component.fecha_gasto,
                monto: parseFloat(component.monto_gasto) || 0,
                descripcion: String(component.descripcion_gasto || "").trim(),
                id_proyecto_fk: component.id_proyecto_fk_gasto,
                id_categoria_fk: component.id_categoria_fk_gasto,
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
                window.showToast("Gasto creado exitosamente", "success");
            component.nombre_gasto = "";
            component.fecha_gasto = "";
            component.monto_gasto = "";
            component.descripcion_gasto = "";
            component.id_proyecto_fk_gasto = "";
            component.id_categoria_fk_gasto = "";
            component.isGastoModalOpen = false;
            await this.fetchGastos(component);
        } catch (error) {
            console.error("Error creating gasto:", error);
            window.showToast &&
                window.showToast("Error al crear el gasto", "error");
        }
    },

    async updateGasto(component) {
        if (!component.gastoToEdit || !component.gastoToEdit.id_gasto_pk)
            return;
        const nombreTrim = String(component.gastoToEdit.nombre || "").trim();
        if (!nombreTrim) {
            window.showToast &&
                window.showToast("El nombre del gasto es obligatorio", "error");
            return;
        }
        try {
            const payload = {
                nombre: nombreTrim,
                fecha: component.gastoToEdit.fecha,
                monto: parseFloat(component.gastoToEdit.monto) || 0,
                descripcion: String(
                    component.gastoToEdit.descripcion || ""
                ).trim(),
                id_proyecto_fk: component.gastoToEdit.id_proyecto_fk,
                id_categoria_fk: component.gastoToEdit.id_categoria_fk,
            };
            const response = await fetch(
                `/api/gastos/${component.gastoToEdit.id_gasto_pk}`,
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
                            "Error al actualizar el gasto",
                            "error"
                        );
                }
                throw data;
            }
            window.showToast &&
                window.showToast("Gasto actualizado exitosamente", "success");
            component.isGastoEditModalOpen = false;
            component.gastoToEdit = null;
            await this.fetchGastos(component);
        } catch (error) {
            console.error("Error updating gasto:", error);
        }
    },

    async deleteGasto(component) {
        if (!component.gastoToDelete || !component.gastoToDelete.id_gasto_pk)
            return;
        try {
            const response = await fetch(
                `/api/gastos/${component.gastoToDelete.id_gasto_pk}`,
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
            component.gastoToDelete = null;
            await this.fetchGastos(component);
        } catch (error) {
            console.error("Error deleting gasto:", error);
            const errorMessage = error?.error || "Error al eliminar el gasto";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },
};

window.catalogosApiHandlers = {
    authHeaders() {
        return {
            "Content-Type": "application/json",
            Accept: "application/json",
        };
    },

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
                : [];
        } catch (error) {
            console.error("Error fetching estados proyecto:", error);
        }
    },

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
                : [];
        } catch (error) {
            console.error("Error fetching ordenes servicio:", error);
        }
    },

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
                : [];
        } catch (error) {
            console.error("Error fetching proyectos:", error);
        }
    },

    async fetchCategorias(component) {
        try {
            const response = await fetch("/api/categorias", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            component.catalogoCategorias = Array.isArray(data?.data)
                ? data.data
                : [];
        } catch (error) {
            console.error("Error fetching categorias:", error);
        }
    },
};
