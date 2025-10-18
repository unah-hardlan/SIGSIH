window.paisesApiHandlers = {
    authHeaders() {
        return {
            "Content-Type": "application/json",
            Accept: "application/json",
        };
    },

    /**
     * Fetches the list of countries from the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async fetchPaises(component) {
        component.loadingPaises = true;
        try {
            const response = await fetch("/api/paises", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            // Assuming the API returns data in 'data' key or directly an array
            component.paises = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching paises:", error);
            window.showToast &&
                window.showToast("Error al cargar países", "error");
        } finally {
            component.loadingPaises = false;
        }
    },

    /**
     * Submits a new country to the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async submitPais(component) {
        const nombreTrim = String(component.nombre_pais || "").trim();
        if (!nombreTrim) {
            window.showToast &&
                window.showToast("El nombre del país es obligatorio", "error");
            return;
        }
        if (
            component.paises.some(
                (p) => p.nombre_pais.toLowerCase() === nombreTrim.toLowerCase()
            )
        ) {
            window.showToast && window.showToast("El país ya existe", "error");
            return;
        }
        try {
            const payload = {
                nombre_pais: nombreTrim,
            };
            const response = await fetch("/api/paises", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
                credentials: "same-origin",
                body: JSON.stringify(payload),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast("País creado exitosamente", "success");
            component.nombre_pais = "";
            component.isPaisModalOpen = false;
            await this.fetchPaises(component); // Use 'this' to call other methods within the same handler object
        } catch (error) {
            console.error("Error creating pais:", error);
            window.showToast &&
                window.showToast("Error al crear el país", "error");
        }
    },

    /**
     * Updates an existing country via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async updatePais(component) {
        if (!component.itemToEdit || !component.itemToEdit.id) return;
        const nombreTrim = String(component.itemToEdit.nombre || "").trim();
        if (!nombreTrim) {
            window.showToast &&
                window.showToast("El nombre del país es obligatorio", "error");
            return;
        }
        if (
            component.paises.some(
                (p) =>
                    p.nombre_pais.toLowerCase() === nombreTrim.toLowerCase() &&
                    p.id_pais_pk !== component.itemToEdit.id
            )
        ) {
            window.showToast &&
                window.showToast("Ya existe otro país con ese nombre", "error");
            return;
        }
        try {
            const payload = {
                nombre_pais: nombreTrim,
            };
            const response = await fetch(
                `/api/paises/${component.itemToEdit.id}`,
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
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast("País actualizado exitosamente", "success");
            component.isPaisEditModalOpen = false;
            component.itemToEdit = null;
            await this.fetchPaises(component);
        } catch (error) {
            console.error("Error updating pais:", error);
            window.showToast &&
                window.showToast("Error al actualizar el país", "error");
        }
    },

    /**
     * Deletes a country via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async deletePais(component) {
        if (!component.itemToDelete || !component.itemToDelete.id) return;
        try {
            const response = await fetch(
                `/api/paises/${component.itemToDelete.id}`,
                {
                    method: "DELETE",
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                }
            );
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast("País eliminado exitosamente", "success");
            component.isPaisDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchPaises(component);
        } catch (error) {
            console.error("Error deleting pais:", error);
            window.showToast &&
                window.showToast("Error al eliminar el país", "error");
        }
    },

    /**
     * Fetches the list of departments from the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async fetchDepartamentos(component) {
        component.loadingDepartamentos = true;
        try {
            const response = await fetch("/api/departamentos", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            component.departamentos = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching departamentos:", error);
            window.showToast &&
                window.showToast("Error al cargar departamentos", "error");
        } finally {
            component.loadingDepartamentos = false;
        }
    },

    /**
     * Submits a new department to the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async submitDepartamento(component) {
        const nombreTrim = String(component.nombre_departamento || "").trim();
        const paisId = component.pais_departamento;
        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del departamento es obligatorio",
                    "error"
                );
            return;
        }
        if (!paisId) {
            window.showToast &&
                window.showToast("Debe seleccionar un país", "error");
            return;
        }
        if (
            component.departamentos.some(
                (d) =>
                    d.nombre_departamento.toLowerCase() ===
                        nombreTrim.toLowerCase() && d.id_pais_pk == paisId
            )
        ) {
            window.showToast &&
                window.showToast(
                    "El departamento ya existe en ese país",
                    "error"
                );
            return;
        }
        try {
            const payload = {
                nombre_departamento: nombreTrim,
                id_pais_pk: paisId,
            };
            const response = await fetch("/api/departamentos", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
                credentials: "same-origin",
                body: JSON.stringify(payload),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast("Departamento creado exitosamente", "success");
            component.nombre_departamento = "";
            component.pais_departamento = "";
            component.isDepartamentoModalOpen = false;
            await this.fetchDepartamentos(component);
        } catch (error) {
            console.error("Error creating departamento:", error);
            window.showToast &&
                window.showToast("Error al crear el departamento", "error");
        }
    },

    /**
     * Updates an existing department via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async updateDepartamento(component) {
        if (!component.itemToEdit || !component.itemToEdit.id) return;
        const nombreTrim = String(component.itemToEdit.nombre || "").trim();
        const paisId = component.itemToEdit.pais;
        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre del departamento es obligatorio",
                    "error"
                );
            return;
        }
        if (!paisId) {
            window.showToast &&
                window.showToast("Debe seleccionar un país", "error");
            return;
        }
        if (
            component.departamentos.some(
                (d) =>
                    d.nombre_departamento.toLowerCase() ===
                        nombreTrim.toLowerCase() &&
                    d.id_pais_pk == paisId &&
                    d.id_departamento_pk !== component.itemToEdit.id
            )
        ) {
            window.showToast &&
                window.showToast(
                    "Ya existe otro departamento con ese nombre en ese país",
                    "error"
                );
            return;
        }
        try {
            const payload = {
                nombre_departamento: nombreTrim,
                id_pais_pk: paisId,
            };
            const response = await fetch(
                `/api/departamentos/${component.itemToEdit.id}`,
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
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast(
                    "Departamento actualizado exitosamente",
                    "success"
                );
            component.isDepartamentoEditModalOpen = false;
            component.itemToEdit = null;
            await this.fetchDepartamentos(component);
        } catch (error) {
            console.error("Error updating departamento:", error);
            window.showToast &&
                window.showToast(
                    "Error al actualizar el departamento",
                    "error"
                );
        }
    },

    /**
     * Deletes a department via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async deleteDepartamento(component) {
        if (!component.itemToDelete || !component.itemToDelete.id) return;
        try {
            const response = await fetch(
                `/api/departamentos/${component.itemToDelete.id}`,
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
                    "Departamento eliminado exitosamente",
                    "success"
                );
            component.isDepartamentoDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchDepartamentos(component);
        } catch (error) {
            console.error("Error deleting departamento:", error);
            const errorMessage =
                error.message || "Error al eliminar el departamento";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },

    /**
     * Fetches the list of cities from the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async fetchCiudades(component) {
        component.loadingCiudades = true;
        try {
            const response = await fetch("/api/ciudades", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            component.ciudades = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching ciudades:", error);
            window.showToast &&
                window.showToast("Error al cargar ciudades", "error");
        } finally {
            component.loadingCiudades = false;
        }
    },

    /**
     * Submits a new city to the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async submitCiudad(component) {
        const nombreTrim = String(component.nombre_ciudad || "").trim();
        const departamentoId = component.departamento_ciudad;
        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre de la ciudad es obligatorio",
                    "error"
                );
            return;
        }
        if (!departamentoId) {
            window.showToast &&
                window.showToast("Debe seleccionar un departamento", "error");
            return;
        }
        if (
            component.ciudades.some(
                (c) =>
                    c.nombre_ciudad.toLowerCase() ===
                        nombreTrim.toLowerCase() &&
                    c.id_departamento_fk == departamentoId
            )
        ) {
            window.showToast &&
                window.showToast(
                    "La ciudad ya existe en ese departamento",
                    "error"
                );
            return;
        }
        try {
            const payload = {
                nombre_ciudad: nombreTrim,
                id_departamento_fk: departamentoId,
            };
            const response = await fetch("/api/ciudades", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
                credentials: "same-origin",
                body: JSON.stringify(payload),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast("Ciudad creada exitosamente", "success");
            component.nombre_ciudad = "";
            component.departamento_ciudad = "";
            component.isCiudadModalOpen = false;
            await this.fetchCiudades(component);
        } catch (error) {
            console.error("Error creating ciudad:", error);
            window.showToast &&
                window.showToast("Error al crear la ciudad", "error");
        }
    },

    /**
     * Updates an existing city via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async updateCiudad(component) {
        if (!component.itemToEdit || !component.itemToEdit.id) return;
        const nombreTrim = String(component.itemToEdit.nombre || "").trim();
        const departamentoId = component.itemToEdit.departamento;
        if (!nombreTrim) {
            window.showToast &&
                window.showToast(
                    "El nombre de la ciudad es obligatorio",
                    "error"
                );
            return;
        }
        if (!departamentoId) {
            window.showToast &&
                window.showToast("Debe seleccionar un departamento", "error");
            return;
        }
        if (
            component.ciudades.some(
                (c) =>
                    c.nombre_ciudad.toLowerCase() ===
                        nombreTrim.toLowerCase() &&
                    c.id_departamento_fk == departamentoId &&
                    c.id_ciudad_pk !== component.itemToEdit.id
            )
        ) {
            window.showToast &&
                window.showToast(
                    "Ya existe otra ciudad con ese nombre en ese departamento",
                    "error"
                );
            return;
        }
        try {
            const payload = {
                nombre_ciudad: nombreTrim,
                id_departamento_fk: departamentoId,
            };
            const response = await fetch(
                `/api/ciudades/${component.itemToEdit.id}`,
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
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast("Ciudad actualizada exitosamente", "success");
            component.isCiudadEditModalOpen = false;
            component.itemToEdit = null;
            await this.fetchCiudades(component);
        } catch (error) {
            console.error("Error updating ciudad:", error);
            window.showToast &&
                window.showToast("Error al actualizar la ciudad", "error");
        }
    },

    /**
     * Deletes a city via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async deleteCiudad(component) {
        if (!component.itemToDelete || !component.itemToDelete.id) return;
        try {
            const response = await fetch(
                `/api/ciudades/${component.itemToDelete.id}`,
                {
                    method: "DELETE",
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                }
            );
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast("Ciudad eliminada exitosamente", "success");
            component.isCiudadDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchCiudades(component);
        } catch (error) {
            console.error("Error deleting ciudad:", error);
            const errorMessage = error.message || "Error al eliminar la ciudad";
            window.showToast && window.showToast(errorMessage, "error");
        }
    },

    /**
     * Fetches the list of addresses from the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async fetchDirecciones(component) {
        component.loadingDirecciones = true;
        try {
            const response = await fetch("/api/direcciones", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            component.direcciones = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching direcciones:", error);
            window.showToast &&
                window.showToast("Error al cargar direcciones", "error");
        } finally {
            component.loadingDirecciones = false;
        }
    },

    /**
     * Fetches the list of agencies from the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async fetchAgencias(component) {
        component.loadingAgencias = true;
        try {
            const response = await fetch("/api/agencias", {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            component.agencias = Array.isArray(data?.data)
                ? data.data
                : Array.isArray(data)
                ? data
                : [];
        } catch (error) {
            console.error("Error fetching agencias:", error);
            window.showToast &&
                window.showToast("Error al cargar agencias", "error");
        } finally {
            component.loadingAgencias = false;
        }
    },

    /**
     * Submits a new address to the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async submitDireccion(component) {
        const calleTrim = String(component.direccion || "").trim();
        const ciudadId = component.ciudad_direccion;
        if (!calleTrim) {
            window.showToast &&
                window.showToast("La calle es obligatoria", "error");
            return;
        }
        if (!ciudadId) {
            window.showToast &&
                window.showToast("Debe seleccionar una ciudad", "error");
            return;
        }
        // TODO: Add duplicate validation if needed
        /* if (
            component.direcciones.some(
                (d) =>
                    d.calle.toLowerCase() === calleTrim.toLowerCase() &&
                    d.numero === component.numero &&
                    d.colonia.toLowerCase() === component.colonia.toLowerCase() &&
                    d.id_ciudad_fk == ciudadId
            )
        ) {
            window.showToast &&
                window.showToast(
                    "La dirección ya existe en esa ciudad",
                    "error"
                );
            return;
        } */
        try {
            const payload = {
                calle: component.direccion.trim(),
                numero: component.numero.trim(),
                colonia: component.colonia.trim(),
                codigo_postal: component.codigo_postal.trim(),
                referencia: component.referencia.trim(),
                id_ciudad_fk: ciudadId,
            };
            const response = await fetch("/api/direcciones", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
                credentials: "same-origin",
                body: JSON.stringify(payload),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast("Dirección creada exitosamente", "success");
            component.direccion = "";
            component.numero = "";
            component.colonia = "";
            component.codigo_postal = "";
            component.referencia = "";
            component.ciudad_direccion = "";
            
            component.isDireccionModalOpen = false;
            await this.fetchDirecciones(component);
        } catch (error) {
            console.error("Error creating direccion:", error);
            window.showToast &&
                window.showToast("Error al crear la dirección", "error");
        }
    },

    /**
     * Updates an existing address via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async updateDireccion(component) {
        if (!component.itemToEdit || !component.itemToEdit.id) return;
        const calleTrim = String(component.itemToEdit.calle || "").trim();
        const ciudadId = component.itemToEdit.ciudad;
        if (!calleTrim) {
            window.showToast &&
                window.showToast("La calle es obligatoria", "error");
            return;
        }
        if (!ciudadId) {
            window.showToast &&
                window.showToast("Debe seleccionar una ciudad", "error");
            return;
        }
        // TODO: Add duplicate validation if needed
        /* if (
            component.direcciones.some(
                (d) =>
                    d.calle.toLowerCase() === calleTrim.toLowerCase() &&
                    d.numero === component.itemToEdit.numero &&
                    d.colonia.toLowerCase() === component.itemToEdit.colonia.toLowerCase() &&
                    d.id_ciudad_fk == ciudadId &&
                    d.id_direccion_pk !== component.itemToEdit.id
            )
        ) {
            window.showToast &&
                window.showToast(
                    "Ya existe otra dirección con esos datos en esa ciudad",
                    "error"
                );
            return;
        } */
        try {
            const payload = {
                calle: component.itemToEdit.calle.trim(),
                numero: component.itemToEdit.numero.trim(),
                colonia: component.itemToEdit.colonia.trim(),
                codigo_postal: component.itemToEdit.codigo_postal.trim(),
                referencia: component.itemToEdit.referencia.trim(),
                id_ciudad_fk: ciudadId,
            };
            const response = await fetch(
                `/api/direcciones/${component.itemToEdit.id}`,
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
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast(
                    "Dirección actualizada exitosamente",
                    "success"
                );
            component.isDireccionEditModalOpen = false;
            component.itemToEdit = null;
            await this.fetchDirecciones(component);
        } catch (error) {
            console.error("Error updating direccion:", error);
            window.showToast &&
                window.showToast("Error al actualizar la dirección", "error");
        }
    },

    /**
     * Deletes an address via the API.
     * @param {object} component - The Alpine.js component's `this` context.
     */
    async deleteDireccion(component) {
        if (!component.itemToDelete || !component.itemToDelete.id) return;
        try {
            const response = await fetch(
                `/api/direcciones/${component.itemToDelete.id}`,
                {
                    method: "DELETE",
                    headers: { Accept: "application/json" },
                    credentials: "same-origin",
                }
            );
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            window.showToast &&
                window.showToast("Dirección eliminada exitosamente", "success");
            component.isDireccionDeleteModalOpen = false;
            component.itemToDelete = null;
            await this.fetchDirecciones(component);
        } catch (error) {
            console.error("Error deleting direccion:", error);
            window.showToast &&
                window.showToast("Error al eliminar la dirección", "error");
        }
    },
};
