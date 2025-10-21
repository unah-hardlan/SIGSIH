window.agenciasApiHandlers = {
  async fetchAgencias(component) {
    component.loading = true;
    try {
      const params = new URLSearchParams();
      if (component.searchAgencia) params.set('search', component.searchAgencia);
      if (component.ciudadFiltro) {
        const val = component.ciudadFiltro;
        const isNumeric = /^\d+$/.test(String(val));
        if (isNumeric) params.set('id_ciudad_fk', val);
        else params.set('ciudad_nombre', val);
      }
      if (component.ordenarPor) params.set('ordenarPor', component.ordenarPor);
      const res = await fetch(`/api/agencias?${params.toString()}`, {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin'
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok) throw data;
      component.agencias = Array.isArray(data?.data) ? data.data : Array.isArray(data) ? data : [];
    } catch (e) {
      console.error('Error fetching agencias:', e);
      window.showToast && window.showToast('Error al cargar agencias', 'error');
    } finally {
      component.loading = false;
    }
  },
  async createAgencia(component) {
    if (component.__submitting) return;
    component.__submitting = true;
    const f = component.formAgencia || {};
    const payload = {
      nombre_agencia: String(f.nombre || '').trim(),
      horario_agencia: String(f.horario || '').trim(),
      id_direccion_fk: f.direccion_id
    };
    if (!payload.nombre_agencia || !payload.nombre_agencia.trim()) { window.showToast && window.showToast('El nombre es obligatorio', 'error'); component.__submitting = false; return; }
    if (!payload.horario_agencia) { window.showToast && window.showToast('El horario es obligatorio', 'error'); component.__submitting = false; return; }
    if (!payload.id_direccion_fk) { window.showToast && window.showToast('Debe seleccionar una dirección', 'error'); component.__submitting = false; return; }
    try {
      const res = await fetch('/api/agencias', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify(payload)
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok) throw data;
      window.showToast && window.showToast('Agencia creada exitosamente', 'success');
  component.isAgenciaModalOpen = false;
      component.formAgencia = { id: null, nombre: '', horario: '', direccion_id: '' };
      await this.fetchAgencias(component);
    } catch (e) {
      console.error('Error creating agencia:', e);
  const msg = e?.message || (e?.errors && e.errors.nombre_agencia ? e.errors.nombre_agencia[0] : null) || 'Error al crear la agencia';
  window.showToast && window.showToast(msg, 'error');
    } finally { component.__submitting = false; }
  },
  async updateAgencia(component) {
    if (component.__submitting) return;
    component.__submitting = true;
    const f = component.formAgencia || {};
    if (!f.id) return;
    const payload = {
      nombre_agencia: String(f.nombre || '').trim(),
      horario_agencia: String(f.horario || '').trim(),
      id_direccion_fk: f.direccion_id
    };
    try {
      const res = await fetch(`/api/agencias/${f.id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify(payload)
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok) throw data;
      window.showToast && window.showToast('Agencia actualizada exitosamente', 'success');
  component.isAgenciaModalOpen = false;
  component.formAgencia = { id: null, nombre: '', horario: '', direccion_id: '' };
      await this.fetchAgencias(component);
    } catch (e) {
      console.error('Error updating agencia:', e);
  const msg = e?.message || (e?.errors && e.errors.nombre_agencia ? e.errors.nombre_agencia[0] : null) || 'Error al actualizar la agencia';
  window.showToast && window.showToast(msg, 'error');
    } finally { component.__submitting = false; }
  },
  async deleteAgencia(component) {
    if (!component.agenciaToDelete?.id) return;
    try {
      const res = await fetch(`/api/agencias/${component.agenciaToDelete.id}`, {
        method: 'DELETE',
        headers: { Accept: 'application/json' },
        credentials: 'same-origin'
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok) throw data;
      window.showToast && window.showToast('Agencia eliminada exitosamente', 'success');
      component.isDeleteAgenciaModalOpen = false;
      component.agenciaToDelete = null;
      await this.fetchAgencias(component);
    } catch (e) {
      console.error('Error deleting agencia:', e);
      const msg = e?.message || (e?.error ? e.error : null) || 'Error al eliminar la agencia';
      window.showToast && window.showToast(msg, 'error');
    }
  }
};