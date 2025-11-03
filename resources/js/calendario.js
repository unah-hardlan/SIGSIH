/*
  Handlers for Calendario module: fetch events by month range, create/update/delete events,
  and fetch catalogs (agencias, clientes, estados-calendario, tipos-mantenimiento, ordenes-servicio optional).
*/

(function initCalendarioApiHandlers() {
  if (window.calendarioApiHandlers) return;

  const jsonHeaders = { "Content-Type": "application/json", Accept: "application/json" };

  function monthRange(year, month /* 0-based */) {
    const start = new Date(Date.UTC(year, month, 1, 0, 0, 0));
    const end = new Date(Date.UTC(year, month + 1, 0, 23, 59, 59));
    const fmt = (d) => {
      const pad = (n) => String(n).padStart(2, "0");
      return `${d.getUTCFullYear()}-${pad(d.getUTCMonth() + 1)}-${pad(d.getUTCDate())} ${pad(d.getUTCHours())}:${pad(d.getUTCMinutes())}:${pad(d.getUTCSeconds())}`;
    };
    return { desde: fmt(start), hasta: fmt(end) };
  }

  async function fetchJson(url, opts = {}) {
    const res = await fetch(url, opts);
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
      // Attach status info for better error messages
      data.__http = { status: res.status, statusText: res.statusText };
      throw data;
    }
    return data;
  }

  async function fetchCatalog(url) {
    try {
      const data = await fetchJson(url, { headers: { Accept: "application/json" }, credentials: "same-origin" });
      if (Array.isArray(data?.data)) return data.data;
      if (Array.isArray(data?.data?.data)) return data.data.data; // handle double-wrapped resource
      if (Array.isArray(data)) return data;
      return [];
    } catch (e) {
      console.error("Catalog fetch error", url, e);
      return [];
    }
  }

  window.calendarioApiHandlers = {
    // Catalogs
    async loadCatalogs(component) {
      component.loadingCatalogs = true;
      try {
        const [agencias, estados, tipos, clientesUnified, ordenes] = await Promise.all([
          fetchCatalog("/api/agencias?per_page=500&all=1"),
          fetchCatalog("/api/estados-calendario"),
          fetchCatalog("/api/tipos-mantenimiento?per_page=500"),
          fetchCatalog("/api/clientes?per_page=500&all=1"),
          fetchCatalog("/api/ordenes-servicio?per_page=500"),
        ]);
        component.catalogAgencias = agencias;
        component.catalogEstados = estados;
        component.catalogTiposMantenimiento = tipos;
        // Calendario now expects id_cliente_fk referencing tbl_cliente.id_cliente_pk
        // Use unified clientes: for empresas display nombre comercial, for persona build name; id is cliente.id
        const clientes = (clientesUnified || []).map((c) => {
          let nombre;
          if (c.tipo === 'empresa') {
            nombre = c.nombre || c.nombre_comercial || c.razon_social || '';
          } else {
            // Build full name from any available persona fields
            const parts = [c.primer_nombre, c.segundo_nombre, c.primer_apellido, c.segundo_apellido].filter(Boolean);
            nombre = parts.join(' ');
          }
          if (!nombre || !nombre.trim()) {
            nombre = `Cliente ${c.id}`; // fallback so it still appears in selector
          }
          return { id: c.id, nombre };
        }).filter(c => c.id); // only require a valid id now
        // Optionally de-duplicate by id (in case backend returns duplicates)
        const uniqMap = {};
        for (const c of clientes) { if (!uniqMap[c.id]) uniqMap[c.id] = c; }
        const clientesFinal = Object.values(uniqMap);
        component.catalogClientes = clientesFinal;
        if (!clientesFinal.length) {
          window.showToast && window.showToast('No se encontraron clientes desde Clientes/Empresas. Verifique que existan registros en esas tablas.', 'warning');
        }
        component.catalogOrdenesServicio = Array.isArray(ordenes)
          ? ordenes
          : Array.isArray(ordenes?.data)
            ? ordenes.data
            : [];
      } finally {
        component.loadingCatalogs = false;
      }
    },

    async fetchClientesByAgencia(component, agenciaId) {
      // When an agency is selected, fetch only clients linked to that agency.
      // Falls back to all clients when agenciaId is falsy.
      try {
        if (!agenciaId) {
          component.loadingFilteredClientes = false;
          component.filteredClientes = [];
          if (component.formEvento) component.formEvento.id_cliente_fk = '';
          if (component.formEventoLista) component.formEventoLista.id_cliente_fk = '';
          return [];
        }
        component.loadingFilteredClientes = true;
        const params = new URLSearchParams();
        params.set('all', '1');
        params.set('per_page', '500');
        params.set('agencia_id', String(agenciaId));
        const data = await fetchJson(`/api/clientes?${params.toString()}`, {
          headers: { Accept: 'application/json' },
          credentials: 'same-origin'
        });
        const list = Array.isArray(data?.data) ? data.data : Array.isArray(data) ? data : [];
        const clientes = (list || []).map((c) => ({ id: c.id, nombre: c.nombre }));
        component.filteredClientes = clientes;
        // If current selected client is not present, clear it to force user selection
        const ensureSelectedIn = (model) => {
          const cur = model?.id_cliente_fk;
          if (!cur) return;
          const found = clientes.some((x) => Number(x.id) === Number(cur));
          if (!found) model.id_cliente_fk = '';
        };
        if (component.formEvento) ensureSelectedIn(component.formEvento);
        if (component.formEventoLista) ensureSelectedIn(component.formEventoLista);
        return clientes;
      } catch (e) {
        console.error('Error fetching clientes por agencia', e);
        component.filteredClientes = [];
        return [];
      } finally {
        component.loadingFilteredClientes = false;
      }
    },

    async onAgenciaChange(component, agenciaId /*, contextKey */) {
      // contextKey reserved if we later need to distinguish forms
      return this.fetchClientesByAgencia(component, agenciaId);
    },

    async fetchOrdenesByCliente(component, clienteId) {
      try {
        if (!clienteId) {
          component.loadingFilteredOrdenes = false;
          component.filteredOrdenesServicio = [];
          if (component.formEvento) component.formEvento.id_orden_servicio_fk = '';
          if (component.formEventoLista) component.formEventoLista.id_orden_servicio_fk = '';
          return [];
        }
        component.loadingFilteredOrdenes = true;
        const params = new URLSearchParams();
        params.set('all', '1');
        params.set('per_page', '500');
        params.set('cliente_id', String(clienteId));
        const data = await fetchJson(`/api/ordenes-servicio?${params.toString()}`, {
          headers: { Accept: 'application/json' },
          credentials: 'same-origin'
        });
        const list = Array.isArray(data?.data) ? data.data : Array.isArray(data) ? data : [];
        const os = (list || []).map((o) => ({
          id: o.id_orden_servicio_pk || o.id || o.id_orden_servicio,
          label: o.numero_orden_servicio || o.codigo_orden || `OS-${o.id_orden_servicio_pk || o.id}`
        })).filter(x => x.id);
        component.filteredOrdenesServicio = os;
        const ensureSelectedIn = (model) => {
          const cur = model?.id_orden_servicio_fk;
          if (!cur) return;
          const found = os.some((x) => Number(x.id) === Number(cur));
          if (!found) model.id_orden_servicio_fk = '';
        };
        if (component.formEvento) ensureSelectedIn(component.formEvento);
        if (component.formEventoLista) ensureSelectedIn(component.formEventoLista);
        return os;
      } catch (e) {
        console.error('Error fetching ordenes por cliente', e);
        component.filteredOrdenesServicio = [];
        return [];
      } finally {
        component.loadingFilteredOrdenes = false;
      }
    },

    async onClienteChange(component, clienteId /*, contextKey */) {
      return this.fetchOrdenesByCliente(component, clienteId);
    },

    // Events
    async fetchMonth(component) {
      component.loadingEvents = true;
      try {
        const { desde, hasta } = monthRange(component.currentYear, component.currentMonth);
        const params = new URLSearchParams();
        params.set("fecha_desde", desde);
        params.set("fecha_hasta", hasta);
        params.set("per_page", "500");
        const data = await fetchJson(`/api/calendario?${params.toString()}`, {
          headers: { Accept: "application/json" },
          credentials: "same-origin",
        });
        const list = Array.isArray(data?.data) ? data.data : [];
        // Normalize into a map keyed by YYYY-MM-DD
        const map = {};
        for (const ev of list) {
          const fechaStr = ev.fecha || ""; // 'Y-m-d H:i:s'
          const dayKey = fechaStr.substring(0, 10);
          const hora = fechaStr.substring(11, 16);
          const estadoNombre = ev.estado?.nombre || ev.estado?.codigo || "";
          const agenciaNombre = ev.agencia?.nombre_agencia || "";
          const direccion = ev.agencia?.direccion
            ? `${ev.agencia.direccion?.direccion || ""}, ${ev.agencia.direccion?.ciudad?.ciudad || ""}`
            : "";
          const clienteNombre = ev.cliente_nombre || ev.cliente?.nombre || (ev.cliente
            ? [ev.cliente.primer_nombre, ev.cliente.segundo_nombre, ev.cliente.primer_apellido, ev.cliente.segundo_apellido].filter(Boolean).join(" ")
            : "");
          const tipoNombre = ev.tipo_mantenimiento?.tipo_mantenimiento || "";
          const item = {
            id: ev.id_calendario_pk,
            titulo: ev.descripcion_calendario,
            hora: hora,
            estado: estadoNombre,
            agencia: agenciaNombre,
            direccion: direccion,
            cliente: clienteNombre,
            tipo: tipoNombre,
            orden: ev.id_orden_servicio_fk ? `OS-${ev.id_orden_servicio_fk}` : "",
            observaciones: ev.observaciones_calendario || "",
            raw: ev,
          };
          if (!map[dayKey]) map[dayKey] = [];
          map[dayKey].push(item);
        }
        component.events = map;
      } catch (e) {
        console.error("Error fetching calendar events", e);
        window.showToast && window.showToast("Error al cargar eventos", "error");
        component.events = {};
      } finally {
        component.loadingEvents = false;
      }
    },

    async createEvent(component, form) {
      try {
        // Quick client-side required validations to avoid blind 422s
        if (!form.id_orden_servicio_fk) {
          window.showToast && window.showToast("Seleccione una Orden de Servicio", "error");
          return;
        }
        const required = [
          ['id_estado_calendario_fk', 'Seleccione un Estado'],
          ['id_agencias_fk', 'Seleccione una Agencia'],
          ['id_tipo_mantenimiento_fk', 'Seleccione un Tipo de mantenimiento'],
          ['id_cliente_fk', 'Seleccione un Cliente']
        ];
        for (const [k, msg] of required) {
          if (!form[k]) {
            window.showToast && window.showToast(msg, 'error');
            return;
          }
        }
        const payload = {
          fecha: form.fecha,
          descripcion_calendario: form.descripcion_calendario,
          observaciones_calendario: form.observaciones_calendario || null,
          id_estado_calendario_fk: form.id_estado_calendario_fk,
          id_agencias_fk: form.id_agencias_fk,
          id_orden_servicio_fk: form.id_orden_servicio_fk,
          id_tipo_mantenimiento_fk: form.id_tipo_mantenimiento_fk,
          id_cliente_fk: form.id_cliente_fk,
        };
        const data = await fetchJson("/api/calendario", {
          method: "POST",
          headers: jsonHeaders,
          credentials: "same-origin",
          body: JSON.stringify(payload),
        });
        window.showToast && window.showToast("Evento creado", "success");
        await this.fetchMonth(component);
        return data?.data;
      } catch (e) {
        const errs = e?.errors;
        if (errs) {
          Object.values(errs).forEach((arr) => Array.isArray(arr) && arr.forEach((m) => window.showToast && window.showToast(m, "error")));
          return;
        }
        const msg = e?.message || e?.error || (e?.__http ? `HTTP ${e.__http.status} ${e.__http.statusText}` : '') || 'No se pudo crear el evento';
        window.showToast && window.showToast(msg, "error");
      }
    },

    async updateEvent(component, id, form) {
      try {
        if (typeof form.id_orden_servicio_fk !== 'undefined' && !form.id_orden_servicio_fk) {
          window.showToast && window.showToast("Seleccione una Orden de Servicio", "error");
          return;
        }
        const payload = { ...form };
        const data = await fetchJson(`/api/calendario/${id}`, {
          method: "PUT",
          headers: jsonHeaders,
          credentials: "same-origin",
          body: JSON.stringify(payload),
        });
        window.showToast && window.showToast("Evento actualizado", "success");
        await this.fetchMonth(component);
        return data?.data;
      } catch (e) {
        const errs = e?.errors;
        if (errs) {
          Object.values(errs).forEach((arr) => Array.isArray(arr) && arr.forEach((m) => window.showToast && window.showToast(m, "error")));
          return;
        }
        const msg = e?.message || e?.error || (e?.__http ? `HTTP ${e.__http.status} ${e.__http.statusText}` : '') || 'No se pudo actualizar el evento';
        window.showToast && window.showToast(msg, "error");
      }
    },

    async cancelEvent(component, id, estadoCanceladoId) {
      // Mark as canceled via PUT changing estado
      return this.updateEvent(component, id, { id_estado_calendario_fk: estadoCanceladoId });
    },

    async deleteEvent(component, id) {
      try {
        await fetchJson(`/api/calendario/${id}`, {
          method: "DELETE",
          headers: { Accept: "application/json" },
          credentials: "same-origin",
        });
        window.showToast && window.showToast("Evento eliminado", "success");
        await this.fetchMonth(component);
      } catch (e) {
        window.showToast && window.showToast("No se pudo eliminar el evento", "error");
      }
    },
  };
})();
