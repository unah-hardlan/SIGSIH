export function notificationsDropdown() {
    return {
        open: false,
        items: [],
        unread: 0,
        timer: null,
        async init() {
            await this.fetchItems();
            this.setupRealtime();
            // Polling fallback cada 45s
            this.timer = setInterval(() => this.fetchItems(), 45000);
        },
        destroy() {
            if (this.timer) clearInterval(this.timer);
        },
        toggle() {
            this.open = !this.open;
        },
        async fetchItems() {
            try {
                const doFetch = window.apiFetch || fetch;
                const res = await doFetch("/api/notifications", {
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                });
                if (!res.ok) return;
                const json = await res.json();
                this.items = json.data || [];
                this.unread = json.unread || 0;
            } catch (_) {
                /* noop */
            }
        },
        async markAll() {
            try {
                const doFetch = window.apiFetch || fetch;
                await doFetch("/api/notifications/mark-all-read", {
                    method: "POST",
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                });
                this.items = this.items.map((n) => ({
                    ...n,
                    read_at: new Date().toISOString(),
                }));
                this.unread = 0;
            } catch (_) { }
        },
        // Modal state for delete confirmation
        deleteModalOpen: false,
        deleteTarget: null,
        openDeleteModal(n) {
            this.deleteTarget = n;
            this.deleteModalOpen = true;
        },
        async confirmDeleteModal() {
            try {
                if (!this.deleteTarget) return;
                await this.deleteNotification(this.deleteTarget);
            } catch (e) {
                console.warn(e);
            } finally {
                this.deleteTarget = null;
                this.deleteModalOpen = false;
            }
        },
        cancelDeleteModal() {
            this.deleteTarget = null;
            this.deleteModalOpen = false;
        },
        async deleteNotification(n) {
            try {
                const doFetch = window.apiFetch || fetch;
                const res = await doFetch(`/api/notifications/${n.id}`, {
                    method: 'DELETE',
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                });
                if (!res.ok) {
                    console.warn('Failed to delete notification', await res.text());
                    return;
                }
                // Remove from local list
                this.items = this.items.filter((it) => it.id !== n.id);
                if (!n.read_at && this.unread > 0) this.unread = Math.max(0, this.unread - 1);
            } catch (e) {
                console.warn('Error deleting notification', e);
            }
        },
        go(n) {
            if (!n.read_at) {
                // Fire and forget mark as read (cookie-auth)
                const doFetch = window.apiFetch || fetch;
                doFetch(`/api/notifications/${n.id}/read`, {
                    method: "POST",
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                });
                n.read_at = new Date().toISOString();
                if (this.unread > 0) this.unread--;
            }
            if (n.url) {
                // Integrate with SPA navigation store if available
                try {
                    const nav = window.Alpine?.store("navigation");
                    if (nav && typeof nav.navigate === "function") {
                        nav.navigate(n.url, n.module || "");
                        this.open = false;
                        return;
                    }
                } catch (_) { }
                window.location.href = n.url;
            }
        },
        setupRealtime() {
            try {
                if (!window.Echo) return;
                let userId = window.__AUTH_USER_ID__;
                if (!userId) {
                    const header = document.querySelector(
                        "header[data-user-id]"
                    );
                    if (header) userId = header.getAttribute("data-user-id");
                }
                if (!userId) return;
                const channel = window.Echo.private(
                    `App.Models.Usuario.${userId}`
                );
                channel.notification((payload) => {
                    const n = {
                        id: payload.id || crypto.randomUUID(),
                        ...payload.data,
                        created_at:
                            payload.created_at || new Date().toISOString(),
                        read_at: null,
                    };
                    this.items.unshift(n);
                    this.items = this.items.slice(0, 15);
                    this.unread += 1;
                });
            } catch (e) {
                console.warn("Notifications realtime off:", e);
            }
        },
        formatTime(iso) {
            try {
                return new Intl.DateTimeFormat(undefined, {
                    dateStyle: "short",
                    timeStyle: "short",
                }).format(new Date(iso));
            } catch {
                return iso;
            }
        },
    };
}

// Expose globally for Alpine inline usage in Blade
if (!window.notificationsDropdown) {
    window.notificationsDropdown = notificationsDropdown;
}
