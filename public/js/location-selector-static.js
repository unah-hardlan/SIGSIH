/**
 * Location Selector - Database AJAX
 * Maneja la selección en cascada de País -> Departamento -> Ciudad
 * usando llamadas AJAX a la base de datos
 */

/**
 * Inicializa los selectores de ubicación en cascada
 */
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

    // Eventos
    paisSelect.addEventListener("change", handlePaisChange);
    departamentoSelect.addEventListener("change", handleDepartamentoChange);

    // Cargar datos iniciales
    initializeInitialData();
}

/**
 * Maneja el cambio de país
 */
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

    // Cargar departamentos desde API
    await loadDepartamentos(selectedPais);
    resetCiudadSelect();
}

/**
 * Maneja el cambio de departamento
 */
async function handleDepartamentoChange() {
    const departamentoSelect = document.getElementById("departamento_id");

    const selectedDepartamento = departamentoSelect.value;

    if (!selectedDepartamento || selectedDepartamento === "") {
        resetCiudadSelect();
        return;
    }

    // Cargar ciudades desde API
    await loadCiudades(selectedDepartamento);
}

/**
 * Carga los departamentos para el país seleccionado desde la API
 */
async function loadDepartamentos(paisId) {
    const departamentoSelect = document.getElementById("departamento_id");

    try {
        // Mostrar loading
        departamentoSelect.innerHTML =
            '<option value="">Cargando departamentos...</option>';
        departamentoSelect.disabled = true;

        // Llamada AJAX a la API
        const response = await fetch(`/cliente/api/departamentos/${paisId}`);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        // Limpiar select
        departamentoSelect.innerHTML =
            '<option value="">Seleccione un departamento</option>';

        // Agregar opciones (acepta formato envuelto u array plano)
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

        // Habilitar select
        departamentoSelect.disabled = false;
    } catch (error) {
        console.error("Error al cargar departamentos:", error);
        departamentoSelect.innerHTML =
            '<option value="">Error al cargar departamentos</option>';
        departamentoSelect.disabled = true;
    }
}

/**
 * Carga las ciudades para el departamento seleccionado desde la API
 */
async function loadCiudades(departamentoId) {
    const ciudadSelect = document.getElementById("ciudad_id");

    try {
        // Mostrar loading
        ciudadSelect.innerHTML =
            '<option value="">Cargando ciudades...</option>';
        ciudadSelect.disabled = true;

        // Llamada AJAX a la API
        const response = await fetch(`/cliente/api/ciudades/${departamentoId}`);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        // Limpiar select
        ciudadSelect.innerHTML =
            '<option value="">Seleccione una ciudad</option>';

        // Agregar opciones (acepta formato envuelto u array plano)
        const ciudades = Array.isArray(data) ? data : data.ciudades || [];
        ciudades.forEach((ciudad) => {
            const option = document.createElement("option");
            const id = ciudad.id ?? ciudad.id_ciudad_pk ?? "";
            const nombre = ciudad.nombre ?? ciudad.nombre_ciudad ?? "";
            option.value = id;
            option.textContent = nombre;
            ciudadSelect.appendChild(option);
        });

        // Habilitar select
        ciudadSelect.disabled = false;
    } catch (error) {
        console.error("Error al cargar ciudades:", error);
        ciudadSelect.innerHTML =
            '<option value="">Error al cargar ciudades</option>';
        ciudadSelect.disabled = true;
    }
}

/**
 * Resetea el select de departamentos
 */
function resetDepartamentoSelect() {
    const departamentoSelect = document.getElementById("departamento_id");
    departamentoSelect.innerHTML =
        '<option value="">Seleccione un departamento</option>';
    departamentoSelect.disabled = true;
}

/**
 * Resetea el select de ciudades
 */
function resetCiudadSelect() {
    const ciudadSelect = document.getElementById("ciudad_id");
    ciudadSelect.innerHTML = '<option value="">Seleccione una ciudad</option>';
    ciudadSelect.disabled = true;
}

/**
 * Inicializa los datos basados en valores previos (para edición)
 */
async function initializeInitialData() {
    const paisSelect = document.getElementById("pais_id");
    const departamentoSelect = document.getElementById("departamento_id");
    const ciudadSelect = document.getElementById("ciudad_id");

    // Obtener valores previos de los data attributes
    const oldPais = paisSelect.getAttribute("data-old-value");
    const oldDepartamento = departamentoSelect.getAttribute("data-old-value");
    const oldCiudad = ciudadSelect.getAttribute("data-old-value");

    // Si hay un país seleccionado previamente
    if (oldPais) {
        paisSelect.value = oldPais;
        await handlePaisChange();

        // Si hay un departamento seleccionado previamente
        if (oldDepartamento) {
            departamentoSelect.value = oldDepartamento;
            await handleDepartamentoChange();

            // Si hay una ciudad seleccionada previamente
            if (oldCiudad) {
                ciudadSelect.value = oldCiudad;
            }
        }
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", initLocationSelectors);
