// Alpine store for CRUD de Objetos del sistema
(function(){
  const API = {
    objetos: '/api/objetos',
  tipos: '/api/tipos-objeto',
  };

  const authHeaders = () => {
    const t = localStorage.getItem('authToken');
    return t ? { 'Authorization': `Bearer ${t}`, 'Content-Type': 'application/json' } : { 'Content-Type': 'application/json' };
  };

  async function apiGet(url){
    const r = await fetch(url, { headers: authHeaders(), credentials: 'same-origin' });
    if(!r.ok) throw new Error(await r.text().catch(()=>r.statusText));
    return r.json();
  }
  async function apiSend(url, method, body){
    const r = await fetch(url, { method, headers: authHeaders(), credentials: 'same-origin', body: JSON.stringify(body) });
    if(!r.ok) throw new Error(await r.text().catch(()=>r.statusText));
    return r.json();
  }

  function isAbort(err){
    const name = err && err.name;
    const msg = (err && err.message) || '';
    return name === 'AbortError' || /aborted/i.test(msg) || /The user aborted a request/i.test(msg) || /signal is aborted/i.test(msg);
  }

  function normalizeList(payload){
    if(Array.isArray(payload)) return payload;
    if(Array.isArray(payload?.data)) return payload.data;
    return [];
  }
  function normalizeMeta(payload){
    if(payload?.meta) return payload.meta;
    return { page: 1, per_page: normalizeList(payload).length, total: normalizeList(payload).length, last_page: 1 };
  }

  function createObjetosStore(){
    return {
      loading: false,
      error: '',
      // listado y paginación
      items: [],
      meta: { page: 1, per_page: 10, total: 0, last_page: 1 },
      q: '',
      tipoId: '', // id_tipo_objetos_fk
      perPage: 10,
      _abortCtrl: null,

  // catalogos
  tipos: [], // [{id,nombre}]

      // formularios / modales
      isCreateOpen: false,
      isEditOpen: false,
      isDeleteOpen: false,
      form: { nombre_objeto: '', descripcion_objeto: '', id_tipo_objetos_fk: '' },
      current: null, // objeto seleccionado para editar/eliminar

      async init(){
        await Promise.allSettled([
          this.fetchTipos(),
          this.fetchList(1),
        ]);
      },

      async fetchTipos(){
        try{
          const res = await apiGet(`${API.tipos}?all=1`);
          this.tipos = normalizeList(res);
        }catch(e){ /* no bloquear UI principal */ }
      },

      buildQuery(page){
        const params = new URLSearchParams();
        params.set('per_page', String(this.perPage));
        params.set('page', String(page || this.meta.page || 1));
        if(this.q) params.set('q', this.q);
        if(this.tipoId) params.set('id_tipo_objetos_fk', this.tipoId);
        params.set('sort', 'id');
        params.set('direction', 'asc');
        return `${API.objetos}?${params.toString()}`;
      },

      async fetchList(page=1){
        try{
          this.loading = true; this.error = '';
          if(this._abortCtrl){ try{ this._abortCtrl.abort(); } catch(_){} }
          this._abortCtrl = new AbortController();
          const url = this.buildQuery(page);
          const r = await fetch(url, { headers: authHeaders(), signal: this._abortCtrl.signal, credentials: 'same-origin' });
          if(!r.ok) throw new Error(await r.text().catch(()=>r.statusText));
          const data = await r.json();
          this.items = normalizeList(data);
          this.meta = normalizeMeta(data);
  } catch(e){ if(!isAbort(e)) this.error = parseErr(e); }
        finally { this.loading = false; this._abortCtrl = null; }
      },

      // helpers UI
      setSearch(val){
  this.q = val;
  this.error = '';
        this.debouncedFetch();
      },
      setTipo(val){
  this.tipoId = val;
  this.error = '';
        this.fetchList(1);
      },
      nextPage(){ if(this.meta.page < this.meta.last_page) this.fetchList(this.meta.page + 1); },
      prevPage(){ if(this.meta.page > 1) this.fetchList(this.meta.page - 1); },

      // opciones y helpers de tipos
      tipoOptions(){
        if(this.tipos && this.tipos.length) return this.tipos;
        // intentar derivar de los items (si vienen con { tipo: {id,nombre} })
        const map = new Map();
        for(const it of this.items){
          const t = it && it.tipo;
          if(t && t.id != null){
            const id = t.id;
            if(!map.has(id)) map.set(id, { id, nombre: t.nombre || `Tipo #${id}` });
          }
        }
        if(map.size) return Array.from(map.values()).sort((a,b)=>Number(a.id)-Number(b.id));
        // último recurso: solo ids
        const set = new Set();
        for(const it of this.items){ if(it.id_tipo_objetos_fk != null) set.add(it.id_tipo_objetos_fk); }
        return Array.from(set).sort((a,b)=>Number(a)-Number(b)).map(id=>({id, nombre:`Tipo #${id}`}));
      },
      tipoNombre(id){
        // preferir catálogo si está cargado
        const t = (this.tipos||[]).find(x=>String(x.id)===String(id));
        if(t) return t.nombre;
        // intentar hallar en los items actuales
        const fromItem = (this.items||[]).map(it=>it.tipo).find(tt=>tt && String(tt.id)===String(id));
        if(fromItem) return fromItem.nombre || `Tipo #${id}`;
        return id!=null?`Tipo #${id}`:'';
      },

      openCreate(){
  this.form = { nombre_objeto: '', descripcion_objeto: '', id_tipo_objetos_fk: '' };
  this.error = '';
        this.isCreateOpen = true;
      },
      openEdit(item){
        this.current = item;
        this.form = {
          nombre_objeto: item?.nombre_objeto || '',
          descripcion_objeto: item?.descripcion_objeto || '',
          id_tipo_objetos_fk: item?.id_tipo_objetos_fk || '',
        };
  this.error = '';
        this.isEditOpen = true;
      },
      openDelete(item){ this.current = item; this.isDeleteOpen = true; },

      async create(){
        try{
          this.loading = true; this.error = '';
          const payload = {
            nombre_objeto: String(this.form.nombre_objeto || '').trim(),
            descripcion_objeto: (this.form.descripcion_objeto || '').trim() || null,
            id_tipo_objetos_fk: Number(this.form.id_tipo_objetos_fk),
          };
          const res = await apiSend(API.objetos, 'POST', payload);
          this.isCreateOpen = false;
          await this.fetchList(1);
          this.syncAccessStore();
          try{ window.showToast?.('Objeto creado correctamente', 'success'); }catch(_){ }
          return res;
        } catch(e){ this.error = parseErr(e); try{ window.showToast?.(`Error al crear objeto: ${this.error}`,'error'); }catch(_){} }
        finally { this.loading = false; }
      },

      async update(){
        if(!this.current?.id) return;
        try{
          this.loading = true; this.error = '';
          const payload = {
            nombre_objeto: String(this.form.nombre_objeto || '').trim(),
            descripcion_objeto: (this.form.descripcion_objeto || '').trim() || null,
            id_tipo_objetos_fk: Number(this.form.id_tipo_objetos_fk),
          };
          const res = await apiSend(`${API.objetos}/${this.current.id}`, 'PUT', payload);
          this.isEditOpen = false; this.current = null;
          await this.fetchList(this.meta.page);
          this.syncAccessStore();
          try{ window.showToast?.('Objeto actualizado correctamente', 'success'); }catch(_){ }
          return res;
        } catch(e){ this.error = parseErr(e); try{ window.showToast?.(`Error al actualizar objeto: ${this.error}`,'error'); }catch(_){} }
        finally { this.loading = false; }
      },

      async remove(){
        if(!this.current?.id) return;
        try{
          this.loading = true; this.error = '';
          const r = await fetch(`${API.objetos}/${this.current.id}`, { method: 'DELETE', headers: authHeaders(), credentials: 'same-origin' });
          if(!r.ok) throw new Error(await r.text().catch(()=>r.statusText));
          this.isDeleteOpen = false; this.current = null;
          // si se borró el último de la página, retroceder de página si corresponde
          const page = (this.items.length === 1 && this.meta.page > 1) ? this.meta.page - 1 : this.meta.page;
          await this.fetchList(page);
          this.syncAccessStore();
          try{ window.showToast?.('Objeto eliminado correctamente', 'success'); }catch(_){ }
        } catch(e){ this.error = parseErr(e); try{ window.showToast?.(`Error al eliminar objeto: ${this.error}`,'error'); }catch(_){} }
        finally { this.loading = false; }
      },

      // Mantener sincronizado el store de access (matriz de permisos)
      async syncAccessStore(){
        try{
          const access = window.Alpine?.store('access');
          if(access && typeof access === 'object'){
            const all = await apiGet(`${API.objetos}?all=1`);
            access.objetos = normalizeList(all);
            // Recalcular permisos si hay rol seleccionado
            if(access.selectedRoleId){ await access.loadPermisosForRole(access.selectedRoleId); }
          }
        } catch(_){}
      },

      // debounce para búsqueda
      _debounceTimer: null,
      debouncedFetch(){
        if(this._debounceTimer) clearTimeout(this._debounceTimer);
        this._debounceTimer = setTimeout(()=> this.fetchList(1), 350);
      },
    };
  }

  function parseErr(e){
    const msg = (e && e.message) ? e.message : String(e||'Error');
    return msg.length > 300 ? msg.slice(0,300)+'…' : msg;
  }

  document.addEventListener('alpine:init', ()=>{
    Alpine.store('objetos', createObjetosStore());
  });
})();
