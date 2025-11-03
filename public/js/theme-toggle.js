(() => {
    function toggleTheme() {
        const html = document.documentElement;
        const button = document.querySelector(".theme-toggle");
        const isDark = html.classList.contains("dark");

        if (button) {
            button.style.transform = "scale(0.9)";
        }

        setTimeout(() => {
            if (isDark) {
                html.classList.remove("dark");
                localStorage.setItem("theme", "light");
            } else {
                html.classList.add("dark");
                localStorage.setItem("theme", "dark");
            }

            if (button) {
                button.style.transform = "scale(1)";
            }
        }, 100);
    }

    window.toggleTheme = toggleTheme;
})();
