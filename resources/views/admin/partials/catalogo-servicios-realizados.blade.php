<div x-data="{
    isServicioRealizadoModalOpen: false,
    isServicioRealizadoEditModalOpen: false,
    isServicioRealizadoDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    serviciosRealizados: [],
    loadingServiciosRealizados: false,
    nombre_servicio: '',
    descripcion_servicio: '',
    async fetchServiciosRealizados() {
        await window.serviciosRealizadosApiHandlers.fetchServiciosRealizados(this);
    },
    async submitServicioRealizado() {
        await window.serviciosRealizadosApiHandlers.submitServicioRealizado(this);
    },
    async updateServicioRealizado() {
        await window.serviciosRealizadosApiHandlers.updateServicioRealizado(this);
    },
    async deleteServicioRealizado() {
        await window.serviciosRealizadosApiHandlers.deleteServicioRealizado(this);
    },
    handleModalSubmit(event) {
        if(event.detail.formId === 'formServicioRealizado') this.submitServicioRealizado();
        if(event.detail.formId === 'formEditServicioRealizado') this.updateServicioRealizado();
    },
    handleDelete() {
        if (this.isServicioRealizadoDeleteModalOpen) {
            this.deleteServicioRealizado();
        }
    }
}"
x-init="fetchServiciosRealizados()"
@keydown.escape.window="
    isServicioRealizadoModalOpen = false;
    isServicioRealizadoEditModalOpen = false;
    isServicioRealizadoDeleteModalOpen = false;
"
@modal-submit.window="handleModalSubmit($event)"
@confirm-delete.window="handleDelete()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Catálogo de Servicios Realizados</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
                'searchModel' => 'filtroServicioRealizado',
                'ordenarOptions' => [
                    'tipo' => 'Tipo de Servicio',
                    'fecha' => 'Fecha'
                ]
            ])
        </x-slot>

        <x-slot name="actions">
            <button
                @click="isServicioRealizadoModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo servicio realizado
            </button>
        </x-slot>

        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Nombre del Servicio</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Descripción</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loadingServiciosRealizados">
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando servicios realizados...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingServiciosRealizados && serviciosRealizados.length === 0">
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500 nunito-regular">
                                No hay servicios realizados registrados
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingServiciosRealizados && serviciosRealizados.length > 0">
                        <template x-for="(servicioRealizado, index) in serviciosRealizados" :key="servicioRealizado.id_servicio_realizado_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === serviciosRealizados.length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="servicioRealizado.nombre_servicio"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="servicioRealizado.descripcion_servicio"></td>
                                <td class="py-2 px-4 flex gap-2" :class="{ 'last:rounded-br-lg': index === serviciosRealizados.length - 1 }">
                                    <a href="#" @click.prevent="isServicioRealizadoEditModalOpen = true; itemToEdit = {id_servicio_realizado_pk: servicioRealizado.id_servicio_realizado_pk, nombre_servicio: servicioRealizado.nombre_servicio, descripcion_servicio: servicioRealizado.descripcion_servicio}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    <a href="#" @click.prevent="isServicioRealizadoDeleteModalOpen = true; itemToDelete = {id_servicio_realizado_pk: servicioRealizado.id_servicio_realizado_pk, nombre_servicio: servicioRealizado.nombre_servicio}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loadingServiciosRealizados">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando servicios realizados...
                </div>
            </template>
            <template x-if="!loadingServiciosRealizados && serviciosRealizados.length === 0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    No hay servicios realizados registrados
                </div>
            </template>
            <template x-if="!loadingServiciosRealizados && serviciosRealizados.length > 0">
                <template x-for="servicioRealizado in serviciosRealizados" :key="servicioRealizado.id_servicio_realizado_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="servicioRealizado.nombre_servicio"></h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="servicioRealizado.descripcion_servicio"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="isServicioRealizadoEditModalOpen = true; itemToEdit = {id_servicio_realizado_pk: servicioRealizado.id_servicio_realizado_pk, nombre_servicio: servicioRealizado.nombre_servicio, descripcion_servicio: servicioRealizado.descripcion_servicio}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="isServicioRealizadoDeleteModalOpen = true; itemToDelete = {id_servicio_realizado_pk: servicioRealizado.id_servicio_realizado_pk, nombre_servicio: servicioRealizado.nombre_servicio}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
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
        <!-- Modal Nuevo Servicio Realizado -->
        <x-admin.form-modal class="nunito-bold" modalName="isServicioRealizadoModalOpen" title="Nuevo Servicio Realizado"
            submitLabel="Guardar Servicio Realizado" formId="formServicioRealizado" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre del Servicio</label>
                    <input type="text" id="nombre_servicio" x-model="nombre_servicio" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div class="col-span-2">
                    <label for="descripcion_servicio"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="descripcion_servicio" x-model="descripcion_servicio" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Servicio Realizado -->
        <x-admin.edit-modal class="nunito-bold" modalName="isServicioRealizadoEditModalOpen" title="Editar Servicio Realizado" itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditServicioRealizado">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_nombre_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre del Servicio</label>
                    <input type="text" id="edit_nombre_servicio" x-model="itemToEdit.nombre_servicio" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div class="col-span-2">
                    <label for="edit_descripcion_servicio"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="edit_descripcion_servicio" x-model="itemToEdit.descripcion_servicio" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                </div>
            </div>
        </x-admin.edit-modal>

        <!-- Modal Confirmar Eliminación -->
        <x-admin.confirmation-modal class="nunito-regular" modalName="isServicioRealizadoDeleteModalOpen" itemToDelete="itemToDelete"
            message="¿Estás seguro de que quieres eliminar este servicio realizado?" />
    </div>
</div>