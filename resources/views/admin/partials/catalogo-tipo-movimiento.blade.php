@vite(['resources/js/tipo-movimientos.js'])

<div x-data="{
    isTipoMovimientoModalOpen: false,
    isTipoMovimientoEditModalOpen: false,
    isTipoMovimientoDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    tipoMovimientos: [],
    loadingTipoMovimientos: false,
    nombre_tipo_movimiento: '',
    descripcion_tipo_movimiento: '',
    filtroTipoMovimiento: '',
    ordenarPor: '',
    async fetchTipoMovimientos() {
        await window.tipoMovimientosApiHandlers.fetchTipoMovimientos(this);
    },
    async submitTipoMovimiento() {
        await window.tipoMovimientosApiHandlers.submitTipoMovimiento(this);
    },
    async updateTipoMovimiento() {
        await window.tipoMovimientosApiHandlers.updateTipoMovimiento(this);
    },
    async deleteTipoMovimiento() {
        await window.tipoMovimientosApiHandlers.deleteTipoMovimiento(this);
    },
    handleModalSubmit(event) {
        if(event.detail.formId === 'formTipoMovimiento') this.submitTipoMovimiento();
        if(event.detail.formId === 'formEditTipoMovimiento') this.updateTipoMovimiento();
    },
    handleDelete() {
        if (this.isTipoMovimientoDeleteModalOpen) {
            this.deleteTipoMovimiento();
        }
    }
}"
x-init="fetchTipoMovimientos()"
@keydown.escape.window="
    isTipoMovimientoModalOpen = false;
    isTipoMovimientoEditModalOpen = false;
    isTipoMovimientoDeleteModalOpen = false;
"
@modal-submit.window="handleModalSubmit($event)"
@confirm-delete.window="handleDelete()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Catálogo de Tipos de Movimiento</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
                'searchModel' => 'filtroTipoMovimiento',
                'ordenarOptions' => [
                    'nombre' => 'Nombre',
                    'id' => 'ID Tipo'
                ]
            ])
        </x-slot>

        <x-slot name="actions">
            <button
                @click="isTipoMovimientoModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo tipo de movimiento
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
                    <template x-if="loadingTipoMovimientos">
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando tipos de movimiento...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingTipoMovimientos && tipoMovimientos.length === 0">
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500 nunito-regular">
                                No hay tipos de movimiento registrados
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingTipoMovimientos && tipoMovimientos.length > 0">
                        <template x-for="(tipoMovimiento, index) in tipoMovimientos" :key="tipoMovimiento.id_tipo_movimiento_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === tipoMovimientos.length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="tipoMovimiento.nombre_tipo_movimiento"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="tipoMovimiento.descripcion_tipo_movimiento"></td>
                                <td class="py-2 px-4 flex gap-2" :class="{ 'last:rounded-br-lg': index === tipoMovimientos.length - 1 }">
                                    <a href="#" @click.prevent="isTipoMovimientoEditModalOpen = true; itemToEdit = {id_tipo_movimiento_pk: tipoMovimiento.id_tipo_movimiento_pk, nombre_tipo_movimiento: tipoMovimiento.nombre_tipo_movimiento, descripcion_tipo_movimiento: tipoMovimiento.descripcion_tipo_movimiento}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    <a href="#" @click.prevent="isTipoMovimientoDeleteModalOpen = true; itemToDelete = {id_tipo_movimiento_pk: tipoMovimiento.id_tipo_movimiento_pk, nombre: tipoMovimiento.nombre_tipo_movimiento}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loadingTipoMovimientos">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando tipos de movimiento...
                </div>
            </template>
            <template x-if="!loadingTipoMovimientos && tipoMovimientos.length === 0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    No hay tipos de movimiento registrados
                </div>
            </template>
            <template x-if="!loadingTipoMovimientos && tipoMovimientos.length > 0">
                <template x-for="tipoMovimiento in tipoMovimientos" :key="tipoMovimiento.id_tipo_movimiento_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="tipoMovimiento.nombre_tipo_movimiento"></h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="tipoMovimiento.descripcion_tipo_movimiento"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="isTipoMovimientoEditModalOpen = true; itemToEdit = {id_tipo_movimiento_pk: tipoMovimiento.id_tipo_movimiento_pk, nombre_tipo_movimiento: tipoMovimiento.nombre_tipo_movimiento, descripcion_tipo_movimiento: tipoMovimiento.descripcion_tipo_movimiento}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="isTipoMovimientoDeleteModalOpen = true; itemToDelete = {id_tipo_movimiento_pk: tipoMovimiento.id_tipo_movimiento_pk, nombre: tipoMovimiento.nombre_tipo_movimiento}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
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
        <!-- Modal Nuevo Tipo de Movimiento -->
        <x-admin.form-modal class="nunito-bold" modalName="isTipoMovimientoModalOpen" title="Nuevo Tipo de Movimiento"
            submitLabel="Guardar Tipo de Movimiento" formId="formTipoMovimiento" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre_tipo_movimiento" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="nombre_tipo_movimiento" x-model="nombre_tipo_movimiento" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div class="col-span-2">
                    <label for="descripcion_tipo_movimiento"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="descripcion_tipo_movimiento" x-model="descripcion_tipo_movimiento" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Tipo de Movimiento -->
        <x-admin.edit-modal class="nunito-bold" modalName="isTipoMovimientoEditModalOpen" title="Editar Tipo de Movimiento" itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditTipoMovimiento">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_nombre_tipo_movimiento" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="edit_nombre_tipo_movimiento" x-model="itemToEdit.nombre_tipo_movimiento" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div class="col-span-2">
                    <label for="edit_descripcion_tipo_movimiento"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="edit_descripcion_tipo_movimiento" x-model="itemToEdit.descripcion_tipo_movimiento" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                </div>
            </div>
        </x-admin.edit-modal>

        <!-- Modal Confirmar Eliminación -->
        <x-admin.confirmation-modal class="nunito-regular" modalName="isTipoMovimientoDeleteModalOpen" itemToDelete="itemToDelete"
            message="¿Estás seguro de que quieres eliminar este tipo de movimiento?" />
    </div>
</div>