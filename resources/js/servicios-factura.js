document.addEventListener("alpine:init", () => {
    Alpine.data("serviciosCrud", () => ({
        isServicioModalOpen: false,
        isEditServicioModalOpen: false,
        isDeleteServicioModalOpen: false,
        itemToEdit: {
            id_servicio_pk: null,
            nombre_servicio: "",
            tarifa: 0,
        },
        itemToDelete: null,
        servicios: [],
        categorias: [],
        numbers: [],
        loadingServicios: false,
        nombre_servicio: "",
        tarifa: "",
        filtroServicio: "",
        ordenarPor: "nombre_servicio",
        currentPage: 1,
        perPage: 10,

        async init() {
            await this.fetchServicios();
            this.$watch("filtroServicio", () => {
                this.currentPage = 1;
            });
            this.$watch("ordenarPor", () => {
                this.currentPage = 1;
            });
        },

        paginatedServicios() {
            return this.filteredServicios.slice(
                (this.currentPage - 1) * this.perPage,
                this.currentPage * this.perPage
            );
        },

        totalPages() {
            return Math.ceil(this.filteredServicios.length / this.perPage);
        },

        nextPage() {
            if (this.currentPage < this.totalPages()) {
                this.currentPage++;
            }
        },

        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },

        goToPage(page) {
            this.currentPage = page;
        },

        get filteredServicios() {
            const term = (this.filtroServicio || "")
                .toString()
                .toLowerCase()
                .trim();
            let list = this.servicios.filter((s) => {
                if (!term) return true;
                const nombre = (s.nombre_servicio || "")
                    .toString()
                    .toLowerCase();
                const tarifaStr = (
                    s.tarifa != null ? String(s.tarifa) : ""
                ).toLowerCase();
                return nombre.includes(term) || tarifaStr.includes(term);
            });

            const key = this.ordenarPor || "nombre_servicio";
            list = list.sort((a, b) => {
                if (key === "tarifa") {
                    const an = Number(a.tarifa ?? 0);
                    const bn = Number(b.tarifa ?? 0);
                    return an - bn;
                }
                const av = (a[key] ?? "").toString().toLowerCase();
                const bv = (b[key] ?? "").toString().toLowerCase();
                return av.localeCompare(bv);
            });

            return list;
        },

        async fetchServicios() {
            this.loadingServicios = true;
            try {
                const response = await fetch("/api/servicios", {
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw data;

                this.servicios = Array.isArray(data?.data)
                    ? data.data
                    : Array.isArray(data)
                    ? data
                    : [];

                this.categorias = this.servicios;
                this.numbers = this.servicios;
            } catch (error) {
                console.error("Error fetching servicios:", error);
                window.showToast &&
                    window.showToast("Error al cargar servicios", "error");
            } finally {
                this.loadingServicios = false;
            }
        },

        async submitServicio() {
            const nombreTrim = String(this.nombre_servicio || "").trim();
            const tarifa = parseFloat(this.tarifa || 0);

            if (!nombreTrim) {
                window.showToast &&
                    window.showToast(
                        "El nombre del servicio es obligatorio",
                        "error"
                    );
                return;
            }

            if (tarifa < 0) {
                window.showToast &&
                    window.showToast(
                        "La tarifa no puede ser negativa",
                        "error"
                    );
                return;
            }

            if (
                this.servicios.some(
                    (s) =>
                        s.nombre_servicio.toLowerCase() ===
                        nombreTrim.toLowerCase()
                )
            ) {
                window.showToast &&
                    window.showToast("El servicio ya existe", "error");
                return;
            }

            try {
                const payload = {
                    nombre_servicio: nombreTrim,
                    tarifa: tarifa,
                };
                const response = await fetch("/api/servicios", {
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
                    window.showToast("Servicio creado exitosamente", "success");
                this.nombre_servicio = "";
                this.tarifa = "";
                this.isServicioModalOpen = false;
                await this.fetchServicios();
                this.categorias = this.servicios;
                this.numbers = this.servicios;
                this.currentPage = 1;
            } catch (error) {
                console.error("Error creating servicio:", error);
                window.showToast &&
                    window.showToast("Error al crear el servicio", "error");
            }
        },

        async updateServicio() {
            if (!this.itemToEdit || !this.itemToEdit.id_servicio_pk) return;
            const nombreTrim = String(
                this.itemToEdit.nombre_servicio || ""
            ).trim();
            const tarifa = parseFloat(this.itemToEdit.tarifa || 0);

            if (!nombreTrim) {
                window.showToast &&
                    window.showToast(
                        "El nombre del servicio es obligatorio",
                        "error"
                    );
                return;
            }

            if (tarifa < 0) {
                window.showToast &&
                    window.showToast(
                        "La tarifa no puede ser negativa",
                        "error"
                    );
                return;
            }

            if (
                this.servicios.some(
                    (s) =>
                        s.nombre_servicio.toLowerCase() ===
                            nombreTrim.toLowerCase() &&
                        s.id_servicio_pk !== this.itemToEdit.id_servicio_pk
                )
            ) {
                window.showToast &&
                    window.showToast(
                        "Ya existe otro servicio con ese nombre",
                        "error"
                    );
                return;
            }

            try {
                const payload = {
                    nombre_servicio: nombreTrim,
                    tarifa: tarifa,
                };
                const response = await fetch(
                    `/api/servicios/${this.itemToEdit.id_servicio_pk}`,
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
                                "Error al actualizar el servicio",
                                "error"
                            );
                    }
                    throw data;
                }
                window.showToast &&
                    window.showToast(
                        "Servicio actualizado exitosamente",
                        "success"
                    );
                this.isEditServicioModalOpen = false;
                this.itemToEdit = {
                    id_servicio_pk: null,
                    nombre_servicio: "",
                    tarifa: 0,
                };
                await this.fetchServicios();
                this.categorias = this.servicios;
                this.numbers = this.servicios;
            } catch (error) {
                console.error("Error updating servicio:", error);
            }
        },

        async deleteServicio() {
            if (!this.itemToDelete || !this.itemToDelete.id_servicio_pk) return;
            try {
                const response = await fetch(
                    `/api/servicios/${this.itemToDelete.id_servicio_pk}`,
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
                        "Servicio eliminado exitosamente",
                        "success"
                    );
                this.isDeleteServicioModalOpen = false;
                this.itemToDelete = null;
                await this.fetchServicios();
                this.categorias = this.servicios;
                this.numbers = this.servicios;
            } catch (error) {
                console.error("Error deleting servicio:", error);
                const errorMessage =
                    error?.error || "Error al eliminar el servicio";
                window.showToast && window.showToast(errorMessage, "error");
            }
        },

        handleModalSubmit(event) {
            if (event.detail.formId === "formServicio") this.submitServicio();
            if (event.detail.formId === "formEditServicio")
                this.updateServicio();
        },

        handleDelete() {
            if (this.isDeleteServicioModalOpen) {
                this.deleteServicio();
            }
        },
    }));
});
