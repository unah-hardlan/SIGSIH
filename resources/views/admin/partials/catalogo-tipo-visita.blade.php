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
@confirm-delete.window="handleDelete()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Catálogo de Tipos de Visita</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
                'searchModel' => 'filtroTipoVisita',
                'ordenarOptions' => [
                    'nombre' => 'Nombre',
                    'id' => 'ID Tipo'
                ]
            ])
        </x-slot>

        <x-slot name="actions">
            <button
                @click="isTipoVisitaModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo tipo de visita
            </button>
        </x-slot>

        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Nombre</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Descripción</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loadingTipoVisitas">
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando tipos de visita...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingTipoVisitas && tipoVisitas.length === 0">
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500 nunito-regular">
                                No hay tipos de visita registrados
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingTipoVisitas && tipoVisitas.length > 0">
                        <template x-for="(tipoVisita, index) in tipoVisitas" :key="tipoVisita.id_tipo_visita_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === tipoVisitas.length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="tipoVisita.nombre_tipo_visita"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="tipoVisita.descripcion_tipo_visita"></td>
                                <td class="py-2 px-4 flex gap-2" :class="{ 'last:rounded-br-lg': index === tipoVisitas.length - 1 }">
                                    <a href="#" @click.prevent="isTipoVisitaEditModalOpen = true; itemToEdit = {id_tipo_visita_pk: tipoVisita.id_tipo_visita_pk, nombre_tipo_visita: tipoVisita.nombre_tipo_visita, descripcion_tipo_visita: tipoVisita.descripcion_tipo_visita}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    <a href="#" @click.prevent="isTipoVisitaDeleteModalOpen = true; itemToDelete = {id_tipo_visita_pk: tipoVisita.id_tipo_visita_pk, nombre: tipoVisita.nombre_tipo_visita}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loadingTipoVisitas">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando tipos de visita...
                </div>
            </template>
            <template x-if="!loadingTipoVisitas && tipoVisitas.length === 0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    No hay tipos de visita registrados
                </div>
            </template>
            <template x-if="!loadingTipoVisitas && tipoVisitas.length > 0">
                <template x-for="tipoVisita in tipoVisitas" :key="tipoVisita.id_tipo_visita_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="tipoVisita.nombre_tipo_visita"></h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="tipoVisita.descripcion_tipo_visita"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="isTipoVisitaEditModalOpen = true; itemToEdit = {id_tipo_visita_pk: tipoVisita.id_tipo_visita_pk, nombre_tipo_visita: tipoVisita.nombre_tipo_visita, descripcion_tipo_visita: tipoVisita.descripcion_tipo_visita}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="isTipoVisitaDeleteModalOpen = true; itemToDelete = {id_tipo_visita_pk: tipoVisita.id_tipo_visita_pk, nombre: tipoVisita.nombre_tipo_visita}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <!-- Modales -->
    <div>
        <!-- Modal Nuevo Tipo de Visita -->
        <x-admin.form-modal class="nunito-bold" modalName="isTipoVisitaModalOpen" title="Nuevo Tipo de Visita"
            submitLabel="Guardar Tipo de Visita" formId="formTipoVisita" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre_tipo_visita" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="nombre_tipo_visita" x-model="nombre_tipo_visita" required
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
                    <input type="text" id="edit_nombre_tipo_visita" x-model="itemToEdit.nombre_tipo_visita" required
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
