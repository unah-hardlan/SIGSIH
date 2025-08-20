// resources/js/auth-guard.js
// Pequeño guard de defensa en profundidad: valida JWT almacenado en localStorage
// El backend (middleware) sigue siendo la autoridad principal.
(function(){
    try {
        const raw = localStorage.getItem('authToken');
        if(!raw){
            return; // sin copia, backend decidirá
        }
        const parts = raw.split('.');
        if(parts.length !== 3) return;
        // Decodificación base64url
        const payload = JSON.parse(atob(parts[1].replace(/-/g,'+').replace(/_/g,'/')));
        if(payload.exp && Date.now()/1000 >= payload.exp){
            localStorage.removeItem('authToken');
            window.location.replace('/login');
        }
    } catch(e){
        // Silencioso: no romper la carga de la página
    }
})();
