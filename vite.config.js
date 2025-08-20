import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/global.css",
                "resources/js/app.js",
                "resources/js/sidebar.js",
                "resources/js/auth.js",
            ],
            refresh: true,
        }),
    ],
});
