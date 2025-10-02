// EntryPoint dedicado al PORTAL CLIENTE (sin módulos administrativos)
// Añade aquí solo lo que el cliente necesita.

import "./bootstrap";
import "./toast";
// Si el cliente puede editar su perfil, mantenlo. Si no, eliminar esta línea.
import "./perfil";

// Iconos mínimos para cliente
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
    faHouseChimney
);

dom.watch();

// Plugin collapse (si alguna sección lo usa)
function collapse(Alpine) {
    Alpine.directive(
        "collapse",
        (el, { expression }, { effect, evaluateLater }) => {
            const duration = 200;
            el.style.height = "0px";
            el.style.overflow = "hidden";
            el.style.transitionProperty = "height";
            el.style.transitionDuration = `${duration}ms`;
            el.style.transitionTimingFunction = "ease-in-out";

            effect(() => {
                const show = evaluateLater(expression);
                show((val) => {
                    el.style.height = val ? el.scrollHeight + "px" : "0px";
                });
            });
        }
    );
}

document.addEventListener("alpine:init", () => {
    try {
        if (!window.__ALPINE_COLLAPSE_CLIENTE__) {
            Alpine.plugin(collapse);
            window.__ALPINE_COLLAPSE_CLIENTE__ = true;
        }
    } catch (_) {}
});

window.__CLIENTE_ENTRY__ = true;
console.debug("[appCliente.js] cargado");
