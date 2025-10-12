<div x-data="{
    isEstadoTicketModalOpen: false,
    isEstadoTicketEditModalOpen: false,
    isEstadoTicketDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    estadosTickets: [],
    loadingEstadosTickets: false,
    nombre: '',
    descripcion: '',
    codigo: '',
    es_final: false,
    orden: '',
    filtroEstadoTicket: '',
    ordenarPor: '',
    async fetchEstadosTickets() {
        await window.estadosTicketsApiHandlers.fetchEstadosTickets(this);
    },
    async submitEstadoTicket() {
        await window.estadosTicketsApiHandlers.submitEstadoTicket(this);
    },
    async updateEstadoTicket() {
        await window.estadosTicketsApiHandlers.updateEstadoTicket(this);
    },
    async deleteEstadoTicket() {
        await window.estadosTicketsApiHandlers.deleteEstadoTicket(this);
    },
    handleModalSubmit(event) {
        if(event.detail.formId === 'formEstadoTicket') this.submitEstadoTicket();
        if(event.detail.formId === 'formEditEstadoTicket') this.updateEstadoTicket();
    },
    handleDelete() {
        if (this.isEstadoTicketDeleteModalOpen) {
            this.deleteEstadoTicket();
        }
    }
}"
x-init="fetchEstadosTickets()"
@keydown.escape.window="
    isEstadoTicketModalOpen = false;
    isEstadoTicketEditModalOpen = false;
    isEstadoTicketDeleteModalOpen = false;
"
@modal-submit.window="handleModalSubmit($event)"
@confirm-delete.window="handleDelete()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Catálogo de Estados de Tickets</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
                'searchModel' => 'filtroEstadoTicket',
                'ordenarOptions' => [
                    'nombre' => 'Nombre',
                    'id' => 'ID Estado'
                ]
            ])
        </x-slot>

        <x-slot name="actions">
            <button
                @click="isEstadoTicketModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo estado de ticket
            </button>
        </x-slot>

        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Código</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Nombre</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Descripción</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Es Final</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Orden</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loadingEstadosTickets">
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando estados de tickets...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingEstadosTickets && estadosTickets.length === 0">
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 nunito-regular">
                                No hay estados de tickets registrados
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingEstadosTickets && estadosTickets.length > 0">
                        <template x-for="(estadoTicket, index) in estadosTickets" :key="estadoTicket.id_estado_ticket_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === estadosTickets.length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estadoTicket.codigo"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estadoTicket.nombre"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estadoTicket.descripcion"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estadoTicket.es_final ? 'Sí' : 'No'"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estadoTicket.orden"></td>
                                <td class="py-2 px-4 flex gap-2" :class="{ 'last:rounded-br-lg': index === estadosTickets.length - 1 }">
                                    <a href="#" @click.prevent="isEstadoTicketEditModalOpen = true; itemToEdit = {id_estado_ticket_pk: estadoTicket.id_estado_ticket_pk, codigo: estadoTicket.codigo, nombre: estadoTicket.nombre, descripcion: estadoTicket.descripcion, es_final: estadoTicket.es_final, orden: estadoTicket.orden}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    <a href="#" @click.prevent="isEstadoTicketDeleteModalOpen = true; itemToDelete = {id_estado_ticket_pk: estadoTicket.id_estado_ticket_pk, nombre: estadoTicket.nombre}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loadingEstadosTickets">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando estados de tickets...
                </div>
            </template>
            <template x-if="!loadingEstadosTickets && estadosTickets.length === 0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    No hay estados de tickets registrados
                </div>
            </template>
            <template x-if="!loadingEstadosTickets && estadosTickets.length > 0">
                <template x-for="estadoTicket in estadosTickets" :key="estadoTicket.id_estado_ticket_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="estadoTicket.nombre"></h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="estadoTicket.descripcion"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="isEstadoTicketEditModalOpen = true; itemToEdit = {id_estado_ticket_pk: estadoTicket.id_estado_ticket_pk, codigo: estadoTicket.codigo, nombre: estadoTicket.nombre, descripcion: estadoTicket.descripcion, es_final: estadoTicket.es_final, orden: estadoTicket.orden}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="isEstadoTicketDeleteModalOpen = true; itemToDelete = {id_estado_ticket_pk: estadoTicket.id_estado_ticket_pk, nombre: estadoTicket.nombre" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
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
        <!-- Modal Nuevo Estado de Ticket -->
        <x-admin.form-modal class="nunito-bold" modalName="isEstadoTicketModalOpen" title="Nuevo Estado de Ticket"
            submitLabel="Guardar Estado de Ticket" formId="formEstadoTicket" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="codigo" class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                    <input type="text" id="codigo" x-model="codigo" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="nombre" x-model="nombre" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div class="col-span-2">
                    <label for="descripcion"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="descripcion" x-model="descripcion" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                </div>
                <div>
                    <label for="es_final" class="block text-sm font-medium text-gray-700 nunito-bold">Es Final</label>
                    <input type="checkbox" id="es_final" x-model="es_final"
                        class="mt-1 block rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div>
                    <label for="orden" class="block text-sm font-medium text-gray-700 nunito-bold">Orden</label>
                    <input type="number" id="orden" x-model="orden" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Estado de Ticket -->
        <x-admin.edit-modal class="nunito-bold" modalName="isEstadoTicketEditModalOpen" title="Editar Estado de Ticket" itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditEstadoTicket">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_codigo" class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                    <input type="text" id="edit_codigo" x-model="itemToEdit.codigo" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div>
                    <label for="edit_nombre" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="edit_nombre" x-model="itemToEdit.nombre" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div class="col-span-2">
                    <label for="edit_descripcion"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="edit_descripcion" x-model="itemToEdit.descripcion" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                </div>
                <div>
                    <label for="edit_es_final" class="block text-sm font-medium text-gray-700 nunito-bold">Es Final</label>
                    <input type="checkbox" id="edit_es_final" x-model="itemToEdit.es_final"
                        class="mt-1 block rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div>
                    <label for="edit_orden" class="block text-sm font-medium text-gray-700 nunito-bold">Orden</label>
                    <input type="number" id="edit_orden" x-model="itemToEdit.orden" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
            </div>
        </x-admin.edit-modal>

        <!-- Modal Confirmar Eliminación -->
        <x-admin.confirmation-modal class="nunito-regular" modalName="isEstadoTicketDeleteModalOpen" itemToDelete="itemToDelete"
            message="¿Estás seguro de que quieres eliminar este estado de ticket?" />
    </div>
</div>
