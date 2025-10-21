document.addEventListener('alpine:init', () => {
    Alpine.data('detalleFacturaCrud', () => ({
        // Modal states
        isDetalleModalOpen: false,
        isEditDetalleModalOpen: false,
        isDeleteDetalleModalOpen: false,
        detalleToEdit: null,
        detalleToDelete: null,
        
        // Data arrays
        detallesFactura: [],
        servicios: [],
        facturas: [],
        
        // Loading states
        loadingDetalles: false,
        loadingServicios: false,
        loadingFacturas: false,
        
        // Form fields
        id_factura_fk: '',
        id_servicio_fk: '',
        descripcion: '',
        precio_unitario: 0,
        cantidad: 0,
        impuesto: 0,
        descuento: 0,
        fecha_servicio: '',
        horas: 0,
        
        // Filtros
        filtroDetalle: '',
        ordenarPor: '',

        async init() {
            await this.fetchFacturas();
            await this.fetchServicios();
            // Cargar detalles solo de la primera factura si existe
            if (this.facturas.length > 0) {
                const firstFacturaId = this.facturas[0].id || this.facturas[0].id_factura_pk;
                await this.fetchDetallesFactura(firstFacturaId);
            }
        },

        async fetchDetallesFactura() {
            this.loadingDetalles = true;
            try {
                let url = '/api/detalles-factura?';
                if (arguments.length > 0 && arguments[0]) {
                    url += `factura=${arguments[0]}&`;
                }
                const timestamp = Date.now() + Math.random();
                url += `all=true&_t=${timestamp}`;
                const response = await fetch(url, {
                    method: 'GET',
                    headers: { 
                        Accept: "application/json",
                        "Cache-Control": "no-cache, no-store, must-revalidate",
                    },
                    credentials: "same-origin",
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw data;
                this.detallesFactura = Array.isArray(data?.data)
                    ? data.data
                    : Array.isArray(data)
                    ? data
                    : [];
                console.log('Detalles loaded:', this.detallesFactura);
            } catch (error) {
                console.error("Error fetching detalles factura:", error);
                this.detallesFactura = [];
            } finally {
                this.loadingDetalles = false;
            }
        },

        async fetchServicios() {
            this.loadingServicios = true;
            try {
                const response = await fetch("/api/servicios", {
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw data;
                
                this.servicios = Array.isArray(data?.data)
                    ? data.data
                    : Array.isArray(data)
                    ? data
                    : [];
            } catch (error) {
                console.error("Error fetching servicios:", error);
            } finally {
                this.loadingServicios = false;
            }
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
                
                this.facturas = Array.isArray(data?.data)
                    ? data.data
                    : Array.isArray(data)
                    ? data
                    : [];
            } catch (error) {
                console.error("Error fetching facturas:", error);
            } finally {
                this.loadingFacturas = false;
            }
        },

        async submitDetalle() {
            const idFactura = parseInt(document.getElementById('id_factura_fk')?.value);
            const idServicio = parseInt(document.getElementById('id_servicio_fk')?.value);
            const descripcion = document.getElementById('descripcion')?.value;
            const precioUnitario = parseFloat(document.getElementById('precio_unitario')?.value);
            const cantidad = parseFloat(document.getElementById('cantidad')?.value);

            if (!idFactura || !idServicio || !descripcion || !precioUnitario || !cantidad) {
                window.showToast && window.showToast("Todos los campos son obligatorios", "error");
                return;
            }

            try {
                const payload = {
                    id_factura_fk: idFactura,
                    id_servicio_fk: idServicio,
                    descripcion: descripcion,
                    precio_unitario: precioUnitario,
                    cantidad: cantidad,
                    impuesto: parseFloat(document.getElementById('impuesto')?.value || 0),
                    descuento: parseFloat(document.getElementById('descuento')?.value || 0),
                    fecha_servicio: document.getElementById('fecha_servicio')?.value,
                    horas: parseFloat(document.getElementById('horas')?.value || 0)
                };

                const response = await fetch("/api/detalles-factura", {
                    method: "POST",
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
                                    window.showToast && window.showToast(msg, "error");
                                });
                            }
                        });
                    } else {
                        window.showToast && window.showToast("Error al crear el detalle", "error");
                    }
                    throw data;
                }

                window.showToast && window.showToast("Detalle creado exitosamente", "success");
                this.isDetalleModalOpen = false;
                this.clearForm();
                await this.fetchDetallesFactura();
            } catch (error) {
                console.error("Error creating detalle:", error);
            }
        },

        async updateDetalle() {
            if (!this.detalleToEdit || (!this.detalleToEdit.id && !this.detalleToEdit.id_detalle_pk))
                return;

            const idFactura = parseInt(document.getElementById('edit_id_factura_fk')?.value);
            const idServicio = parseInt(document.getElementById('edit_id_servicio_fk')?.value);
            const descripcion = document.getElementById('edit_descripcion')?.value;
            const precioUnitario = parseFloat(document.getElementById('edit_precio_unitario')?.value);
            const cantidad = parseFloat(document.getElementById('edit_cantidad')?.value);

            try {
                const detalleId = this.detalleToEdit.id_detalle_pk || this.detalleToEdit.id;
                const payload = {
                    id_factura_fk: idFactura,
                    id_servicio_fk: idServicio,
                    descripcion: descripcion,
                    precio_unitario: precioUnitario,
                    cantidad: cantidad,
                    impuesto: parseFloat(document.getElementById('edit_impuesto')?.value || 0),
                    descuento: parseFloat(document.getElementById('edit_descuento')?.value || 0),
                    fecha_servicio: document.getElementById('edit_fecha_servicio')?.value,
                    horas: parseFloat(document.getElementById('edit_horas')?.value || 0)
                };

                const response = await fetch(`/api/detalles-factura/${detalleId}`, {
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
                                    window.showToast && window.showToast(msg, "error");
                                });
                            }
                        });
                    } else {
                        window.showToast && window.showToast("Error al actualizar el detalle", "error");
                    }
                    throw data;
                }

                window.showToast && window.showToast("Detalle actualizado exitosamente", "success");
                this.isEditDetalleModalOpen = false;
                this.detalleToEdit = null;
                await this.fetchDetallesFactura();
            } catch (error) {
                console.error("Error updating detalle:", error);
            }
        },

        async deleteDetalle() {
            if (!this.detalleToDelete || (!this.detalleToDelete.id && !this.detalleToDelete.id_detalle_pk))
                return;

            try {
                const detalleId = this.detalleToDelete.id_detalle_pk || this.detalleToDelete.id;
                const response = await fetch(`/api/detalles-factura/${detalleId}`, {
                    method: "DELETE",
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                });

                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw data;

                window.showToast && window.showToast("Detalle eliminado exitosamente", "success");
                this.isDeleteDetalleModalOpen = false;
                this.detalleToDelete = null;
                await this.fetchDetallesFactura();
            } catch (error) {
                console.error("Error deleting detalle:", error);
                const errorMessage = error?.error || "Error al eliminar el detalle";
                window.showToast && window.showToast(errorMessage, "error");
            }
        },

        clearForm() {
            this.id_factura_fk = '';
            this.id_servicio_fk = '';
            this.descripcion = '';
            this.precio_unitario = 0;
            this.cantidad = 0;
            this.impuesto = 0;
            this.descuento = 0;
            this.fecha_servicio = '';
            this.horas = 0;
        },

        // Funciones para abrir modales (llamadas desde la vista)
        openCreateDetalleModal() {
            this.clearForm();
            this.isDetalleModalOpen = true;
        },

        openEditDetalleModal(detalle) {
            this.detalleToEdit = detalle;
            // Llenar el formulario con los datos del detalle
            this.id_factura_fk = detalle.id_factura_fk || '';
            this.id_servicio_fk = detalle.id_servicio_fk || '';
            this.descripcion = detalle.descripcion || '';
            this.precio_unitario = detalle.precio_unitario || 0;
            this.cantidad = detalle.cantidad || 0;
            this.impuesto = detalle.impuesto || 0;
            this.descuento = detalle.descuento || 0;
            this.fecha_servicio = detalle.fecha_servicio || '';
            this.horas = detalle.horas || 0;
            this.isEditDetalleModalOpen = true;
        },

        openDeleteDetalleModal(detalle) {
            this.detalleToDelete = detalle;
            this.isDeleteDetalleModalOpen = true;
        },

        handleModalSubmit(event) {
            console.log('Detalle Modal submit triggered:', event.detail);
            if(event.detail.formId === 'formDetalle') {
                console.log('Calling submitDetalle');
                this.submitDetalle();
            }
            if(event.detail.formId === 'formEditDetalle') {
                console.log('Calling updateDetalle');
                this.updateDetalle();
            }
        },

        handleDelete() {
            if (this.isDeleteDetalleModalOpen) {
                this.deleteDetalle();
            }
        }
    }));
});

