// Centralized session utilities (logout & token update from X-New-JWT)

(function () {
    function applyNewTokenFromHeader(_resp) {
        // Ya no actualizamos token en localStorage; backend podría rotar cookie vía Set-Cookie.
    }

    window.appLogout = function () {
        fetch("/api/logout", {
            method: "POST",
            headers: { Accept: "application/json" },
            credentials: "same-origin",
        }).finally(() => {
            document.cookie = "auth_token=; path=/; Max-Age=0; SameSite=Lax";
            window.location.replace("/login");
        });
    };

    // Wrap fetch to capture X-New-JWT (simple opt-in wrapper)
    window.apiFetch = async function (url, options = {}) {
        const headers = options.headers || {};
        options.headers = headers;
        options.credentials = options.credentials || "same-origin"; // permitirá enviar cookie auth_token
        const resp = await fetch(url, options);
        applyNewTokenFromHeader(resp);
        return resp;
    };
})();
