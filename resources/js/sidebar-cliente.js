// Sidebar específico para el portal del cliente
// Incluye: dropdown persistente, gestión de scroll, responsive y hooks para SPA cliente

document.addEventListener("alpine:init", () => {
    // Dropdown para secciones del sidebar (persistente en localStorage)
    window.sidebarDropdown = (key, active = false) => ({
        open:
            localStorage.getItem(`sidebar-${key}`) !== null
                ? JSON.parse(localStorage.getItem(`sidebar-${key}`))
                : active,
        toggle() {
            this.open = !this.open;
            localStorage.setItem(`sidebar-${key}`, this.open);
        },
        init() {
            document.addEventListener("update-sidebar-dropdown", (event) => {
                if (event.detail.key === key) {
                    this.open = event.detail.open;
                }
            });
        },
    });

    // Manager de scroll del sidebar (salva/rehidrata posición)
    window.sidebarScrollManager = {
        init() {
            const sidebar = document.querySelector("aside");
            if (!sidebar) return;

            this.restoreScrollPosition(sidebar);

            this.setupScrollListener(sidebar);
        },

        restoreScrollPosition(sidebar) {
            const savedScrollTop = localStorage.getItem(
                "sidebar-scroll-position"
            );
            if (savedScrollTop !== null) {
                requestAnimationFrame(() => {
                    sidebar.scrollTop = parseInt(savedScrollTop, 10);
                });
            }
        },

        setupScrollListener(sidebar) {
            let scrollTimeout;

            sidebar.addEventListener("scroll", () => {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    localStorage.setItem(
                        "sidebar-scroll-position",
                        sidebar.scrollTop
                    );
                }, 100);
            });
        },
    };

    // Inicializador responsive pensado para el cliente.
    // Este método es llamado desde el x-init del layout cliente: initResponsiveSidebar($data)
    window.initResponsiveSidebar = function (scope) {
        if (!scope) return;

        scope.isMobile = window.innerWidth < 768;

        // Por defecto mostrar sidebar en escritorio y ocultarlo en móvil
        scope.sidebarOpen = !scope.isMobile;

        function checkMobile() {
            var wasMobile = scope.isMobile;
            scope.isMobile = window.innerWidth < 768;

            if (wasMobile && !scope.isMobile) {
                scope.sidebarOpen = true;
            } else if (!wasMobile && scope.isMobile) {
                scope.sidebarOpen = false;
            }
        }

        window.addEventListener("resize", checkMobile);
    };
});

// Re-inicializaciones y hooks fuera de Alpine.init
document.addEventListener("DOMContentLoaded", () => {
    // Inicializar scroll manager (con leve retraso para asegurar layout)
    setTimeout(() => {
        if (window.sidebarScrollManager) {
            window.sidebarScrollManager.init();
        }
    }, 80);
});

// Cuando la SPA del cliente carga una nueva vista, re-aplicar comportamiento del sidebar
document.addEventListener("spa:loaded", (e) => {
    // Re-iniciar el scroll manager y actualizar link activo
    setTimeout(() => {
        if (window.sidebarScrollManager) window.sidebarScrollManager.init();

        try {
            // Marcar el link activo usando las mismas clases que el blade del sidebar
            const applyActive = (path) => {
                // Normalizar path
                let targetPath = path || window.location.pathname;
                try {
                    targetPath = new URL(targetPath, window.location.origin)
                        .pathname;
                } catch (e) {
                    // ignore
                }

                const links = document.querySelectorAll("aside nav a");

                links.forEach((link) => {
                    // Clases definidas en Blade
                    const activeClasses = [
                        "text-white",
                        "bg-blue-600",
                        "shadow-md",
                        "font-bold",
                    ];
                    const inactiveClasses = [
                        "text-gray-800",
                        "hover:bg-gray-200",
                        "hover:text-gray-900",
                    ];

                    // Icon/text color classes
                    const iconActive = ["text-white"];
                    const iconInactive = [
                        "text-gray-600",
                        "group-hover:text-gray-700",
                    ];
                    const textActive = ["text-white"];
                    const textInactive = [
                        "text-gray-800",
                        "group-hover:text-gray-900",
                    ];

                    // Remove all possible active/inactive classes to avoid duplicates
                    link.classList.remove(...activeClasses, ...inactiveClasses);

                    // Determine link href pathname
                    let href = link.getAttribute("href") || "";
                    try {
                        href = new URL(href, window.location.origin).pathname;
                    } catch (e) {
                        // ignore
                    }

                    const isActive = href === targetPath;

                    if (isActive) {
                        link.classList.add(...activeClasses);
                        // icon
                        const icon = link.querySelector("i");
                        if (icon) {
                            icon.classList.remove(
                                ...iconInactive,
                                ...iconActive
                            );
                            icon.classList.add(...iconActive);
                            // Quitar color inline para que herede el color del link activo
                            try {
                                icon.style.removeProperty("color");
                            } catch (e) {}
                        }
                        // span
                        const span = link.querySelector("span");
                        if (span) {
                            span.classList.remove(
                                ...textInactive,
                                ...textActive
                            );
                            span.classList.add(...textActive);
                        }
                    } else {
                        link.classList.add(...inactiveClasses);
                        const icon = link.querySelector("i");
                        if (icon) {
                            icon.classList.remove(
                                ...iconInactive,
                                ...iconActive
                            );
                            icon.classList.add(...iconInactive);
                            // Asegurar que no hay color inline que fuerce otro color
                            try {
                                icon.style.removeProperty("color");
                            } catch (e) {}
                        }
                        const span = link.querySelector("span");
                        if (span) {
                            span.classList.remove(
                                ...textInactive,
                                ...textActive
                            );
                            span.classList.add(...textInactive);
                        }
                    }
                });
            };

            if (
                window.clienteSPA &&
                typeof window.clienteSPA.updateActiveLink === "function"
            ) {
                // dejar que la SPA maneje (ya está en spa-cliente.js) pero pasar el path para sincronizar
                window.clienteSPA.updateActiveLink(window.location.pathname);
            }

            // Aplicar de todos modos para asegurar consistencia
            applyActive(window.location.pathname);
        } catch (err) {
            console.warn(
                "[sidebar-cliente] Error al actualizar estado tras SPA load",
                err
            );
        }
    }, 60);
});

// También escuchar evento genérico del layout cuando se carga una vista (por ejemplo después de navegaciones no-SPA)
document.addEventListener("app:view-loaded", () => {
    if (window.sidebarScrollManager) window.sidebarScrollManager.init();
});

// Export por compatibilidad de módulos ESM (vite)
export default {};
