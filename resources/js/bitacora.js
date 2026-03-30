document.addEventListener("alpine:init", () => {
    Alpine.data("bitacoraList", () => ({
        items: [],
        loading: false,
        error: "",
        isClearAllModalOpen: false,
        isDetailModalOpen: false,
        itemToDelete: { nombre: "todos los registros de la bitácora" },
        selectedItem: null,
        pagination: { page: 1, last_page: 1, total: 0, per_page: 10 },
        filters: {
            search: "",
            accion: "",
            usuario: "",
            objeto: "",
            desde: "",
            hasta: "",
            per_page: 10,
            sort: "fecha_evento",
            direction: "desc",
        },
        init() {
            this.fetch();
        },
        apiBase() {
            return "/api/bitacoras";
        },
        exportCsvUrl() {
            return "/api/bitacoras/export/csv";
        },
        reportUrl() {
            const params = new URLSearchParams();
            params.set("modulo", "Bitacora");
            const now = new Date();
            const pad = (n) => String(n).padStart(2, "0");
            const yyyy = now.getFullYear();
            const mm = pad(now.getMonth() + 1);
            const dd = pad(now.getDate());
            params.set("fecha", `${yyyy}-${mm}-${dd}`);

            Object.entries(this.filters).forEach(([k, v]) => {
                if (v !== "" && v != null) params.set(k, v);
            });

            params.set("sort", this.filters.sort || "fecha_evento");
            params.set("direction", this.filters.direction || "desc");
            return `/admin/reportes-header?${params.toString()}`;
        },
        authHeader() {
            return {};
        },
        async fetch(page = null) {
            this.loading = true;
            this.error = "";
            if (page) this.pagination.page = page;
            const params = new URLSearchParams();
            Object.entries(this.filters).forEach(([k, v]) => {
                if (v !== null && v !== undefined && v !== "")
                    params.append(k, v);
            });
            params.append("page", this.pagination.page);
            try {
                const res = await axios.get(
                    `${this.apiBase()}?${params.toString()}`,
                    {
                        headers: {
                            ...this.authHeader(),
                            Accept: "application/json",
                        },
                    }
                );
                const data = res.data || {};
                this.items = data.data || [];
                this.pagination.page =
                    data.meta?.current_page || data.page || 1;
                this.pagination.last_page =
                    data.meta?.last_page || data.last_page || 1;
                this.pagination.total =
                    data.meta?.total || data.total || this.items.length;
                this.pagination.per_page =
                    data.meta?.per_page ||
                    data.per_page ||
                    this.filters.per_page;
            } catch (e) {
                this.error =
                    e?.response?.data?.error || e?.message || "Error al cargar";
            } finally {
                this.loading = false;
            }
        },
        changePage(p) {
            if (p >= 1 && p <= this.pagination.last_page) this.fetch(p);
        },
        friendlyDescription(item) {
            if (!item) return "-";
            const descripcion = (item.descripcion || "").toString().trim();
            const objeto = item.objeto?.nombre_objeto || "registro";
            const idTxt = item.id_registro ? ` (ID: ${item.id_registro})` : "";
            const isRawEndpoint = /^(POST|PUT|PATCH|DELETE)\s+/i.test(descripcion);

            if (descripcion && !isRawEndpoint) return descripcion;

            if (item.accion === "Insertar") return `Se creó un registro en ${objeto}${idTxt}`;
            if (item.accion === "Actualizar") return `Se actualizó un registro en ${objeto}${idTxt}`;
            if (item.accion === "Eliminar") return `Se eliminó un registro de ${objeto}${idTxt}`;
            return descripcion || "Acción registrada";
        },
        openDetail(item) {
            this.selectedItem = item;
            this.isDetailModalOpen = true;
        },
        flattenObject(obj, prefix = '') {
            const flattened = {};
            if (!obj || typeof obj !== 'object') return flattened;

            Object.keys(obj).forEach(key => {
                const value = obj[key];
                const fullKey = prefix ? `${prefix}.${key}` : key;

                if (value !== null && typeof value === 'object' && !Array.isArray(value)) {
                    Object.assign(flattened, this.flattenObject(value, fullKey));
                } else if (Array.isArray(value)) {
                    flattened[fullKey] = value.join(', ');
                } else {
                    flattened[fullKey] = value;
                }
            });

            return flattened;
        },
        isHiddenField(fieldPath) {
            if (!fieldPath) return false;

            const lower = fieldPath.toLowerCase();

            // Hide ID fields
            if (lower === 'id' || lower.endsWith('_pk') || lower.endsWith('_id') || lower.endsWith('_fk')) {
                return true;
            }

            // Hide timestamp fields
            if (['created_at', 'updated_at', 'deleted_at', 'fecha_creacion', 'fecha_actualizacion'].includes(lower)) {
                return true;
            }

            // Hide certain system fields
            if (['remember_token', 'email_verified_at', 'id_bitacora_pk', 'id_bitacora'].includes(lower)) {
                return true;
            }

            return false;
        },
        fieldLabel(fieldPath) {
            if (!fieldPath) return fieldPath;

            // Remove prefixes like 'persona.' or 'usuario.'
            const basePath = fieldPath.includes('.') ? fieldPath.split('.').pop() : fieldPath;

            // Convert snake_case to Title Case
            return basePath
                .split('_')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
                .join(' ');
        },
        displayValue(value) {
            if (value === null || value === undefined || value === '') return '-';

            if (typeof value === 'boolean') {
                return value ? 'Sí' : 'No';
            }

            if (typeof value === 'object') {
                return JSON.stringify(value);
            }

            return String(value);
        },
        buildDetailRows(item) {
            if (!item) return [];

            const accion = (item.accion || '').toLowerCase();
            if (accion !== 'actualizar') return [];

            const beforeFlat = this.flattenObject(item.antes || {});
            const afterFlat = this.flattenObject(item.despues || {});

            // Get all unique keys from both before and after
            const allKeys = Array.from(new Set([
                ...Object.keys(beforeFlat),
                ...Object.keys(afterFlat)
            ])).sort();

            const rows = [];

            allKeys.forEach(key => {
                // Skip hidden fields
                if (this.isHiddenField(key)) return;

                const beforeValue = beforeFlat[key];
                const afterValue = afterFlat[key];

                // For UPDATE actions, skip unchanged fields
                if (accion === 'actualizar' && beforeValue === afterValue) {
                    return;
                }

                // For UPDATE, show both
                rows.push({
                    campo: this.fieldLabel(key),
                    antes: this.displayValue(beforeValue),
                    despues: this.displayValue(afterValue)
                });
            });

            return rows;
        },
        formatJson(value) {
            if (value === null || value === undefined || value === "") return "Sin datos";
            if (typeof value === "string") return value;
            try {
                return JSON.stringify(value, null, 2);
            } catch (_) {
                return String(value);
            }
        },
        resetFilters() {
            this.filters = {
                search: "",
                accion: "",
                usuario: "",
                objeto: "",
                desde: "",
                hasta: "",
                per_page: 10,
                sort: "fecha_evento",
                direction: "desc",
            };
            this.fetch(1);
        },
        async exportCsv() {
            this.loading = true;
            this.error = "";

            try {
                const params = new URLSearchParams();
                Object.entries(this.filters).forEach(([k, v]) => {
                    if (v !== null && v !== undefined && v !== "") {
                        params.append(k, v);
                    }
                });

                const res = await axios.get(`${this.exportCsvUrl()}?${params.toString()}`, {
                    headers: {
                        ...this.authHeader(),
                        Accept: "text/csv",
                    },
                    responseType: "blob",
                });

                const blob = new Blob([res.data], { type: "text/csv;charset=utf-8;" });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement("a");
                link.href = url;

                const dispo = res.headers["content-disposition"] || "";
                const match = dispo.match(/filename="?([^\"]+)"?/i);
                const fileName = match?.[1] || `bitacora_${new Date().toISOString().slice(0, 19).replace(/[:T]/g, "-")}.csv`;

                link.setAttribute("download", fileName);
                document.body.appendChild(link);
                link.click();
                link.remove();
                window.URL.revokeObjectURL(url);
            } catch (e) {
                const msg = e?.response?.data?.error || e?.message || "Error al exportar CSV";
                this.error = msg;
                if (window.showToast) {
                    window.showToast(msg, "error");
                }
            } finally {
                this.loading = false;
            }
        },
        async clearAllRecords() {
            this.loading = true;
            this.error = "";
            try {
                const res = await axios.post(
                    `${this.apiBase()}/clean/all`,
                    {},
                    {
                        headers: {
                            ...this.authHeader(),
                            Accept: "application/json",
                        },
                    }
                );
                if (res.data.ok) {
                    this.isClearAllModalOpen = false;
                    if (window.showToast) {
                        window.showToast("Bitácora limpiada exitosamente.", "success");
                    }
                    await this.fetch(1);
                } else {
                    if (window.showToast) {
                        window.showToast(res.data.error || 'Error al limpiar la bitácora', "error");
                    }
                }
            } catch (e) {
                const msg = e?.response?.data?.error || e?.message || 'Error al limpiar la bitácora';
                if (window.showToast) {
                    window.showToast(msg, "error");
                }
            } finally {
                this.loading = false;
            }
        },
    }));
});
