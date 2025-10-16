window.reportesVisitaApiHandlers = {
  headers() { return { 'Content-Type': 'application/json', Accept: 'application/json' }; },
  async fetchCatalogs(component) {
    try {
      const [tv, sr, ar, os] = await Promise.all([
        fetch('/api/tipos-visita?all=1', { headers: this.headers(), credentials: 'same-origin' }),
        fetch('/api/servicios-realizados?all=1', { headers: this.headers(), credentials: 'same-origin' }),
        fetch('/api/acciones-realizadas?all=1', { headers: this.headers(), credentials: 'same-origin' }),
        fetch('/api/ordenes-servicio?all=1', { headers: this.headers(), credentials: 'same-origin' }),
      ]);
      const [tvData, srData, arData, osData] = await Promise.all([
        tv.json().catch(() => ({})),
        sr.json().catch(() => ({})),
        ar.json().catch(() => ({})),
        os.json().catch(() => ({})),
      ]);
      component.tiposVisita = Array.isArray(tvData?.data) ? tvData.data : (Array.isArray(tvData) ? tvData : []);
      component.serviciosRealizados = Array.isArray(srData?.data) ? srData.data : (Array.isArray(srData) ? srData : []);
      component.accionesRealizadas = Array.isArray(arData?.data) ? arData.data : (Array.isArray(arData) ? arData : []);
      component.ordenesServicio = Array.isArray(osData?.data) ? osData.data : (Array.isArray(osData) ? osData : []);
    } catch (e) { console.error('Error cargando catálogos reporte:', e); }
  },
  async fetchReportes(component) {
    component.loadingReportes = true;
    try {
      const p = new URLSearchParams();
      if (component.searchReportes) p.set('q', component.searchReportes);
      if (component.filtroTipoVisita) p.set('id_tipo_visita_fk', component.filtroTipoVisita);
      if (component.filtroServicioRealizado) p.set('id_servicio_realizado_fk', component.filtroServicioRealizado);
      if (component.filtroAccionRealizada) p.set('id_accion_realizada_fk', component.filtroAccionRealizada);
      if (component.filtroOrdenServicio) p.set('id_orden_servicio_fk', component.filtroOrdenServicio);
      if (component.desde) p.set('desde', component.desde);
      if (component.hasta) p.set('hasta', component.hasta);
      if (component.ordenarPor) {
        const sortMap = { fecha: 'fecha' };
        p.set('sort', sortMap[component.ordenarPor] || component.ordenarPor);
        if (component.ordenarDirection) p.set('direction', component.ordenarDirection);
      }
      // get paginated or all; using all for client simplicity
      p.set('all', '1'); p.set('sort', 'fecha');
      const resp = await fetch(`/api/reportes-visita?${p.toString()}`, {
        headers: this.headers(), credentials: 'same-origin'
      });
      const data = await resp.json().catch(() => ({}));
      if (!resp.ok) throw data;
      const items = Array.isArray(data?.data) ? data.data : (Array.isArray(data) ? data : []);
      component.reportes = items.map(i => {
        const osNum = i?.orden_servicio?.numero_orden_servicio
          || i?.numero_orden_servicio
          || (Array.isArray(component.ordenesServicio)
                ? (component.ordenesServicio.find(os => Number(os.id_orden_servicio_pk) === Number(i.id_orden_servicio_fk))?.numero_orden_servicio)
                : null);
        const osId = i?.orden_servicio?.id_orden_servicio_pk || i?.id_orden_servicio_fk;
        // Mostrar el número tal cual si existe (evitar duplicar prefijo "OS"); si no hay número, mostrar fallback "OS {id}"
        const osLabel = osNum ? String(osNum) : (osId ? `OS ${osId}` : '');
        return {
        id_reportes_pk: i.id_reportes_pk,
        fecha_reporte: i.fecha_reporte,
        observaciones: i.observaciones,
        id_tipo_visita_fk: i.id_tipo_visita_fk,
        id_servicio_realizado_fk: i.id_servicio_realizado_fk,
        id_accion_realizada_fk: i.id_accion_realizada_fk,
        id_orden_servicio_fk: i.id_orden_servicio_fk,
        tipo_visita: i.tipo_visita?.nombre_tipo_visita || '',
        servicio_realizado: i.servicio_realizado?.nombre_servicio || '',
        accion_realizada: i.accion_realizada?.nombre_accion || '',
        // Mostrar como en el modal: "OS " + numero_orden_servicio (si existe); si no, fallback al ID
        orden_servicio: osLabel,
      };
      });
    } catch (e) {
      console.error('Error cargando reportes visita:', e);
      window.showToast && window.showToast('Error al cargar reportes', 'error');
    } finally { component.loadingReportes = false; }
  },
  async storeReporte(component) {
    // Validaciones mínimas
    if (!component.new_id_tipo_visita_fk || !component.new_id_servicio_realizado_fk || !component.new_id_accion_realizada_fk || !component.new_id_orden_servicio_fk) {
      return window.showToast && window.showToast('Completa los campos requeridos', 'error');
    }
    try {
      const payload = {
        fecha_reporte: component.new_fecha_reporte || null,
        observaciones: component.new_observaciones?.trim() || '',
        id_tipo_visita_fk: Number(component.new_id_tipo_visita_fk),
        id_servicio_realizado_fk: Number(component.new_id_servicio_realizado_fk),
        id_accion_realizada_fk: Number(component.new_id_accion_realizada_fk),
        id_orden_servicio_fk: Number(component.new_id_orden_servicio_fk),
      };
      const resp = await fetch('/api/reportes-visita', { method: 'POST', headers: this.headers(), credentials: 'same-origin', body: JSON.stringify(payload) });
      const data = await resp.json().catch(() => ({}));
      if (!resp.ok) throw data;
      window.showToast && window.showToast('Reporte creado', 'success');
      component.isReporteModalOpen = false;
      component.new_fecha_reporte = '';
      component.new_observaciones = '';
      component.new_id_tipo_visita_fk = '';
      component.new_id_servicio_realizado_fk = '';
      component.new_id_accion_realizada_fk = '';
      component.new_id_orden_servicio_fk = '';
      await this.fetchReportes(component);
    } catch (e) {
      console.error('Error creando reporte:', e);
      const msg = e?.message || Object.values(e?.errors || {})?.[0]?.[0] || 'Error al crear';
      window.showToast && window.showToast(msg, 'error');
    }
  },
  async updateReporte(component) {
    if (!component.reporteToEdit?.id_reportes_pk) return;
    try {
      const payload = {
        fecha_reporte: component.edit_fecha_reporte || undefined,
        observaciones: component.edit_observaciones?.trim(),
        id_tipo_visita_fk: Number(component.edit_id_tipo_visita_fk),
        id_servicio_realizado_fk: Number(component.edit_id_servicio_realizado_fk),
        id_accion_realizada_fk: Number(component.edit_id_accion_realizada_fk),
        id_orden_servicio_fk: Number(component.edit_id_orden_servicio_fk),
      };
      const resp = await fetch(`/api/reportes-visita/${component.reporteToEdit.id_reportes_pk}`, { method: 'PUT', headers: this.headers(), credentials: 'same-origin', body: JSON.stringify(payload) });
      const data = await resp.json().catch(() => ({}));
      if (!resp.ok) throw data;
      window.showToast && window.showToast('Reporte actualizado', 'success');
      component.isReporteEditModalOpen = false;
      component.reporteToEdit = null;
      await this.fetchReportes(component);
    } catch (e) {
      console.error('Error actualizando reporte:', e);
      const msg = e?.message || Object.values(e?.errors || {})?.[0]?.[0] || 'Error al actualizar';
      window.showToast && window.showToast(msg, 'error');
    }
  },
  async deleteReporte(component) {
    if (!component.reporteToDelete?.id_reportes_pk) return;
    try {
      const resp = await fetch(`/api/reportes-visita/${component.reporteToDelete.id_reportes_pk}`, { method: 'DELETE', headers: this.headers(), credentials: 'same-origin' });
      const data = await resp.json().catch(() => ({}));
      if (!resp.ok) throw data;
      window.showToast && window.showToast('Reporte eliminado', 'success');
      component.isReporteDeleteModalOpen = false;
      component.reporteToDelete = null;
      await this.fetchReportes(component);
    } catch (e) {
      console.error('Error eliminando reporte:', e);
      const msg = e?.error || 'Error al eliminar';
      window.showToast && window.showToast(msg, 'error');
    }
  },
};
