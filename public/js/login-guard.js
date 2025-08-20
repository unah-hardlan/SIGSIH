// public/js/login-guard.js
// Redirige fuera de la pantalla de login si ya existe un JWT válido en localStorage.
(function(){
    try {
        const t = localStorage.getItem('authToken');
        if(!t) return;
        const parts = t.split('.');
        if(parts.length !== 3) return;
        const payload = JSON.parse(atob(parts[1].replace(/-/g,'+').replace(/_/g,'/')));
        if(payload.exp && Date.now()/1000 < payload.exp){
            window.location.replace('/admin/dashboard');
        }
    } catch(e) { /* silencioso */ }
})();
