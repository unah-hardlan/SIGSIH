(function () {
    function applyNewTokenFromHeader(_resp) {}

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

    window.apiFetch = async function (url, options = {}) {
        const headers = options.headers || {};
        options.headers = headers;
        options.credentials = options.credentials || "same-origin";
        const resp = await fetch(url, options);
        applyNewTokenFromHeader(resp);
        return resp;
    };
})();
