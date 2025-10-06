// Nota: limpieza de authToken eliminada (migrado a sesiones Sanctum)
import "./bootstrap";
// Global fetch limiter to avoid 429 bursts on initial app bootstrap
if (!window.__FETCH_LIMITER_INSTALLED__) {
    window.__FETCH_LIMITER_INSTALLED__ = true;
    (function installFetchLimiter() {
        const origFetch = window.fetch.bind(window);
        const maxConcurrent = 6; // allow a handful in flight
        let inFlight = 0;
        const queue = [];

        function runNext() {
            if (inFlight >= maxConcurrent) return;
            const next = queue.shift();
            if (!next) return;
            inFlight++;
            const { args, resolve, reject, delay } = next;
            const doFetch = () => origFetch(...args).then(resolve, reject).finally(() => {
                inFlight--;
                runNext();
            });
            if (delay) setTimeout(doFetch, delay);
            else doFetch();
        }

        window.fetch = function limitedFetch(...args) {
            try {
                const url = (args && args[0] ? args[0].toString() : "") || "";
                const isApi = url.includes("/api/");
                const isDashboard = url.includes("/api/dashboard/");
                // Stagger dashboard datasets a bit to avoid parallel spikes
                const delay = isDashboard ? Math.floor(Math.random() * 180) + 60 : (isApi ? Math.floor(Math.random() * 80) : 0);
                if (!isApi) return origFetch(...args);
                return new Promise((resolve, reject) => {
                    queue.push({ args, resolve, reject, delay });
                    runNext();
                });
            } catch (_) {
                return origFetch(...args);
            }
        };
    })();
}
import "./usuarios";
import "./parametros";
import "./perfil";
import "./dashboard";
import "./seguridad";
import "./objetos";
import "./roles";
import "./asignar-roles";
import "./bitacora";
import "./toast";
import "./ubicaciones";
import "./tipo-visitas";
import "./tipo-productos";

import { library, dom } from "@fortawesome/fontawesome-svg-core";
import {
    faEye,
    faEyeSlash,
    faMoon,
    faSun,
    faChevronLeft,
    faChevronRight,
    faFilePdf,
    faPlus,
    faEdit,
    faTrash,
    faUserPlus,
    faFileAlt,
    faBook,
    faDatabase,
    faTools,
    faBox,
    faUserFriends,
    faMapMarkerAlt,
    faWrench,
    faBell,
    faSpinner,
    faFileInvoice,
    faProjectDiagram,
    faBoxOpen,
    faChartBar,
    faLock,
    faUserLock,
    faShieldAlt,
    faUser,
    faSliders,
    faKey,
    faUsers,
    faBuilding,
    faEnvelopeOpenText,
    faCogs,
    faTicketAlt,
    faCalendarAlt,
    faHouseChimney,
    faFileInvoiceDollar,
    faBarcode,
    faArchive,
    faBoxes,
    faUserCog,
    faUserCircle,
    faUnlockAlt,
    faListAlt,
    faCoins,
    faTasks,
    faCalendarCheck,
    faVenusMars,
    faUserShield,
    faList,
    faClipboardList,
    faObjectGroup,
    faUserTag,
    faChartLine,
    faRocket,
    faBolt,
    faChartPie,
    faClipboardCheck,
    faHistory,
    faUserEdit,
    faSignOutAlt,
    faCamera,
    faBookOpen,
    faGlobe,
    faMapMarkedAlt,
    faCity,
    faFilter,
    faChevronUp,
    faChevronDown,
    faTimes,
    faSignInAlt,
    faSearch,
    faClock,
    faBan,
    faRedo,
    faInfoCircle,
    faCalendarDay,
    faFolder,
} from "@fortawesome/free-solid-svg-icons";

library.add(
    faEye,
    faEyeSlash,
    faMoon,
    faSun,
    faChevronLeft,
    faChevronRight,
    faFilePdf,
    faPlus,
    faEdit,
    faTrash,
    faUserPlus,
    faFileAlt,
    faBook,
    faDatabase,
    faTools,
    faBox,
    faUserFriends,
    faMapMarkerAlt,
    faWrench,
    faBell,
    faSpinner,
    faFileInvoice,
    faProjectDiagram,
    faBoxOpen,
    faChartBar,
    faLock,
    faUserLock,
    faHouseChimney,
    faShieldAlt,
    faUser,
    faSliders,
    faKey,
    faUsers,
    faBuilding,
    faEnvelopeOpenText,
    faCogs,
    faTicketAlt,
    faCalendarAlt,
    faFileInvoiceDollar,
    faBarcode,
    faArchive,
    faBoxes,
    faUserCog,
    faUserCircle,
    faUnlockAlt,
    faListAlt,
    faCoins,
    faTasks,
    faCalendarCheck,
    faVenusMars,
    faUserShield,
    faList,
    faClipboardList,
    faObjectGroup,
    faUserTag,
    faChartLine,
    faRocket,
    faBolt,
    faChartPie,
    faClipboardCheck,
    faHistory,
    faUserEdit,
    faSignOutAlt,
    faCamera,
    faBookOpen,
    faGlobe,
    faMapMarkedAlt,
    faCity,
    faFilter,
    faChevronUp,
    faChevronDown,
    faTimes,
    faSignInAlt,
    faSearch,
    faClock,
    faBan,
    faRedo,
    faInfoCircle,
    faCalendarDay,
    faFolder
);
dom.watch();

library.add(faEye, faEyeSlash, faMoon, faSun);
dom.watch();

// Alpine.js collapse plugin
document.addEventListener("alpine:init", () => {
    try {
        // Evitar registrar el plugin 'collapse' múltiples veces
        if (!window.__ALPINE_COLLAPSE_REGISTERED__) {
            Alpine.plugin(collapse);
            window.__ALPINE_COLLAPSE_REGISTERED__ = true;
        }
    } catch (_) {}
});
function collapse(Alpine) {
    Alpine.directive(
        "collapse",
        (el, { expression }, { effect, evaluateLater }) => {
            let duration = 200;
            el.style.height = "0px";
            el.style.overflow = "hidden";
            el.style.transitionProperty = "height";
            el.style.transitionDuration = `${duration}ms`;
            el.style.transitionTimingFunction = "ease-in-out";

            effect(() => {
                let show = evaluateLater(expression);
                show((value) => {
                    if (value) {
                        el.style.height = el.scrollHeight + "px";
                    } else {
                        el.style.height = "0px";
                    }
                });
            });
        }
    );
}

// Navigation store for SPA-like navigation
document.addEventListener("alpine:init", () => {
    Alpine.store("navigation", {
        isTransitioning: false,
        loadedViews: {},
        currentView: null,

        async navigate(url, viewName) {
            // Ya no forzamos completar perfil desde el cliente; el backend controla acceso.

            // Si ya estamos en esta vista, no hacer nada
            if (this.currentView === viewName) return;

            // Si la vista ya está cargada, usarla directamente
            if (this.loadedViews[viewName]) {
                this.setContent(this.loadedViews[viewName]);
                this.updateState(url, viewName);
                return;
            }

            this.isTransitioning = true;
            this.showLoader();
            try {
                const res = await fetch(`/load-view?view=${viewName}`, {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "text/html",
                    },
                    credentials: "same-origin",
                });
                if (
                    res.status === 401 ||
                    res.status === 419 ||
                    res.redirected ||
                    (res.url && res.url.includes("/login"))
                ) {
                    window.location.assign("/login");
                    return;
                }
                if (res.status === 403) {
                    const deniedHtml = await res.text();
                    this.setContent(deniedHtml);
                    this.updateState(url, viewName);
                    return;
                }
                if (!res.ok) {
                    throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                }
                const html = await res.text();
                this.loadedViews[viewName] = html;
                this.setContent(html);
                this.updateState(url, viewName);
            } catch (error) {
                console.error("Error loading view:", error);
                this.showError(
                    "Error al cargar la vista. Por favor, intenta de nuevo."
                );
            } finally {
                this.isTransitioning = false;
            }
        },

        setContent(html) {
            // Guardar posición del scroll del sidebar antes de cambiar contenido
            this.saveSidebarScrollPosition();

            // Destruir instancias de Chart.js antes de reemplazar el DOM
            try {
                if (typeof destroyExistingCharts === "function")
                    destroyExistingCharts();
            } catch (_) {}

            const mainEl = document.querySelector("main");
            // Intentar destruir árbol Alpine anterior (si aplica)
            try {
                if (window.Alpine && Alpine.destroyTree)
                    Alpine.destroyTree(mainEl);
            } catch (_) {}

            // Sanitizar: evitar recargar Alpine desde vistas parciales (remueve scripts externos de Alpine)
            let sanitized = html;
            try {
                sanitized = sanitized.replace(
                    /<script[^>]*src=["'][^"']*alpine[^"']*["'][^>]*>\s*<\/script>/gi,
                    ""
                );
            } catch (_) {}

            mainEl.innerHTML = sanitized;
            // Reinicializar Alpine sólo en raíces nuevas (evita redefinir $nextTick)
            try {
                if (window.Alpine) {
                    // Limpieza defensiva: si alguna magia global quedó definida por doble carga, elimínala
                    try {
                        if ("$nextTick" in window) delete window.$nextTick;
                    } catch (_) {}
                    try {
                        if ("$watch" in window) delete window.$watch;
                    } catch (_) {}
                    try {
                        if ("$dispatch" in window) delete window.$dispatch;
                    } catch (_) {}
                    const roots = Array.from(
                        mainEl.querySelectorAll("[x-data]")
                    ).filter((el) => !el.__x);
                    for (const root of roots) {
                        try {
                            Alpine.initTree(root);
                        } catch (_) {}
                    }
                }
            } catch (_) {}

            // Restaurar posición del scroll del sidebar después de cargar nuevo contenido
            this.restoreSidebarScrollPosition();

            // Inicializar gráficos si estamos en el dashboard
            if (
                html.includes('id="ordenesChart"') ||
                html.includes('id="cotizacionesChart"') ||
                html.includes('id="proyectosChart"')
            ) {
                // Usar setTimeout para asegurar que el DOM esté listo
                setTimeout(() => {
                    initializeDashboardChartsWithRetry();
                }, 100);
            }

            // Notificar a listeners (p.ej., re-vincular el switch de tema) que la vista se cargó
            try {
                document.dispatchEvent(new CustomEvent("app:view-loaded"));
            } catch (_) {}
        },

        saveSidebarScrollPosition() {
            const sidebar = document.querySelector("aside");
            if (sidebar) {
                localStorage.setItem(
                    "sidebar-scroll-position",
                    sidebar.scrollTop
                );
            }
        },

        restoreSidebarScrollPosition() {
            const sidebar = document.querySelector("aside");
            const savedScrollTop = localStorage.getItem(
                "sidebar-scroll-position"
            );
            if (sidebar && savedScrollTop !== null) {
                // Usar requestAnimationFrame para asegurar que el DOM esté listo
                requestAnimationFrame(() => {
                    sidebar.scrollTop = parseInt(savedScrollTop, 10);
                });
            }
        },

        updateState(url, viewName) {
            // Actualizar la URL sin recargar la página
            window.history.pushState({ viewName }, "", url);
            this.currentView = viewName;
            try {
                const main = document.querySelector("main");
                if (main) main.dataset.currentView = viewName;
            } catch (_) {}
            this.updateActiveLinks(url);
        },

        showLoader() {
            document.querySelector("main").innerHTML = `
                <div class="flex flex-col justify-center items-center h-64">
                    <div class="text-blue-500 mb-4">
                        <i class="fas fa-spinner fa-spin text-3xl"></i>
                    </div>
                    <div class="text-blue-500 text-lg font-medium">Cargando...</div>
                </div>
            `;
        },

        showError(message) {
            document.querySelector("main").innerHTML = `
                <div class="flex flex-col justify-center items-center h-64">
                    <div class="text-red-500 mb-4">
                        <i class="fas fa-exclamation-triangle text-3xl"></i>
                    </div>
                    <div class="text-red-500 text-lg font-medium">${message}</div>
                </div>
            `;
        },

        // Método para actualizar los enlaces activos en la barra lateral
        updateActiveLinks(url) {
            // Eliminar la clase activa de todos los enlaces
            document.querySelectorAll(".sidebar-link").forEach((link) => {
                link.classList.remove("bg-gray-800", "text-blue-400");
                link.classList.add("hover:bg-gray-800", "hover:text-blue-400");
            });

            // Encontrar y marcar el enlace activo actual
            document.querySelectorAll(".sidebar-link").forEach((link) => {
                if (link.getAttribute("href") === url) {
                    link.classList.add("bg-gray-800", "text-blue-400");
                    link.classList.remove(
                        "hover:bg-gray-800",
                        "hover:text-blue-400"
                    );

                    // Encontrar y abrir el menú padre si existe
                    const parentDropdown = link.closest(
                        '[x-data^="sidebarDropdown"]'
                    );
                    if (parentDropdown) {
                        const dropdownKey = parentDropdown
                            .getAttribute("x-data")
                            .match(/sidebarDropdown\('([^']+)'/)[1];
                        localStorage.setItem(`sidebar-${dropdownKey}`, "true");

                        // Forzar actualización del menú desplegable
                        const event = new CustomEvent(
                            "update-sidebar-dropdown",
                            {
                                detail: { key: dropdownKey, open: true },
                            }
                        );
                        document.dispatchEvent(event);
                    }
                }
            });
        },

        // Método para manejar navegación con botón atrás/adelante del navegador
        handlePopState(event) {
            if (event.state && event.state.viewName) {
                const viewName = event.state.viewName;
                const url = window.location.pathname;

                // Cargar la vista sin cambiar el estado del historial
                if (this.loadedViews[viewName]) {
                    this.setContent(this.loadedViews[viewName]);
                    this.currentView = viewName;
                    this.updateActiveLinks(url);
                } else {
                    // Si no está cargada, hacer un reload completo
                    window.location.reload();
                }
            }
        },

        // Método para cargar la vista inicial basada en la URL actual
        async loadInitialView() {
            const path = window.location.pathname;
            const viewName = this.extractViewNameFromPath(path);

            if (viewName && viewName !== "dashboard") {
                // Si no estamos en dashboard, cargar la vista correspondiente
                await this.navigate(path, viewName);
            } else {
                // Establecer dashboard como vista actual
                this.currentView = "dashboard";
                try {
                    const main = document.querySelector("main");
                    if (main) main.dataset.currentView = "dashboard";
                } catch (_) {}
                this.updateActiveLinks(path);
            }
        },

        // Extraer el nombre de la vista desde el path
        extractViewNameFromPath(path) {
            const match = path.match(/\/admin\/(.+)$/);
            return match ? match[1] : "dashboard";
        },
    });

    // Manejar navegación con botones del navegador
    window.addEventListener("popstate", (event) => {
        Alpine.store("navigation").handlePopState(event);
    });

    // Cargar vista inicial cuando la página esté lista
    document.addEventListener("DOMContentLoaded", () => {
        // Verificar si la página es una SPA page
        const isSpaPage = document.querySelector('meta[name="spa-page"]');
        const spaView = document.querySelector('meta[name="spa-view"]');

        if (isSpaPage && spaView) {
            const viewName = spaView.getAttribute("content");
            Alpine.store("navigation").currentView = viewName;
            Alpine.store("navigation").updateActiveLinks(
                window.location.pathname
            );
        } else {
            Alpine.store("navigation").loadInitialView();
        }
    });
});

if (typeof window !== "undefined" && window.Chart) {
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = "#6B7280";
}

// Función para inicializar los gráficos del dashboard
function initializeDashboardCharts() {
    const waitForCanvasReady = (el, cb, attempt = 0) => {
        const max = 20; // ~3s total (20 * 150ms)
        const delay = 150;
        if (!el) return;
        const rect = el.getBoundingClientRect();
        const ready =
            rect.width > 10 && rect.height > 10 && el.offsetParent !== null;
        if (ready) return cb();
        if (attempt >= max) return cb(); // last resort: try anyway
        setTimeout(() => waitForCanvasReady(el, cb, attempt + 1), delay);
    };
    // Helper reutilizable para fetch con parse seguro
    const tryFetch = async (url, headers) => {
        const r = await fetch(url, { headers });
        if (!r.ok) return { ok: false };
        try {
            return { ok: true, data: await r.json() };
        } catch (_) {
            return { ok: false };
        }
    };
    // Initialize 'ordenesChart' only if element exists
    const ordenesEl = document.getElementById("ordenesChart");
    if (ordenesEl) {
        // Destruir instancia existente si existe
        if (window.ordenesChartInstance) {
            window.ordenesChartInstance.destroy();
        }
        const initOrdenes = async () => {
            const ordenesCtx = ordenesEl.getContext("2d");
            const store = window.Alpine?.store("dashboard");
            const fromCache = store?.charts?.ordenes;
            const draw = (json) => {
                const labels = json?.labels || [
                    "Abiertas",
                    "En Proceso",
                    "Cerradas",
                ];
                const data = json?.data || [0, 0, 0];
                if (!window.ordenesChartInstance) {
                    window.ordenesChartInstance = new Chart(ordenesCtx, {
                        type: "doughnut",
                        data: {
                            labels,
                            datasets: [
                                {
                                    data,
                                    backgroundColor: [
                                        "#EF4444",
                                        "#F59E0B",
                                        "#10B981",
                                    ],
                                    borderWidth: 2,
                                    borderColor: "#FFFFFF",
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: "bottom",
                                    labels: {
                                        padding: 20,
                                        usePointStyle: true,
                                        font: { size: 12 },
                                    },
                                },
                            },
                        },
                    });
                } else {
                    window.ordenesChartInstance.data.labels = labels;
                    window.ordenesChartInstance.data.datasets[0].data = data;
                    window.ordenesChartInstance.update();
                }
            };
            if (fromCache) draw(fromCache);
            if (store) {
                const updated = await store.getChart("ordenes", {
                    force: true,
                });
                if (updated) draw(updated);
            } else {
                let res = await tryFetch(
                    "/api/dashboard/ordenes-estado",
                    authHeaders()
                );
                if (!res.ok)
                    res = await tryFetch("/api-web/dashboard/ordenes-estado", {
                        Accept: "application/json",
                    });
                if (res.ok) draw(res.data);
            }
        };
        waitForCanvasReady(ordenesEl, initOrdenes);
    }

    // Initialize 'cotizacionesChart' only if element exists
    const cotizacionesEl = document.getElementById("cotizacionesChart");
    if (cotizacionesEl) {
        // Destruir instancia existente si existe
        if (window.cotizacionesChartInstance) {
            window.cotizacionesChartInstance.destroy();
        }

        const initCotizaciones = async () => {
            const cotizacionesCtx = cotizacionesEl.getContext("2d");
            const store = window.Alpine?.store("dashboard");
            const fromCache = store?.charts?.cotizaciones;
            const draw = (json) => {
                const labels = json?.labels || [];
                const data = json?.data || [];
                if (!window.cotizacionesChartInstance) {
                    window.cotizacionesChartInstance = new Chart(
                        cotizacionesCtx,
                        {
                            type: "line",
                            data: {
                                labels,
                                datasets: [
                                    {
                                        label: "Cotizaciones",
                                        data,
                                        borderColor: "#6366F1",
                                        backgroundColor:
                                            "rgba(99, 102, 241, 0.1)",
                                        borderWidth: 3,
                                        fill: true,
                                        tension: 0.4,
                                        pointBackgroundColor: "#6366F1",
                                        pointBorderColor: "#FFFFFF",
                                        pointBorderWidth: 2,
                                        pointRadius: 5,
                                    },
                                ],
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: "#F3F4F6" },
                                    },
                                    x: { grid: { display: false } },
                                },
                            },
                        }
                    );
                } else {
                    window.cotizacionesChartInstance.data.labels = labels;
                    window.cotizacionesChartInstance.data.datasets[0].data =
                        data;
                    window.cotizacionesChartInstance.update();
                }
            };
            if (fromCache) draw(fromCache);
            if (store) {
                const updated = await store.getChart("cotizaciones", {
                    force: true,
                });
                if (updated) draw(updated);
            } else {
                let res = await tryFetch(
                    "/api/dashboard/cotizaciones-mes",
                    authHeaders()
                );
                if (!res.ok)
                    res = await tryFetch(
                        "/api-web/dashboard/cotizaciones-mes",
                        { Accept: "application/json" }
                    );
                if (res.ok) draw(res.data);
            }
        };
        waitForCanvasReady(cotizacionesEl, initCotizaciones);
    }

    // Initialize 'proyectosChart' only if element exists
    const proyectosEl = document.getElementById("proyectosChart");
    if (proyectosEl) {
        // Destruir instancia existente si existe
        if (window.proyectosChartInstance) {
            window.proyectosChartInstance.destroy();
        }

        const initProyectos = async () => {
            const proyectosCtx = proyectosEl.getContext("2d");
            const store = window.Alpine?.store("dashboard");
            const fromCache = store?.charts?.proyectos;
            const draw = (json) => {
                const labels = json?.labels || [];
                const data = json?.data || [];
                const colors = labels.map(
                    (_, i) =>
                        [
                            "#06B6D4",
                            "#10B981",
                            "#F59E0B",
                            "#EF4444",
                            "#6366F1",
                            "#8B5CF6",
                        ][i % 6]
                );
                if (!window.proyectosChartInstance) {
                    window.proyectosChartInstance = new Chart(proyectosCtx, {
                        type: "bar",
                        data: {
                            labels,
                            datasets: [
                                {
                                    data,
                                    backgroundColor: colors,
                                    borderRadius: 6,
                                    borderSkipped: false,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            indexAxis: "y",
                            plugins: { legend: { display: false } },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    grid: { color: "#F3F4F6" },
                                },
                                y: { grid: { display: false } },
                            },
                        },
                    });
                } else {
                    window.proyectosChartInstance.data.labels = labels;
                    window.proyectosChartInstance.data.datasets[0].data = data;
                    window.proyectosChartInstance.data.datasets[0].backgroundColor =
                        colors;
                    window.proyectosChartInstance.update();
                }
            };
            if (fromCache) draw(fromCache);
            if (store) {
                const updated = await store.getChart("proyectos", {
                    force: true,
                });
                if (updated) draw(updated);
            } else {
                let res = await tryFetch(
                    "/api/dashboard/proyectos-estado",
                    authHeaders()
                );
                if (!res.ok)
                    res = await tryFetch(
                        "/api-web/dashboard/proyectos-estado",
                        { Accept: "application/json" }
                    );
                if (res.ok) draw(res.data);
            }
        };
        waitForCanvasReady(proyectosEl, initProyectos);
    }
}

// Destruir instancias activas de Chart.js antes de reemplazar el DOM
function destroyExistingCharts() {
    try {
        if (window.ordenesChartInstance) {
            window.ordenesChartInstance.destroy();
        }
    } catch (_) {}
    try {
        if (window.cotizacionesChartInstance) {
            window.cotizacionesChartInstance.destroy();
        }
    } catch (_) {}
    try {
        if (window.proyectosChartInstance) {
            window.proyectosChartInstance.destroy();
        }
    } catch (_) {}
    window.ordenesChartInstance = null;
    window.cotizacionesChartInstance = null;
    window.proyectosChartInstance = null;
}

// Reintento robusto para inicializar gráficos cuando el DOM/tamaño/Chart están listos
function initializeDashboardChartsWithRetry(retry = 0) {
    const maxRetries = 10;
    const delay = 200;
    // Verificar disponibilidad de Chart y de al menos un canvas objetivo
    const hasTargets =
        document.getElementById("ordenesChart") ||
        document.getElementById("cotizacionesChart") ||
        document.getElementById("proyectosChart");
    if (!window.Chart || !hasTargets) {
        if (retry < maxRetries) {
            return setTimeout(
                () => initializeDashboardChartsWithRetry(retry + 1),
                delay
            );
        }
        return;
    }
    initializeDashboardCharts();
}

// Inicializar gráficos cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", () => {
    initializeDashboardChartsWithRetry();
});

// Utilidad: encabezados con token si existe
function authHeaders() {
    // Cookie auth_token se envía automáticamente; no usar localStorage.
    return { Accept: "application/json" };
}

// Parchar setContent para destruir charts antes de reemplazar el contenido
// (ubicación original más arriba en la definición del store de navegación)
(function patchSetContent() {
    const nav = window.Alpine?.store && window.Alpine.store("navigation");
    if (!nav || typeof nav.setContent !== "function") return;
    const original = nav.setContent.bind(nav);
    nav.setContent = function (html) {
        // Destruir charts antes de cambiar el DOM
        destroyExistingCharts();
        return original(html);
    };
})();
