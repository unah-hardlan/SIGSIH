if (typeof window !== "undefined") {
    window.empresaData = function () {
        return {
            tab: "default",
            isEmpresaModalOpen: false,
            isDeleteEmpresaModalOpen: false,
            empresaToEdit: null,
            empresaToDelete: null,
            empresas: [],

            numbers: [],
            formEmpresa: {
                id: null,
                nombre_comercial: "",
                razon_social: "",
                rtn: "",
                descripcion_empresa: "",
                horario_atencion: "",
                fecha_registro: new Date().toISOString().slice(0, 10),
                estado_cliente: "activo",
            },
            horarioUI: {
                dias: {
                    lun: false,
                    mar: false,
                    mie: false,
                    jue: false,
                    vie: false,
                    sab: false,
                    dom: false,
                },
                desde: "08:00",
                hasta: "17:00",
            },
            loadingEmpresas: false,
            saving: false,
            deleting: false,
            errors: {},
            searchEmpresa: "",
            estadoEmpresa: "",
            ordenarPor: "nombre_comercial",

            currentPage: 1,
            perPage: 10,
            reportUrl() {
                const params = new URLSearchParams();
                params.set("modulo", "Empresas");
                if (this.searchEmpresa)
                    params.set("search", this.searchEmpresa);

                if (this.estadoEmpresa)
                    params.set(
                        "estado_empresa",
                        this.estadoEmpresa.toLowerCase()
                    );

                if (this.ordenarPor) {
                    const mapOrden = {
                        nombre_comercial: "nombre_empresa",
                        estado_cliente: "estado_empresa",
                        fecha_registro: "fecha_registro",
                    };
                    const serverOrden =
                        mapOrden[this.ordenarPor] || this.ordenarPor;
                    params.set("ordenar_por", serverOrden);
                }

                const now = new Date();
                const pad = (n) => String(n).padStart(2, "0");
                const yyyy = now.getFullYear();
                const mm = pad(now.getMonth() + 1);
                const dd = pad(now.getDate());
                params.set("fecha", `${yyyy}-${mm}-${dd}`);
                params.set("fecha_generacion", now.toISOString());

                return "/admin/reportes-header?" + params.toString();
            },

            _norm(v) {
                return (v || "").toString().trim().toLowerCase();
            },
            _isDuplicateLocal(field, value, excludeId = null) {
                const val = this._norm(value);
                if (!val) return false;
                return this.empresas.some((e) => {
                    if (excludeId && e.id === excludeId) return false;
                    return this._norm(e[field]) === val;
                });
            },
            _applyServerDuplicateErrors(jsonOrText, payload) {
                try {
                    if (!jsonOrText) return false;
                    if (typeof jsonOrText === "object") {
                        if (
                            jsonOrText.errors &&
                            typeof jsonOrText.errors === "object"
                        ) {
                            this.errors = jsonOrText.errors;
                            return true;
                        }

                        if (jsonOrText.message) {
                            return this._applyDuplicateFromMessage(
                                jsonOrText.message,
                                payload
                            );
                        }
                    } else if (typeof jsonOrText === "string") {
                        return this._applyDuplicateFromMessage(
                            jsonOrText,
                            payload
                        );
                    }
                } catch (_) {}
                return false;
            },
            _applyDuplicateFromMessage(message, payload) {
                if (!message) return false;
                const msg = message.toString();
                let matched = false;
                const push = (k, m) => {
                    (this.errors[k] || (this.errors[k] = [])).push(m);
                    matched = true;
                };

                if (
                    /Duplicate entry/i.test(msg) ||
                    /ya ha sido (tomad|utilizad)/i.test(msg) ||
                    /already been taken/i.test(msg)
                ) {
                    if (
                        /nombre|nombre_comercial/i.test(msg) &&
                        payload?.nombre_comercial
                    )
                        push(
                            "nombre_comercial",
                            "El nombre comercial ya existe."
                        );
                    if (
                        /razon|razón|razon_social/i.test(msg) &&
                        payload?.razon_social
                    )
                        push("razon_social", "La razón social ya existe.");
                    if (/rtn/i.test(msg) && payload?.rtn)
                        push("rtn", "El RTN ya existe.");

                    if (!matched) {
                        if (payload?.nombre_comercial)
                            push("nombre_comercial", "Valor duplicado.");
                        if (payload?.razon_social)
                            push("razon_social", "Valor duplicado.");
                        if (payload?.rtn) push("rtn", "Valor duplicado.");
                    }
                    return true;
                }

                if (/existe|existente|taken|únic|unique/i.test(msg)) {
                    if (payload?.nombre_comercial)
                        push(
                            "nombre_comercial",
                            "El nombre comercial ya existe."
                        );
                    if (payload?.razon_social)
                        push("razon_social", "La razón social ya existe.");
                    if (payload?.rtn) push("rtn", "El RTN ya existe.");
                    return true;
                }
                return matched;
            },
            resetForm() {
                this.formEmpresa = {
                    id: null,
                    nombre_comercial: "",
                    razon_social: "",
                    rtn: "",
                    descripcion_empresa: "",
                    horario_atencion: "",
                    fecha_registro: new Date().toISOString().slice(0, 10),
                    estado_cliente: "activo",
                };
                this.errors = {};
                this.resetHorarioUI();
            },
            openEmpresaModal(edit = false, empresa = null) {
                this.isEmpresaModalOpen = true;
                this.empresaToEdit = edit ? { ...empresa } : null;
                if (!edit || !empresa) {
                    this.resetForm();
                } else {
                    this.formEmpresa = {
                        id: empresa.id,
                        nombre_comercial: empresa.nombre_comercial,
                        razon_social: empresa.razon_social,
                        rtn: empresa.rtn,
                        descripcion_empresa: empresa.descripcion_empresa,
                        horario_atencion: empresa.horario_atencion,
                        fecha_registro:
                            (empresa.raw.fecha_registro || "")
                                .toString()
                                .split(" ")[0] ||
                            new Date().toISOString().slice(0, 10),
                        estado_cliente: (
                            empresa.estado_cliente || "activo"
                        ).toLowerCase(),
                    };
                    this.parseHorarioToUI(empresa.horario_atencion || "");
                }
            },
            openDeleteEmpresaModal(empresa) {
                this.empresaToDelete = empresa;
                this.isDeleteEmpresaModalOpen = true;
            },
            apiHeaders() {
                const t = localStorage.getItem("authToken");
                return {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    ...(t ? { Authorization: "Bearer " + t } : {}),
                };
            },
            showToast(msg, type = "ok") {
                const d = document.createElement("div");
                d.className =
                    "fixed top-4 right-4 z-50 px-4 py-2 rounded shadow text-sm " +
                    (type === "error"
                        ? "bg-red-600 text-white"
                        : "bg-green-600 text-white");
                d.textContent = msg;
                document.body.appendChild(d);
                setTimeout(() => d.remove(), 3500);
            },
            mapEmpresa(e) {
                const months = [
                    "enero",
                    "febrero",
                    "marzo",
                    "abril",
                    "mayo",
                    "junio",
                    "julio",
                    "agosto",
                    "septiembre",
                    "octubre",
                    "noviembre",
                    "diciembre",
                ];
                let dateStr = (e.fecha_registro || "").toString().split(" ")[0];
                let formattedDate = "";
                if (dateStr) {
                    let parts = dateStr.split("-");
                    if (parts.length === 3) {
                        let year = parts[0];
                        let month = parseInt(parts[1]) - 1;
                        let day = parts[2];
                        formattedDate = `${day} de ${months[month]} del ${year}`;
                    } else {
                        formattedDate = dateStr;
                    }
                }
                return {
                    id: e.id_cliente_fk || e.id || Math.random(),
                    nombre_comercial: e.nombre_comercial || "—",
                    razon_social: e.razon_social || "",
                    rtn: e.rtn || "",
                    descripcion_empresa: e.descripcion_empresa || "",
                    horario_atencion: e.horario_atencion || "",
                    fecha_registro: formattedDate,
                    estado_cliente: (
                        e.estado_cliente || "activo"
                    ).toLowerCase(),
                    estado_label:
                        (e.estado_cliente || "activo").toLowerCase() ===
                        "activo"
                            ? "Activo"
                            : "Inactivo",
                    contactos: e.contactos || [],
                    raw: e,
                };
            },
            async fetchEmpresas() {
                this.loadingEmpresas = true;
                try {
                    const params = new URLSearchParams();
                    params.set("per_page", "100");
                    if (this.searchEmpresa)
                        params.set("search", this.searchEmpresa);
                    if (this.estadoEmpresa)
                        params.set(
                            "estado_cliente",
                            this.estadoEmpresa.toLowerCase()
                        );
                    let r = await fetch(
                        "/api/empresas-cliente?" + params.toString(),
                        {
                            headers: this.apiHeaders(),
                            credentials: "same-origin",
                        }
                    );
                    if (r.status === 401 && window.__AUTH?.ensureToken) {
                        await window.__AUTH.ensureToken(true);
                        r = await fetch(
                            "/api/empresas-cliente?" + params.toString(),
                            {
                                headers: this.apiHeaders(),
                                credentials: "same-origin",
                            }
                        );
                    }
                    if (!r.ok) throw new Error("Error");
                    const j = await r.json();
                    this.empresas = (j.data || []).map((e) =>
                        this.mapEmpresa(e)
                    );
                    this.sortEmpresasLocal();

                    this.numbers = this.empresas;
                } catch (e) {
                    this.showToast("No se pudieron cargar empresas", "error");
                } finally {
                    this.loadingEmpresas = false;
                }
            },
            async createEmpresaCliente() {
                this.saving = true;
                this.errors = {};
                try {
                    const payload = {
                        nombre_comercial: this.formEmpresa.nombre_comercial,
                        razon_social: this.formEmpresa.razon_social || null,
                        rtn: this.formEmpresa.rtn || null,
                        descripcion_empresa:
                            this.formEmpresa.descripcion_empresa || null,
                        horario_atencion:
                            this.formEmpresa.horario_atencion || null,
                        fecha_registro:
                            this.formEmpresa.fecha_registro ||
                            new Date().toISOString().slice(0, 10),
                        estado_cliente: (
                            this.formEmpresa.estado_cliente || "activo"
                        ).toLowerCase(),
                    };
                    let r = await fetch("/api/empresas-cliente", {
                        method: "POST",
                        headers: this.apiHeaders(),
                        credentials: "same-origin",
                        body: JSON.stringify(payload),
                    });
                    if (r.status === 401 && window.__AUTH?.ensureToken) {
                        await window.__AUTH.ensureToken(true);
                        r = await fetch("/api/empresas-cliente", {
                            method: "POST",
                            headers: this.apiHeaders(),
                            credentials: "same-origin",
                            body: JSON.stringify(payload),
                        });
                    }
                    if (r.status === 422) {
                        const j = await r.json();
                        this.errors = j.errors || {};
                        throw new Error("Validación");
                    }
                    if (!r.ok) {
                        let data = null;
                        let text = "";
                        try {
                            text = await r.text();
                            data = JSON.parse(text);
                        } catch (_) {}
                        this._applyServerDuplicateErrors(data || text, payload);
                        throw new Error("Error");
                    }
                    const j = await r.json();
                    if (j.data) {
                        this.empresas.unshift(this.mapEmpresa(j.data));
                        this.sortEmpresasLocal();
                    }
                    this.showToast("Empresa creada");
                    this.isEmpresaModalOpen = false;
                    this.resetForm();

                    this.numbers = this.empresas;
                } catch (e) {
                    this.showToast(
                        Object.keys(this.errors).length
                            ? "Corrige los campos duplicados"
                            : "No se creó empresa",
                        "error"
                    );
                } finally {
                    this.saving = false;
                }
            },
            async updateEmpresaCliente() {
                if (!this.formEmpresa.id) return;
                this.saving = true;
                this.errors = {};
                try {
                    const payload = {
                        nombre_comercial: this.formEmpresa.nombre_comercial,
                        razon_social: this.formEmpresa.razon_social || null,
                        rtn: this.formEmpresa.rtn || null,
                        descripcion_empresa:
                            this.formEmpresa.descripcion_empresa || null,
                        horario_atencion:
                            this.formEmpresa.horario_atencion || null,
                        fecha_registro: this.formEmpresa.fecha_registro,
                        estado_cliente: (
                            this.formEmpresa.estado_cliente || "activo"
                        ).toLowerCase(),
                    };
                    let r = await fetch(
                        "/api/empresas-cliente/" + this.formEmpresa.id,
                        {
                            method: "PUT",
                            headers: this.apiHeaders(),
                            credentials: "same-origin",
                            body: JSON.stringify(payload),
                        }
                    );
                    if (r.status === 401 && window.__AUTH?.ensureToken) {
                        await window.__AUTH.ensureToken(true);
                        r = await fetch(
                            "/api/empresas-cliente/" + this.formEmpresa.id,
                            {
                                method: "PUT",
                                headers: this.apiHeaders(),
                                credentials: "same-origin",
                                body: JSON.stringify(payload),
                            }
                        );
                    }
                    if (r.status === 422) {
                        const j = await r.json();
                        this.errors = j.errors || {};
                        throw new Error("Validación");
                    }
                    if (!r.ok) {
                        let data = null;
                        let text = "";
                        try {
                            text = await r.text();
                            data = JSON.parse(text);
                        } catch (_) {}
                        this._applyServerDuplicateErrors(data || text, payload);
                        throw new Error("Error");
                    }
                    const j = await r.json();
                    const idx = this.empresas.findIndex(
                        (e) => e.id === this.formEmpresa.id
                    );
                    if (idx > -1 && j.data) {
                        this.empresas.splice(idx, 1, this.mapEmpresa(j.data));
                        this.sortEmpresasLocal();
                    }
                    this.showToast("Empresa actualizada");
                    this.isEmpresaModalOpen = false;
                    this.resetForm();

                    this.numbers = this.empresas;
                } catch (e) {
                    this.showToast(
                        Object.keys(this.errors).length
                            ? "Corrige los campos duplicados"
                            : "No se actualizó empresa",
                        "error"
                    );
                } finally {
                    this.saving = false;
                }
            },
            async deleteEmpresaClienteApi(id) {
                this.deleting = true;
                try {
                    let r = await fetch("/api/empresas-cliente/" + id, {
                        method: "DELETE",
                        headers: this.apiHeaders(),
                        credentials: "same-origin",
                    });
                    if (r.status === 401 && window.__AUTH?.ensureToken) {
                        await window.__AUTH.ensureToken(true);
                        r = await fetch("/api/empresas-cliente/" + id, {
                            method: "DELETE",
                            headers: this.apiHeaders(),
                            credentials: "same-origin",
                        });
                    }
                    if (!r.ok) throw new Error("Error");
                    this.empresas = this.empresas.filter((e) => e.id !== id);
                    this.showToast("Empresa eliminada");

                    this.numbers = this.empresas;
                } catch (e) {
                    this.showToast("Error al eliminar empresa", "error");
                } finally {
                    this.deleting = false;
                    this.isDeleteEmpresaModalOpen = false;
                    this.empresaToDelete = null;
                }
            },
            init() {
                this.fetchEmpresas();
                const debounce = (fn, ms = 400) => {
                    let h;
                    return (...a) => {
                        clearTimeout(h);
                        h = setTimeout(() => fn(...a), ms);
                    };
                };
                this.$watch(
                    "searchEmpresa",
                    debounce(() => {
                        this.fetchEmpresas();
                        this.currentPage = 1;
                    })
                );
                this.$watch("estadoEmpresa", () => {
                    this.fetchEmpresas();
                    this.currentPage = 1;
                });
                this.$watch("ordenarPor", () => {
                    this.sortEmpresasLocal();
                    this.currentPage = 1;
                });
                this.$watch("horarioUI.desde", () => this.syncHorarioString());
                this.$watch("horarioUI.hasta", () => this.syncHorarioString());
                ["lun", "mar", "mie", "jue", "vie", "sab", "dom"].forEach(
                    (k) => {
                        this.$watch("horarioUI.dias." + k, () =>
                            this.syncHorarioString()
                        );
                    }
                );

                this.$watch("isEmpresaModalOpen", (open) => {
                    if (!open) {
                        this.resetForm();
                        this.empresaToEdit = null;
                    }
                });
                this.$watch("isDeleteEmpresaModalOpen", (open) => {
                    if (!open) {
                        this.empresaToDelete = null;
                    }
                });
            },
            sortEmpresasLocal() {
                if (!this.ordenarPor) return;
                const campo = this.ordenarPor;
                if (campo === "fecha_registro") {
                    this.empresas.sort((a, b) => {
                        const ad = new Date(
                            a.raw?.fecha_registro || a.fecha_registro || 0
                        );
                        const bd = new Date(
                            b.raw?.fecha_registro || b.fecha_registro || 0
                        );
                        return bd - ad;
                    });
                    return;
                }
                this.empresas.sort((a, b) => {
                    const av = (a[campo] || "").toString().toLowerCase();
                    const bv = (b[campo] || "").toString().toLowerCase();
                    if (av < bv) return -1;
                    if (av > bv) return 1;
                    return 0;
                });
            },

            paginatedEmpresas() {
                return this.empresas.slice(
                    (this.currentPage - 1) * this.perPage,
                    this.currentPage * this.perPage
                );
            },
            totalPages() {
                return Math.ceil(this.empresas.length / this.perPage);
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
            submitEmpresa() {
                if (!this.validateEmpresaForm()) return;
                if (this.formEmpresa.id) {
                    this.updateEmpresaCliente();
                } else {
                    this.createEmpresaCliente();
                }
            },
            deleteEmpresa() {
                if (this.empresaToDelete) {
                    this.deleteEmpresaClienteApi(this.empresaToDelete.id);
                }
            },
            resetHorarioUI() {
                this.horarioUI = {
                    dias: {
                        lun: false,
                        mar: false,
                        mie: false,
                        jue: false,
                        vie: false,
                        sab: false,
                        dom: false,
                    },
                    desde: "08:00",
                    hasta: "17:00",
                };
            },
            diasLabels() {
                return [
                    { k: "lun", t: "Lun" },
                    { k: "mar", t: "Mar" },
                    { k: "mie", t: "Mié" },
                    { k: "jue", t: "Jue" },
                    { k: "vie", t: "Vie" },
                    { k: "sab", t: "Sáb" },
                    { k: "dom", t: "Dom" },
                ];
            },
            setDias(what) {
                const all = ["lun", "mar", "mie", "jue", "vie", "sab", "dom"];
                if (what === "lv") {
                    all.forEach(
                        (k) =>
                            (this.horarioUI.dias[k] = [
                                "lun",
                                "mar",
                                "mie",
                                "jue",
                                "vie",
                            ].includes(k))
                    );
                } else if (what === "todos") {
                    all.forEach((k) => (this.horarioUI.dias[k] = true));
                } else if (what === "ninguno") {
                    all.forEach((k) => (this.horarioUI.dias[k] = false));
                }
                this.syncHorarioString();
            },
            formatDiasCompact() {
                const order = ["lun", "mar", "mie", "jue", "vie", "sab", "dom"];
                const label = {
                    lun: "Lun",
                    mar: "Mar",
                    mie: "Mié",
                    jue: "Jue",
                    vie: "Vie",
                    sab: "Sáb",
                    dom: "Dom",
                };
                const sel = order.filter((k) => this.horarioUI.dias[k]);
                if (sel.length === 0) return "";
                let groups = [];
                let start = null;
                let prev = null;
                const pushGroup = (s, e) => {
                    if (s === e) groups.push(label[s]);
                    else groups.push(label[s] + "-" + label[e]);
                };
                sel.forEach((k) => {
                    if (start === null) {
                        start = prev = k;
                    } else {
                        const idxPrev = order.indexOf(prev),
                            idxCur = order.indexOf(k);
                        if (idxCur === idxPrev + 1) {
                            prev = k;
                        } else {
                            pushGroup(start, prev);
                            start = prev = k;
                        }
                    }
                });
                if (start !== null) pushGroup(start, prev);
                return groups.join(", ");
            },
            syncHorarioString() {
                const diasStr = this.formatDiasCompact();
                const tiempo = `${this.horarioUI.desde}-${this.horarioUI.hasta}`;
                this.formEmpresa.horario_atencion = diasStr
                    ? `${diasStr} ${tiempo}`
                    : "";
            },
            parseHorarioToUI(str) {
                this.resetHorarioUI();
                if (!str) return;
                try {
                    const m = str.match(
                        /(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})/
                    );
                    if (m) {
                        this.horarioUI.desde = this.normHora(m[1]);
                        this.horarioUI.hasta = this.normHora(m[2]);
                    }
                    const dias = [
                        "Lun",
                        "Mar",
                        "Mié",
                        "Jue",
                        "Vie",
                        "Sáb",
                        "Dom",
                    ];
                    const map = {
                        Lun: "lun",
                        Mar: "mar",
                        Mié: "mie",
                        Jue: "jue",
                        Vie: "vie",
                        Sáb: "sab",
                        Dom: "dom",
                    };
                    const dayPart = str.replace(/\d{1,2}:\d{2}.*$/, "").trim();
                    if (dayPart) {
                        dayPart
                            .split(",")
                            .map((s) => s.trim())
                            .forEach((tok) => {
                                if (!tok) return;
                                const r = tok.split("-").map((x) => x.trim());
                                if (r.length === 1) {
                                    const key = map[r[0]];
                                    if (key) this.horarioUI.dias[key] = true;
                                } else if (r.length === 2) {
                                    const a = dias.indexOf(r[0]);
                                    const b = dias.indexOf(r[1]);
                                    if (a > -1 && b > -1 && a <= b) {
                                        for (let i = a; i <= b; i++) {
                                            this.horarioUI.dias[
                                                map[dias[i]]
                                            ] = true;
                                        }
                                    }
                                }
                            });
                    }
                    this.syncHorarioString();
                } catch (e) {}
            },
            normHora(h) {
                const [hh, mm] = h.split(":");
                const H = String(
                    Math.max(0, Math.min(23, parseInt(hh || "0", 10)))
                ).padStart(2, "0");
                const M = String(
                    Math.max(0, Math.min(59, parseInt(mm || "0", 10)))
                ).padStart(2, "0");
                return `${H}:${M}`;
            },
            _isFutureDate(value) {
                if (!value) return false;
                const selected = new Date(`${value}T00:00:00`);
                if (Number.isNaN(selected.getTime())) return false;
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                return selected > today;
            },
            validateEmpresaForm() {
                this.errors = {};
                const add = (k, m) => {
                    (this.errors[k] || (this.errors[k] = [])).push(m);
                };
                if (
                    !this.formEmpresa.nombre_comercial ||
                    !this.formEmpresa.nombre_comercial.trim()
                )
                    add(
                        "nombre_comercial",
                        "El nombre comercial es obligatorio."
                    );
                if ((this.formEmpresa.nombre_comercial || "").length > 150)
                    add("nombre_comercial", "Máximo 150 caracteres.");
                if ((this.formEmpresa.razon_social || "").length > 150)
                    add("razon_social", "Máximo 150 caracteres.");
                if (
                    (this.formEmpresa.rtn || "").length > 0 &&
                    !/^[-0-9A-Za-z]{3,30}$/.test(this.formEmpresa.rtn || "")
                )
                    add(
                        "rtn",
                        "RTN inválido, use solo números/letras y guiones (3-30)."
                    );
                if ((this.formEmpresa.descripcion_empresa || "").length > 255)
                    add("descripcion_empresa", "Máximo 255 caracteres.");
                if (!this.formEmpresa.fecha_registro)
                    add("fecha_registro", "La fecha es obligatoria.");
                else if (this._isFutureDate(this.formEmpresa.fecha_registro))
                    add("fecha_registro", "No se permiten fechas futuras");

                const excludeId = this.formEmpresa.id || null;
                if (
                    this._isDuplicateLocal(
                        "nombre_comercial",
                        this.formEmpresa.nombre_comercial,
                        excludeId
                    )
                )
                    add("nombre_comercial", "El nombre comercial ya existe.");
                if (
                    this._isDuplicateLocal(
                        "razon_social",
                        this.formEmpresa.razon_social,
                        excludeId
                    )
                )
                    add("razon_social", "La razón social ya existe.");
                if (
                    this._isDuplicateLocal(
                        "rtn",
                        this.formEmpresa.rtn,
                        excludeId
                    )
                )
                    add("rtn", "El RTN ya existe.");
                const anyDay = Object.values(this.horarioUI.dias).some(Boolean);
                if (anyDay && this.horarioUI.desde >= this.horarioUI.hasta)
                    add(
                        "horario_atencion",
                        "La hora inicial debe ser menor que la final."
                    );
                this.syncHorarioString();
                if (
                    !["activo", "inactivo"].includes(
                        (this.formEmpresa.estado_cliente || "").toLowerCase()
                    )
                )
                    add("estado_cliente", "Estado inválido.");
                return Object.keys(this.errors).length === 0;
            },
        };
    };
}
