(() => {
    const colors = {
        success:
            "bg-green-50 dark:bg-green-950/90 border-green-500 dark:border-green-400 text-green-800 dark:text-green-300",
        error: "bg-red-50 dark:bg-red-950/90 border-red-500 dark:border-red-400 text-red-800 dark:text-red-300",
        warning:
            "bg-yellow-50 dark:bg-yellow-950/90 border-yellow-500 dark:border-yellow-400 text-yellow-800 dark:text-yellow-300",
        info: "bg-blue-50 dark:bg-blue-950/90 border-blue-500 dark:border-blue-400 text-blue-800 dark:text-blue-300",
    };

    const icons = {
        success:
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
        error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
        warning:
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
        info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
    };

    function ensureContainer() {
        let container = document.getElementById("toast-container");
        if (!container) {
            container = document.createElement("div");
            container.id = "toast-container";
            container.className = "fixed top-4 left-4 z-50 space-y-3 max-w-md";
            document.body.appendChild(container);
        }
        return container;
    }

    function showToast(message, type = "info", duration = 5000) {
        if (!message) return;

        const container = ensureContainer();
        const toast = document.createElement("div");
        const colorClass = colors[type] || colors.info;
        const iconPath = icons[type] || icons.info;

        toast.className = `flex items-start gap-3 p-4 rounded-lg border-l-4 shadow-lg transform transition-all duration-300 ease-in-out ${colorClass}`;
        toast.style.opacity = "0";
        toast.style.transform = "translateX(-100%)";

        toast.innerHTML = `
			<svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				${iconPath}
			</svg>
			<div class="flex-1 text-sm font-medium">
				${message}
			</div>
			<button type="button" class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
				</svg>
			</button>
		`;

        const closeButton = toast.querySelector("button");
        closeButton.addEventListener("click", () => toast.remove());

        container.appendChild(toast);

        requestAnimationFrame(() => {
            toast.style.opacity = "1";
            toast.style.transform = "translateX(0)";
        });

        if (duration > 0) {
            setTimeout(() => {
                toast.style.opacity = "0";
                toast.style.transform = "translateX(-100%)";
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }
    }

    window.showToast = showToast;
})();
