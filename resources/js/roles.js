// Alpine store for CRUD de Roles
  const API = { roles: '/api/roles' };

  const authHeaders = () => ({ 'Content-Type': 'application/json' });

  const hasConfiguracionAcceso = () => {
    try {
      const main = document.querySelector('main');
      return (main?.dataset?.canConfiguracionAcceso || '') === '1';
    } catch (_) {
      return false;
    }
  };

  function normalizeList(payload){
    if(Array.isArray(payload)) return payload;
    if(Array.isArray(payload?.data)) return payload.data;
    return [];
  }
  function normalizeMeta(payload){
    if(payload?.meta) return payload.meta;
    const list = normalizeList(payload);
    return { page: 1, per_page: list.length, total: list.length, last_page: 1 };
  }

  async function apiSend(url, method, body){
    const r = await fetch(url, { method, headers: authHeaders(), credentials: 'same-origin', body: body?JSON.stringify(body):undefined });
    if(!r.ok) throw new Error(await r.text().catch(()=>r.statusText));
    return r.json();
  }

  function createRolesStore(){
    return {
      loading: false,
      error: '',
      items: [],
      meta: { page: 1, per_page: 10, total: 0, last_page: 1 },
      q: '',
      sort: 'rol',
      direction: 'asc',
      perPage: 10,
      _abortCtrl: null,
  blocked: false,

      isCreateOpen: false,
      isEditOpen: false,
      isDeleteOpen: false,
      form: { rol: '', descripcion_rol: '' },
      current: null,

      async init(){
        if(!hasConfiguracionAcceso()){
          this.blocked = true;
          this.items = [];
          this.meta = { page: 1, per_page: 10, total: 0, last_page: 1 };
          this.error = 'No tienes permisos para ver los roles del sistema.';
          return;
        }
        this.blocked = false;
        await this.fetchList(1);
      },
      buildQuery(page){
        const params = new URLSearchParams();
        params.set('per_page', String(this.perPage));
        params.set('page', String(page || this.meta.page || 1));
        if(this.q) params.set('q', this.q);
        if(this.sort) params.set('sort', this.sort);
        if(this.direction) params.set('direction', this.direction);
        return `${API.roles}?${params.toString()}`;
      },
      async fetchList(page=1){
        if(!hasConfiguracionAcceso()){
          this.blocked = true;
          this.items = [];
          this.meta = { page: 1, per_page: 10, total: 0, last_page: 1 };
          this.error = 'No tienes permisos para ver los roles del sistema.';
          return;
        }
        this.blocked = false;
        try{
          this.loading = true; this.error = '';
          if(this._abortCtrl){ try{ this._abortCtrl.abort(); }catch(_){} }
          this._abortCtrl = new AbortController();
          const url = this.buildQuery(page);
          const r = await fetch(url, { headers: authHeaders(), signal: this._abortCtrl.signal, credentials: 'same-origin' });
            if(r.status === 403){ this.items = []; this.meta = { page: 1, per_page: 10, total: 0, last_page: 1 }; this.error = 'No tienes permisos para ver los roles del sistema.'; return; }
          if(!r.ok) throw new Error(await r.text().catch(()=>r.statusText));
          const data = await r.json();
          this.items = normalizeList(data);
          this.meta = normalizeMeta(data);
        } catch(e){
          if((e && e.name)==='AbortError') return; // ignorar
          this.error = (e && e.message) ? e.message : String(e||'Error');
        } finally { this.loading = false; this._abortCtrl = null; }
      },
      setSearch(val){ this.q = val; this.debouncedFetch(); },
  setSort(val){ this.sort = val || 'rol'; if(!this.blocked) this.fetchList(1); },
  setDirection(val){ this.direction = (val==='desc'?'desc':'asc'); if(!this.blocked) this.fetchList(1); },

      openCreate(){ this.form = { rol: '', descripcion_rol: '' }; this.isCreateOpen = true; this.error=''; },
      openEdit(item){ this.current = item; this.form = { rol: item?.rol||'', descripcion_rol: item?.descripcion_rol||'' }; this.isEditOpen = true; this.error=''; },
      openDelete(item){ this.current = item; this.isDeleteOpen = true; this.error=''; },

      async create(){
        try{
          this.loading = true; this.error='';
          const payload = { rol: String(this.form.rol||'').trim(), descripcion_rol: (this.form.descripcion_rol||'').trim()||null };
          const res = await apiSend(API.roles, 'POST', payload);
          this.isCreateOpen = false; await this.fetchList(1);
          // refrescar store de access roles si existe
          const access = window.Alpine?.store('access');
          if(access){
            const all = await fetch(`${API.roles}?all=1`, { headers: authHeaders(), credentials: 'same-origin' }).then(r=>r.json()).catch(()=>null);
            if(all){ access.roles = normalizeList(all); }
          }
          try{ window.showToast && window.showToast('Rol creado correctamente', 'success'); }catch(_){}
          return res;
        }catch(e){ this.error = (e && e.message) ? e.message : String(e||'Error'); try{ window.showToast && window.showToast('Error al crear el rol', 'error'); }catch(_){} }
        finally{ this.loading = false; }
      },
      async update(){
        if(!this.current?.id) return;
        try{
          this.loading = true; this.error='';
          const payload = { rol: String(this.form.rol||'').trim(), descripcion_rol: (this.form.descripcion_rol||'').trim()||null };
          const res = await apiSend(`${API.roles}/${this.current.id}`, 'PUT', payload);
          this.isEditOpen = false; this.current = null; await this.fetchList(this.meta.page);
          const access = window.Alpine?.store('access');
          if(access){
            const all = await fetch(`${API.roles}?all=1`, { headers: authHeaders(), credentials: 'same-origin' }).then(r=>r.json()).catch(()=>null);
            if(all){ access.roles = normalizeList(all); }
          }
          try{ window.showToast && window.showToast('Rol actualizado', 'success'); }catch(_){}
          return res;
        }catch(e){ this.error = (e && e.message) ? e.message : String(e||'Error'); try{ window.showToast && window.showToast('Error al actualizar el rol', 'error'); }catch(_){} }
        finally{ this.loading = false; }
      },
      async remove(){
        if(!this.current?.id) return;
        try{
          this.loading = true; this.error='';
          const r = await fetch(`${API.roles}/${this.current.id}`, { method: 'DELETE', headers: authHeaders(), credentials: 'same-origin' });
          if(!r.ok) throw new Error(await r.text().catch(()=>r.statusText));
          this.isDeleteOpen = false; this.current = null; const page = (this.items.length===1 && this.meta.page>1)?this.meta.page-1:this.meta.page; await this.fetchList(page);
          const access = window.Alpine?.store('access');
          if(access){
            const all = await fetch(`${API.roles}?all=1`, { headers: authHeaders(), credentials: 'same-origin' }).then(r=>r.json()).catch(()=>null);
            if(all){ access.roles = normalizeList(all); }
          }
          try{ window.showToast && window.showToast('Rol eliminado', 'success'); }catch(_){}
        }catch(e){ this.error = (e && e.message) ? e.message : String(e||'Error'); try{ window.showToast && window.showToast('Error al eliminar el rol', 'error'); }catch(_){} }
        finally{ this.loading = false; }
      },

      // debounce búsqueda
      _debounceTimer: null,
      debouncedFetch(){
        if(this._debounceTimer) clearTimeout(this._debounceTimer);
        this._debounceTimer = setTimeout(()=> this.fetchList(1), 350);
      },
    };
  }

  document.addEventListener('alpine:init', ()=>{
    Alpine.store('roles', createRolesStore());
  });

