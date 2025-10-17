import "./bootstrap";

if (!window.__FETCH_LIMITER_INSTALLED__) {
    window.__FETCH_LIMITER_INSTALLED__ = true;

    (function installFetchLimiter() {
        const origFetch = window.fetch.bind(window);
        const maxConcurrent = 6; // allow a handful in flight
        let inFlight = 0;
        const queue = [];

        const AUTH_KEY = "authToken";
        let tokenPromise = null;

        function getToken() {
            try {
                return localStorage.getItem(AUTH_KEY) || null;
            } catch (_) {
                return null;
            }
        }

        function setToken(t) {
            try {
                if (t) localStorage.setItem(AUTH_KEY, t);
                else localStorage.removeItem(AUTH_KEY);
            } catch (_) {}

            try {
                if (window.axios) {
                    if (t)
                        window.axios.defaults.headers.common[
                            "Authorization"
                        ] = `Bearer ${t}`;
                    else
                        delete window.axios.defaults.headers.common[
                            "Authorization"
                        ];
                }
            } catch (_) {}

            try {
                document.dispatchEvent(
                    new CustomEvent("auth:updated", {
                        detail: { token: t || null },
                    })
                );
            } catch (_) {}
        }

        async function fetchSessionToken(force = false) {
            if (!force) {
                const existing = getToken();
                if (existing) return existing;
            }

            if (!tokenPromise) {
                tokenPromise = (async () => {
                    try {
                        const res = await origFetch("/session/token", {
                            method: "GET",
                            headers: {
                                Accept: "application/json",
                                "X-Requested-With": "XMLHttpRequest",
                            },
                            credentials: "same-origin",
                        });
                        if (res.status === 401 || res.status === 419) {
                            setToken(null);
                            return null;
                        }
                        if (!res.ok) return null;
                        const data = await res.json().catch(() => null);
                        const t = data && (data.token || data.access_token);
                        if (t) setToken(t);
                        return t || null;
                    } catch (_) {
                        return null;
                    } finally {
                        setTimeout(() => {
                            tokenPromise = null;
                        }, 0);
                    }
                })();
            }
            return tokenPromise;
        }

        try {
            window.__AUTH = {
                getToken,
                setToken,
                ensureToken: (force = false) => fetchSessionToken(force),
                headers() {
                    const t = getToken();
                    const h = { Accept: "application/json" };
                    if (t) h["Authorization"] = `Bearer ${t}`;
                    return h;
                },
            };
        } catch (_) {}

        function withAuthToApi(input, init) {
            const t = getToken();
            const merged = init ? { ...init } : {};
            merged.headers = new Headers(
                init && init.headers ? init.headers : {}
            );
            merged.headers.set("X-Requested-With", "XMLHttpRequest");
            if (!merged.headers.has("Accept"))
                merged.headers.set("Accept", "application/json");
            if (t) merged.headers.set("Authorization", `Bearer ${t}`);
            return [input, merged];
        }

        function runNext() {
            if (inFlight >= maxConcurrent) return;
            const next = queue.shift();
            if (!next) return;
            inFlight++;

            if (next.run) {
                const { run, resolve, reject, delay } = next;
                const doFetch = () =>
                    run()
                        .then(resolve, reject)
                        .finally(() => {
                            inFlight--;
                            runNext();
                        });
                if (delay) setTimeout(doFetch, delay);
                else doFetch();
            } else {
                const { args, resolve, reject, delay } = next;
                const doFetch = () =>
                    origFetch(...args)
                        .then(resolve, reject)
                        .finally(() => {
                            inFlight--;
                            runNext();
                        });
                if (delay) setTimeout(doFetch, delay);
                else doFetch();
            }
        }

        // 🔹 Se mantiene dentro del IIFE, con acceso a todas las variables
        window.fetch = function limitedFetch(...args) {
            try {
                const url = (args && args[0] ? args[0].toString() : "") || "";
                const isApi = url.includes("/api/");
                const isDashboard = url.includes("/api/dashboard/");
                const delay = isDashboard
                    ? Math.floor(Math.random() * 180) + 60
                    : isApi
                    ? Math.floor(Math.random() * 80)
                    : 0;

                if (!isApi) return origFetch(...args);

                return new Promise((resolve, reject) => {
                    const run = async () => {
                        await fetchSessionToken(false);
                        let [input, init] = withAuthToApi(args[0], args[1]);
                        let res = await origFetch(input, init);
                        if (res.status === 401) {
                            setToken(null);
                            await fetchSessionToken(true);
                            [input, init] = withAuthToApi(args[0], args[1]);
                            res = await origFetch(input, init);
                        }
                        return res;
                    };
                    queue.push({ run, resolve, reject, delay });
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
import "./agencias";
import "./tipo-visitas";
import "./tipo-productos";
import "./tipo-objetos";
import "./tipo-movimientos";
import "./servicios-realizados";
import "./estados-facturas";
import "./estados-cai";
import "./cai";
import "./facturas";
import "./servicios-factura";
import "./proyectos";
import "./estados-calendario";
import "./estados-tickets";
import "./generos";
import "./estados-solicitud";
import "./estados-proyecto";
import "./categorias";
import "./acciones-realizadas";
import "./productos";
import "./kardex";
import "./origen-kardex";
import "./calendario";

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
    faClipboardQuestion,
    faMapSigns,
    faCheckCircle,
    faTimesCircle,
    faTrashAlt,
    faExclamationTriangle,
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
    faFolder,
    faClipboardQuestion,
    faMapSigns,
    faCheckCircle,
    faTimesCircle,
    faTrashAlt,
    faExclamationTriangle
);
dom.watch();

library.add(faEye, faEyeSlash, faMoon, faSun);
dom.watch();

document.addEventListener("alpine:init", () => {
    try {
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

document.addEventListener("alpine:init", () => {
    Alpine.store("navigation", {
        isTransitioning: false,
        loadedViews: {},
        currentView: null,

        async navigate(url, viewName) {
            if (this.currentView === viewName) return;

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
            this.saveSidebarScrollPosition();

            try {
                if (typeof destroyExistingCharts === "function")
                    destroyExistingCharts();
            } catch (_) {}

            const mainEl = document.querySelector("main");
            try {
                if (window.Alpine && Alpine.destroyTree)
                    Alpine.destroyTree(mainEl);
            } catch (_) {}

            let sanitized = html;
            try {
                sanitized = sanitized.replace(
                    /<script[^>]*src=["'][^"']*alpine[^"']*["'][^>]*>\s*<\/script>/gi,
                    ""
                );
            } catch (_) {}

            mainEl.innerHTML = sanitized;
            try {
                if (window.Alpine) {
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

            // Indicar a Livewire que el DOM ha cambiado para que re-inicialice componentes
            try {
                if (
                    window.Livewire &&
                    typeof window.Livewire.rescan === "function"
                ) {
                    window.Livewire.rescan(mainEl);
                }
            } catch (_) {}
            try {
                if (
                    window.Livewire &&
                    typeof window.Livewire.restart === "function"
                ) {
                    window.Livewire.restart();
                }
            } catch (_) {}
            try {
                window.dispatchEvent(new Event("livewire:navigated"));
            } catch (_) {}

            this.restoreSidebarScrollPosition();

            if (
                html.includes('id="ordenesChart"') ||
                html.includes('id="cotizacionesChart"') ||
                html.includes('id="proyectosChart"')
            ) {
                setTimeout(() => {
                    initializeDashboardChartsWithRetry();
                }, 100);
            }

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

        updateActiveLinks(url) {
            document.querySelectorAll(".sidebar-link").forEach((link) => {
                link.classList.remove("bg-gray-800", "text-blue-400");
                link.classList.add("hover:bg-gray-800", "hover:text-blue-400");
            });

            document.querySelectorAll(".sidebar-link").forEach((link) => {
                if (link.getAttribute("href") === url) {
                    link.classList.add("bg-gray-800", "text-blue-400");
                    link.classList.remove(
                        "hover:bg-gray-800",
                        "hover:text-blue-400"
                    );

                    const parentDropdown = link.closest(
                        '[x-data^="sidebarDropdown"]'
                    );
                    if (parentDropdown) {
                        const dropdownKey = parentDropdown
                            .getAttribute("x-data")
                            .match(/sidebarDropdown\('([^']+)'/)[1];
                        localStorage.setItem(`sidebar-${dropdownKey}`, "true");

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

        handlePopState(event) {
            if (event.state && event.state.viewName) {
                const viewName = event.state.viewName;
                const url = window.location.pathname;

                if (this.loadedViews[viewName]) {
                    this.setContent(this.loadedViews[viewName]);
                    this.currentView = viewName;
                    this.updateActiveLinks(url);
                } else {
                    window.location.reload();
                }
            }
        },

        async loadInitialView() {
            const path = window.location.pathname;
            const viewName = this.extractViewNameFromPath(path);

            if (viewName && viewName !== "dashboard") {
                await this.navigate(path, viewName);
            } else {
                this.currentView = "dashboard";
                try {
                    const main = document.querySelector("main");
                    if (main) main.dataset.currentView = "dashboard";
                } catch (_) {}
                this.updateActiveLinks(path);
            }
        },

        extractViewNameFromPath(path) {
            const match = path.match(/\/admin\/(.+)$/);
            return match ? match[1] : "dashboard";
        },
    });

    window.addEventListener("popstate", (event) => {
        Alpine.store("navigation").handlePopState(event);
    });

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

    const cotizacionesEl = document.getElementById("cotizacionesChart");
    if (cotizacionesEl) {
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

    const proyectosEl = document.getElementById("proyectosChart");
    if (proyectosEl) {
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

function initializeDashboardChartsWithRetry(retry = 0) {
    const maxRetries = 10;
    const delay = 200;
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

document.addEventListener("DOMContentLoaded", () => {
    try {
        window.__AUTH && window.__AUTH.ensureToken(false);
    } catch (_) {}
    initializeDashboardChartsWithRetry();
});

function authHeaders() {
    try {
        if (window.__AUTH && typeof window.__AUTH.headers === "function") {
            return window.__AUTH.headers();
        }
    } catch (_) {}

    return { Accept: "application/json" };
}

(function patchSetContent() {
    const nav = window.Alpine?.store && window.Alpine.store("navigation");
    if (!nav || typeof nav.setContent !== "function") return;
    const original = nav.setContent.bind(nav);
    nav.setContent = function (html) {
        destroyExistingCharts();
        return original(html);
    };
})();

// Alpine component factory for Gestión de Órdenes (available globally for Livewire/SPA)
if (typeof window !== "undefined") {
    window.gestionOrdenes = function (detalleBaseUrl) {
        return {
            tab: "ordenes",
            searchOrden: "",
            tecnicoOrden: "",
            ordenarPor: "fecha_recepcion",
            expandedRows: {},
            isModalOpen: false,
            isEditModalOpen: false,
            isDeleteModalOpen: false,
            ordenToEdit: null,
            ordenToDelete: null,
            ordenes: [],
            tecnicosDisponibles: [],
            solicitudesOptions: [],
            tecnicosOptions: [],

            cotizacionesOptions: [],
            estadosOrdenOptions: [],
            loadingCatalogos: {
                solicitudes: false,
                tecnicos: false,
                cotizaciones: false,
                estadosOrden: false,
            },
            authError: false,
            authNotified: false,
            loadingOrdenes: false,
            saving: false,
            deleting: false,
            errors: {},
            formOrden: {
                id: null,
                id_solicitud_servicio_fk: "",
                id_tecnico_fk: "",
                id_estado_orden_servicio_fk: "",
                fecha_recepcion: new Date().toISOString().slice(0, 10),
                fecha_inicio: "",
                fecha_finalizacion: "",
                observaciones: "",
                diagnostico_tecnico: "",
                diagnostico_cliente: "",
                id_cotizacion_fk: "",
            },
            getToken() {
                try {
                    return (
                        window.__AUTH?.getToken?.() ||
                        localStorage.getItem("authToken")
                    );
                } catch (_) {
                    return null;
                }
            },
            setToken(token) {
                try {
                    if (window.__AUTH?.setToken) window.__AUTH.setToken(token);
                    else if (token) localStorage.setItem("authToken", token);
                } catch (_) {}
                return token;
            },
            getCsrf() {
                const m = document.head.querySelector(
                    'meta[name="csrf-token"]'
                );
                return m ? m.content : "";
            },
            apiHeaders() {
                const t = this.getToken();
                const h = {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                };
                if (t) h["Authorization"] = "Bearer " + t;
                return h;
            },
            async requireAuth() {
                const existing = this.getToken();
                if (existing) {
                    this.authError = false;
                    return true;
                }
                try {
                    // Prefer global ensureToken if available
                    const t =
                        typeof window.__AUTH?.ensureToken === "function"
                            ? await window.__AUTH.ensureToken(false)
                            : null;
                    if (t) {
                        this.authError = false;
                        return true;
                    }
                    const res = await fetch("/session/token", {
                        method: "GET",
                        headers: {
                            Accept: "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            "X-CSRF-TOKEN": this.getCsrf(),
                        },
                        credentials: "same-origin",
                    });
                    if (!res.ok) throw new Error("unauthorized");
                    const data = await res.json();
                    if (data && (data.token || data.access_token)) {
                        this.setToken(data.token || data.access_token);
                        this.authError = false;
                        return true;
                    }
                } catch (e) {
                    console.error("Auth error", e);
                }
                this.authError = true;
                if (!this.authNotified) {
                    this.showToast(
                        "Inicia sesión para gestionar órdenes de servicio.",
                        "error"
                    );
                    this.authNotified = true;
                }
                return false;
            },
            handleUnauthorized() {
                this.authError = true;
                if (!this.authNotified) {
                    this.showToast(
                        "Tu sesión expiró. Vuelve a iniciar sesión para continuar.",
                        "error"
                    );
                    this.authNotified = true;
                }
            },
            showToast(message, type = "ok") {
                const toast = document.createElement("div");
                toast.className =
                    "fixed top-4 right-4 z-50 px-4 py-2 rounded shadow text-sm " +
                    (type === "error"
                        ? "bg-red-600 text-white"
                        : "bg-green-600 text-white");
                toast.textContent = message;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3500);
            },
            resetForm() {
                this.formOrden = {
                    id: null,
                    id_solicitud_servicio_fk: "",
                    id_tecnico_fk: "",
                    id_estado_orden_servicio_fk: "",
                    fecha_recepcion: new Date().toISOString().slice(0, 10),
                    fecha_inicio: "",
                    fecha_finalizacion: "",
                    observaciones: "",
                    diagnostico_tecnico: "",
                    diagnostico_cliente: "",
                    id_cotizacion_fk: "",
                };
                this.errors = {};
            },
            formatDate(value) {
                if (!value) return "";
                const date = new Date(value);
                if (!isNaN(date.getTime())) {
                    return date.toISOString().slice(0, 10);
                }
                const cleaned = value.toString();
                return cleaned.includes("T")
                    ? cleaned.split("T")[0]
                    : cleaned.split(" ")[0];
            },
            mapOrden(orden) {
                const solicitud = orden.solicitud_servicio || {};
                const cliente = solicitud.cliente || {};
                const empresa = cliente.empresa || {};
                const contacto = solicitud.contacto || {};
                const tecnico = orden.tecnico || {};
                const calificacion = {}; // calificación eliminada
                const estado = orden.estado || {};
                const cotizacion =
                    orden.cotizacion || orden.cotizacion_generada || {};
                const fechaRecepcion = this.formatDate(orden.fecha_recepcion);
                const fechaInicio = this.formatDate(orden.fecha_inicio);
                const fechaFinalizacion = this.formatDate(
                    orden.fecha_finalizacion
                );
                const calificacionValor = calificacion.calificacion ?? null;
                // Estado: derive name even if relation missing, using FK + options
                const estadoIdFromRel =
                    estado.id_estado_orden_servicio_pk ?? null;
                const estadoIdFromTop =
                    orden.id_estado_orden_servicio_fk ?? null;
                const estadoId = estadoIdFromRel ?? estadoIdFromTop;
                let estadoNombre =
                    estado.nombre_estado ||
                    estado.nombre ||
                    estado.codigo ||
                    "";
                if (!estadoNombre && estadoId) {
                    try {
                        const opt = Array.isArray(this.estadosOrdenOptions)
                            ? this.estadosOrdenOptions.find(
                                  (o) => String(o.value) === String(estadoId)
                              )
                            : null;
                        if (opt) estadoNombre = opt.label || `ID ${estadoId}`;
                    } catch (_) {
                        estadoNombre = `ID ${estadoId}`;
                    }
                }
                return {
                    id: orden.id_orden_servicio_pk,
                    numero: orden.numero_orden_servicio || "",
                    id_solicitud: orden.id_solicitud_servicio_fk,
                    numero_solicitud:
                        solicitud.numero_solicitud_acf ||
                        solicitud.numero_solicitud_cliente ||
                        "",
                    numero_solicitud_acf:
                        solicitud.numero_solicitud_acf || null,
                    numero_solicitud_cliente:
                        solicitud.numero_solicitud_cliente || null,
                    cliente_nombre:
                        empresa.nombre_comercial || empresa.razon_social || "",
                    contacto_valor: contacto.valor_contacto || "",
                    contacto_tipo: contacto.tipo_contacto || "",
                    id_tecnico: orden.id_tecnico_fk,
                    tecnico_nombre: tecnico.primer_nombre
                        ? [tecnico.primer_nombre, tecnico.primer_apellido]
                              .filter(Boolean)
                              .join(" ")
                        : "",
                    tecnico_documento: tecnico.dni || "",
                    fecha_recepcion: fechaRecepcion,
                    fecha_inicio: fechaInicio,
                    fecha_finalizacion: fechaFinalizacion,
                    observaciones: orden.observaciones || "",
                    diagnostico_tecnico: orden.diagnostico_tecnico || "",
                    diagnostico_cliente: orden.diagnostico_cliente || "",

                    id_cotizacion: orden.id_cotizacion_fk,
                    cotizacion_total: cotizacion.total || null,
                    estado: estadoNombre,
                    estado_id: estadoId ? Number(estadoId) : null,
                    estado_codigo: estado.codigo || "",
                    raw: orden,
                };
            },
            actualizarTecnicos() {
                const mapa = new Map();
                this.ordenes.forEach((orden) => {
                    if (!orden.id_tecnico) return;
                    const valor = String(orden.id_tecnico);
                    const label = orden.tecnico_nombre
                        ? `${orden.tecnico_nombre} (ID ${orden.id_tecnico})`
                        : `ID ${orden.id_tecnico}`;
                    mapa.set(valor, label);
                });
                this.tecnicosDisponibles = Array.from(mapa.entries()).map(
                    ([value, label]) => ({ value, label })
                );
            },
            ensureOption(listName, value, label) {
                if (value === null || value === undefined || value === "")
                    return;
                if (!Array.isArray(this[listName])) {
                    this[listName] = [];
                }
                const normalizedValue = String(value);
                const exists = this[listName].some(
                    (opt) => String(opt.value) === normalizedValue
                );
                if (!exists) {
                    this[listName].push({
                        value: normalizedValue,
                        label: label || `ID ${normalizedValue}`,
                    });
                    this.sortOptions(listName);
                }
            },
            sortOptions(listName) {
                if (!Array.isArray(this[listName])) return;
                this[listName].sort((a, b) =>
                    a.label.localeCompare(b.label, "es")
                );
            },
            ensureOrdenOptions(orden) {
                if (!orden) return;
                this.ensureOption(
                    "solicitudesOptions",
                    orden.id_solicitud,
                    String(orden.id_solicitud)
                );
                this.ensureOption(
                    "tecnicosOptions",
                    orden.id_tecnico,
                    orden.tecnico_nombre
                        ? `${orden.tecnico_nombre}`
                        : String(orden.id_tecnico)
                );
                if (orden.estado_id) {
                    const label = orden.estado
                        ? `${orden.estado}`
                        : `ID ${orden.estado_id}`;
                    this.ensureOption(
                        "estadosOrdenOptions",
                        orden.estado_id,
                        label
                    );
                }

                if (orden.id_cotizacion) {
                    this.ensureOption(
                        "cotizacionesOptions",
                        orden.id_cotizacion,
                        String(orden.id_cotizacion)
                    );
                }
            },
            async fetchCatalogos() {
                if (!(await this.requireAuth())) {
                    return;
                }
                await Promise.all([
                    this.fetchSolicitudes(),
                    this.fetchTecnicos(),
                    this.fetchCotizaciones(),
                ]);
            },
            async fetchSolicitudes() {
                if (this.authError) return;
                this.loadingCatalogos.solicitudes = true;
                try {
                    const params = new URLSearchParams();
                    params.set("per_page", "200");
                    const response = await fetch(
                        "/api/solicitudes?" + params.toString(),
                        { headers: this.apiHeaders() }
                    );
                    if (response.status === 401) {
                        this.handleUnauthorized();
                        return;
                    }
                    if (!response.ok)
                        throw new Error("Error al cargar solicitudes");
                    const json = await response.json();
                    const opciones = (json.data || []).map((item) => ({
                        value: String(item.id_solicitud_pk),
                        label: String(item.id_solicitud_pk),
                    }));
                    this.solicitudesOptions = opciones;
                    this.sortOptions("solicitudesOptions");
                    this.ordenes.forEach((orden) =>
                        this.ensureOrdenOptions(orden)
                    );
                } catch (error) {
                    console.error(error);
                    this.showToast(
                        "No se pudieron cargar las solicitudes",
                        "error"
                    );
                } finally {
                    this.loadingCatalogos.solicitudes = false;
                }
            },
            async fetchTecnicos() {
                if (this.authError) return;
                this.loadingCatalogos.tecnicos = true;
                try {
                    const params = new URLSearchParams();
                    params.set("all", "1");
                    params.set("sort", "nombre");
                    const response = await fetch(
                        "/api/personas?" + params.toString(),
                        { headers: this.apiHeaders() }
                    );
                    if (response.status === 401) {
                        this.handleUnauthorized();
                        return;
                    }
                    if (!response.ok)
                        throw new Error("Error al cargar personas");
                    const json = await response.json();
                    const opciones = (json.data || []).map((item) => {
                        const nombres = [
                            item.primer_nombre,
                            item.segundo_nombre,
                            item.primer_apellido,
                            item.segundo_apellido,
                        ]
                            .filter(Boolean)
                            .join(" ")
                            .trim();
                        return {
                            value: String(item.id),
                            label: nombres || "Persona sin nombre",
                        };
                    });
                    this.tecnicosOptions = opciones;
                    this.sortOptions("tecnicosOptions");
                    this.ordenes.forEach((orden) =>
                        this.ensureOrdenOptions(orden)
                    );
                } catch (error) {
                    console.error(error);
                    this.showToast(
                        "No se pudieron cargar las personas",
                        "error"
                    );
                } finally {
                    this.loadingCatalogos.tecnicos = false;
                }
            },

            async fetchCotizaciones() {
                if (this.authError) return;
                this.loadingCatalogos.cotizaciones = true;
                try {
                    const params = new URLSearchParams();
                    params.set("per_page", "100");
                    params.set("sort", "fecha");
                    params.set("direction", "desc");
                    const response = await fetch(
                        "/api/cotizaciones?" + params.toString(),
                        { headers: this.apiHeaders() }
                    );
                    if (response.status === 401) {
                        this.handleUnauthorized();
                        return;
                    }
                    if (!response.ok)
                        throw new Error("Error al cargar cotizaciones");
                    const json = await response.json();
                    const opciones = (json.data || []).map((item) => ({
                        value: String(item.id_cotizacion_pk),
                        label: String(item.id_cotizacion_pk),
                    }));
                    this.cotizacionesOptions = opciones;
                    this.sortOptions("cotizacionesOptions");
                    this.ordenes.forEach((orden) =>
                        this.ensureOrdenOptions(orden)
                    );
                } catch (error) {
                    console.error(error);
                    this.showToast(
                        "No se pudieron cargar las cotizaciones",
                        "error"
                    );
                } finally {
                    this.loadingCatalogos.cotizaciones = false;
                }
            },
            async fetchOrdenes() {
                if (!(await this.requireAuth()) || this.authError) return;
                this.loadingOrdenes = true;
                try {
                    const params = new URLSearchParams();
                    params.set("per_page", "100");
                    const response = await fetch(
                        "/api/ordenes-servicio?" + params.toString(),
                        { headers: this.apiHeaders() }
                    );
                    if (response.status === 401) {
                        this.handleUnauthorized();
                        return;
                    }
                    if (!response.ok)
                        throw new Error("Error al cargar órdenes");
                    const data = await response.json();
                    this.ordenes = (data.data || []).map((orden) =>
                        this.mapOrden(orden)
                    );
                    this.ordenes.forEach((orden) =>
                        this.ensureOrdenOptions(orden)
                    );
                    this.actualizarTecnicos();
                } catch (error) {
                    console.error(error);
                    this.showToast(
                        "No se pudieron cargar las órdenes de servicio",
                        "error"
                    );
                } finally {
                    this.loadingOrdenes = false;
                }
            },
            openCreateOrden() {
                this.resetForm();
                this.isModalOpen = true;
            },
            openEditOrden(orden) {
                this.errors = {};
                this.ordenToEdit = orden;
                // Carga perezosa del registro actualizado desde la API
                (async () => {
                    try {
                        if (!(await this.requireAuth())) return;
                        const res = await fetch(
                            "/api/ordenes-servicio/" + orden.id,
                            { headers: this.apiHeaders() }
                        );
                        if (res.status === 401) {
                            this.handleUnauthorized();
                            return;
                        }
                        if (!res.ok)
                            throw new Error("Error al cargar la orden");
                        const json = await res.json();
                        const full = json.data || json; // Resource envuelve en data
                        const mapped = this.mapOrden(full);
                        this.ensureOrdenOptions(mapped);
                        this.formOrden = {
                            id: mapped.id,
                            id_solicitud_servicio_fk: mapped.id_solicitud ?? "",
                            id_tecnico_fk: mapped.id_tecnico ?? "",
                            id_estado_orden_servicio_fk: mapped.estado_id
                                ? String(mapped.estado_id)
                                : "",
                            fecha_recepcion: mapped.fecha_recepcion || "",
                            fecha_inicio: mapped.fecha_inicio || "",
                            fecha_finalizacion: mapped.fecha_finalizacion || "",
                            observaciones: mapped.observaciones || "",
                            diagnostico_tecnico:
                                mapped.diagnostico_tecnico || "",
                            diagnostico_cliente:
                                mapped.diagnostico_cliente || "",
                            id_cotizacion_fk: mapped.id_cotizacion ?? "",
                        };
                    } catch (e) {
                        console.error(e);
                        // Fallback a datos en memoria si falla la carga
                        this.formOrden = {
                            id: orden.id,
                            id_solicitud_servicio_fk: orden.id_solicitud ?? "",
                            id_tecnico_fk: orden.id_tecnico ?? "",
                            id_estado_orden_servicio_fk: orden.estado_id
                                ? String(orden.estado_id)
                                : "",
                            fecha_recepcion: orden.fecha_recepcion || "",
                            fecha_inicio: orden.fecha_inicio || "",
                            fecha_finalizacion: orden.fecha_finalizacion || "",
                            observaciones: orden.observaciones || "",
                            diagnostico_tecnico:
                                orden.diagnostico_tecnico || "",
                            diagnostico_cliente:
                                orden.diagnostico_cliente || "",
                            id_cotizacion_fk: orden.id_cotizacion ?? "",
                        };
                    } finally {
                        this.isEditModalOpen = true;
                    }
                })();
            },
            openDeleteOrden(orden) {
                this.ordenToDelete = orden;
                this.isDeleteModalOpen = true;
            },
            detalleUrl(id) {
                const base = detalleBaseUrl || "/admin/detalle-orden";
                return base + "?orden=" + id;
            },

            buildPayload() {
                return {
                    id_solicitud_servicio_fk: this.formOrden
                        .id_solicitud_servicio_fk
                        ? Number(this.formOrden.id_solicitud_servicio_fk)
                        : null,
                    id_tecnico_fk: this.formOrden.id_tecnico_fk
                        ? Number(this.formOrden.id_tecnico_fk)
                        : null,
                    id_estado_orden_servicio_fk: this.formOrden
                        .id_estado_orden_servicio_fk
                        ? Number(this.formOrden.id_estado_orden_servicio_fk)
                        : null,
                    fecha_recepcion: this.formOrden.fecha_recepcion || null,
                    fecha_inicio: this.formOrden.fecha_inicio || null,
                    fecha_finalizacion:
                        this.formOrden.fecha_finalizacion || null,
                    observaciones: this.formOrden.observaciones || null,
                    diagnostico_tecnico:
                        this.formOrden.diagnostico_tecnico || null,
                    diagnostico_cliente:
                        this.formOrden.diagnostico_cliente || null,
                    id_cotizacion_fk: this.formOrden.id_cotizacion_fk
                        ? Number(this.formOrden.id_cotizacion_fk)
                        : null,
                };
            },
            async fetchEstadosOrden() {
                if (this.authError) return;
                this.loadingCatalogos.estadosOrden = true;
                try {
                    const res = await fetch("/api/estados-orden-servicio", {
                        headers: this.apiHeaders(),
                    });
                    if (res.status === 401) {
                        this.handleUnauthorized();
                        return;
                    }
                    if (!res.ok)
                        throw new Error("Error al cargar estados de orden");
                    const json = await res.json();
                    const opciones = (json.data || []).map((e) => {
                        const text = [e.nombre, e.codigo ? `(${e.codigo})` : ""]
                            .filter(Boolean)
                            .join(" ");
                        return { value: String(e.id), label: text };
                    });
                    this.estadosOrdenOptions = opciones;
                    this.sortOptions("estadosOrdenOptions");
                } catch (err) {
                    console.error(err);
                    this.showToast(
                        "No se pudieron cargar los estados de orden",
                        "error"
                    );
                } finally {
                    this.loadingCatalogos.estadosOrden = false;
                }
            },
            async createOrden() {
                if (!(await this.requireAuth()) || this.authError) return;
                if (this.saving) return;
                this.saving = true;
                this.errors = {};
                try {
                    const payload = this.buildPayload();
                    const response = await fetch("/api/ordenes-servicio", {
                        method: "POST",
                        headers: this.apiHeaders(),
                        body: JSON.stringify(payload),
                    });
                    if (response.status === 401) {
                        this.handleUnauthorized();
                        return;
                    }
                    if (response.status === 422) {
                        const errorData = await response.json();
                        this.errors = errorData.errors || {};
                        throw new Error("Validación");
                    }
                    if (!response.ok)
                        throw new Error("Error al crear la orden");
                    const data = await response.json();
                    if (data.data) {
                        const mapped = this.mapOrden(data.data);
                        this.ensureOrdenOptions(mapped);
                        this.ordenes.unshift(mapped);
                        this.actualizarTecnicos();
                    }
                    this.showToast("Orden de servicio creada correctamente");
                    this.isModalOpen = false;
                    this.resetForm();
                } catch (error) {
                    console.error(error);
                    if (error.message !== "Validación")
                        this.showToast("No se pudo crear la orden", "error");
                } finally {
                    this.saving = false;
                }
            },
            async updateOrden() {
                if (!(await this.requireAuth()) || this.authError) return;
                if (!this.formOrden.id || this.saving) return;
                this.saving = true;
                this.errors = {};
                try {
                    const payload = this.buildPayload();
                    const response = await fetch(
                        "/api/ordenes-servicio/" + this.formOrden.id,
                        {
                            method: "PUT",
                            headers: this.apiHeaders(),
                            body: JSON.stringify(payload),
                        }
                    );
                    if (response.status === 401) {
                        this.handleUnauthorized();
                        return;
                    }
                    if (response.status === 422) {
                        const errorData = await response.json();
                        this.errors = errorData.errors || {};
                        throw new Error("Validación");
                    }
                    if (!response.ok)
                        throw new Error("Error al actualizar la orden");
                    const data = await response.json();
                    if (data.data) {
                        const mapped = this.mapOrden(data.data);
                        this.ensureOrdenOptions(mapped);
                        const index = this.ordenes.findIndex(
                            (orden) => orden.id === this.formOrden.id
                        );
                        if (index !== -1) {
                            this.ordenes.splice(index, 1, mapped);
                        }
                        this.actualizarTecnicos();
                    }
                    this.showToast("Orden de servicio actualizada");
                    this.isEditModalOpen = false;
                    this.ordenToEdit = null;
                    this.resetForm();
                } catch (error) {
                    console.error(error);
                    if (error.message !== "Validación")
                        this.showToast(
                            "No se pudo actualizar la orden",
                            "error"
                        );
                } finally {
                    this.saving = false;
                }
            },
            async performDeleteOrden() {
                if (!(await this.requireAuth()) || this.authError) return;
                if (!this.ordenToDelete || this.deleting) return;
                this.deleting = true;
                try {
                    const response = await fetch(
                        "/api/ordenes-servicio/" + this.ordenToDelete.id,
                        { method: "DELETE", headers: this.apiHeaders() }
                    );
                    if (response.status === 401) {
                        this.handleUnauthorized();
                        return;
                    }
                    if (!response.ok)
                        throw new Error("Error al eliminar la orden");
                    this.ordenes = this.ordenes.filter(
                        (orden) => orden.id !== this.ordenToDelete.id
                    );
                    this.actualizarTecnicos();
                    this.showToast("Orden de servicio eliminada");
                } catch (error) {
                    console.error(error);
                    this.showToast("No se pudo eliminar la orden", "error");
                } finally {
                    this.deleting = false;
                    this.isDeleteModalOpen = false;
                    this.ordenToDelete = null;
                }
            },
            submitOrden() {
                if (this.formOrden.id) this.updateOrden();
                else this.createOrden();
            },
            filteredOrdenes() {
                const term = this.searchOrden.trim().toLowerCase();
                return this.ordenes
                    .filter((orden) => {
                        if (!this.tecnicoOrden) return true;
                        return (
                            String(orden.id_tecnico) ===
                            String(this.tecnicoOrden)
                        );
                    })
                    .filter((orden) => {
                        if (!term) return true;
                        return [
                            orden.numero,
                            orden.id,
                            orden.id_solicitud,
                            orden.numero_solicitud,
                            orden.tecnico_nombre,
                            orden.estado,
                            orden.cliente_nombre,
                            orden.observaciones,
                            orden.diagnostico_cliente,
                            orden.diagnostico_tecnico,
                        ]
                            .filter(Boolean)
                            .some((field) =>
                                field.toString().toLowerCase().includes(term)
                            );
                    })
                    .sort((a, b) => {
                        switch (this.ordenarPor) {
                            case "id":
                                return Number(a.id) - Number(b.id);
                            case "fecha_recepcion":
                                return (
                                    new Date(
                                        a.fecha_recepcion || "1970-01-01"
                                    ) -
                                    new Date(b.fecha_recepcion || "1970-01-01")
                                );
                            case "fecha_inicio":
                                return (
                                    new Date(a.fecha_inicio || "1970-01-01") -
                                    new Date(b.fecha_inicio || "1970-01-01")
                                );
                            case "fecha_finalizacion":
                                return (
                                    new Date(
                                        a.fecha_finalizacion || "1970-01-01"
                                    ) -
                                    new Date(
                                        b.fecha_finalizacion || "1970-01-01"
                                    )
                                );
                            default:
                                return 0;
                        }
                    });
            },
            toggleRow(id) {
                this.expandedRows[id] = !this.expandedRows[id];
            },
            async init() {
                if (!(await this.requireAuth())) return;
                await Promise.all([
                    this.fetchCatalogos(),
                    this.fetchEstadosOrden(),
                    this.fetchOrdenes(),
                ]);
            },
        };
    };
}

// Alpine component factory for Gestión de Solicitudes (Admin)
if (typeof window !== "undefined") {
    window.gestionSolicitudes = function () {
        return {
            // Tabs and filters
            tab: "solicitudes",
            searchSolicitud: "",
            estadoSolicitud: "",
            ordenarPor: "id",
            searchContacto: "",

            // Modals state
            isModalOpen: false,
            isEditModalOpen: false,
            isDeleteModalOpen: false,
            solicitudToEdit: null,
            solicitudToDelete: null,

            // Estados modals (not wired yet; reserved for future use)
            isEstadoModalOpen: false,
            isEditEstadoModalOpen: false,
            estadoToEdit: null,
            isDeleteEstadoModalOpen: false,
            estadoToDelete: null,

            // Contactos modals/state
            isContactoModalOpen: false,
            isEditContactoModalOpen: false,
            isDeleteContactoModalOpen: false,
            contactoToEdit: null,
            contactoToDelete: null,

            // Data collections
            solicitudes: [],
            contactos: [],
            clientesOptions: [],
            estadosOptions: [],
            contactosOptions: [], // full list for selects; filtered per cliente when rendering

            // Forms and flags
            formSolicitud: {
                id: null,
                id_cliente_fk: "",
                descripcion_problema: "",
                id_estado_solicitud_fk: "",
                id_contacto_fk: "",
            },
            formContacto: {
                id: null,
                tipo_contacto: "",
                valor_contacto: "",
                id_cliente_fk: "",
            },
            errors: {},
            saving: false,
            deleting: false,
            loadingSolicitudes: false,
            loadingCatalogos: {
                clientes: false,
                estados: false,
                contactos: false,
            },
            loadingContactos: false,

            // Auth helpers (same pattern as órdenes)
            getToken() {
                try {
                    return (
                        window.__AUTH?.getToken?.() ||
                        localStorage.getItem("authToken")
                    );
                } catch (_) {
                    return null;
                }
            },
            setToken(token) {
                try {
                    if (window.__AUTH?.setToken) window.__AUTH.setToken(token);
                    else if (token) localStorage.setItem("authToken", token);
                } catch (_) {}
                return token;
            },
            getCsrf() {
                const m = document.head.querySelector(
                    'meta[name="csrf-token"]'
                );
                return m ? m.content : "";
            },
            apiHeaders() {
                const t = this.getToken();
                const h = {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                };
                if (t) h["Authorization"] = "Bearer " + t;
                return h;
            },
            async requireAuth() {
                const existing = this.getToken();
                if (existing) return true;
                try {
                    const t =
                        typeof window.__AUTH?.ensureToken === "function"
                            ? await window.__AUTH.ensureToken(false)
                            : null;
                    if (t) return true;
                    const res = await fetch("/session/token", {
                        method: "GET",
                        headers: {
                            Accept: "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            "X-CSRF-TOKEN": this.getCsrf(),
                        },
                        credentials: "same-origin",
                    });
                    if (!res.ok) return false;
                    const data = await res.json();
                    if (data && (data.token || data.access_token)) {
                        this.setToken(data.token || data.access_token);
                        return true;
                    }
                } catch (_) {}
                return false;
            },

            showToast(message, type = "ok") {
                const el = document.createElement("div");
                el.className =
                    "fixed top-4 right-4 z-50 px-4 py-2 rounded shadow text-sm " +
                    (type === "error"
                        ? "bg-red-600 text-white"
                        : type === "warn"
                        ? "bg-yellow-600 text-white"
                        : "bg-green-600 text-white");
                el.textContent = message;
                document.body.appendChild(el);
                setTimeout(() => el.remove(), 3500);
            },

            resetSolicitudForm() {
                this.formSolicitud = {
                    id: null,
                    id_cliente_fk: "",
                    descripcion_problema: "",
                    id_estado_solicitud_fk: "",
                    id_contacto_fk: "",
                };
                this.errors = {};
            },
            resetContactoForm() {
                this.formContacto = {
                    id: null,
                    tipo_contacto: "",
                    valor_contacto: "",
                    id_cliente_fk: "",
                };
                this.errors = {};
            },

            // Mapping helpers
            mapSolicitud(item) {
                const cliente = item.cliente || {};
                const empresa = cliente.empresa || {};
                const estado = item.estado_solicitud || {};
                const contacto = item.contacto || {};
                return {
                    id: item.id_solicitud_pk,
                    id_cliente_fk: item.id_cliente_fk,
                    cliente_nombre:
                        empresa.nombre_comercial || empresa.razon_social || "",
                    numero_solicitud_acf: item.numero_solicitud_acf,
                    numero_solicitud_cliente: item.numero_solicitud_cliente,
                    descripcion_problema: item.descripcion_problema,
                    id_estado_solicitud_fk: item.id_estado_solicitud_fk,
                    estado_nombre: estado.nombre_estado || "",
                    id_contacto_fk: item.id_contacto_fk,
                    contacto_valor: contacto.valor_contacto || "",
                };
            },

            // Catalogs
            async fetchClientes() {
                this.loadingCatalogos.clientes = true;
                try {
                    const params = new URLSearchParams();
                    params.set("per_page", "200");
                    const res = await fetch(
                        "/api/empresas-cliente?" + params.toString(),
                        { headers: this.apiHeaders() }
                    );
                    if (!res.ok) throw new Error("Error clientes");
                    const json = await res.json();
                    const items = json.data || json?.data?.data || json; // resource wrapper or plain
                    const options = (items.data || items).map((it) => ({
                        value: String(it.id_cliente_fk ?? it.id),
                        label:
                            it.nombre_comercial ||
                            it.razon_social ||
                            `Cliente ${it.id_cliente_fk ?? it.id}`,
                    }));
                    this.clientesOptions = options;
                    this.clientesOptions.sort((a, b) =>
                        a.label.localeCompare(b.label, "es")
                    );
                } catch (e) {
                    console.error(e);
                    this.showToast(
                        "No se pudieron cargar los clientes",
                        "error"
                    );
                } finally {
                    this.loadingCatalogos.clientes = false;
                }
            },
            async fetchEstados() {
                this.loadingCatalogos.estados = true;
                try {
                    const params = new URLSearchParams();
                    params.set("per_page", "200");
                    const res = await fetch(
                        "/api/estados-solicitud?" + params.toString(),
                        { headers: this.apiHeaders() }
                    );
                    if (!res.ok) throw new Error("Error estados");
                    const json = await res.json();
                    const options = (json.data || []).map((it) => ({
                        value: String(it.id),
                        label: it.nombre_estado || `Estado ${it.id}`,
                    }));
                    this.estadosOptions = options;
                    this.estadosOptions.sort((a, b) =>
                        a.label.localeCompare(b.label, "es")
                    );
                } catch (e) {
                    console.error(e);
                    this.showToast(
                        "No se pudieron cargar los estados",
                        "error"
                    );
                } finally {
                    this.loadingCatalogos.estados = false;
                }
            },
            async fetchContactosCatalogo() {
                this.loadingCatalogos.contactos = true;
                try {
                    const params = new URLSearchParams();
                    params.set("per_page", "200");
                    const res = await fetch(
                        "/api/contactos?" + params.toString(),
                        { headers: this.apiHeaders() }
                    );
                    if (!res.ok) throw new Error("Error contactos");
                    const json = await res.json();
                    const items = json.data || [];
                    this.contactosOptions = items.map((it) => ({
                        value: String(it.id_contacto_pk || it.id),
                        label: `${
                            it.valor_contacto || it.tipo_contacto || "Contacto"
                        } (ID ${it.id_contacto_pk || it.id})`,
                        id_cliente_fk: String(it.id_cliente_fk || ""),
                    }));
                } catch (e) {
                    console.error(e);
                    this.showToast(
                        "No se pudieron cargar los contactos",
                        "error"
                    );
                } finally {
                    this.loadingCatalogos.contactos = false;
                }
            },
            filteredContactosForSelectedCliente() {
                const idCliente = String(
                    this.formSolicitud.id_cliente_fk || ""
                );
                if (!idCliente) return this.contactosOptions;
                return this.contactosOptions.filter(
                    (c) => String(c.id_cliente_fk || "") === idCliente
                );
            },

            // CRUD Solicitudes
            async fetchSolicitudes() {
                if (!(await this.requireAuth())) return;
                this.loadingSolicitudes = true;
                try {
                    const params = new URLSearchParams();
                    params.set("per_page", "100");
                    const res = await fetch(
                        "/api/solicitudes?" + params.toString(),
                        { headers: this.apiHeaders() }
                    );
                    if (!res.ok) throw new Error("Error solicitudes");
                    const json = await res.json();
                    this.solicitudes = (json.data || []).map((it) =>
                        this.mapSolicitud(it)
                    );
                } catch (e) {
                    console.error(e);
                    this.showToast(
                        "No se pudieron cargar las solicitudes",
                        "error"
                    );
                } finally {
                    this.loadingSolicitudes = false;
                }
            },
            buildSolicitudPayload() {
                return {
                    id_cliente_fk: this.formSolicitud.id_cliente_fk
                        ? Number(this.formSolicitud.id_cliente_fk)
                        : null,
                    descripcion_problema:
                        this.formSolicitud.descripcion_problema || null,
                    id_estado_solicitud_fk: this.formSolicitud
                        .id_estado_solicitud_fk
                        ? Number(this.formSolicitud.id_estado_solicitud_fk)
                        : null,
                    id_contacto_fk: this.formSolicitud.id_contacto_fk
                        ? Number(this.formSolicitud.id_contacto_fk)
                        : null,
                };
            },
            openCreateSolicitud() {
                this.resetSolicitudForm();
                this.isModalOpen = true;
            },
            openEditSolicitud(item) {
                this.errors = {};
                this.solicitudToEdit = item;
                this.formSolicitud = {
                    id: item.id,
                    id_cliente_fk: item.id_cliente_fk ?? "",
                    descripcion_problema: item.descripcion_problema ?? "",
                    id_estado_solicitud_fk: item.id_estado_solicitud_fk ?? "",
                    id_contacto_fk: item.id_contacto_fk ?? "",
                };
                this.isEditModalOpen = true;
            },
            openDeleteSolicitud(item) {
                this.solicitudToDelete = item;
                this.isDeleteModalOpen = true;
            },
            async createSolicitud() {
                if (!(await this.requireAuth()) || this.saving) return;
                this.saving = true;
                this.errors = {};
                try {
                    const payload = this.buildSolicitudPayload();
                    const res = await fetch("/api/solicitudes", {
                        method: "POST",
                        headers: this.apiHeaders(),
                        body: JSON.stringify(payload),
                    });
                    if (res.status === 422) {
                        const err = await res.json();
                        this.errors = err.errors || {};
                        throw new Error("Validación");
                    }
                    if (!res.ok) throw new Error("Error al crear la solicitud");
                    const json = await res.json();
                    if (json.data)
                        this.solicitudes.unshift(this.mapSolicitud(json.data));
                    this.showToast("Solicitud creada correctamente");
                    this.isModalOpen = false;
                    this.resetSolicitudForm();
                } catch (e) {
                    console.error(e);
                    if (e.message !== "Validación")
                        this.showToast(
                            "No se pudo crear la solicitud",
                            "error"
                        );
                } finally {
                    this.saving = false;
                }
            },
            async updateSolicitud() {
                if (
                    !(await this.requireAuth()) ||
                    !this.formSolicitud.id ||
                    this.saving
                )
                    return;
                this.saving = true;
                this.errors = {};
                try {
                    const payload = this.buildSolicitudPayload();
                    const res = await fetch(
                        `/api/solicitudes/${this.formSolicitud.id}`,
                        {
                            method: "PUT",
                            headers: this.apiHeaders(),
                            body: JSON.stringify(payload),
                        }
                    );
                    if (res.status === 422) {
                        const err = await res.json();
                        this.errors = err.errors || {};
                        throw new Error("Validación");
                    }
                    if (!res.ok)
                        throw new Error("Error al actualizar la solicitud");
                    const json = await res.json();
                    if (json.data) {
                        const mapped = this.mapSolicitud(json.data);
                        const idx = this.solicitudes.findIndex(
                            (s) => s.id === this.formSolicitud.id
                        );
                        if (idx !== -1) this.solicitudes.splice(idx, 1, mapped);
                    }
                    this.showToast("Solicitud actualizada");
                    this.isEditModalOpen = false;
                    this.solicitudToEdit = null;
                    this.resetSolicitudForm();
                } catch (e) {
                    console.error(e);
                    if (e.message !== "Validación")
                        this.showToast(
                            "No se pudo actualizar la solicitud",
                            "error"
                        );
                } finally {
                    this.saving = false;
                }
            },
            async performDeleteSolicitud() {
                if (
                    !(await this.requireAuth()) ||
                    !this.solicitudToDelete ||
                    this.deleting
                )
                    return;
                this.deleting = true;
                try {
                    const res = await fetch(
                        `/api/solicitudes/${this.solicitudToDelete.id}`,
                        { method: "DELETE", headers: this.apiHeaders() }
                    );
                    if (!res.ok)
                        throw new Error("Error al eliminar la solicitud");
                    this.solicitudes = this.solicitudes.filter(
                        (s) => s.id !== this.solicitudToDelete.id
                    );
                    this.showToast("Solicitud eliminada");
                } catch (e) {
                    console.error(e);
                    this.showToast("No se pudo eliminar la solicitud", "error");
                } finally {
                    this.deleting = false;
                    this.isDeleteModalOpen = false;
                    this.solicitudToDelete = null;
                }
            },
            submitSolicitud() {
                if (this.formSolicitud.id) this.updateSolicitud();
                else this.createSolicitud();
            },

            // Contactos list + CRUD (simple)
            async fetchContactos() {
                if (!(await this.requireAuth())) return;
                this.loadingContactos = true;
                try {
                    const params = new URLSearchParams();
                    params.set("per_page", "100");
                    const res = await fetch(
                        "/api/contactos?" + params.toString(),
                        { headers: this.apiHeaders() }
                    );
                    if (!res.ok) throw new Error("Error contactos");
                    const json = await res.json();
                    this.contactos = (json.data || []).map((it) => ({
                        id: it.id_contacto_pk || it.id,
                        tipo_contacto: it.tipo_contacto,
                        valor_contacto: it.valor_contacto,
                        id_cliente_fk: it.id_cliente_fk,
                    }));
                } catch (e) {
                    console.error(e);
                    this.showToast(
                        "No se pudieron cargar los contactos",
                        "error"
                    );
                } finally {
                    this.loadingContactos = false;
                }
            },
            openCreateContacto() {
                this.resetContactoForm();
                this.isContactoModalOpen = true;
            },
            openEditContacto(item) {
                this.errors = {};
                this.contactoToEdit = item;
                this.formContacto = {
                    id: item.id,
                    tipo_contacto: item.tipo_contacto || "",
                    valor_contacto: item.valor_contacto || "",
                    id_cliente_fk: item.id_cliente_fk || "",
                };
                this.isEditContactoModalOpen = true;
            },
            openDeleteContacto(item) {
                this.contactoToDelete = item;
                this.isDeleteContactoModalOpen = true;
            },
            async createContacto() {
                if (!(await this.requireAuth()) || this.saving) return;
                this.saving = true;
                this.errors = {};
                try {
                    const payload = {
                        tipo_contacto: this.formContacto.tipo_contacto || null,
                        valor_contacto:
                            this.formContacto.valor_contacto || null,
                        id_cliente_fk: this.formContacto.id_cliente_fk
                            ? Number(this.formContacto.id_cliente_fk)
                            : null,
                    };
                    const res = await fetch("/api/contactos", {
                        method: "POST",
                        headers: this.apiHeaders(),
                        body: JSON.stringify(payload),
                    });
                    if (res.status === 422) {
                        const err = await res.json();
                        this.errors = err.errors || {};
                        throw new Error("Validación");
                    }
                    if (!res.ok) throw new Error("Error al crear contacto");
                    const json = await res.json();
                    if (json.data)
                        this.contactos.unshift({
                            id: json.data.id_contacto_pk || json.data.id,
                            tipo_contacto: json.data.tipo_contacto,
                            valor_contacto: json.data.valor_contacto,
                            id_cliente_fk: json.data.id_cliente_fk,
                        });
                    this.showToast("Contacto creado");
                    this.isContactoModalOpen = false;
                    this.resetContactoForm();
                } catch (e) {
                    console.error(e);
                    if (e.message !== "Validación")
                        this.showToast("No se pudo crear el contacto", "error");
                } finally {
                    this.saving = false;
                }
            },
            async updateContacto() {
                if (
                    !(await this.requireAuth()) ||
                    !this.formContacto.id ||
                    this.saving
                )
                    return;
                this.saving = true;
                this.errors = {};
                try {
                    const payload = {
                        tipo_contacto: this.formContacto.tipo_contacto || null,
                        valor_contacto:
                            this.formContacto.valor_contacto || null,
                        id_cliente_fk: this.formContacto.id_cliente_fk
                            ? Number(this.formContacto.id_cliente_fk)
                            : null,
                    };
                    const res = await fetch(
                        `/api/contactos/${this.formContacto.id}`,
                        {
                            method: "PUT",
                            headers: this.apiHeaders(),
                            body: JSON.stringify(payload),
                        }
                    );
                    if (res.status === 422) {
                        const err = await res.json();
                        this.errors = err.errors || {};
                        throw new Error("Validación");
                    }
                    if (!res.ok)
                        throw new Error("Error al actualizar contacto");
                    const json = await res.json();
                    if (json.data) {
                        const updated = {
                            id: json.data.id_contacto_pk || json.data.id,
                            tipo_contacto: json.data.tipo_contacto,
                            valor_contacto: json.data.valor_contacto,
                            id_cliente_fk: json.data.id_cliente_fk,
                        };
                        const idx = this.contactos.findIndex(
                            (c) => c.id === this.formContacto.id
                        );
                        if (idx !== -1) this.contactos.splice(idx, 1, updated);
                    }
                    this.showToast("Contacto actualizado");
                    this.isEditContactoModalOpen = false;
                    this.contactoToEdit = null;
                    this.resetContactoForm();
                } catch (e) {
                    console.error(e);
                    if (e.message !== "Validación")
                        this.showToast(
                            "No se pudo actualizar el contacto",
                            "error"
                        );
                } finally {
                    this.saving = false;
                }
            },
            async performDeleteContacto() {
                if (
                    !(await this.requireAuth()) ||
                    !this.contactoToDelete ||
                    this.deleting
                )
                    return;
                this.deleting = true;
                try {
                    const res = await fetch(
                        `/api/contactos/${this.contactoToDelete.id}`,
                        { method: "DELETE", headers: this.apiHeaders() }
                    );
                    if (!res.ok) throw new Error("Error al eliminar contacto");
                    this.contactos = this.contactos.filter(
                        (c) => c.id !== this.contactoToDelete.id
                    );
                    this.showToast("Contacto eliminado");
                } catch (e) {
                    console.error(e);
                    this.showToast("No se pudo eliminar el contacto", "error");
                } finally {
                    this.deleting = false;
                    this.isDeleteContactoModalOpen = false;
                    this.contactoToDelete = null;
                }
            },
            submitContacto() {
                if (this.formContacto.id) this.updateContacto();
                else this.createContacto();
            },

            // Derived collections and filters
            filteredSolicitudes() {
                const term = this.searchSolicitud.trim().toLowerCase();
                const estadoSel = this.estadoSolicitud
                    ? String(this.estadoSolicitud).toLowerCase()
                    : "";
                return this.solicitudes
                    .filter((s) => {
                        if (!estadoSel) return true;
                        const byId =
                            String(
                                s.id_estado_solicitud_fk || ""
                            ).toLowerCase() === estadoSel;
                        const byName =
                            (s.estado_nombre || "").toLowerCase() === estadoSel;
                        return byId || byName;
                    })
                    .filter((s) => {
                        if (!term) return true;
                        return [
                            s.id,
                            s.cliente_nombre,
                            s.numero_solicitud_acf,
                            s.numero_solicitud_cliente,
                            s.descripcion_problema,
                            s.estado_nombre,
                        ]
                            .filter(Boolean)
                            .some((f) =>
                                f.toString().toLowerCase().includes(term)
                            );
                    })
                    .sort((a, b) => {
                        switch (this.ordenarPor) {
                            case "id":
                                return Number(a.id) - Number(b.id);
                            case "estado":
                            case "estado_solicitud":
                                return (a.estado_nombre || "").localeCompare(
                                    b.estado_nombre || "",
                                    "es"
                                );
                            case "fecha_creacion":
                                // Campo no disponible: mantener orden estable
                                return 0;
                            default:
                                return 0;
                        }
                    });
            },

            // Init
            async init() {
                if (!(await this.requireAuth())) return;
                await Promise.all([
                    this.fetchClientes(),
                    this.fetchEstados(),
                    this.fetchContactosCatalogo(),
                    this.fetchSolicitudes(),
                    this.fetchContactos(),
                ]);
            },
        };
    };
}
