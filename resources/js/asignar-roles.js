// Alpine store to assign roles to users
  const API = { users: '/api/usuarios', roles: '/api/roles' };

  const authHeaders = () => {
    const t = localStorage.getItem('authToken');
    const h = { 'Accept': 'application/json' };
    if (t) h['Authorization'] = `Bearer ${t}`;
    return h;
  };

  function normalizeList(payload){
    if (Array.isArray(payload)) return payload;
    if (Array.isArray(payload?.data)) return payload.data;
    return [];
  }

  function normalizeMeta(payload){
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

      isAssignOpen: false,
      current: null,
      form: { id_rol_fk: '' },

      async init(){
        await Promise.all([this.fetchRoles(), this.fetchUsers(1)]);
      },

      rolNombre(id){
        const r = this.roles.find(x => String(x.id) === String(id));
        return r ? r.rol : '';
      },

      async fetchRoles(){
        try{
          const r = await fetch(`${API.roles}?all=1`, { headers: authHeaders(), credentials: 'same-origin' });
          if (!r.ok) throw new Error(await r.text().catch(()=>r.statusText));
          const data = await r.json();
          this.roles = normalizeList(data);
        } catch(e){ this.error = (e && e.message) ? e.message : 'Error cargando roles'; }
      },

      buildQuery(page){
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

      async fetchUsers(page=1){
        try{
          this.loading = true; this.error='';
          if (this._abortCtrl) { try{ this._abortCtrl.abort(); }catch(_){} }
          this._abortCtrl = new AbortController();
          const url = this.buildQuery(page);
          const r = await fetch(url, { headers: authHeaders(), signal: this._abortCtrl.signal, credentials: 'same-origin' });
          if (!r.ok) throw new Error(await r.text().catch(()=>r.statusText));
          const data = await r.json();
          let list = normalizeList(data);
          // filtro por rol en el cliente (no soportado en API index())
          if (this.filterRol) list = list.filter(u => String(u.id_rol_fk||'') === String(this.filterRol));
          this.items = list;
          this.meta = normalizeMeta(data);
        } catch(e){ if ((e && e.name) !== 'AbortError') this.error = (e && e.message) ? e.message : 'Error'; }
        finally { this.loading = false; this._abortCtrl = null; }
      },

      setSearch(v){ this.q = v; this.debouncedFetch(); },
      setSort(v){ this.sort = v || 'usuario'; this.fetchUsers(1); },
      setDirection(v){ this.direction = (v==='desc'?'desc':'asc'); this.fetchUsers(1); },
      setFilterRol(v){ this.filterRol = v || ''; this.fetchUsers(1); },

      openAssign(user){
        this.current = user;
        this.form.id_rol_fk = String(user?.id_rol_fk || '');
        this.isAssignOpen = true;
        this.error = '';
      },

      async saveAssign(){
        if (!this.current?.id) return; // solo edición de rol existente
        const id = this.current.id;
        try{
          this.loading = true; this.error='';
          const r = await fetch(`${API.users}/${id}/rol`, {
            method: 'PUT',
            headers: { ...authHeaders(), 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ id_rol_fk: this.form.id_rol_fk ? Number(this.form.id_rol_fk) : null }),
          });
          if (!r.ok) throw new Error(await r.text().catch(()=>r.statusText));
          // reflejar en memoria
          const idx = this.items.findIndex(u => u.id === id);
          if (idx > -1) this.items[idx].id_rol_fk = this.form.id_rol_fk ? Number(this.form.id_rol_fk) : null;
          this.isAssignOpen = false;
          try{ window.showToast && window.showToast('Rol asignado al usuario', 'success'); }catch(_){}
        } catch(e){ this.error = (e && e.message) ? e.message : 'Error asignando rol'; }
        finally { this.loading = false; }
      },

      // debounce
      _debounceTimer: null,
      debouncedFetch(){ if (this._debounceTimer) clearTimeout(this._debounceTimer); this._debounceTimer = setTimeout(()=> this.fetchUsers(1), 350); },
    });
  });
