// Bundle ligero para portal CLIENTE (sin módulos administrativos que generan 403)
// Solo incluye lo necesario para iconos, Alpine helpers básicos y utilidades compartidas.

import "./bootstrap"; // axios / window helpers
import "./toast"; // notificaciones si las usas en cliente
// Puedes añadir otros módulos específicos del cliente aquí (ej: './perfil', './dashboard-cliente')

// FontAwesome: cargar solo el subconjunto usado en el portal cliente
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

// Plugin collapse mínimo (copiado de app.js pero sin importar módulos admin)
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

// Exponer bandera para diagnosticar en producción
window.__CLIENTE_BUNDLE_OK__ = true;
console.debug("[cliente.js] bundle cargado");
