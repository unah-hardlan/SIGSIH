(function clearLegacyAuthArtifacts() {
    try {
        localStorage.removeItem("authToken");
    } catch (_) {}
})();
