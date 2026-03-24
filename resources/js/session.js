const AppSession = (() => {
    function applyNewTokenFromHeader(_resp) { }

    async function ensureSessionAliveOnHistoryNavigation() {
        try {
            const resp = await fetch("/session/token", {
                method: "GET",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                credentials: "same-origin",
                cache: "no-store",
            });

            if (resp.status === 401) {
                window.location.replace("/login");
                return;
            }

            if (!resp.ok) {
                window.location.replace("/login");
            }
        } catch (_) {
            window.location.replace("/login");
        }
    }

    function logout() {
        try {
            localStorage.removeItem("app:lastActivityAt");
            if ("BroadcastChannel" in window) {
                const channel = new BroadcastChannel("idle-logout");
                channel.postMessage({ type: "logout" });
                channel.close();
            }
        } catch (_) { }
        fetch("/api/logout", {
            method: "POST",
            headers: { Accept: "application/json" },
            credentials: "same-origin",
        })
            .catch(() => { })
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

    if ("BroadcastChannel" in window) {
        const channel = new BroadcastChannel("idle-logout");
        channel.onmessage = (event) => {
            if (event.data && event.data.type === "logout") {
                logout();
            }
        };
    }

    window.addEventListener("pageshow", (event) => {
        const navEntry =
            typeof performance !== "undefined" && performance.getEntriesByType
                ? performance.getEntriesByType("navigation")[0]
                : null;
        const fromHistory =
            event.persisted ||
            (navEntry && navEntry.type === "back_forward");

        if (fromHistory) {
            ensureSessionAliveOnHistoryNavigation();
        }
    });

    return { logout, apiFetch };
})();

window.appLogout = AppSession.logout;
window.apiFetch = AppSession.apiFetch;
