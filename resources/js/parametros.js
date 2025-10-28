document.addEventListener("alpine:init", () => {
    Alpine.data("parametrosCrud", () => ({
        isModalOpen: false,
        isEditModalOpen: false,
        showDeleteModal: false,
        parametros: [],
        numbersParametros: [],
        loading: false,
        error: "",
        formError: "",
        isSubmitting: false,

        // Paginación client-side
        currentPageParametros: 1,
        perPageParametros: 10,

        search: "",
        ordenarPor: "",
        ordenDirection: "asc",
        debounceTimer: null,

        createForm: {
            parametro: "",
            valor: "",
        },
        editForm: {
            id: null,
            parametro: "",
            valor: "",
        },
        parametroToEdit: null,
        parametroToDelete: null,
        apiBase: "/api/parametros",

        // Métodos de paginación client-side
        paginatedParametros() {
            return this.parametros.slice(
                (this.currentPageParametros - 1) * this.perPageParametros,
                this.currentPageParametros * this.perPageParametros
            );
        },
        totalPagesParametros() {
            return Math.ceil(this.parametros.length / this.perPageParametros);
        },
        nextPageParametros() {
            if (this.currentPageParametros < this.totalPagesParametros()) {
                this.currentPageParametros++;
            }
        },
        prevPageParametros() {
            if (this.currentPageParametros > 1) {
                this.currentPageParametros--;
            }
        },

        notify(msg, type = "success") {
            const el = document.createElement("div");
            el.textContent = msg;
            el.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded shadow text-sm text-white ${
                type === "error" ? "bg-red-600" : "bg-green-600"
            }`;
            document.body.appendChild(el);
            setTimeout(() => {
                el.classList.add("opacity-0", "transition");
            }, 2500);
            setTimeout(() => el.remove(), 3000);
        },

        init() {
            window.addEventListener("modal-submit", (e) => {
                if (e.detail?.formId === "formCrearParametro") {
                    this.createParametro();
                }
                if (e.detail?.formId === "formEditarParametro") {
                    this.updateParametro();
                }
            });

            this.$watch("search", () => {
                this.debounceFetch();
                this.currentPageParametros = 1;
            });
            this.$watch("ordenarPor", (val, old) => {
                if (old === val) {
                    this.ordenDirection =
                        this.ordenDirection === "asc" ? "desc" : "asc";
                } else {
                    this.ordenDirection = "asc";
                }
                this.currentPageParametros = 1;
                this.fetchParametros();
            });
            this.$watch("showDeleteModal", (val) => {
                if (!val) this.parametroToDelete = null;
            });

            window.addEventListener("confirm-delete", () => {
                if (this.parametroToDelete)
                    this.deleteParametro(this.parametroToDelete);
            });

            this.fetchParametros();
        },

        debounceFetch() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.currentPageParametros = 1;
                this.fetchParametros();
            }, 400);
        },

        authHeaders() {
            return {
                "Content-Type": "application/json",
                Accept: "application/json",
            };
        },

        async fetchParametros() {
            this.loading = true;
            this.error = "";

            const params = new URLSearchParams();
            params.append("all", "1"); // Traer todos para paginación client-side

            if (this.search) params.append("q", this.search);
            if (this.ordenarPor) {
                params.append("sort", this.ordenarPor);
                params.append("direction", this.ordenDirection);
            }

            try {
                const r = await fetch(`${this.apiBase}?${params.toString()}`, {
                    headers: this.authHeaders(),
                    credentials: "include",
                });

                if (!r.ok) throw await r.json();
                const data = await r.json();

                this.parametros = Array.isArray(data?.data)
                    ? data.data
                    : Array.isArray(data)
                    ? data
                    : [];
                this.numbersParametros = this.parametros;
            } catch (e) {
                this.error =
                    e.error || e.message || "Error al cargar parámetros";
                this.parametros = [];
                this.numbersParametros = [];
            } finally {
                this.loading = false;
            }
        },

        openCreate() {
            if (this.isSubmitting) return;
            this.createForm = {
                parametro: "",
                valor: "",
            };
            this.formError = "";
            this.isModalOpen = true;
        },

        async createParametro() {
            if (this.isSubmitting) return;
            this.isSubmitting = true;
            this.formError = "";

            try {
                const payload = { ...this.createForm };
                const r = await fetch(this.apiBase, {
                    method: "POST",
                    headers: this.authHeaders(),
                    body: JSON.stringify(payload),
                    credentials: "include",
                });

                if (!r.ok) throw await r.json();

                this.isModalOpen = false;
                await this.fetchParametros();
                this.numbersParametros = this.parametros;
                this.notify("Parámetro creado");
            } catch (e) {
                this.formError =
                    (e.errors && Object.values(e.errors).flat().join("\n")) ||
                    e.error ||
                    "Error al crear parámetro";
                this.notify(this.formError, "error");
            } finally {
                this.isSubmitting = false;
            }
        },

        openEdit(p) {
            if (this.isSubmitting) return;
            this.editForm = {
                id: p.id,
                parametro: p.parametro,
                valor: p.valor,
            };
            this.formError = "";
            this.parametroToEdit = p;
            this.isEditModalOpen = true;
        },

        async updateParametro() {
            if (this.isSubmitting) return;
            this.isSubmitting = true;
            this.formError = "";

            const payload = {
                valor: this.editForm.valor,
            };

            try {
                const r = await fetch(`${this.apiBase}/${this.editForm.id}`, {
                    method: "PUT",
                    headers: this.authHeaders(),
                    body: JSON.stringify(payload),
                    credentials: "include",
                });

                if (!r.ok) throw await r.json();
                const data = await r.json();

                this.isEditModalOpen = false;

                const idx = this.parametros.findIndex(
                    (x) => x.id === this.editForm.id
                );
                if (idx > -1) {
                    this.parametros[idx] = data.data || data;
                }
                this.numbersParametros = this.parametros;

                this.notify("Parámetro actualizado");
            } catch (e) {
                this.formError =
                    (e.errors && Object.values(e.errors).flat().join("\n")) ||
                    e.error ||
                    "Error al actualizar parámetro";
                this.notify(this.formError, "error");
            } finally {
                this.isSubmitting = false;
            }
        },

        openDelete(parametro) {
            this.parametroToDelete = parametro;
            this.showDeleteModal = true;
        },

        openReporte() {
            const params = new URLSearchParams();
            params.append("modulo", "PARAMETROS");
            if (this.search) params.append("q", this.search);
            if (this.ordenarPor) {
                params.append("sort", this.ordenarPor);
                params.append("direction", this.ordenDirection);
            }
            const url = `/admin/reportes-header?${params.toString()}`;
            window.open(url, "_blank");
        },

        async deleteParametro(parametro) {
            if (!parametro || !parametro.id) return;

            try {
                const resp = await fetch(`${this.apiBase}/${parametro.id}`, {
                    method: "DELETE",
                    headers: this.authHeaders(),
                    credentials: "include",
                });

                if (resp.status === 404) {
                    const idx404 = this.parametros.findIndex(
                        (x) => x.id === parametro.id
                    );
                    if (idx404 > -1) {
                        this.parametros.splice(idx404, 1);
                    }
                    this.numbersParametros = this.parametros;
                    this.notify("Parámetro ya inexistente, lista actualizada");
                    return;
                }

                if (!resp.ok) {
                    const data = await resp.json().catch(() => ({}));
                    throw new Error(
                        data.message || "Error al eliminar parámetro"
                    );
                }

                const idx = this.parametros.findIndex(
                    (x) => x.id === parametro.id
                );
                if (idx > -1) {
                    this.parametros.splice(idx, 1);
                }
                this.numbersParametros = this.parametros;

                this.notify("Parámetro eliminado");
            } catch (e) {
                console.error(e);
                this.notify(e.message || "Error al eliminar", "error");
            }
        },
    }));
});
