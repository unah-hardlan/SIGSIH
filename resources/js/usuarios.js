const hasGestionUsuariosAccess = () => {
    try {
        const main = document.querySelector("main");
        return (main?.dataset?.canGestionUsuarios || "") === "1";
    } catch (_) {
        return false;
    }
};

document.addEventListener("alpine:init", () => {
    Alpine.data("usuariosCrud", () => ({
        isModalOpen: false,
        isEditUserModalOpen: false,
        showDeleteModal: false,
        isResetPasswordConfirmModalOpen: false,
        isResetPasswordResultModalOpen: false,
        users: [],
        loading: false,
        error: "",
        formError: "",
        isSubmitting: false,
        pagination: { page: 1, per_page: 10, last_page: 1, total: 0 },
        search: "",
        filtroPerfil: "",
        ordenarPor: "",
        ordenDirection: "asc",
        debounceTimer: null,
        roles: [],
        rolesLoading: false,
        rolesError: "",
        createForm: {
            usuario: "",
            correo_electronico: "",
            estado_usuario: "ACTIVO",
            contrasena: "",
            id_rol_fk: "",
        },
        editForm: {
            id: null,
            usuario: "",
            correo_electronico: "",
            estado_usuario: "ACTIVO",
            contrasena: "",
            id_rol_fk: null,
        },
        userToEdit: null,
        userToInactivate: null,
        userToResetPassword: null,
        resetPasswordResult: {
            usuario: "",
            passwordGenerica: "",
        },
        currentSessionUser: "",

        numbers: [],
        currentPage: 1,
        perPage: 10,
        apiBase: "/api/usuarios",
        canAccess() {
            return hasGestionUsuariosAccess();
        },
        notify(msg, type = "success") {
            const el = document.createElement("div");
            el.textContent = msg;
            el.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded shadow text-sm text-white ${type === "error" ? "bg-red-600" : "bg-green-600"
                }`;
            document.body.appendChild(el);
            setTimeout(() => {
                el.classList.add("opacity-0", "transition");
            }, 2500);
            setTimeout(() => el.remove(), 3000);
        },

        paginatedUsuarios() {
            return this.numbers.slice(
                (this.currentPage - 1) * this.perPage,
                this.currentPage * this.perPage
            );
        },
        totalPages() {
            return Math.ceil(this.numbers.length / this.perPage);
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
        init() {
            if (!this.canAccess()) {
                this.error = "No tienes permisos para ver los usuarios.";
                return;
            }
            window.addEventListener("modal-submit", (e) => {
                if (e.detail?.formId === "formCrear") {
                    this.createUser();
                }
                if (e.detail?.formId === "formEditar") {
                    this.updateUser();
                }
                if (e.detail?.formId === "formResetPasswordGenerica") {
                    this.confirmResetPasswordGenerica();
                }
            });
            this.$watch("search", () => this.debounceFetch());
            this.$watch("filtroPerfil", () => {
                this.currentPage = 1;
                this.fetchUsers();
            });
            this.$watch("ordenarPor", (val, old) => {
                if (old === val) {
                    this.ordenDirection =
                        this.ordenDirection === "asc" ? "desc" : "asc";
                } else {
                    this.ordenDirection = "asc";
                }
                this.currentPage = 1;
                this.fetchUsers();
            });
            this.$watch("showDeleteModal", (val) => {
                if (!val) this.userToInactivate = null;
            });

            window.addEventListener("inactivar-user", () => {
                if (this.userToInactivate)
                    this.inactivarUser(this.userToInactivate);
            });
            window.addEventListener("confirm-delete", () => {
                if (this.userToInactivate)
                    this.inactivarUser(this.userToInactivate);
            });
            this.fetchUsers();
            this.fetchRoles();
            this.fetchCurrentSessionUser();
        },
        async fetchCurrentSessionUser() {
            try {
                const r = await fetch("/api/me", {
                    headers: this.authHeaders(),
                    credentials: "include",
                });
                if (!r.ok) return;
                const data = await r.json();
                this.currentSessionUser = (data?.usuario?.usuario || "").toString().toUpperCase();
            } catch (_) {
                this.currentSessionUser = "";
            }
        },
        isSelfUser(u) {
            const target = (u?.usuario || "").toString().toUpperCase();
            return !!target && !!this.currentSessionUser && target === this.currentSessionUser;
        },
        async fetchRoles() {
            if (!this.canAccess()) {
                this.rolesLoading = false;
                this.roles = [];
                this.rolesError = "No tienes permisos para listar roles.";
                return;
            }
            try {
                this.rolesLoading = true;
                this.rolesError = "";
                const r = await fetch("/api/roles?all=1", {
                    headers: this.authHeaders(),
                    credentials: "include",
                });
                if (r.status === 403) {
                    this.rolesError = "No tienes permisos para listar roles.";
                    this.roles = [];
                    return;
                }
                if (!r.ok) throw await r.json();
                const data = await r.json();
                this.roles = Array.isArray(data?.data) ? data.data : data;
            } catch (e) {
                this.rolesError = e.error || e.message || "Error roles";
            } finally {
                this.rolesLoading = false;
            }
        },
        debounceFetch() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.currentPage = 1;
                this.fetchUsers();
            }, 400);
        },
        getToken() {
            return null;
        },
        authHeaders() {
            return {
                "Content-Type": "application/json",
                Accept: "application/json",
            };
        },
        async fetchUsers() {
            if (!this.canAccess()) {
                this.loading = false;
                this.error = "No tienes permisos para ver los usuarios.";
                this.users = [];
                return;
            }
            this.loading = true;
            this.error = "";
            const params = new URLSearchParams({
                per_page: 10000,
                page: 1,
            });
            if (this.search) params.append("q", this.search);
            if (this.filtroPerfil) {
                params.append("estado", this.filtroPerfil);
            } else {
                params.append("all", "1");
            }
            if (this.ordenarPor) {
                params.append("sort", this.ordenarPor);
                params.append("direction", this.ordenDirection);
            }
            try {
                const r = await fetch(`${this.apiBase}?${params.toString()}`, {
                    headers: this.authHeaders(),
                    credentials: "include",
                });
                if (r.status === 403) {
                    this.error = "No tienes permisos para ver los usuarios.";
                    this.users = [];
                    return;
                }
                if (r.status === 401) {
                    this.error = "Sesión expirada. Inicia sesión.";
                    this.users = [];
                    return;
                }
                if (!r.ok) throw await r.json();
                const data = await r.json();
                this.users = data.data;
                this.numbers = this.users;
            } catch (e) {
                this.error = e.error || e.message || "Error";
            } finally {
                this.loading = false;
            }
        },
        userRole(u) {
            return u && u.rol ? u.rol : "-";
        },
        userName(u) {
            return (u && (u.nombre || u.usuario)) || "-";
        },
        changePage(p) {
            if (p >= 1 && p <= this.pagination.last_page) {
                this.pagination.page = p;
                this.fetchUsers();
            }
        },
        openCreate() {
            if (!this.canAccess() || this.isSubmitting) return;
            this.createForm = {
                usuario: "",
                correo_electronico: "",
                estado_usuario: "ACTIVO",
                contrasena: "",
                id_rol_fk: "",
            };
            this.formError = "";
            this.isModalOpen = true;
            if (!this.roles.length) this.fetchRoles();
        },
        async createUser() {
            if (!this.canAccess() || this.isSubmitting) return;
            this.isSubmitting = true;
            this.formError = "";
            if (!this.createForm.id_rol_fk) {
                this.formError = "Debe seleccionar un rol";
                this.isSubmitting = false;
                return;
            }
            try {
                const payload = { ...this.createForm };
                const r = await fetch(this.apiBase, {
                    method: "POST",
                    headers: this.authHeaders(),
                    body: JSON.stringify(payload),
                });
                if (!r.ok) throw await r.json();
                const data = await r.json();
                this.isModalOpen = false;
                if (this.currentPage === 1 && !this.ordenarPor) {
                    this.users.unshift(data.data || data);
                    this.numbers = this.users;
                } else {
                    this.fetchUsers();
                }
                this.notify("Usuario creado");
            } catch (e) {
                this.formError =
                    (e.errors && Object.values(e.errors).flat().join("\n")) ||
                    e.error ||
                    "Error creando";
                this.notify(this.formError, "error");
            } finally {
                this.isSubmitting = false;
            }
        },
        openEdit(u) {
            if (!this.canAccess() || this.isSubmitting) return;
            if (this.isSelfUser(u)) {
                this.notify("No puedes editar tu propio usuario desde este módulo", "error");
                return;
            }
            this.editForm = {
                id: u.id,
                usuario: u.usuario,
                correo_electronico: u.correo_electronico,
                estado_usuario: u.estado_usuario,
                contrasena: "",
                id_rol_fk: u.id_rol_fk || null,
            };
            this.formError = "";
            this.userToEdit = u;
            this.isEditUserModalOpen = true;
            if (!this.roles.length) this.fetchRoles();
        },
        async updateUser() {
            if (!this.canAccess() || this.isSubmitting) return;
            this.isSubmitting = true;
            this.formError = "";
            const payload = {
                correo_electronico: this.editForm.correo_electronico,
                estado_usuario: this.editForm.estado_usuario,
            };
            if (this.editForm.contrasena)
                payload.contrasena = this.editForm.contrasena;
            if (this.editForm.id_rol_fk)
                payload.id_rol_fk = this.editForm.id_rol_fk;
            try {
                const r = await fetch(`${this.apiBase}/${this.editForm.id}`, {
                    method: "PUT",
                    headers: this.authHeaders(),
                    body: JSON.stringify(payload),
                });
                if (!r.ok) throw await r.json();
                const data = await r.json();
                this.isEditUserModalOpen = false;
                const idx = this.users.findIndex(
                    (x) => x.id === this.editForm.id
                );
                if (idx > -1) {
                    this.users[idx] = data.data || data;
                }
                this.numbers = this.users;
                if (this.ordenarPor) this.fetchUsers();
                this.notify("Usuario actualizado");
            } catch (e) {
                this.formError =
                    (e.errors && Object.values(e.errors).flat().join("\n")) ||
                    e.error ||
                    "Error actualizando";
                this.notify(this.formError, "error");
            } finally {
                this.isSubmitting = false;
            }
        },
        openInactivar(user) {
            if (!this.canAccess()) return;
            if (this.isSelfUser(user)) {
                this.notify("No puedes inactivar tu propio usuario", "error");
                return;
            }
            this.userToInactivate = user;
            this.showDeleteModal = true;
        },
        openResetPasswordModal(user) {
            if (!user?.id) return;
            if (this.isSelfUser(user)) {
                this.notify("No puedes restablecer tu propia contraseña desde este módulo", "error");
                return;
            }

            this.userToResetPassword = user;
            this.isResetPasswordConfirmModalOpen = true;
        },

        async confirmResetPasswordGenerica() {
            const user = this.userToResetPassword;
            if (!user?.id) return;

            try {
                const resp = await fetch(`${this.apiBase}/${user.id}/reset-password-generica`, {
                    method: "PUT",
                    headers: this.authHeaders(),
                    credentials: "include",
                });
                const data = await resp.json().catch(() => ({}));
                if (!resp.ok) {
                    throw new Error(data?.error || "No se pudo restablecer la contraseña");
                }

                this.isResetPasswordConfirmModalOpen = false;
                this.resetPasswordResult = {
                    usuario: data?.usuario || user?.usuario || "",
                    passwordGenerica: data?.password_generica || "",
                };
                this.isResetPasswordResultModalOpen = true;
                await this.fetchUsers();
            } catch (e) {
                this.notify(e.message || "Error al restablecer contraseña", "error");
            }
        },
        async copyResetPassword() {
            const pwd = this.resetPasswordResult?.passwordGenerica || "";
            if (!pwd) return;
            try {
                await navigator.clipboard.writeText(pwd);
                this.notify("Contraseña copiada al portapapeles");
            } catch (_) {
                this.notify("No se pudo copiar automáticamente", "error");
            }
        },
        openReporte() {
            if (!this.canAccess()) return;
            const params = new URLSearchParams();
            params.append("modulo", "usuarios");
            if (this.search) params.append("q", this.search);
            if (this.filtroPerfil) params.append("estado", this.filtroPerfil);
            else params.append("all", "1");
            if (this.ordenarPor) {
                params.append("sort", this.ordenarPor);
                params.append("direction", this.ordenDirection);
            }
            const url = `/admin/reportes-header?${params.toString()}`;
            window.open(url, "_blank");
        },
        async inactivarUser(user) {
            if (!user || !user.id) return;
            if (this.isSelfUser(user)) {
                this.notify("No puedes inactivar tu propio usuario", "error");
                return;
            }
            try {
                const resp = await fetch(`${this.apiBase}/${user.id}`, {
                    method: "DELETE",
                    headers: this.authHeaders(),
                    credentials: "include",
                });
                if (!resp.ok) {
                    const data = await resp.json().catch(() => ({}));
                    throw new Error(
                        data.message || "Error al inactivar usuario"
                    );
                }
                const idx = this.users.findIndex((x) => x.id === user.id);
                if (idx > -1) {
                    this.users[idx].estado_usuario = "INACTIVO";
                    if (this.filtroPerfil === "ACTIVO") {
                        this.users.splice(idx, 1);
                    }
                }
                this.numbers = this.users;
                if (this.ordenarPor) this.fetchUsers();
                this.notify("Usuario inactivado");
            } catch (e) {
                console.error(e);
                this.notify(e.message || "Error al inactivar", "error");
            }
        },

        paginatedUsuarios() {
            const start = (this.currentPage - 1) * this.perPage;
            const end = start + this.perPage;
            return this.numbers.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.numbers.length / this.perPage);
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
            if (page >= 1 && page <= this.totalPages()) {
                this.currentPage = page;
            }
        },
    }));
});
