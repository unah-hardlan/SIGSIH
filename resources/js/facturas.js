document.addEventListener('alpine:init', () => {
    Alpine.data('facturasCrud', () => ({
        // Tab state
        tab: 'facturas',
        
        // Modal states para facturas
        isFacturaModalOpen: false,
        isEditFacturaModalOpen: false,
        isDeleteFacturaModalOpen: false,
        itemToEdit: null,
        itemToDelete: null,
        
    // Data arrays
    facturas: [],
    detalles: [],
    estadosFactura: [],
    clientes: [],
    cais: [],
        
    // Loading states
    loadingFacturas: false,
    loadingDetalles: false,
    loadingEstadosFactura: false,
        
        // Form fields para crear factura
        numero: '',
        fecha: '',
        oc: '',
        subtotal: 0,
        total: 0,
        total_letras: '',
    id_estado_factura_fk: '',
    id_cai_fk: '',
    id_cliente_fk: '',

    // Detalle modal states y modelo
    isDetalleModalOpen: false,
    isEditDetalleModalOpen: false,
    isDeleteDetalleModalOpen: false,
    detalleToEdit: null,
    detalleToDelete: null,

    // Filtros
    filtroFactura: '',
    ordenarPor: '',
    // Modelos usados por partial filtros-generales
    searchFacturas: '',
    estadoFacturaFiltro: '',
    clienteFacturaFiltro: '',

    // Filtros para detalle
    searchDetalleFactura: '',
    servicioDetalleFiltro: '',
    facturaDetalleFiltro: '',

        async init() {
            await this.fetchFacturas();
            await this.fetchEstadosFactura();
            await this.fetchClientes();
            await this.fetchCais();
        },

        async fetchFacturas() {
            this.loadingFacturas = true;
            try {
                const response = await fetch("/api/facturas", {
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw data;
                
                console.log('Response structure:', data);
                
                // Usar estructura de respuesta igual que CAI
                if (data.success && data.data) {
                    this.facturas = data.data;
                    console.log('Total facturas loaded:', this.facturas.length);
                } else if (Array.isArray(data)) {
                    // Fallback para compatibilidad
                    this.facturas = data;
                    console.log('Fallback - Total facturas loaded:', this.facturas.length);
                } else {
                    console.error('Invalid response structure:', data);
                    this.facturas = [];
                }
                
                if (this.facturas.length > 0) {
                    console.log('Sample factura data:', this.facturas[0]);
                }
            } catch (error) {
                console.error("Error fetching facturas:", error);
                window.showToast &&
                    window.showToast(
                        "Error al cargar facturas",
                        "error"
                    );
            } finally {
                this.loadingFacturas = false;
            }
        },

        async fetchEstadosFactura() {
            this.loadingEstadosFactura = true;
            try {
                // Agregar timestamp único y forzar recarga completa
                const timestamp = Date.now() + Math.random();
                const response = await fetch(`/api/estados-factura?all=true&_t=${timestamp}&_bust=${Math.random()}`, {
                    method: 'GET',
                    headers: { 
                        Accept: "application/json",
                        "Cache-Control": "no-cache, no-store, must-revalidate",
                        "Pragma": "no-cache",
                        "Expires": "0"
                    },
                    credentials: "same-origin",
                    cache: 'no-store' // Forzar no usar caché
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw data;
                
                console.log('Estados fetched (fresh):', data);
                console.log('Response headers:', [...response.headers.entries()]);
                
                this.estadosFactura = Array.isArray(data?.data)
                    ? data.data
                    : Array.isArray(data)
                    ? data
                    : [];
                    
                console.log('Estados processed (should be fresh):', this.estadosFactura);
            } catch (error) {
                console.error("Error fetching estados factura:", error);
            } finally {
                this.loadingEstadosFactura = false;
            }
        },

        async fetchClientes() {
            try {
                // Usar el mismo endpoint que gestión de solicitudes para catálogo unificado
                const params = new URLSearchParams();
                params.set('per_page', '500');
                params.set('all', '1');

                const res = await fetch('/api/clientes?' + params.toString(), {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin'
                });

                if (!res.ok) throw new Error('Error fetching clientes');

                const payload = await res.json();
                const raw = Array.isArray(payload?.data)
                    ? payload.data
                    : Array.isArray(payload?.data?.data)
                    ? payload.data.data
                    : Array.isArray(payload)
                    ? payload
                    : [];

                // Mapear igual que en gestionSolicitudes: id y nombre legible
                this.clientes = (raw || []).map((c) => {
                    let nombre = '';
                    if ((c.tipo || c.tipo_cliente) === 'empresa') {
                        nombre = c.nombre || c.nombre_comercial || c.razon_social || `Cliente #${c.id_cliente_fk || c.id}`;
                    } else {
                        // persona
                        const persona = Array.isArray(c.persona) ? c.persona[0] : c.persona || {};
                        nombre = [persona.primer_nombre, persona.segundo_nombre, persona.primer_apellido, persona.segundo_apellido]
                            .filter(Boolean)
                            .join(' ')
                            .trim();
                        if (!nombre) nombre = c.nombre || `Cliente #${c.id_cliente_fk || c.id}`;
                    }
                    return { id: c.id_cliente_fk || c.id, nombre };
                });
            } catch (error) {
                console.error('Error fetching clientes:', error);
                this.clientes = [];
            }
        },

        async fetchCais() {
            try {
                const response = await fetch("/api/cai", {
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw data;
                
                this.cais = Array.isArray(data?.data)
                    ? data.data
                    : Array.isArray(data)
                    ? data
                    : [];
            } catch (error) {
                console.error("Error fetching CAIs:", error);
            }
        },

        async submitFactura() {
            console.log('submitFactura called');
            // Leer valores directamente desde los campos del formulario
            const numeroTrim = String(document.getElementById('numero_factura')?.value || "").trim();
            const fechaTrim = String(document.getElementById('fecha_factura')?.value || "").trim();
            const ocTrim = String(document.getElementById('oc_factura')?.value || "").trim();
            const subtotalValue = document.getElementById('subtotal_factura')?.value;
            const totalValue = document.getElementById('total_factura')?.value;
            const subtotal = subtotalValue ? parseFloat(subtotalValue) : null;
            const total = totalValue ? parseFloat(totalValue) : null;
            const totalLetrasTrim = String(document.getElementById('total_letras_factura')?.value || "").trim();
            const estadoFacturaId = parseInt(document.getElementById('estado_factura_id')?.value) || null;
            const caiId = parseInt(document.getElementById('cai_factura')?.value) || null;
            const clienteId = parseInt(document.getElementById('cliente_id')?.value) || null;

            console.log('Form data:', {
                numeroTrim, fechaTrim, ocTrim, subtotal, total, totalLetrasTrim, 
                estadoFacturaId, caiId, clienteId
            });
            
            console.log('Estado field element:', document.getElementById('estado_factura_id'));
            console.log('Estado field value:', document.getElementById('estado_factura_id')?.value);
            console.log('Estado parsed:', estadoFacturaId);

            if (!numeroTrim) {
                window.showToast &&
                    window.showToast(
                        "El número de factura es obligatorio",
                        "error"
                    );
                return;
            }

            if (!fechaTrim) {
                window.showToast &&
                    window.showToast(
                        "La fecha es obligatoria",
                        "error"
                    );
                return;
            }

            if (subtotal === null || subtotal < 0) {
                window.showToast &&
                    window.showToast(
                        "El subtotal es obligatorio y debe ser mayor o igual a 0",
                        "error"
                    );
                return;
            }

            if (total === null || total < 0) {
                window.showToast &&
                    window.showToast(
                        "El total es obligatorio y debe ser mayor o igual a 0",
                        "error"
                    );
                return;
            }

            try {
                const payload = {
                    numero: numeroTrim,
                    fecha: fechaTrim,
                    oc: ocTrim,
                    subtotal: subtotal,
                    total: total,
                    total_letras: totalLetrasTrim,
                    id_estado_factura_fk: estadoFacturaId,
                    id_cai_fk: caiId,
                    id_cliente_fk: clienteId,
                };

                console.log('Payload to send:', payload);

                const response = await fetch("/api/facturas", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    credentials: "same-origin",
                    body: JSON.stringify(payload),
                });

                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);
                
                const data = await response.json().catch((err) => {
                    console.error('Error parsing JSON:', err);
                    return {};
                });
                
                console.log('Response data:', data);
                console.log('Response data.data:', data?.data);
                console.log('Response data.success:', data?.success);
                
                if (!response.ok) {
                    if (data && data.errors) {
                        Object.values(data.errors).forEach((errArr) => {
                            if (Array.isArray(errArr)) {
                                errArr.forEach((msg) => {
                                    window.showToast &&
                                        window.showToast(msg, "error");
                                });
                            }
                        });
                    } else {
                        window.showToast &&
                            window.showToast(
                                data.message || "Error al crear la factura",
                                "error"
                            );
                    }
                    throw data;
                }

                if (data.success) {
                    window.showToast &&
                        window.showToast(
                            data.message || "Factura creada exitosamente",
                            "success"
                        );
                    this.isFacturaModalOpen = false;
                    this.clearForm();
                    await this.fetchFacturas();
                } else {
                    window.showToast &&
                        window.showToast(
                            data.message || "Error inesperado al crear la factura",
                            "error"
                        );
                }
            } catch (error) {
                console.error("Error creating factura:", error);
            }
        },

        async updateFactura() {
            if (!this.itemToEdit || (!this.itemToEdit.id && !this.itemToEdit.id_factura_pk))
                return;

            // Leer valores directamente desde los campos del formulario
            const numeroTrim = String(document.getElementById('edit_numero_factura')?.value || "").trim();
            const fechaTrim = String(document.getElementById('edit_fecha_factura')?.value || "").trim();
            const ocTrim = String(document.getElementById('edit_oc_factura')?.value || "").trim();
            const subtotal = parseFloat(document.getElementById('edit_subtotal_factura')?.value) || 0;
            const total = parseFloat(document.getElementById('edit_total_factura')?.value) || 0;
            const totalLetrasTrim = String(document.getElementById('edit_total_letras_factura')?.value || "").trim();
            const estadoFacturaId = parseInt(document.getElementById('edit_estado_factura_id')?.value) || null;
            const caiId = parseInt(document.getElementById('edit_cai_factura')?.value) || null;
            const clienteId = parseInt(document.getElementById('edit_cliente_id')?.value) || null;

            if (!numeroTrim) {
                window.showToast &&
                    window.showToast(
                        "El número de factura es obligatorio",
                        "error"
                    );
                return;
            }

            if (!fechaTrim) {
                window.showToast &&
                    window.showToast(
                        "La fecha es obligatoria",
                        "error"
                    );
                return;
            }

            try {
                const facturaId = this.itemToEdit.id_factura_pk || this.itemToEdit.id;
                const payload = {
                    numero: numeroTrim,
                    fecha: fechaTrim,
                    oc: ocTrim,
                    subtotal: subtotal,
                    total: total,
                    total_letras: totalLetrasTrim,
                    id_estado_factura_fk: estadoFacturaId,
                    id_cai_fk: caiId,
                    id_cliente_fk: clienteId,
                };

                const response = await fetch(`/api/facturas/${facturaId}`, {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    credentials: "same-origin",
                    body: JSON.stringify(payload),
                });

                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    if (data && data.errors) {
                        Object.values(data.errors).forEach((errArr) => {
                            if (Array.isArray(errArr)) {
                                errArr.forEach((msg) => {
                                    window.showToast &&
                                        window.showToast(msg, "error");
                                });
                            }
                        });
                    } else {
                        window.showToast &&
                            window.showToast(
                                "Error al actualizar la factura",
                                "error"
                            );
                    }
                    throw data;
                }

                window.showToast &&
                    window.showToast(
                        "Factura actualizada exitosamente",
                        "success"
                    );
                this.isEditFacturaModalOpen = false;
                this.itemToEdit = null;
                await this.fetchFacturas();
            } catch (error) {
                console.error("Error updating factura:", error);
            }
        },

        async deleteFactura() {
            if (!this.itemToDelete || (!this.itemToDelete.id && !this.itemToDelete.id_factura_pk))
                return;

            try {
                const facturaId = this.itemToDelete.id_factura_pk || this.itemToDelete.id;
                const response = await fetch(`/api/facturas/${facturaId}`, {
                    method: "DELETE",
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                });

                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw data;

                window.showToast &&
                    window.showToast(
                        "Factura eliminada exitosamente",
                        "success"
                    );
                this.isDeleteFacturaModalOpen = false;
                this.itemToDelete = null;
                await this.fetchFacturas();
            } catch (error) {
                console.error("Error deleting factura:", error);
                const errorMessage =
                    error?.error || "Error al eliminar la factura";
                window.showToast && window.showToast(errorMessage, "error");
            }
        },

        clearForm() {
            this.numero = '';
            this.fecha = '';
            this.oc = '';
            this.subtotal = 0;
            this.total = 0;
            this.total_letras = '';
            this.id_estado_factura_fk = '';
            this.id_cai_fk = '';
            this.id_cliente_fk = '';
        },

        handleModalSubmit(event) {
            console.log('Modal submit triggered:', event.detail);
            if(event.detail.formId === 'formFactura') {
                console.log('Calling submitFactura');
                this.submitFactura();
            }
            if(event.detail.formId === 'formEditFactura') {
                console.log('Calling updateFactura');
                this.updateFactura();
            }
        },

        handleDelete() {
            if (this.isDeleteFacturaModalOpen) {
                this.deleteFactura();
            }
        }
    }));
});

// Event listeners para manejar envíos de modales
window.addEventListener('modal-submit', function(event) {
    try {
        const el = document.querySelector('[x-data*="facturasCrud"]');
        const facturasCrudComponent = el ? Alpine.$data(el) : null;
        if (facturasCrudComponent && facturasCrudComponent.handleModalSubmit) {
            facturasCrudComponent.handleModalSubmit(event);
        }
    } catch (_) { /* ignore if component not present */ }
});

window.addEventListener('confirm-delete', function(event) {
    try {
        const el = document.querySelector('[x-data*="facturasCrud"]');
        const facturasCrudComponent = el ? Alpine.$data(el) : null;
        if (facturasCrudComponent && facturasCrudComponent.handleDelete) {
            facturasCrudComponent.handleDelete();
        }
    } catch (_) { /* ignore if component not present */ }
});