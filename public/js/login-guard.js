// public/js/login-guard.js
// Limpia cualquier residuo de authToken en localStorage (ya no se usa).
(function () {
    try { localStorage.removeItem('authToken'); } catch (e) { }
})();
