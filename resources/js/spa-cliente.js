class ClienteSPA {
    constructor() {
        this.currentRoute = window.location.pathname;
        this.contentContainer = null;
        this.loadingOverlay = null;
        this.cache = new Map();
        this.isLoading = false;
        this.init();
    }

    init() {
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", () => this.setup());
        } else {
            this.setup();
        }
    }

    setup() {
        this.contentContainer = document.querySelector("main");
        if (!this.contentContainer) {
            console.warn("[ClienteSPA] No se encontró el contenedor principal");
            return;
        }
        this.createLoadingOverlay();
        this.setupEventListeners();
        this.interceptLinks();
        window.addEventListener("popstate", (e) => {
            if (e.state && e.state.path) {
                this.loadPage(e.state.path, false);
            }
        });
        history.replaceState(
            { path: this.currentRoute },
            "",
            this.currentRoute
        );
    }

    createLoadingOverlay() {}

    setupEventListeners() {
        document.addEventListener("spa:navigate", (e) => {
            if (e.detail && e.detail.path) {
                this.navigateTo(e.detail.path);
            }
        });
        document.addEventListener("spa:clearCache", () => {
            this.cache.clear();
            console.log("[ClienteSPA] Caché limpiado");
        });
    }

    interceptLinks() {
        document.addEventListener(
            "click",
            (e) => {
                const link = e.target.closest(
                    'a[data-spa-link], a[href^="/cliente/"]'
                );
                if (link && !link.hasAttribute("data-no-spa")) {
                    const href = link.getAttribute("href");
                    if (
                        href &&
                        (href.startsWith("/cliente/") ||
                            link.hasAttribute("data-spa-link"))
                    ) {
                        e.preventDefault();
                        e.stopPropagation();
                        this.navigateTo(href);
                        if (window.innerWidth < 768) {
                            window.dispatchEvent(
                                new CustomEvent("closemobilesidebar")
                            );
                        }
                    }
                }
            },
            true
        );
    }

    async navigateTo(path) {
        if (this.isLoading || path === this.currentRoute) {
            return;
        }
        history.pushState({ path }, "", path);
        await this.loadPage(path, true);
    }

    async loadPage(path, updateHistory = false) {
        if (this.isLoading) return;
        this.isLoading = true;
        try {
            let html;
            if (this.cache.has(path)) {
                html = this.cache.get(path);
            } else {
                html = await this.fetchPage(path);
                this.cache.set(path, html);
            }
            this.updateContent(html);
            this.currentRoute = path;
            this.updateActiveLink(path);
            window.scrollTo({ top: 0, behavior: "smooth" });
            document.dispatchEvent(
                new CustomEvent("spa:loaded", { detail: { path } })
            );
        } catch (error) {
            console.error("[ClienteSPA] Error al cargar página:", error);
            this.showError(
                "Error al cargar el contenido. Por favor, intenta de nuevo."
            );
            setTimeout(() => {
                window.location.href = path;
            }, 2000);
        } finally {
            this.isLoading = false;
        }
    }

    async fetchPage(path) {
        const response = await fetch(path, {
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-SPA-Request": "true",
                Accept: "text/html",
            },
        });
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        const html = await response.text();
        return html;
    }

    updateContent(html) {
        const temp = document.createElement("div");
        temp.innerHTML = html;
        this.contentContainer.innerHTML = temp.querySelector("main").innerHTML;
        this.reinitializeScripts(this.contentContainer);
        try {
            document.dispatchEvent(new CustomEvent("app:view-loaded"));
        } catch (_) {}
    }

    reinitializeScripts(container) {
        const scripts = container.querySelectorAll("script");
        scripts.forEach((script, index) => {
            if (script.textContent && script.textContent.trim()) {
                try {
                    const newScript = document.createElement("script");
                    newScript.textContent = script.textContent;
                    Array.from(script.attributes).forEach((attr) => {
                        newScript.setAttribute(attr.name, attr.value);
                    });
                    script.parentNode.replaceChild(newScript, script);
                } catch (e) {
                    console.error(
                        `[ClienteSPA] Error al ejecutar script ${index + 1}:`,
                        e
                    );
                }
            }
        });
    }

    updateActiveLink(path) {
        let targetPath = path || window.location.pathname;
        try {
            targetPath = new URL(targetPath, window.location.origin).pathname;
        } catch (e) {}

        const links = document.querySelectorAll("aside nav a[data-spa-link]");
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
            } catch (e) {}

            const isActive = targetPath.startsWith(href) && href !== "/";

            if (isActive) {
                link.classList.remove(...inactiveClasses);
                link.classList.add(...activeClasses);
            } else {
                link.classList.remove(...activeClasses);
                link.classList.add(...inactiveClasses);
            }
        });
    }

    showLoading() {
        if (this.loadingOverlay) {
            this.loadingOverlay.classList.remove("hidden");
            this.loadingOverlay.classList.add("flex");
        }
    }

    hideLoading() {
        if (this.loadingOverlay) {
            this.loadingOverlay.classList.add("hidden");
            this.loadingOverlay.classList.remove("flex");
        }
    }

    showError(message) {
        if (window.showToast) {
            window.showToast(message, "error");
        } else {
            alert(message);
        }
    }

    clearCacheForRoute(path) {
        this.cache.delete(path);
    }

    async preloadRoute(path) {
        if (!this.cache.has(path)) {
            try {
                const html = await this.fetchPage(path);
                this.cache.set(path, html);
                console.log(`[ClienteSPA] Ruta precargada: ${path}`);
            } catch (error) {
                console.warn(`[ClienteSPA] Error al precargar: ${path}`, error);
            }
        }
    }
}

let clienteSPA;

if (typeof window !== "undefined" && !window.clienteSPA) {
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", () => {
            clienteSPA = new ClienteSPA();
            window.clienteSPA = clienteSPA;
        });
    } else {
        clienteSPA = new ClienteSPA();
        window.clienteSPA = clienteSPA;
    }
}

export default ClienteSPA;
