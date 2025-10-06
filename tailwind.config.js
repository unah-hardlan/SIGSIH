/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    darkMode: "class",
    theme: {
        extend: {
            fontFamily: {
                sans: ["Nunito", "sans-serif"],
                serif: ["PT Serif", "serif"],
            },
        },
    },
    variants: {
        extend: {},
    },
    plugins: [],
    corePlugins: {
        preflight: true,
    },
};
