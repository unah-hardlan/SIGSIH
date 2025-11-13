import "./bootstrap";
import "./notifications";

try {
    if (!window.__ALLOW_CONSOLE__) {
        const noop = () => {};
        ["log", "info", "debug", "warn", "error"].forEach((m) => {
            try {
                console[m] = noop;
            } catch (_) {}
        });
    }
} catch (_) {}

if (!window.__FETCH_LIMITER_INSTALLED__) {
    window.__FETCH_LIMITER_INSTALLED__ = true;

    (function installFetchLimiter() {
        const origFetch = window.fetch.bind(window);
        const maxConcurrent = 6;
        let inFlight = 0;
        const queue = [];

        let tokenPromise = null;

        function getToken() {
            return null;
        }
        function setToken(_) {
            try {
                if (window.axios && window.axios.defaults?.headers?.common) {
                    delete window.axios.defaults.headers.common[
                        "Authorization"
                    ];
                }
            } catch (_) {}
            try {
                document.dispatchEvent(
                    new CustomEvent("auth:updated", { detail: { token: null } })
                );
            } catch (_) {}
        }

        async function fetchSessionToken(force = false) {
            if (!force) return null;

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

                        await res.json().catch(() => null);
                        return null;
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
                    return { Accept: "application/json" };
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
                        let [input, init] = withAuthToApi(args[0], args[1]);
                        let res = await origFetch(input, init);
                        if (res.status === 401) {
                            [input, init] = withAuthToApi(args[0], args[1]);
                            res = await origFetch(input, init);
                        }

                        if (res.status === 401) {
                            try {
                                const clone = res.clone();
                                const data = await clone
                                    .json()
                                    .catch(() => null);
                                if (
                                    data &&
                                    data.code === "SESSION_REMOVED_LIMIT"
                                ) {
                                    try {
                                        window.showToast &&
                                            window.showToast(
                                                "Se superó el límite de sesiones. Esta sesión se cerró para respetar el máximo permitido.",
                                                "warning",
                                                { duration: 4000 }
                                            );
                                    } catch (_) {}
                                    try {
                                        window.appLogout && window.appLogout();
                                    } catch (_) {}
                                }
                            } catch (_) {}
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
import "./gestion-db";
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
import "./helpers/subdivisiones";
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
import "./detalle-factura";
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
import "./tipo-mantenimiento";
import "./reportes-visita";
import "./calendario";
import "./tickets";
import "./empresas";

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
    faArrowUp,
    faArrowDown,
    faBalanceScale,
    faInbox,
    faSortDown,
    faSort,
    faSortUp,
    faIdCard,
    faFolderOpen,
    faConciergeBell,
    faFlag,
    faTags,
    faHeadset,
    faHome,
    faExternalLinkAlt,
    faExclamationCircle,
    faStar,
    faExchangeAlt,
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
    faExclamationTriangle,
    faArrowUp,
    faArrowDown,
    faBalanceScale,
    faInbox,
    faSortDown,
    faSortUp,
    faIdCard,
    faFolderOpen,
    faIdCard,
    faFolderOpen,
    faSort,
    faConciergeBell,
    faFlag,
    faTags,
    faHeadset,
    faExternalLinkAlt,
    faExclamationCircle,
    faStar,
    faExchangeAlt,
    faHome
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
        const max = 20;
        const delay = 150;
        if (!el) return;
        const rect = el.getBoundingClientRect();
        const ready =
            rect.width > 10 && rect.height > 10 && el.offsetParent !== null;
        if (ready) return cb();
        if (attempt >= max) return cb();
        setTimeout(() => waitForCanvasReady(el, cb, attempt + 1), delay);
    };

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

if (typeof window !== "undefined") {
    window.settingsState = function () {
        const initial = {
            appLogoUrl: "/images/logo.png",
            appName: "Hardlan",
            appLogoHeight: 96,
        };
        return {
            tab: localStorage.getItem("mantenimientoTab") || "personalizacion",
            logoUrl: initial.appLogoUrl,
            nombreSistema: initial.appName,
            logoHeight: Number(initial.appLogoHeight) || 96,
            selectedLogoFile: null,
            savedMessagePersonalizacion: "",
            savedMessageParametros: "",
            timezone: "UTC",
            dateFormat: "Y-m-d",
            sessionsLimit: 1,
            requireEmailVerification: false,
            passwordResetCooldown: 5,
            passwordResetExpire: 60,
            passwordResetMaxPerDay: 5,
            dniFormat: "0000-0000-00000",
            adminIntentos: 3,
            adminCorreo: "",
            adminUsuario: "",
            adminPassword: "",
            async init() {
                try {
                    const res = await fetch("/api-web/system-settings", {
                        credentials: "same-origin",
                    });
                    if (res.ok) {
                        const data = await res.json();
                        this.nombreSistema = data.appName || this.nombreSistema;
                        this.logoUrl = data.logoUrl || this.logoUrl;
                        this.logoHeight = data.logoHeight || this.logoHeight;
                        this.timezone = data.timezone || this.timezone;
                        this.dateFormat = data.dateFormat || this.dateFormat;
                        this.sessionsLimit =
                            data.sessionsLimit || this.sessionsLimit;
                        this.requireEmailVerification =
                            !!data.requireEmailVerification;
                        this.passwordResetCooldown =
                            data.passwordResetCooldown ??
                            this.passwordResetCooldown;
                        this.passwordResetExpire =
                            data.passwordResetExpire ??
                            this.passwordResetExpire;
                        this.passwordResetMaxPerDay =
                            data.passwordResetMaxPerDay ??
                            this.passwordResetMaxPerDay;
                        this.dniFormat = (
                            data.dniFormat ||
                            this.dniFormat ||
                            ""
                        ).toString();
                        this.adminIntentos =
                            data.adminIntentos || this.adminIntentos;
                        this.adminCorreo = data.adminCorreo || this.adminCorreo;
                        this.adminUsuario =
                            data.adminUsuario || this.adminUsuario;
                        this.adminPassword =
                            data.adminPassword || this.adminPassword;
                    }
                } catch (_) {}
            },
            onLogoSelected(e) {
                const file = e.target.files?.[0];
                if (!file) return;
                this.selectedLogoFile = file;
                const reader = new FileReader();
                reader.onload = (ev) => {
                    this.logoUrl = ev.target?.result;
                };
                reader.readAsDataURL(file);
            },
            async guardarPersonalizacion() {
                const fd = new FormData();
                if (this.nombreSistema)
                    fd.append("app_name", this.nombreSistema);
                if (this.selectedLogoFile)
                    fd.append("logo", this.selectedLogoFile);
                if (this.logoHeight)
                    fd.append("logo_height", String(this.logoHeight));
                try {
                    const res = await fetch("/api-web/system-settings", {
                        method: "POST",
                        body: fd,
                        credentials: "same-origin",
                        headers: {
                            "X-CSRF-TOKEN":
                                document
                                    .querySelector('meta[name="csrf-token"]')
                                    ?.getAttribute("content") || "",
                        },
                    });
                    if (!res.ok) throw new Error("bad");
                    const data = await res.json();
                    this.nombreSistema = data.appName || this.nombreSistema;
                    this.logoUrl = data.logoUrl || this.logoUrl;
                    this.logoHeight = data.logoHeight || this.logoHeight;
                    this.selectedLogoFile = null;
                    this.savedMessagePersonalizacion =
                        "Personalización guardada correctamente";
                    try {
                        const headerLogo = document.querySelector(
                            'header img[alt="Logo"]'
                        );
                        if (headerLogo) {
                            if (this.logoUrl) headerLogo.src = this.logoUrl;
                            if (this.logoHeight)
                                headerLogo.style.setProperty(
                                    "--app-logo-max",
                                    `${this.logoHeight}px`
                                );
                        }
                        if (this.nombreSistema) {
                            document.title = this.nombreSistema;
                        }
                    } catch (_) {}
                    setTimeout(
                        () => (this.savedMessagePersonalizacion = ""),
                        2500
                    );
                } catch (e) {
                    this.savedMessagePersonalizacion = "No se pudo guardar";
                    setTimeout(
                        () => (this.savedMessagePersonalizacion = ""),
                        2500
                    );
                }
            },
            async guardarParametros() {
                const fd = new FormData();
                if (this.timezone) fd.append("timezone", this.timezone);
                if (this.dateFormat) fd.append("date_format", this.dateFormat);
                if (this.sessionsLimit)
                    fd.append("sessions_limit", String(this.sessionsLimit));
                fd.append(
                    "require_email_verification",
                    this.requireEmailVerification ? "1" : "0"
                );
                if (
                    this.passwordResetCooldown !== undefined &&
                    this.passwordResetCooldown !== null
                )
                    fd.append(
                        "password_reset_cooldown",
                        String(this.passwordResetCooldown)
                    );
                if (
                    this.passwordResetExpire !== undefined &&
                    this.passwordResetExpire !== null
                )
                    fd.append(
                        "password_reset_expire",
                        String(this.passwordResetExpire)
                    );
                if (
                    this.passwordResetMaxPerDay !== undefined &&
                    this.passwordResetMaxPerDay !== null
                )
                    fd.append(
                        "password_reset_max_per_day",
                        String(this.passwordResetMaxPerDay)
                    );
                if (this.dniFormat) fd.append("dni_format", this.dniFormat);
                if (this.adminIntentos)
                    fd.append("admin_intentos", String(this.adminIntentos));
                if (this.adminCorreo)
                    fd.append("admin_correo", this.adminCorreo);
                if (this.adminUsuario)
                    fd.append("admin_usuario", this.adminUsuario);
                if (this.adminPassword)
                    fd.append("admin_password", this.adminPassword);
                try {
                    const res = await fetch("/api-web/system-settings", {
                        method: "POST",
                        body: fd,
                        credentials: "same-origin",
                        headers: {
                            "X-CSRF-TOKEN":
                                document
                                    .querySelector('meta[name="csrf-token"]')
                                    ?.getAttribute("content") || "",
                        },
                    });
                    if (!res.ok) throw new Error("bad");
                    const data = await res.json();
                    this.timezone = data.timezone || this.timezone;
                    this.dateFormat = data.dateFormat || this.dateFormat;
                    this.sessionsLimit =
                        data.sessionsLimit || this.sessionsLimit;
                    this.requireEmailVerification =
                        !!data.requireEmailVerification;
                    this.passwordResetCooldown =
                        data.passwordResetCooldown ??
                        this.passwordResetCooldown;
                    this.passwordResetExpire =
                        data.passwordResetExpire ?? this.passwordResetExpire;
                    this.passwordResetMaxPerDay =
                        data.passwordResetMaxPerDay ??
                        this.passwordResetMaxPerDay;
                    this.dniFormat = (
                        data.dniFormat ||
                        this.dniFormat ||
                        ""
                    ).toString();
                    this.adminIntentos =
                        data.adminIntentos || this.adminIntentos;
                    this.adminCorreo = data.adminCorreo || this.adminCorreo;
                    this.adminUsuario = data.adminUsuario || this.adminUsuario;
                    this.adminPassword =
                        data.adminPassword || this.adminPassword;
                    this.savedMessageParametros =
                        "Parámetros guardados correctamente";
                    setTimeout(() => (this.savedMessageParametros = ""), 2500);
                } catch (e) {
                    this.savedMessageParametros = "No se pudo guardar";
                    setTimeout(() => (this.savedMessageParametros = ""), 2500);
                }
            },
        };
    };
}

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

            numbers: [],
            currentPage: 1,
            perPage: 10,
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
            validationErrors: {},
            formOrden: {
                id: null,
                id_solicitud_servicio_fk: "",
                id_tecnico_fk: "",
                id_estado_orden_servicio_fk: "",
                fecha_recepcion: null,
                fecha_inicio: "",
                fecha_finalizacion: "",
                observaciones: "",
                diagnostico_tecnico: "",
                diagnostico_cliente: "",
                id_cotizacion_fk: "",
                calificacion_servicio: "",

                repuestos: [],
            },

            formOrdenAdd: { _touched: {} },
            formOrdenEdit: { _touched: {} },
            getToken() {
                return null;
            },
            setToken(token) {
                return null;
            },
            getCsrf() {
                const m = document.head.querySelector(
                    'meta[name="csrf-token"]'
                );
                return m ? m.content : "";
            },
            apiHeaders() {
                return {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                };
            },
            async requireAuth() {
                this.authError = false;
                return true;
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
                    fecha_recepcion: this.localDateTimeNow(),
                    fecha_inicio: "",
                    fecha_finalizacion: "",
                    observaciones: "",
                    diagnostico_tecnico: "",
                    diagnostico_cliente: "",
                    id_cotizacion_fk: "",
                    calificacion_servicio: "",
                    repuestos: [],
                };
                this.errors = {};
            },

            validateTexto(value, fieldName, maxLength) {
                const v = value == null ? "" : String(value);

                this.validationErrors = this.validationErrors || {};
                if (maxLength && v.length > maxLength) {
                    this.validationErrors[fieldName] = [
                        `Máximo ${maxLength} caracteres.`,
                    ];
                    return v.slice(0, maxLength);
                }

                if (this.validationErrors && this.validationErrors[fieldName]) {
                    delete this.validationErrors[fieldName];
                }
                return v;
            },
            validateNumero(value, fieldName, maxDigits) {
                if (value === null || value === undefined || value === "") {
                    if (
                        this.validationErrors &&
                        this.validationErrors[fieldName]
                    ) {
                        delete this.validationErrors[fieldName];
                    }
                    return "";
                }
                const s = String(value).replace(/[^0-9]/g, "");
                const limited = maxDigits ? s.slice(0, maxDigits) : s;
                this.validationErrors = this.validationErrors || {};
                if (maxDigits && s.length > maxDigits) {
                    this.validationErrors[fieldName] = [
                        `Máximo ${maxDigits} dígitos.`,
                    ];
                } else if (
                    this.validationErrors &&
                    this.validationErrors[fieldName]
                ) {
                    delete this.validationErrors[fieldName];
                }

                return limited === "" ? "" : Number(limited);
            },

            isRepuestosModalOpen: false,
            repuestosModalOrder: null,
            productsOptions: [],
            repuestosList: [],
            repuestosForm: {
                id_producto_fk: "",
                cantidad: 1,
                _touched: {},
            },
            async fetchProducts() {
                if (this.productsOptions && this.productsOptions.length) return;
                try {
                    const params = new URLSearchParams();
                    params.set("per_page", "200");
                    const res = await fetch(
                        "/api/productos?" + params.toString(),
                        { headers: this.apiHeaders() }
                    );
                    if (res.status === 401) {
                        this.handleUnauthorized();
                        return;
                    }
                    if (!res.ok) throw new Error("Error al cargar productos");
                    const json = await res.json();
                    this.productsOptions = (json.data || []).map((p) => ({
                        value: String(p.id_producto_pk),
                        label:
                            p.nombre_producto ||
                            p.nombre ||
                            "#" + p.id_producto_pk,
                    }));
                } catch (e) {
                    console.error(e);
                    this.showToast(
                        "No se pudieron cargar los productos",
                        "error"
                    );
                }
            },
            async openRepuestosModal(orden) {
                this.repuestosModalOrder = orden;
                this.repuestosList = [];
                this.repuestosForm = { id_producto_fk: "", cantidad: 1 };
                await this.fetchProducts();

                try {
                    const params = new URLSearchParams();
                    params.set("per_page", "200");
                    params.set("id_orden_servicio_fk", String(orden.id));
                    const res = await fetch(
                        "/api/detalles-orden-producto?" + params.toString(),
                        { headers: this.apiHeaders() }
                    );
                    if (res.status === 401) {
                        this.handleUnauthorized();
                        return;
                    }
                    if (!res.ok) throw new Error("Error al cargar detalles");
                    const json = await res.json();
                    const items = (json.data || [])
                        .filter(
                            (i) =>
                                String(i.id_orden_servicio_fk) ===
                                String(orden.id)
                        )
                        .map((i) => ({
                            id: i.id_detalle_pk || i.id || null,
                            id_producto_fk: i.id_producto_fk,
                            producto_nombre:
                                i.producto?.nombre_producto ||
                                i.producto?.nombre ||
                                i.producto_nombre ||
                                "",
                            cantidad: i.cantidad || 1,
                        }));
                    this.repuestosList = items;
                } catch (e) {
                    console.error(e);
                }
                this.isRepuestosModalOpen = true;
            },
            async addRepuesto() {
                if (!this.repuestosModalOrder) return;
                const payload = {
                    id_orden_servicio_fk: Number(this.repuestosModalOrder.id),
                    id_producto_fk: Number(this.repuestosForm.id_producto_fk),
                    cantidad: Number(this.repuestosForm.cantidad) || 1,
                };
                try {
                    const res = await fetch("/api/detalles-orden-producto", {
                        method: "POST",
                        headers: this.apiHeaders(),
                        body: JSON.stringify(payload),
                    });
                    if (res.status === 401) {
                        this.handleUnauthorized();
                        return;
                    }
                    if (!res.ok) throw new Error("Error al agregar repuesto");
                    const json = await res.json();
                    const item = json.data || json;
                    this.repuestosList.push({
                        id: item.id_detalle_pk || item.id || null,
                        id_producto_fk: item.id_producto_fk,
                        producto_nombre:
                            item.producto?.nombre_producto ||
                            item.producto?.nombre ||
                            "",
                        cantidad: item.cantidad,
                    });
                    this.repuestosForm = { id_producto_fk: "", cantidad: 1 };

                    this.fetchOrdenes();
                } catch (e) {
                    console.error(e);
                    this.showToast("No se pudo agregar el repuesto", "error");
                }
            },

            addRepuestoToForm() {
                try {
                    if (!this.repuestosForm.id_producto_fk) {
                        this.showToast("Seleccione un producto", "error");
                        return;
                    }
                    const prodId = String(this.repuestosForm.id_producto_fk);
                    const label =
                        (this.productsOptions || []).find(
                            (p) => String(p.value) === prodId
                        )?.label || "#" + prodId;
                    const item = {
                        id_producto_fk: Number(prodId),
                        cantidad: Number(this.repuestosForm.cantidad) || 1,
                        producto_nombre: label,
                    };
                    if (!Array.isArray(this.formOrden.repuestos))
                        this.formOrden.repuestos = [];
                    this.formOrden.repuestos.push(item);
                    this.repuestosForm = { id_producto_fk: "", cantidad: 1 };
                } catch (e) {
                    console.error(e);
                    this.showToast("No se pudo agregar el repuesto", "error");
                }
            },
            removeRepuestoFromForm(idx) {
                if (!Array.isArray(this.formOrden.repuestos)) return;
                this.formOrden.repuestos.splice(idx, 1);
            },
            async removeRepuesto(item) {
                if (!item || !item.id) return;
                try {
                    const res = await fetch(
                        "/api/detalles-orden-producto/" + item.id,
                        { method: "DELETE", headers: this.apiHeaders() }
                    );
                    if (res.status === 401) {
                        this.handleUnauthorized();
                        return;
                    }
                    if (!res.ok) throw new Error("Error al eliminar repuesto");
                    this.repuestosList = this.repuestosList.filter(
                        (r) => r.id !== item.id
                    );
                    this.fetchOrdenes();
                } catch (e) {
                    console.error(e);
                    this.showToast("No se pudo eliminar el repuesto", "error");
                }
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
            localDateTimeNow() {
                const d = new Date();
                const pad = (n) => String(n).padStart(2, "0");
                const yyyy = d.getFullYear();
                const mm = pad(d.getMonth() + 1);
                const dd = pad(d.getDate());
                const hh = pad(d.getHours());
                const min = pad(d.getMinutes());
                return `${yyyy}-${mm}-${dd}T${hh}:${min}`;
            },
            normalizeDateTime(value) {
                if (!value) return null;
                const v = String(value).trim();
                if (!v) return null;

                if (v.includes(" ") && v.split(" ").length >= 2) {
                    const [d, t] = v.split(" ");
                    if (!t.includes(":")) return d + " 00:00:00";

                    const parts = t.split(":");
                    if (parts.length === 2)
                        return `${d} ${parts[0]}:${parts[1]}:00`;
                    return `${d} ${t}`;
                }

                if (v.includes("T")) {
                    const [d, time] = v.split("T");
                    if (!time) return `${d} 00:00:00`;
                    const parts = time.split(":");
                    if (parts.length === 2)
                        return `${d} ${parts[0]}:${parts[1]}:00`;

                    return `${d} ${time}`;
                }

                if (v.match(/^\d{4}-\d{2}-\d{2}$/)) return `${v} 00:00:00`;
                return v;
            },
            toInputDatetime(value) {
                if (!value) return "";
                const v = String(value).trim();
                if (!v) return "";

                if (v.includes("T")) {
                    const [d, t] = v.split("T");
                    if (!t) return `${d}T00:00`;
                    const parts = t.split(":");
                    if (parts.length >= 2)
                        return `${d}T${parts[0]}:${parts[1]}`;
                    return `${d}T00:00`;
                }

                if (v.includes(" ")) {
                    const [d, t] = v.split(" ");
                    if (!t) return `${d}T00:00`;
                    const parts = t.split(":");
                    if (parts.length >= 2)
                        return `${d}T${parts[0]}:${parts[1]}`;
                    return `${d}T00:00`;
                }

                if (v.match(/^\d{4}-\d{2}-\d{2}$/)) return `${v}T00:00`;

                const date = new Date(v);
                if (!isNaN(date.getTime())) {
                    const pad = (n) => String(n).padStart(2, "0");
                    const yyyy = date.getFullYear();
                    const mm = pad(date.getMonth() + 1);
                    const dd = pad(date.getDate());
                    const hh = pad(date.getHours());
                    const min = pad(date.getMinutes());
                    return `${yyyy}-${mm}-${dd}T${hh}:${min}`;
                }
                return "";
            },

            formatCotLabel(item) {
                if (!item) return "";

                if (item.codigo_cotizacion)
                    return String(item.codigo_cotizacion);
                if (item.codigo) return String(item.codigo);

                const fechaRaw =
                    item.fecha_cotizacion ||
                    item.fecha ||
                    item.created_at ||
                    item.fecha_creacion ||
                    null;
                let fechaStr = "000000000000";
                if (fechaRaw) {
                    try {
                        const d = new Date(fechaRaw);
                        if (!isNaN(d.getTime())) {
                            const pad = (n) => String(n).padStart(2, "0");
                            fechaStr = `${d.getFullYear()}${pad(
                                d.getMonth() + 1
                            )}${pad(d.getDate())}${pad(d.getHours())}${pad(
                                d.getMinutes()
                            )}`;
                        }
                    } catch (_) {}
                }
                const id = String(
                    item.id_cotizacion_pk || item.id || item.id_cotizacion || ""
                );
                const pad4 = id ? id.padStart(4, "0") : "0000";
                return `COT-${fechaStr}-${pad4}`;
            },
            mapOrden(orden) {
                const solicitud = orden.solicitud_servicio || {};
                const cliente = solicitud.cliente || {};
                const empresa = cliente.empresa || {};
                const persona = cliente.persona || {};
                const contacto = solicitud.contacto || {};
                const tecnico = orden.tecnico || {};
                const calificacionValor =
                    orden.calificacion_servicio ??
                    orden.raw?.calificacion_servicio ??
                    null;
                const estado = orden.estado || {};
                const cotizacion =
                    orden.cotizacion || orden.cotizacion_generada || {};
                const fechaRecepcion =
                    orden.fecha_recepcion_formatted ||
                    orden.fecha_recepcion ||
                    this.formatDate(orden.fecha_recepcion);
                const fechaInicio =
                    orden.fecha_inicio_formatted ||
                    orden.fecha_inicio ||
                    this.formatDate(orden.fecha_inicio);
                const fechaFinalizacion =
                    orden.fecha_finalizacion_formatted ||
                    orden.fecha_finalizacion ||
                    this.formatDate(orden.fecha_finalizacion);

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
                        empresa.nombre_comercial ||
                        empresa.razon_social ||
                        [
                            persona.primer_nombre,
                            persona.segundo_nombre,
                            persona.primer_apellido,
                            persona.segundo_apellido,
                        ]
                            .filter(Boolean)
                            .join(" ") ||
                        "",
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
                    calificacion_servicio: calificacionValor || null,
                    repuestos_count:
                        orden.repuestos_count !== undefined
                            ? orden.repuestos_count
                            : Array.isArray(
                                  orden.repuestos || orden.raw?.repuestos
                              )
                            ? (orden.repuestos || orden.raw?.repuestos).length
                            : null,
                    repuestos_summary: (function () {
                        try {
                            const arr =
                                orden.repuestos || orden.raw?.repuestos || null;
                            if (Array.isArray(arr) && arr.length) {
                                const names = arr
                                    .map(
                                        (r) =>
                                            r.nombre ||
                                            r.producto_nombre ||
                                            r.repuesto ||
                                            (r.id_producto
                                                ? "#" + r.id_producto
                                                : "")
                                    )
                                    .filter(Boolean);
                                if (names.length <= 3) return names.join(", ");
                                return (
                                    names.slice(0, 3).join(", ") +
                                    " +" +
                                    (names.length - 3)
                                );
                            }
                            if (orden.repuestos_count)
                                return String(orden.repuestos_count) + " rep.";
                        } catch (e) {
                            return null;
                        }
                        return null;
                    })(),
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
                        label: String(label ?? `ID ${normalizedValue}`),
                    });
                    this.sortOptions(listName);
                }
            },
            sortOptions(listName) {
                if (!Array.isArray(this[listName])) return;
                this[listName].sort((a, b) => {
                    const la =
                        a && a.label !== undefined && a.label !== null
                            ? String(a.label)
                            : "";
                    const lb =
                        b && b.label !== undefined && b.label !== null
                            ? String(b.label)
                            : "";
                    try {
                        return la.localeCompare(lb, "es");
                    } catch (e) {
                        if (la < lb) return -1;
                        if (la > lb) return 1;
                        return 0;
                    }
                });
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
                    const cotId = String(orden.id_cotizacion);
                    let label = `COT-${cotId.padStart(4, "0")}`;
                    try {
                        const found = (this.cotizacionesOptions || []).find(
                            (o) => String(o.value) === cotId
                        );
                        if (found && found.label) label = found.label;
                    } catch (_) {}
                    this.ensureOption("cotizacionesOptions", cotId, label);
                }
            },
            async fetchCatalogos() {
                if (!(await this.requireAuth())) {
                    return;
                }

                await Promise.all([
                    this.fetchSolicitudes(),
                    this.fetchTecnicos(),
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

                    let raw = [];
                    if (response.ok) {
                        try {
                            const parsed = await response
                                .json()
                                .catch(() => null);
                            if (Array.isArray(parsed)) raw = parsed;
                            else if (Array.isArray(parsed?.data))
                                raw = parsed.data;
                            else if (Array.isArray(parsed?.items))
                                raw = parsed.items;
                            else raw = [];
                        } catch (e) {
                            console.debug(
                                "fetchSolicitudes: parse error, using empty array",
                                e
                            );
                            raw = [];
                        }
                    } else {
                        throw new Error("Error al cargar solicitudes");
                    }

                    const opciones = (raw || []).map((item) => ({
                        value: String(
                            item.id_solicitud_pk || item.id || item.id_solicitud
                        ),

                        label:
                            item.nombre_solicitud ||
                            item.numero_solicitud_acf ||
                            item.numero_solicitud_cliente ||
                            `Solicitud #${
                                item.id_solicitud_pk ||
                                item.id ||
                                item.id_solicitud
                            }`,
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
                    const response = await fetch("/api/tecnicos", {
                        headers: this.apiHeaders(),
                    });
                    if (response.status === 401) {
                        this.handleUnauthorized();
                        return;
                    }
                    if (!response.ok)
                        throw new Error("Error al cargar técnicos");
                    const json = await response.json();
                    const opciones = (json.data || json || []).map((item) => {
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
                        "No se pudieron cargar los técnicos",
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
                        value: String(
                            item.id_cotizacion_pk ||
                                item.id ||
                                item.id_cotizacion
                        ),
                        label: this.formatCotLabel(item),
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

            async fetchCotizacionesForCliente(clienteId) {
                if (this.authError) return;
                this.loadingCatalogos.cotizaciones = true;
                try {
                    if (!clienteId) {
                        await this.fetchCotizaciones();
                        return;
                    }
                    const params = new URLSearchParams();
                    params.set("per_page", "100");

                    params.set("id_cliente_fk", String(clienteId));
                    params.set("sort", "fecha");
                    params.set("direction", "desc");
                    let response = await fetch(
                        "/api/cotizaciones?" + params.toString(),
                        { headers: this.apiHeaders() }
                    );
                    if (response.status === 401) {
                        this.handleUnauthorized();
                        return;
                    }

                    if (!response.ok) {
                        const params2 = new URLSearchParams();
                        params2.set("per_page", "100");
                        params2.set("cliente_id", String(clienteId));
                        params2.set("sort", "fecha");
                        params2.set("direction", "desc");
                        response = await fetch(
                            "/api/cotizaciones?" + params2.toString(),
                            { headers: this.apiHeaders() }
                        );
                        if (response.status === 401) {
                            this.handleUnauthorized();
                            return;
                        }
                    }
                    if (!response.ok)
                        throw new Error(
                            "Error al cargar cotizaciones por cliente"
                        );
                    const parsed = await response.json().catch(() => null);
                    const raw = Array.isArray(parsed)
                        ? parsed
                        : Array.isArray(parsed?.data)
                        ? parsed.data
                        : [];
                    const opciones = (raw || []).map((item) => ({
                        value: String(
                            item.id_cotizacion_pk ||
                                item.id ||
                                item.id_cotizacion
                        ),
                        label: this.formatCotLabel(item),
                    }));
                    this.cotizacionesOptions = opciones;
                    this.sortOptions("cotizacionesOptions");
                } catch (e) {
                    console.error(e);

                    try {
                        await this.fetchCotizaciones();
                    } catch (_) {}
                } finally {
                    this.loadingCatalogos.cotizaciones = false;
                }
            },

            async onSolicitudChange(solicitudId) {
                if (!solicitudId) {
                    this.cotizacionesOptions = [];
                    return;
                }
                try {
                    const response = await fetch(
                        "/api/solicitudes/" + String(solicitudId),
                        { headers: this.apiHeaders() }
                    );
                    if (response.status === 401) {
                        this.handleUnauthorized();
                        return;
                    }
                    if (!response.ok) {
                        await this.fetchCotizaciones();
                        return;
                    }
                    const json = await response.json().catch(() => null);
                    const full = json?.data || json || {};

                    const clienteId =
                        full.id_cliente_fk ||
                        full.cliente?.id ||
                        full.cliente?.id_cliente_pk ||
                        full.cliente_id ||
                        full.id_cliente ||
                        null;
                    if (clienteId) {
                        await this.fetchCotizacionesForCliente(clienteId);
                    } else {
                        await this.fetchCotizaciones();
                    }
                } catch (e) {
                    console.error("onSolicitudChange error", e);
                    await this.fetchCotizaciones();
                }
            },
            isVerMasModalOpen: false,
            ordenSeleccionada: null,

            openVerMasModal(orden) {
                this.ordenSeleccionada = orden;
                this.isVerMasModalOpen = true;
            },

            detalleUrl(id) {
                return detalleUrl.replace("ID_ORDEN", id);
            },
            async fetchOrdenes() {
                if (!(await this.requireAuth()) || this.authError) return;
                this.loadingOrdenes = true;
                try {
                    const params = new URLSearchParams();
                    params.set("per_page", "100");

                    if (
                        this.searchOrden &&
                        String(this.searchOrden).trim() !== ""
                    ) {
                        params.set("q", String(this.searchOrden).trim());
                    }
                    if (
                        this.tecnicoOrden &&
                        String(this.tecnicoOrden).trim() !== ""
                    ) {
                        params.set(
                            "id_tecnico_fk",
                            String(this.tecnicoOrden).trim()
                        );
                    }
                    if (
                        this.ordenarPor &&
                        String(this.ordenarPor).trim() !== ""
                    ) {
                        params.set("order_by", String(this.ordenarPor).trim());
                    }

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
                    this.numbers = this.ordenes;
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

                this.fetchProducts().catch(() => {});

                this.formOrdenAdd = this.formOrdenAdd || { _touched: {} };
                this.formOrdenAdd._touched = {};
                this.isModalOpen = true;
            },
            openEditOrden(orden) {
                this.errors = {};
                this.ordenToEdit = orden;

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
                        const full = json.data || json;
                        const mapped = this.mapOrden(full);
                        this.ensureOrdenOptions(mapped);

                        this.formOrden = {
                            id: mapped.id,
                            id_solicitud_servicio_fk: mapped.id_solicitud ?? "",
                            id_tecnico_fk: mapped.id_tecnico ?? "",
                            id_estado_orden_servicio_fk: mapped.estado_id
                                ? String(mapped.estado_id)
                                : "",
                            fecha_recepcion: this.toInputDatetime(
                                mapped.fecha_recepcion ||
                                    mapped.fecha_recepcion_formatted ||
                                    mapped.fecha_recepcion_formatted
                            ),
                            fecha_inicio: this.toInputDatetime(
                                mapped.fecha_inicio ||
                                    mapped.fecha_inicio_formatted
                            ),
                            fecha_finalizacion: this.toInputDatetime(
                                mapped.fecha_finalizacion ||
                                    mapped.fecha_finalizacion_formatted
                            ),
                            observaciones: mapped.observaciones || "",
                            diagnostico_tecnico:
                                mapped.diagnostico_tecnico || "",
                            diagnostico_cliente:
                                mapped.diagnostico_cliente || "",
                            id_cotizacion_fk: mapped.id_cotizacion ?? "",
                            calificacion_servicio:
                                full.calificacion_servicio ??
                                mapped.calificacion_servicio ??
                                "",
                            repuestos:
                                full.repuestos && Array.isArray(full.repuestos)
                                    ? full.repuestos.map((r) => ({
                                          id_producto_fk:
                                              r.id_producto_fk ||
                                              r.id_producto ||
                                              r.id_producto_fk,
                                          cantidad: r.cantidad || r.cant || 1,
                                          producto_nombre:
                                              r.nombre ||
                                              r.producto_nombre ||
                                              r.repuesto ||
                                              "",
                                      }))
                                    : mapped.raw?.repuestos || [],
                        };

                        this.fetchProducts().catch(() => {});
                    } catch (e) {
                        console.error(e);

                        this.formOrden = {
                            id: orden.id,
                            id_solicitud_servicio_fk: orden.id_solicitud ?? "",
                            id_tecnico_fk: orden.id_tecnico ?? "",
                            id_estado_orden_servicio_fk: orden.estado_id
                                ? String(orden.estado_id)
                                : "",
                            fecha_recepcion: this.toInputDatetime(
                                orden.raw?.fecha_recepcion ||
                                    orden.fecha_recepcion
                            ),
                            fecha_inicio: this.toInputDatetime(
                                orden.raw?.fecha_inicio || orden.fecha_inicio
                            ),
                            fecha_finalizacion: this.toInputDatetime(
                                orden.raw?.fecha_finalizacion ||
                                    orden.fecha_finalizacion
                            ),
                            observaciones: orden.observaciones || "",
                            diagnostico_tecnico:
                                orden.diagnostico_tecnico || "",
                            diagnostico_cliente:
                                orden.diagnostico_cliente || "",
                            id_cotizacion_fk: orden.id_cotizacion ?? "",
                            calificacion_servicio:
                                orden.raw?.calificacion_servicio ?? "",
                            repuestos:
                                orden.raw && orden.raw.repuestos
                                    ? orden.raw.repuestos
                                    : [],
                        };
                    } finally {
                        this.formOrdenEdit = this.formOrdenEdit || {
                            _touched: {},
                        };
                        this.formOrdenEdit._touched = {};
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
                    fecha_recepcion: this.normalizeDateTime(
                        this.formOrden.fecha_recepcion
                    ),
                    fecha_inicio: this.normalizeDateTime(
                        this.formOrden.fecha_inicio
                    ),
                    fecha_finalizacion: this.normalizeDateTime(
                        this.formOrden.fecha_finalizacion
                    ),
                    observaciones: this.formOrden.observaciones || null,
                    diagnostico_tecnico:
                        this.formOrden.diagnostico_tecnico || null,
                    diagnostico_cliente:
                        this.formOrden.diagnostico_cliente || null,
                    id_cotizacion_fk: this.formOrden.id_cotizacion_fk
                        ? Number(this.formOrden.id_cotizacion_fk)
                        : null,
                    calificacion_servicio:
                        this.formOrden.calificacion_servicio || null,

                    repuestos: Array.isArray(this.formOrden.repuestos)
                        ? this.formOrden.repuestos.map((r) => ({
                              id_producto_fk:
                                  Number(
                                      r.id_producto_fk || r.id_producto || 0
                                  ) || null,
                              cantidad: Number(r.cantidad) || 1,
                          }))
                        : [],
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
                    if (!this.validateForm()) {
                        this.saving = false;
                        return;
                    }
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
                    if (!this.validateForm()) {
                        this.saving = false;
                        return;
                    }
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
            validateForm() {
                const errs = {};
                const requiredMsg = "Este campo es obligatorio.";
                if (!this.formOrden.id_solicitud_servicio_fk) {
                    errs.id_solicitud_servicio_fk = [requiredMsg];
                }
                if (!this.formOrden.id_tecnico_fk) {
                    errs.id_tecnico_fk = [requiredMsg];
                }
                if (!this.formOrden.id_cotizacion_fk) {
                    errs.id_cotizacion_fk = [requiredMsg];
                }

                if (
                    this.formOrden.fecha_inicio &&
                    this.formOrden.fecha_finalizacion
                ) {
                    const fechaInicio = new Date(this.formOrden.fecha_inicio);
                    const fechaFinalizacion = new Date(
                        this.formOrden.fecha_finalizacion
                    );

                    if (fechaInicio > fechaFinalizacion) {
                        errs.fecha_inicio = [
                            "La fecha de inicio no puede ser mayor que la fecha de finalización.",
                        ];
                        errs.fecha_finalizacion = [
                            "La fecha de finalización no puede ser menor que la fecha de inicio.",
                        ];
                    }
                }

                this.errors = errs;
                if (Object.keys(errs).length) {
                    this.showToast(
                        "Por favor completa los campos requeridos.",
                        "error"
                    );
                    return false;
                }
                return true;
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

            paginatedOrdenes() {
                const start = (this.currentPage - 1) * this.perPage;
                const end = start + this.perPage;
                return this.filteredOrdenes().slice(start, end);
            },
            totalPages() {
                return Math.ceil(this.filteredOrdenes().length / this.perPage);
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
                if (page >= 1 && page <= this.totalPages()) {
                    this.currentPage = page;
                }
            },

            async init() {
                if (!(await this.requireAuth())) return;

                const debounce = (fn, ms = 350) => {
                    let h;
                    return (...a) => {
                        clearTimeout(h);
                        h = setTimeout(() => fn(...a), ms);
                    };
                };

                this.$watch(
                    "searchOrden",
                    debounce(() => {
                        this.fetchOrdenes();
                    })
                );
                this.$watch(
                    "tecnicoOrden",
                    debounce(() => {
                        this.fetchOrdenes();
                    }, 250)
                );
                this.$watch(
                    "ordenarPor",
                    debounce(() => {
                        this.fetchOrdenes();
                    }, 150)
                );

                this.$watch(
                    "formOrden.id_solicitud_servicio_fk",
                    debounce((val) => {
                        if (!val) return;

                        this.onSolicitudChange(val);
                    }, 250)
                );

                await Promise.all([
                    this.fetchCatalogos(),
                    this.fetchEstadosOrden(),
                    this.fetchOrdenes(),
                ]);
            },
        };
    };
}

if (typeof window !== "undefined") {
    window.gestionSolicitudes = function () {
        return {
            tab: "solicitudes",
            searchSolicitud: "",
            estadoSolicitud: "",
            ordenarPor: "estado_solicitud",
            searchContacto: "",
            ordenarPorContacto: "tipo_contacto",

            get numbers() {
                return this.tab === "solicitudes"
                    ? this.numbersSolicitudes
                    : this.numbersContactos;
            },
            get currentPage() {
                return this.tab === "solicitudes"
                    ? this.currentPageSolicitudes
                    : this.currentPageContactos;
            },
            set currentPage(value) {
                if (this.tab === "solicitudes") {
                    this.currentPageSolicitudes = value;
                } else {
                    this.currentPageContactos = value;
                }
            },
            get perPage() {
                return this.tab === "solicitudes"
                    ? this.perPageSolicitudes
                    : this.perPageContactos;
            },
            reportUrl() {
                const params = new URLSearchParams();
                params.set("modulo", "Solicitudes");
                if (this.searchSolicitud)
                    params.set("search", this.searchSolicitud);
                if (this.estadoSolicitud)
                    params.set("estado_solicitud", this.estadoSolicitud);
                if (this.ordenarPor) params.set("ordenar_por", this.ordenarPor);
                const now = new Date();
                try {
                    const fechaStr = now.toLocaleDateString("es-HN", {
                        day: "2-digit",
                        month: "short",
                        year: "numeric",
                    });
                    params.set("fecha", fechaStr);
                } catch (_) {
                    params.set("fecha", now.toISOString().slice(0, 10));
                }
                params.set("fecha_generacion", now.toISOString());
                return "/admin/reportes-header?" + params.toString();
            },

            isModalOpen: false,
            isEditModalOpen: false,
            isDeleteModalOpen: false,
            solicitudToEdit: null,
            solicitudToDelete: null,

            isEstadoModalOpen: false,
            isEditEstadoModalOpen: false,
            estadoToEdit: {
                id: null,
                nombre_estado: "",
                descripcion_estado: "",
                codigo: "",
                es_final: 0,
                orden: 0,
            },
            isDeleteEstadoModalOpen: false,
            estadoToDelete: null,

            isContactoModalOpen: false,
            isEditContactoModalOpen: false,
            isDeleteContactoModalOpen: false,
            contactoToEdit: null,
            contactoToDelete: null,

            solicitudes: [],
            contactos: [],
            clientesOptions: [],
            estadosOptions: [],
            contactosOptions: [],

            numbersSolicitudes: [],
            currentPageSolicitudes: 1,
            perPageSolicitudes: 10,

            numbersContactos: [],
            currentPageContactos: 1,
            perPageContactos: 10,

            formSolicitud: {
                id: null,
                id_cliente_fk: "",
                nombre_solicitud: "",
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

            getToken() {
                return null;
            },
            setToken(token) {
                return null;
            },
            getCsrf() {
                const m = document.head.querySelector(
                    'meta[name="csrf-token"]'
                );
                return m ? m.content : "";
            },
            apiHeaders() {
                return {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                };
            },
            async requireAuth() {
                return true;
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
                    nombre_solicitud: "",
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

            mapSolicitud(item) {
                const cliente = item.cliente || {};
                const empresa = cliente.empresa || {};
                const persona = cliente.persona || {};
                const estado = item.estado_solicitud || {};
                const contacto = item.contacto || {};

                let clienteNombre = "";
                if (
                    cliente.tipo ||
                    empresa.nombre_comercial ||
                    empresa.razon_social
                ) {
                    clienteNombre =
                        cliente.nombre ||
                        cliente.nombre_comercial ||
                        empresa.nombre_comercial ||
                        empresa.razon_social ||
                        "";
                }
                if (!clienteNombre || !String(clienteNombre).trim()) {
                    const pn =
                        cliente.primer_nombre || persona.primer_nombre || "";
                    const sn =
                        cliente.segundo_nombre || persona.segundo_nombre || "";
                    const pa =
                        cliente.primer_apellido ||
                        persona.primer_apellido ||
                        "";
                    const sa =
                        cliente.segundo_apellido ||
                        persona.segundo_apellido ||
                        "";
                    clienteNombre = [pn, sn, pa, sa]
                        .filter(Boolean)
                        .join(" ")
                        .trim();
                }
                return {
                    id: item.id_solicitud_pk,
                    id_cliente_fk: item.id_cliente_fk,
                    cliente_nombre: clienteNombre,
                    numero_solicitud_acf: item.numero_solicitud_acf,
                    numero_solicitud_cliente: item.numero_solicitud_cliente,
                    nombre_solicitud: item.nombre_solicitud || "",
                    descripcion_problema: item.descripcion_problema,
                    id_estado_solicitud_fk: item.id_estado_solicitud_fk,
                    estado_nombre: estado.nombre_estado || "",
                    id_contacto_fk: item.id_contacto_fk,
                    contacto_valor: contacto.valor_contacto || "",
                };
            },

            async fetchClientes() {
                this.loadingCatalogos.clientes = true;
                try {
                    const params = new URLSearchParams();
                    params.set("per_page", "500");
                    params.set("all", "1");
                    const res = await fetch(
                        "/api/clientes?" + params.toString(),
                        {
                            headers: this.apiHeaders(),
                            credentials: "same-origin",
                        }
                    );
                    if (!res.ok) throw new Error("Error clientes");
                    const data = await res.json();
                    const raw = Array.isArray(data?.data)
                        ? data.data
                        : Array.isArray(data?.data?.data)
                        ? data.data.data
                        : Array.isArray(data)
                        ? data
                        : [];
                    const mapped = (raw || [])
                        .map((c) => {
                            let nombre;
                            if (c.tipo === "empresa") {
                                nombre =
                                    c.nombre ||
                                    c.nombre_comercial ||
                                    c.razon_social ||
                                    "";
                            } else {
                                const parts = [
                                    c.primer_nombre,
                                    c.segundo_nombre,
                                    c.primer_apellido,
                                    c.segundo_apellido,
                                ].filter(Boolean);
                                nombre = parts.join(" ");
                            }
                            if (!nombre || !String(nombre).trim()) {
                                nombre = `Cliente ${c.id}`;
                            }
                            const id = c.id;
                            if (!id) return null;
                            return { value: String(id), label: nombre };
                        })
                        .filter(Boolean);

                    const uniq = {};
                    for (const it of mapped) {
                        if (!uniq[it.value]) uniq[it.value] = it;
                    }
                    this.clientesOptions = Object.values(uniq).sort((a, b) =>
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
                        "/api-web/estados-solicitud?" + params.toString(),
                        { headers: this.apiHeaders() }
                    );
                    if (!res.ok) throw new Error("Error estados");
                    const json = await res.json();
                    const options = (json.data || []).map((it) => ({
                        value: String(it.id_estado_solicitud_pk || it.id),
                        label:
                            it.nombre ||
                            it.nombre_estado ||
                            `Estado ${it.id_estado_solicitud_pk || it.id}`,
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

                    this.contactosOptions = items.map((it) => {
                        const value = String(it.id_contacto_pk || it.id);
                        const base =
                            it.valor_contacto || it.tipo_contacto || "Contacto";
                        const cliente =
                            this.clienteLabelById(it.id_cliente_fk) || "";
                        const label = cliente ? `${base} — ${cliente}` : base;
                        return {
                            value,
                            label,
                            id_cliente_fk: String(it.id_cliente_fk || ""),
                        };
                    });

                    this.contactosOptions.sort((a, b) =>
                        a.label.localeCompare(b.label, "es")
                    );
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

                if (!idCliente) return [];
                return this.contactosOptions.filter(
                    (c) => String(c.id_cliente_fk || "") === idCliente
                );
            },

            onClienteChange() {
                this.formSolicitud.id_contacto_fk = "";
            },

            clienteLabelById(id) {
                if (!id) return "";
                const sid = String(id);
                const found = this.clientesOptions.find(
                    (o) => String(o.value) === sid
                );
                return found ? found.label : "";
            },

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
                    if (res.status === 401) {
                        this.handleUnauthorized();
                        return;
                    }

                    let raw = [];
                    if (res.ok) {
                        try {
                            const parsed = await res.json().catch(() => null);
                            if (Array.isArray(parsed)) raw = parsed;
                            else if (Array.isArray(parsed?.data))
                                raw = parsed.data;
                            else if (Array.isArray(parsed?.items))
                                raw = parsed.items;
                            else raw = [];
                        } catch (e) {
                            console.debug(
                                "gestionSolicitudes.fetchSolicitudes: parse error",
                                e
                            );
                            raw = [];
                        }
                    } else {
                        throw new Error("Error solicitudes");
                    }

                    this.solicitudes = (raw || []).map((it) =>
                        this.mapSolicitud(it)
                    );

                    this.numbersSolicitudes = this.solicitudes;
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
                    nombre_solicitud:
                        this.formSolicitud.nombre_solicitud || null,
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
                    id_cliente_fk: item.id_cliente_fk
                        ? Number(item.id_cliente_fk)
                        : "",
                    nombre_solicitud: item.nombre_solicitud ?? "",
                    descripcion_problema: item.descripcion_problema ?? "",
                    id_estado_solicitud_fk: item.id_estado_solicitud_fk
                        ? Number(item.id_estado_solicitud_fk)
                        : "",
                    id_contacto_fk: item.id_contacto_fk
                        ? Number(item.id_contacto_fk)
                        : "",
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
                    if (!res.ok) {
                        let msg = "Error al crear la solicitud";
                        try {
                            const errText = await res.text();
                            if (errText) msg += `: ${errText.slice(0, 300)}`;
                        } catch (_) {}
                        throw new Error(msg);
                    }
                    const json = await res.json();
                    if (json.data)
                        this.solicitudes.unshift(this.mapSolicitud(json.data));

                    this.numbersSolicitudes = this.solicitudes;
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

                    this.numbersSolicitudes = this.solicitudes;
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

                    this.numbersSolicitudes = this.solicitudes;
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
                this.errors = {};
                const errs = {};
                if (!this.formSolicitud.id_cliente_fk)
                    errs.id_cliente_fk = ["Seleccione un cliente."];
                if (
                    !this.formSolicitud.descripcion_problema ||
                    String(this.formSolicitud.descripcion_problema).trim()
                        .length === 0
                )
                    errs.descripcion_problema = [
                        "La descripción es obligatoria.",
                    ];

                if (
                    this.formSolicitud.nombre_solicitud &&
                    String(this.formSolicitud.nombre_solicitud).length > 150
                ) {
                    errs.nombre_solicitud = ["Máximo 150 caracteres."];
                }
                if (!this.formSolicitud.id_estado_solicitud_fk)
                    errs.id_estado_solicitud_fk = ["Seleccione un estado."];
                if (!this.formSolicitud.id_contacto_fk)
                    errs.id_contacto_fk = ["Seleccione un contacto."];
                if (Object.keys(errs).length) {
                    this.formSolicitud._touched =
                        this.formSolicitud._touched || {};
                    Object.keys(errs).forEach((k) => {
                        this.formSolicitud._touched[k] = true;
                    });
                    this.errors = errs;
                    this.showToast("Complete los campos requeridos", "warn");
                    return;
                }
                if (this.formSolicitud.id) this.updateSolicitud();
                else this.createSolicitud();
            },

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

                    this.numbersContactos = this.contactos;
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

                    this.numbersContactos = this.contactos;
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

                    this.numbersContactos = this.contactos;
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

                    this.numbersContactos = this.contactos;
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
                this.errors = {};
                const errs = {};
                if (
                    !this.formContacto.tipo_contacto ||
                    String(this.formContacto.tipo_contacto).trim().length === 0
                ) {
                    errs.tipo_contacto = ["El tipo de contacto es requerido."];
                }
                if (
                    !this.formContacto.valor_contacto ||
                    String(this.formContacto.valor_contacto).trim().length === 0
                ) {
                    errs.valor_contacto = [
                        "El valor de contacto es requerido.",
                    ];
                }
                if (!this.formContacto.id_cliente_fk) {
                    errs.id_cliente_fk = ["Seleccione un cliente."];
                }
                if (Object.keys(errs).length) {
                    this.formContacto._touched =
                        this.formContacto._touched || {};
                    Object.keys(errs).forEach(
                        (k) => (this.formContacto._touched[k] = true)
                    );
                    this.errors = errs;
                    this.showToast("Complete los campos requeridos", "warn");
                    return;
                }
                if (this.formContacto.id) this.updateContacto();
                else this.createContacto();
            },

            filteredContactos() {
                const term = this.searchContacto.trim().toLowerCase();
                const list = this.contactos
                    .filter((c) => {
                        if (!term) return true;
                        const cliente =
                            this.clienteLabelById(c.id_cliente_fk) || "";
                        return [c.tipo_contacto, c.valor_contacto, cliente]
                            .filter(Boolean)
                            .some((f) =>
                                f.toString().toLowerCase().includes(term)
                            );
                    })
                    .sort((a, b) => {
                        switch (this.ordenarPorContacto) {
                            case "valor_contacto":
                                return (a.valor_contacto || "").localeCompare(
                                    b.valor_contacto || "",
                                    "es"
                                );
                            case "cliente": {
                                const na =
                                    this.clienteLabelById(a.id_cliente_fk) ||
                                    "";
                                const nb =
                                    this.clienteLabelById(b.id_cliente_fk) ||
                                    "";
                                return na.localeCompare(nb, "es");
                            }
                            case "tipo_contacto":
                            default:
                                return (a.tipo_contacto || "").localeCompare(
                                    b.tipo_contacto || "",
                                    "es"
                                );
                        }
                    });
                return list;
            },
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
                            case "estado":
                            case "estado_solicitud":
                                return (a.estado_nombre || "").localeCompare(
                                    b.estado_nombre || "",
                                    "es"
                                );
                            case "cliente":
                                return (
                                    a.cliente_nombre ||
                                    this.clienteLabelById(a.id_cliente_fk) ||
                                    ""
                                ).localeCompare(
                                    b.cliente_nombre ||
                                        this.clienteLabelById(
                                            b.id_cliente_fk
                                        ) ||
                                        "",
                                    "es"
                                );
                            case "solicitud_acf":
                                return String(
                                    a.numero_solicitud_acf || ""
                                ).localeCompare(
                                    String(b.numero_solicitud_acf || ""),
                                    "es",
                                    { numeric: true }
                                );
                            case "solicitud_cliente":
                                return String(
                                    a.numero_solicitud_cliente || ""
                                ).localeCompare(
                                    String(b.numero_solicitud_cliente || ""),
                                    "es",
                                    { numeric: true }
                                );
                            case "fecha_creacion":
                                return 0;
                            default:
                                return 0;
                        }
                    });
            },

            paginatedSolicitudes() {
                const filtered = this.filteredSolicitudes();
                const start =
                    (this.currentPageSolicitudes - 1) * this.perPageSolicitudes;
                const end = start + this.perPageSolicitudes;
                return filtered.slice(start, end);
            },
            totalPagesSolicitudes() {
                return Math.ceil(
                    this.filteredSolicitudes().length / this.perPageSolicitudes
                );
            },
            nextPageSolicitudes() {
                if (
                    this.currentPageSolicitudes < this.totalPagesSolicitudes()
                ) {
                    this.currentPageSolicitudes++;
                }
            },
            prevPageSolicitudes() {
                if (this.currentPageSolicitudes > 1) {
                    this.currentPageSolicitudes--;
                }
            },
            goToPageSolicitudes(page) {
                if (page >= 1 && page <= this.totalPagesSolicitudes()) {
                    this.currentPageSolicitudes = page;
                }
            },

            paginatedContactos() {
                const filtered = this.filteredContactos();
                const start =
                    (this.currentPageContactos - 1) * this.perPageContactos;
                const end = start + this.perPageContactos;
                return filtered.slice(start, end);
            },
            totalPagesContactos() {
                return Math.ceil(
                    this.filteredContactos().length / this.perPageContactos
                );
            },
            nextPageContactos() {
                if (this.currentPageContactos < this.totalPagesContactos()) {
                    this.currentPageContactos++;
                }
            },
            prevPageContactos() {
                if (this.currentPageContactos > 1) {
                    this.currentPageContactos--;
                }
            },
            goToPageContactos(page) {
                if (page >= 1 && page <= this.totalPagesContactos()) {
                    this.currentPageContactos = page;
                }
            },

            totalPages() {
                return this.tab === "solicitudes"
                    ? this.totalPagesSolicitudes()
                    : this.totalPagesContactos();
            },
            nextPage() {
                if (this.tab === "solicitudes") {
                    this.nextPageSolicitudes();
                } else {
                    this.nextPageContactos();
                }
            },
            prevPage() {
                if (this.tab === "solicitudes") {
                    this.prevPageSolicitudes();
                } else {
                    this.prevPageContactos();
                }
            },

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
