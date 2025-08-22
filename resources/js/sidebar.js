document.addEventListener("alpine:init", () => {
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
});

document.addEventListener("DOMContentLoaded", () => {
    setTimeout(() => {
        if (window.sidebarScrollManager) {
            window.sidebarScrollManager.init();
        }
    }, 100);
});

// Helper para inicializar la lógica responsive del sidebar desde x-init sin usar "const" dentro del atributo
window.initResponsiveSidebar = function (scope) {
    if (!scope) return;

    // Configuración inicial
    scope.isMobile = window.innerWidth < 768;

    // Establecer estado inicial del sidebar según el tipo de dispositivo
    if (scope.isMobile) {
        scope.sidebarOpen = false; // En móviles, cerrado por defecto
    } else {
        scope.sidebarOpen = true; // En desktop, abierto por defecto
    }

    function checkMobile() {
        var wasMobile = scope.isMobile;
        scope.isMobile = window.innerWidth < 768;

        // Al cambiar de móvil a desktop, abrir sidebar
        if (wasMobile && !scope.isMobile) {
            scope.sidebarOpen = true;
        }
        // Al cambiar de desktop a móvil, cerrar sidebar
        else if (!wasMobile && scope.isMobile) {
            scope.sidebarOpen = false;
        }
    }

    window.addEventListener("resize", checkMobile);
};
