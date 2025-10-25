document.addEventListener("alpine:init", () => {
    window.sidebarDropdown = (key, active = false) => ({
        open:
            sessionStorage.getItem(`sidebar-${key}`) !== null
                ? JSON.parse(sessionStorage.getItem(`sidebar-${key}`))
                : active,
        toggle() {
            this.open = !this.open;
            sessionStorage.setItem(`sidebar-${key}`, this.open);
        },
        close() {
            this.open = false;
            sessionStorage.setItem(`sidebar-${key}`, false);
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

window.initResponsiveSidebar = function (scope) {
    if (!scope) return;

    scope.isMobile = window.innerWidth < 768;

    if (scope.isMobile) {
        scope.sidebarOpen = false;
    } else {
        scope.sidebarOpen = true;
    }

    function toggleBodyOverflow(sidebarOpen, isMobile) {
        if (isMobile && sidebarOpen) {
            document.body.style.overflow = "hidden";
        } else {
            document.body.style.overflow = "";
        }
    }

    scope.$watch("sidebarOpen", (newValue) => {
        toggleBodyOverflow(newValue, scope.isMobile);
    });

    function checkMobile() {
        var wasMobile = scope.isMobile;
        scope.isMobile = window.innerWidth < 768;

        if (wasMobile && !scope.isMobile) {
            scope.sidebarOpen = true;
            document.body.style.overflow = "";
        } else if (!wasMobile && scope.isMobile) {
            scope.sidebarOpen = false;
            document.body.style.overflow = "";
        }
    }

    window.addEventListener("resize", checkMobile);

    // Aplicar el estado inicial
    toggleBodyOverflow(scope.sidebarOpen, scope.isMobile);
};
