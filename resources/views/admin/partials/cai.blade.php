<div x-data="{ 
    tab: 'cai', 
    isCaiModalOpen: false, 
    isEditCaiModalOpen: false, 
    itemToEdit: null, 
    isDeleteCaiModalOpen: false, 
    itemToDelete: null,
    formCai: { _touched: {} },
    formEditCai: { _touched: {} },
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
    normalizeStr(v){
        try{ return (v ?? '').toString().normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().trim(); }catch(_){ return (v ?? '').toString().toLowerCase().trim(); }
    },
    equalsNormalized(a,b){ if(!b) return true; return this.normalizeStr(a) === this.normalizeStr(b); },
    filteredCais(){
        try{
            let items = Array.isArray(this.cais) ? [...this.cais] : [];
            if(this.estadoCaiFiltro){
                items = items.filter(c => this.equalsNormalized(c?.estado_cai?.nombre || c?.estado_cai?.nombre_estado_cai || '', this.estadoCaiFiltro));
            }
            const q = this.normalizeStr(this.searchCai);
            if(q){
                items = items.filter(c => {
                    const fields = [c?.codigo, c?.rango_inicio, c?.rango_fin, c?.fecha_limite, c?.estado_cai?.nombre || c?.estado_cai?.nombre_estado_cai];
                    return fields.some(v => this.normalizeStr(v).includes(q));
                });
            }
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

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">CAI</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
            'searchModel' => 'searchCai',
            'ordenarModel' => 'ordenarPor',
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

        <x-slot name="actions">
            @perm(['CAI','Facturación','Facturacion','Gestión de Facturacion','Gestion de Facturacion'], 'insercion')
            <button @click="isCaiModalOpen = true; formCai._touched = {}"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo CAI
            </button>
            @else
            <button type="button" disabled title="Sin permiso para crear CAI"
                class="bg-green-600 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm opacity-60 cursor-not-allowed">
                Nuevo CAI
            </button>
            @endperm
            <a href="/admin/reportes-header?modulo=CAI&fecha={{ \App\Helpers\DateHelper::nowFormatted('d/m/Y') }}" target="_blank"
                class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 text-sm">
                <i class="fas fa-file-alt"></i> Generar Reporte
            </a>
        </x-slot>

        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Código</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Rango Inicio</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Rango Fin</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Consecutivo</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Fecha Límite</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Estado CAI</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loadingCai">
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando registros CAI...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingCai && cais.length === 0">
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500 nunito-regular">
                                No hay registros CAI
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingCai && cais.length > 0">
                        <template x-for="(cai, index) in paginatedCais()" :key="cai.id_cai_pk || cai.id">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === cais.length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="cai.codigo"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="cai.rango_inicio"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="cai.rango_fin"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="cai.consecutivo_actual"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="cai.fecha_limite"></td>
                                <td class="py-2 px-4">
                                    <span class="px-2 py-1 rounded text-xs nunito-regular"
                                        :class="cai.estado_cai?.es_final ? 'bg-red-100 dark:bg-red-800 text-red-600 dark:text-red-100' : 'bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-100'"
                                        x-text="cai.estado_cai?.nombre || 'Sin estado'"></span>
                                </td>
                                <td class="py-2 px-4 flex gap-2" :class="{ 'last:rounded-br-lg': index === cais.length - 1 }">
                                    @perm(['CAI','Facturación','Facturacion','Gestión de Facturacion','Gestion de Facturacion'], 'actualizacion')
                                    <a href="#" @click.prevent="formEditCai._touched = {}; itemToEdit = { 
                                            ...cai,
                                            codigo: window.caiApiHandlers.normalizeCodigo(cai.codigo),
                                            rango_inicio: window.caiApiHandlers.formatRango(cai.rango_inicio),
                                            rango_fin: window.caiApiHandlers.formatRango(cai.rango_fin),
                                            consecutivo_actual: window.caiApiHandlers.onlyDigits(cai.consecutivo_actual),
                                            fecha_limite: window.caiApiHandlers.normalizeFecha(cai.fecha_limite)
                                        }; isEditCaiModalOpen = true;"
                                        class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    @else
                                    <span class="text-blue-300 cursor-not-allowed" title="Sin permiso para editar"><i class="fas fa-edit"></i></span>
                                    @endperm

                                    @perm(['CAI','Facturación','Facturacion','Gestión de Facturacion','Gestion de Facturacion'], 'eliminacion')
                                    <a href="#" @click.prevent="itemToDelete = {...cai}; isDeleteCaiModalOpen = true;"
                                        class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                    @else
                                    <span class="text-red-300 cursor-not-allowed" title="Sin permiso para eliminar"><i class="fas fa-trash"></i></span>
                                    @endperm
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">

            <template x-if="loadingCai">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando registros CAI...
                </div>
            </template>
            <template x-if="!loadingCai && cais.length === 0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center text-gray-500 nunito-regular">
                    No hay registros CAI
                </div>
            </template>
            <template x-if="!loadingCai && cais.length > 0">
                <template x-for="cai in paginatedCais()" :key="cai.id_cai_pk || cai.id">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-2 border dark:border-gray-800 border-black">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="cai.codigo"></h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 nunito-regular"
                                    x-text="'Fecha límite: '+(cai.fecha_limite||'-')"></p>
                            </div>
                            <div>
                                <span class="px-2 py-1 rounded text-xs"
                                    :class="cai.estado_cai?.es_final ? 'bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-100' : 'bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-100'"
                                    x-text="cai.estado_cai?.nombre || 'Sin estado'"></span>
                            </div>
                        </div>
                        <div class="text-sm text-gray-700 dark:text-gray-300 grid grid-cols-2 gap-2 nunito-regular">
                            <div><span class="nunito-bold text-gray-600 dark:text-gray-300">Rango Inicio:</span> <span
                                    x-text="cai.rango_inicio || '-'"></span></div>
                            <div><span class="nunito-bold text-gray-600 dark:text-gray-300">Rango Fin:</span> <span
                                    x-text="cai.rango_fin || '-'"></span></div>
                            <div><span class="nunito-bold text-gray-600 dark:text-gray-300">Consecutivo:</span> <span
                                    x-text="cai.consecutivo_actual || '0'"></span></div>
                        </div>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            @perm(['CAI','Facturación','Facturacion','Gestión de Facturacion','Gestion de Facturacion'], 'actualizacion')
                            <button @click.prevent="formEditCai._touched = {}; itemToEdit = { 
                                    ...cai,
                                    codigo: window.caiApiHandlers.normalizeCodigo(cai.codigo),
                                    rango_inicio: window.caiApiHandlers.formatRango(cai.rango_inicio),
                                    rango_fin: window.caiApiHandlers.formatRango(cai.rango_fin),
                                    consecutivo_actual: window.caiApiHandlers.onlyDigits(cai.consecutivo_actual),
                                    fecha_limite: window.caiApiHandlers.normalizeFecha(cai.fecha_limite)
                                }; isEditCaiModalOpen = true;"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @else
                            <button type="button" disabled title="Sin permiso para editar"
                                class="px-3 py-1 text-xs bg-blue-400 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @endperm

                            @perm(['CAI','Facturación','Facturacion','Gestión de Facturacion','Gestion de Facturacion'], 'eliminacion')
                            <button @click.prevent="itemToDelete = {...cai}; isDeleteCaiModalOpen = true;"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @else
                            <button type="button" disabled title="Sin permiso para eliminar"
                                class="px-3 py-1 text-xs bg-red-400 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @endperm
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <x-pagination />

    <x-admin.form-modal class="nunito-bold" modalName="isCaiModalOpen" title="Nuevo CAI" submitLabel="Guardar CAI"
        maxWidth="max-w-md" formId="formCai">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="codigo" class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                <input type="text" id="codigo" name="codigo" x-model="codigo" maxlength="50" placeholder="Ej: CAI-2024-003-C"
                    title="Solo letras, números y guiones. Se normaliza automáticamente."
                    @input="formCai._touched.codigo = true" @blur="codigo = window.caiApiHandlers.normalizeCodigo(codigo); formCai._touched.codigo = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formCai._touched && formCai._touched.codigo && (codigo === '' || (codigo && codigo.length >= 50)) ? 'border-red-500' : ''"
                    aria-describedby="hint-cod">
                <p id="hint-cod" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formato sugerido: CAI-AAAA-XXX-X. Se convierte a mayúsculas y sin espacios.</p>
                <small class="block mt-1 text-sm text-gray-500" :class="formCai._touched && formCai._touched.codigo && (codigo === '' || (codigo && codigo.length >= 50)) ? 'text-red-500' : ''">Requerido. Máximo 50 caracteres.</small>
            </div>
            <div>
                <label for="rango_inicio" class="block text-sm font-medium text-gray-700 nunito-bold">Rango
                    Inicio</label>
                <input type="text" id="rango_inicio" name="rango_inicio" x-model="rango_inicio" maxlength="25"
                    placeholder="000-000-00-00000000"
                    title="Formato: 000-000-00-00000000. Se autoformatea mientras escribe."
                    @input="rango_inicio = window.caiApiHandlers.formatRango(rango_inicio); formCai._touched.rango_inicio = true" @blur="formCai._touched.rango_inicio = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formCai._touched && formCai._touched.rango_inicio && !rango_inicio ? 'border-red-500' : ''"
                    aria-describedby="hint-rini">
                <p id="hint-rini" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formato: 000-000-00-00000000</p>
                <small class="block mt-1 text-sm text-gray-500" :class="formCai._touched && formCai._touched.rango_inicio && !rango_inicio ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="rango_fin" class="block text-sm font-medium text-gray-700 nunito-bold">Rango Fin</label>
                <input type="text" id="rango_fin" name="rango_fin" x-model="rango_fin" maxlength="25" placeholder="000-000-00-00000000"
                    title="Formato: 000-000-00-00000000. Se autoformatea mientras escribe."
                    @input="rango_fin = window.caiApiHandlers.formatRango(rango_fin); formCai._touched.rango_fin = true" @blur="formCai._touched.rango_fin = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formCai._touched && formCai._touched.rango_fin && !rango_fin ? 'border-red-500' : ''"
                    aria-describedby="hint-rfin">
                <p id="hint-rfin" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formato: 000-000-00-00000000</p>
                <small class="block mt-1 text-sm text-gray-500" :class="formCai._touched && formCai._touched.rango_fin && !rango_fin ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="consecutivo_actual" class="block text-sm font-medium text-gray-700 nunito-bold">Consecutivo
                    Actual</label>
                <input type="text" id="consecutivo_actual" name="consecutivo_actual" x-model="consecutivo_actual" maxlength="12"
                    placeholder="Ej: 999" title="Solo números" inputmode="numeric"
                    @input="consecutivo_actual = window.caiApiHandlers.onlyDigits(consecutivo_actual); formCai._touched.consecutivo_actual = true" @blur="formCai._touched.consecutivo_actual = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formCai._touched && formCai._touched.consecutivo_actual && !consecutivo_actual ? 'border-red-500' : ''"
                    aria-describedby="hint-consec">
                <p id="hint-consec" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Solo números</p>
                <small class="block mt-1 text-sm text-gray-500" :class="formCai._touched && formCai._touched.consecutivo_actual && !consecutivo_actual ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="fecha_limite" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha
                    Límite</label>
                <input type="date" id="fecha_limite" name="fecha_limite" x-model="fecha_limite" x-ref="fecha_limite"
                    placeholder="dd/mm/aaaa o yyyy-mm-dd"
                    title="Puede ingresar dd/mm/aaaa o yyyy-mm-dd. Se normaliza a yyyy-mm-dd."
                    @blur="fecha_limite = window.caiApiHandlers.normalizeFecha(fecha_limite); formCai._touched.fecha_limite = true" @input="formCai._touched.fecha_limite = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formCai._touched && formCai._touched.fecha_limite && !fecha_limite ? 'border-red-500' : ''"
                    aria-describedby="hint-fecha">
                <p id="hint-fecha" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Se normaliza a yyyy-mm-dd</p>
                <small class="block mt-1 text-sm text-gray-500" :class="formCai._touched && formCai._touched.fecha_limite && !fecha_limite ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="id_estado_cai_fk" class="block text-sm font-medium text-gray-700 nunito-bold">Estado
                    CAI</label>
                <select id="id_estado_cai_fk" name="id_estado_cai_fk" x-model="id_estado_cai_fk" @change="formCai._touched.estado = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formCai._touched && formCai._touched.estado && !id_estado_cai_fk ? 'border-red-500' : ''">
                    <option value="">Seleccionar estado</option>
                    <template x-for="estado in estadosCai" :key="estado.id_estado_cai_pk || estado.id">
                        <option :value="estado.id_estado_cai_pk || estado.id"
                            x-text="estado.nombre_estado_cai || estado.nombre" class="nunito-regular"></option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500" :class="formCai._touched && formCai._touched.estado && !id_estado_cai_fk ? 'text-red-500' : ''">Requerido.</small>
            </div>
        </div>
    </x-admin.form-modal>

    <x-admin.edit-modal class="nunito-bold" modalName="isEditCaiModalOpen" title="Editar CAI" itemToEdit="itemToEdit"
        maxWidth="max-w-md" formId="formEditCai">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="edit_codigo" class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                <input type="text" id="edit_codigo" name="edit_codigo" :value="itemToEdit?.codigo" x-ref="edit_codigo" maxlength="50"
                    placeholder="Ej: CAI-2024-003-C"
                    title="Solo letras, números y guiones. Se normaliza automáticamente."
                    @input="$el.value = $el.value; formEditCai._touched.codigo = true" @blur="$el.value = window.caiApiHandlers.normalizeCodigo($el.value); formEditCai._touched.codigo = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formEditCai._touched && formEditCai._touched.codigo && (!$refs.edit_codigo.value || $refs.edit_codigo.value.length >= 50) ? 'border-red-500' : ''"
                    aria-describedby="hint-cod-edit">
                <p id="hint-cod-edit" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formato sugerido:
                    CAI-AAAA-XXX-X</p>
                <small class="block mt-1 text-sm text-gray-500" :class="formEditCai._touched && formEditCai._touched.codigo && (!$refs.edit_codigo.value || $refs.edit_codigo.value.length >= 50) ? 'text-red-500' : ''">Requerido. Máximo 50 caracteres.</small>
            </div>
            <div>
                <label for="edit_rango_inicio" class="block text-sm font-medium text-gray-700 nunito-bold">Rango
                    Inicio</label>
                <input type="text" id="edit_rango_inicio" name="edit_rango_inicio" :value="itemToEdit?.rango_inicio" x-ref="edit_rango_inicio" maxlength="25"
                    placeholder="000-000-00-00000000"
                    title="Formato: 000-000-00-00000000. Se autoformatea mientras escribe."
                    @input="$el.value = window.caiApiHandlers.formatRango($el.value); formEditCai._touched.rango_inicio = true" @blur="formEditCai._touched.rango_inicio = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formEditCai._touched && formEditCai._touched.rango_inicio && !$refs.edit_rango_inicio.value ? 'border-red-500' : ''"
                    aria-describedby="hint-rini-edit">
                <p id="hint-rini-edit" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formato:
                    000-000-00-00000000</p>
                <small class="block mt-1 text-sm text-gray-500" :class="formEditCai._touched && formEditCai._touched.rango_inicio && !$refs.edit_rango_inicio.value ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="edit_rango_fin" class="block text-sm font-medium text-gray-700 nunito-bold">Rango
                    Fin</label>
                <input type="text" id="edit_rango_fin" name="edit_rango_fin" :value="itemToEdit?.rango_fin" x-ref="edit_rango_fin" maxlength="25"
                    placeholder="000-000-00-00000000"
                    title="Formato: 000-000-00-00000000. Se autoformatea mientras escribe."
                    @input="$el.value = window.caiApiHandlers.formatRango($el.value); formEditCai._touched.rango_fin = true" @blur="formEditCai._touched.rango_fin = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formEditCai._touched && formEditCai._touched.rango_fin && !$refs.edit_rango_fin.value ? 'border-red-500' : ''"
                    aria-describedby="hint-rfin-edit">
                <p id="hint-rfin-edit" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formato:
                    000-000-00-00000000</p>
                <small class="block mt-1 text-sm text-gray-500" :class="formEditCai._touched && formEditCai._touched.rango_fin && !$refs.edit_rango_fin.value ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="edit_consecutivo_actual"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Consecutivo Actual</label>
                <input type="text" id="edit_consecutivo_actual" name="edit_consecutivo_actual" x-ref="edit_consecutivo_actual"
                    :value="itemToEdit?.consecutivo_actual" inputmode="numeric" maxlength="12" placeholder="Ej: 999"
                    title="Solo números" @input="$el.value = window.caiApiHandlers.onlyDigits($el.value); formEditCai._touched.consecutivo_actual = true" @blur="formEditCai._touched.consecutivo_actual = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formEditCai._touched && formEditCai._touched.consecutivo_actual && !$refs.edit_consecutivo_actual.value ? 'border-red-500' : ''"
                    aria-describedby="hint-consec-edit">
                <p id="hint-consec-edit" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Solo números</p>
                <small class="block mt-1 text-sm text-gray-500" :class="formEditCai._touched && formEditCai._touched.consecutivo_actual && !$refs.edit_consecutivo_actual.value ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="edit_fecha_limite" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha
                    Límite</label>
                <input type="date" id="edit_fecha_limite" name="edit_fecha_limite" :value="itemToEdit?.fecha_limite" x-ref="edit_fecha_limite"
                    placeholder="dd/mm/aaaa o yyyy-mm-dd"
                    title="Puede ingresar dd/mm/aaaa o yyyy-mm-dd. Se normaliza a yyyy-mm-dd."
                    @blur="$el.value = window.caiApiHandlers.normalizeFecha($el.value); formEditCai._touched.fecha_limite = true" @input="formEditCai._touched.fecha_limite = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formEditCai._touched && formEditCai._touched.fecha_limite && !$refs.edit_fecha_limite.value ? 'border-red-500' : ''"
                    aria-describedby="hint-fecha-edit">
                <p id="hint-fecha-edit" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Se normaliza a yyyy-mm-dd
                </p>
                <small class="block mt-1 text-sm text-gray-500" :class="formEditCai._touched && formEditCai._touched.fecha_limite && !$refs.edit_fecha_limite.value ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="edit_id_estado_cai_fk" class="block text-sm font-medium text-gray-700 nunito-bold">Estado
                    CAI</label>
                <select id="edit_id_estado_cai_fk" name="edit_id_estado_cai_fk" :value="itemToEdit?.id_estado_cai_fk" x-ref="edit_id_estado_cai_fk" @change="formEditCai._touched.estado = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formEditCai._touched && formEditCai._touched.estado && !$refs.edit_id_estado_cai_fk.value ? 'border-red-500' : ''">
                    <option value="">Seleccionar estado</option>
                    <template x-for="estado in estadosCai" :key="estado.id_estado_cai_pk || estado.id">
                        <option :value="estado.id_estado_cai_pk || estado.id"
                            x-text="estado.nombre_estado_cai || estado.nombre"
                            :selected="(estado.id_estado_cai_pk || estado.id) === itemToEdit?.id_estado_cai_fk"
                            class="nunito-regular"></option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500" :class="formEditCai._touched && formEditCai._touched.estado && !$refs.edit_id_estado_cai_fk.value ? 'text-red-500' : ''">Requerido.</small>
            </div>
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-bold" modalName="isDeleteCaiModalOpen" itemToDelete="itemToDelete"
        message="¿Estás seguro de que quieres eliminar este CAI?" />
</div>