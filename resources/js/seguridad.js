// Access management store for roles, objects and permissions (Permiso booleans)
(function(){
  const API = {
    roles: '/api/roles',
    objetos: '/api/objetos',
  permisos: '/api/permisos',
  upsertPerm: (rolId, objId) => `/api/permisos/roles/${rolId}/objetos/${objId}`,
  };

  const authHeaders = () => {
    const t = localStorage.getItem('authToken');
    return t ? { 'Authorization': `Bearer ${t}`, 'Content-Type':'application/json' } : { 'Content-Type':'application/json' };
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
      roles: [], // [{id, rol, descripcion_rol, ...}]
      objetos: [], // [{id, nombre_objeto, ...}]
      selectedRoleId: null,
  // pending operations per cell: key `${objId}:${field}` -> { controller, token }
  pending: {},
  // debounce timers per cell
  commitTimers: {},
      // permisos por objeto para el rol seleccionado: map de objId -> { id, permiso_consultar, permiso_insercion, permiso_actualizar, permiso_eliminacion }
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
          const [rolesRes, objetosRes] = await Promise.all([
            apiGet(`${API.roles}?all=1`),
            apiGet(`${API.objetos}?all=1`),
          ]);
          this.roles = normalizeCollection(rolesRes);
          this.objetos = normalizeCollection(objetosRes);
          if(this.roles.length && !this.selectedRoleId){
            await this.selectRole(this.roles[0].id);
          }
        } catch(e){ this.error = parseErr(e); }
        finally { this.loading = false; }
      },

      async selectRole(roleId){
        this.selectedRoleId = roleId;
        await this.loadPermisosForRole(roleId);
      },

      async loadPermisosForRole(roleId){
        try{
          this.loading = true; this.error = '';
          const res = await apiGet(`${API.permisos}?all=1&id_rol_fk=${encodeURIComponent(roleId)}`);
          const list = normalizeCollection(res);
          // construir mapa base con falsos
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
        } catch(e){ this.error = parseErr(e); }
        finally { this.loading = false; }
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
        // Debounce commit to avoid lag and coalesce rapid toggles
        this.scheduleCommit(objId, field);
      },

      scheduleCommit(objId, field){
        const key = this.keyFor(objId, field);
        if(this.commitTimers[key]){ clearTimeout(this.commitTimers[key]); }
        this.commitTimers[key] = setTimeout(() => {
          delete this.commitTimers[key];
          this.commitNow(objId, field);
        }, 180);
      },

      async commitNow(objId, field){
        const roleId = this.selectedRoleId; if(!roleId) return;
        const rec = this.permsByObj[objId]; if(!rec) return;
        const desired = !!rec[field];
        const key = this.keyFor(objId, field);
        // cancel any in-flight for this cell
        const old = this.pending[key];
        if(old){ try { old.controller.abort(); } catch(_){} }
        const controller = new AbortController();
        const token = Symbol('toggle');
        this.pending[key] = { controller, token };
        try{
          if(rec.id){
            const payload = { [field]: desired };
            const updated = await apiSend(`${API.permisos}/${rec.id}`, 'PUT', payload, { signal: controller.signal });
            // reflect booleans in case backend normalizes
            const data = updated?.data || updated;
            if(data && this.pending[key]?.token === token){
              rec.permiso_consultar = !!data.permiso_consultar;
              rec.permiso_insercion = !!data.permiso_insercion;
              rec.permiso_actualizar = !!data.permiso_actualizar;
              rec.permiso_eliminacion = !!data.permiso_eliminacion;
            }
          } else {
            // Intentar upsert atómico por rol/objeto
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
              // Fallback seguro: buscar existente o crear
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
                    rec.permiso_consultar = !!d2.permiso_consultar; rec.permiso_insercion = !!d2.permiso_insercion;
                    rec.permiso_actualizar = !!d2.permiso_actualizar; rec.permiso_eliminacion = !!d2.permiso_eliminacion;
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
          // if request was aborted due to a newer toggle, ignore
          if (e && (e.name === 'AbortError' || /aborted|abort/i.test(e.message||''))) {
            return;
          }
          // revert on error
          // revert to server-synced state only if this is still the latest operation for this cell
          if(this.pending[key]?.token === token){
            rec[field] = !desired;
          }
          this.error = parseErr(e);
          setTimeout(()=>{ this.error=''; }, 2500);
        } finally {
          // clear pending only if still current
          if(this.pending[key]?.token === token){
            delete this.pending[key];
          }
        }
      },
    };
  }

  function parseErr(e){
    const msg = (e && e.message) ? e.message : String(e||'Error');
    return msg.length > 300 ? msg.slice(0,300)+'…' : msg;
  }

  document.addEventListener('alpine:init', () => {
    Alpine.store('access', createAccessStore());
  });
})();
