document.addEventListener("alpine:init", () => {
    Alpine.data("bitacoraList", () => ({
        items: [],
        loading: false,
        error: "",
        pagination: { page: 1, last_page: 1, total: 0, per_page: 10 },
        filters: {
            search: "",
            accion: "",
            usuario: "",
            objeto: "",
            desde: "",
            hasta: "",
            per_page: 10,
            sort: "fecha_evento",
            direction: "desc",
        },
        init() {
            this.fetch();
        },
        apiBase() {
            return "/api/bitacoras";
        },
        reportUrl() {
            const params = new URLSearchParams();
            params.set("modulo", "Bitacora");
            params.set("fecha", new Date().toISOString().split("T")[0]);

            Object.entries(this.filters).forEach(([k, v]) => {
                if (v !== "" && v != null) params.set(k, v);
            });

            params.set("sort", this.filters.sort || "fecha_evento");
            params.set("direction", this.filters.direction || "desc");
            return `/admin/reportes-header?${params.toString()}`;
        },
        authHeader() {
            return {};
        },
        async fetch(page = null) {
            this.loading = true;
            this.error = "";
            if (page) this.pagination.page = page;
            const params = new URLSearchParams();
            Object.entries(this.filters).forEach(([k, v]) => {
                if (v !== null && v !== undefined && v !== "")
                    params.append(k, v);
            });
            params.append("page", this.pagination.page);
            try {
                const res = await axios.get(
                    `${this.apiBase()}?${params.toString()}`,
                    {
                        headers: {
                            ...this.authHeader(),
                            Accept: "application/json",
                        },
                    }
                );
                const data = res.data || {};
                this.items = data.data || [];
                this.pagination.page =
                    data.meta?.current_page || data.page || 1;
                this.pagination.last_page =
                    data.meta?.last_page || data.last_page || 1;
                this.pagination.total =
                    data.meta?.total || data.total || this.items.length;
                this.pagination.per_page =
                    data.meta?.per_page ||
                    data.per_page ||
                    this.filters.per_page;
            } catch (e) {
                this.error =
                    e?.response?.data?.error || e?.message || "Error al cargar";
            } finally {
                this.loading = false;
            }
        },
        changePage(p) {
            if (p >= 1 && p <= this.pagination.last_page) this.fetch(p);
        },
        resetFilters() {
            this.filters = {
                search: "",
                accion: "",
                usuario: "",
                objeto: "",
                desde: "",
                hasta: "",
                per_page: 10,
                sort: "fecha_evento",
                direction: "desc",
            };
            this.fetch(1);
        },
    }));
});
