(function () {
    const DEFAULT_SIDEBAR_ORDER = [
        {
            id: "seguridad",
            title: "Seguridad",
            items: [
                "Usuarios",
                "Parámetros",
                "Parametros",
                "Configuración de accesos",
                "Configuracion de accesos",
            ],
        },
        {
            id: "clientes",
            title: "Clientes",
            items: [
                "Empresas",
                "Cotizaciones",
                "Solicitudes",
                "Órdenes de Servicios",
                "Ordenes de Servicios",
            ],
        },
        {
            id: "proyectos",
            title: "Proyectos",
            items: [
                "Proyectos",
                "Gestión de proyectos",
                "Gestion de proyectos",
                "Vista de proyectos",
            ],
        },
        {
            id: "tickets",
            title: "Tickets",
            items: ["Gestión de tickets", "Gestion de tickets", "Tickets"],
        },
        {
            id: "calendario",
            title: "Calendario",
            items: [
                "Agencias",
                "Calendario",
                "Gestión de Calendario",
                "Gestion de Calendario",
            ],
        },
        { id: "facturacion", title: "Facturación", items: ["Facturas", "CAI"] },
        {
            id: "reportes",
            title: "Reportes",
            items: ["Gestión de Reportes", "Gestion de Reportes", "Reportes"],
        },
        {
            id: "inventario",
            title: "Inventario",
            items: ["Productos", "Kardex"],
        },
        {
            id: "administracion",
            title: "Administración",
            items: [
                "Gestión de personas",
                "Gestion de personas",
                "Mi perfil",
                "Perfil",
                "Profile",
                "Bitácora",
                "Bitacora",
                "Gestión de base de datos",
                "Gestion de base de datos",
            ],
        },
        {
            id: "mantenimiento",
            title: "Mantenimiento",
            items: ["Mantenimiento del Sistema", "Mantenimiento del sistema"],
        },
        {
            id: "catalogo",
            title: "Catalogo",
            items: [
                "Acciones Realizadas",
                "Administración de Facturas",
                "Categorias de Ingresos y Gastos",
                "Categorías de Ingresos y Gastos",
                "Estados CAI",
                "Estados de Proyecto",
                "Estados de Solicitud",
                "Estados de Tickets",
                "Estados del Calendario",
                "Género",
                "Genero",
                "Servicio Factura",
                "Servicios Realizados",
                "Tipo de Movimiento",
                "Tipo de Objeto",
                "Tipo de Producto",
                "Tipo de Visita",
                "Ubicaciones",
            ],
        },
    ];

    const hydrateFromDataset = () => {
        const dataset = Array.isArray(window.__ADMIN_MODULES__)
            ? window.__ADMIN_MODULES__
            : [];
        if (!dataset.length) return [];
        return dataset
            .filter(
                (module) =>
                    module &&
                    module.key &&
                    module.key !== "dashboard" &&
                    Array.isArray(module.submodules) &&
                    module.submodules.length
            )
            .map((module) => {
                const names = new Set();
                (module.object_names || []).forEach((name) => {
                    if (name) names.add(name);
                });
                module.submodules.forEach((sub) => {
                    (sub.object_names || []).forEach((name) => {
                        if (name) names.add(name);
                    });
                });
                return {
                    id: module.key,
                    title: module.label || module.key,
                    items: Array.from(names.values()),
                };
            });
    };

    const SIDEBAR_ORDER = (() => {
        const fromConfig = hydrateFromDataset();
        return fromConfig.length ? fromConfig : DEFAULT_SIDEBAR_ORDER;
    })();

    const norm = (s) =>
        (s || "")
            .toString()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .toLowerCase()
            .trim();
    const SECURITY_MODULE_KEY = "seguridad";
    const ADMIN_ROLE_KEY = "administrador";
    const CLIENT_ROLE_KEY = "cliente";
    const HIDDEN_OBJECTS_BY_MODULE = (() => {
        const raw = new Map([[SECURITY_MODULE_KEY, ["permisos"]]]);
        const normalized = new Map();
        for (const [moduleKey, names] of raw.entries()) {
            const moduleNorm = norm(moduleKey || "");
            if (!moduleNorm) continue;
            const set = new Set();
            for (const name of names || []) {
                const nameNorm = norm(name || "");
                if (nameNorm) set.add(nameNorm);
            }
            if (set.size) {
                normalized.set(moduleNorm, set);
            }
        }
        return normalized;
    })();
    const SECURITY_OBJECT_NAMES = (() => {
        const names = new Set();

        for (const mod of SIDEBAR_ORDER) {
            if (!mod) continue;
            const modId = mod.id ? norm(mod.id) : "";
            const modTitle = mod.title ? norm(mod.title) : "";
            if (
                modId !== SECURITY_MODULE_KEY &&
                modTitle !== SECURITY_MODULE_KEY
            ) {
                continue;
            }
            if (modTitle) names.add(modTitle);
            if (Array.isArray(mod.items)) {
                for (const item of mod.items) {
                    const normalized = norm(item);
                    if (normalized) names.add(normalized);
                }
            }
            if (Array.isArray(mod.object_names)) {
                for (const objName of mod.object_names) {
                    const normalized = norm(objName);
                    if (normalized) names.add(normalized);
                }
            }
            if (Array.isArray(mod.submodules)) {
                for (const sub of mod.submodules) {
                    if (sub?.label) {
                        const normalizedLabel = norm(sub.label);
                        if (normalizedLabel) names.add(normalizedLabel);
                    }
                    if (Array.isArray(sub?.object_names)) {
                        for (const subObj of sub.object_names) {
                            const normalizedSub = norm(subObj);
                            if (normalizedSub) names.add(normalizedSub);
                        }
                    }
                }
            }
        }

        if (!names.size) {
            const fallback = [
                "seguridad",
                "configuracion de accesos",
                "gestion de usuarios",
                "usuarios",
                "parametros",
                "permisos",
            ];
            fallback.forEach((value) => names.add(value));
        }

        return names;
    })();

    const ALL_HIDDEN_OBJECT_NAMES = (() => {
        const set = new Set();
        for (const hidden of HIDDEN_OBJECTS_BY_MODULE.values()) {
            for (const name of hidden) {
                const nameNorm = norm(name || "");
                if (nameNorm) set.add(nameNorm);
            }
        }
        return set;
    })();

    const shouldHideObject = (moduleKeyLike, objectNameLike) => {
        const moduleNorm = norm(moduleKeyLike || "");
        const objectNorm = norm(objectNameLike || "");
        if (!moduleNorm || !objectNorm) return false;
        const hidden = HIDDEN_OBJECTS_BY_MODULE.get(moduleNorm);
        return hidden ? hidden.has(objectNorm) : false;
    };

    const isGloballyHiddenObject = (objectNameLike) => {
        const nameNorm = norm(objectNameLike || "");
        if (!nameNorm) return false;
        return ALL_HIDDEN_OBJECT_NAMES.has(nameNorm);
    };

    const HIDDEN_FALLBACK_GROUPS = new Set(["configuracion", "modulo"]);
    const API = {
        roles: "/api/roles",
        objetos: "/api/objetos",
        tipos: "/api/tipos-objeto",
        permisos: "/api/permisos",
        upsertPerm: (rolId, objId) =>
            `/api/permisos/roles/${rolId}/objetos/${objId}`,
    };

    const hasConfiguracionAcceso = () => {
        try {
            const main = document.querySelector("main");
            return (main?.dataset?.canConfiguracionAcceso || "") === "1";
        } catch (_) {
            return false;
        }
    };

    function mapObjeto(o) {
        return {
            id: o.id ?? o.id_objetos_pk ?? o.id_objeto_pk ?? o.id,
            nombre_objeto: o.nombre_objeto ?? o.nombre ?? "",
            id_tipo_objetos_fk: o.id_tipo_objetos_fk ?? o.tipo_id ?? null,
            ruta: o.ruta ?? null,
            clave_permiso: o.clave_permiso ?? o.clave ?? null,
            tipo: o.tipo || null,
            tipo_nombre: o.tipo?.nombre || o.tipo_nombre || null,
        };
    }

    const authHeaders = () => ({
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
    });

    async function apiGet(url, opts = {}) {
        const res = await fetch(url, {
            headers: authHeaders(),
            credentials: "same-origin",
            signal: opts.signal,
        });
        if (res.status === 403) {
            const err = new Error("Permiso denegado");
            err.status = 403;
            throw err;
        }
        if (!res.ok)
            throw new Error(await res.text().catch(() => res.statusText));
        return res.json();
    }
    async function apiGetList(url, opts = {}) {
        const data = await apiGet(url, opts);
        if (Array.isArray(data)) return data;
        if (Array.isArray(data?.data)) return data.data;
        return [];
    }
    async function apiSend(url, method, body, opts = {}) {
        const res = await fetch(url, {
            method,
            headers: authHeaders(),
            credentials: "same-origin",
            body: JSON.stringify(body),
            signal: opts.signal,
        });
        if (res.status === 403) {
            const err = new Error("Permiso denegado");
            err.status = 403;
            throw err;
        }
        if (!res.ok)
            throw new Error(await res.text().catch(() => res.statusText));
        return res.json();
    }

    function normalizeCollection(payload) {
        if (Array.isArray(payload)) return payload;
        if (Array.isArray(payload?.data)) return payload.data;
        return [];
    }

    function createAccessStore() {
        return {
            loading: false,
            error: "",
            roles: [],
            objetos: [],
            tipos: [],
            selectedRoleId: null,

            _roleLoadPromise: null,
            _roleLoadingId: null,
            _selectRoleDebounce: null,
            _fetchPermRetries: 3,
            _initialized: false,
            blocked: false,
            pending: {},
            commitTimers: {},
            permsByObj: {},
            _lastGuardToast: 0,
            permColumns: [
                { field: "permiso_ver", label: "Ver" },
                { field: "permiso_consultar", label: "Leer" },
                { field: "permiso_insercion", label: "Crear" },
                { field: "permiso_actualizar", label: "Editar" },
                { field: "permiso_eliminacion", label: "Eliminar" },
            ],

            async init() {
                if (this.blocked) return;
                if (!hasConfiguracionAcceso()) {
                    this.blocked = true;
                    this.error = "No tienes permisos para ver esta sección";
                    this.roles = [];
                    this.objetos = [];
                    this.tipos = [];
                    return;
                }
                try {
                    this.loading = true;
                    this.error = "";
                    this._initialized = true;
                    const [rolesRes, objetosRes, tiposRes] = await Promise.all([
                        apiGet(`${API.roles}?all=1`),
                        apiGet(`${API.objetos}?all=1`),
                        apiGet(`${API.tipos}?all=1`),
                    ]);
                    const rawRoles = normalizeCollection(rolesRes);
                    const filteredRoles = rawRoles.filter(
                        (role) => norm(role?.rol || "") !== CLIENT_ROLE_KEY
                    );
                    this.roles = filteredRoles;
                    if (
                        this.selectedRoleId &&
                        !filteredRoles.some(
                            (role) => role?.id === this.selectedRoleId
                        )
                    ) {
                        this.selectedRoleId = null;
                    }
                    this.objetos =
                        normalizeCollection(objetosRes).map(mapObjeto);
                    this.tipos = normalizeCollection(tiposRes).map((t) => ({
                        id:
                            t.id ??
                            t.id_tipo_objeto_pk ??
                            t.id_tipo_objetos_fk ??
                            t.id,
                        nombre: t.nombre || t.nombre_tipo_objeto || "",
                        clave_permiso: t.clave_permiso || null,
                    }));
                    if (this.roles.length && !this.selectedRoleId) {
                        await this.selectRole(this.roles[0].id);
                    }
                } catch (e) {
                    if (e && e.status === 403) {
                        this.blocked = true;
                        this.error = "No tienes permisos para ver esta sección";
                        return;
                    }
                    this.error = parseErr(e);
                } finally {
                    this.loading = false;
                }
            },

            async selectRole(roleId) {
                if (this.blocked) return;
                if (!this.roles.some((role) => role?.id === roleId)) return;
                this.selectedRoleId = roleId;
                if (this._selectRoleDebounce)
                    clearTimeout(this._selectRoleDebounce);
                this._selectRoleDebounce = setTimeout(() => {
                    this.ensureRolePerms(roleId);
                }, 120);
            },

            async ensureRolePerms(roleId) {
                if (this.blocked) return;
                if (!roleId) return;
                try {
                    await this.loadPermisosForRole(roleId);
                } catch (_) {}
            },

            objetosByTipo(tipoId) {
                const tid = tipoId ?? 0;
                return this.objetos.filter(
                    (o) => (o.id_tipo_objetos_fk ?? 0) === tid
                );
            },
            grupos() {
                const assigned = new Set();
                const groups = [];
                const objetosByName = this.objetos.slice();
                const nameMap = new Map();
                for (const o of objetosByName) {
                    nameMap.set(o.id, norm(o.nombre_objeto));
                }

                for (const mod of SIDEBAR_ORDER) {
                    const labelOrder = mod.items.map((s) => norm(s));
                    const bucket = [];

                    const moduleTitle = norm(mod.title);
                    const moduleKeyLike = mod.id ?? mod.title;
                    let moduleObjId = null;
                    for (const [id, n] of nameMap.entries()) {
                        if (n === moduleTitle) {
                            moduleObjId = id;
                            break;
                        }
                    }

                    for (const o of objetosByName) {
                        if (assigned.has(o.id)) continue;
                        const n = nameMap.get(o.id);
                        if (shouldHideObject(moduleKeyLike, n)) continue;
                        if (labelOrder.includes(n)) {
                            if (moduleObjId != null && o.id === moduleObjId)
                                continue;
                            bucket.push(o);
                            assigned.add(o.id);
                        }
                    }

                    bucket.sort((a, b) => {
                        const ia = labelOrder.indexOf(nameMap.get(a.id));
                        const ib = labelOrder.indexOf(nameMap.get(b.id));
                        if (ia !== ib) return ia - ib;
                        return (a.nombre_objeto || "").localeCompare(
                            b.nombre_objeto || ""
                        );
                    });
                    groups.push({
                        id: mod.id,
                        nombre: mod.title,
                        objetos: bucket,
                        moduleObjId,
                    });
                }

                const restantes = this.objetos.filter(
                    (o) => !assigned.has(o.id)
                );
                if (restantes.length) {
                    const byTipo = new Map();
                    for (const o of restantes) {
                        if (isGloballyHiddenObject(o.nombre_objeto)) continue;
                        const tname =
                            (o.tipo?.nombre || o.tipo_nombre || "Otros") + "";
                        if (!byTipo.has(tname)) byTipo.set(tname, []);
                        byTipo.get(tname).push(o);
                    }
                    for (const [tname, arr] of byTipo) {
                        if (HIDDEN_FALLBACK_GROUPS.has(norm(tname))) continue;
                        arr.sort((a, b) =>
                            (a.nombre_objeto || "").localeCompare(
                                b.nombre_objeto || ""
                            )
                        );
                        groups.push({
                            id: `otros-${norm(tname)}`,
                            nombre: tname || "Otros",
                            objetos: arr,
                        });
                    }
                }
                return groups;
            },
            currentRole() {
                return (
                    this.roles.find(
                        (role) => role?.id === this.selectedRoleId
                    ) || null
                );
            },
            isAdminSelected() {
                const role = this.currentRole();
                if (!role) return false;
                return norm(role.rol || "") === ADMIN_ROLE_KEY;
            },
            isSecurityObject(objId) {
                const obj = this.objetos.find((o) => o.id === objId);
                if (!obj) return false;
                const normalized = norm(obj.nombre_objeto || "");
                return !!normalized && SECURITY_OBJECT_NAMES.has(normalized);
            },
            isProtectedModule(groupId) {
                return (
                    this.isAdminSelected() &&
                    norm(groupId || "") === SECURITY_MODULE_KEY
                );
            },
            shouldBlockSecurityToggle(objId, nextValue) {
                if (!this.isAdminSelected()) return false;
                if (!this.isSecurityObject(objId)) return false;
                return nextValue === false;
            },
            notifySecurityGuard() {
                const now = Date.now();
                if (now - this._lastGuardToast < 800) return;
                this._lastGuardToast = now;
                try {
                    window.showToast &&
                        window.showToast(
                            "El rol Administrador debe conservar los permisos de Seguridad.",
                            "warning"
                        );
                } catch (_) {}
            },
            moduloTieneAcceso(groupId) {
                const g = this.grupos().find((x) => x.id === groupId);
                if (!g) return false;

                if (g.moduleObjId != null) {
                    return this.isChecked(g.moduleObjId, "permiso_ver");
                }

                const objs = g.objetos || [];
                for (const o of objs) {
                    if (this.isChecked(o.id, "permiso_ver")) return true;
                }
                return false;
            },
            async toggleModulo(groupId, desired) {
                if (this.blocked) return;
                const g = this.grupos().find((x) => x.id === groupId);
                if (!g) return;
                const target = !!desired;
                if (this.isProtectedModule(g.id) && !target) {
                    this.notifySecurityGuard();
                    return;
                }

                if (g.moduleObjId != null) {
                    const cur = this.isChecked(g.moduleObjId, "permiso_ver");
                    if (cur !== target) {
                        const rec = this.permsByObj[g.moduleObjId];
                        if (rec) {
                            rec.permiso_ver = target;
                            this.scheduleCommit(g.moduleObjId, "permiso_ver");
                        } else {
                            await this.toggle(g.moduleObjId, "permiso_ver");
                        }
                    }
                    return;
                }

                const objs = g.objetos || [];
                for (const o of objs) {
                    const cur = this.isChecked(o.id, "permiso_ver");
                    if (cur !== target) {
                        await this.toggle(o.id, "permiso_ver");
                    }
                }
            },

            async loadPermisosForRole(roleId) {
                if (this.blocked) return;
                if (!hasConfiguracionAcceso()) {
                    this.blocked = true;
                    this.error = "No tienes permisos para ver esta sección";
                    return;
                }
                if (this._roleLoadPromise && this._roleLoadingId === roleId) {
                    return this._roleLoadPromise;
                }
                this._roleLoadingId = roleId;
                const attemptFetch = async () => {
                    for (let i = 0; i < this._fetchPermRetries; i++) {
                        try {
                            const res = await fetch(
                                `${
                                    API.permisos
                                }?all=1&id_rol_fk=${encodeURIComponent(
                                    roleId
                                )}`,
                                {
                                    headers: authHeaders(),
                                    credentials: "same-origin",
                                }
                            );
                            if (res.status === 429) {
                                await new Promise((r) =>
                                    setTimeout(r, 250 * (i + 1))
                                );
                                continue;
                            }
                            if (!res.ok)
                                throw new Error(
                                    res.status + ": " + res.statusText
                                );
                            return await res.json();
                        } catch (err) {
                            if (i === this._fetchPermRetries - 1) throw err;
                        }
                    }
                    throw new Error("No se pudieron cargar permisos");
                };
                this.loading = true;
                this.error = "";
                this._roleLoadPromise = attemptFetch()
                    .then((payload) => {
                        const list = normalizeCollection(payload);
                        const byObj = {};
                        for (const o of this.objetos) {
                            byObj[o.id] = {
                                id: null,
                                id_rol_fk: roleId,
                                id_objeto_fk: o.id,
                                permiso_ver: false,
                                permiso_consultar: false,
                                permiso_insercion: false,
                                permiso_actualizar: false,
                                permiso_eliminacion: false,
                            };
                        }
                        for (const p of list) {
                            const objId = p.id_objeto_fk || p.objeto?.id;
                            if (objId && byObj[objId]) {
                                byObj[objId] = {
                                    id: p.id ?? p.id_permiso_pk ?? null,
                                    id_rol_fk: p.id_rol_fk ?? roleId,
                                    id_objeto_fk: objId,
                                    permiso_ver: !!p.permiso_ver,
                                    permiso_consultar: !!p.permiso_consultar,
                                    permiso_insercion: !!p.permiso_insercion,
                                    permiso_actualizar: !!p.permiso_actualizar,
                                    permiso_eliminacion:
                                        !!p.permiso_eliminacion,
                                };
                            }
                        }
                        this.permsByObj = byObj;
                    })
                    .catch((e) => {
                        this.error = parseErr(e);
                        throw e;
                    })
                    .finally(() => {
                        this.loading = false;
                        this._roleLoadPromise = null;
                    });
                return this._roleLoadPromise;
            },

            isChecked(objId, field) {
                return !!this.permsByObj?.[objId]?.[field];
            },

            keyFor(objId, field) {
                return `${objId}:${field}`;
            },
            isPending(objId, field) {
                return !!this.pending[this.keyFor(objId, field)];
            },
            cancelPending(objId, field) {
                const key = this.keyFor(objId, field);
                const p = this.pending[key];
                if (p?.controller) {
                    try {
                        p.controller.abort();
                    } catch (_) {}
                }
                delete this.pending[key];
            },

            async toggle(objId, field) {
                if (this.blocked) return;
                const roleId = this.selectedRoleId;
                if (!roleId) return;
                const rec = this.permsByObj[objId];
                if (!rec) return;
                const prev = rec[field];
                const next = !prev;
                if (this.shouldBlockSecurityToggle(objId, next)) {
                    this.notifySecurityGuard();
                    return;
                }
                rec[field] = next;
                this.scheduleCommit(objId, field);
            },

            scheduleCommit(objId, field) {
                const key = this.keyFor(objId, field);

                if (this.commitTimers[key]) return;
                this.commitTimers[key] = setTimeout(() => {
                    delete this.commitTimers[key];
                    this.commitNow(objId, field);
                }, 280);
            },

            async commitNow(objId, field) {
                if (this.blocked) return;
                if (!hasConfiguracionAcceso()) {
                    this.blocked = true;
                    this.error = "No tienes permisos para ver esta sección";
                    return;
                }
                const roleId = this.selectedRoleId;
                if (!roleId) return;
                const rec = this.permsByObj[objId];
                if (!rec) return;
                const desired = !!rec[field];
                const key = this.keyFor(objId, field);
                const old = this.pending[key];
                if (old) {
                    try {
                        old.controller.abort();
                    } catch (_) {}
                }
                const controller = new AbortController();
                const token = Symbol("toggle");
                this.pending[key] = { controller, token };
                let saved = false;
                try {
                    if (rec.id) {
                        const payload = { [field]: desired };
                        const updated = await apiSend(
                            `${API.permisos}/${rec.id}`,
                            "PUT",
                            payload,
                            { signal: controller.signal }
                        );
                        const data = updated?.data || updated;
                        if (data && this.pending[key]?.token === token) {
                            rec.permiso_ver = !!data.permiso_ver;
                            rec.permiso_consultar = !!data.permiso_consultar;
                            rec.permiso_insercion = !!data.permiso_insercion;
                            rec.permiso_actualizar = !!data.permiso_actualizar;
                            rec.permiso_eliminacion =
                                !!data.permiso_eliminacion;
                        }
                    } else {
                        const payload = { [field]: desired };
                        try {
                            const updated = await apiSend(
                                API.upsertPerm(roleId, objId),
                                "PUT",
                                payload,
                                { signal: controller.signal }
                            );
                            const data = updated?.data || updated;
                            if (data && this.pending[key]?.token === token) {
                                rec.id = data.id ?? rec.id;
                                rec.permiso_ver = !!data.permiso_ver;
                                rec.permiso_consultar =
                                    !!data.permiso_consultar;
                                rec.permiso_insercion =
                                    !!data.permiso_insercion;
                                rec.permiso_actualizar =
                                    !!data.permiso_actualizar;
                                rec.permiso_eliminacion =
                                    !!data.permiso_eliminacion;
                            }
                        } catch (err) {
                            const existing = await apiGetList(
                                `${
                                    API.permisos
                                }?all=1&id_rol_fk=${encodeURIComponent(
                                    roleId
                                )}&id_objeto_fk=${encodeURIComponent(objId)}`,
                                { signal: controller.signal }
                            );
                            const first = existing[0];
                            if (first) {
                                const foundId = first.id ?? first.id_permiso_pk;
                                if (foundId) {
                                    const full = {
                                        permiso_ver: !!rec.permiso_ver,
                                        permiso_consultar:
                                            !!rec.permiso_consultar,
                                        permiso_insercion:
                                            !!rec.permiso_insercion,
                                        permiso_actualizar:
                                            !!rec.permiso_actualizar,
                                        permiso_eliminacion:
                                            !!rec.permiso_eliminacion,
                                    };
                                    const upd = await apiSend(
                                        `${API.permisos}/${foundId}`,
                                        "PUT",
                                        full,
                                        { signal: controller.signal }
                                    );
                                    const d2 = upd?.data || upd;
                                    if (this.pending[key]?.token === token) {
                                        rec.id = foundId;
                                        rec.permiso_ver = !!d2.permiso_ver;
                                        rec.permiso_consultar =
                                            !!d2.permiso_consultar;
                                        rec.permiso_insercion =
                                            !!d2.permiso_insercion;
                                        rec.permiso_actualizar =
                                            !!d2.permiso_actualizar;
                                        rec.permiso_eliminacion =
                                            !!d2.permiso_eliminacion;
                                    }
                                } else {
                                    await this.loadPermisosForRole(roleId);
                                }
                            } else {
                                const createPayload = {
                                    id_rol_fk: roleId,
                                    id_objeto_fk: objId,
                                    permiso_ver: !!rec.permiso_ver,
                                    permiso_consultar: !!rec.permiso_consultar,
                                    permiso_insercion: !!rec.permiso_insercion,
                                    permiso_actualizar:
                                        !!rec.permiso_actualizar,
                                    permiso_eliminacion:
                                        !!rec.permiso_eliminacion,
                                };
                                const created = await apiSend(
                                    API.permisos,
                                    "POST",
                                    createPayload,
                                    { signal: controller.signal }
                                );
                                const cd = created?.data || created;
                                if (
                                    cd?.id &&
                                    this.pending[key]?.token === token
                                )
                                    rec.id = cd.id;
                            }
                        }
                    }
                    saved = true;
                } catch (e) {
                    if (
                        e &&
                        (e.name === "AbortError" ||
                            /aborted|abort/i.test(e.message || ""))
                    ) {
                        return;
                    }
                    if (this.pending[key]?.token === token) {
                        rec[field] = !desired;
                    }
                    const message = parseErr(e);
                    this.error = message;
                    const toastMessage = /administrador/i.test(message)
                        ? message
                        : "No se pudo guardar el cambio de permiso";
                    try {
                        window.showToast &&
                            window.showToast(toastMessage, "error");
                    } catch (_) {}
                    setTimeout(() => {
                        this.error = "";
                    }, 2500);
                } finally {
                    if (this.pending[key]?.token === token) {
                        delete this.pending[key];
                    }
                }
                if (saved) {
                    try {
                        window.showToast &&
                            window.showToast("Permiso actualizado", "success", {
                                duration: 2500,
                            });
                    } catch (_) {}
                }
            },
        };
    }

    function parseErr(e) {
        const msg = e && e.message ? e.message : String(e || "Error");
        return msg.length > 300 ? msg.slice(0, 300) + "…" : msg;
    }

    document.addEventListener("alpine:init", () => {
        const store = createAccessStore();
        Alpine.store("access", store);
        const shouldInit = () => {
            try {
                const current =
                    document.querySelector("main")?.dataset?.currentView || "";
                return current === "configuracion-acceso";
            } catch (_) {
                return false;
            }
        };

        const ensureInit = () => {
            if (store.blocked) return;
            if (!hasConfiguracionAcceso()) {
                store.blocked = true;
                store.error = "No tienes permisos para ver esta sección";
                store.roles = [];
                store.objetos = [];
                store.tipos = [];
                return;
            }

            if (!shouldInit() && store._initialized) {
                return;
            }
            if (!store._initialized) {
                store._initialized = true;
                setTimeout(() => {
                    store.init().catch(() => {});
                }, 0);
            }
        };

        ensureInit();
        document.addEventListener("app:view-loaded", ensureInit);
    });
})();
