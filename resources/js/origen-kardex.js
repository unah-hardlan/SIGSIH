window.origenKardexApiHandlers = {
  async fetchOrigenes(component){
    component.loading = true;
    try{
      const params = new URLSearchParams();
      if (component.filtroOrigen) params.set('q', component.filtroOrigen);
      if (component.ordenarPor) params.set('sort', component.ordenarPor);
      const resp = await fetch(`/api/origenes?${params.toString()}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
      const data = await resp.json().catch(()=>({}));
      if(!resp.ok) throw data;
      component.origenes = Array.isArray(data?.data) ? data.data : Array.isArray(data) ? data : [];
    }catch(err){
      console.error('Error fetching origenes:', err);
      window.showToast && window.showToast('Error al cargar orígenes', 'error');
    }finally{
      component.loading = false;
    }
  },
  async submitOrigen(component){
    const nombre = String(component.nombre_origen||'').trim();
    const descripcion = String(component.descripcion_origen||'').trim();
    const activo = !!component.activo;
    if(!nombre){
      return window.showToast && window.showToast('El nombre es obligatorio','error');
    }
    if(component.origenes.some(o => (o.nombre_origen||'').toLowerCase() === nombre.toLowerCase())){
      return window.showToast && window.showToast('Ya existe un origen con ese nombre','error');
    }
    try{
      const resp = await fetch('/api/origenes', {
        method:'POST',
        headers:{ 'Content-Type':'application/json', Accept:'application/json' },
        credentials:'same-origin',
        body: JSON.stringify({ nombre_origen: nombre, descripcion_origen: descripcion, activo })
      });
      const data = await resp.json().catch(()=>({}));
      if(!resp.ok) throw data;
      window.showToast && window.showToast('Origen creado','success');
      component.nombre_origen=''; component.descripcion_origen=''; component.activo=true; component.isModalOpen=false;
      await this.fetchOrigenes(component);
    }catch(err){
      console.error('Error creando origen:', err);
      const validationMsg = err?.message || (Array.isArray(err?.errors?.nombre_origen) ? err.errors.nombre_origen[0] : null)
        || (Array.isArray(err?.errors?.descripcion_origen) ? err.errors.descripcion_origen[0] : null)
        || (Array.isArray(err?.errors?.activo) ? err.errors.activo[0] : null);
      const msg = validationMsg || err?.error || 'Error al crear el origen';
      window.showToast && window.showToast(msg,'error');
    }
  },
  async updateOrigen(component){
    if(!component.itemToEdit?.id_origen_pk) return;
    const payload = {
      nombre_origen: String(component.itemToEdit.nombre_origen||'').trim(),
      descripcion_origen: String(component.itemToEdit.descripcion_origen||'').trim(),
      activo: !!component.itemToEdit.activo,
    };
    if(!payload.nombre_origen){
      return window.showToast && window.showToast('El nombre es obligatorio','error');
    }
    if(component.origenes.some(o => (o.nombre_origen||'').toLowerCase() === payload.nombre_origen.toLowerCase() && o.id_origen_pk !== component.itemToEdit.id_origen_pk)){
      return window.showToast && window.showToast('Ya existe otro origen con ese nombre','error');
    }
    try{
      const resp = await fetch(`/api/origenes/${component.itemToEdit.id_origen_pk}`, {
        method:'PUT', headers:{ 'Content-Type':'application/json', Accept:'application/json' }, credentials:'same-origin', body: JSON.stringify(payload)
      });
      const data = await resp.json().catch(()=>({}));
      if(!resp.ok) throw data;
      window.showToast && window.showToast('Origen actualizado','success');
      component.isEditModalOpen=false; component.itemToEdit=null;
      await this.fetchOrigenes(component);
    }catch(err){
      console.error('Error actualizando origen:', err);
      const validationMsg = err?.message || (Array.isArray(err?.errors?.nombre_origen) ? err.errors.nombre_origen[0] : null)
        || (Array.isArray(err?.errors?.descripcion_origen) ? err.errors.descripcion_origen[0] : null)
        || (Array.isArray(err?.errors?.activo) ? err.errors.activo[0] : null);
      const msg = validationMsg || err?.error || 'Error al actualizar el origen';
      window.showToast && window.showToast(msg,'error');
    }
  },
  async deleteOrigen(component){
    if(!component.itemToDelete?.id_origen_pk) return;
    try{
      const resp = await fetch(`/api/origenes/${component.itemToDelete.id_origen_pk}`, { method:'DELETE', headers:{ Accept:'application/json' }, credentials:'same-origin' });
      const data = await resp.json().catch(()=>({}));
      if(!resp.ok) throw data;
      window.showToast && window.showToast('Origen eliminado','success');
      component.isDeleteModalOpen=false; component.itemToDelete=null;
      await this.fetchOrigenes(component);
    }catch(err){
      console.error('Error eliminando origen:', err);
      const msg = err?.error || 'Error al eliminar el origen';
      window.showToast && window.showToast(msg,'error');
    }
  }
};
