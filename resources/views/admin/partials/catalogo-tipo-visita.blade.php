<script src="{{ asset('js/tipo-visitas.js') }}"></script>

<div x-data="{
    isTipoVisitaModalOpen: false,
    isTipoVisitaEditModalOpen: false,
    isTipoVisitaDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    tipoVisitas: [],
    loadingTipoVisitas: false,
    nombre_tipo_visita: '',
    descripcion_tipo_visita: '',
    async fetchTipoVisitas() {
        await window.tipoVisitasApiHandlers.fetchTipoVisitas(this);
    },
    async submitTipoVisita() {
        await window.tipoVisitasApiHandlers.submitTipoVisita(this);
    },
    async updateTipoVisita() {
        await window.tipoVisitasApiHandlers.updateTipoVisita(this);
    },
    async deleteTipoVisita() {
        await window.tipoVisitasApiHandlers.deleteTipoVisita(this);
    },
    handleModalSubmit(event) {
        if(event.detail.formId === 'formTipoVisita') this.submitTipoVisita();
        if(event.detail.formId === 'formEditTipoVisita') this.updateTipoVisita();
    },
    handleDelete() {
        if (this.isTipoVisitaDeleteModalOpen) {
            this.deleteTipoVisita();
        }
    }
}"
x-init="fetchTipoVisitas()"
@keydown.escape.window="
    isTipoVisitaModalOpen = false;
    isTipoVisitaEditModalOpen = false;
    isTipoVisitaDeleteModalOpen = false;
"
@modal-submit.window="handleModalSubmit($event)"
@confirm-delete.window="handleDelete()"
    <!-- Tabla Mobile -->
    <x-admin.tabla-mobile titulo="Tipo de Visita" class="nunito-bold bg-white dark:bg-gray-900">
        <x-slot name="filtros">
            @include('partials.filtros-generales', [
                'searchModel' => 'filtroTipoVisita',
                'ordenarOptions' => [
                    'nombre' => 'Nombre',
                    'id' => 'ID Tipo'
                ]
            ])
        </x-slot>
        <x-slot name="boton">
            <button 
                @click="isTipoVisitaModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm"
            >
                Nuevo tipo de visita
            </button>
        </x-slot>
        
        <table class="min-w-full text-sm bg-white dark:bg-gray-900">
            <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">ID Tipo</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Nombre</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Descripción</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="tipoVisita in tipoVisitas" :key="tipoVisita.id_tipo_visita_pk">
                    <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="tipoVisita.id_tipo_visita_pk"></td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="tipoVisita.nombre_tipo_visita"></td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="tipoVisita.descripcion_tipo_visita"></td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#" @click.prevent="isTipoVisitaEditModalOpen = true; itemToEdit = {id_tipo_visita_pk: tipoVisita.id_tipo_visita_pk, nombre_tipo_visita: tipoVisita.nombre_tipo_visita, descripcion_tipo_visita: tipoVisita.descripcion_tipo_visita}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click.prevent="isTipoVisitaDeleteModalOpen = true; itemToDelete = {id_tipo_visita_pk: tipoVisita.id_tipo_visita_pk, nombre: tipoVisita.nombre_tipo_visita}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <x-slot name="mobileTemplate">
            <div class="space-y-4">
                <template x-for="tipoVisita in tipoVisitas" :key="tipoVisita.id_tipo_visita_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="tipoVisita.nombre_tipo_visita"></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular" x-text="'ID: ' + tipoVisita.id_tipo_visita_pk"></p>
                            </div>
                        </div>
                        <div class="space-y-1 text-sm">
                            <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Descripción:</span> <span class="text-gray-900 dark:text-gray-200 nunito-regular" x-text="tipoVisita.descripcion_tipo_visita"></span></div>
                        </div>
                        <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="isTipoVisitaEditModalOpen = true; itemToEdit = {id_tipo_visita_pk: tipoVisita.id_tipo_visita_pk, nombre_tipo_visita: tipoVisita.nombre_tipo_visita, descripcion_tipo_visita: tipoVisita.descripcion_tipo_visita}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="isTipoVisitaDeleteModalOpen = true; itemToDelete = {id_tipo_visita_pk: tipoVisita.id_tipo_visita_pk, nombre: tipoVisita.nombre_tipo_visita}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </x-slot>
    </x-admin.tabla-mobile>

    <!-- Modales -->
    <div>
        <!-- Modal Nuevo Tipo de Visita -->
        <x-admin.form-modal class="nunito-bold" modalName="isTipoVisitaModalOpen" title="Nuevo Tipo de Visita"
            submitLabel="Guardar Tipo de Visita" formId="formTipoVisita" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre_tipo_visita" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="nombre_tipo_visita" x-model="nombre_tipo_visita"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div class="col-span-2">
                    <label for="descripcion_tipo_visita"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="descripcion_tipo_visita" x-model="descripcion_tipo_visita" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Tipo de Visita -->
        <x-admin.edit-modal class="nunito-bold" modalName="isTipoVisitaEditModalOpen" title="Editar Tipo de Visita" itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditTipoVisita">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_nombre_tipo_visita" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="edit_nombre_tipo_visita" x-model="itemToEdit.nombre_tipo_visita"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div class="col-span-2">
                    <label for="edit_descripcion_tipo_visita"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="edit_descripcion_tipo_visita" x-model="itemToEdit.descripcion_tipo_visita" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                </div>
            </div>
        </x-admin.edit-modal>

        <!-- Modal Confirmar Eliminación -->
        <x-admin.confirmation-modal class="nunito-regular" modalName="isTipoVisitaDeleteModalOpen" itemToDelete="itemToDelete"
            message="¿Estás seguro de que quieres eliminar este tipo de visita?" />
    </div>
</div>
