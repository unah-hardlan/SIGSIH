<div x-data="{
    isEstadoSolicitudModalOpen: false,
    isEstadoSolicitudEditModalOpen: false,
    isEstadoSolicitudDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    estadosSolicitud: [],
    loadingEstadosSolicitud: false,
    // Campos para el formulario de 'Nuevo'
    codigo: '',
    nombre: '',
    descripcion: '',
    es_final: false,
    orden: '',
    // Filtros
    filtroEstadoSolicitud: '',
    ordenarPor: 'id_estado_solicitud_pk',
    // Funciones que llaman a los handlers de la API
    async fetchEstadosSolicitud() {
        await window.estadosSolicitudApiHandlers.fetchEstadosSolicitud(this);
    },
    async submitEstadoSolicitud() {
        await window.estadosSolicitudApiHandlers.submitEstadoSolicitud(this);
    },
    async updateEstadoSolicitud() {
        await window.estadosSolicitudApiHandlers.updateEstadoSolicitud(this);
    },
    async deleteEstadoSolicitud() {
        await window.estadosSolicitudApiHandlers.deleteEstadoSolicitud(this);
    },
    // Manejadores de eventos de los modales
    handleModalSubmit(event) {
        if (event.detail.formId === 'formEstadoSolicitud') this.submitEstadoSolicitud();
        if (event.detail.formId === 'formEditEstadoSolicitud') this.updateEstadoSolicitud();
    },
    handleDelete() {
        if (this.isEstadoSolicitudDeleteModalOpen) {
            this.deleteEstadoSolicitud();
        }
    }
}"
x-init="fetchEstadosSolicitud()"
@keydown.escape.window="
    isEstadoSolicitudModalOpen = false;
    isEstadoSolicitudEditModalOpen = false;
    isEstadoSolicitudDeleteModalOpen = false;
"
@modal-submit.window="handleModalSubmit($event)"
@confirm-delete.window="handleDelete()">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Catálogo de Estados de Solicitud</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
                'searchModel' => 'filtroEstadoSolicitud',
                'ordenarOptions' => [
                    'nombre' => 'Nombre',
                    'id_estado_solicitud_pk' => 'ID'
                ]
            ])
        </x-slot>

        <x-slot name="actions">
            <button
                @click="isEstadoSolicitudModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo Estado
            </button>
        </x-slot>

        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left border-0 dark:text-gray-300">Nombre</th>
                        <th class="py-2 px-4 text-left border-0 dark:text-gray-300">Código</th>
                        <th class="py-2 px-4 text-left border-0 dark:text-gray-300">Descripción</th>
                        <th class="py-2 px-4 text-left border-0 dark:text-gray-300">Es Final</th>
                        <th class="py-2 px-4 text-left border-0 dark:text-gray-300">Orden</th>
                        <th class="py-2 px-4 text-left border-0 dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loadingEstadosSolicitud">
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando estados...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingEstadosSolicitud && estadosSolicitud.length === 0">
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 nunito-regular">
                                No hay estados de solicitud registrados
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingEstadosSolicitud && estadosSolicitud.length > 0">
                        <template x-for="(estado, index) in estadosSolicitud" :key="estado.id_estado_solicitud_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="estado.nombre"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="estado.codigo"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="estado.descripcion"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200">
                                    <span x-text="estado.es_final ? 'Sí' : 'No'"></span>
                                </td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="estado.orden"></td>
                                <td class="py-2 px-4 flex gap-2">
                                    <a href="#" @click.prevent="isEstadoSolicitudEditModalOpen = true; itemToEdit = JSON.parse(JSON.stringify(estado))" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    <a href="#" @click.prevent="isEstadoSolicitudDeleteModalOpen = true; itemToDelete = { id_estado_solicitud_pk: estado.id_estado_solicitud_pk, nombre: estado.nombre }" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loadingEstadosSolicitud">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando...
                </div>
            </template>
             <template x-if="!loadingEstadosSolicitud && estadosSolicitud.length === 0">
                 <div class="bg-white dark:bg-gray-800 rounded-lg shadow border p-8 text-center text-gray-500 nunito-regular">
                    No hay estados registrados
                </div>
            </template>
            <template x-if="!loadingEstadosSolicitud && estadosSolicitud.length > 0">
                <template x-for="estado in estadosSolicitud" :key="estado.id_estado_solicitud_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border p-4 space-y-2">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="estado.nombre"></h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="'Código: ' + estado.codigo"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="estado.descripcion"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="'Es Final: ' + (estado.es_final ? 'Sí' : 'No')"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="'Orden: ' + estado.orden"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                             <button @click.prevent="isEstadoSolicitudEditModalOpen = true; itemToEdit = JSON.parse(JSON.stringify(estado))" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">Editar</button>
                            <button @click.prevent="isEstadoSolicitudDeleteModalOpen = true; itemToDelete = { id_estado_solicitud_pk: estado.id_estado_solicitud_pk, nombre: estado.nombre }" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">Eliminar</button>
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <div>
        <x-admin.form-modal modalName="isEstadoSolicitudModalOpen" title="Nuevo Estado de Solicitud" submitLabel="Guardar" formId="formEstadoSolicitud" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" id="nombre" x-model="nombre" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2">
                </div>
                <div>
                    <label for="codigo" class="block text-sm font-medium text-gray-700">Código</label>
                    <input type="text" id="codigo" x-model="codigo" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2">
                </div>
                <div class="col-span-2">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea id="descripcion" x-model="descripcion" rows="2" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2"></textarea>
                </div>
                <div>
                    <label for="orden" class="block text-sm font-medium text-gray-700">Orden</label>
                    <input type="number" id="orden" x-model="orden" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="es_final" x-model="es_final" class="rounded border-gray-500 text-blue-600 shadow-sm">
                    <label for="es_final" class="ml-2 block text-sm font-medium text-gray-700">Es Final</label>
                </div>
            </div>
        </x-admin.form-modal>

        <x-admin.edit-modal modalName="isEstadoSolicitudEditModalOpen" title="Editar Estado de Solicitud" itemToEdit="itemToEdit" formId="formEditEstadoSolicitud" maxWidth="max-w-2xl">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" id="edit_nombre" x-model="itemToEdit.nombre" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2">
                </div>
                <div>
                    <label for="edit_codigo" class="block text-sm font-medium text-gray-700">Código</label>
                    <input type="text" id="edit_codigo" x-model="itemToEdit.codigo" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2">
                </div>
                <div class="col-span-2">
                    <label for="edit_descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea id="edit_descripcion" x-model="itemToEdit.descripcion" rows="2" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2"></textarea>
                </div>
                <div>
                    <label for="edit_orden" class="block text-sm font-medium text-gray-700">Orden</label>
                    <input type="number" id="edit_orden" x-model="itemToEdit.orden" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="edit_es_final" x-model="itemToEdit.es_final" class="rounded border-gray-500 text-blue-600 shadow-sm">
                    <label for="edit_es_final" class="ml-2 block text-sm font-medium text-gray-700">Es Final</label>
                </div>
            </div>
        </x-admin.edit-modal>

        <x-admin.confirmation-modal modalName="isEstadoSolicitudDeleteModalOpen" itemToDelete="itemToDelete" message="¿Estás seguro de que quieres eliminar este estado?" />
    </div>
</div>