if (typeof window.ticketsCliente === "undefined") {
    window.ticketsCliente = function () {
        return {
            page: 1,
            pageSize: 10,
            loading: false,
            filtros: {
                search: "",
                estado: "",
                desde: "",
                hasta: "",
            },
            estados: [],
            datos: [],
            get resumen() {
                const norm = (v) => (v ?? "").toString().trim().toLowerCase();
                const pendientes = this.datos.filter(
                    (t) => norm(t.estado) === "pendiente"
                ).length;
                const enProceso = this.datos.filter(
                    (t) => norm(t.estado) === "en proceso"
                ).length;
                const asignados = this.datos.filter(
                    (t) => norm(t.estado) === "asignado"
                ).length;
                const resueltos = this.datos.filter(
                    (t) => norm(t.estado) === "resuelto"
                ).length;
                const cerrados = this.datos.filter(
                    (t) => norm(t.estado) === "cerrado"
                ).length;
                return {
                    total: this.datos.length,
                    pendientes,
                    enProceso,
                    asignados,
                    resueltos,
                    cerrados,
                };
            },
            async init() {
                this.loading = true;
                try {
                    const res = await fetch("/cliente/tickets-data", {
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
                    const s = (this.filtros.search || "").toLowerCase();
                    const estadoOk =
                        !this.filtros.estado ||
                        d.estado === this.filtros.estado;
                    const textoOk =
                        !s ||
                        d.numero.toLowerCase().includes(s) ||
                        (d.tecnico || "").toLowerCase().includes(s);
                    const desdeOk =
                        !this.filtros.desde ||
                        d.fecha_creacion >= this.filtros.desde;
                    const hastaOk =
                        !this.filtros.hasta ||
                        d.fecha_creacion <= this.filtros.hasta;
                    return estadoOk && textoOk && desdeOk && hastaOk;
                });
            },
            get totalPages() {
                return Math.max(
                    1,
                    Math.ceil(this.filtradas.length / this.pageSize)
                );
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
                const k = String(e || "").toLowerCase();
                return (
                    {
                        pendiente:
                            "bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300",
                        "en proceso":
                            "bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300",
                        asignado:
                            "bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300",
                        cerrado:
                            "bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200",
                        resuelto:
                            "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300",
                    }[k] ||
                    "bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200"
                );
            },
        };
    };
}
