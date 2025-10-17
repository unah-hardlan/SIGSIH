<div x-data="{ 
    tab: 'cai', 
    isCaiModalOpen: false, 
    isEditCaiModalOpen: false, 
    itemToEdit: null, 
    isDeleteCaiModalOpen: false, 
    itemToDelete: null,
    cais: [],
    estadosCai: [],
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
    ordenarPor: '',
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
    handleModalSubmit(event) {
        if(event.detail.formId === 'formCai') this.submitCai();
        if(event.detail.formId === 'formEditCai') this.updateCai();
    },
    handleDelete() {
        if (this.isDeleteCaiModalOpen) {
            this.deleteCai();
        }
    }
}"
x-init="fetchCai()"
@modal-submit.window="handleModalSubmit($event)"
@confirm-delete.window="handleDelete()">

    <div x-show="tab==='cai'" class="overflow-x-auto">
        <x-admin.tabla-crud class="nunito-bold">
            <x-slot name="titulo">
                <h2 class="text-2xl dark:text-white text-gray-800 nunito-bold">CAI</h2>
            </x-slot>
            <x-slot name="filtros">
                @include('partials.filtros-generales', [
                    'searchModel' => 'searchCai',
                    'filtrosSelect' => [
                        'estadoCaiFiltro' => [
                            'label' => 'Estado',
                            'options' => ['Activo', 'Inactivo']
                        ]
                    ],
                    'ordenarOptions' => [
                        'fecha_limite' => 'Fecha Límite',
                        'codigo' => 'Código',
                        'estado_cai' => 'Estado'
                    ]
                ])
            </x-slot>
            <x-slot name="boton">
                <div class="flex gap-2 items-stretch">
                    <button @click="isCaiModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nuevo CAI</button>
                    <a href="/admin/reportes-header?modulo=CAI&fecha={{ now()->format('d-M-Y') }}" target="_blank"
                       class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 text-sm">
                        <i class="fas fa-file-alt"></i> Generar Reporte
                    </a>
                </div>
            </x-slot>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
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
                        <template x-if="cais.length > 0">
                            <template x-for="cai in cais" :key="cai.id_cai_pk || cai.id">
                                <tr class="border-b nunito-regular bg-white dark:bg-gray-900">
                                    
                                    <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white" x-text="cai.codigo"></td>
                                    <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white" x-text="cai.rango_inicio"></td>
                                    <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white" x-text="cai.rango_fin"></td>
                                    <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white" x-text="cai.consecutivo_actual"></td>
                                    <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white" x-text="cai.fecha_limite"></td>
                                    <td class="py-2 px-4 nunito-regular">
                                        <span class="px-2 py-1 rounded nunito-regular"
                                              :class="cai.estado_cai?.es_final ? 'bg-red-100 dark:bg-red-800 text-red-600 dark:text-red-100' : 'bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-100'"
                                              x-text="cai.estado_cai?.nombre || 'Sin estado'">
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 flex gap-2">
                                        <a href="#" @click.prevent="itemToEdit = {...cai}; isEditCaiModalOpen = true;" class="text-blue-500 hover:text-blue-700 dark:text-blue-300"><i class="fas fa-edit"></i></a>
                                        <a href="#" @click.prevent="itemToDelete = {...cai}; isDeleteCaiModalOpen = true;" class="text-red-500 hover:text-red-700 dark:text-red-400"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            </template>
                        </template>
                        <template x-if="cais.length === 0 && !loadingCai">
                            <tr class="border-b nunito-regular bg-white dark:bg-gray-900">
                                <td colspan="8" class="py-4 px-4 text-center text-gray-500 dark:text-gray-400">
                                    No hay registros CAI
                                </td>
                            </tr>
                        </template>
                        <template x-if="loadingCai">
                            <tr class="border-b nunito-regular bg-white dark:bg-gray-900">
                                <td colspan="8" class="py-4 px-4 text-center text-gray-500 dark:text-gray-400">
                                    Cargando registros CAI...
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </x-admin.tabla-crud>
    </div>

    {{-- Modales --}}
    <x-admin.form-modal class="nunito-bold"
        modalName="isCaiModalOpen" 
        title="Nuevo CAI" 
        submitLabel="Guardar CAI"
        maxWidth="max-w-md"
        formId="formCai">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="codigo" class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                <input type="text" id="codigo" name="codigo" x-model="codigo" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="rango_inicio" class="block text-sm font-medium text-gray-700 nunito-bold">Rango Inicio</label>
                <input type="text" id="rango_inicio" name="rango_inicio" x-model="rango_inicio" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="rango_fin" class="block text-sm font-medium text-gray-700 nunito-bold">Rango Fin</label>
                <input type="text" id="rango_fin" name="rango_fin" x-model="rango_fin" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="consecutivo_actual" class="block text-sm font-medium text-gray-700 nunito-bold">Consecutivo Actual</label>
                <input type="number" id="consecutivo_actual" name="consecutivo_actual" x-model="consecutivo_actual" min="0" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="fecha_limite" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha Límite</label>
                <input type="date" id="fecha_limite" name="fecha_limite" x-model="fecha_limite" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="id_estado_cai_fk" class="block text-sm font-medium text-gray-700 nunito-bold">Estado CAI</label>
                <select id="id_estado_cai_fk" name="id_estado_cai_fk" x-model="id_estado_cai_fk" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="">Seleccionar estado</option>
                    <template x-for="estado in estadosCai" :key="estado.id_estado_cai_pk || estado.id">
                        <option :value="estado.id_estado_cai_pk || estado.id" x-text="estado.nombre_estado_cai || estado.nombre" class="nunito-regular"></option>
                    </template>
                </select>
            </div>
        </div>
    </x-admin.form-modal>

    <x-admin.edit-modal class="nunito-bold"
        modalName="isEditCaiModalOpen" 
        title="Editar CAI" 
        itemToEdit="itemToEdit"
        maxWidth="max-w-md"
        formId="formEditCai">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="edit_codigo" class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                <input type="text" id="edit_codigo" name="edit_codigo" :value="itemToEdit?.codigo" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_rango_inicio" class="block text-sm font-medium text-gray-700 nunito-bold">Rango Inicio</label>
                <input type="text" id="edit_rango_inicio" name="edit_rango_inicio" :value="itemToEdit?.rango_inicio" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_rango_fin" class="block text-sm font-medium text-gray-700 nunito-bold">Rango Fin</label>
                <input type="text" id="edit_rango_fin" name="edit_rango_fin" :value="itemToEdit?.rango_fin" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_consecutivo_actual" class="block text-sm font-medium text-gray-700 nunito-bold">Consecutivo Actual</label>
                <input type="number" id="edit_consecutivo_actual" name="edit_consecutivo_actual" :value="itemToEdit?.consecutivo_actual" min="0" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_fecha_limite" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha Límite</label>
                <input type="date" id="edit_fecha_limite" name="edit_fecha_limite" :value="itemToEdit?.fecha_limite" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_id_estado_cai_fk" class="block text-sm font-medium text-gray-700 nunito-bold">Estado CAI</label>
                <select id="edit_id_estado_cai_fk" name="edit_id_estado_cai_fk" :value="itemToEdit?.id_estado_cai_fk" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="">Seleccionar estado</option>
                    <template x-for="estado in estadosCai" :key="estado.id_estado_cai_pk || estado.id">
                        <option :value="estado.id_estado_cai_pk || estado.id" x-text="estado.nombre_estado_cai || estado.nombre" :selected="(estado.id_estado_cai_pk || estado.id) === itemToEdit?.id_estado_cai_fk" class="nunito-regular"></option>
                    </template>
                </select>
            </div>
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-bold"
        modalName="isDeleteCaiModalOpen"
        itemToDelete="itemToDelete"
        message="¿Estás seguro de que quieres eliminar este CAI?"
    />
</div>
