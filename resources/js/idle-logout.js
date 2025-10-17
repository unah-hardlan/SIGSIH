(function () {
    // Config via data attribute or defaults
    const DEFAULT_IDLE_MINUTES = 120; // fallback to 2h if not provided
    const DEFAULT_WARNING_SECONDS = 30; // show warning this many seconds before logout

    function readConfig() {
        try {
            const el = document.getElementById('idle-logout-config');
            if (!el || !el.textContent) return { minutes: DEFAULT_IDLE_MINUTES, warnSeconds: DEFAULT_WARNING_SECONDS };
            const cfg = JSON.parse(el.textContent);
            const minutes = Math.max(1, parseInt(cfg.minutes || DEFAULT_IDLE_MINUTES, 10));
            const warnSeconds = Math.max(5, parseInt(cfg.warnSeconds || DEFAULT_WARNING_SECONDS, 10));
            return { minutes, warnSeconds };
        } catch (_) {
            return { minutes: DEFAULT_IDLE_MINUTES, warnSeconds: DEFAULT_WARNING_SECONDS };
        }
    }

        const cfg = readConfig();
        const idleMs = cfg.minutes * 60 * 1000;
        const rawWarnMs = cfg.warnSeconds * 1000;
        // Asegurar que el aviso nunca sea mayor/igual que el tiempo total de inactividad
        const warnMs = (rawWarnMs >= idleMs)
            ? Math.max(5000, Math.floor(idleMs * 0.2)) // por defecto 20% o mínimo 5s
            : rawWarnMs;
    const storageKey = 'app:lastActivityAt';
    const bc = ('BroadcastChannel' in window) ? new BroadcastChannel('idle-logout') : null;

    let warningTimer = null;
    let logoutTimer = null;
    let warned = false;

    function now() { return Date.now(); }
    function getLastActivity() {
        const v = localStorage.getItem(storageKey);
        const t = v ? parseInt(v, 10) : NaN;
        return Number.isFinite(t) ? t : now();
    }
    function setLastActivity(ts) {
        try { localStorage.setItem(storageKey, String(ts)); } catch (_) { }
        if (bc) { try { bc.postMessage({ type: 'activity', at: ts }); } catch (_) { } }
    }

    function createWarning() {
        const el = document.createElement('div');
        el.id = 'idle-warning';
        el.style.position = 'fixed';
        el.style.inset = '0';
        el.style.display = 'flex';
        el.style.alignItems = 'center';
        el.style.justifyContent = 'center';
        el.style.background = 'rgba(0,0,0,0.5)';
        el.style.zIndex = '99999';
        // No cerrar por click en el overlay; el cierre es explícito
        el.addEventListener('click', (e) => { /* swallow overlay clicks */ });

        const box = document.createElement('div');
        box.style.background = 'white';
        box.style.color = '#111827';
        box.style.padding = '1.25rem';
        box.style.borderRadius = '0.5rem';
        box.style.maxWidth = '26rem';
        box.style.width = '90%';
        box.style.textAlign = 'center';
        // Evitar que clicks dentro del modal burbujeen al overlay
        box.addEventListener('click', (e) => e.stopPropagation());
        box.innerHTML = '<h3 style="font-weight:600;margin-bottom:0.5rem;">Sesión por expirar</h3>' +
            '<p style="margin-bottom:0.5rem;">Tu sesión se cerrará por inactividad.</p>' +
            '<p><span id="idle-countdown"></span></p>' +
            '<div style="margin-top:0.75rem;display:flex;gap:0.5rem;justify-content:center;">' +
            '  <button id="idle-stay" style="background:#2563eb;color:white;padding:0.5rem 0.75rem;border-radius:0.375rem;">Seguir en la sesión</button>' +
            '  <button id="idle-logout-btn" style="background:#ef4444;color:white;padding:0.5rem 0.75rem;border-radius:0.375rem;">Cerrar ahora</button>' +
            '</div>';
        el.appendChild(box);
        document.body.appendChild(el);
        return el;
    }

    function removeWarning() {
        const el = document.getElementById('idle-warning');
        if (el) el.remove();
    }

    function formatSeconds(s) {
        const sec = Math.max(0, Math.floor(s));
        return `${sec}s`;
    }

    function startWarningCountdown(deadline) {
        warned = true;
        const el = createWarning();
        const label = el.querySelector('#idle-countdown');
        const stayBtn = el.querySelector('#idle-stay');
        const logoutBtn = el.querySelector('#idle-logout-btn');

        const tick = () => {
            const remain = Math.max(0, deadline - now());
            if (label) label.textContent = `Se cerrará en ${formatSeconds(remain / 1000)}`;
            if (remain <= 0) {
                clearInterval(warningTimer);
                warningTimer = null;
                if (window.appLogout) {
                    window.appLogout();
                } else {
                    window.location.replace('/login');
                }
            }
        };
        tick();
        warningTimer = setInterval(tick, 500);

        const reset = () => {
            removeWarning();
            if (warningTimer) { clearInterval(warningTimer); warningTimer = null; }
            warned = false;
            markActivity();
        };

        stayBtn?.addEventListener('click', reset);
        logoutBtn?.addEventListener('click', () => {
            if (window.appLogout) window.appLogout(); else window.location.replace('/login');
        });
    }

    function scheduleTimers() {
        if (logoutTimer) { clearTimeout(logoutTimer); logoutTimer = null; }

        // Si ya estamos mostrando la advertencia, no reprogramar hasta que el usuario decida
        if (warned) return;

        const last = getLastActivity();
        const elapsed = now() - last;
        const remain = idleMs - elapsed;

        if (remain <= warnMs) {
            if (!warned) {
                const deadline = now() + Math.max(0, remain);
                startWarningCountdown(deadline);
            }
            logoutTimer = setTimeout(() => {
                if (window.appLogout) window.appLogout(); else window.location.replace('/login');
            }, Math.max(0, remain));
        } else {
            // Schedule warning to appear warnMs before logout
            logoutTimer = setTimeout(() => {
                const deadline = now() + warnMs;
                startWarningCountdown(deadline);
            }, remain - warnMs);
        }
    }

    function markActivity() {
        setLastActivity(now());
        removeWarning();
        warned = false;
        scheduleTimers();
    }

    function attachActivityListeners() {
        const events = ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll', 'visibilitychange'];
        const handler = (e) => {
            // Si el aviso está visible, ignorar actividad pasiva; solo el botón "Seguir" debe cerrar/renovar
            if (warned) return;
            if (e.type === 'visibilitychange') {
                if (!document.hidden) markActivity();
            } else {
                markActivity();
            }
        };
        events.forEach(ev => window.addEventListener(ev, handler, { passive: true }));
    }

    function syncAcrossTabs() {
        window.addEventListener('storage', (e) => {
            if (e.key === storageKey) scheduleTimers();
        });
        if (bc) {
            bc.onmessage = (msg) => {
                if (!msg || !msg.data) return;
                if (msg.data.type === 'activity') scheduleTimers();
                if (msg.data.type === 'logout') {
                    // limpiar timers y modal ante logout en otra pestaña
                    if (logoutTimer) { clearTimeout(logoutTimer); logoutTimer = null; }
                    if (warningTimer) { clearInterval(warningTimer); warningTimer = null; }
                    warned = false;
                    removeWarning();
                }
            };
        }
    }

    // Initialize only on authenticated pages (session.js is also loaded there)
    document.addEventListener('DOMContentLoaded', () => {
        // Siempre registrar actividad al cargar vista autenticada (nuevo login o navegación)
        setLastActivity(now());
        attachActivityListeners();
        syncAcrossTabs();
        scheduleTimers();
    });
})();
