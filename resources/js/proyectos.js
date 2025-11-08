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
            const params = new URLSearchParams();
            if (component.filtroProyecto)
                params.set("q", component.filtroProyecto);
            if (component.ordenarPorProyecto)
                params.set("sort", component.ordenarPorProyecto);
            const response = await fetch(
                `/api/proyectos?${params.toString()}`,
                {
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                }
            );
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
            await window.catalogosApiHandlers.fetchProyectos(component);
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
            await window.catalogosApiHandlers.fetchProyectos(component);
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
            await window.catalogosApiHandlers.fetchProyectos(component);
        } catch (error) {
            console.error("Error deleting proyecto:", error);
            const errorMessage =
                error?.error || "Error al eliminar el proyecto";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },
};

window.VistaProyectosData = function (initial = {}) {
    return {
        proyectos: Array.isArray(initial.proyectos) ? initial.proyectos : [],
        currentProyectoIndex: 0,
        loading: false,

        ingresosProyecto: [],
        gastosProyecto: [],
        loadingMovimientos: false,
        lastLoadedProjectId: null,

        showProjectListModal: false,
        searchQuery: "",
        filterEstado: "todos",
        filterBalance: "todos",
        sortBy: "nombre",

        SESSION_KEY: "vista_proyectos_selected_id",
        selectedProjectId: null,

        async init() {
            try {
                const saved = sessionStorage.getItem(this.SESSION_KEY);
                if (saved) this.selectedProjectId = String(saved);
            } catch (e) {
                console.warn("No se pudo leer sessionStorage", e);
            }

            if (!this.proyectos || this.proyectos.length === 0) {
                await this.fetchProyectos();
            }

            if (this.proyectos.length > 0) {
                if (this.selectedProjectId) {
                    const idx = this.proyectos.findIndex(
                        (p) =>
                            String(p.id_proyecto_pk) ===
                            String(this.selectedProjectId)
                    );
                    if (idx !== -1) {
                        this.currentProyectoIndex = idx;
                    } else {
                        this.selectedProjectId = null;
                        try {
                            sessionStorage.removeItem(this.SESSION_KEY);
                        } catch (e) {}
                        this.currentProyectoIndex = 0;
                    }
                } else {
                    if (this.currentProyectoIndex === -1)
                        this.currentProyectoIndex = 0;
                }
                await this.loadMovimientosForCurrent();
            }
        },

        async fetchProyectos() {
            this.loading = true;
            try {
                const response = await fetch("/api/proyectos", {
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw data;
                this.proyectos = Array.isArray(data?.data) ? data.data : [];
                if (this.proyectos.length === 0) this.currentProyectoIndex = -1;
            } catch (error) {
                console.error("Error fetching proyectos:", error);
                window.showToast &&
                    window.showToast("Error al cargar proyectos", "error");
            } finally {
                this.loading = false;
            }
        },

        get currentProyecto() {
            return this.proyectos[this.currentProyectoIndex] || null;
        },

        async previousProyecto() {
            if (this.proyectos.length === 0) return;
            this.ingresosProyecto = [];
            this.gastosProyecto = [];
            this.currentProyectoIndex =
                this.currentProyectoIndex > 0
                    ? this.currentProyectoIndex - 1
                    : this.proyectos.length - 1;
            this.saveSelectedProject(this.currentProyecto);
            await this.loadMovimientosForCurrent();
        },

        async nextProyecto() {
            if (this.proyectos.length === 0) return;
            this.ingresosProyecto = [];
            this.gastosProyecto = [];
            this.currentProyectoIndex =
                this.currentProyectoIndex < this.proyectos.length - 1
                    ? this.currentProyectoIndex + 1
                    : 0;
            this.saveSelectedProject(this.currentProyecto);
            await this.loadMovimientosForCurrent();
        },

        async loadMovimientosForCurrent() {
            const proyecto = this.currentProyecto;
            if (!proyecto || !proyecto.id_proyecto_pk) {
                this.ingresosProyecto = [];
                this.gastosProyecto = [];
                this.lastLoadedProjectId = null;
                return;
            }

            const proyectoId = String(proyecto.id_proyecto_pk);
            if (
                this.lastLoadedProjectId &&
                this.lastLoadedProjectId === proyectoId
            )
                return;

            this.loadingMovimientos = true;
            this.ingresosProyecto = [];
            this.gastosProyecto = [];
            try {
                const respI = await fetch(
                    `/api/ingresos?proyecto=${proyectoId}`,
                    {
                        headers: { Accept: "application/json" },
                        credentials: "same-origin",
                    }
                );
                const dataI = await respI.json().catch(() => ({}));
                let rawIngresos =
                    respI.ok && Array.isArray(dataI?.data) ? dataI.data : [];
                const filteredIngresos = rawIngresos.filter((i) => {
                    const candidates = [
                        i.id_proyecto_fk,
                        i.id_proyecto_pk,
                        i.id_proyecto,
                        i.proyecto && i.proyecto.id_proyecto_pk,
                        i.proyecto && i.proyecto.id_proyecto_fk,
                        i.proyecto && i.proyecto.id_proyecto,
                        i.proyecto && i.proyecto.id,
                        i.proyecto && i.proyecto.id_proyecto,
                    ];
                    return candidates.some(
                        (c) =>
                            c !== undefined &&
                            c !== null &&
                            String(c) === proyectoId
                    );
                });
                this.ingresosProyecto = filteredIngresos;

                const respG = await fetch(
                    `/api/gastos?proyecto=${proyectoId}`,
                    {
                        headers: { Accept: "application/json" },
                        credentials: "same-origin",
                    }
                );
                const dataG = await respG.json().catch(() => ({}));
                let rawGastos =
                    respG.ok && Array.isArray(dataG?.data) ? dataG.data : [];
                const filteredGastos = rawGastos.filter((g) => {
                    const candidates = [
                        g.id_proyecto_fk,
                        g.id_proyecto_pk,
                        g.id_proyecto,
                        g.proyecto && g.proyecto.id_proyecto_pk,
                        g.proyecto && g.proyecto.id_proyecto_fk,
                        g.proyecto && g.proyecto.id_proyecto,
                        g.proyecto && g.proyecto.id,
                        g.proyecto && g.proyecto.id_proyecto,
                    ];
                    return candidates.some(
                        (c) =>
                            c !== undefined &&
                            c !== null &&
                            String(c) === proyectoId
                    );
                });
                this.gastosProyecto = filteredGastos;

                try {
                    const ingresosTotal = this.ingresosProyecto.reduce(
                        (s, it) => {
                            const monto =
                                parseFloat(it.monto_ingreso ?? it.monto ?? 0) ||
                                0;
                            return s + monto;
                        },
                        0
                    );
                    const gastosTotal = this.gastosProyecto.reduce((s, it) => {
                        const monto =
                            parseFloat(it.monto ?? it.monto_ingreso ?? 0) || 0;
                        return s + monto;
                    }, 0);
                    proyecto.total_ingresos = ingresosTotal;
                    proyecto.total_gastos = gastosTotal;
                } catch (errTotals) {
                    console.warn("Error calculando totales:", errTotals);
                }

                this.lastLoadedProjectId = proyectoId;
            } catch (e) {
                console.error("Error cargando movimientos del proyecto:", e);
                window.showToast &&
                    window.showToast("Error al cargar movimientos", "error");
                this.ingresosProyecto = [];
                this.gastosProyecto = [];
                this.lastLoadedProjectId = null;
            } finally {
                this.loadingMovimientos = false;
            }
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat("es-HN", {
                style: "currency",
                currency: "HNL",
            }).format(amount || 0);
        },

        formatDate(date) {
            if (!date) return "N/A";
            try {
                const d = new Date(date);
                if (isNaN(d.getTime())) return date;
                return d.toLocaleDateString("es-ES", {
                    year: "numeric",
                    month: "long",
                    day: "numeric",
                });
            } catch (e) {
                return date;
            }
        },

        openProjectListModal() {
            this.showProjectListModal = true;
        },
        closeProjectListModal() {
            this.showProjectListModal = false;
        },

        async selectProyecto(index) {
            if (index >= 0 && index < this.proyectos.length) {
                this.ingresosProyecto = [];
                this.gastosProyecto = [];
                this.currentProyectoIndex = index;
                this.saveSelectedProject(this.currentProyecto);
                this.closeProjectListModal();
                await this.loadMovimientosForCurrent();
            }
        },

        combinedMovimientos() {
            const items = [];
            try {
                this.ingresosProyecto.forEach((i) => {
                    const fecha =
                        i.fecha_ingreso ||
                        i.created_at ||
                        i.fecha ||
                        i.createdAt ||
                        null;
                    items.push(
                        Object.assign({}, i, {
                            __tipo: "ingreso",
                            __fecha: fecha,
                        })
                    );
                });
                this.gastosProyecto.forEach((g) => {
                    const fecha =
                        g.fecha ||
                        g.created_at ||
                        g.fecha_gasto ||
                        g.createdAt ||
                        null;
                    items.push(
                        Object.assign({}, g, {
                            __tipo: "gasto",
                            __fecha: fecha,
                        })
                    );
                });
                items.sort((a, b) => {
                    const da = a.__fecha ? new Date(a.__fecha) : null;
                    const db = b.__fecha ? new Date(b.__fecha) : null;
                    if (da === null && db === null) return 0;
                    if (da === null) return 1;
                    if (db === null) return -1;
                    return db - da;
                });
            } catch (e) {
                console.error("Error combinando movimientos:", e);
            }
            return items;
        },

        filteredProyectos() {
            let filtered = [...this.proyectos];
            if (this.searchQuery.trim()) {
                const query = this.searchQuery.toLowerCase().trim();
                filtered = filtered.filter((p) => {
                    const nombre = (p.nombre_proyecto || "").toLowerCase();
                    const desc = (
                        p.descripcion_proyecto ||
                        p.descripcion ||
                        ""
                    ).toLowerCase();
                    return nombre.includes(query) || desc.includes(query);
                });
            }

            if (this.filterEstado !== "todos") {
                filtered = filtered.filter((p) => {
                    const balance =
                        (p.total_ingresos || 0) - (p.total_gastos || 0);
                    const tieneMovimientos =
                        p.total_ingresos > 0 || p.total_gastos > 0;
                    if (this.filterEstado === "activos") {
                        return balance > 0 || !tieneMovimientos;
                    } else if (this.filterEstado === "completados") {
                        return balance === 0 && tieneMovimientos;
                    } else if (this.filterEstado === "deficit") {
                        return balance < 0;
                    }
                    return true;
                });
            }

            if (this.filterBalance !== "todos") {
                filtered = filtered.filter((p) => {
                    const balance =
                        (p.total_ingresos || 0) - (p.total_gastos || 0);
                    if (this.filterBalance === "positivo") return balance > 0;
                    if (this.filterBalance === "negativo") return balance < 0;
                    if (this.filterBalance === "cero") return balance === 0;
                    return true;
                });
            }

            filtered.sort((a, b) => {
                if (this.sortBy === "nombre")
                    return (a.nombre_proyecto || "").localeCompare(
                        b.nombre_proyecto || ""
                    );
                if (this.sortBy === "fecha") {
                    const fechaA = new Date(
                        a.created_at || a.fecha_creacion || 0
                    );
                    const fechaB = new Date(
                        b.created_at || b.fecha_creacion || 0
                    );
                    return fechaB - fechaA;
                }
                if (this.sortBy === "balance") {
                    const balanceA =
                        (a.total_ingresos || 0) - (a.total_gastos || 0);
                    const balanceB =
                        (b.total_ingresos || 0) - (b.total_gastos || 0);
                    return balanceB - balanceA;
                }
                if (this.sortBy === "ingresos")
                    return (b.total_ingresos || 0) - (a.total_ingresos || 0);
                if (this.sortBy === "gastos")
                    return (b.total_gastos || 0) - (a.total_gastos || 0);
                return 0;
            });

            return filtered;
        },

        clearFilters() {
            this.searchQuery = "";
            this.filterEstado = "todos";
            this.filterBalance = "todos";
            this.sortBy = "nombre";
        },

        scrollToCurrentInModal() {
            const proyecto = this.currentProyecto;
            if (!proyecto) {
                window.showToast &&
                    window.showToast("No hay proyecto seleccionado", "info");
                return;
            }
            if (!this.showProjectListModal) {
                this.openProjectListModal();
                if (this.$nextTick)
                    return this.$nextTick(() => this.scrollToCurrentInModal());
                setTimeout(() => this.scrollToCurrentInModal(), 150);
                return;
            }
            const id =
                proyecto.id_proyecto_pk !== undefined &&
                proyecto.id_proyecto_pk !== null
                    ? proyecto.id_proyecto_pk
                    : this.currentProyectoIndex;
            const el = document.getElementById("proj_" + id);
            if (!el) {
                window.showToast &&
                    window.showToast(
                        "El proyecto actual no coincide con los filtros o no está visible",
                        "info"
                    );
                return;
            }
            try {
                el.scrollIntoView({ behavior: "smooth", block: "center" });
                el.classList.add("transition-all");
                setTimeout(() => el.classList.remove("transition-all"), 1500);
            } catch (e) {
                console.warn("scrollToCurrentInModal error", e);
            }
        },

        saveSelectedProject(proyecto) {
            try {
                if (
                    proyecto &&
                    proyecto.id_proyecto_pk !== undefined &&
                    proyecto.id_proyecto_pk !== null
                ) {
                    const id = String(proyecto.id_proyecto_pk);
                    sessionStorage.setItem(this.SESSION_KEY, id);
                    this.selectedProjectId = id;
                } else {
                    sessionStorage.removeItem(this.SESSION_KEY);
                    this.selectedProjectId = null;
                }
            } catch (e) {
                console.warn("No se pudo escribir en sessionStorage", e);
            }
        },
    };
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
            const params = new URLSearchParams();
            if (component.filtroIngreso)
                params.set("q", component.filtroIngreso);
            if (component.ordenarPorIngreso)
                params.set("sort", component.ordenarPorIngreso);
            const response = await fetch(`/api/ingresos?${params.toString()}`, {
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
            const params = new URLSearchParams();
            if (component.filtroGasto) params.set("q", component.filtroGasto);
            if (component.ordenarPorGasto)
                params.set("sort", component.ordenarPorGasto);
            const response = await fetch(`/api/gastos?${params.toString()}`, {
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
