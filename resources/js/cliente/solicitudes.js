if (typeof window.solicitudesCliente === "undefined") {
    window.solicitudesCliente = function () {
        return {
            solicitudes: [],
            filtros: {
                search: "",
                estado: "",
            },
            modalNueva: false,
            modalDetalle: false,
            solicitudActual: null,
            nuevaSolicitud: {
                nombre_solicitud: "",
                descripcion_problema: "",
                correo_contacto:
                    document
                        .querySelector('meta[name="user-email"]')
                        ?.getAttribute("content") || "",
            },

            get resumen() {
                const norm = (v) => (v ?? "").toString().trim().toLowerCase();
                const enEspera = this.solicitudes.filter((s) =>
                    ["pendiente", "en espera", "espera"].includes(
                        norm(s.estado)
                    )
                ).length;
                const asignadas = this.solicitudes.filter((s) =>
                    ["asignada", "asignadas", "asignado", "asignados"].includes(
                        norm(s.estado)
                    )
                ).length;
                const enProceso = this.solicitudes.filter((s) =>
                    ["en proceso", "proceso"].includes(norm(s.estado))
                ).length;
                const rechazadas = this.solicitudes.filter((s) =>
                    [
                        "rechazada",
                        "rechazadas",
                        "rechazado",
                        "rechazados",
                    ].includes(norm(s.estado))
                ).length;
                const finalizadas = this.solicitudes.filter((s) =>
                    [
                        "finalizada",
                        "finalizadas",
                        "finalizado",
                        "finalizados",
                        "resuelta",
                        "resueltas",
                        "resuelto",
                        "resueltos",
                        "cerrada",
                        "cerradas",
                        "cerrado",
                        "cerrados",
                    ].includes(norm(s.estado))
                ).length;
                return {
                    total: this.solicitudes.length,
                    enEspera,
                    asignadas,
                    enProceso,
                    rechazadas,
                    finalizadas,
                };
            },

            get solicitudesFiltradas() {
                const sTerm = (this.filtros.search || "")
                    .toString()
                    .toLowerCase();
                const norm = (v) => (v ?? "").toString().toLowerCase();
                return this.solicitudes.filter((solicitud) => {
                    const hay = [
                        norm(solicitud.descripcion_problema),
                        norm(
                            solicitud.numero_solicitud_acf_fmt ??
                                solicitud.numero_solicitud_acf
                        ),
                        norm(
                            solicitud.numero_solicitud_cliente_fmt ??
                                solicitud.numero_solicitud_cliente
                        ),
                        norm(solicitud.nombre_solicitud),
                    ].some((v) => v.includes(sTerm));

                    let matchEstado = true;
                    if (this.filtros.estado) {
                        const estadoSolicitud = norm(solicitud.estado);
                        switch (this.filtros.estado) {
                            case "Pendiente":
                                matchEstado = [
                                    "pendiente",
                                    "en espera",
                                    "espera",
                                ].some((e) => estadoSolicitud.includes(e));
                                break;
                            case "En Proceso":
                                matchEstado = [
                                    "asignada",
                                    "asignado",
                                    "asignadas",
                                    "asignados",
                                ].some((e) => estadoSolicitud.includes(e));
                                break;
                            case "Resuelta":
                                matchEstado = ["en proceso", "proceso"].some(
                                    (e) => estadoSolicitud.includes(e)
                                );
                                break;
                            case "Rechazada":
                                matchEstado = [
                                    "rechazada",
                                    "rechazado",
                                    "rechazadas",
                                    "rechazados",
                                ].some((e) => estadoSolicitud.includes(e));
                                break;
                            case "Cerrada":
                                matchEstado = [
                                    "finalizada",
                                    "finalizado",
                                    "finalizadas",
                                    "finalizados",
                                    "resuelta",
                                    "resuelto",
                                    "resueltas",
                                    "resueltos",
                                    "cerrada",
                                    "cerrado",
                                    "cerradas",
                                    "cerrados",
                                ].some((e) => estadoSolicitud.includes(e));
                                break;
                            default:
                                matchEstado = true;
                        }
                    }

                    return (!sTerm || hay) && matchEstado;
                });
            },

            async init() {
                try {
                    const res = await fetch("/cliente/solicitudes-data", {
                        credentials: "same-origin",
                    });
                    const json = await res.json();
                    this.solicitudes = json.data || [];
                } catch (e) {
                    console.error(e);
                }
            },

            verDetalle(solicitud) {
                this.solicitudActual = solicitud;
                this.modalDetalle = true;
            },

            async crearSolicitud() {
                try {
                    const res = await fetch("/cliente/solicitudes", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                            Accept: "application/json",
                        },
                        credentials: "same-origin",
                        body: JSON.stringify(this.nuevaSolicitud),
                    });
                    const json = await res.json();
                    if (!res.ok || !json.success)
                        throw new Error(json.message || "Error");
                    this.solicitudes.unshift(json.data);
                    window.showToast?.(
                        "Solicitud creada y ticket generado",
                        "success"
                    );
                    this.modalNueva = false;
                    this.nuevaSolicitud = {
                        nombre_solicitud: "",
                        descripcion_problema: "",
                        correo_contacto:
                            document
                                .querySelector('meta[name="user-email"]')
                                ?.getAttribute("content") || "",
                    };
                } catch (e) {
                    console.error(e);
                    window.showToast?.(
                        e.message || "No se pudo crear la solicitud",
                        "error"
                    );
                }
            },
        };
    };
}
