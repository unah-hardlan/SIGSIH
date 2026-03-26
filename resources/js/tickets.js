window.ticketsApiHandlers = {
    headers() {
        let tz = "UTC";
        try {
            tz = Intl.DateTimeFormat().resolvedOptions().timeZone || "UTC";
        } catch (_) { }
        return {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-Timezone": tz,
        };
    },
    toMysqlDateTime(value) {
        try {
            if (!value) {
                const d = new Date();
                const pad = (n) => String(n).padStart(2, "0");
                return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(
                    d.getDate()
                )} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(
                    d.getSeconds()
                )}`;
            }

            let s = String(value).trim().replace("T", " ");
            if (/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/.test(s)) s += ":00";
            return s;
        } catch (_) {
            return value;
        }
    },
    async fetchCatalogs(component) {
        try {
            const [estResp, perResp, cliResp, tecResp] = await Promise.all([
                fetch("/api/estados-ticket?all=1&sort=orden", {
                    headers: this.headers(),
                    credentials: "same-origin",
                }),
                fetch("/api/personas?all=1&sort=nombre", {
                    headers: this.headers(),
                    credentials: "same-origin",
                }),
                fetch("/api/clientes?all=1", {
                    headers: this.headers(),
                    credentials: "same-origin",
                }),
                fetch("/api/tecnicos", {
                    headers: this.headers(),
                    credentials: "same-origin",
                }),
            ]);
            const [estData, perData, cliData, tecData] = await Promise.all([
                estResp.json().catch(() => ({})),
                perResp.json().catch(() => ({})),
                cliResp.json().catch(() => ({})),
                tecResp.json().catch(() => ({})),
            ]);
            component.estadosTicket = Array.isArray(estData?.data)
                ? estData.data
                : Array.isArray(estData)
                    ? estData
                    : [];
            component.personas = Array.isArray(perData?.data)
                ? perData.data
                : Array.isArray(perData)
                    ? perData
                    : [];
            component.clientes = Array.isArray(cliData?.data)
                ? cliData.data
                : Array.isArray(cliData)
                    ? cliData
                    : [];
            component.tecnicos = Array.isArray(tecData?.data)
                ? tecData.data
                : Array.isArray(tecData)
                    ? tecData
                    : [];

            if (
                !Array.isArray(component.tecnicos) ||
                component.tecnicos.length === 0
            ) {
                const plain = (s) => {
                    try {
                        return String(s || "")
                            .toLowerCase()
                            .normalize("NFD")
                            .replace(/[\u0300-\u036f]/g, "");
                    } catch (_) {
                        return String(s || "").toLowerCase();
                    }
                };
                let tecnicoUserIds = new Set();
                try {
                    const rolesResp = await fetch("/api/roles?per_page=1000", {
                        headers: this.headers(),
                        credentials: "same-origin",
                    });
                    const rolesJson = await rolesResp.json().catch(() => ({}));
                    const rolesItems = Array.isArray(rolesJson?.data?.data)
                        ? rolesJson.data.data
                        : Array.isArray(rolesJson?.data)
                            ? rolesJson.data
                            : [];
                    const roleTecnico = (rolesItems || []).find(
                        (r) => plain(r?.rol) === "tecnico"
                    );
                    if (roleTecnico?.id_rol_pk) {
                        const usersResp = await fetch(
                            `/api/roles/${roleTecnico.id_rol_pk}/usuarios?all=1`,
                            {
                                headers: this.headers(),
                                credentials: "same-origin",
                            }
                        );
                        const usersJson = await usersResp
                            .json()
                            .catch(() => ({}));
                        const usersItems = Array.isArray(usersJson?.data)
                            ? usersJson.data
                            : Array.isArray(usersJson)
                                ? usersJson
                                : [];
                        tecnicoUserIds = new Set(
                            (usersItems || []).map((u) => String(u.id))
                        );
                    }
                } catch (_) { }
                if (tecnicoUserIds.size === 0) {
                    try {
                        const users = await fetch(
                            "/api/usuarios?per_page=1000&estado=ACTIVO",
                            {
                                headers: this.headers(),
                                credentials: "same-origin",
                            }
                        );
                        const usersData = await users.json().catch(() => ({}));
                        const usuarios = Array.isArray(usersData?.data?.data)
                            ? usersData.data.data
                            : Array.isArray(usersData?.data)
                                ? usersData.data
                                : Array.isArray(usersData)
                                    ? usersData
                                    : [];
                        tecnicoUserIds = new Set(
                            (usuarios || [])
                                .filter((u) => plain(u?.rol) === "tecnico")
                                .map((u) => String(u.id))
                        );
                    } catch (_) { }
                }
                component.tecnicos = (component.personas || []).filter((p) =>
                    tecnicoUserIds.has(String(p.id_usuario_fk))
                );
            }
        } catch (e) {
            console.error("Error cargando catálogos tickets:", e);
        }
    },
    nombrePersona(p) {
        if (!p) return "";
        const n = [
            p.primer_nombre,
            p.segundo_nombre,
            p.primer_apellido,
            p.segundo_apellido,
        ]
            .filter(Boolean)
            .join(" ")
            .trim();
        return n || p.nombre || "";
    },
    async fetchTickets(component) {
        component.loading = true;
        try {
            const p = new URLSearchParams();
            if (component.search) p.set("descripcion_ticket", component.search);
            if (component.filtroEstado)
                p.set("id_estado_ticket_fk", component.filtroEstado);
            if (component.filtroTecnico)
                p.set("id_tecnico_fk", component.filtroTecnico);
            if (component.filtroCliente)
                p.set("id_cliente_fk", component.filtroCliente);
            if (component.desde) p.set("fecha_desde", component.desde);
            if (component.hasta) p.set("fecha_hasta", component.hasta);
            p.set("per_page", "200");
            const resp = await fetch(`/api/tickets?${p.toString()}`, {
                headers: this.headers(),
                credentials: "same-origin",
            });
            const data = await resp.json().catch(() => ({}));
            if (!resp.ok) throw data;
            const items = Array.isArray(data?.data?.data)
                ? data.data.data
                : Array.isArray(data?.data)
                    ? data.data
                    : [];
            const estados = component.estadosTicket || [];
            const personas = component.personas || [];
            const tecnicos = component.tecnicos || personas;
            const clientes = component.clientes || [];
            component.tickets = items.map((t) => {
                const estadoNombre =
                    t.estado?.nombre ||
                    estados.find(
                        (e) =>
                            String(e.id_estado_ticket_pk) ===
                            String(t.id_estado_ticket_fk)
                    )?.nombre ||
                    "";
                const tecnicoNombre =
                    this.nombrePersona(t.tecnico) ||
                    this.nombrePersona(
                        tecnicos.find(
                            (per) => String(per.id) === String(t.id_tecnico_fk)
                        ) ||
                        personas.find(
                            (per) =>
                                String(per.id) === String(t.id_tecnico_fk)
                        )
                    );

                const clienteNombre =
                    t.cliente?.nombre ||
                    clientes.find(
                        (c) => String(c.id) === String(t.id_cliente_fk)
                    )?.nombre ||
                    "";
                return {
                    id_ticket_pk: t.id_ticket_pk,
                    fecha_creacion: t.fecha_creacion,
                    descripcion_ticket: t.descripcion_ticket,
                    id_estado_ticket_fk: t.id_estado_ticket_fk,
                    id_tecnico_fk: t.id_tecnico_fk,
                    id_cliente_fk: t.id_cliente_fk,
                    estado_nombre: estadoNombre,
                    tecnico_nombre: tecnicoNombre,
                    cliente_nombre: clienteNombre,
                };
            });

            const key = component.ordenarPor || "id";
            const dir =
                (component.ordenarDirection || "desc") === "asc" ? 1 : -1;
            const sorters = {
                id: (a, b) =>
                    (Number(a.id_ticket_pk) - Number(b.id_ticket_pk)) * dir,
                cliente: (a, b) =>
                    a.cliente_nombre.localeCompare(b.cliente_nombre, "es") *
                    dir,
                fecha: (a, b) =>
                    (new Date(a.fecha_creacion) - new Date(b.fecha_creacion)) *
                    dir,
                estado: (a, b) =>
                    a.estado_nombre.localeCompare(b.estado_nombre, "es") * dir,
            };
            if (sorters[key]) component.tickets.sort(sorters[key]);
        } catch (e) {
            console.error("Error cargando tickets:", e);
            window.showToast &&
                window.showToast("Error al cargar tickets", "error");
        } finally {
            component.loading = false;
        }
    },
    async store(component) {
        if (component.savingTicket) {
            return;
        }

        if (
            !component.new_fecha_creacion ||
            !component.new_descripcion_ticket ||
            !component.new_id_estado_ticket_fk ||
            !component.new_id_tecnico_fk ||
            !component.new_id_cliente_fk
        ) {
            return (
                window.showToast &&
                window.showToast("Completa los campos requeridos", "error")
            );
        }

        try {
            const selected = new Date(component.new_fecha_creacion);
            if (!Number.isNaN(selected.getTime())) {
                const selectedDay = new Date(selected);
                selectedDay.setHours(0, 0, 0, 0);

                const today = new Date();
                today.setHours(0, 0, 0, 0);

                if (selectedDay.getTime() < today.getTime()) {
                    return (
                        window.showToast &&
                        window.showToast(
                            "No se permiten fechas anteriores al día actual",
                            "error"
                        )
                    );
                }
            }
        } catch (_) { }

        component.savingTicket = true;
        try {
            const payload = {
                fecha_creacion: this.toMysqlDateTime(
                    component.new_fecha_creacion
                ),
                descripcion_ticket: component.new_descripcion_ticket?.trim(),
                id_estado_ticket_fk: Number(component.new_id_estado_ticket_fk),
                id_tecnico_fk: Number(component.new_id_tecnico_fk),
                id_cliente_fk: Number(component.new_id_cliente_fk),
            };
            const resp = await fetch("/api/tickets", {
                method: "POST",
                headers: this.headers(),
                credentials: "same-origin",
                body: JSON.stringify(payload),
            });
            const data = await resp.json().catch(() => ({}));
            if (!resp.ok) throw data;
            window.showToast && window.showToast("Ticket creado", "success");
            component.isModalOpen = false;
            component.new_fecha_creacion = "";
            component.new_descripcion_ticket = "";
            component.new_id_estado_ticket_fk = "";
            component.new_id_tecnico_fk = "";
            component.new_id_cliente_fk = "";
            await this.fetchTickets(component);
        } catch (e) {
            const msg =
                Object.values(e?.errors || {})?.[0]?.[0] ||
                e?.message ||
                "Error al crear";
            window.showToast && window.showToast(msg, "error");
        } finally {
            component.savingTicket = false;
        }
    },
    async update(component) {
        if (!component.ticketToEdit?.id_ticket_pk) return;
        try {
            const payload = {
                fecha_creacion: component.edit_fecha_creacion
                    ? this.toMysqlDateTime(component.edit_fecha_creacion)
                    : undefined,
                descripcion_ticket: component.edit_descripcion_ticket?.trim(),
                id_estado_ticket_fk: Number(component.edit_id_estado_ticket_fk),
                id_tecnico_fk: Number(component.edit_id_tecnico_fk),
                id_cliente_fk: Number(component.edit_id_cliente_fk),
            };
            const resp = await fetch(
                `/api/tickets/${component.ticketToEdit.id_ticket_pk}`,
                {
                    method: "PUT",
                    headers: this.headers(),
                    credentials: "same-origin",
                    body: JSON.stringify(payload),
                }
            );
            const data = await resp.json().catch(() => ({}));
            if (!resp.ok) throw data;
            window.showToast &&
                window.showToast("Ticket actualizado", "success");
            component.isEditModalOpen = false;
            component.ticketToEdit = null;
            await this.fetchTickets(component);
        } catch (e) {
            const msg =
                e?.message ||
                Object.values(e?.errors || {})?.[0]?.[0] ||
                "Error al actualizar";
            window.showToast && window.showToast(msg, "error");
        }
    },
    async remove(component) {
        if (!component.ticketToDelete?.id_ticket_pk) return;
        try {
            const resp = await fetch(
                `/api/tickets/${component.ticketToDelete.id_ticket_pk}`,
                {
                    method: "DELETE",
                    headers: this.headers(),
                    credentials: "same-origin",
                }
            );
            const data = await resp.json().catch(() => ({}));
            if (!resp.ok) throw data;
            window.showToast && window.showToast("Ticket eliminado", "success");
            component.isDeleteModalOpen = false;
            component.ticketToDelete = null;
            await this.fetchTickets(component);
        } catch (e) {
            const msg = e?.error || "Error al eliminar";
            window.showToast && window.showToast(msg, "error");
        }
    },
};
