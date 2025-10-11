// Dashboard store and component: cache-first + background revalidation
// Persists KPI and chart datasets across SPA navigation using Alpine.store and localStorage TTL.

function dashAuthHeaders() {
    // Ya no añadimos Authorization manual; la cookie auth_token se enviará automáticamente.
    return { Accept: "application/json" };
}

async function dashTryFetch(url, headers) {
    const r = await fetch(url, { headers, credentials: "same-origin" });
    if (!r.ok) return { ok: false, status: r.status };
    try {
        return { ok: true, data: await r.json() };
    } catch (_) {
        return { ok: false, status: 0 };
    }
}

document.addEventListener("alpine:init", () => {
    const TTL_MS = 120000; // 2 min

    // Load persisted cache
    let persisted = {};
    try {
        persisted = JSON.parse(localStorage.getItem("dashboard.cache") || "{}");
    } catch (_) {}

    const now = () => Date.now();
    const isStale = (ts) => !ts || now() - ts > (persisted.ttlMs || TTL_MS);

    Alpine.store("dashboard", {
        ttlMs: persisted.ttlMs || TTL_MS,
        indicators: persisted.indicators || null,
        actividadesRecientes: persisted.actividadesRecientes || null,
        charts: {
            ordenes: persisted.charts?.ordenes || null,
            cotizaciones: persisted.charts?.cotizaciones || null,
            proyectos: persisted.charts?.proyectos || null,
        },
        lastFetched: {
            indicators: persisted.lastFetched?.indicators || 0,
            actividades: persisted.lastFetched?.actividades || 0,
            ordenes: persisted.lastFetched?.ordenes || 0,
            cotizaciones: persisted.lastFetched?.cotizaciones || 0,
            proyectos: persisted.lastFetched?.proyectos || 0,
        },

        persist() {
            try {
                localStorage.setItem(
                    "dashboard.cache",
                    JSON.stringify({
                        ttlMs: this.ttlMs,
                        indicators: this.indicators,
                        actividadesRecientes: this.actividadesRecientes,
                        charts: this.charts,
                        lastFetched: this.lastFetched,
                    })
                );
            } catch (_) {}
        },

        // KPIs
        async getIndicators({ force = false } = {}) {
            if (
                !force &&
                this.indicators &&
                !isStale(this.lastFetched.indicators)
            ) {
                return this.indicators;
            }
            let res = await dashTryFetch(
                "/api/dashboard/indicadores",
                dashAuthHeaders()
            );
            if (!res.ok) {
                res = await dashTryFetch("/api-web/dashboard/indicadores", {
                    Accept: "application/json",
                });
            }
            if (res.ok && res.data) {
                this.indicators = res.data;
                this.lastFetched.indicators = now();
                this.persist();
            }
            return this.indicators;
        },

        // Charts
        async getChart(name, { force = false } = {}) {
            const keyToUrl = {
                ordenes: [
                    "/api/dashboard/ordenes-estado",
                    "/api-web/dashboard/ordenes-estado",
                ],
                cotizaciones: [
                    "/api/dashboard/cotizaciones-mes",
                    "/api-web/dashboard/cotizaciones-mes",
                ],
                proyectos: [
                    "/api/dashboard/proyectos-estado",
                    "/api-web/dashboard/proyectos-estado",
                ],
            };
            const current = this.charts[name];
            const lastTs = this.lastFetched[name] || 0;
            if (!force && current && !isStale(lastTs)) {
                return current;
            }
            const urls = keyToUrl[name];
            if (!urls) return current;
            let res = await dashTryFetch(urls[0], dashAuthHeaders());
            if (!res.ok)
                res = await dashTryFetch(urls[1], {
                    Accept: "application/json",
                });
            if (res.ok && res.data) {
                this.charts[name] = res.data;
                this.lastFetched[name] = now();
                this.persist();
            }
            return this.charts[name];
        },

        // Actividades recientes de la bitácora
        async getActividadesRecientes({ force = false } = {}) {
            if (
                !force &&
                this.actividadesRecientes &&
                !isStale(this.lastFetched.actividades)
            ) {
                return this.actividadesRecientes;
            }
            let res = await dashTryFetch(
                "/api/dashboard/actividades-recientes",
                dashAuthHeaders()
            );
            if (!res.ok) {
                res = await dashTryFetch(
                    "/api-web/dashboard/actividades-recientes",
                    { Accept: "application/json" }
                );
            }
            if (res.ok && Array.isArray(res.data)) {
                this.actividadesRecientes = res.data;
                this.lastFetched.actividades = now();
                this.persist();
            }
            return this.actividadesRecientes || [];
        },
    });

    // Provide a global Alpine component factory for the KPIs box
    window.dashboardKPIs = function () {
        return {
            // mirrored props for simple templates
            totalUsuarios: 0,
            empresasActivas: 0,
            ordenesServicio: 0,
            cotizaciones: 0,
            proyectosActivos: 0,
            proyectosFinalizados: 0,
            ticketsAbiertos: 0,
            ticketsCerrados: 0,
            inventarioProductos: 0,
            reportesGenerados: 0,

            init() {
                // Hydrate from cache immediately if available
                const cached = this.$store.dashboard.indicators;
                if (cached) this.assign(cached);
                // Background revalidation
                this.revalidate();
            },

            async revalidate() {
                const data = await this.$store.dashboard.getIndicators({
                    force: true,
                });
                if (data) this.assign(data);
            },

            assign(d) {
                this.totalUsuarios = d.totalUsuarios ?? 0;
                this.empresasActivas = d.empresasActivas ?? 0;
                this.ordenesServicio = d.ordenesServicio ?? 0;
                this.cotizaciones = d.cotizaciones ?? 0;
                this.proyectosActivos = d.proyectosActivos ?? 0;
                this.proyectosFinalizados = d.proyectosFinalizados ?? 0;
                this.ticketsAbiertos = d.ticketsAbiertos ?? 0;
                this.ticketsCerrados = d.ticketsCerrados ?? 0;
                this.inventarioProductos = d.inventarioProductos ?? 0;
                this.reportesGenerados = d.reportesGenerados ?? 0;
            },

            fmt(n) {
                try {
                    return Number(n || 0).toLocaleString("es-HN");
                } catch (_) {
                    return n ?? "0";
                }
            },

            totalTickets() {
                return (
                    (this.ticketsAbiertos || 0) + (this.ticketsCerrados || 0)
                );
            },

            percentTickets(kind) {
                const total = this.totalTickets();
                if (!total) return 0;
                const num =
                    kind === "abiertos"
                        ? this.ticketsAbiertos || 0
                        : this.ticketsCerrados || 0;
                return Math.round((num / total) * 100);
            },
        };
    };

    // Componente Alpine.js para mostrar las actividades recientes de la bitácora
    window.actividadesRecientesDashboard = function () {
        return {
            actividades: [],
            loading: true,
            error: "",

            async init() {
                await this.cargarActividades();
            },

            async cargarActividades() {
                this.loading = true;
                this.error = "";
                try {
                    const store = this.$store.dashboard;
                    const actividades = await store.getActividadesRecientes({
                        force: true,
                    });
                    this.actividades = actividades || [];
                } catch (error) {
                    console.error(
                        "Error al cargar actividades recientes:",
                        error
                    );
                    this.error = "Error al cargar las actividades";
                } finally {
                    this.loading = false;
                }
            },

            getIconoAccion(accion) {
                const iconos = {
                    Login: "fas fa-sign-in-alt",
                    Logout: "fas fa-sign-out-alt",
                    Insertar: "fas fa-plus",
                    Creación: "fas fa-plus",
                    Actualizar: "fas fa-edit",
                    Actualización: "fas fa-edit",
                    Eliminar: "fas fa-trash",
                    Eliminación: "fas fa-trash",
                    Consulta: "fas fa-eye",
                    default: "fas fa-info-circle",
                };
                return iconos[accion] || iconos.default;
            },

            getColorAccion(accion) {
                const colores = {
                    Login: "bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400",
                    Logout: "bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400",
                    Insertar:
                        "bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400",
                    Creación:
                        "bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400",
                    Actualizar:
                        "bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400",
                    Actualización:
                        "bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400",
                    Eliminar:
                        "bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400",
                    Eliminación:
                        "bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400",
                    Consulta:
                        "bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-400",
                    default:
                        "bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-400",
                };
                return colores[accion] || colores.default;
            },

            getColorUsuario(index) {
                const colores = [
                    "bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400",
                    "bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400",
                    "bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400",
                    "bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400",
                    "bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400",
                ];
                return colores[index % colores.length];
            },
        };
    };
});
