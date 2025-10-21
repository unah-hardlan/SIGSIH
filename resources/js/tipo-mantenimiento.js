window.tipoMantenimientoApiHandlers = {
    async fetchTipos(component) {
        component.loading = true;
        try {
            const params = new URLSearchParams();
            // Filtro de búsqueda
            const filtro = component.filtroTipoMantenimiento || component.filtro || '';
            if (filtro) params.set('tipo_mantenimiento', filtro);
            // Orden: mapear 'nombre' usado por el filtro compartido al campo real
            if (component.ordenarPor) {
                const sortField = component.ordenarPor === 'nombre' ? 'tipo_mantenimiento' : component.ordenarPor;
                params.set('sort', sortField);
            }
            const resp = await fetch(`/api/tipos-mantenimiento?${params.toString()}`, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });
            const data = await resp.json().catch(() => ({}));
            if (!resp.ok) throw data;
            // The controller returns data as resource collection array in 'data'
            component.items = Array.isArray(data?.data) ? data.data : (Array.isArray(data) ? data : []);
        } catch (err) {
            console.error('Error fetching tipos mantenimiento:', err);
            window.showToast && window.showToast('Error al cargar tipos de mantenimiento', 'error');
        } finally {
            component.loading = false;
        }
    },
    async submitTipo(component) {
        const nombre = String(component.tipo_mantenimiento || '').trim();
        const descripcion = String(component.descripcion_mantenimiento || '').trim();
        if (!nombre) {
            return window.showToast && window.showToast('El nombre es obligatorio', 'error');
        }
        if (component.items.some(o => (o.tipo_mantenimiento || '').toLowerCase() === nombre.toLowerCase())) {
            return window.showToast && window.showToast('Ya existe un tipo con ese nombre', 'error');
        }
        try {
            const resp = await fetch('/api/tipos-mantenimiento', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify({ tipo_mantenimiento: nombre, descripcion_mantenimiento: descripcion })
            });
            const data = await resp.json().catch(() => ({}));
            if (!resp.ok) throw data;
            window.showToast && window.showToast('Tipo de mantenimiento creado', 'success');
            component.tipo_mantenimiento = '';
            component.descripcion_mantenimiento = '';
            component.isModalOpen = false;
            await this.fetchTipos(component);
        } catch (err) {
            console.error('Error creando tipo mantenimiento:', err);
            const validationMsg = err?.message
                || (Array.isArray(err?.errors?.tipo_mantenimiento) ? err.errors.tipo_mantenimiento[0] : null)
                || (Array.isArray(err?.errors?.descripcion_mantenimiento) ? err.errors.descripcion_mantenimiento[0] : null);
            const msg = validationMsg || err?.error || 'Error al crear';
            window.showToast && window.showToast(msg, 'error');
        }
    },
    async updateTipo(component) {
        if (!component.itemToEdit?.id_tipo_mantenimiento_pk) return;
        // Usar los campos locales del modal para evitar acceder a itemToEdit cuando pueda ser null
        const nombre = String(component.edit_tipo_mantenimiento ?? component.itemToEdit?.tipo_mantenimiento ?? '').trim();
        const descripcion = String(component.edit_descripcion_mantenimiento ?? component.itemToEdit?.descripcion_mantenimiento ?? '').trim();
        const payload = {
            tipo_mantenimiento: nombre,
            descripcion_mantenimiento: descripcion,
        };
        // Evitar enviar si no hay cambios
        if (
            nombre === (component.itemToEdit?.tipo_mantenimiento || '') &&
            descripcion === (component.itemToEdit?.descripcion_mantenimiento || '')
        ) {
            window.showToast && window.showToast('No hay cambios para guardar', 'warning');
            component.isEditModalOpen = false;
            component.itemToEdit = null;
            component.edit_tipo_mantenimiento = '';
            component.edit_descripcion_mantenimiento = '';
            return;
        }
        if (!payload.tipo_mantenimiento) {
            return window.showToast && window.showToast('El nombre es obligatorio', 'error');
        }
        if (component.items.some(o => (o.tipo_mantenimiento || '').toLowerCase() === payload.tipo_mantenimiento.toLowerCase() && o.id_tipo_mantenimiento_pk !== component.itemToEdit.id_tipo_mantenimiento_pk)) {
            return window.showToast && window.showToast('Ya existe otro tipo con ese nombre', 'error');
        }
        try {
            const resp = await fetch(`/api/tipos-mantenimiento/${component.itemToEdit.id_tipo_mantenimiento_pk}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify(payload)
            });
            const data = await resp.json().catch(() => ({}));
            if (!resp.ok) throw data;
            window.showToast && window.showToast('Tipo de mantenimiento actualizado', 'success');
            component.isEditModalOpen = false;
            component.itemToEdit = null;
            component.edit_tipo_mantenimiento = '';
            component.edit_descripcion_mantenimiento = '';
            await this.fetchTipos(component);
        } catch (err) {
            console.error('Error actualizando tipo mantenimiento:', err);
            const validationMsg = err?.message
                || (Array.isArray(err?.errors?.tipo_mantenimiento) ? err.errors.tipo_mantenimiento[0] : null)
                || (Array.isArray(err?.errors?.descripcion_mantenimiento) ? err.errors.descripcion_mantenimiento[0] : null);
            const msg = validationMsg || err?.error || 'Error al actualizar';
            window.showToast && window.showToast(msg, 'error');
        }
    },
    async deleteTipo(component) {
        if (!component.itemToDelete?.id) return;
        try {
            const resp = await fetch(`/api/tipos-mantenimiento/${component.itemToDelete.id}`, {
                method: 'DELETE',
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });
            const data = await resp.json().catch(() => ({}));
            if (!resp.ok) throw data;
            window.showToast && window.showToast('Tipo eliminado', 'success');
            component.isDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchTipos(component);
        } catch (err) {
            console.error('Error eliminando tipo mantenimiento:', err);
            const msg = err?.error || 'Error al eliminar';
            window.showToast && window.showToast(msg, 'error');
        }
    },
};
