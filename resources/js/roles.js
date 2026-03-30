const API = { roles: "/api/roles" };

const authHeaders = () => ({
    "Content-Type": "application/json",
    Accept: "application/json",
    "X-Requested-With": "XMLHttpRequest",
});

const hasConfiguracionAcceso = () => {
    try {
        const main = document.querySelector("main");
        return (main?.dataset?.canConfiguracionAcceso || "") === "1";
    } catch (_) {
        return false;
    }
};

const normalizeRoleName = (s) =>
    (s || "")
        .toString()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .toLowerCase()
        .trim();
const HIDDEN_ROLE_NAMES = new Set(["cliente"]);
const filterHiddenRoles = (list) =>
    list.filter(
        (role) => !HIDDEN_ROLE_NAMES.has(normalizeRoleName(role?.rol || ""))
    );

function normalizeList(payload) {
    if (Array.isArray(payload)) return payload;
    if (Array.isArray(payload?.data)) return payload.data;
    return [];
}
function normalizeMeta(payload) {
    if (payload?.meta) return payload.meta;
    const list = normalizeList(payload);
    return { page: 1, per_page: list.length, total: list.length, last_page: 1 };
}

async function apiSend(url, method, body) {
    const r = await fetch(url, {
        method,
        headers: authHeaders(),
        credentials: "same-origin",
        body: body ? JSON.stringify(body) : undefined,
    });
    if (!r.ok) {
        throw new Error(await parseApiError(r, "No se pudo completar la operación"));
    }
    return r.json();
}

async function parseApiError(response, fallback = "No se pudo completar la operación") {
    try {
        const data = await response.json();
        const message =
            data?.message ||
            data?.error ||
            (typeof data === "string" ? data : "");
        if (message) return normalizeFriendlyError(message);
    } catch (_) {
        try {
            const rawText = await response.text();
            if (rawText) return normalizeFriendlyError(rawText);
        } catch (_) { }
    }
    return normalizeFriendlyError(fallback);
}

function normalizeFriendlyError(rawMessage) {
    const msg = String(rawMessage || "").trim();
    if (!msg) return "No se pudo completar la operación";

    // Evita mostrar objetos JSON crudos o errores SQL técnicos.
    const normalized = msg.toLowerCase();
    if (
        normalized.includes("restricción de integridad") ||
        normalized.includes("restriccion de integridad") ||
        normalized.includes("integrity constraint") ||
        normalized.includes("foreign key") ||
        normalized.includes("sqlstate")
    ) {
        return "No se puede eliminar este rol porque tiene usuarios o permisos asociados.";
    }

    if (msg.startsWith("{") && msg.endsWith("}")) {
        return "No se pudo completar la operación solicitada.";
    }

    return msg;
}

function createRolesStore() {
    return {
        loading: false,
        error: "",
        items: [],
        meta: { page: 1, per_page: 10, total: 0, last_page: 1 },
        q: "",
        sort: "rol",
        direction: "asc",
        perPage: 10,
        _abortCtrl: null,
        blocked: false,

        allItems: [],
        currentPage: 1,

        isCreateOpen: false,
        isEditOpen: false,
        isDeleteOpen: false,
        form: { rol: "", descripcion_rol: "" },
        current: null,

        currentView() {
            try {
                return (
                    document.querySelector("main")?.dataset?.currentView || ""
                );
            } catch (_) {
                return "";
            }
        },
        async init() {
            if (this._initialized) return;
            if (!hasConfiguracionAcceso()) {
                this.blocked = true;
                this.items = [];
                this.meta = { page: 1, per_page: 10, total: 0, last_page: 1 };
                this.error =
                    "No tienes permisos para ver los roles del sistema.";
                return;
            }
            // Permitir inicialización cuando se llama explícitamente desde la pestaña, sin depender de currentView.
            this.blocked = false;
            this._initialized = true;
            await this.fetchList(1);
        },
        buildQuery(page) {
            const params = new URLSearchParams();
            params.set("per_page", "10000");
            params.set("page", "1");
            if (this.q) params.set("q", this.q);
            if (this.sort) params.set("sort", this.sort);
            if (this.direction) params.set("direction", this.direction);
            return `${API.roles}?${params.toString()}`;
        },
        async fetchList(page = 1) {
            if (!hasConfiguracionAcceso()) {
                this.blocked = true;
                this.items = [];
                this.allItems = [];
                this.meta = { page: 1, per_page: 10, total: 0, last_page: 1 };
                this.error =
                    "No tienes permisos para ver los roles del sistema.";
                return;
            }
            this.blocked = false;
            try {
                this.loading = true;
                this.error = "";
                if (this._abortCtrl) {
                    try {
                        this._abortCtrl.abort();
                    } catch (_) { }
                }
                this._abortCtrl = new AbortController();
                const url = this.buildQuery(page);
                const r = await fetch(url, {
                    headers: authHeaders(),
                    signal: this._abortCtrl.signal,
                    credentials: "same-origin",
                });
                if (r.status === 403) {
                    this.items = [];
                    this.allItems = [];
                    this.meta = {
                        page: 1,
                        per_page: 10,
                        total: 0,
                        last_page: 1,
                    };
                    this.error =
                        "No tienes permisos para ver los roles del sistema.";
                    return;
                }
                if (!r.ok)
                    throw new Error(await r.text().catch(() => r.statusText));
                const data = await r.json();
                this.allItems = normalizeList(data);
                this.applyFiltersAndPagination();
            } catch (e) {
                if ((e && e.name) === "AbortError") return;
                this.error = e && e.message ? e.message : String(e || "Error");
            } finally {
                this.loading = false;
                this._abortCtrl = null;
            }
        },
        applyFiltersAndPagination() {
            let filtered = this.allItems;
            if (this.q) {
                const searchTerm = this.q.toLowerCase();
                filtered = filtered.filter(
                    (role) =>
                        (role.rol || "").toLowerCase().includes(searchTerm) ||
                        (role.descripcion_rol || "")
                            .toLowerCase()
                            .includes(searchTerm)
                );
            }

            // Aplicar ordenamiento
            if (this.sort) {
                filtered = filtered.sort((a, b) => {
                    let aVal = a[this.sort] || "";
                    let bVal = b[this.sort] || "";
                    if (typeof aVal === "string") aVal = aVal.toLowerCase();
                    if (typeof bVal === "string") bVal = bVal.toLowerCase();
                    if (aVal < bVal) return this.direction === "asc" ? -1 : 1;
                    if (aVal > bVal) return this.direction === "asc" ? 1 : -1;
                    return 0;
                });
            }

            this.meta.total = filtered.length;
            this.meta.last_page = Math.ceil(filtered.length / this.perPage);

            this.items = this.paginatedRoles(filtered);
        },

        paginatedRoles(items = null) {
            const source = items || this.allItems;
            const start = (this.currentPage - 1) * this.perPage;
            const end = start + this.perPage;
            return source.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.allItems.length / this.perPage);
        },
        nextPage() {
            if (this.currentPage < this.totalPages()) {
                this.currentPage++;
                this.applyFiltersAndPagination();
            }
        },
        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.applyFiltersAndPagination();
            }
        },
        goToPage(page) {
            if (page >= 1 && page <= this.totalPages()) {
                this.currentPage = page;
                this.applyFiltersAndPagination();
            }
        },
        setSearch(val) {
            this.q = val;
            this.currentPage = 1;
            this.debouncedFetch();
        },
        setSort(val) {
            this.sort = val || "rol";
            this.currentPage = 1;
            this.applyFiltersAndPagination();
        },
        setDirection(val) {
            this.direction = val === "desc" ? "desc" : "asc";
            this.currentPage = 1;
            this.applyFiltersAndPagination();
        },

        openCreate() {
            this.form = { rol: "", descripcion_rol: "" };
            this.isCreateOpen = true;
            this.error = "";
        },
        openEdit(item) {
            this.current = item;
            this.form = {
                rol: item?.rol || "",
                descripcion_rol: item?.descripcion_rol || "",
            };
            this.isEditOpen = true;
            this.error = "";
        },
        openDelete(item) {
            this.current = item;
            this.isDeleteOpen = true;
            this.error = "";
        },

        async create() {
            try {
                this.loading = true;
                this.error = "";
                const payload = {
                    rol: String(this.form.rol || "").trim(),
                    descripcion_rol:
                        (this.form.descripcion_rol || "").trim() || null,
                };
                const res = await apiSend(API.roles, "POST", payload);
                this.isCreateOpen = false;
                await this.fetchList(1);

                const access = window.Alpine?.store("access");
                if (access) {
                    const all = await fetch(`${API.roles}?all=1`, {
                        headers: authHeaders(),
                        credentials: "same-origin",
                    })
                        .then((r) => r.json())
                        .catch(() => null);
                    if (all) {
                        access.roles = filterHiddenRoles(normalizeList(all));
                    }
                }
                try {
                    window.showToast &&
                        window.showToast("Rol creado correctamente", "success");
                } catch (_) { }
                return res;
            } catch (e) {
                this.error = e && e.message ? e.message : String(e || "Error");
                try {
                    window.showToast &&
                        window.showToast(this.error || "Error al crear el rol", "error");
                } catch (_) { }
            } finally {
                this.loading = false;
            }
        },
        async update() {
            if (!this.current?.id) return;
            try {
                this.loading = true;
                this.error = "";
                const payload = {
                    rol: String(this.form.rol || "").trim(),
                    descripcion_rol:
                        (this.form.descripcion_rol || "").trim() || null,
                };
                const res = await apiSend(
                    `${API.roles}/${this.current.id}`,
                    "PUT",
                    payload
                );
                this.isEditOpen = false;
                this.current = null;
                await this.fetchList(1);
                const access = window.Alpine?.store("access");
                if (access) {
                    const all = await fetch(`${API.roles}?all=1`, {
                        headers: authHeaders(),
                        credentials: "same-origin",
                    })
                        .then((r) => r.json())
                        .catch(() => null);
                    if (all) {
                        access.roles = filterHiddenRoles(normalizeList(all));
                    }
                }
                try {
                    window.showToast &&
                        window.showToast("Rol actualizado", "success");
                } catch (_) { }
                return res;
            } catch (e) {
                this.error = e && e.message ? e.message : String(e || "Error");
                try {
                    window.showToast &&
                        window.showToast(this.error || "Error al actualizar el rol", "error");
                } catch (_) { }
            } finally {
                this.loading = false;
            }
        },
        async remove() {
            if (!this.current?.id) return;
            try {
                this.loading = true;
                this.error = "";
                const r = await fetch(`${API.roles}/${this.current.id}`, {
                    method: "DELETE",
                    headers: authHeaders(),
                    credentials: "same-origin",
                });
                if (!r.ok) {
                    throw new Error(
                        await parseApiError(r, "No se pudo eliminar el rol seleccionado")
                    );
                }
                this.isDeleteOpen = false;
                this.current = null;
                await this.fetchList(1);
                const access = window.Alpine?.store("access");
                if (access) {
                    const all = await fetch(`${API.roles}?all=1`, {
                        headers: authHeaders(),
                        credentials: "same-origin",
                    })
                        .then((r) => r.json())
                        .catch(() => null);
                    if (all) {
                        access.roles = filterHiddenRoles(normalizeList(all));
                    }
                }
                try {
                    window.showToast &&
                        window.showToast("Rol eliminado", "success");
                } catch (_) { }
            } catch (e) {
                this.error = e && e.message ? e.message : String(e || "Error");
                try {
                    window.showToast &&
                        window.showToast(this.error || "Error al eliminar el rol", "error");
                } catch (_) { }
            } finally {
                this.loading = false;
            }
        },

        _debounceTimer: null,
        debouncedFetch() {
            if (this._debounceTimer) clearTimeout(this._debounceTimer);
            this._debounceTimer = setTimeout(() => {
                this.currentPage = 1;
                this.applyFiltersAndPagination();
            }, 350);
        },
    };
}

document.addEventListener("alpine:init", () => {
    Alpine.store("roles", createRolesStore());
});
