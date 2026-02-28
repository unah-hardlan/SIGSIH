if (!window.sidebarDropdown) {
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
}

if (!window.sidebarScrollManager) {
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
}

if (!window.initResponsiveSidebar) {
    window.initResponsiveSidebar = function (scope) {
        if (!scope) return;
        scope.isMobile = window.innerWidth < 768;
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
}

document.addEventListener("DOMContentLoaded", () => {
    setTimeout(() => {
        if (window.sidebarScrollManager) {
            window.sidebarScrollManager.init();
        }
    }, 80);
});

document.addEventListener("spa:loaded", (e) => {
    setTimeout(() => {
        if (window.sidebarScrollManager) window.sidebarScrollManager.init();

        try {
            const applyActive = (path) => {
                let targetPath = path || window.location.pathname;
                try {
                    targetPath = new URL(targetPath, window.location.origin)
                        .pathname;
                } catch (e) { }

                const links = document.querySelectorAll(
                    "aside nav a[data-spa-link]"
                );

                const activeClasses = [
                    "bg-blue-600",
                    "text-white",
                    "shadow-md",
                    "font-bold",
                ];
                const inactiveClasses = ["text-gray-800", "dark:text-gray-200"];

                links.forEach((link) => {
                    let href = link.getAttribute("href") || "";
                    try {
                        href = new URL(href, window.location.origin).pathname;
                    } catch (e) { }

                    const isActive =
                        targetPath.startsWith(href) && href !== "/";

                    if (isActive) {
                        link.classList.remove(...inactiveClasses);
                        link.classList.add(...activeClasses);
                    } else {
                        link.classList.remove(...activeClasses);
                        link.classList.add(...inactiveClasses);
                    }
                });
            };

            applyActive(window.location.pathname);

            if (
                window.clienteSPA &&
                typeof window.clienteSPA.updateActiveLink === "function"
            ) {
                window.clienteSPA.updateActiveLink(window.location.pathname);
            }
        } catch (err) {
            console.warn(
                "[sidebar-cliente] Error al actualizar estado tras SPA load",
                err
            );
        }
    }, 60);
});

document.addEventListener("app:view-loaded", () => {
    if (window.sidebarScrollManager) window.sidebarScrollManager.init();
});

export default {};
