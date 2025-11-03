document.addEventListener("alpine:init", () => {
    Alpine.data("facturasCrud", () => ({
        // Tab state
        tab: "facturas",
        // Backend validation errors per form
        errors: {},
        errorsEdit: {},
        formError: "",
        formErrorEdit: "",
        // Al cambiar a la pestaña 'detalle', cargar todos los detalles (sin filtrar por defecto)
        async setTab(tabName) {
            this.tab = tabName;
            if (tabName === "detalle") {
                await this.fetchDetallesFactura();
            }
        },

        async fetchServicios() {
            try {
                // Try to fetch all servicios and avoid paginated/cached responses
                const ts = Date.now() + Math.random();
                const url = `/api/servicios?all=true&_t=${ts}`;
                console.log("Fetching servicios from", url);
                const response = await fetch(url, {
                    method: "GET",
                    headers: {
                        Accept: "application/json",
                        "Cache-Control": "no-cache, no-store, must-revalidate",
                        Pragma: "no-cache",
                        Expires: "0",
                    },
                    credentials: "same-origin",
                    cache: "no-store",
                });

                console.log(
                    "Servicios response status:",
                    response.status,
                    response.statusText
                );
                const data = await response.json().catch((err) => {
                    console.warn("Could not parse servicios JSON:", err);
                    return {};
                });

                console.log("Servicios response payload:", data);

                if (!response.ok) {
                    // Log and fallback to empty list
                    console.error(
                        "Servicios fetch returned non-ok status",
                        response.status,
                        data
                    );
                    this.servicios = [];
                    return;
                }

                // Accept multiple shapes: { data: [...] } or plain array
                this.servicios = Array.isArray(data?.data)
                    ? data.data
                    : Array.isArray(data)
                    ? data
                    : [];

                console.log(
                    "Servicios processed count:",
                    this.servicios.length
                );
            } catch (error) {
                console.error("Error fetching servicios:", error);
                this.servicios = [];
            }
        },

        // Modal states para facturas
        isFacturaModalOpen: false,
        isEditFacturaModalOpen: false,
        isDeleteFacturaModalOpen: false,
        // Alias para compatibilidad con componentes globales
        isEditModalOpen: false,
        isEditListModalOpen: false,
        // initialize as object to avoid reactive "cannot read property" errors in templates
        itemToEdit: {},
        itemToDelete: null,

        // Data arrays
        facturas: [],
        detalles: [],
        servicios: [],
        estadosFactura: [],
        clientes: [],
        cais: [],

        // Paginación facturas
        numbers: [], // alias esperado por el componente de paginación
        currentPage: 1,
        perPage: 10,

        // Paginación detalles
        numbersDetalles: [], // alias esperado por el componente de paginación para detalles
        currentPageDetalles: 1,
        perPageDetalles: 5,

        // Computed: facturas filtradas
        get filteredFacturas() {
            let result = this.facturas;
            // Filtro búsqueda
            if (this.searchFacturas && this.searchFacturas.trim() !== "") {
                const q = this.searchFacturas.trim().toLowerCase();
                result = result.filter((f) => {
                    return (
                        (f.numero && f.numero.toLowerCase().includes(q)) ||
                        (f.cliente_nombre &&
                            f.cliente_nombre.toLowerCase().includes(q)) ||
                        (f.cai && String(f.cai).toLowerCase().includes(q)) ||
                        (f.total_letras &&
                            f.total_letras.toLowerCase().includes(q)) ||
                        (f.oc && String(f.oc).toLowerCase().includes(q)) ||
                        (f.estado_factura &&
                            f.estado_factura.toLowerCase().includes(q)) ||
                        (f.total && String(f.total).toLowerCase().includes(q))
                    );
                });
            }
            // Filtro estado (acepta nombre o id; comparación case-insensitive para nombres)
            if (this.estadoFacturaFiltro && this.estadoFacturaFiltro !== "") {
                const filtro = String(this.estadoFacturaFiltro).toLowerCase();
                result = result.filter((f) => {
                    try {
                        const nombre = (f.estado_factura || "")
                            .toString()
                            .toLowerCase();
                        const fk = (
                            f.id_estado_factura_fk ||
                            f.id_estado_factura ||
                            f.estado_factura_id ||
                            ""
                        ).toString();
                        return nombre === filtro || fk === filtro;
                    } catch (e) {
                        return false;
                    }
                });
            }
            // Filtro cliente
            if (this.clienteFacturaFiltro && this.clienteFacturaFiltro !== "") {
                result = result.filter(
                    (f) => f.cliente_nombre === this.clienteFacturaFiltro
                );
            }
            // Ordenamiento
            if (this.ordenarPor && this.ordenarPor !== "") {
                result = [...result].sort((a, b) => {
                    if (this.ordenarPor === "fecha") {
                        return String(a.fecha).localeCompare(String(b.fecha));
                    }
                    if (this.ordenarPor === "total") {
                        return (
                            (parseFloat(a.total) || 0) -
                            (parseFloat(b.total) || 0)
                        );
                    }
                    if (this.ordenarPor === "estado_factura") {
                        return String(a.estado_factura).localeCompare(
                            String(b.estado_factura)
                        );
                    }
                    return 0;
                });
            }
            return result;
        },

        // Loading states
        loadingFacturas: false,
        loadingDetalles: false,
        loadingEstadosFactura: false,

        // Form fields para crear factura
        numero: "",
        fecha: "",
        oc: "",
        subtotal: 0,
        total: 0,
        total_letras: "",
        id_estado_factura_fk: "",
        id_cai_fk: "",
        id_cliente_fk: "",

        // Detalle modal states y modelo
        isDetalleModalOpen: false,
        isEditDetalleModalOpen: false,
        isDeleteDetalleModalOpen: false,
        // initialize as empty objects so bindings like detalleToEdit.id_factura don't throw
        detalleToEdit: {},
        detalleToDelete: {},

        // Filtros
        filtroFactura: "",
        ordenarPor: "",
        // Modelos usados por partial filtros-generales
        searchFacturas: "",
        estadoFacturaFiltro: "",
        clienteFacturaFiltro: "",

        // Filtros para detalle
        searchDetalleFactura: "",
        servicioDetalleFiltro: "",
        facturaDetalleFiltro: "",
        // Si se abrió la vista Detalle filtrada por una factura, guardar su id
        currentFacturaFilter: null,

        async init() {
            await this.fetchFacturas();
            await this.fetchEstadosFactura();
            await this.fetchClientes();
            await this.fetchCais();
            // Cargar servicios para los selectores de detalle
            await this.fetchServicios();

            // Cargar todos los detalles una sola vez y recalcular totales por factura
            // Esto asegura que al entrar a la vista principal los subtotales y totales
            // ya estén reflejados sin necesidad de abrir la pestaña de Detalle.
            try {
                await this.fetchAllDetallesAndRecompute();
            } catch (e) {
                console.warn("fetchAllDetallesAndRecompute failed on init", e);
            }

            // Exponer helper simple para conversión de número a letras (útil en HTML inline)
            try {
                window.numberToSpanishWords = (val) => {
                    try {
                        return this.totalToLetras2(val);
                    } catch (e) {
                        return "";
                    }
                };
            } catch (e) {
                /* ignore if not possible */
            }

            // Watch modal open/close to clear create forms when they close
            try {
                // Clear Nueva Factura modal when it closes
                this.$watch &&
                    this.$watch("isFacturaModalOpen", (val) => {
                        if (!val) {
                            try {
                                this.clearForm();
                                this.errors = {};
                                this.formError = "";
                                // clear DOM inputs too
                                [
                                    "fecha_factura",
                                    "oc_factura",
                                    "impuesto_factura",
                                    "estado_factura_id",
                                    "cai_factura",
                                    "cliente_id",
                                ].forEach((id) => {
                                    const el = document.getElementById(id);
                                    if (el) el.value = "";
                                });
                            } catch (e) {
                                /* ignore */
                            }
                        }
                    });

                // Clear Nuevo Detalle modal when it closes
                this.$watch &&
                    this.$watch("isDetalleModalOpen", (val) => {
                        if (!val) {
                            try {
                                this.clearDetalleForm();
                                [
                                    "id_factura_fk",
                                    "id_servicio_fk",
                                    "descripcion",
                                    "precio_unitario",
                                    "cantidad",
                                    "impuesto",
                                    "fecha_servicio",
                                    "horas",
                                    "descuento",
                                ].forEach((id) => {
                                    const el = document.getElementById(id);
                                    if (el) el.value = "";
                                });
                            } catch (e) {
                                /* ignore */
                            }
                        }
                    });

                // Clear Editar Factura errors when closing
                this.$watch &&
                    this.$watch("isEditFacturaModalOpen", (val) => {
                        if (!val) {
                            this.errorsEdit = {};
                            this.formErrorEdit = "";
                        }
                    });

                // Watchers para paginación - resetear página cuando cambien filtros
                this.$watch &&
                    this.$watch("searchFacturas", () => {
                        this.currentPage = 1;
                    });

                this.$watch &&
                    this.$watch("estadoFacturaFiltro", () => {
                        this.currentPage = 1;
                    });

                this.$watch &&
                    this.$watch("clienteFacturaFiltro", () => {
                        this.currentPage = 1;
                    });

                this.$watch &&
                    this.$watch("ordenarPor", () => {
                        this.currentPage = 1;
                    });
            } catch (e) {
                /* ignore */
            }

            // If the UI was restored with the Detalle tab active (persisted), ensure detalles are loaded
            // This handles the case where the user lands directly on the Detalle tab after login
            if (this.tab === "detalle") {
                await this.fetchDetallesFactura();
            }
        },

        // Fetch all detalles once and compute subtotal/total per factura so the list shows correct values on first render
        async fetchAllDetallesAndRecompute() {
            try {
                const ts = Date.now() + Math.random();
                const url = `/api/detalles-factura?all=true&_t=${ts}`;
                const response = await fetch(url, {
                    method: "GET",
                    headers: {
                        Accept: "application/json",
                        "Cache-Control": "no-cache, no-store, must-revalidate",
                        Pragma: "no-cache",
                        Expires: "0",
                    },
                    credentials: "same-origin",
                    cache: "no-store",
                });

                const payload = await response.json().catch(() => ({}));
                if (!response.ok) {
                    console.warn(
                        "fetchAllDetallesAndRecompute: non-ok response",
                        payload
                    );
                    return;
                }

                const allDetalles = Array.isArray(payload?.data)
                    ? payload.data
                    : Array.isArray(payload)
                    ? payload
                    : [];

                if (!Array.isArray(allDetalles) || allDetalles.length === 0) {
                    // nothing to do
                    return;
                }

                // Group totals by factura id
                const totalsMap = {};
                allDetalles.forEach((d) => {
                    const fid =
                        d.id_factura_fk ||
                        d.id_factura ||
                        d.id_factura_pk ||
                        d.factura_id ||
                        null;
                    if (!fid) return;
                    // Calculate line total
                    let line = 0;
                    if (d.total_linea !== undefined && d.total_linea !== null) {
                        line = parseFloat(d.total_linea) || 0;
                    } else {
                        const pu = parseFloat(d.precio_unitario) || 0;
                        const qty = parseFloat(d.cantidad) || 0;
                        const impuesto = parseFloat(d.impuesto) || 0;
                        const descuento = parseFloat(d.descuento) || 0;
                        line = pu * qty + impuesto - descuento;
                    }
                    totalsMap[fid] = (totalsMap[fid] || 0) + line;
                });

                // Apply computed subtotals/totales to facturas list
                (this.facturas || []).forEach((f) => {
                    const fid = f.id_factura_pk || f.id || f.numero || null;
                    if (!fid) return;
                    const subtotal = Number((totalsMap[fid] || 0).toFixed(2));
                    // Calcular impuesto como 15% del subtotal (política de negocio fija)
                    const impuestoVal = Number((subtotal * 0.15).toFixed(2));
                    const total = Number((subtotal + impuestoVal).toFixed(2));
                    const letras = this.totalToLetras2(total);
                    // assign back to the factura object so bindings update
                    f.subtotal = subtotal;
                    f.impuesto = impuestoVal;
                    f.total = total;
                    f.total_letras = letras;
                });

                // If an itemToEdit is present (modal open), and its factura is in map, update it too
                try {
                    const editFid =
                        this.itemToEdit?.id_factura_pk ||
                        this.itemToEdit?.id ||
                        null;
                    if (editFid && totalsMap[editFid] !== undefined) {
                        const subtotal = Number(
                            (totalsMap[editFid] || 0).toFixed(2)
                        );
                        const impuestoVal =
                            parseFloat(this.itemToEdit.impuesto) || 0;
                        const total = Number(
                            (subtotal + impuestoVal).toFixed(2)
                        );
                        const letras = this.totalToLetras2(total);
                        this.itemToEdit.subtotal = subtotal;
                        this.itemToEdit.total = total;
                        this.itemToEdit.total_letras = letras;
                        // reflect in DOM if open
                        try {
                            document.getElementById("edit_subtotal_factura") &&
                                (document.getElementById(
                                    "edit_subtotal_factura"
                                ).value = subtotal);
                        } catch (e) {}
                        try {
                            document.getElementById("edit_total_factura") &&
                                (document.getElementById(
                                    "edit_total_factura"
                                ).value = total);
                        } catch (e) {}
                        try {
                            document.getElementById(
                                "edit_total_letras_factura"
                            ) &&
                                (document.getElementById(
                                    "edit_total_letras_factura"
                                ).value = letras);
                        } catch (e) {}
                    }
                } catch (e) {
                    /* ignore */
                }
            } catch (error) {
                console.error(
                    "Error fetching all detalles for recompute:",
                    error
                );
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

                console.log("Response structure:", data);

                // Usar estructura de respuesta igual que CAI
                if (data.success && data.data) {
                    this.facturas = data.data;
                    console.log("Total facturas loaded:", this.facturas.length);
                } else if (Array.isArray(data)) {
                    // Fallback para compatibilidad
                    this.facturas = data;
                    console.log(
                        "Fallback - Total facturas loaded:",
                        this.facturas.length
                    );
                } else {
                    console.error("Invalid response structure:", data);
                    this.facturas = [];
                }

                if (this.facturas.length > 0) {
                    console.log("Sample factura data:", this.facturas[0]);
                }

                // Sincronizar alias para paginación
                this.numbers = this.facturas;
            } catch (error) {
                console.error("Error fetching facturas:", error);
                window.showToast &&
                    window.showToast("Error al cargar facturas", "error");
            } finally {
                this.loadingFacturas = false;
            }
        },

        // Métodos de paginación
        paginatedFacturas() {
            const filtered = this.filteredFacturas;
            return filtered.slice(
                (this.currentPage - 1) * this.perPage,
                this.currentPage * this.perPage
            );
        },

        totalPages() {
            return Math.ceil(this.filteredFacturas.length / this.perPage);
        },

        nextPage() {
            if (this.currentPage < this.totalPages()) {
                this.currentPage++;
            }
        },

        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },

        goToPage(page) {
            this.currentPage = page;
        },

        // Métodos de paginación para detalles
        paginatedDetalles() {
            return this.detalles.slice(
                (this.currentPageDetalles - 1) * this.perPageDetalles,
                this.currentPageDetalles * this.perPageDetalles
            );
        },

        totalPagesDetalles() {
            return Math.ceil(this.detalles.length / this.perPageDetalles);
        },

        nextPageDetalles() {
            if (this.currentPageDetalles < this.totalPagesDetalles()) {
                this.currentPageDetalles++;
            }
        },

        prevPageDetalles() {
            if (this.currentPageDetalles > 1) {
                this.currentPageDetalles--;
            }
        },

        goToPageDetalles(page) {
            this.currentPageDetalles = page;
        },

        async fetchEstadosFactura() {
            this.loadingEstadosFactura = true;
            try {
                // Agregar timestamp único y forzar recarga completa
                const timestamp = Date.now() + Math.random();
                const response = await fetch(
                    `/api/estados-factura?all=true&_t=${timestamp}&_bust=${Math.random()}`,
                    {
                        method: "GET",
                        headers: {
                            Accept: "application/json",
                            "Cache-Control":
                                "no-cache, no-store, must-revalidate",
                            Pragma: "no-cache",
                            Expires: "0",
                        },
                        credentials: "same-origin",
                        cache: "no-store", // Forzar no usar caché
                    }
                );
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw data;

                console.log("Estados fetched (fresh):", data);
                console.log("Response headers:", [
                    ...response.headers.entries(),
                ]);

                this.estadosFactura = Array.isArray(data?.data)
                    ? data.data
                    : Array.isArray(data)
                    ? data
                    : [];

                console.log(
                    "Estados processed (should be fresh):",
                    this.estadosFactura
                );
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
                params.set("per_page", "500");
                params.set("all", "1");

                const res = await fetch("/api/clientes?" + params.toString(), {
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                });

                if (!res.ok) throw new Error("Error fetching clientes");

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
                    let nombre = "";
                    if ((c.tipo || c.tipo_cliente) === "empresa") {
                        nombre =
                            c.nombre ||
                            c.nombre_comercial ||
                            c.razon_social ||
                            `Cliente #${c.id_cliente_fk || c.id}`;
                    } else {
                        // persona
                        const persona = Array.isArray(c.persona)
                            ? c.persona[0]
                            : c.persona || {};
                        nombre = [
                            persona.primer_nombre,
                            persona.segundo_nombre,
                            persona.primer_apellido,
                            persona.segundo_apellido,
                        ]
                            .filter(Boolean)
                            .join(" ")
                            .trim();
                        if (!nombre)
                            nombre =
                                c.nombre ||
                                `Cliente #${c.id_cliente_fk || c.id}`;
                    }
                    return { id: c.id_cliente_fk || c.id, nombre };
                });
            } catch (error) {
                window.showToast &&
                    window.showToast("Error al cargar clientes", "error");
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
                const rawList = Array.isArray(data?.data)
                    ? data.data
                    : Array.isArray(data)
                    ? data
                    : [];

                // Enriquecer con flags de estado/uso y vista previa del próximo número
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                this.cais = (rawList || []).map((c) => {
                    // Soportar claves snake_case de Eloquent (estado_cai) además de camelCase
                    const estado =
                        c?.estadoCai || c?.estado_cai || c?.estado || {};
                    const estadoNombre = String(
                        estado?.nombre ||
                            estado?.nombre_estado ||
                            estado?.nombre_estado_cai ||
                            ""
                    ).trim();
                    const estadoCodigo = String(estado?.codigo || "")
                        .trim()
                        .toLowerCase();

                    const fechaStr = c?.fecha_limite || null;
                    let fechaDate = null;
                    let isFechaVencida = false;
                    if (fechaStr) {
                        try {
                            fechaDate = new Date(fechaStr);
                            fechaDate.setHours(0, 0, 0, 0);
                            isFechaVencida = fechaDate < today;
                        } catch (_) {
                            /* ignore parse errors */
                        }
                    }

                    const consecutivo =
                        parseInt(c?.consecutivo_actual ?? 0) || 0;
                    const rangoInicio = parseInt(c?.rango_inicio ?? 0) || 0;
                    const rangoFin = parseInt(c?.rango_fin ?? 0) || 0;
                    const isAgotado = rangoFin > 0 && consecutivo >= rangoFin;
                    const isVencidoPorEstado =
                        /vencid/i.test(estadoNombre) ||
                        ["cai-ven", "ven", "vencido", "ven-cer"].includes(
                            estadoCodigo
                        );
                    const isActivo =
                        /activ/i.test(estadoNombre) ||
                        estadoCodigo === "act" ||
                        estadoCodigo.startsWith("act");
                    const usable = !!(
                        isActivo &&
                        !isFechaVencida &&
                        !isAgotado &&
                        !isVencidoPorEstado
                    );

                    const next = (consecutivo || 0) + 1;
                    const inRange =
                        rangoFin > 0 && next >= rangoInicio && next <= rangoFin;
                    // Prefijo: primeros 3 bloques del CAI
                    let prefixStr = "";
                    try {
                        const parts = String(c?.codigo || "").split("-");
                        prefixStr = [parts[0], parts[1], parts[2]]
                            .filter(Boolean)
                            .join("-");
                    } catch (_) {
                        /* ignore */
                    }
                    // Ya no se usa el número del CAI para el número de factura.
                    // Se mantiene el próximo consecutivo del CAI para información.
                    const nextPreview = null;

                    const extras = [];
                    if (!isActivo) extras.push("INACTIVO");
                    if (isFechaVencida) extras.push("FECHA VENCIDA");
                    if (isVencidoPorEstado) extras.push("VENCIDO");
                    if (isAgotado) extras.push("AGOTADO");
                    const optionLabel = `${c?.codigo || ""}${
                        extras.length ? " — " + extras.join(" · ") : ""
                    }`;

                    return {
                        ...c,
                        _estado_nombre: estadoNombre,
                        _estado_codigo: estadoCodigo,
                        _fecha_vencida: !!isFechaVencida,
                        _agotado: !!isAgotado,
                        _vencido: !!isVencidoPorEstado,
                        _activo: !!isActivo,
                        _usable: !!usable,
                        _next_numero_preview: nextPreview,
                        _next_cai_consecutivo: inRange ? next : null,
                        _option_label: optionLabel,
                    };
                });
            } catch (error) {
                window.showToast &&
                    window.showToast("Error al cargar CAIs", "error");
            }
        },

        async fetchDetallesFactura() {
            this.loadingDetalles = true;
            try {
                // Allow optional factura id as first arg
                let url = "/api/detalles-factura?all=true";
                if (arguments.length > 0 && arguments[0]) {
                    const fid = arguments[0];
                    url = `/api/detalles-factura?factura=${encodeURIComponent(
                        fid
                    )}&all=true`;
                }
                const timestamp = Date.now() + Math.random();
                url += `&_t=${timestamp}`;
                const response = await fetch(url, {
                    method: "GET",
                    headers: {
                        Accept: "application/json",
                        "Cache-Control": "no-cache, no-store, must-revalidate",
                        Pragma: "no-cache",
                        Expires: "0",
                    },
                    credentials: "same-origin",
                    cache: "no-store",
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw data;

                this.detalles = Array.isArray(data?.data)
                    ? data.data
                    : Array.isArray(data)
                    ? data
                    : [];

                console.log("Todos los detalles de factura:", this.detalles);

                // Sincronizar alias para paginación de detalles
                this.numbersDetalles = this.detalles;

                // Recalcular subtotal/total cuando cambian los detalles mostrados
                try {
                    this.recomputeFacturaTotals();
                } catch (e) {
                    console.warn("recomputeFacturaTotals failed", e);
                }
            } catch (error) {
                window.showToast &&
                    window.showToast(
                        "Error al obtener los detalles de factura",
                        "error"
                    );
                this.detalles = [];
                this.numbersDetalles = []; // También limpiar el alias
            } finally {
                this.loadingDetalles = false;
            }
        },

        async openDetalleForFactura(factura) {
            // Set tab then fetch detalles filtered to this factura
            try {
                const fid = factura.id || factura.id_factura_pk;
                this.currentFacturaFilter = fid;
                this.tab = "detalle";
                // small delay to ensure UI tab change renders if needed
                await this.fetchDetallesFactura(fid);
            } catch (e) {
                window.showToast &&
                    window.showToast(
                        "No se pudieron cargar los detalles de la factura",
                        "error"
                    );
            }
        },

        async openEditFactura(factura) {
            try {
                // Crear una copia de la factura y formatear la fecha para el input date
                this.itemToEdit = { ...factura };

                // Formatear fecha: convertir "2025-10-07 00:00:00" a "2025-10-07"
                if (this.itemToEdit.fecha) {
                    // Extraer solo la parte de la fecha (YYYY-MM-DD) sin la hora
                    this.itemToEdit.fecha = this.itemToEdit.fecha.split(" ")[0];
                }

                const fid = factura.id || factura.id_factura_pk;
                if (fid) {
                    await this.fetchDetallesFactura(fid);
                }
                // ensure totals reflect the detalles
                try {
                    this.recomputeFacturaTotals();
                } catch (e) {}
                this.isEditFacturaModalOpen = true;
            } catch (e) {
                console.error("Error opening edit factura modal", e);
                // fallback: open modal anyway
                this.itemToEdit = { ...factura };
                // Formatear fecha en el fallback también
                if (this.itemToEdit.fecha) {
                    this.itemToEdit.fecha = this.itemToEdit.fecha.split(" ")[0];
                }
                this.isEditFacturaModalOpen = true;
            }
        },

        async submitFactura() {
            console.log("submitFactura called");
            // reset previous errors
            this.errors = {};
            this.formError = "";

            const fechaTrim = String(
                document.getElementById("fecha_factura")?.value || ""
            ).trim();
            // For creation, subtotal/total/total_letras are sent automatically as zeros.
            const subtotal = 0;
            const total = 0;
            // Send total_letras as textual 'cero' form so DB non-null constraint is satisfied
            const totalLetrasTrim = this.totalToLetras2(0);
            const ocTrim = String(
                document.getElementById("oc_factura")?.value || ""
            ).trim();
            const estadoFacturaId =
                parseInt(document.getElementById("estado_factura_id")?.value) ||
                null;
            const caiId =
                parseInt(document.getElementById("cai_factura")?.value) || null;
            const clienteId =
                parseInt(document.getElementById("cliente_id")?.value) || null;

            console.log("Form data:", {
                fecha: fechaTrim,
                subtotal,
                total,
                total_letras: totalLetrasTrim,
                estadoFacturaId,
                caiId,
                clienteId,
            });

            console.log(
                "Estado field element:",
                document.getElementById("estado_factura_id")
            );
            console.log(
                "Estado field value:",
                document.getElementById("estado_factura_id")?.value
            );
            console.log("Estado parsed:", estadoFacturaId);

            if (!fechaTrim) {
                window.showToast &&
                    window.showToast("La fecha es obligatoria", "error");
                return;
            }

            // Note: subtotal/total/total_letras are not required on creation; backend will compute authoritative values.

            try {
                // Validar que el CAI seleccionado esté utilizable en cliente (el backend también valida)
                try {
                    const selectedCai = (this.cais || []).find(
                        (c) => (c.id || c.id_cai_pk) == caiId
                    );
                    if (selectedCai && selectedCai._usable === false) {
                        const reason = selectedCai._fecha_vencida
                            ? "fecha vencida"
                            : selectedCai._vencido
                            ? "estado vencido"
                            : "rango agotado";
                        window.showToast &&
                            window.showToast(
                                `El CAI seleccionado no se puede usar (${reason}).`,
                                "error"
                            );
                        return;
                    }
                } catch (_) {
                    /* ignore */
                }

                const payload = {
                    // Numero se genera en el servidor con base en el CAI
                    fecha: fechaTrim,
                    oc: ocTrim || null,
                    subtotal: subtotal,
                    // El impuesto se calcula en el backend; enviar 0 para cumplir validación
                    impuesto: 0,
                    total: total,
                    total_letras: totalLetrasTrim,
                    id_estado_factura_fk: estadoFacturaId,
                    id_cai_fk: caiId,
                    id_cliente_fk: clienteId,
                };

                console.log("Payload to send:", payload);

                const response = await fetch("/api/facturas", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    credentials: "same-origin",
                    body: JSON.stringify(payload),
                });

                console.log("Response status:", response.status);
                console.log("Response ok:", response.ok);

                const data = await response.json().catch((err) => {
                    console.error("Error parsing JSON:", err);
                    return {};
                });

                console.log("Response data:", data);
                console.log("Response data.data:", data?.data);
                console.log("Response data.success:", data?.success);

                if (!response.ok) {
                    if (data && data.errors) {
                        // Map field errors to UI and toast
                        this.errors = data.errors || {};
                        try {
                            Object.values(data.errors).forEach((errArr) => {
                                if (Array.isArray(errArr)) {
                                    errArr.forEach((msg) => {
                                        window.showToast &&
                                            window.showToast(msg, "error");
                                    });
                                }
                            });
                        } catch (_) {}
                    } else {
                        // General error (e.g., CAI vencido o sin rango)
                        this.formError =
                            data?.message || "Error al crear la factura";
                        window.showToast &&
                            window.showToast(this.formError, "error");
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
                    this.errors = {};
                    this.formError = "";
                    await this.fetchFacturas();
                } else {
                    window.showToast &&
                        window.showToast(
                            data.message ||
                                "Error inesperado al crear la factura",
                            "error"
                        );
                }
            } catch (error) {
                console.error("Error creating factura:", error);
            }
        },

        async updateFactura() {
            if (
                !this.itemToEdit ||
                (!this.itemToEdit.id && !this.itemToEdit.id_factura_pk)
            )
                return;
            // reset previous errors
            this.errorsEdit = {};
            this.formErrorEdit = "";
            // Leer valores directamente desde los campos del formulario
            // numero y oc no son editables por modal: si no existen, mantener los actuales
            const fechaTrim = String(
                document.getElementById("edit_fecha_factura")?.value || ""
            ).trim();
            const subtotal =
                parseFloat(
                    document.getElementById("edit_subtotal_factura")?.value
                ) || 0;

            const impuestoCalculated = Number((subtotal * 0.15).toFixed(2));
            const total = Number((subtotal + impuestoCalculated).toFixed(2));
            const totalLetrasTrim =
                String(
                    document.getElementById("edit_total_letras_factura")
                        ?.value || ""
                ).trim() || this.totalToLetras2(total);
            const ocEditTrim = String(
                document.getElementById("edit_oc_factura")?.value || ""
            ).trim();
            const estadoFacturaId =
                parseInt(
                    document.getElementById("edit_estado_factura_id")?.value
                ) || null;
            const caiId =
                parseInt(document.getElementById("edit_cai_factura")?.value) ||
                null;
            const clienteId =
                parseInt(document.getElementById("edit_cliente_id")?.value) ||
                null;

            if (!fechaTrim) {
                window.showToast &&
                    window.showToast("La fecha es obligatoria", "error");
                return;
            }

            try {
                const facturaId =
                    this.itemToEdit.id_factura_pk || this.itemToEdit.id;
                const payload = {
                    // Numero no editable; se mantiene en servidor
                    fecha: fechaTrim,
                    oc: ocEditTrim || this.itemToEdit?.oc || null,
                    subtotal: subtotal,
                    impuesto: impuestoCalculated,
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
                        this.errorsEdit = data.errors || {};
                        try {
                            Object.values(data.errors).forEach((errArr) => {
                                if (Array.isArray(errArr)) {
                                    errArr.forEach((msg) => {
                                        window.showToast &&
                                            window.showToast(msg, "error");
                                    });
                                }
                            });
                        } catch (_) {}
                    } else {
                        this.formErrorEdit =
                            data?.message || "Error al actualizar la factura";
                        window.showToast &&
                            window.showToast(this.formErrorEdit, "error");
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
                this.errorsEdit = {};
                this.formErrorEdit = "";
                await this.fetchFacturas();
            } catch (error) {
                console.error("Error updating factura:", error);
            }
        },

        async deleteFactura() {
            if (
                !this.itemToDelete ||
                (!this.itemToDelete.id && !this.itemToDelete.id_factura_pk)
            )
                return;

            try {
                const facturaId =
                    this.itemToDelete.id_factura_pk || this.itemToDelete.id;
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
            this.numero = "";
            this.fecha = "";
            this.oc = "";
            this.subtotal = 0;
            this.total = 0;
            this.total_letras = "";
            this.id_estado_factura_fk = "";
            this.id_cai_fk = "";
            this.id_cliente_fk = "";
        },

        // Helpers: generar número de factura, OC y convertir total a letras (es)
        generateFacturaNumero() {
            // Formato: FYYYYMMDDHHMMSSxxx
            const d = new Date();
            const pad = (n, z = 2) => String(n).padStart(z, "0");
            const s =
                d.getFullYear().toString() +
                pad(d.getMonth() + 1) +
                pad(d.getDate()) +
                pad(d.getHours()) +
                pad(d.getMinutes()) +
                pad(d.getSeconds());
            const r = Math.floor(Math.random() * 900) + 100;
            // Nuevo formato solicitado: FAC-XXX-XXX-XXXXX
            // Usamos dos bloques fijos de 3 dígitos (serie/sucursal) por ahora y una secuencia de 5 dígitos.
            // Para minimizar colisiones en cliente usamos un contador en localStorage que incrementa por cada factura creada desde este navegador.
            // Evitar uso de localStorage: generar parte final a partir de timestamp + aleatorio
            const seriesA = "001"; // serie/establecimiento (ajustable)
            const seriesB = "001"; // punto de emisión (ajustable)
            // Combinar segundos Unix y un número aleatorio para obtener 5 dígitos
            const seconds = Math.floor(Date.now() / 1000);
            const pseudo =
                (seconds + Math.floor(Math.random() * 89999) + 10000) % 100000;
            const seqStr = String(pseudo).padStart(5, "0");
            return `FAC-${seriesA}-${seriesB}-${seqStr}`;
        },

        generateOC() {
            // Simple OC generator: OC + 6 dígitos aleatorios
            const r = Math.floor(Math.random() * 900000) + 100000;
            return "OC" + r;
        },

        // Nueva versión segura de totalToLetras con formato: '... exactos' o '... con X centavo(s)'
        totalToLetras2(value) {
            const n = Math.abs(parseFloat(value) || 0);
            const entero = Math.floor(n);
            const cent = Math.round((n - entero) * 100);

            const unidades = [
                "",
                "uno",
                "dos",
                "tres",
                "cuatro",
                "cinco",
                "seis",
                "siete",
                "ocho",
                "nueve",
                "diez",
                "once",
                "doce",
                "trece",
                "catorce",
                "quince",
                "dieciseis",
                "diecisiete",
                "dieciocho",
                "diecinueve",
            ];
            const decenas = [
                "",
                "",
                "veinte",
                "treinta",
                "cuarenta",
                "cincuenta",
                "sesenta",
                "setenta",
                "ochenta",
                "noventa",
            ];
            const centenas = [
                "",
                "ciento",
                "doscientos",
                "trescientos",
                "cuatrocientos",
                "quinientos",
                "seiscientos",
                "setecientos",
                "ochocientos",
                "novecientos",
            ];

            function numeroMenosDeMil(num) {
                let words = "";
                if (num === 0) return "cero";
                if (num === 100) return "cien";
                if (num >= 100) {
                    const c = Math.floor(num / 100);
                    words += centenas[c] + " ";
                    num = num % 100;
                }
                if (num < 20) {
                    words += unidades[num] || "";
                } else if (num < 30) {
                    if (num === 20) words += "veinte";
                    else words += "veinti" + (unidades[num - 20] || "");
                } else {
                    const d = Math.floor(num / 10);
                    const u = num % 10;
                    words += decenas[d] || "";
                    if (u) words += " y " + unidades[u];
                }
                return words.trim();
            }

            let words = "";
            const millones = Math.floor(entero / 1000000);
            const restoMillones = entero % 1000000;
            const miles = Math.floor(restoMillones / 1000);
            const resto = restoMillones % 1000;

            if (millones > 0) {
                if (millones === 1) words += "un millón ";
                else words += numeroMenosDeMil(millones) + " millones ";
            }
            if (miles > 0) {
                if (miles === 1) words += "mil ";
                else words += numeroMenosDeMil(miles) + " mil ";
            }
            if (resto > 0) {
                words += numeroMenosDeMil(resto) + " ";
            }

            words = words.trim();
            if (!words) words = "cero";

            if (cent === 0) {
                return (words + " exactos").replace(/\buno mil\b/, "un mil");
            }
            const centWord = cent === 1 ? "centavo" : "centavos";
            return (words + " con " + cent + " " + centWord).replace(
                /\buno mil\b/,
                "un mil"
            );
        },

        // Recalcula subtotal/total/total_letras basándose en los detalles actualmente cargados
        recomputeFacturaTotals() {
            try {
                // Sumar total_linea si existe, sino calcular desde precio_unitario * cantidad + impuesto - descuento
                let subtotal = 0;
                if (Array.isArray(this.detalles)) {
                    this.detalles.forEach((d) => {
                        let line = 0;
                        if (
                            d.total_linea !== undefined &&
                            d.total_linea !== null
                        ) {
                            line = parseFloat(d.total_linea) || 0;
                        } else {
                            const pu = parseFloat(d.precio_unitario) || 0;
                            const qty = parseFloat(d.cantidad) || 0;
                            const impuesto = parseFloat(d.impuesto) || 0;
                            const descuento = parseFloat(d.descuento) || 0;
                            line = pu * qty + impuesto - descuento;
                        }
                        subtotal += line;
                    });
                }
                subtotal = Number(subtotal.toFixed(2));
                // Actualizar modelos y DOM
                this.subtotal = subtotal;
                const elSub = document.getElementById("subtotal_factura");
                if (elSub) elSub.value = subtotal;
                const elEditSub = document.getElementById(
                    "edit_subtotal_factura"
                );
                if (elEditSub) elEditSub.value = subtotal;

                // Calcular impuesto como 15% del subtotal (no editable en los modales)
                const impuestoVal = Number((subtotal * 0.15).toFixed(2));

                const total = Number((subtotal + impuestoVal).toFixed(2));
                this.total = total;
                const elTotal = document.getElementById("total_factura");
                if (elTotal) elTotal.value = total;
                const elEditTotal =
                    document.getElementById("edit_total_factura");
                if (elEditTotal) elEditTotal.value = total;

                // Total en letras
                const letras = this.totalToLetras2(total);
                this.total_letras = letras;
                const elTL = document.getElementById("total_letras_factura");
                if (elTL) elTL.value = letras;
                const elEditTL = document.getElementById(
                    "edit_total_letras_factura"
                );
                if (elEditTL) elEditTL.value = letras;
                // También actualizar la factura en la lista si corresponde (para reflejar cambios en la tabla)
                try {
                    const fid =
                        this.itemToEdit?.id_factura_pk ||
                        this.itemToEdit?.id ||
                        null;
                    if (fid) {
                        // actualizar itemToEdit
                        this.itemToEdit.subtotal = subtotal;
                        // actualizar impuesto calculado
                        this.itemToEdit.impuesto = impuestoVal;
                        this.itemToEdit.total = total;
                        this.itemToEdit.total_letras = letras;
                        // actualizar entrada en facturas
                        const idx = (this.facturas || []).findIndex(
                            (f) =>
                                (f.id_factura_pk || f.id || f.numero) ==
                                (this.itemToEdit.id_factura_pk ||
                                    this.itemToEdit.id ||
                                    this.itemToEdit.numero)
                        );
                        if (idx !== -1) {
                            this.facturas[idx].subtotal = subtotal;
                            this.facturas[idx].impuesto = impuestoVal;
                            this.facturas[idx].total = total;
                            this.facturas[idx].total_letras = letras;
                        }
                    } else if (this.currentFacturaFilter) {
                        // si estamos filtrando por una factura en particular, actualizar la coincidencia
                        const fid2 = this.currentFacturaFilter;
                        const idx2 = (this.facturas || []).findIndex(
                            (f) => (f.id_factura_pk || f.id) == fid2
                        );
                        if (idx2 !== -1) {
                            this.facturas[idx2].subtotal = subtotal;
                            this.facturas[idx2].total = total;
                            this.facturas[idx2].total_letras = letras;
                        }
                    }
                } catch (e) {
                    console.warn(
                        "Could not update factura list with recomputed totals",
                        e
                    );
                }
            } catch (e) {
                console.warn("Error recomputing factura totals", e);
            }
        },

        totalToLetras(value) {
            // value puede ser número o cadena; devolver texto en español simple
            const n = Math.abs(parseFloat(value) || 0);
            const entero = Math.floor(n);
            const cent = Math.round((n - entero) * 100);

            const unidades = [
                "",
                "uno",
                "dos",
                "tres",
                "cuatro",
                "cinco",
                "seis",
                "siete",
                "ocho",
                "nueve",
                "diez",
                "once",
                "doce",
                "trece",
                "catorce",
                "quince",
                "dieciseis",
                "diecisiete",
                "dieciocho",
                "diecinueve",
            ];
            const decenas = [
                "",
                "",
                "veinte",
                "treinta",
                "cuarenta",
                "cincuenta",
                "sesenta",
                "setenta",
                "ochenta",
                "noventa",
            ];
            const centenas = [
                "",
                "ciento",
                "doscientos",
                "trescientos",
                "cuatrocientos",
                "quinientos",
                "seiscientos",
                "setecientos",
                "ochocientos",
                "novecientos",
            ];

            function numeroMenosDeMil(num) {
                let words = "";
                if (num === 0) return "cero";
                if (num === 100) return "cien";
                if (num >= 100) {
                    const c = Math.floor(num / 100);
                    words += centenas[c] + " ";
                    num = num % 100;
                }
                if (num < 20) {
                    words += unidades[num] || "";
                } else if (num < 30) {
                    if (num === 20) words += "veinte";
                    else words += "veinti" + (unidades[num - 20] || "");
                } else {
                    const d = Math.floor(num / 10);
                    const u = num % 10;
                    words += decenas[d] || "";
                    if (u) words += " y " + unidades[u];
                }
                return words.trim();
            }

            let words = "";
            const millones = Math.floor(entero / 1000000);
            const restoMillones = entero % 1000000;
            const miles = Math.floor(restoMillones / 1000);
            const resto = restoMillones % 1000;

            if (millones > 0) {
                if (millones === 1) words += "un millón ";
                else words += numeroMenosDeMil(millones) + " millones ";
            }
            if (miles > 0) {
                if (miles === 1) words += "mil ";
                else words += numeroMenosDeMil(miles) + " mil ";
            }
            if (resto > 0) {
                words += numeroMenosDeMil(resto) + " ";
            }

            words = words.trim();
            if (!words) words = "cero";

            // Añadir céntimos en formato 'con XX/100'
            const centStr = String(cent).padStart(2, "0");
            return (words + " con " + centStr + "/100").replace(
                /uno mil/,
                "un mil"
            );
        },

        handleModalSubmit(event) {
            console.log("Modal submit triggered:", event.detail);
            if (event.detail.formId === "formFactura") {
                console.log("Calling submitFactura");
                this.submitFactura();
            }
            if (event.detail.formId === "formEditFactura") {
                console.log("Calling updateFactura");
                this.updateFactura();
            }

            // Detalle handlers (form ids used in detalle modals)
            if (event.detail.formId === "formDetalle") {
                console.log("Calling submitDetalle");
                this.submitDetalle();
            }
            if (event.detail.formId === "formEditDetalle") {
                console.log("Calling updateDetalle");
                this.updateDetalle();
            }
        },

        handleDelete() {
            // Prefer detalle deletion if that modal is open
            if (this.isDeleteDetalleModalOpen) {
                this.deleteDetalle();
                return;
            }
            if (this.isDeleteFacturaModalOpen) {
                this.deleteFactura();
            }
        },

        // ---------- Detalle functions (copied/adapted from detalle-factura.js) ----------
        // Form fields for detalle (DOM-backed forms are used)
        id_factura_fk: "",
        id_servicio_fk: "",
        descripcion: "",
        precio_unitario: 0,
        cantidad: 0,
        impuesto: 0,
        descuento: 0,
        fecha_servicio: "",
        horas: 0,

        clearDetalleForm() {
            this.id_factura_fk = "";
            this.id_servicio_fk = "";
            this.descripcion = "";
            this.precio_unitario = 0;
            this.cantidad = 0;
            this.impuesto = 0;
            this.descuento = 0;
            this.fecha_servicio = "";
            this.horas = 0;
        },

        async submitDetalle() {
            console.log("submitDetalle called");
            const idFactura = parseInt(
                document.getElementById("id_factura_fk")?.value
            );
            const idServicio = parseInt(
                document.getElementById("id_servicio_fk")?.value
            );
            const descripcion =
                document.getElementById("descripcion")?.value || "";
            const precioUnitario = parseFloat(
                document.getElementById("precio_unitario")?.value || 0
            );
            const cantidad = parseFloat(
                document.getElementById("cantidad")?.value || 0
            );

            if (!idFactura || !idServicio) {
                window.showToast &&
                    window.showToast(
                        "Factura y Servicio son obligatorios",
                        "error"
                    );
                return;
            }

            try {
                const payload = {
                    id_factura_fk: idFactura,
                    id_servicio_fk: idServicio,
                    descripcion: descripcion,
                    precio_unitario: precioUnitario,
                    cantidad: cantidad,
                    impuesto: parseFloat(
                        document.getElementById("impuesto")?.value || 0
                    ),
                    descuento: parseFloat(
                        document.getElementById("descuento")?.value || 0
                    ),
                    fecha_servicio:
                        document.getElementById("fecha_servicio")?.value,
                    horas: parseFloat(
                        document.getElementById("horas")?.value || 0
                    ),
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
                                    window.showToast &&
                                        window.showToast(msg, "error");
                                });
                            }
                        });
                    } else {
                        window.showToast &&
                            window.showToast(
                                "Error al crear el detalle",
                                "error"
                            );
                    }
                    throw data;
                }

                window.showToast &&
                    window.showToast("Detalle creado exitosamente", "success");
                this.isDetalleModalOpen = false;
                this.clearDetalleForm();
                // Refrescar detalles filtrados para la factura y recomputar totales globales
                await this.fetchDetallesFactura(
                    this.currentFacturaFilter || payload.id_factura_fk
                );
                // Asegurar que la lista de facturas y sus totales/impuestos se actualicen inmediatamente
                // sin necesidad de recargar la página
                try {
                    await this.fetchAllDetallesAndRecompute();
                } catch (e) {
                    console.warn(
                        "fetchAllDetallesAndRecompute after submitDetalle failed",
                        e
                    );
                }
            } catch (error) {
                console.error("Error creating detalle:", error);
            }
        },

        async updateDetalle() {
            if (
                !this.detalleToEdit ||
                (!this.detalleToEdit.id && !this.detalleToEdit.id_detalle_pk)
            )
                return;

            const idFactura = parseInt(
                document.getElementById("edit_id_factura_fk")?.value
            );
            const idServicio = parseInt(
                document.getElementById("edit_id_servicio_fk")?.value
            );
            const descripcion =
                document.getElementById("edit_descripcion")?.value || "";
            const precioUnitario = parseFloat(
                document.getElementById("edit_precio_unitario")?.value || 0
            );
            const cantidad = parseFloat(
                document.getElementById("edit_cantidad")?.value || 0
            );

            try {
                const detalleId =
                    this.detalleToEdit.id_detalle_pk || this.detalleToEdit.id;
                const payload = {
                    id_factura_fk: idFactura,
                    id_servicio_fk: idServicio,
                    descripcion: descripcion,
                    precio_unitario: precioUnitario,
                    cantidad: cantidad,
                    impuesto: parseFloat(
                        document.getElementById("edit_impuesto")?.value || 0
                    ),
                    descuento: parseFloat(
                        document.getElementById("edit_descuento")?.value || 0
                    ),
                    fecha_servicio: document.getElementById(
                        "edit_fecha_servicio"
                    )?.value,
                    horas: parseFloat(
                        document.getElementById("edit_horas")?.value || 0
                    ),
                };

                const response = await fetch(
                    `/api/detalles-factura/${detalleId}`,
                    {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                        },
                        credentials: "same-origin",
                        body: JSON.stringify(payload),
                    }
                );

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
                                "Error al actualizar el detalle",
                                "error"
                            );
                    }
                    throw data;
                }

                window.showToast &&
                    window.showToast(
                        "Detalle actualizado exitosamente",
                        "success"
                    );
                this.isEditDetalleModalOpen = false;
                this.detalleToEdit = {};
                await this.fetchDetallesFactura(
                    this.currentFacturaFilter || idFactura
                );
            } catch (error) {
                console.error("Error updating detalle:", error);
            }
        },

        async deleteDetalle() {
            if (
                !this.detalleToDelete ||
                (!this.detalleToDelete.id &&
                    !this.detalleToDelete.id_detalle_pk)
            )
                return;

            try {
                const detalleId =
                    this.detalleToDelete.id_detalle_pk ||
                    this.detalleToDelete.id;
                const detalleFacturaId =
                    this.detalleToDelete.id_factura_fk ||
                    this.detalleToDelete.id_factura ||
                    this.detalleToDelete.id_factura_pk ||
                    null;
                const response = await fetch(
                    `/api/detalles-factura/${detalleId}`,
                    {
                        method: "DELETE",
                        headers: { Accept: "application/json" },
                        credentials: "same-origin",
                    }
                );

                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw data;

                window.showToast &&
                    window.showToast(
                        "Detalle eliminado exitosamente",
                        "success"
                    );
                this.isDeleteDetalleModalOpen = false;
                this.detalleToDelete = {};
                await this.fetchDetallesFactura(
                    this.currentFacturaFilter || detalleFacturaId
                );
            } catch (error) {
                console.error("Error deleting detalle:", error);
                const errorMessage =
                    error?.error || "Error al eliminar el detalle";
                window.showToast && window.showToast(errorMessage, "error");
            }
        },

        openCreateDetalleModal() {
            this.clearDetalleForm();
            // Clear DOM inputs if present
            try {
                [
                    "id_factura_fk",
                    "id_servicio_fk",
                    "descripcion",
                    "precio_unitario",
                    "cantidad",
                    "impuesto",
                    "fecha_servicio",
                    "horas",
                    "descuento",
                ].forEach((id) => {
                    const el = document.getElementById(id);
                    if (el) el.value = "";
                });
            } catch (e) {}
            // If we're viewing detalles filtered to a factura, preselect it
            try {
                if (this.currentFacturaFilter) {
                    const sel = document.getElementById("id_factura_fk");
                    if (sel) sel.value = this.currentFacturaFilter;
                }
            } catch (e) {}
            this.isDetalleModalOpen = true;
        },

        openEditDetalleModal(detalle) {
            this.detalleToEdit = detalle;
            // Llenar el formulario con los datos del detalle (para edicion)
            // Se usan bindings directos al DOM para mantener compatibilidad
            // Con servers que devuelven varios nombres de campo
            // Populate DOM inputs so document.getElementById reads the correct values
            try {
                const facturaVal =
                    detalle.id_factura_fk ||
                    detalle.id_factura ||
                    detalle.id_factura_pk ||
                    "";
                const servicioVal =
                    detalle.id_servicio_fk ||
                    detalle.id_servicio ||
                    detalle.id_servicio_pk ||
                    "";
                document.getElementById("edit_id_factura_fk") &&
                    (document.getElementById("edit_id_factura_fk").value =
                        facturaVal);
                document.getElementById("edit_id_servicio_fk") &&
                    (document.getElementById("edit_id_servicio_fk").value =
                        servicioVal);
                document.getElementById("edit_descripcion") &&
                    (document.getElementById("edit_descripcion").value =
                        detalle.descripcion || "");
                document.getElementById("edit_precio_unitario") &&
                    (document.getElementById("edit_precio_unitario").value =
                        detalle.precio_unitario || 0);
                document.getElementById("edit_cantidad") &&
                    (document.getElementById("edit_cantidad").value =
                        detalle.cantidad || 0);
                document.getElementById("edit_impuesto") &&
                    (document.getElementById("edit_impuesto").value =
                        detalle.impuesto || 0);
                document.getElementById("edit_descuento") &&
                    (document.getElementById("edit_descuento").value =
                        detalle.descuento || 0);
                document.getElementById("edit_fecha_servicio") &&
                    (document.getElementById("edit_fecha_servicio").value =
                        detalle.fecha_servicio || "");
                document.getElementById("edit_horas") &&
                    (document.getElementById("edit_horas").value =
                        detalle.horas || 0);
            } catch (e) {
                console.warn("Could not populate edit detalle DOM fields:", e);
            }
            this.isEditDetalleModalOpen = true;
        },

        openDeleteDetalleModal(detalle) {
            this.detalleToDelete = detalle;
            this.isDeleteDetalleModalOpen = true;
        },

        // ----------------------------------------------------------------------------------
    }));
});

// Event listeners para manejar envíos de modales
window.addEventListener("modal-submit", function (event) {
    try {
        const el = document.querySelector('[x-data*="facturasCrud"]');
        const facturasCrudComponent = el ? Alpine.$data(el) : null;
        if (facturasCrudComponent && facturasCrudComponent.handleModalSubmit) {
            facturasCrudComponent.handleModalSubmit(event);
        }
    } catch (_) {
        /* ignore if component not present */
    }
});

window.addEventListener("confirm-delete", function (event) {
    try {
        const el = document.querySelector('[x-data*="facturasCrud"]');
        const facturasCrudComponent = el ? Alpine.$data(el) : null;
        if (facturasCrudComponent && facturasCrudComponent.handleDelete) {
            facturasCrudComponent.handleDelete();
        }
    } catch (_) {
        /* ignore if component not present */
    }
});
