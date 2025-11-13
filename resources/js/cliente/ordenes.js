if (typeof window.ordenesCliente === "undefined") {
    window.ordenesCliente = function () {
        return {
            page: 1,
            pageSize: 10,
            loading: false,
            showRateModal: false,
            showRatedModal: false,
            rateValue: "",
            selected: null,
            filtros: {
                search: "",
                estado: "",
                desde: "",
                hasta: "",
            },
            estados: [],
            datos: [],
            async init() {
                this.loading = true;
                try {
                    const res = await fetch("/cliente/ordenes-data", {
                        headers: {
                            Accept: "application/json",
                        },
                        credentials: "same-origin",
                    });
                    const j = await res.json();
                    const arr = j.data || [];
                    this.datos = Array.isArray(arr) ? arr : [];
                    const uniq = {};
                    this.datos.forEach((d) => {
                        const k = (d.estado || "").trim();
                        if (k && !uniq[k]) uniq[k] = k;
                    });
                    this.estados = Object.values(uniq);
                } catch (e) {
                    this.datos = [];
                    this.estados = [];
                } finally {
                    this.loading = false;
                }
                const debounce = (fn, ms = 300) => {
                    let h;
                    return (...a) => {
                        clearTimeout(h);
                        h = setTimeout(() => fn(...a), ms);
                    };
                };
                this.$watch(
                    "filtros.search",
                    debounce(() => {
                        this.page = 1;
                    })
                );
                this.$watch("filtros.estado", () => {
                    this.page = 1;
                });
                this.$watch("filtros.desde", () => {
                    this.page = 1;
                });
                this.$watch("filtros.hasta", () => {
                    this.page = 1;
                });
            },
            get filtradas() {
                return this.datos.filter((d) => {
                    const s = this.filtros.search.toLowerCase();
                    const estadoOk =
                        !this.filtros.estado ||
                        d.estado === this.filtros.estado;
                    const textoOk =
                        !s ||
                        d.numero.toLowerCase().includes(s) ||
                        d.tecnico.toLowerCase().includes(s);
                    const desdeOk =
                        !this.filtros.desde ||
                        d.fecha_creada >= this.filtros.desde;
                    const hastaOk =
                        !this.filtros.hasta ||
                        d.fecha_creada <= this.filtros.hasta;
                    return estadoOk && textoOk && desdeOk && hastaOk;
                });
            },
            get totalPages() {
                return Math.max(
                    1,
                    Math.ceil(this.filtradas.length / this.pageSize)
                );
            },
            get totalOrdenes() {
                return this.filtradas.length;
            },
            get abiertasCount() {
                const open = ["programada", "en proceso", "abierta"];
                return this.filtradas.filter((d) =>
                    open.includes(String(d.estado || "").toLowerCase())
                ).length;
            },
            get cerradasCount() {
                const closed = ["finalizada", "cancelada", "cerrada"];
                return this.filtradas.filter((d) =>
                    closed.includes(String(d.estado || "").toLowerCase())
                ).length;
            },
            get paginadas() {
                const s = (this.page - 1) * this.pageSize;
                return this.filtradas.slice(s, s + this.pageSize);
            },
            get inicioPagina() {
                return this.filtradas.length === 0
                    ? 0
                    : (this.page - 1) * this.pageSize + 1;
            },
            get finPagina() {
                return Math.min(
                    this.filtradas.length,
                    this.page * this.pageSize
                );
            },
            prev() {
                if (this.page > 1) this.page--;
            },
            next() {
                if (this.page < this.totalPages) this.page++;
            },
            resetFiltros() {
                this.filtros = {
                    search: "",
                    estado: "",
                    desde: "",
                    hasta: "",
                };
                this.page = 1;
            },
            estadoBadge(e) {
                const key = String(e || "").toLowerCase();
                return (
                    {
                        programada:
                            "bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300",
                        "en proceso":
                            "bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300",
                        finalizada:
                            "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300",
                        cancelada:
                            "bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300",
                        abierta:
                            "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300",
                        cerrada:
                            "bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200",
                    }[key] ||
                    "bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200"
                );
            },
            isCalificable(o) {
                const n = String((o && o.estado) || "").toLowerCase();
                if (!n) return false;
                if (o && o.calificada === true) return false;
                if (
                    n.includes("cerrad") ||
                    n.includes("finaliz") ||
                    n.includes("resuelt")
                )
                    return true;
                if (n.includes("complet") || n.includes("conclu")) return true;
                return false;
            },
            calificarOrden(o) {
                this.selected = o;
                this.rateValue = "";
                this.showRateModal = true;
            },
            async submitRate() {
                if (!this.selected || !this.selected.id || !this.rateValue)
                    return;
                try {
                    const tokenEl = document.querySelector(
                        'meta[name="csrf-token"]'
                    );
                    const csrf = tokenEl ? tokenEl.getAttribute("content") : "";
                    const res = await fetch(
                        `/cliente/ordenes/${this.selected.id}/calificar`,
                        {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                Accept: "application/json",
                                "X-CSRF-TOKEN": csrf,
                            },
                            body: JSON.stringify({
                                calificacion: this.rateValue,
                            }),
                            credentials: "same-origin",
                        }
                    );
                    const j = await res.json().catch(() => ({}));
                    if (!res.ok || j.success === false) {
                        alert("No se pudo calificar la orden");
                        return;
                    }
                    this.showRateModal = false;
                    this.showRatedModal = true;
                    try {
                        const id = this.selected.id;
                        const idx = this.datos.findIndex((d) => d.id === id);
                        if (idx >= 0) this.datos[idx].calificada = true;
                        this.selected.calificada = true;
                    } catch (_) {}
                } catch (e) {
                    console.error(e);
                    alert("No se pudo calificar la orden");
                }
            },
        };
    };
}
