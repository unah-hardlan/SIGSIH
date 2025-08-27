// Centralized session utilities (logout & token update from X-New-JWT)

(function () {
    function applyNewTokenFromHeader(resp) {
        const newJwt = resp.headers.get("X-New-JWT");
        if (newJwt) {
            localStorage.setItem("authToken", newJwt);
        }
    }

    window.appLogout = function () {
        const token = localStorage.getItem("authToken");
        fetch("/api/logout", {
            method: "POST",
            headers: {
                Accept: "application/json",
                ...(token ? { Authorization: "Bearer " + token } : {}),
            },
            credentials: "same-origin",
        }).finally(() => {
            localStorage.removeItem("authToken");
            document.cookie = "auth_token=; path=/; Max-Age=0; SameSite=Lax";
            window.location.replace("/login");
        });
    };

    // Wrap fetch to capture X-New-JWT (simple opt-in wrapper)
    window.apiFetch = async function (url, options = {}) {
        const token = localStorage.getItem("authToken");
        const headers = options.headers || {};
        if (token && !headers["Authorization"]) {
            headers["Authorization"] = "Bearer " + token;
        }
        options.headers = headers;
        options.credentials = options.credentials || "same-origin";
        const resp = await fetch(url, options);
        applyNewTokenFromHeader(resp);
        return resp;
    };
})();
