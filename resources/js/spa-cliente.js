/**
 * Sistema de navegación SPA para el portal del cliente
 * Gestiona la carga dinámica de contenido sin recargar la página completa
 */

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
        // Esperar a que el DOM esté listo
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

        // Manejar navegación del historial (botones adelante/atrás)
        window.addEventListener("popstate", (e) => {
            if (e.state && e.state.path) {
                this.loadPage(e.state.path, false);
            }
        });

        // Guardar estado inicial
        history.replaceState(
            { path: this.currentRoute },
            "",
            this.currentRoute
        );
    }

    createLoadingOverlay() {
        this.loadingOverlay = document.createElement("div");
        this.loadingOverlay.className =
            "fixed inset-0 bg-black/20 dark:bg-black/40 backdrop-blur-sm z-[9998] hidden items-center justify-center";
        this.loadingOverlay.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 flex items-center gap-4">
                <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-700 dark:text-gray-200 font-medium">Cargando...</span>
            </div>
        `;
        document.body.appendChild(this.loadingOverlay);
    }

    setupEventListeners() {
        // Escuchar evento personalizado para navegación programática
        document.addEventListener("spa:navigate", (e) => {
            if (e.detail && e.detail.path) {
                this.navigateTo(e.detail.path);
            }
        });

        // Escuchar evento para limpiar caché
        document.addEventListener("spa:clearCache", () => {
            this.cache.clear();
            console.log("[ClienteSPA] Caché limpiado");
        });
    }

    interceptLinks() {
        // Interceptar clicks en links del sidebar
        document.addEventListener(
            "click",
            (e) => {
                // Buscar el enlace más cercano con data-spa-link o que empiece con /cliente/
                const link = e.target.closest(
                    'a[data-spa-link], a[href^="/cliente/"]'
                );

                if (link && !link.hasAttribute("data-no-spa")) {
                    const href = link.getAttribute("href");

                    // Solo interceptar si es una URL relativa del cliente
                    if (
                        href &&
                        (href.startsWith("/cliente/") ||
                            link.hasAttribute("data-spa-link"))
                    ) {
                        e.preventDefault();
                        e.stopPropagation();

                        this.navigateTo(href);

                        // Cerrar sidebar móvil si está abierto
                        if (window.innerWidth < 768) {
                            window.dispatchEvent(
                                new CustomEvent("closemobilesidebar")
                            );
                        }
                    }
                }
            },
            true
        ); // Usar capturing phase para interceptar antes
    }

    async navigateTo(path) {
        if (this.isLoading || path === this.currentRoute) {
            return;
        }

        // Actualizar historial
        history.pushState({ path }, "", path);
        await this.loadPage(path, true);
    }

    async loadPage(path, updateHistory = false) {
        if (this.isLoading) return;

        this.isLoading = true;
        this.showLoading();

        try {
            // Verificar si está en caché
            let html;
            if (this.cache.has(path)) {
                html = this.cache.get(path);
            } else {
                html = await this.fetchPage(path);
                this.cache.set(path, html);
            }

            // Actualizar contenido
            this.updateContent(html);
            this.currentRoute = path;

            // Actualizar estado activo del sidebar
            this.updateActiveLink(path);

            // Scroll al inicio
            window.scrollTo({ top: 0, behavior: "smooth" });

            // Emitir evento de navegación completada
            document.dispatchEvent(
                new CustomEvent("spa:loaded", { detail: { path } })
            );
        } catch (error) {
            console.error("[ClienteSPA] Error al cargar página:", error);
            this.showError(
                "Error al cargar el contenido. Por favor, intenta de nuevo."
            );

            // En caso de error, recargar la página completa
            setTimeout(() => {
                window.location.href = path;
            }, 2000);
        } finally {
            this.isLoading = false;
            this.hideLoading();
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
        // Crear un contenedor temporal para parsear el HTML
        const temp = document.createElement("div");
        temp.innerHTML = html;

        // Extraer solo el contenido principal
        const newContent = temp.querySelector("main > *:last-child");

        if (!newContent) {
            throw new Error("No se encontró contenido válido en la respuesta");
        }

        // Actualizar el contenido del último elemento de main (el div con el @yield('content'))
        const currentContent = this.contentContainer.querySelector(
            ":scope > *:last-child"
        );
        if (currentContent) {
            // Fade out
            currentContent.style.opacity = "0";
            currentContent.style.transform = "translateY(10px)";

            setTimeout(() => {
                currentContent.replaceWith(newContent);

                // PRIMERO: Ejecutar scripts inline ANTES de inicializar Alpine
                this.reinitializeScripts(newContent);

                // Fade in
                newContent.style.opacity = "0";
                newContent.style.transform = "translateY(10px)";
                newContent.style.transition =
                    "opacity 0.3s ease, transform 0.3s ease";

                setTimeout(() => {
                    newContent.style.opacity = "1";
                    newContent.style.transform = "translateY(0)";
                }, 10);

                // DESPUÉS: Reinicializar Alpine.js después de que los scripts se ejecuten
                setTimeout(() => {
                    if (
                        window.Alpine &&
                        typeof window.Alpine.initTree === "function"
                    ) {
                        window.Alpine.initTree(newContent);
                    }
                }, 50);
            }, 200);
        }
    }

    reinitializeScripts(container) {
        // Ejecutar scripts inline si existen
        const scripts = container.querySelectorAll("script");

        scripts.forEach((script, index) => {
            if (script.textContent && script.textContent.trim()) {
                try {
                    // Crear un nuevo script element para asegurar la ejecución
                    const newScript = document.createElement("script");
                    newScript.textContent = script.textContent;

                    // Copiar atributos si existen
                    Array.from(script.attributes).forEach((attr) => {
                        newScript.setAttribute(attr.name, attr.value);
                    });

                    // Reemplazar el script antiguo con el nuevo
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
        // Remover clase activa de todos los links
        const links = document.querySelectorAll("aside nav a");
        links.forEach((link) => {
            link.classList.remove("text-white", "bg-blue-600", "shadow-sm");
            link.classList.add(
                "text-gray-300",
                "hover:bg-gray-700",
                "hover:text-white"
            );
        });

        // Agregar clase activa al link actual
        const activeLink = document.querySelector(
            `aside nav a[href="${path}"]`
        );
        if (activeLink) {
            activeLink.classList.remove(
                "text-gray-300",
                "hover:bg-gray-700",
                "hover:text-white"
            );
            activeLink.classList.add("text-white", "bg-blue-600", "shadow-sm");
        }
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
        // Mostrar toast de error si existe la función
        if (window.showToast) {
            window.showToast(message, "error");
        } else {
            alert(message);
        }
    }

    // Método público para limpiar caché de una ruta específica
    clearCacheForRoute(path) {
        this.cache.delete(path);
    }

    // Método público para precargar una ruta
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

// Inicializar SPA cuando el script se cargue
let clienteSPA;

// Asegurar que solo se inicialice una vez
if (typeof window !== "undefined" && !window.clienteSPA) {
    // Esperar a que el DOM esté completamente cargado
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", () => {
            clienteSPA = new ClienteSPA();
            window.clienteSPA = clienteSPA;
            console.log(
                "[ClienteSPA] Inicializado después de DOMContentLoaded"
            );
        });
    } else {
        clienteSPA = new ClienteSPA();
        window.clienteSPA = clienteSPA;
    }
}

export default ClienteSPA;
