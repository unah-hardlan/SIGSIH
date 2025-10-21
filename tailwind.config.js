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
    safelist: [
        "border-2",
        "border-l-4",
        "border-blue-500",
        "border-green-500",
        "border-purple-500",
        "border-indigo-500",
        "border-orange-500",
        "border-pink-500",
    ],
};
