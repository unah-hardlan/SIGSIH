

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';



let ENABLE_ECHO = false;
try {
	ENABLE_ECHO = !!(import.meta.env.VITE_PUSHER_APP_KEY);
} catch (_) { }

if (ENABLE_ECHO) {
	(async () => {
		try {
			const { default: Echo } = await import('laravel-echo');
			const Pusher = (await import('pusher-js')).default;
			window.Pusher = Pusher;
			window.Echo = new Echo({
				broadcaster: 'pusher',
				key: import.meta.env.VITE_PUSHER_APP_KEY,
				cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
				wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
				wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
				wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
				forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
				enabledTransports: ['ws', 'wss'],
			});
		} catch (e) {
			console.warn('Realtime disabled (Echo init failed):', e);
		}
	})();
}
