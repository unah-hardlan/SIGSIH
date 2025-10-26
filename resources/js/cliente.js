import "./bootstrap";
import "./toast";
import "./cliente/perfil";
import DOMPurify from "dompurify";

import { library, dom } from "@fortawesome/fontawesome-svg-core";
import {
    faUserEdit,
    faSignOutAlt,
    faBell,
    faFileInvoice,
    faClipboardList,
    faFileInvoiceDollar,
    faUser,
    faMoon,
    faSun,
    faHouseChimney,
    faClipboardQuestion,
    faPlus,
} from "@fortawesome/free-solid-svg-icons";

library.add(
    faUserEdit,
    faSignOutAlt,
    faBell,
    faFileInvoice,
    faClipboardList,
    faFileInvoiceDollar,
    faUser,
    faMoon,
    faSun,
    faHouseChimney,
    faClipboardQuestion,
    faPlus
);

dom.watch();

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
    try {
        if (!window.__ALPINE_COLLAPSE_REGISTERED__) {
            Alpine.plugin(collapse);
            window.__ALPINE_COLLAPSE_REGISTERED__ = true;
        }
    } catch (_) {}
});

window.__CLIENTE_BUNDLE_OK__ = true;
console.debug("[cliente.js] bundle cargado");

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
        const mainEl = document.querySelector("main");
        mainEl.innerHTML = DOMPurify.sanitize(html);
        try {
            document.dispatchEvent(new CustomEvent("app:view-loaded"));
        } catch (_) {}
    },

    updateState(url, viewName) {
        window.history.pushState({ viewName }, "", url);
        this.currentView = viewName;
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
});
