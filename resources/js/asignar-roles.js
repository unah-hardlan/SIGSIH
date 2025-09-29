// Alpine store to assign roles to users
const API = { users: '/api/usuarios', roles: '/api/roles' };

const authHeaders = () => ({ 'Accept': 'application/json' });

function normalizeList(payload) {
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.data)) return payload.data;
  return [];
}

function normalizeMeta(payload) {
  const data = payload?.meta;
  if (data) return { page: data.page, per_page: data.per_page, total: data.total, last_page: data.last_page };
  const list = normalizeList(payload);
  return { page: 1, per_page: list.length || 10, total: list.length, last_page: 1 };
}

document.addEventListener('alpine:init', () => {
  Alpine.store('assignRoles', {
    loading: false,
    error: '',
    items: [], // usuarios
    roles: [], // catálogo de roles
    meta: { page: 1, per_page: 10, total: 0, last_page: 1 },
    q: '',
    sort: 'usuario',
    direction: 'asc',
    filterRol: '',
    perPage: 10,
    _abortCtrl: null,
  // cache simple por usuario para roles asignados
  _rolesCache: {}, // { [userId]: { roles: string[], rol_principal: string, ts: number } }
  _rolesInflight: {}, // { [userId]: Promise }

    isAssignOpen: false,
    current: null,
    form: { id_rol_fk: '' },
    // Multi-rol
    rolesSelected: [],
    rol_principal: '',

    async init() {
      await Promise.all([this.fetchRoles(), this.fetchUsers(1)]);
    },

    rolNombre(id) {
      const r = this.roles.find(x => String(x.id) === String(id));
      return r ? r.rol : '';
    },

    async fetchRoles() {
      try {
        const r = await fetch(`${API.roles}?all=1`, { headers: authHeaders(), credentials: 'same-origin' });
        if (!r.ok) throw new Error(await r.text().catch(() => r.statusText));
        const data = await r.json();
        this.roles = normalizeList(data);
      } catch (e) { this.error = (e && e.message) ? e.message : 'Error cargando roles'; }
    },

    buildQuery(page) {
      const p = new URLSearchParams();
      p.set('per_page', String(this.perPage));
      p.set('page', String(page || this.meta.page || 1));
      if (this.q) p.set('q', this.q);
      if (this.sort) p.set('sort', this.sort);
      if (this.direction) p.set('direction', this.direction);
      // mostrar todos los estados por defecto para ver inactivos también
      p.set('all', '1');
      return `${API.users}?${p.toString()}`;
    },

    async fetchUsers(page = 1) {
      try {
        this.loading = true; this.error = '';
        if (this._abortCtrl) { try { this._abortCtrl.abort(); } catch (_) { } }
        this._abortCtrl = new AbortController();
        const url = this.buildQuery(page);
        const r = await fetch(url, { headers: authHeaders(), signal: this._abortCtrl.signal, credentials: 'same-origin' });
        if (!r.ok) throw new Error(await r.text().catch(() => r.statusText));
        const data = await r.json();
        let list = normalizeList(data);
        // filtro por rol en el cliente (no soportado en API index())
        if (this.filterRol) list = list.filter(u => String(u.id_rol_fk || '') === String(this.filterRol));
        this.items = list;
        this.meta = normalizeMeta(data);
      } catch (e) { if ((e && e.name) !== 'AbortError') this.error = (e && e.message) ? e.message : 'Error'; }
      finally { this.loading = false; this._abortCtrl = null; }
    },

    setSearch(v) { this.q = v; this.debouncedFetch(); },
    setSort(v) { this.sort = v || 'usuario'; this.fetchUsers(1); },
    setDirection(v) { this.direction = (v === 'desc' ? 'desc' : 'asc'); this.fetchUsers(1); },
    setFilterRol(v) { this.filterRol = v || ''; this.fetchUsers(1); },

    openAssign(user) {
      this.current = user;
      this.form.id_rol_fk = String(user?.id_rol_fk || '');
      // por defecto, usar el rol principal como seleccionado
      this.rolesSelected = [];
      if (user?.id_rol_fk) this.rolesSelected = [String(user.id_rol_fk)];
      this.rol_principal = String(user?.id_rol_fk || '');
      // normalizar y asegurar que el principal quede seleccionado
      this.setPrincipal(this.rol_principal);
      this.isAssignOpen = true;
      this.error = '';
      // Pre-cargar desde cache o backend los roles asignados actualmente
      if (user?.id) {
        this.fetchUserRoles(user.id, { preferCache: true });
      }
    },

    async fetchUserRoles(id, { preferCache = false, ttlMs = 30000 } = {}) {
      const now = Date.now();
      const cached = this._rolesCache[id];
      if (preferCache && cached && (now - (cached.ts || 0) < ttlMs)) {
        this.rolesSelected = (cached.roles || []).map(String);
        this.rol_principal = cached.rol_principal ? String(cached.rol_principal) : this.rol_principal;
        this.setPrincipal(this.rol_principal);
        return;
      }
      if (this._rolesInflight[id]) {
        try { await this._rolesInflight[id]; } catch (_) { }
        const c2 = this._rolesCache[id];
        if (c2) {
          this.rolesSelected = (c2.roles || []).map(String);
          this.rol_principal = c2.rol_principal ? String(c2.rol_principal) : this.rol_principal;
          this.setPrincipal(this.rol_principal);
        }
        return;
      }
      this._rolesInflight[id] = (async () => {
        try {
          const r = await fetch(`${API.users}/${id}/roles`, { headers: authHeaders(), credentials: 'same-origin' });
          if (!r.ok) return;
          const data = await r.json();
          const roles = Array.isArray(data?.roles) ? data.roles.map(v => String(v)) : [];
          const principal = data?.rol_principal ? String(data.rol_principal) : '';
          this._rolesCache[id] = { roles, rol_principal: principal, ts: Date.now() };
          this.rolesSelected = roles;
          if (principal) this.rol_principal = principal;
          this.setPrincipal(this.rol_principal);
        } catch (_) { }
        finally { delete this._rolesInflight[id]; }
      })();
      await this._rolesInflight[id];
    },

    async saveAssign() {
      if (!this.current?.id) return; // solo edición de rol existente
      const id = this.current.id;
      try {
        this.loading = true; this.error = '';
        const r = await fetch(`${API.users}/${id}/rol`, {
          method: 'PUT',
          headers: { ...authHeaders(), 'Content-Type': 'application/json' },
          credentials: 'same-origin',
          body: JSON.stringify({ id_rol_fk: this.form.id_rol_fk ? Number(this.form.id_rol_fk) : null }),
        });
        if (!r.ok) throw new Error(await r.text().catch(() => r.statusText));
        // reflejar en memoria
        const idx = this.items.findIndex(u => u.id === id);
        if (idx > -1) this.items[idx].id_rol_fk = this.form.id_rol_fk ? Number(this.form.id_rol_fk) : null;
        this.isAssignOpen = false;
        try { window.showToast && window.showToast('Rol asignado al usuario', 'success'); } catch (_) { }
      } catch (e) { this.error = (e && e.message) ? e.message : 'Error asignando rol'; }
      finally { this.loading = false; }
    },

    // Helpers multi-rol
    isRoleSelected(id) { return this.rolesSelected.some(x => String(x) === String(id)); },
    toggleRole(id) {
      const sid = String(id);
      // no permitir quitar el principal
      if (this.rol_principal === sid) return;
      const idx = this.rolesSelected.map(String).findIndex(x => x === sid);
      if (idx > -1) this.rolesSelected.splice(idx, 1);
      else this.rolesSelected.push(sid);
      this.rolesSelected = this.rolesSelected.map(String);
    },
    setPrincipal(id) {
      this.rol_principal = String(id || '');
      // Asegurar que el principal quede siempre seleccionado
      if (id) {
        const sid = String(id);
        if (!this.rolesSelected.map(String).includes(sid)) this.rolesSelected.push(String(id));
      }
      // Normalizar a strings para x-model, convertimos a number en el guardado
      this.rolesSelected = this.rolesSelected.map(x => String(x));
    },

    async saveAssignMulti() {
      if (!this.current?.id) return;
      const id = this.current.id;
      // x-model en checkboxes usa strings; convertimos a números limpios
      const roles = (this.rolesSelected || []).map(v => Number(String(v).trim())).filter(v => Number.isFinite(v) && v > 0);
      const rol_principal = this.rol_principal ? Number(this.rol_principal) : null;
      try {
        this.loading = true; this.error = '';
        const r = await fetch(`${API.users}/${id}/roles`, {
          method: 'PUT',
          headers: { ...authHeaders(), 'Content-Type': 'application/json' },
          credentials: 'same-origin',
          body: JSON.stringify({ roles, rol_principal }),
        });
        if (!r.ok) throw new Error(await r.text().catch(() => r.statusText));
        const idx = this.items.findIndex(u => u.id === id);
        if (idx > -1) this.items[idx].id_rol_fk = rol_principal ?? (roles[0] || null);
        this.isAssignOpen = false;
        try { window.showToast && window.showToast('Roles actualizados para el usuario', 'success'); } catch (_) { }
      } catch (e) { this.error = (e && e.message) ? e.message : 'Error asignando roles'; }
      finally { this.loading = false; }
    },

    // debounce
    _debounceTimer: null,
    debouncedFetch() { if (this._debounceTimer) clearTimeout(this._debounceTimer); this._debounceTimer = setTimeout(() => this.fetchUsers(1), 350); },
  });
});
