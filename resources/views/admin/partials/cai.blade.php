<div x-data="{ 
    tab: 'cai', 
    isCaiModalOpen: false, 
    isEditCaiModalOpen: false, 
    itemToEdit: null, 
    isDeleteCaiModalOpen: false, 
    itemToDelete: null,
    cais: [],
    estadosCai: [],
    categorias: [],
    numbers: [],
    loadingCai: false,
    async fetchCai() {
        await window.caiApiHandlers.fetchCai(this);
    },
    async submitCai() {
        await window.caiApiHandlers.submitCai(this);
    },
    async updateCai() {
        await window.caiApiHandlers.updateCai(this);
    },
    async deleteCai() {
        await window.caiApiHandlers.deleteCai(this);
    },
    codigo: '',
    rango_inicio: '',
    rango_fin: '',
    consecutivo_actual: 0,
    fecha_limite: '',
    id_estado_cai_fk: '',
    // Campos para filtros
    searchCai: '',
    estadoCaiFiltro: '',
    ordenarPor: 'fecha_limite',
    // Paginación
    currentPage: 1,
    perPage: 10,
    paginatedCais() {
        return this.filteredCais().slice((this.currentPage - 1) * this.perPage, this.currentPage * this.perPage);
    },
    totalPages() {
        return Math.ceil(this.filteredCais().length / this.perPage);
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
    // Helpers para filtrar/ordenar en cliente
    normalizeStr(v){
        try{ return (v ?? '').toString().normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().trim(); }catch(_){ return (v ?? '').toString().toLowerCase().trim(); }
    },
    equalsNormalized(a,b){ if(!b) return true; return this.normalizeStr(a) === this.normalizeStr(b); },
    filteredCais(){
        try{
            let items = Array.isArray(this.cais) ? [...this.cais] : [];
            // Filtro por estado (por nombre)
            if(this.estadoCaiFiltro){
                items = items.filter(c => this.equalsNormalized(c?.estado_cai?.nombre || c?.estado_cai?.nombre_estado_cai || '', this.estadoCaiFiltro));
            }
            // Búsqueda simple por código/rangos/fecha/estado
            const q = this.normalizeStr(this.searchCai);
            if(q){
                items = items.filter(c => {
                    const fields = [c?.codigo, c?.rango_inicio, c?.rango_fin, c?.fecha_limite, c?.estado_cai?.nombre || c?.estado_cai?.nombre_estado_cai];
                    return fields.some(v => this.normalizeStr(v).includes(q));
                });
            }
            // Ordenamiento
            const key = this.ordenarPor || 'fecha_limite';
            const map = {
                codigo: x => x?.codigo || '',
                rango_inicio: x => x?.rango_inicio || '',
                rango_fin: x => x?.rango_fin || '',
                fecha_limite: x => x?.fecha_limite || ''
            };
            const getter = map[key] || map.fecha_limite;
            items.sort((a,b)=>{
                const av = getter(a) || '';
                const bv = getter(b) || '';
                if(key==='fecha_limite'){
                    const ad = Date.parse(av) || 0; const bd = Date.parse(bv) || 0; return ad - bd;
                }
                const as = String(av).toLowerCase(); const bs = String(bv).toLowerCase();
                if(as < bs) return -1; if(as > bs) return 1; return 0;
            });
            return items;
        }catch(_){ return this.cais || []; }
    },
    async fetchCai() {
        await window.caiApiHandlers.fetchCai(this);
        // synchronize aliases for reusable pagination components
        this.categorias = this.cais;
        this.numbers = this.cais;
    },
    async submitCai() {
        await window.caiApiHandlers.submitCai(this);
        this.categorias = this.cais;
        this.numbers = this.cais;
    },
    async updateCai() {
        await window.caiApiHandlers.updateCai(this);
        this.categorias = this.cais;
        this.numbers = this.cais;
    },
    async deleteCai() {
        await window.caiApiHandlers.deleteCai(this);
        this.categorias = this.cais;
        this.numbers = this.cais;
    },
    handleModalSubmit(event) {
        if(event.detail.formId === 'formCai') this.submitCai();
        if(event.detail.formId === 'formEditCai') this.updateCai();
    },
    handleDelete() {
        if (this.isDeleteCaiModalOpen) {
            this.deleteCai();
        }
    }
}" x-init="fetchCai()"
x-effect="
$watch('searchCai', () => { currentPage = 1; });
$watch('estadoCaiFiltro', () => { currentPage = 1; });
$watch('ordenarPor', () => { currentPage = 1; });
"
@modal-submit.window="handleModalSubmit($event)" @confirm-delete.window="handleDelete()">

    <div x-show="tab==='cai'" class="overflow-x-auto">
        <x-admin.tabla-crud class="nunito-bold">
            <x-slot name="titulo">
                <h2 class="text-2xl dark:text-white text-gray-800 nunito-bold">CAI</h2>
            </x-slot>
            <x-slot name="filtros">
                @include('partials.filtros-generales', [
                'searchModel' => 'searchCai',
                'filtrosSelect' => [],
                'ordenarOptions' => [
                'codigo' => 'Código',
                'rango_inicio' => 'Rango Inicio',
                'rango_fin' => 'Rango Fin',
                'fecha_limite' => 'Fecha Límite',
                ]
                ])
                <select x-model="estadoCaiFiltro"
                    class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 md:w-64 sm:min-w-[14rem] md:min-w-[16rem] shrink-0 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                    <option value="">Todos los estados</option>
                    <template x-for="estado in estadosCai" :key="estado.id_estado_cai_pk || estado.id">
                        <option :value="estado.nombre_estado_cai || estado.nombre"
                            x-text="estado.nombre_estado_cai || estado.nombre"></option>
                    </template>
                </select>
            </x-slot>
            <x-slot name="boton">
                <div class="flex gap-2 items-stretch">
                    <button @click="isCaiModalOpen = true"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nuevo
                        CAI</button>
                    <a href="/admin/reportes-header?modulo=CAI&fecha={{ now()->format('d-M-Y') }}" target="_blank"
                        class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 text-sm">
                        <i class="fas fa-file-alt"></i> Generar Reporte
                    </a>
                </div>
            </x-slot>
            <div class="overflow-x-auto">
                <table
                    class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse table-white-dividers">
                    <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                        <tr>

                            <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Código</th>
                            <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Rango Inicio</th>
                            <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Rango Fin</th>
                            <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Consecutivo</th>
                            <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Fecha Límite</th>
                            <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Estado CAI</th>
                            <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="filteredCais().length > 0">
                            <template x-for="cai in paginatedCais()" :key="cai.id_cai_pk || cai.id">
                                <tr class="border-b dark:border-gray-700 nunito-regular">
                                    <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white"
                                        x-text="cai.codigo"></td>
                                    <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white"
                                        x-text="cai.rango_inicio"></td>
                                    <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white"
                                        x-text="cai.rango_fin"></td>
                                    <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white"
                                        x-text="cai.consecutivo_actual"></td>
                                    <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white"
                                        x-text="cai.fecha_limite"></td>
                                    <td class="py-2 px-4 nunito-regular">
                                        <span class="px-2 py-1 rounded nunito-regular"
                                            :class="cai.estado_cai?.es_final ? 'bg-red-100 dark:bg-red-800 text-red-600 dark:text-red-100' : 'bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-100'"
                                            x-text="cai.estado_cai?.nombre || 'Sin estado'"></span>
                                    </td>
                                    <td class="py-2 px-4 flex gap-2">
                                        <a href="#" @click.prevent="itemToEdit = { 
                                                ...cai,
                                                codigo: window.caiApiHandlers.normalizeCodigo(cai.codigo),
                                                rango_inicio: window.caiApiHandlers.formatRango(cai.rango_inicio),
                                                rango_fin: window.caiApiHandlers.formatRango(cai.rango_fin),
                                                consecutivo_actual: window.caiApiHandlers.onlyDigits(cai.consecutivo_actual),
                                                fecha_limite: window.caiApiHandlers.normalizeFecha(cai.fecha_limite)
                                            }; isEditCaiModalOpen = true;"
                                            class="text-blue-500 hover:text-blue-700 dark:text-blue-300"><i
                                                class="fas fa-edit"></i></a>
                                        <a href="#"
                                            @click.prevent="itemToDelete = {...cai}; isDeleteCaiModalOpen = true;"
                                            class="text-red-500 hover:text-red-700 dark:text-red-400"><i
                                                class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            </template>
                        </template>
                        <template x-if="cais.length === 0 && !loadingCai">
                            <tr class="border-b dark:border-gray-700 nunito-regular">
                                <td colspan="8" class="py-4 px-4 text-center text-gray-500 dark:text-gray-400">
                                    No hay registros CAI
                                </td>
                            </tr>
                        </template>
                        <template x-if="loadingCai">
                            <tr class="border-b dark:border-gray-700 nunito-regular">
                                <td colspan="8" class="py-4 px-4 text-center text-gray-500 dark:text-gray-400">
                                    Cargando registros CAI...
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Paginación del lado del cliente -->
            <x-pagination />

            <x-slot name="cards">
                <template x-if="loadingCai">
                    <div class="p-8 text-center text-gray-500 nunito-regular">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Cargando registros CAI...
                    </div>
                </template>
                <template x-if="!loadingCai && cais.length === 0">
                    <div class="p-8 text-center text-gray-500 nunito-regular">No hay registros CAI</div>
                </template>
                <template x-for="cai in paginatedCais()" :key="'card-cai-'+(cai.id_cai_pk || cai.id)">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-2">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white" x-text="cai.codigo"></h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400"
                                    x-text="'Fecha límite: '+(cai.fecha_limite||'-')"></p>
                            </div>
                            <div>
                                <span class="px-2 py-1 rounded text-xs"
                                    :class="cai.estado_cai?.es_final ? 'bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-100' : 'bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-100'"
                                    x-text="cai.estado_cai?.nombre || 'Sin estado'"></span>
                            </div>
                        </div>
                        <div class="text-sm text-gray-700 dark:text-gray-300 grid grid-cols-2 gap-2">
                            <div><span class="nunito-bold text-gray-600 dark:text-gray-300">Rango Inicio:</span> <span
                                    x-text="cai.rango_inicio || '-' "></span></div>
                            <div><span class="nunito-bold text-gray-600 dark:text-gray-300">Rango Fin:</span> <span
                                    x-text="cai.rango_fin || '-' "></span></div>
                            <div><span class="nunito-bold text-gray-600 dark:text-gray-300">Consecutivo:</span> <span
                                    x-text="cai.consecutivo_actual || '0'"></span></div>
                        </div>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="itemToEdit = { 
                                    ...cai,
                                    codigo: window.caiApiHandlers.normalizeCodigo(cai.codigo),
                                    rango_inicio: window.caiApiHandlers.formatRango(cai.rango_inicio),
                                    rango_fin: window.caiApiHandlers.formatRango(cai.rango_fin),
                                    consecutivo_actual: window.caiApiHandlers.onlyDigits(cai.consecutivo_actual),
                                    fecha_limite: window.caiApiHandlers.normalizeFecha(cai.fecha_limite)
                                }; isEditCaiModalOpen = true;"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1"><i
                                    class="fas fa-edit"></i> Editar</button>
                            <button @click.prevent="itemToDelete = {...cai}; isDeleteCaiModalOpen = true;"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1"><i
                                    class="fas fa-trash"></i> Eliminar</button>
                        </div>
                    </div>
                </template>
            </x-slot>
        </x-admin.tabla-crud>
    </div>

    {{-- Modales --}}
    <x-admin.form-modal class="nunito-bold" modalName="isCaiModalOpen" title="Nuevo CAI" submitLabel="Guardar CAI"
        maxWidth="max-w-md" formId="formCai">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="codigo" class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                <input type="text" id="codigo" name="codigo" x-model="codigo" placeholder="Ej: CAI-2024-003-C"
                    title="Solo letras, números y guiones. Se normaliza automáticamente."
                    @blur="codigo = window.caiApiHandlers.normalizeCodigo(codigo)"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    aria-describedby="hint-cod">
                <p id="hint-cod" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formato sugerido: CAI-AAAA-XXX-X.
                    Se convierte a mayúsculas y sin espacios.</p>
            </div>
            <div>
                <label for="rango_inicio" class="block text-sm font-medium text-gray-700 nunito-bold">Rango
                    Inicio</label>
                <input type="text" id="rango_inicio" name="rango_inicio" x-model="rango_inicio"
                    placeholder="000-000-00-00000000"
                    title="Formato: 000-000-00-00000000. Se autoformatea mientras escribe."
                    @input="rango_inicio = window.caiApiHandlers.formatRango(rango_inicio)"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    aria-describedby="hint-rini">
                <p id="hint-rini" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formato: 000-000-00-00000000</p>
            </div>
            <div>
                <label for="rango_fin" class="block text-sm font-medium text-gray-700 nunito-bold">Rango Fin</label>
                <input type="text" id="rango_fin" name="rango_fin" x-model="rango_fin" placeholder="000-000-00-00000000"
                    title="Formato: 000-000-00-00000000. Se autoformatea mientras escribe."
                    @input="rango_fin = window.caiApiHandlers.formatRango(rango_fin)"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    aria-describedby="hint-rfin">
                <p id="hint-rfin" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formato: 000-000-00-00000000</p>
            </div>
            <div>
                <label for="consecutivo_actual" class="block text-sm font-medium text-gray-700 nunito-bold">Consecutivo
                    Actual</label>
                <input type="text" id="consecutivo_actual" name="consecutivo_actual" x-model="consecutivo_actual"
                    placeholder="Ej: 999" title="Solo números" inputmode="numeric"
                    @input="consecutivo_actual = window.caiApiHandlers.onlyDigits(consecutivo_actual)"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    aria-describedby="hint-consec">
                <p id="hint-consec" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Solo números</p>
            </div>
            <div>
                <label for="fecha_limite" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha
                    Límite</label>
                <input type="date" id="fecha_limite" name="fecha_limite" x-model="fecha_limite"
                    placeholder="dd/mm/aaaa o yyyy-mm-dd"
                    title="Puede ingresar dd/mm/aaaa o yyyy-mm-dd. Se normaliza a yyyy-mm-dd."
                    @blur="fecha_limite = window.caiApiHandlers.normalizeFecha(fecha_limite)"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    aria-describedby="hint-fecha">
                <p id="hint-fecha" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Se normaliza a yyyy-mm-dd</p>
            </div>
            <div>
                <label for="id_estado_cai_fk" class="block text-sm font-medium text-gray-700 nunito-bold">Estado
                    CAI</label>
                <select id="id_estado_cai_fk" name="id_estado_cai_fk" x-model="id_estado_cai_fk"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <option value="">Seleccionar estado</option>
                    <template x-for="estado in estadosCai" :key="estado.id_estado_cai_pk || estado.id">
                        <option :value="estado.id_estado_cai_pk || estado.id"
                            x-text="estado.nombre_estado_cai || estado.nombre" class="nunito-regular"></option>
                    </template>
                </select>
            </div>
        </div>
    </x-admin.form-modal>

    <x-admin.edit-modal class="nunito-bold" modalName="isEditCaiModalOpen" title="Editar CAI" itemToEdit="itemToEdit"
        maxWidth="max-w-md" formId="formEditCai">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="edit_codigo" class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                <input type="text" id="edit_codigo" name="edit_codigo" :value="itemToEdit?.codigo"
                    placeholder="Ej: CAI-2024-003-C"
                    title="Solo letras, números y guiones. Se normaliza automáticamente."
                    @blur="$el.value = window.caiApiHandlers.normalizeCodigo($el.value)"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    aria-describedby="hint-cod-edit">
                <p id="hint-cod-edit" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formato sugerido:
                    CAI-AAAA-XXX-X</p>
            </div>
            <div>
                <label for="edit_rango_inicio" class="block text-sm font-medium text-gray-700 nunito-bold">Rango
                    Inicio</label>
                <input type="text" id="edit_rango_inicio" name="edit_rango_inicio" :value="itemToEdit?.rango_inicio"
                    placeholder="000-000-00-00000000"
                    title="Formato: 000-000-00-00000000. Se autoformatea mientras escribe."
                    @input="$el.value = window.caiApiHandlers.formatRango($el.value)"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    aria-describedby="hint-rini-edit">
                <p id="hint-rini-edit" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formato:
                    000-000-00-00000000</p>
            </div>
            <div>
                <label for="edit_rango_fin" class="block text-sm font-medium text-gray-700 nunito-bold">Rango
                    Fin</label>
                <input type="text" id="edit_rango_fin" name="edit_rango_fin" :value="itemToEdit?.rango_fin"
                    placeholder="000-000-00-00000000"
                    title="Formato: 000-000-00-00000000. Se autoformatea mientras escribe."
                    @input="$el.value = window.caiApiHandlers.formatRango($el.value)"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    aria-describedby="hint-rfin-edit">
                <p id="hint-rfin-edit" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formato:
                    000-000-00-00000000</p>
            </div>
            <div>
                <label for="edit_consecutivo_actual"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Consecutivo Actual</label>
                <input type="text" id="edit_consecutivo_actual" name="edit_consecutivo_actual"
                    :value="itemToEdit?.consecutivo_actual" inputmode="numeric" placeholder="Ej: 999"
                    title="Solo números" @input="$el.value = window.caiApiHandlers.onlyDigits($el.value)"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    aria-describedby="hint-consec-edit">
                <p id="hint-consec-edit" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Solo números</p>
            </div>
            <div>
                <label for="edit_fecha_limite" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha
                    Límite</label>
                <input type="date" id="edit_fecha_limite" name="edit_fecha_limite" :value="itemToEdit?.fecha_limite"
                    placeholder="dd/mm/aaaa o yyyy-mm-dd"
                    title="Puede ingresar dd/mm/aaaa o yyyy-mm-dd. Se normaliza a yyyy-mm-dd."
                    @blur="$el.value = window.caiApiHandlers.normalizeFecha($el.value)"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    aria-describedby="hint-fecha-edit">
                <p id="hint-fecha-edit" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Se normaliza a yyyy-mm-dd
                </p>
            </div>
            <div>
                <label for="edit_id_estado_cai_fk" class="block text-sm font-medium text-gray-700 nunito-bold">Estado
                    CAI</label>
                <select id="edit_id_estado_cai_fk" name="edit_id_estado_cai_fk" :value="itemToEdit?.id_estado_cai_fk"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <option value="">Seleccionar estado</option>
                    <template x-for="estado in estadosCai" :key="estado.id_estado_cai_pk || estado.id">
                        <option :value="estado.id_estado_cai_pk || estado.id"
                            x-text="estado.nombre_estado_cai || estado.nombre"
                            :selected="(estado.id_estado_cai_pk || estado.id) === itemToEdit?.id_estado_cai_fk"
                            class="nunito-regular"></option>
                    </template>
                </select>
            </div>
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-bold" modalName="isDeleteCaiModalOpen" itemToDelete="itemToDelete"
        message="¿Estás seguro de que quieres eliminar este CAI?" />
</div>