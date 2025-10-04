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
  rolesLoading: false,

    // Mapeos de roles por nombre para validaciones
    _roleKeyById: {}, // { id: 'cliente'|'administrador'|'tecnico'|... }
    _roleIdByKey: {}, // { 'cliente': id, 'administrador': id, 'tecnico': id }

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
        this._rebuildRoleMaps();
      } catch (e) { this.error = (e && e.message) ? e.message : 'Error cargando roles'; }
    },

    _normalizeRoleName(n) {
      try {
        return String(n || '')
          .normalize('NFD')
          .replace(/[\u0300-\u036f]/g, '')
          .toLowerCase()
          .trim();
      } catch (_) { return String(n || '').toLowerCase().trim(); }
    },
    _rebuildRoleMaps() {
      this._roleKeyById = {};
      this._roleIdByKey = {};
      for (const r of (this.roles || [])) {
        const key = this._normalizeRoleName(r.rol);
        this._roleKeyById[String(r.id)] = key;
        // primer id gana por si hay duplicados
        if (!this._roleIdByKey[key]) this._roleIdByKey[key] = String(r.id);
      }
    },
    _roleIs(key, id) {
      return (this._roleKeyById[String(id)] || '') === key;
    },
    _idForRole(key) { return this._roleIdByKey[key] || null; },
    // Helpers de validación Cliente vs Admin/Técnico
    _isCliente(id) { return this._roleIs('cliente', id); },
    _isAdmin(id) { return this._roleIs('administrador', id); },
    _isTecnico(id) { return this._roleIs('tecnico', id); },
    _hasClienteSelected() { return this.rolesSelected.map(String).some(id => this._isCliente(id)) || this._isCliente(this.rol_principal); },
    _hasAdminOTecnicoSelected() { return this.rolesSelected.map(String).some(id => this._isAdmin(id) || this._isTecnico(id)) || this._isAdmin(this.rol_principal) || this._isTecnico(this.rol_principal); },
    _notify(msg, type='warning') { try { window.showToast ? window.showToast(msg, type) : alert(msg); } catch (_) { alert(msg); } },

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
        const hasCache = !!this._rolesCache[user.id];
        if (hasCache) {
          // aplicar cache de inmediato (evita parpadeo y trae técnico en re-apertura)
          this.applyCachedRoles(user.id);
          this.rolesLoading = false;
        } else {
          // mostrar cargando para evitar que se vea el chequeo activándose "al instante"
          this.rolesLoading = true;
          this.fetchUserRoles(user.id, { preferCache: true, ttlMs: Number.POSITIVE_INFINITY })
            .finally(() => { this.rolesLoading = false; });
        }
      }
    },

    applyCachedRoles(id) {
      const cached = this._rolesCache[id];
      if (!cached) return false;
      this.rolesSelected = (cached.roles || []).map(String);
      if (cached.rol_principal) this.rol_principal = String(cached.rol_principal);
      this.setPrincipal(this.rol_principal);
      return true;
    },

    async fetchUserRoles(id, { preferCache = false, ttlMs = 30000 } = {}) {
      const now = Date.now();
      const cached = this._rolesCache[id];
      if (preferCache && cached && (ttlMs === Number.POSITIVE_INFINITY || (now - (cached.ts || 0) < ttlMs))) {
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
      // Regla: Cliente no se puede combinar con Admin/Técnico
      const adding = !this.rolesSelected.map(String).includes(sid);
      if (adding) {
        if (this._isCliente(sid) && this._hasAdminOTecnicoSelected()) {
          this._notify('Un usuario con rol Cliente no puede tener Administrador ni Técnico.');
          return; // no agregar Cliente
        }
        if ((this._isAdmin(sid) || this._isTecnico(sid)) && this._hasClienteSelected()) {
          this._notify('No se puede combinar Cliente con Administrador/Técnico.');
          return; // no agregar Admin/Técnico
        }
      }
      const idx = this.rolesSelected.map(String).findIndex(x => x === sid);
      if (idx > -1) this.rolesSelected.splice(idx, 1);
      else this.rolesSelected.push(sid);
      this.rolesSelected = this.rolesSelected.map(String);
      // enforcement post-cambio
      this._enforceClienteRule();
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
      // Aplicar regla Cliente vs Admin/Tecnico
      this._enforceClienteRule();
    },

    _enforceClienteRule() {
      // Si el principal es Cliente, remover Admin/Técnico adicionales
      const clienteId = this._idForRole('cliente');
      const adminId = this._idForRole('administrador');
      const tecnicoId = this._idForRole('tecnico');
      if (!clienteId && !adminId && !tecnicoId) return;

      const rs = this.rolesSelected.map(String);
      let changed = false;
      if (this._isCliente(this.rol_principal)) {
        // quitar admin/tecnico
        const forbid = [adminId, tecnicoId].filter(Boolean).map(String);
        this.rolesSelected = rs.filter(x => !forbid.includes(x) || x === String(this.rol_principal));
        changed = rs.length !== this.rolesSelected.length;
        if (changed) this._notify('Con rol principal Cliente no se permiten roles Administrador ni Técnico.');
      } else {
        // si hay admin/tecnico como principal o adicional, quitar Cliente de adicionales
        const hasAdminOTec = this._hasAdminOTecnicoSelected();
        if (hasAdminOTec && clienteId) {
          this.rolesSelected = rs.filter(x => String(x) !== String(clienteId));
          changed = rs.length !== this.rolesSelected.length;
          if (changed) this._notify('Cliente no puede combinarse con Administrador/Técnico. Rol Cliente removido.');
        }
      }
      // mantener principal siempre incluido
      if (this.rol_principal && !this.rolesSelected.includes(String(this.rol_principal))) {
        this.rolesSelected.push(String(this.rol_principal));
      }
    },

    isRoleDisabled(id) {
      const sid = String(id);
      if (this.rol_principal === sid) return true;
      // deshabilitar combinaciones inválidas para mejorar UX
      if (this._isCliente(this.rol_principal)) {
        return this._isAdmin(sid) || this._isTecnico(sid);
      }
      if (this._isAdmin(this.rol_principal) || this._isTecnico(this.rol_principal)) {
        return this._isCliente(sid);
      }
      // si cliente está seleccionado como adicional, bloquear admin/tecnico; y viceversa
      const clienteSel = this.rolesSelected.map(String).some(x => this._isCliente(x));
      const adminOTecSel = this.rolesSelected.map(String).some(x => this._isAdmin(x) || this._isTecnico(x));
      if (clienteSel) return this._isAdmin(sid) || this._isTecnico(sid);
      if (adminOTecSel) return this._isCliente(sid);
      return false;
    },

    async saveAssignMulti() {
      if (!this.current?.id) return;
      const id = this.current.id;
      // x-model en checkboxes usa strings; convertimos a números limpios
      const roles = (this.rolesSelected || []).map(v => Number(String(v).trim())).filter(v => Number.isFinite(v) && v > 0);
      const rol_principal = this.rol_principal ? Number(this.rol_principal) : null;
      // Validación previa: Cliente no se combina con Admin/Técnico
      const clienteId = this._idForRole('cliente');
      const adminId = this._idForRole('administrador');
      const tecnicoId = this._idForRole('tecnico');
      const hasCliente = roles.map(String).includes(String(clienteId)) || String(rol_principal) === String(clienteId);
      const hasAdminOTec = roles.map(String).some(x => [adminId, tecnicoId].filter(Boolean).map(String).includes(String(x))) || [adminId, tecnicoId].filter(Boolean).map(String).includes(String(rol_principal));
      if (hasCliente && hasAdminOTec) {
        this._notify('No se puede guardar: Cliente no puede combinarse con Administrador/Técnico.', 'error');
        return;
      }
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
        // actualizar cache local para evitar refetchs posteriores
        this._rolesCache[id] = { roles: this.rolesSelected.map(String), rol_principal: String(this.rol_principal || ''), ts: Date.now() };
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
