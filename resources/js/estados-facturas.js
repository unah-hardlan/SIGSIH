document.addEventListener("alpine:init", () => {
    Alpine.data("estadosFacturaCrud", () => ({
        isEstadoFacturaModalOpen: false,
        isEditEstadoFacturaModalOpen: false,
        isDeleteEstadoFacturaModalOpen: false,
        itemToEdit: {
            id_estado_factura_pk: null,
            codigo: "",
            nombre: "",
            descripcion: "",
            es_final: false,
            orden: 0,
        },
        itemToDelete: null,
        estadosFactura: [],
        categorias: [],
        numbers: [],
        loadingEstadosFactura: false,
        nombre: "",
        descripcion: "",
        codigo: "",
        es_final: false,
        orden: 0,
        filtroEstadoFactura: "",
        ordenarPor: "nombre",
        currentPage: 1,
        perPage: 10,

        async init() {
            await this.fetchEstadosFactura();
            this.$watch("filtroEstadoFactura", () => {
                this.currentPage = 1;
            });
            this.$watch("ordenarPor", () => {
                this.currentPage = 1;
            });
        },

        paginatedEstadosFactura() {
            return this.filteredEstadosFactura.slice(
                (this.currentPage - 1) * this.perPage,
                this.currentPage * this.perPage
            );
        },

        totalPages() {
            return Math.ceil(this.filteredEstadosFactura.length / this.perPage);
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

        get filteredEstadosFactura() {
            const term = String(this.filtroEstadoFactura || "")
                .toLowerCase()
                .trim();
            let list = Array.from(this.estadosFactura || []);
            if (term) {
                list = list.filter((ef) => {
                    const codigo = String(ef?.codigo || "").toLowerCase();
                    const nombre = String(
                        ef?.nombre_estado || ""
                    ).toLowerCase();
                    const desc = String(
                        ef?.descripcion_estado_factura || ""
                    ).toLowerCase();
                    const orden = String(ef?.orden ?? "").toLowerCase();
                    return (
                        codigo.includes(term) ||
                        nombre.includes(term) ||
                        desc.includes(term) ||
                        orden.includes(term)
                    );
                });
            }
            const key = this.ordenarPor || "nombre";
            const collator = new Intl.Collator("es", {
                sensitivity: "base",
                numeric: true,
            });
            const getVal = (ef) => {
                if (key === "codigo") return String(ef?.codigo || "");
                if (key === "orden") return Number(ef?.orden) || 0;
                return String(ef?.nombre_estado || "");
            };
            list.sort((a, b) => {
                const va = getVal(a);
                const vb = getVal(b);
                if (key === "orden") return va - vb;
                return collator.compare(va, vb);
            });
            return list;
        },

        async fetchEstadosFactura() {
            this.loadingEstadosFactura = true;
            try {
                const response = await fetch("/api/estados-factura", {
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw data;

                this.estadosFactura = Array.isArray(data?.data)
                    ? data.data
                    : Array.isArray(data)
                    ? data
                    : [];

                this.categorias = this.estadosFactura;
                this.numbers = this.estadosFactura;
            } catch (error) {
                console.error("Error fetching estados factura:", error);
                window.showToast &&
                    window.showToast(
                        "Error al cargar estados de factura",
                        "error"
                    );
            } finally {
                this.loadingEstadosFactura = false;
            }
        },

        async submitEstadoFactura() {
            const nombreTrim = String(this.nombre || "").trim();
            const descripcionTrim = String(this.descripcion || "").trim();
            const codigoTrim = String(this.codigo || "").trim();

            if (!nombreTrim) {
                window.showToast &&
                    window.showToast(
                        "El nombre del estado es obligatorio",
                        "error"
                    );
                return;
            }

            if (
                this.estadosFactura.some(
                    (ef) =>
                        String(ef?.nombre_estado || "").toLowerCase() ===
                        nombreTrim.toLowerCase()
                )
            ) {
                window.showToast &&
                    window.showToast("El estado de factura ya existe", "error");
                return;
            }

            try {
                const payload = {
                    nombre: nombreTrim,
                    descripcion: descripcionTrim,
                    codigo: codigoTrim,
                    es_final: this.es_final || false,
                    orden: parseInt(this.orden) || 0,
                };
                const response = await fetch("/api/estados-factura", {
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
                        "Estado de factura creado exitosamente",
                        "success"
                    );
                this.nombre = "";
                this.descripcion = "";
                this.codigo = "";
                this.es_final = false;
                this.orden = 0;
                this.isEstadoFacturaModalOpen = false;
                await this.fetchEstadosFactura();
                this.categorias = this.estadosFactura;
                this.numbers = this.estadosFactura;
                this.currentPage = 1;
            } catch (error) {
                console.error("Error creating estado factura:", error);
                window.showToast &&
                    window.showToast(
                        "Error al crear el estado de factura",
                        "error"
                    );
            }
        },

        async updateEstadoFactura() {
            if (!this.itemToEdit || !this.itemToEdit.id_estado_factura_pk)
                return;

            const nombreTrim = String(
                document.getElementById("edit_nombre")?.value || ""
            ).trim();
            const descripcionTrim = String(
                document.getElementById("edit_descripcion")?.value || ""
            ).trim();
            const codigoTrim = String(
                document.getElementById("edit_codigo")?.value || ""
            ).trim();
            const esFinal =
                document.getElementById("edit_es_final")?.checked || false;
            const orden =
                parseInt(document.getElementById("edit_orden")?.value) || 0;

            if (!nombreTrim) {
                window.showToast &&
                    window.showToast(
                        "El nombre del estado es obligatorio",
                        "error"
                    );
                return;
            }

            if (
                this.estadosFactura.some(
                    (ef) =>
                        String(ef?.nombre_estado || "").toLowerCase() ===
                            nombreTrim.toLowerCase() &&
                        ef.id_estado_factura_pk !==
                            this.itemToEdit.id_estado_factura_pk
                )
            ) {
                window.showToast &&
                    window.showToast(
                        "Ya existe otro estado con ese nombre",
                        "error"
                    );
                return;
            }

            if (
                codigoTrim &&
                this.estadosFactura.some(
                    (ef) =>
                        ef.codigo &&
                        ef.codigo.toLowerCase() === codigoTrim.toLowerCase() &&
                        ef.id_estado_factura_pk !==
                            this.itemToEdit.id_estado_factura_pk
                )
            ) {
                window.showToast &&
                    window.showToast(
                        "Ya existe otro estado con ese código",
                        "error"
                    );
                return;
            }

            try {
                const payload = {
                    nombre: nombreTrim,
                    descripcion: descripcionTrim,
                    codigo: codigoTrim,
                    es_final: esFinal,
                    orden: orden,
                };
                const response = await fetch(
                    `/api/estados-factura/${this.itemToEdit.id_estado_factura_pk}`,
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
                                "Error al actualizar el estado de factura",
                                "error"
                            );
                    }
                    throw data;
                }
                window.showToast &&
                    window.showToast(
                        "Estado de factura actualizado exitosamente",
                        "success"
                    );
                this.isEditEstadoFacturaModalOpen = false;
                this.itemToEdit = {
                    id_estado_factura_pk: null,
                    codigo: "",
                    nombre: "",
                    descripcion: "",
                    es_final: false,
                    orden: 0,
                };
                await this.fetchEstadosFactura();
                this.categorias = this.estadosFactura;
                this.numbers = this.estadosFactura;
            } catch (error) {
                console.error("Error updating estado factura:", error);
            }
        },

        async deleteEstadoFactura() {
            if (!this.itemToDelete || !this.itemToDelete.id_estado_factura_pk)
                return;
            try {
                const response = await fetch(
                    `/api/estados-factura/${this.itemToDelete.id_estado_factura_pk}`,
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
                        "Estado de factura eliminado exitosamente",
                        "success"
                    );
                this.isDeleteEstadoFacturaModalOpen = false;
                this.itemToDelete = null;
                await this.fetchEstadosFactura();
                this.categorias = this.estadosFactura;
                this.numbers = this.estadosFactura;
            } catch (error) {
                console.error("Error deleting estado factura:", error);
                const errorMessage =
                    error?.error || "Error al eliminar el estado de factura";
                window.showToast && window.showToast(errorMessage, "error");
            }
        },

        handleModalSubmit(event) {
            if (event.detail.formId === "formEstadoFactura")
                this.submitEstadoFactura();
            if (event.detail.formId === "formEditEstadoFactura")
                this.updateEstadoFactura();
        },

        handleDelete() {
            if (this.isDeleteEstadoFacturaModalOpen) {
                this.deleteEstadoFactura();
            }
        },
    }));
});
