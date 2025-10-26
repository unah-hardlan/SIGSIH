const AppSession = (() => {
    function applyNewTokenFromHeader(_resp) {
        // Placeholder for token refresh logic if needed
    }

    function logout() {
        try {
            localStorage.removeItem("app:lastActivityAt");
            if ("BroadcastChannel" in window) {
                const channel = new BroadcastChannel("idle-logout");
                channel.postMessage({ type: "logout" });
                channel.close();
            }
        } catch (_) {}
        fetch("/api/logout", {
            method: "POST",
            headers: { Accept: "application/json" },
            credentials: "same-origin",
        })
            .catch(() => {
                // Handle fetch error if needed
            })
            .finally(() => {
                document.cookie =
                    "auth_token=; path=/; Max-Age=0; SameSite=Strict; Secure";
                window.location.replace("/login");
            });
    }

    async function apiFetch(url, options = {}) {
        const headers = options.headers || {};
        options.headers = headers;
        options.credentials = options.credentials || "same-origin";
        const resp = await fetch(url, options);
        if (!resp.ok) {
            throw new Error(`HTTP error! status: ${resp.status}`);
        }
        applyNewTokenFromHeader(resp);
        return resp;
    }

    // Listener for BroadcastChannel
    if ("BroadcastChannel" in window) {
        const channel = new BroadcastChannel("idle-logout");
        channel.onmessage = (event) => {
            if (event.data && event.data.type === "logout") {
                logout();
            }
        };
    }

    return { logout, apiFetch };
})();

// Expose globally for backward compatibility
window.appLogout = AppSession.logout;
window.apiFetch = AppSession.apiFetch;
