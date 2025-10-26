<div x-data="{
    isEstadoTicketModalOpen: false,
    isEstadoTicketEditModalOpen: false,
    isEstadoTicketDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    estadosTickets: [],
    loadingEstadosTickets: false,

    // 1️⃣ Variables de Paginación
    numbersEstadosTickets: [],
    currentPageEstadosTickets: 1,
    perPageEstadosTickets: 10,

    nombre: '',
    descripcion: '',
    codigo: '',
    es_final: false,
    orden: '',
    filtroEstadoTicket: '',
    ordenarPor: 'nombre',

    // 2️⃣ Métodos de Paginación
    paginatedEstadosTickets() {
        return this.estadosTickets.slice(
            (this.currentPageEstadosTickets - 1) * this.perPageEstadosTickets, 
            this.currentPageEstadosTickets * this.perPageEstadosTickets
        );
    },
    totalPagesEstadosTickets() {
        return Math.ceil(this.estadosTickets.length / this.perPageEstadosTickets);
    },
    nextPageEstadosTickets() {
        if (this.currentPageEstadosTickets < this.totalPagesEstadosTickets()) {
            this.currentPageEstadosTickets++;
        }
    },
    prevPageEstadosTickets() {
        if (this.currentPageEstadosTickets > 1) {
            this.currentPageEstadosTickets--;
        }
    },

    // 3️⃣ Sincronizar Alias en cada operación CRUD
    async fetchEstadosTickets() {
        await window.estadosTicketsApiHandlers.fetchEstadosTickets(this);
        this.numbersEstadosTickets = this.estadosTickets; // ← LÍNEA AGREGADA
    },
    async submitEstadoTicket() {
        await window.estadosTicketsApiHandlers.submitEstadoTicket(this);
        this.fetchEstadosTickets(); // Refrescar datos
    },
    async updateEstadoTicket() {
        await window.estadosTicketsApiHandlers.updateEstadoTicket(this);
        this.fetchEstadosTickets(); // Refrescar datos
    },
    async deleteEstadoTicket() {
        await window.estadosTicketsApiHandlers.deleteEstadoTicket(this);
        this.fetchEstadosTickets(); // Refrescar datos
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
x-effect="
    // 4️⃣ Reset de página en filtros
    $watch('filtroEstadoTicket', () => { fetchEstadosTickets(); currentPageEstadosTickets = 1; });
    $watch('ordenarPor', () => { fetchEstadosTickets(); currentPageEstadosTickets = 1; });
"
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
                'ordenarModel' => 'ordenarPor',
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
                        <!-- 5️⃣ Usar paginatedEstadosTickets() en el template -->
                        <template x-for="(estadoTicket, index) in paginatedEstadosTickets()" :key="estadoTicket.id_estado_ticket_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === paginatedEstadosTickets().length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estadoTicket.codigo"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estadoTicket.nombre"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estadoTicket.descripcion"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estadoTicket.es_final ? 'Sí' : 'No'"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estadoTicket.orden"></td>
                                <td class="py-2 px-4 flex gap-2" :class="{ 'last:rounded-br-lg': index === paginatedEstadosTickets().length - 1 }">
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
                <template x-for="estadoTicket in paginatedEstadosTickets()" :key="estadoTicket.id_estado_ticket_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="estadoTicket.nombre"></h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="estadoTicket.descripcion"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="isEstadoTicketEditModalOpen = true; itemToEdit = {id_estado_ticket_pk: estadoTicket.id_estado_ticket_pk, codigo: estadoTicket.codigo, nombre: estadoTicket.nombre, descripcion: estadoTicket.descripcion, es_final: estadoTicket.es_final, orden: estadoTicket.orden}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="isEstadoTicketDeleteModalOpen = true; itemToDelete = {id_estado_ticket_pk: estadoTicket.id_estado_ticket_pk, nombre: estadoTicket.nombre}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <!-- 6️⃣ Componente de Paginación -->
    <div x-show="estadosTickets.length > perPageEstadosTickets" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
        <!-- Mostrando (centered, supports light/dark) -->
        <div class="mb-2">
            <span class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                Mostrando
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="(currentPageEstadosTickets - 1) * perPageEstadosTickets + 1"></strong>
                a
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="Math.min(currentPageEstadosTickets * perPageEstadosTickets, estadosTickets.length)"></strong>
                de
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="estadosTickets.length"></strong>
                resultados
            </span>
        </div>

        <!-- Controls (light/dark) -->
        <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
            <button @click="prevPageEstadosTickets()" :disabled="currentPageEstadosTickets === 1"

class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                <span>Anterior</span>
            </button>

            <div class="flex items-center gap-1">
                <template x-for="page in Array.from({length: totalPagesEstadosTickets()}, (_, i) => i + 1).slice(Math.max(0, currentPageEstadosTickets - 3), currentPageEstadosTickets + 2)" :key="page">
                    <button @click="currentPageEstadosTickets = page"
                            class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                            :class="page === currentPageEstadosTickets ? 'bg-blue-600 text-white' : ''">
                        <span x-text="page"></span>
                    </button>
                </template>
            </div>

            <button @click="nextPageEstadosTickets()" :disabled="currentPageEstadosTickets === totalPagesEstadosTickets()"
                    class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                <span>Siguiente</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
    </div>

    <!-- Modales -->
    <div>
        <!-- Modal Nuevo Estado de Ticket -->
        <x-admin.form-modal class="nunito-bold" modalName="isEstadoTicketModalOpen" title="Nuevo Estado de Ticket"
            submitLabel="Guardar Estado de Ticket" formId="formEstadoTicket" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="codigo" class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                    <input type="text" id="codigo" x-model="codigo" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="nombre" x-model="nombre" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div class="col-span-2">
                    <label for="descripcion"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="descripcion" x-model="descripcion" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"></textarea>
                </div>
                <div>
                    <label for="es_final" class="block text-sm font-medium text-gray-700 nunito-bold">Es Final</label>
                    <input type="checkbox" id="es_final" x-model="es_final"
                        class="mt-1 block rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>

<label for="orden" class="block text-sm font-medium text-gray-700 nunito-bold">Orden</label>
                    <input type="number" id="orden" x-model="orden" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Estado de Ticket -->
        <x-admin.edit-modal class="nunito-bold" modalName="isEstadoTicketEditModalOpen" title="Editar Estado de Ticket" itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditEstadoTicket">
            <template x-if="itemToEdit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_codigo" class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                    <input type="text" id="edit_codigo" x-model="itemToEdit.codigo" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>
                    <label for="edit_nombre" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="edit_nombre" x-model="itemToEdit.nombre" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div class="col-span-2">
                    <label for="edit_descripcion"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="edit_descripcion" x-model="itemToEdit.descripcion" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"></textarea>
                </div>
                <div>
                    <label for="edit_es_final" class="block text-sm font-medium text-gray-700 nunito-bold">Es Final</label>
                    <input type="checkbox" id="edit_es_final" x-model="itemToEdit.es_final"
                        class="mt-1 block rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>
                    <label for="edit_orden" class="block text-sm font-medium text-gray-700 nunito-bold">Orden</label>
                    <input type="number" id="edit_orden" x-model="itemToEdit.orden" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
            </div>
            </template>
        </x-admin.edit-modal>

        <!-- Modal Confirmar Eliminación -->
        <x-admin.confirmation-modal class="nunito-regular" modalName="isEstadoTicketDeleteModalOpen" itemToDelete="itemToDelete"
            message="¿Estás seguro de que quieres eliminar este estado de ticket?" />
    </div>
</div>