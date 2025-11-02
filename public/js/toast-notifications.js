(() => {
    const colors = {
        success:
            "bg-green-50 border-green-500 text-green-800 dark:bg-green-900/90 dark:border-green-300 dark:text-green-100",
        error: "bg-red-50 border-red-500 text-red-800 dark:bg-red-900/90 dark:border-red-300 dark:text-red-100",
        warning:
            "bg-yellow-50 border-yellow-500 text-yellow-900 dark:bg-yellow-900/90 dark:border-yellow-300 dark:text-yellow-100",
        info: "bg-blue-50 border-blue-500 text-blue-800 dark:bg-blue-900/90 dark:border-blue-300 dark:text-blue-100",
    };

    const icons = {
        success:
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
        error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
        warning:
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
        info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
    };

    function ensureContainer(position = "top-left") {
        const id = `toast-container-${position}`;
        let container = document.getElementById(id);
        if (!container) {
            container = document.createElement("div");
            container.id = id;
            const base = "fixed z-50 space-y-3 max-w-md";
            const posClass =
                position === "top-right"
                    ? "top-4 right-4"
                    : position === "bottom-right"
                    ? "bottom-4 right-4"
                    : position === "bottom-left"
                    ? "bottom-4 left-4"
                    : "top-4 left-4";
            container.className = `${base} ${posClass}`;
            document.body.appendChild(container);
        }
        return container;
    }

    function showToast(message, type = "info", options = 5000) {
        if (!message) return;

        let duration = 5000;
        let position = "top-left";
        if (typeof options === "number") {
            duration = options;
        } else if (options && typeof options === "object") {
            duration =
                typeof options.duration === "number"
                    ? options.duration
                    : duration;
            position = options.position || position;
        }

        const container = ensureContainer(position);
        const toast = document.createElement("div");
        const colorClass = colors[type] || colors.info;
        const iconPath = icons[type] || icons.info;

        toast.className = `flex items-start gap-3 p-4 rounded-lg border-l-4 shadow-lg backdrop-blur-sm transform transition-all duration-300 ease-in-out ${colorClass}`;
        toast.style.opacity = "0";
        toast.style.transform = position.includes("right")
            ? "translateX(100%)"
            : "translateX(-100%)";

        toast.innerHTML = `
			<svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				${iconPath}
			</svg>
			<div class="flex-1 text-sm font-medium">
				${message}
			</div>
			<button type="button" class="flex-shrink-0 text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white">
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
                toast.style.transform = position.includes("right")
                    ? "translateX(100%)"
                    : "translateX(-100%)";
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }
    }

    window.showToast = showToast;
})();
