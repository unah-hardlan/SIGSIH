// Access management store for roles, objects and permissions (Permiso booleans)
// MARCADOR-DISENO-COMPACTO-PERMISOS
// Referencia: Este archivo fue parte del commit de "diseño compacto" que reorganiza la matriz
// de permisos (agrupación por módulo, encabezado de módulo con toggle y columnas condensadas).
// Si se pierde el commit, usar este bloque como punto de anclaje para reconstruir.
(function(){
  // Sidebar-driven module ordering and submodule labels for grouping/ordering
  const SIDEBAR_ORDER = [
    { id:'seguridad', title:'Seguridad', items:[ 'Usuarios','Parámetros','Parametros','Configuración de accesos','Configuracion de accesos' ] },
    { id:'clientes', title:'Clientes', items:[ 'Empresas','Cotizaciones','Solicitudes','Órdenes de Servicios','Ordenes de Servicios' ] },
  { id:'proyectos', title:'Proyectos', items:[ 'Proyectos','Gestión de proyectos','Gestion de proyectos','Vista de proyectos' ] },
    { id:'tickets', title:'Tickets', items:[ 'Gestión de tickets','Gestion de tickets','Tickets' ] },
  { id:'calendario', title:'Calendario', items:[ 'Agencias','Calendario','Gestión de Calendario','Gestion de Calendario' ] },
    { id:'facturacion', title:'Facturación', items:[ 'Facturas','CAI' ] },
    { id:'reportes', title:'Reportes', items:[ 'Gestión de Reportes','Gestion de Reportes','Reportes' ] },
    { id:'inventario', title:'Inventario', items:[ 'Productos','Kardex' ] },
  { id:'administracion', title:'Administración', items:[ 'Gestión de personas','Gestion de personas','Mi perfil','Perfil','Profile','Bitácora','Bitacora','Gestión de base de datos','Gestion de base de datos' ] },
    { id:'mantenimiento', title:'Mantenimiento', items:[ 'Mantenimiento del Sistema','Mantenimiento del sistema' ] },
    { id:'catalogo', title:'Catalogo', items:[
      'Acciones Realizadas','Administración de Facturas','Categorias de Ingresos y Gastos','Categorías de Ingresos y Gastos','Estados CAI','Estados de Proyecto','Estados de Solicitud','Estados de Tickets','Estados del Calendario','Género','Genero','Perfiles','Servicio Factura','Servicios Realizados','Tipo de Movimiento','Tipo de Objeto','Tipo de Personas','Tipo de Producto','Tipo de Visita','Ubicaciones'
    ] },
  ];

  const norm = (s) => (s||'').toString().normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().trim();
  // Tipo/grupo de fallback que no deben mostrarse en la matriz
  const HIDDEN_FALLBACK_GROUPS = new Set(['configuracion','modulo']);
  const API = {
    roles: '/api/roles',
    objetos: '/api/objetos',
  tipos: '/api/tipos-objeto',
    permisos: '/api/permisos',
    upsertPerm: (rolId, objId) => `/api/permisos/roles/${rolId}/objetos/${objId}`,
  };

  const authHeaders = () => {
    const t = localStorage.getItem('authToken');
    const h = { 'Content-Type': 'application/json' };
    if (t) h['Authorization'] = `Bearer ${t}`;
    return h;
  };

  async function apiGet(url, opts = {}){
    const res = await fetch(url, { headers: authHeaders(), credentials: 'same-origin', signal: opts.signal });
    if(!res.ok) throw new Error(await res.text().catch(()=>res.statusText));
    return res.json();
  }
  async function apiGetList(url, opts = {}){
    const data = await apiGet(url, opts);
    if (Array.isArray(data)) return data;
    if (Array.isArray(data?.data)) return data.data;
    return [];
  }
  async function apiSend(url, method, body, opts = {}){
    const res = await fetch(url, { method, headers: authHeaders(), credentials: 'same-origin', body: JSON.stringify(body), signal: opts.signal });
    if(!res.ok) throw new Error(await res.text().catch(()=>res.statusText));
    return res.json();
  }

  function normalizeCollection(payload){
    if(Array.isArray(payload)) return payload;
    if(Array.isArray(payload?.data)) return payload.data;
    return [];
  }

  function createAccessStore(){
    return {
      loading: false,
      error: '',
      roles: [],
      objetos: [],
  tipos: [],
      selectedRoleId: null,
  // Control anti-ráfaga
  _roleLoadPromise: null,
  _roleLoadingId: null,
  _selectRoleDebounce: null,
  _fetchPermRetries: 3,
      pending: {},
      commitTimers: {},
      permsByObj: {},
      permColumns: [
        { field: 'permiso_consultar', label: 'Ver' },
        { field: 'permiso_insercion', label: 'Crear' },
        { field: 'permiso_actualizar', label: 'Editar' },
        { field: 'permiso_eliminacion', label: 'Eliminar' },
      ],

      async init(){
        try{
          this.loading = true; this.error = '';
          const [rolesRes, objetosRes, tiposRes] = await Promise.all([
            apiGet(`${API.roles}?all=1`),
            apiGet(`${API.objetos}?all=1`),
            apiGet(`${API.tipos}?all=1`),
          ]);
          this.roles = normalizeCollection(rolesRes);
          this.objetos = normalizeCollection(objetosRes);
          this.tipos = normalizeCollection(tiposRes);
          if(this.roles.length && !this.selectedRoleId){
            await this.selectRole(this.roles[0].id);
          }
        } catch(e){ this.error = parseErr(e); }
        finally { this.loading = false; }
      },

      async selectRole(roleId){
        this.selectedRoleId = roleId;
        if(this._selectRoleDebounce) clearTimeout(this._selectRoleDebounce);
        this._selectRoleDebounce = setTimeout(()=>{ this.ensureRolePerms(roleId); },120);
      },

      async ensureRolePerms(roleId){
        if(!roleId) return;
        try{ await this.loadPermisosForRole(roleId); }catch(_){ }
      },

      // Grouping helpers for module/submodule UX
      objetosByTipo(tipoId){
        const tid = tipoId ?? 0;
        return this.objetos.filter(o => (o.id_tipo_objetos_fk ?? 0) === tid);
      },
      grupos(){
        // Build groups following sidebar order first
        const assigned = new Set();
        const groups = [];
        const objetosByName = this.objetos.slice();
        const nameMap = new Map();
        for(const o of objetosByName){ nameMap.set(o.id, norm(o.nombre_objeto)); }

        for(const mod of SIDEBAR_ORDER){
          const labelOrder = mod.items.map(s => norm(s));
          const bucket = [];
          // Intentar localizar el Objeto del MÓDULO (p.ej. "Seguridad", "Clientes", etc.)
          // Preferir SIEMPRE coincidencia exacta con el título del módulo, no con los submódulos
          const moduleTitle = norm(mod.title);
          let moduleObjId = null;
          for (const [id, n] of nameMap.entries()){
            if (n === moduleTitle) { moduleObjId = id; break; }
          }
          // First pass: push objetos that match any submódulo label exactly (normalized)
          for(const o of objetosByName){
            if(assigned.has(o.id)) continue;
            const n = nameMap.get(o.id);
            if(labelOrder.includes(n)){
              // Evitar incluir el Objeto del módulo como submódulo
              if (moduleObjId != null && o.id === moduleObjId) continue;
              bucket.push(o); assigned.add(o.id);
            }
          }
          // Order bucket by the order in labelOrder, then alpha
          bucket.sort((a,b)=>{
            const ia = labelOrder.indexOf(nameMap.get(a.id));
            const ib = labelOrder.indexOf(nameMap.get(b.id));
            if(ia !== ib) return ia - ib;
            return (a.nombre_objeto||'').localeCompare(b.nombre_objeto||'');
          });
          groups.push({ id: mod.id, nombre: mod.title, objetos: bucket, moduleObjId });
        }

        // Unassigned objetos → fallback grouping by tipo or into "Otros"
        const restantes = this.objetos.filter(o => !assigned.has(o.id));
        if(restantes.length){
          // Try to cluster by tipo names (if available)
          const byTipo = new Map();
          for(const o of restantes){
            const tname = (o.tipo?.nombre || o.tipo_nombre || 'Otros') + '';
            if(!byTipo.has(tname)) byTipo.set(tname, []);
            byTipo.get(tname).push(o);
          }
          for(const [tname, arr] of byTipo){
            // Omitir grupos de fallback no deseados (p.ej. "Configuración", "Módulo")
            if (HIDDEN_FALLBACK_GROUPS.has(norm(tname))) continue;
            arr.sort((a,b)=> (a.nombre_objeto||'').localeCompare(b.nombre_objeto||''));
            groups.push({ id: `otros-${norm(tname)}`, nombre: tname || 'Otros', objetos: arr });
          }
        }
        return groups;
      },
      moduloTieneAcceso(groupId){
        const g = this.grupos().find(x => x.id === groupId);
        if(!g) return false;
        // Si existe un Objeto para el Módulo, usarlo como fuente de verdad
        if (g.moduleObjId != null){
          return this.isChecked(g.moduleObjId, 'permiso_consultar');
        }
        // Fallback: si cualquier submódulo tiene Ver
        const objs = g.objetos || [];
        for(const o of objs){ if(this.isChecked(o.id, 'permiso_consultar')) return true; }
        return false;
      },
      async toggleModulo(groupId, desired){
        const g = this.grupos().find(x => x.id === groupId);
        if(!g) return;
        // Preferir togglear el Objeto del Módulo si existe
        if (g.moduleObjId != null){
          const cur = this.isChecked(g.moduleObjId, 'permiso_consultar');
          if(cur !== desired){
            // set explicit desired value
            const rec = this.permsByObj[g.moduleObjId];
            if (rec) {
              rec.permiso_consultar = !!desired;
              this.scheduleCommit(g.moduleObjId, 'permiso_consultar');
            } else {
              await this.toggle(g.moduleObjId, 'permiso_consultar');
            }
          }
          return;
        }
        // Fallback: aplicar sobre todos los submódulos visibles del grupo
        const objs = g.objetos || [];
        for(const o of objs){
          const cur = this.isChecked(o.id, 'permiso_consultar');
          if(cur !== desired){ await this.toggle(o.id, 'permiso_consultar'); }
        }
      },

      async loadPermisosForRole(roleId){
        if(this._roleLoadPromise && this._roleLoadingId === roleId){
          return this._roleLoadPromise;
        }
        this._roleLoadingId = roleId;
        const attemptFetch = async () => {
          for(let i=0;i<this._fetchPermRetries;i++){
            try{
              const res = await fetch(`${API.permisos}?all=1&id_rol_fk=${encodeURIComponent(roleId)}`, { headers: authHeaders(), credentials:'same-origin' });
              if(res.status === 429){
                await new Promise(r=>setTimeout(r, 250 * (i+1)));
                continue;
              }
              if(!res.ok) throw new Error(res.status+': '+res.statusText);
              return await res.json();
            }catch(err){
              if(i === this._fetchPermRetries-1) throw err;
            }
          }
          throw new Error('No se pudieron cargar permisos');
        };
        this.loading = true; this.error='';
        this._roleLoadPromise = attemptFetch()
          .then(payload => {
            const list = normalizeCollection(payload);
            const byObj = {};
            for(const o of this.objetos){
              byObj[o.id] = {
                id: null,
                id_rol_fk: roleId,
                id_objeto_fk: o.id,
                permiso_consultar: false,
                permiso_insercion: false,
                permiso_actualizar: false,
                permiso_eliminacion: false,
              };
            }
            for(const p of list){
              const objId = p.id_objeto_fk || p.objeto?.id;
              if(objId && byObj[objId]){
                byObj[objId] = {
                  id: p.id ?? p.id_permiso_pk ?? null,
                  id_rol_fk: p.id_rol_fk ?? roleId,
                  id_objeto_fk: objId,
                  permiso_consultar: !!p.permiso_consultar,
                  permiso_insercion: !!p.permiso_insercion,
                  permiso_actualizar: !!p.permiso_actualizar,
                  permiso_eliminacion: !!p.permiso_eliminacion,
                };
              }
            }
            this.permsByObj = byObj;
          })
          .catch(e=>{ this.error = parseErr(e); throw e; })
          .finally(()=>{ this.loading=false; this._roleLoadPromise=null; });
        return this._roleLoadPromise;
      },

      isChecked(objId, field){
        return !!this.permsByObj?.[objId]?.[field];
      },

      keyFor(objId, field){ return `${objId}:${field}`; },
      isPending(objId, field){ return !!this.pending[this.keyFor(objId, field)]; },
      cancelPending(objId, field){
        const key = this.keyFor(objId, field);
        const p = this.pending[key];
        if(p?.controller){ try { p.controller.abort(); } catch(_){} }
        delete this.pending[key];
      },

      async toggle(objId, field){
        const roleId = this.selectedRoleId; if(!roleId) return;
        const rec = this.permsByObj[objId]; if(!rec) return;
        const prev = rec[field];
        rec[field] = !prev; // optimistic flip
        this.scheduleCommit(objId, field);
      },

      scheduleCommit(objId, field){
        const key = this.keyFor(objId, field);
        // No recrear timer si ya existe: coalesce toggles rápidos
        if(this.commitTimers[key]) return;
        this.commitTimers[key] = setTimeout(() => {
          delete this.commitTimers[key];
          this.commitNow(objId, field);
        }, 280); // ligero aumento para reducir ráfagas
      },

      async commitNow(objId, field){
        const roleId = this.selectedRoleId; if(!roleId) return;
        const rec = this.permsByObj[objId]; if(!rec) return;
        const desired = !!rec[field];
        const key = this.keyFor(objId, field);
        const old = this.pending[key];
        if(old){ try { old.controller.abort(); } catch(_){} }
        const controller = new AbortController();
        const token = Symbol('toggle');
        this.pending[key] = { controller, token };
        try{
          if(rec.id){
            const payload = { [field]: desired };
            const updated = await apiSend(`${API.permisos}/${rec.id}`, 'PUT', payload, { signal: controller.signal });
            const data = updated?.data || updated;
            if(data && this.pending[key]?.token === token){
              rec.permiso_consultar = !!data.permiso_consultar;
              rec.permiso_insercion = !!data.permiso_insercion;
              rec.permiso_actualizar = !!data.permiso_actualizar;
              rec.permiso_eliminacion = !!data.permiso_eliminacion;
            }
          } else {
            const payload = { [field]: desired };
            try{
              const updated = await apiSend(API.upsertPerm(roleId, objId), 'PUT', payload, { signal: controller.signal });
              const data = updated?.data || updated;
              if(data && this.pending[key]?.token === token){
                rec.id = data.id ?? rec.id;
                rec.permiso_consultar = !!data.permiso_consultar;
                rec.permiso_insercion = !!data.permiso_insercion;
                rec.permiso_actualizar = !!data.permiso_actualizar;
                rec.permiso_eliminacion = !!data.permiso_eliminacion;
              }
            } catch(err){
              const existing = await apiGetList(`${API.permisos}?all=1&id_rol_fk=${encodeURIComponent(roleId)}&id_objeto_fk=${encodeURIComponent(objId)}`, { signal: controller.signal });
              const first = existing[0];
              if (first) {
                const foundId = first.id ?? first.id_permiso_pk;
                if (foundId) {
                  const full = {
                    permiso_consultar: !!rec.permiso_consultar,
                    permiso_insercion: !!rec.permiso_insercion,
                    permiso_actualizar: !!rec.permiso_actualizar,
                    permiso_eliminacion: !!rec.permiso_eliminacion,
                  };
                  const upd = await apiSend(`${API.permisos}/${foundId}`, 'PUT', full, { signal: controller.signal });
                  const d2 = upd?.data || upd; if(this.pending[key]?.token === token){
                    rec.id = foundId;
                    rec.permiso_consultar = !!d2.permiso_consultar;
                    rec.permiso_insercion = !!d2.permiso_insercion;
                    rec.permiso_actualizar = !!d2.permiso_actualizar;
                    rec.permiso_eliminacion = !!d2.permiso_eliminacion;
                  }
                } else {
                  await this.loadPermisosForRole(roleId);
                }
              } else {
                const createPayload = {
                  id_rol_fk: roleId,
                  id_objeto_fk: objId,
                  permiso_consultar: !!rec.permiso_consultar,
                  permiso_insercion: !!rec.permiso_insercion,
                  permiso_actualizar: !!rec.permiso_actualizar,
                  permiso_eliminacion: !!rec.permiso_eliminacion,
                };
                const created = await apiSend(API.permisos, 'POST', createPayload, { signal: controller.signal });
                const cd = created?.data || created;
                if(cd?.id && this.pending[key]?.token === token) rec.id = cd.id;
              }
            }
          }
        } catch(e){
          if (e && (e.name === 'AbortError' || /aborted|abort/i.test(e.message||''))) {
            return;
          }
          if(this.pending[key]?.token === token){
            rec[field] = !desired;
          }
          this.error = parseErr(e);
          try{ window.showToast && window.showToast('No se pudo guardar el cambio de permiso', 'error'); }catch(_){}
          setTimeout(()=>{ this.error=''; }, 2500);
        } finally {
          if(this.pending[key]?.token === token){
            delete this.pending[key];
          }
        }
        try{ window.showToast && window.showToast('Permiso actualizado', 'success', { duration: 2500 }); }catch(_){}
      },
    };
  }

  function parseErr(e){
    const msg = (e && e.message) ? e.message : String(e||'Error');
    return msg.length > 300 ? msg.slice(0,300)+'…' : msg;
  }

  document.addEventListener('alpine:init', () => {
    const store = createAccessStore();
    Alpine.store('access', store);
    // Cargar perezoso: sólo auto-init si estamos en la vista de Configuración de accesos
    try {
      const main = document.querySelector('main');
      const isAccessView = main && /admin\.partials\.configuracion-acceso|configuracion-acceso/i.test(main.innerHTML || '');
      if (isAccessView) {
        // defer para dar tiempo a Alpine a hidratar
        setTimeout(() => { store.init().catch(()=>{}); }, 0);
      }
    } catch(_){}
  });

})();
 
