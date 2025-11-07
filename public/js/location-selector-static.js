function initLocationSelectors() {
    const paisSelect = document.getElementById("pais_id");
    const departamentoSelect = document.getElementById("departamento_id");
    const ciudadSelect = document.getElementById("ciudad_id");

    if (!paisSelect || !departamentoSelect || !ciudadSelect) {
        console.error(
            "No se encontraron los selectores de ubicación requeridos"
        );
        return;
    }

    paisSelect.addEventListener("change", handlePaisChange);
    departamentoSelect.addEventListener("change", handleDepartamentoChange);

    initializeInitialData();
}

async function handlePaisChange() {
    const paisSelect = document.getElementById("pais_id");
    const departamentoSelect = document.getElementById("departamento_id");
    const ciudadSelect = document.getElementById("ciudad_id");

    const selectedPais = paisSelect.value;

    if (!selectedPais || selectedPais === "") {
        resetDepartamentoSelect();
        resetCiudadSelect();
        return;
    }

    await loadDepartamentos(selectedPais);
    resetCiudadSelect();
}

async function handleDepartamentoChange() {
    const departamentoSelect = document.getElementById("departamento_id");

    const selectedDepartamento = departamentoSelect.value;

    if (!selectedDepartamento || selectedDepartamento === "") {
        resetCiudadSelect();
        return;
    }

    await loadCiudades(selectedDepartamento);
}

async function loadDepartamentos(paisId) {
    const departamentoSelect = document.getElementById("departamento_id");

    try {
        departamentoSelect.innerHTML =
            '<option value="">Cargando departamentos...</option>';
        departamentoSelect.disabled = true;

        const response = await fetch(`/cliente/api/departamentos/${paisId}`);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        departamentoSelect.innerHTML =
            '<option value="">Seleccione un departamento</option>';

        const departamentos = Array.isArray(data)
            ? data
            : data.departamentos || [];
        departamentos.forEach((departamento) => {
            const option = document.createElement("option");
            const id = departamento.id ?? departamento.id_departamento_pk ?? "";
            const nombre =
                departamento.nombre ?? departamento.nombre_departamento ?? "";
            option.value = id;
            option.textContent = nombre;
            departamentoSelect.appendChild(option);
        });

        departamentoSelect.disabled = false;
    } catch (error) {
        console.error("Error al cargar departamentos:", error);
        departamentoSelect.innerHTML =
            '<option value="">Error al cargar departamentos</option>';
        departamentoSelect.disabled = true;
    }
}

async function loadCiudades(departamentoId) {
    const ciudadSelect = document.getElementById("ciudad_id");

    try {
        ciudadSelect.innerHTML =
            '<option value="">Cargando ciudades...</option>';
        ciudadSelect.disabled = true;

        const response = await fetch(`/cliente/api/ciudades/${departamentoId}`);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        ciudadSelect.innerHTML =
            '<option value="">Seleccione una ciudad</option>';

        const ciudades = Array.isArray(data) ? data : data.ciudades || [];
        ciudades.forEach((ciudad) => {
            const option = document.createElement("option");
            const id = ciudad.id ?? ciudad.id_ciudad_pk ?? "";
            const nombre = ciudad.nombre ?? ciudad.nombre_ciudad ?? "";
            option.value = id;
            option.textContent = nombre;
            ciudadSelect.appendChild(option);
        });

        ciudadSelect.disabled = false;
    } catch (error) {
        console.error("Error al cargar ciudades:", error);
        ciudadSelect.innerHTML =
            '<option value="">Error al cargar ciudades</option>';
        ciudadSelect.disabled = true;
    }
}

function resetDepartamentoSelect() {
    const departamentoSelect = document.getElementById("departamento_id");
    departamentoSelect.innerHTML =
        '<option value="">Seleccione un departamento</option>';
    departamentoSelect.disabled = true;
}

function resetCiudadSelect() {
    const ciudadSelect = document.getElementById("ciudad_id");
    ciudadSelect.innerHTML = '<option value="">Seleccione una ciudad</option>';
    ciudadSelect.disabled = true;
}

async function initializeInitialData() {
    const paisSelect = document.getElementById("pais_id");
    const departamentoSelect = document.getElementById("departamento_id");
    const ciudadSelect = document.getElementById("ciudad_id");

    const oldPais = paisSelect.getAttribute("data-old-value");
    const oldDepartamento = departamentoSelect.getAttribute("data-old-value");
    const oldCiudad = ciudadSelect.getAttribute("data-old-value");

    if (oldPais) {
        paisSelect.value = oldPais;
        await handlePaisChange();

        if (oldDepartamento) {
            departamentoSelect.value = oldDepartamento;
            await handleDepartamentoChange();

            if (oldCiudad) {
                ciudadSelect.value = oldCiudad;
            }
        }
    }
}

document.addEventListener("DOMContentLoaded", initLocationSelectors);
