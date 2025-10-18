<div x-data="{
    isPaisModalOpen: false,
    isDepartamentoModalOpen: false,
    isCiudadModalOpen: false,
    isDireccionModalOpen: false,
    // Removed generic isEditModalOpen and isDeleteModalOpen,
    // as we now have specific ones for each entity (e.g., isPaisEditModalOpen)
    itemToEdit: null,
    itemToDelete: null,
    isCiudadEditModalOpen: false,
    isCiudadDeleteModalOpen: false,
    isDireccionEditModalOpen: false,
    isDireccionDeleteModalOpen: false,
    isPaisEditModalOpen: false,
    isPaisDeleteModalOpen: false,
    isDepartamentoEditModalOpen: false,
    isDepartamentoDeleteModalOpen: false,
    paises: [],
    loadingPaises: false,
    nombre_pais: '',
    normalize(str) { return (str || '').toString().trim().toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu, ''); },
    caCatalog: ['Belice','Costa Rica','El Salvador','Guatemala','Honduras','Nicaragua','Panamá'],
    get caOptionsDisponibles() {
        const have = new Set((this.paises || []).map(p => this.normalize(p.nombre_pais)));
        const opts = (this.caCatalog || []).filter(n => !have.has(this.normalize(n)));
        try { return opts.sort((a,b)=> a.localeCompare(b,'es')); } catch(_) { return opts; }
    },
    departamentos: [],
    loadingDepartamentos: false,
    nombre_departamento: '',
    pais_departamento: '',
    suggestedDepartamentos: [],
    editSuggestedDepartamentos: [],
    get selectedPaisNombre() {
        const id = this.pais_departamento;
        const p = this.paises.find(pp => String(pp.id_pais_pk) === String(id));
        return p ? p.nombre_pais : '';
    },
    refreshDepartamentoSuggestions() {
        try {
            const nombrePais = this.selectedPaisNombre;
            const list = (window.subdivisionHelper && window.subdivisionHelper.getByPaisName(nombrePais)) || [];
            this.suggestedDepartamentos = list.map(n => ({ nombre: n }));
            if (this.suggestedDepartamentos.length > 0) {
                const exists = this.suggestedDepartamentos.some(o => o.nombre === this.nombre_departamento);
                if (!exists) this.nombre_departamento = '';
            }
        } catch (_) {
            this.suggestedDepartamentos = [];
        }
    },
    refreshEditDepartamentoSuggestions() {
        try {
            const p = this.paises.find(pp => String(pp.id_pais_pk) === String(this.itemToEdit?.pais || ''));
            const nombrePais = p ? p.nombre_pais : '';
            const list = (window.subdivisionHelper && window.subdivisionHelper.getByPaisName(nombrePais)) || [];
            this.editSuggestedDepartamentos = list.map(n => ({ nombre: n }));
            if (this.editSuggestedDepartamentos.length > 0 && this.itemToEdit) {
                const exists = this.editSuggestedDepartamentos.some(o => o.nombre === this.itemToEdit.nombre);
                if (!exists) this.itemToEdit.nombre = '';
            }
        } catch (_) {
            this.editSuggestedDepartamentos = [];
        }
    },
    ciudades: [],
    loadingCiudades: false,
    nombre_ciudad: '',
    departamento_ciudad: '',
    pais_ciudad: '',
    suggestedCiudades: [],
    edit_pais_ciudad: '',
    editSuggestedCiudades: [],
    get selectedCiudadPaisNombre() {
        const id = this.pais_ciudad;
        const p = this.paises.find(pp => String(pp.id_pais_pk) === String(id));
        return p ? p.nombre_pais : '';
    },
    get selectedEditCiudadPaisNombre() {
        const id = this.edit_pais_ciudad;
        const p = this.paises.find(pp => String(pp.id_pais_pk) === String(id));
        return p ? p.nombre_pais : '';
    },
    get departamentosFiltradosCiudad() {
        const pid = this.pais_ciudad;
        if (!pid) return [];
        return (this.departamentos || []).filter(d => String(d.id_pais_pk) === String(pid));
    },
    get departamentosFiltradosCiudadEdit() {
        const pid = this.edit_pais_ciudad;
        if (!pid) return [];
        return (this.departamentos || []).filter(d => String(d.id_pais_pk) === String(pid));
    },
    getDepartamentoNombreById(depId) {
        const d = (this.departamentos || []).find(x => String(x.id_departamento_pk) === String(depId));
        return d ? d.nombre_departamento : '';
    },
    getDepartamentoByCiudadId(ciudadId) {
        if (!ciudadId) return null;
        const city = (this.ciudades || []).find(c => String(c.id_ciudad_pk) === String(ciudadId));
        if (!city) return null;
        const dep = (this.departamentos || []).find(d => String(d.id_departamento_pk) === String(city.id_departamento_fk));
        return dep || null;
    },
    getDepartamentoNombreByDireccion(dir) {
        // Usa el objeto anidado si viene del API, si no, resuelve por listas cargadas
        const depObj = (dir?.ciudad && dir.ciudad.departamento) || this.getDepartamentoByCiudadId(dir?.id_ciudad_fk);
        return depObj?.nombre_departamento || '';
    },
    getPaisNombreByDireccion(dir) {
        const depObj = (dir?.ciudad && dir.ciudad.departamento) || this.getDepartamentoByCiudadId(dir?.id_ciudad_fk);
        const paisId = depObj?.id_pais_pk;
        if (paisId) {
            const p = (this.paises || []).find(pp => String(pp.id_pais_pk) === String(paisId));
            return p ? p.nombre_pais : '';
        }
        // Fallback si el backend incluye pais anidado
        return dir?.ciudad?.departamento?.pais?.nombre_pais || '';
    },
    refreshCiudadSuggestions() {
        try {
            const paisNombre = this.selectedCiudadPaisNombre;
            const depNombre = this.getDepartamentoNombreById(this.departamento_ciudad);
            const list = (window.subdivisionHelper && window.subdivisionHelper.getCitiesByPaisDep(paisNombre, depNombre)) || [];
            this.suggestedCiudades = list.map(n => ({ nombre: n }));
            // Reset city name if not among suggestions
            if (this.suggestedCiudades.length > 0) {
                const exists = this.suggestedCiudades.some(o => o.nombre === this.nombre_ciudad);
                if (!exists) this.nombre_ciudad = '';
            }
            // Ensure departamento belongs to selected country
            if (this.pais_ciudad) {
                const ok = this.departamentosFiltradosCiudad.some(d => String(d.id_departamento_pk) === String(this.departamento_ciudad));
                if (!ok) this.departamento_ciudad = '';
            }
        } catch (_) {
            this.suggestedCiudades = [];
        }
    },
    refreshEditCiudadSuggestions() {
        try {
            const paisNombre = this.selectedEditCiudadPaisNombre;
            const depNombre = this.getDepartamentoNombreById(this.itemToEdit?.departamento || '');
            const list = (window.subdivisionHelper && window.subdivisionHelper.getCitiesByPaisDep(paisNombre, depNombre)) || [];
            this.editSuggestedCiudades = list.map(n => ({ nombre: n }));
            if (this.editSuggestedCiudades.length > 0 && this.itemToEdit) {
                const exists = this.editSuggestedCiudades.some(o => o.nombre === this.itemToEdit.nombre);
                if (!exists) this.itemToEdit.nombre = '';
            }
            // Ensure departamento belongs to selected country
            if (this.edit_pais_ciudad && this.itemToEdit) {
                const ok = this.departamentosFiltradosCiudadEdit.some(d => String(d.id_departamento_pk) === String(this.itemToEdit.departamento));
                if (!ok) this.itemToEdit.departamento = '';
            }
        } catch (_) {
            this.editSuggestedCiudades = [];
        }
    },
    direcciones: [],
    loadingDirecciones: false,
    direccion: '',
    numero: '',
    colonia: '',
    codigo_postal: '',
    referencia: '',
    ciudad_direccion: '',
    
    async fetchPaises() {
        await window.paisesApiHandlers.fetchPaises(this);
    },
    async submitPais() {
        await window.paisesApiHandlers.submitPais(this);
    },
    async updatePais() {
        await window.paisesApiHandlers.updatePais(this);
    },
    async deletePais() {
        await window.paisesApiHandlers.deletePais(this);
    },
    async fetchDepartamentos() {
        await window.paisesApiHandlers.fetchDepartamentos(this);
    },
    async submitDepartamento() {
        await window.paisesApiHandlers.submitDepartamento(this);
    },
    async updateDepartamento() {
        await window.paisesApiHandlers.updateDepartamento(this);
    },
    async deleteDepartamento() {
        await window.paisesApiHandlers.deleteDepartamento(this);
    },
    async fetchCiudades() {
        await window.paisesApiHandlers.fetchCiudades(this);
    },
    async submitCiudad() {
        await window.paisesApiHandlers.submitCiudad(this);
    },
    async updateCiudad() {
        await window.paisesApiHandlers.updateCiudad(this);
    },
    async deleteCiudad() {
        await window.paisesApiHandlers.deleteCiudad(this);
    },
    async fetchDirecciones() {
        await window.paisesApiHandlers.fetchDirecciones(this);
    },
    async submitDireccion() {
        await window.paisesApiHandlers.submitDireccion(this);
    },
    async updateDireccion() {
        await window.paisesApiHandlers.updateDireccion(this);
    },
    async deleteDireccion() {
        await window.paisesApiHandlers.deleteDireccion(this);
    },
    handleModalSubmit(event) {
        if(event.detail.formId === 'formPais') this.submitPais();
        if(event.detail.formId === 'formEditPais') this.updatePais();
        if(event.detail.formId === 'formDepartamento') this.submitDepartamento();
        if(event.detail.formId === 'formEditDepartamento') this.updateDepartamento();
        if(event.detail.formId === 'formCiudad') this.submitCiudad();
        if(event.detail.formId === 'formEditCiudad') this.updateCiudad();
        if(event.detail.formId === 'formDireccion') this.submitDireccion();
        if(event.detail.formId === 'formEditDireccion') this.updateDireccion();
    },
    handleDelete() {
        if (this.isPaisDeleteModalOpen) {
            this.deletePais();
        }
        if (this.isDepartamentoDeleteModalOpen) {
            this.deleteDepartamento();
        }
        if (this.isCiudadDeleteModalOpen) {
            this.deleteCiudad();
        }
        if (this.isDireccionDeleteModalOpen) {
            this.deleteDireccion();
        }
    }
}"
    x-init="fetchPaises(); fetchDepartamentos(); fetchCiudades(); fetchDirecciones()"
    @keydown.escape.window="
    isPaisModalOpen = false;
    isDepartamentoModalOpen = false;
    isCiudadModalOpen = false;
    isDireccionModalOpen = false;
    isPaisEditModalOpen = false;
    isPaisDeleteModalOpen = false;
    isDepartamentoEditModalOpen = false;
    isDepartamentoDeleteModalOpen = false;
    isCiudadEditModalOpen = false;
    isCiudadDeleteModalOpen = false;
    isDireccionEditModalOpen = false;
    isDireccionDeleteModalOpen = false;
"
    @modal-submit.window="handleModalSubmit($event)"
    @confirm-delete.window="handleDelete()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Ubicaciones de Agencias</h1>
        <div class="flex flex-wrap gap-2 items-center mb-6">
            @include('partials.filtros-generales', [
            'searchModel' => 'filtroUbicaciones',
            'ordenarOptions' => [
            'nombre' => 'Nombre',
            'id' => 'ID'
            ]
            ])
        </div>
    </div>

    <div class="flex flex-col gap-8">
        <!-- Card Direcciones -->
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-400 dark:border-gray-700 overflow-hidden w-full">
            <div class="bg-gradient-to-r from-orange-700 to-orange-900 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                            <i class="fas fa-map-marker-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white nunito-bold">Direcciones</h2>
                            <p class="text-orange-100 text-sm nunito-regular">Gestiona las direcciones por ciudad</p>
                        </div>
                    </div>
                    <button @click="isDireccionModalOpen = true"
                        class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg nunito-bold transition flex items-center space-x-2">
                        <i class="fas fa-plus text-sm"></i>
                        <span class="text-sm">Nuevo</span>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <x-responsive-table>
                    <x-slot name="table">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-300 dark:bg-gray-700 nunito-bold">
                                <tr>
                                    <th class="px-4 py-3 text-left text-gray-700 dark:text-white">Calle</th>
                                    <th class="px-4 py-3 text-left text-gray-700 dark:text-white">Número</th>
                                    <th class="px-4 py-3 text-left text-gray-700 dark:text-white hidden lg:table-cell">Colonia</th>
                                    <th class="px-4 py-3 text-left text-gray-700 dark:text-white">Código Postal</th>
                                    <th class="px-4 py-3 text-left text-gray-700 dark:text-white hidden lg:table-cell">Referencia</th>
                                    <th class="px-4 py-3 text-left text-gray-700 dark:text-white">Ciudad</th>
                                    <th class="px-4 py-3 text-left text-gray-700 dark:text-white">Departamento</th>
                                    <th class="px-4 py-3 text-left text-gray-700 dark:text-white">País</th>
                                    <th class="px-4 py-3 text-center text-gray-700 dark:text-white">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-if="loadingDirecciones">
                                    <tr>
                                        <td colspan="9" class="px-4 py-3 text-center text-gray-500">Cargando direcciones...</td>
                                    </tr>
                                </template>
                                <template x-if="!loadingDirecciones && direcciones.length === 0">
                                    <tr>
                                        <td colspan="9" class="px-4 py-3 text-center text-gray-500">No hay direcciones registradas</td>
                                    </tr>
                                </template>
                                <template x-if="!loadingDirecciones && direcciones.length > 0">
                                    <template x-for="direccion in direcciones" :key="direccion.id_direccion_pk">
                                        <tr class="nunito-regular">
                                            <td class="px-4 py-3 text-gray-900 dark:text-white" x-text="direccion.calle"></td>
                                            <td class="px-4 py-3 text-gray-900 dark:text-white" x-text="direccion.numero"></td>
                                            <td class="px-4 py-3 text-gray-900 dark:text-white hidden lg:table-cell" x-text="direccion.colonia"></td>
                                            <td class="px-4 py-3 text-gray-900 dark:text-white" x-text="direccion.codigo_postal"></td>
                                            <td class="px-4 py-3 text-gray-900 dark:text-white hidden lg:table-cell" x-text="direccion.referencia"></td>
                                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300" x-text="direccion.ciudad?.nombre_ciudad || 'N/A'"></td>
                                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300" x-text="getDepartamentoNombreByDireccion(direccion) || 'N/A'"></td>
                                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300" x-text="getPaisNombreByDireccion(direccion) || 'N/A'"></td>
                                            <td class="px-4 py-3">
                                                <div class="flex justify-center gap-2">
                                                    <button @click="isDireccionEditModalOpen = true; itemToEdit = {id: direccion.id_direccion_pk, calle: direccion.calle, numero: direccion.numero, colonia: direccion.colonia, codigo_postal: direccion.codigo_postal, referencia: direccion.referencia, ciudad: direccion.id_ciudad_fk}" class="text-blue-500 hover:text-blue-700 p-1 rounded">
                                                        <i class="fas fa-edit text-sm"></i>
                                                    </button>
                                                    <button @click="isDireccionDeleteModalOpen = true; itemToDelete = {id: direccion.id_direccion_pk, nombre: `${direccion.direccion_completa}`}" class="text-red-500 hover:text-red-700 p-1 rounded">
                                                        <i class="fas fa-trash text-sm"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                            </tbody>
                        </table>
                    </x-slot>

                    <x-slot name="cards">
                        <template x-if="loadingDirecciones">
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-6 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando direcciones...
                            </div>
                        </template>
                        <template x-if="!loadingDirecciones && direcciones.length === 0">
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-6 text-center text-gray-500 nunito-regular">
                                No hay direcciones registradas
                            </div>
                        </template>
                        <template x-if="!loadingDirecciones && direcciones.length > 0">
                            <template x-for="direccion in direcciones" :key="direccion.id_direccion_pk">
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-3">
                                    <div class="space-y-1">
                                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-bold">Dirección</p>
                                        <p class="text-base text-gray-900 dark:text-white nunito-regular" x-text="direccion.calle + ' ' + direccion.numero"></p>
                                        <p class="text-sm text-gray-700 dark:text-gray-300" x-text="direccion.colonia"></p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-sm text-gray-600 dark:text-gray-300">
                                        <div>
                                            <span class="block nunito-bold">Código Postal</span>
                                            <span class="nunito-regular" x-text="direccion.codigo_postal"></span>
                                        </div>
                                        <div>
                                            <span class="block nunito-bold">Ciudad</span>
                                            <span class="nunito-regular" x-text="direccion.ciudad?.nombre_ciudad || 'N/A'"></span>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300">
                                        <span class="block nunito-bold">Referencia</span>
                                        <span class="nunito-regular" x-text="direccion.referencia"></span>
                                    </div>
                                    <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                                        <button @click="isDireccionEditModalOpen = true; itemToEdit = {id: direccion.id_direccion_pk, calle: direccion.calle, numero: direccion.numero, colonia: direccion.colonia, codigo_postal: direccion.codigo_postal, referencia: direccion.referencia, ciudad: direccion.id_ciudad_fk}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <button @click="isDireccionDeleteModalOpen = true; itemToDelete = {id: direccion.id_direccion_pk, nombre: `${direccion.direccion_completa}`}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </template>
                    </x-slot>
                </x-responsive-table>
            </div>
        </div>

        <div class="flex flex-col gap-8">
            <!-- Países y Departamentos lado a lado -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-400 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-900 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                                    <i class="fas fa-globe text-white text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-white nunito-bold">Países</h2>
                                    <p class="text-blue-100 text-sm nunito-regular">Gestiona los países disponibles</p>
                                </div>
                            </div>
                            <button @click="isPaisModalOpen = true"
                                class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg nunito-bold transition flex items-center space-x-2">
                                <i class="fas fa-plus text-sm"></i>
                                <span class="text-sm">Nuevo</span>
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <x-responsive-table>
                            <x-slot name="table">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-300 dark:bg-gray-700 nunito-bold">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-gray-700 dark:text-white">Nombre</th>
                                            <th class="px-4 py-3 text-center text-gray-700 dark:text-white">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        <template x-if="loadingPaises">
                                            <tr>
                                                <td colspan="2" class="px-4 py-3 text-center text-gray-500">Cargando países...</td>
                                            </tr>
                                        </template>
                                        <template x-if="!loadingPaises && paises.length === 0">
                                            <tr>
                                                <td colspan="2" class="px-4 py-3 text-center text-gray-500">No hay países registrados</td>
                                            </tr>
                                        </template>
                                        <template x-if="!loadingPaises && paises.length > 0">
                                            <template x-for="pais in paises" :key="pais.id_pais_pk">
                                                <tr class="nunito-regular">
                                                    <td class="px-4 py-3 text-gray-900 dark:text-white" x-text="pais.nombre_pais"></td>
                                                    <td class="px-4 py-3">
                                                        <div class="flex justify-center gap-2">
                                                            <button @click="isPaisEditModalOpen = true; itemToEdit = {id: pais.id_pais_pk, nombre: pais.nombre_pais}" class="text-blue-500 hover:text-blue-700 p-1 rounded">
                                                                <i class="fas fa-edit text-sm"></i>
                                                            </button>
                                                            <button @click="isPaisDeleteModalOpen = true; itemToDelete = {id: pais.id_pais_pk, nombre: pais.nombre_pais}" class="text-red-500 hover:text-red-700 p-1 rounded">
                                                                <i class="fas fa-trash text-sm"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                        </template>
                                    </tbody>
                                </table>
                            </x-slot>

                            <x-slot name="cards">
                                <template x-if="loadingPaises">
                                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-6 text-center text-gray-500 nunito-regular">
                                        <i class="fas fa-spinner fa-spin mr-2"></i> Cargando países...
                                    </div>
                                </template>
                                <template x-if="!loadingPaises && paises.length === 0">
                                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-6 text-center text-gray-500 nunito-regular">
                                        No hay países registrados
                                    </div>
                                </template>
                                <template x-if="!loadingPaises && paises.length > 0">
                                    <template x-for="pais in paises" :key="pais.id_pais_pk">
                                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-3">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white nunito-bold" x-text="pais.nombre_pais"></h3>
                                            <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                                                <button @click="isPaisEditModalOpen = true; itemToEdit = {id: pais.id_pais_pk, nombre: pais.nombre_pais}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
                                                <button @click="isPaisDeleteModalOpen = true; itemToDelete = {id: pais.id_pais_pk, nombre: pais.nombre_pais}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </template>
                            </x-slot>
                        </x-responsive-table>
                    </div>
                </div>

                <!-- Card Departamentos -->
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-400 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-700 to-green-900 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                                    <i class="fas fa-map-marked-alt text-white text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-white nunito-bold">Departamentos</h2>
                                    <p class="text-green-100 text-sm nunito-regular">Gestiona los departamentos por país</p>
                                </div>
                            </div>
                            <button @click="isDepartamentoModalOpen = true; refreshDepartamentoSuggestions()"
                                class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg nunito-bold transition flex items-center space-x-2">
                                <i class="fas fa-plus text-sm"></i>
                                <span class="text-sm">Nuevo</span>
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <x-responsive-table>
                            <x-slot name="table">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-300 dark:bg-gray-700 nunito-bold">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-gray-700 dark:text-white">Nombre</th>
                                            <th class="px-4 py-3 text-left text-gray-700 dark:text-white">País</th>
                                            <th class="px-4 py-3 text-center text-gray-700 dark:text-white">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        <template x-if="loadingDepartamentos">
                                            <tr>
                                                <td colspan="3" class="px-4 py-3 text-center text-gray-500">Cargando departamentos...</td>
                                            </tr>
                                        </template>
                                        <template x-if="!loadingDepartamentos && departamentos.length === 0">
                                            <tr>
                                                <td colspan="3" class="px-4 py-3 text-center text-gray-500">No hay departamentos registrados</td>
                                            </tr>
                                        </template>
                                        <template x-if="!loadingDepartamentos && departamentos.length > 0">
                                            <template x-for="departamento in departamentos" :key="departamento.id_departamento_pk">
                                                <tr class="nunito-regular">
                                                    <td class="px-4 py-3 text-gray-900 dark:text-white" x-text="departamento.nombre_departamento"></td>
                                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300" x-text="departamento.pais?.nombre_pais || 'N/A'"></td>
                                                    <td class="px-4 py-3">
                                                        <div class="flex justify-center gap-2">
                                                            <button @click="isDepartamentoEditModalOpen = true; itemToEdit = {id: departamento.id_departamento_pk, nombre: departamento.nombre_departamento, pais: departamento.id_pais_pk}; refreshEditDepartamentoSuggestions()" class="text-blue-500 hover:text-blue-700 p-1 rounded">
                                                                <i class="fas fa-edit text-sm"></i>
                                                            </button>
                                                            <button @click="isDepartamentoDeleteModalOpen = true; itemToDelete = {id: departamento.id_departamento_pk, nombre: departamento.nombre_departamento}" class="text-red-500 hover:text-red-700 p-1 rounded">
                                                                <i class="fas fa-trash text-sm"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                        </template>
                                    </tbody>
                                </table>
                            </x-slot>

                            <x-slot name="cards">
                                <template x-if="loadingDepartamentos">
                                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-6 text-center text-gray-500 nunito-regular">
                                        <i class="fas fa-spinner fa-spin mr-2"></i> Cargando departamentos...
                                    </div>
                                </template>
                                <template x-if="!loadingDepartamentos && departamentos.length === 0">
                                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-6 text-center text-gray-500 nunito-regular">
                                        No hay departamentos registrados
                                    </div>
                                </template>
                                <template x-if="!loadingDepartamentos && departamentos.length > 0">
                                    <template x-for="departamento in departamentos" :key="departamento.id_departamento_pk">
                                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-3">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white nunito-bold" x-text="departamento.nombre_departamento"></h3>
                                                <p class="text-sm text-gray-600 dark:text-gray-300 nunito-regular" x-text="departamento.pais?.nombre_pais || 'N/A'"></p>
                                            </div>
                                            <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                                                <button @click="isDepartamentoEditModalOpen = true; itemToEdit = {id: departamento.id_departamento_pk, nombre: departamento.nombre_departamento, pais: departamento.id_pais_pk}; refreshEditDepartamentoSuggestions()" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
                                                <button @click="isDepartamentoDeleteModalOpen = true; itemToDelete = {id: departamento.id_departamento_pk, nombre: departamento.nombre_departamento}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </template>
                            </x-slot>
                        </x-responsive-table>
                    </div>
                </div>
            </div>

            <!-- Card Ciudades -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-400 dark:border-gray-700 overflow-hidden w-full">
                <div class="bg-gradient-to-r from-purple-700 to-purple-900 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                                <i class="fas fa-city text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white nunito-bold">Ciudades</h2>
                                <p class="text-purple-100 text-sm nunito-regular">Gestiona las ciudades por departamento</p>
                            </div>
                        </div>
                        <button @click="isCiudadModalOpen = true; pais_ciudad=''; departamento_ciudad=''; nombre_ciudad=''; suggestedCiudades=[]"
                            class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg nunito-bold transition flex items-center space-x-2">
                            <i class="fas fa-plus text-sm"></i>
                            <span class="text-sm">Nuevo</span>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <x-responsive-table>
                        <x-slot name="table">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-300 dark:bg-gray-700 nunito-bold">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-gray-700 dark:text-white">Nombre</th>
                                        <th class="px-4 py-3 text-left text-gray-700 dark:text-white">Departamento</th>
                                        <th class="px-4 py-3 text-center text-gray-700 dark:text-white">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <template x-if="loadingCiudades">
                                        <tr>
                                            <td colspan="3" class="px-4 py-3 text-center text-gray-500">Cargando ciudades...</td>
                                        </tr>
                                    </template>
                                    <template x-if="!loadingCiudades && ciudades.length === 0">
                                        <tr>
                                            <td colspan="3" class="px-4 py-3 text-center text-gray-500">No hay ciudades registradas</td>
                                        </tr>
                                    </template>
                                    <template x-if="!loadingCiudades && ciudades.length > 0">
                                        <template x-for="ciudad in ciudades" :key="ciudad.id_ciudad_pk">
                                            <tr class="nunito-regular">
                                                <td class="px-4 py-3 text-gray-900 dark:text-white" x-text="ciudad.nombre_ciudad"></td>
                                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300" x-text="ciudad.departamento?.nombre_departamento || 'N/A'"></td>
                                                <td class="px-4 py-3">
                                                    <div class="flex justify-center gap-2">
                                                        <button @click="isCiudadEditModalOpen = true; itemToEdit = {id: ciudad.id_ciudad_pk, nombre: ciudad.nombre_ciudad, departamento: ciudad.id_departamento_fk}; edit_pais_ciudad = (departamentos.find(d => String(d.id_departamento_pk) === String(ciudad.id_departamento_fk))?.id_pais_pk || ''); refreshEditCiudadSuggestions()" class="text-blue-500 hover:text-blue-700 p-1 rounded">
                                                            <i class="fas fa-edit text-sm"></i>
                                                        </button>
                                                        <button @click="isCiudadDeleteModalOpen = true; itemToDelete = {id: ciudad.id_ciudad_pk, nombre: ciudad.nombre_ciudad}" class="text-red-500 hover:text-red-700 p-1 rounded">
                                                            <i class="fas fa-trash text-sm"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </template>
                                </tbody>
                            </table>
                        </x-slot>

                        <x-slot name="cards">
                            <template x-if="loadingCiudades">
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-6 text-center text-gray-500 nunito-regular">
                                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando ciudades...
                                </div>
                            </template>
                            <template x-if="!loadingCiudades && ciudades.length === 0">
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-6 text-center text-gray-500 nunito-regular">
                                    No hay ciudades registradas
                                </div>
                            </template>
                            <template x-if="!loadingCiudades && ciudades.length > 0">
                                <template x-for="ciudad in ciudades" :key="ciudad.id_ciudad_pk">
                                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-3">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white nunito-bold" x-text="ciudad.nombre_ciudad"></h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 nunito-regular" x-text="ciudad.departamento?.nombre_departamento || 'N/A'"></p>
                                        </div>
                                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                                            <button @click="isCiudadEditModalOpen = true; itemToEdit = {id: ciudad.id_ciudad_pk, nombre: ciudad.nombre_ciudad, departamento: ciudad.id_departamento_fk}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <button @click="isCiudadDeleteModalOpen = true; itemToDelete = {id: ciudad.id_ciudad_pk, nombre: ciudad.nombre_ciudad}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </template>
                        </x-slot>
                    </x-responsive-table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nuevo País -->
    <x-admin.form-modal class="nunito-bold"
        modalName="isPaisModalOpen"
        title="Nuevo País"
        submitLabel="Guardar País"
        maxWidth="max-w-2xl"
        formId="formPais">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="nombre_pais" class="block text-sm font-medium text-gray-700 nunito-bold">País</label>
                <template x-if="caOptionsDisponibles.length > 0">
                    <select id="nombre_pais_select" name="nombre_pais_select" x-model="nombre_pais" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                        <option value="">Selecciona un país</option>
                        <template x-for="n in caOptionsDisponibles" :key="n">
                            <option :value="n" x-text="n"></option>
                        </template>
                    </select>
                </template>
                <template x-if="caOptionsDisponibles.length === 0">
                    <input type="text" id="nombre_pais" name="nombre_pais" x-model="nombre_pais" placeholder="Todos los países de CA ya están registrados" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border bg-gray-100 text-gray-600 nunito-regular px-2" disabled>
                </template>
                <p class="text-xs text-gray-500 mt-1 nunito-regular">Solo países de Centroamérica.</p>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Nuevo Departamento -->
    <x-admin.form-modal class="nunito-bold"
        modalName="isDepartamentoModalOpen"
        title="Nuevo Departamento"
        submitLabel="Guardar Departamento"
        maxWidth="max-w-2xl"
        formId="formDepartamento">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 nunito-bold">País</label>
                <select id="pais_departamento" name="pais_departamento" x-model="pais_departamento" @change="refreshDepartamentoSuggestions()" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="">Selecciona un país</option>
                    <template x-for="pais in paises" :key="pais.id_pais_pk">
                        <option :value="pais.id_pais_pk" x-text="pais.nombre_pais"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 nunito-bold">Nombre Departamento</label>
                <!-- If we have suggestions for the selected country, show a select; otherwise show a text input -->
                <template x-if="suggestedDepartamentos.length > 0">
                    <select id="nombre_departamento_select" name="nombre_departamento_select" x-model="nombre_departamento" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                        <option value="">Selecciona un departamento</option>
                        <template x-for="opt in suggestedDepartamentos" :key="opt.nombre">
                            <option :value="opt.nombre" x-text="opt.nombre"></option>
                        </template>
                    </select>
                </template>
                <template x-if="suggestedDepartamentos.length === 0">
                    <input type="text" id="nombre_departamento" name="nombre_departamento" x-model="nombre_departamento" placeholder="Escribe el nombre del departamento" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2" />
                </template>
                <p class="text-xs text-gray-500 mt-1 nunito-regular" x-show="suggestedDepartamentos.length === 0">No hay catálogo para el país seleccionado. Puedes escribirlo manualmente.</p>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Nueva Ciudad -->
    <x-admin.form-modal class="nunito-bold"
        modalName="isCiudadModalOpen"
        title="Nueva Ciudad"
        submitLabel="Guardar Ciudad"
        maxWidth="max-w-2xl"
        formId="formCiudad">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 nunito-bold">País</label>
                <select id="pais_ciudad" name="pais_ciudad" x-model="pais_ciudad" @change="refreshCiudadSuggestions()" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="">Selecciona un país</option>
                    <template x-for="pais in paises" :key="pais.id_pais_pk">
                        <option :value="pais.id_pais_pk" x-text="pais.nombre_pais"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 nunito-bold">Departamento</label>
                <select id="departamento_ciudad" name="departamento_ciudad" x-model="departamento_ciudad" @change="refreshCiudadSuggestions()" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="">Selecciona un departamento</option>
                    <template x-for="departamento in departamentosFiltradosCiudad" :key="departamento.id_departamento_pk">
                        <option :value="departamento.id_departamento_pk" x-text="departamento.nombre_departamento"></option>
                    </template>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 nunito-bold">Nombre Ciudad</label>
                <template x-if="suggestedCiudades.length > 0">
                    <select id="nombre_ciudad_select" name="nombre_ciudad_select" x-model="nombre_ciudad" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                        <option value="">Selecciona una ciudad</option>
                        <template x-for="opt in suggestedCiudades" :key="opt.nombre">
                            <option :value="opt.nombre" x-text="opt.nombre"></option>
                        </template>
                    </select>
                </template>
                <template x-if="suggestedCiudades.length === 0">
                    <input type="text" id="nombre_ciudad" name="nombre_ciudad" x-model="nombre_ciudad" placeholder="Escribe el nombre de la ciudad" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </template>
                <p class="text-xs text-gray-500 mt-1 nunito-regular" x-show="suggestedCiudades.length === 0">No hay catálogo para el país/departamento seleccionado. Puedes escribirlo manualmente.</p>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Nueva Dirección -->
    <x-admin.form-modal class="nunito-bold"
        modalName="isDireccionModalOpen"
        title="Nueva Dirección"
        submitLabel="Guardar Dirección"
        maxWidth="max-w-4xl"
        formId="formDireccion">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label for="direccion" class="block text-sm font-medium text-gray-700 nunito-bold">Calle</label>
                <input type="text" id="direccion" name="direccion" x-model="direccion" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="numero" class="block text-sm font-medium text-gray-700 nunito-bold">Número</label>
                <input type="text" id="numero" name="numero" x-model="numero" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="colonia" class="block text-sm font-medium text-gray-700 nunito-bold">Colonia</label>
                <input type="text" id="colonia" name="colonia" x-model="colonia" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="codigo_postal" class="block text-sm font-medium text-gray-700 nunito-bold">Código Postal</label>
                <input type="text" id="codigo_postal" name="codigo_postal" x-model="codigo_postal" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="referencia" class="block text-sm font-medium text-gray-700 nunito-bold">Referencia</label>
                <input type="text" id="referencia" name="referencia" x-model="referencia" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            
            <div>
                <label for="ciudad_direccion" class="block text-sm font-medium text-gray-700 nunito-bold">Ciudad</label>
                <select id="ciudad_direccion" name="ciudad_direccion" x-model="ciudad_direccion" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="">Selecciona una ciudad</option>
                    <template x-for="ciudad in ciudades" :key="ciudad.id_ciudad_pk">
                        <option :value="ciudad.id_ciudad_pk" x-text="ciudad.nombre_ciudad"></option>
                    </template>
                </select>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Original generic modals (these seem redundant now given specific modals below, consider removing them entirely) -->
    <!--
    <x-admin.form-modal modalName="isEditModalOpen" title="Editar País" submitLabel="Guardar Cambios">
        <div>
            <label for="nombre_pais" class="block text-sm font-medium text-gray-700">Nombre País</label>
            <input type="text" id="nombre_pais" x-model="itemToEdit.nombre" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        </div>
    </x-admin.form-modal>

    <x-admin.form-modal modalName="isDeleteModalOpen" title="Eliminar País" submitLabel="Confirmar Eliminación">
        <p>¿Estás seguro de que deseas eliminar el país <span x-text="itemToDelete.nombre"></span>?</p>
    </x-admin.form-modal>
    -->
    <!-- Modales Departamentos (redundant generic ones) -->
    <!--
    <x-admin.form-modal modalName="isEditModalOpen" title="Editar Departamento" submitLabel="Guardar Cambios">
        <div>
            <label for="nombre_departamento" class="block text-sm font-medium text-gray-700">Nombre Departamento</label>
            <input type="text" id="nombre_departamento" x-model="itemToEdit.nombre" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        </div>
    </x-admin.form-modal>

    <x-admin.form-modal modalName="isDeleteModalOpen" title="Eliminar Departamento" submitLabel="Confirmar Eliminación">
        <p>¿Estás seguro de que deseas eliminar el departamento <span x-text="itemToDelete.nombre"></span>?</p>
    </x-admin.form-modal>
    -->

    <!-- Modales Países (Specific) -->
    <x-admin.edit-modal class="nunito-bold" modalName="isPaisEditModalOpen" title="Editar País" itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditPais">
        <div>
            <label for="edit_nombre_pais" class="block text-sm font-medium text-gray-700">Nombre País</label>
            <input type="text" id="edit_nombre_pais" name="edit_nombre_pais" x-model="itemToEdit.nombre" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-regular" modalName="isPaisDeleteModalOpen" itemToDelete="itemToDelete" message="¿Estás seguro de que quieres eliminar este país?" />

    <!-- Modales Departamentos (Specific) -->
    <x-admin.edit-modal class="nunito-bold" modalName="isDepartamentoEditModalOpen" title="Editar Departamento" itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditDepartamento">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="edit_nombre_departamento" class="block text-sm font-medium text-gray-700">Nombre Departamento</label>
                <template x-if="editSuggestedDepartamentos.length > 0">
                    <select id="edit_nombre_departamento_select" name="edit_nombre_departamento_select" x-model="itemToEdit.nombre" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="">Selecciona un departamento</option>
                        <template x-for="opt in editSuggestedDepartamentos" :key="opt.nombre">
                            <option :value="opt.nombre" x-text="opt.nombre"></option>
                        </template>
                    </select>
                </template>
                <template x-if="editSuggestedDepartamentos.length === 0">
                    <input type="text" id="edit_nombre_departamento" name="edit_nombre_departamento" x-model="itemToEdit.nombre" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </template>
            </div>
            <div>
                <label for="edit_pais_departamento" class="block text-sm font-medium text-gray-700">País</label>
                <select id="edit_pais_departamento" name="edit_pais_departamento" x-model="itemToEdit.pais" @change="refreshEditDepartamentoSuggestions()" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">Selecciona un país</option>
                    <template x-for="pais in paises" :key="pais.id_pais_pk">
                        <option :value="pais.id_pais_pk" x-text="pais.nombre_pais"></option>
                    </template>
                </select>
                <script>
                    document.addEventListener('alpine:init', () => {
                        // when the modal opens, try to refresh suggestions
                        // This is a no-op if component isn't ready
                    });
                </script>
            </div>
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-regular" modalName="isDepartamentoDeleteModalOpen" itemToDelete="itemToDelete" message="¿Estás seguro de que quieres eliminar este departamento?" />

    <!-- Modales Ciudades (Specific) -->
    <x-admin.edit-modal class="nunito-bold" modalName="isCiudadEditModalOpen" title="Editar Ciudad" itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditCiudad">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">País</label>
                <select id="edit_pais_ciudad" name="edit_pais_ciudad" x-model="edit_pais_ciudad" @change="refreshEditCiudadSuggestions()" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">Selecciona un país</option>
                    <template x-for="pais in paises" :key="pais.id_pais_pk">
                        <option :value="pais.id_pais_pk" x-text="pais.nombre_pais"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Departamento</label>
                <select id="edit_departamento_ciudad" name="edit_departamento_ciudad" x-model="itemToEdit.departamento" @change="refreshEditCiudadSuggestions()" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">Selecciona un departamento</option>
                    <template x-for="departamento in departamentosFiltradosCiudadEdit" :key="departamento.id_departamento_pk">
                        <option :value="departamento.id_departamento_pk" x-text="departamento.nombre_departamento"></option>
                    </template>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Nombre Ciudad</label>
                <template x-if="editSuggestedCiudades.length > 0">
                    <select id="edit_nombre_ciudad_select" name="edit_nombre_ciudad_select" x-model="itemToEdit.nombre" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="">Selecciona una ciudad</option>
                        <template x-for="opt in editSuggestedCiudades" :key="opt.nombre">
                            <option :value="opt.nombre" x-text="opt.nombre"></option>
                        </template>
                    </select>
                </template>
                <template x-if="editSuggestedCiudades.length === 0">
                    <input type="text" id="edit_nombre_ciudad" name="edit_nombre_ciudad" x-model="itemToEdit.nombre" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </template>
            </div>
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-regular" modalName="isCiudadDeleteModalOpen" itemToDelete="itemToDelete" message="¿Estás seguro de que quieres eliminar esta ciudad?" />

    <!-- Modales Direcciones (Specific) -->
    <x-admin.edit-modal class="nunito-bold" modalName="isDireccionEditModalOpen" title="Editar Dirección" itemToEdit="itemToEdit" maxWidth="max-w-4xl" formId="formEditDireccion">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label for="edit_direccion" class="block text-sm font-medium text-gray-700">Calle</label>
                <input type="text" id="edit_direccion" name="edit_direccion" x-model="itemToEdit.calle" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="edit_numero" class="block text-sm font-medium text-gray-700">Número</label>
                <input type="text" id="edit_numero" name="edit_numero" x-model="itemToEdit.numero" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="edit_colonia" class="block text-sm font-medium text-gray-700">Colonia</label>
                <input type="text" id="edit_colonia" name="edit_colonia" x-model="itemToEdit.colonia" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="edit_codigo_postal" class="block text-sm font-medium text-gray-700">Código Postal</label>
                <input type="text" id="edit_codigo_postal" name="edit_codigo_postal" x-model="itemToEdit.codigo_postal" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="edit_referencia" class="block text-sm font-medium text-gray-700">Referencia</label>
                <input type="text" id="edit_referencia" name="edit_referencia" x-model="itemToEdit.referencia" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
            
            <div>
                <label for="edit_ciudad_direccion" class="block text-sm font-medium text-gray-700">Ciudad</label>
                <select id="edit_ciudad_direccion" name="edit_ciudad_direccion" x-model="itemToEdit.ciudad" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">Selecciona una ciudad</option>
                    <template x-for="ciudad in ciudades" :key="ciudad.id_ciudad_pk">
                        <option :value="ciudad.id_ciudad_pk" x-text="ciudad.nombre_ciudad"></option>
                    </template>
                </select>
            </div>
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-regular" modalName="isDireccionDeleteModalOpen" itemToDelete="itemToDelete" message="¿Estás seguro de que quieres eliminar esta dirección?" />
</div>