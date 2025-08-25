import "./bootstrap";
import "./usuarios";
import "./parametros";

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
    faSignInAlt
);
dom.watch();

library.add(faEye, faEyeSlash, faMoon, faSun);
dom.watch();

// Alpine.js collapse plugin
document.addEventListener("alpine:init", () => {
    Alpine.plugin(collapse);
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
            // Prevenir múltiples navegaciones simultáneas
            if (this.isTransitioning) return;

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
                const response = await fetch(`/load-view?view=${viewName}`);
                if (!response.ok) {
                    throw new Error(
                        `HTTP ${response.status}: ${response.statusText}`
                    );
                }

                const html = await response.text();
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

            document.querySelector("main").innerHTML = html;
            // Reinicializar Alpine.js en el nuevo contenido
            Alpine.initTree(document.querySelector("main"));

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
                    initializeDashboardCharts();
                }, 100);
            }
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

Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = "#6B7280";

// Función para inicializar los gráficos del dashboard
function initializeDashboardCharts() {
    // Initialize 'ordenesChart' only if element exists
    const ordenesEl = document.getElementById("ordenesChart");
    if (ordenesEl) {
        // Destruir instancia existente si existe
        if (window.ordenesChartInstance) {
            window.ordenesChartInstance.destroy();
        }

        const ordenesCtx = ordenesEl.getContext("2d");
        window.ordenesChartInstance = new Chart(ordenesCtx, {
            type: "doughnut",
            data: {
                labels: ["Abiertas", "En Proceso", "Cerradas"],
                datasets: [
                    {
                        data: [45, 123, 1079],
                        backgroundColor: ["#EF4444", "#F59E0B", "#10B981"],
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
    }

    // Initialize 'cotizacionesChart' only if element exists
    const cotizacionesEl = document.getElementById("cotizacionesChart");
    if (cotizacionesEl) {
        // Destruir instancia existente si existe
        if (window.cotizacionesChartInstance) {
            window.cotizacionesChartInstance.destroy();
        }

        const cotizacionesCtx = cotizacionesEl.getContext("2d");
        window.cotizacionesChartInstance = new Chart(cotizacionesCtx, {
            type: "line",
            data: {
                labels: [
                    "Ene",
                    "Feb",
                    "Mar",
                    "Abr",
                    "May",
                    "Jun",
                    "Jul",
                    "Ago",
                ],
                datasets: [
                    {
                        label: "Cotizaciones",
                        data: [65, 78, 90, 81, 96, 87, 102, 115],
                        borderColor: "#6366F1",
                        backgroundColor: "rgba(99, 102, 241, 0.1)",
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
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "#F3F4F6",
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                        },
                    },
                },
            },
        });
    }

    // Initialize 'proyectosChart' only if element exists
    const proyectosEl = document.getElementById("proyectosChart");
    if (proyectosEl) {
        // Destruir instancia existente si existe
        if (window.proyectosChartInstance) {
            window.proyectosChartInstance.destroy();
        }

        const proyectosCtx = proyectosEl.getContext("2d");
        window.proyectosChartInstance = new Chart(proyectosCtx, {
            type: "bar",
            data: {
                labels: [
                    "En Proceso",
                    "Finalizados",
                    "Pendientes",
                    "Cancelados",
                ],
                datasets: [
                    {
                        data: [234, 187, 23, 12],
                        backgroundColor: [
                            "#06B6D4",
                            "#10B981",
                            "#F59E0B",
                            "#EF4444",
                        ],
                        borderRadius: 6,
                        borderSkipped: false,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: "y",
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: "#F3F4F6",
                        },
                    },
                    y: {
                        grid: {
                            display: false,
                        },
                    },
                },
            },
        });
    }
}

// Inicializar gráficos cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", () => {
    initializeDashboardCharts();
});
