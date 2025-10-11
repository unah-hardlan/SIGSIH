import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/global.css",
                "resources/css/theme.css",
                "resources/css/auth.css",
                "resources/css/auth.css",
                "resources/js/app.js",
                "resources/js/cliente.js",
                "resources/js/sidebar.js",
                "resources/js/auth.js",
                "resources/js/session.js",
                "resources/js/auth-guard.js",
                "resources/js/login-guard.js",
                "resources/js/toast.js",
                "resources/js/tabla-responsive.js",
                "resources/js/spa-cliente.js",
                "resources/js/cliente/perfil.js",
                "resources/js/tipo-movimientos.js",
                "resources/js/servicios-realizados.js",
                "resources/js/tipo-objetos.js",
            ],
            refresh: true,
        }),
    ],
});
