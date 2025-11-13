if (typeof window.facturasCliente === "undefined") {
    window.facturasCliente = function () {
        return {
            page: 1,
            pageSize: 10,
            filtros: {
                search: "",
                estado: "",
                desde: "",
                hasta: "",
            },
            estados: ["Pagada", "Pendiente"],
            datos: [],
            async init() {
                try {
                    const res = await fetch("/cliente/facturas-data", {
                        headers: {
                            Accept: "application/json",
                        },
                    });
                    if (res.ok) {
                        const payload = await res.json();
                        this.datos = Array.isArray(payload)
                            ? payload
                            : payload.data || [];
                    }
                } catch (e) {}
            },
            get filtradas() {
                return this.datos.filter((d) => {
                    const s = this.filtros.search.toLowerCase();
                    const eOk =
                        !this.filtros.estado ||
                        d.estado === this.filtros.estado;
                    const sOk = !s || d.numero.toLowerCase().includes(s);
                    const dOk =
                        !this.filtros.desde || d.fecha >= this.filtros.desde;
                    const hOk =
                        !this.filtros.hasta || d.fecha <= this.filtros.hasta;
                    return eOk && sOk && dOk && hOk;
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
                return (
                    {
                        Pagada: "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300",
                        Pendiente:
                            "bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300",
                        Vencida:
                            "bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300",
                        Anulada:
                            "bg-gray-300 text-gray-700 dark:bg-gray-700 dark:text-gray-200",
                    }[e] ||
                    "bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200"
                );
            },
            get totalFacturas() {
                return this.datos.length;
            },
            get pagadasCount() {
                return this.datos.filter((d) => d.estado === "Pagada").length;
            },
            get pendientesCount() {
                return this.datos.filter((d) => d.estado === "Pendiente")
                    .length;
            },
        };
    };
}
