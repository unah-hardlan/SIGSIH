<div x-data="{
    isEstadoTicketModalOpen: false,
    isEstadoTicketEditModalOpen: false,
    isEstadoTicketDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    estadosTickets: [],
    loadingEstadosTickets: false,

    numbersEstadosTickets: [],
    currentPageEstadosTickets: 1,
    perPageEstadosTickets: 10,

    nombre: '',
    descripcion: '',
    codigo: '',
    es_final: false,
    orden: '',
    formEstadoTicket: { _touched: {} },
    formEditEstadoTicket: { _touched: {} },
    filtroEstadoTicket: '',
    ordenarPor: 'nombre',

    paginatedEstadosTickets() {
        return this.estadosTickets.slice(
            (this.currentPageEstadosTickets - 1) * this.perPageEstadosTickets, 
            this.currentPageEstadosTickets * this.perPageEstadosTickets
        );
    },
    totalPagesEstadosTickets() {
        return Math.max(1, Math.ceil((this.estadosTickets || []).length / this.perPageEstadosTickets));
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

    async fetchEstadosTickets() {
        await window.estadosTicketsApiHandlers.fetchEstadosTickets(this);
        this.numbersEstadosTickets = this.estadosTickets;
    },
    async submitEstadoTicket() {
        await window.estadosTicketsApiHandlers.submitEstadoTicket(this);
        this.fetchEstadosTickets(); 
    },
    async updateEstadoTicket() {
        await window.estadosTicketsApiHandlers.updateEstadoTicket(this);
        this.fetchEstadosTickets(); 
    },
    async deleteEstadoTicket() {
        await window.estadosTicketsApiHandlers.deleteEstadoTicket(this);
        this.fetchEstadosTickets(); 
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
            @perm(['Catálogo','Estados de Tickets','Estado de Ticket'], 'insercion')
            <button
                @click="formEstadoTicket = { _touched: {} }; codigo=''; nombre=''; descripcion=''; es_final=false; orden=''; isEstadoTicketModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo estado de ticket
            </button>
            @else
            <button disabled title="No tiene permiso para crear Estados de Tickets"
                class="bg-green-600 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm opacity-50 cursor-not-allowed">
                Nuevo estado de ticket
            </button>
            @endperm
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
                        <template x-for="(estadoTicket, index) in paginatedEstadosTickets()" :key="estadoTicket.id_estado_ticket_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === paginatedEstadosTickets().length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estadoTicket.codigo"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estadoTicket.nombre"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estadoTicket.descripcion"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estadoTicket.es_final ? 'Sí' : 'No'"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estadoTicket.orden"></td>
                                <td class="py-2 px-4 flex gap-2" :class="{ 'last:rounded-br-lg': index === paginatedEstadosTickets().length - 1 }">
                                    @perm(['Catálogo','Estados de Tickets','Estado de Ticket'], 'actualizacion')
                                    <a href="#" @click.prevent="formEditEstadoTicket = { _touched: {} }; isEstadoTicketEditModalOpen = true; itemToEdit = {id_estado_ticket_pk: estadoTicket.id_estado_ticket_pk, codigo: estadoTicket.codigo, nombre: estadoTicket.nombre, descripcion: estadoTicket.descripcion, es_final: estadoTicket.es_final, orden: estadoTicket.orden}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    @else
                                    <span class="text-blue-300 cursor-not-allowed" title="No tiene permiso para editar Estados de Tickets"><i class="fas fa-edit"></i></span>
                                    @endperm

                                    @perm(['Catálogo','Estados de Tickets','Estado de Ticket'], 'eliminacion')
                                    <a href="#" @click.prevent="isEstadoTicketDeleteModalOpen = true; itemToDelete = {id_estado_ticket_pk: estadoTicket.id_estado_ticket_pk, nombre: estadoTicket.nombre}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                    @else
                                    <span class="text-red-300 cursor-not-allowed" title="No tiene permiso para eliminar Estados de Tickets"><i class="fas fa-trash"></i></span>
                                    @endperm
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
                            @perm(['Catálogo','Estados de Tickets','Estado de Ticket'], 'actualizacion')
                            <button @click.prevent="formEditEstadoTicket = { _touched: {} }; isEstadoTicketEditModalOpen = true; itemToEdit = {id_estado_ticket_pk: estadoTicket.id_estado_ticket_pk, codigo: estadoTicket.codigo, nombre: estadoTicket.nombre, descripcion: estadoTicket.descripcion, es_final: estadoTicket.es_final, orden: estadoTicket.orden}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @else
                            <button disabled title="No tiene permiso para editar Estados de Tickets" class="px-3 py-1 text-xs bg-blue-600 text-white rounded opacity-50 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @endperm
                            @perm(['Catálogo','Estados de Tickets','Estado de Ticket'], 'eliminacion')
                            <button @click.prevent="isEstadoTicketDeleteModalOpen = true; itemToDelete = {id_estado_ticket_pk: estadoTicket.id_estado_ticket_pk, nombre: estadoTicket.nombre}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @else
                            <button disabled title="No tiene permiso para eliminar Estados de Tickets" class="px-3 py-1 text-xs bg-red-600 text-white rounded opacity-50 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @endperm
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <div x-show="estadosTickets.length > perPageEstadosTickets" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
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

        <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
            <button @click="prevPageEstadosTickets()" :disabled="currentPageEstadosTickets === 1"

                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
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
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <div>
        @perm(['Catálogo','Estados de Tickets','Estado de Ticket'], 'insercion')
        <x-admin.form-modal class="nunito-bold" modalName="isEstadoTicketModalOpen" title="Nuevo Estado de Ticket"
            submitLabel="Guardar Estado de Ticket" formId="formEstadoTicket" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="codigo" class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                    <input type="text" id="codigo" x-model="codigo" maxlength="10" required @input="formEstadoTicket._touched.codigo = true" @blur="formEstadoTicket._touched.codigo = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEstadoTicket._touched && formEstadoTicket._touched.codigo && (codigo === '' || (codigo && codigo.length >= 10)) ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500" :class="formEstadoTicket._touched && formEstadoTicket._touched.codigo && (codigo === '' || (codigo && codigo.length >= 10)) ? 'text-red-500' : ''">Requerido. Máximo 10 caracteres.</small>
                </div>
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="nombre" x-model="nombre" maxlength="150" required @input="formEstadoTicket._touched.nombre = true" @blur="formEstadoTicket._touched.nombre = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEstadoTicket._touched && formEstadoTicket._touched.nombre && !nombre ? 'border-red-500' : (formEstadoTicket._touched && formEstadoTicket._touched.nombre && (nombre && nombre.length >= 150) ? 'border-red-500' : '')">
                    <small class="block mt-1 text-sm text-gray-500" :class="formEstadoTicket._touched && formEstadoTicket._touched.nombre && !nombre ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                </div>
                <div class="col-span-2">
                    <label for="descripcion"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="descripcion" x-model="descripcion" maxlength="255" rows="2" @input="formEstadoTicket._touched.descripcion = true" @blur="formEstadoTicket._touched.descripcion = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEstadoTicket._touched && formEstadoTicket._touched.descripcion && (descripcion === '' || (descripcion && descripcion.length >= 255)) ? 'border-red-500' : ''"></textarea>
                    <small class="block mt-1 text-sm text-gray-500" :class="formEstadoTicket._touched && formEstadoTicket._touched.descripcion && (descripcion === '' || (descripcion && descripcion.length >= 255)) ? 'text-red-500' : ''">Máximo 255 caracteres.</small>
                </div>
                <div>
                    <label for="es_final" class="block text-sm font-medium text-gray-700 nunito-bold">Es Final</label>
                    <input type="checkbox" id="es_final" x-model="es_final"
                        class="mt-1 block rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>

                    <label for="orden" class="block text-sm font-medium text-gray-700 nunito-bold">Orden</label>
                    <input type="number" id="orden" x-model="orden" required min="0" @input="formEstadoTicket._touched.orden = true" @blur="formEstadoTicket._touched.orden = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEstadoTicket._touched && formEstadoTicket._touched.orden && (orden === '' || orden < 0) ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500" :class="formEstadoTicket._touched && formEstadoTicket._touched.orden && (orden === '' || orden < 0) ? 'text-red-500' : ''">Requerido. Valor >= 0.</small>
                </div>
            </div>
        </x-admin.form-modal>
        @endperm

        @perm(['Catálogo','Estados de Tickets','Estado de Ticket'], 'actualizacion')
        <x-admin.edit-modal class="nunito-bold" modalName="isEstadoTicketEditModalOpen" title="Editar Estado de Ticket" itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditEstadoTicket">
            <template x-if="itemToEdit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit_codigo" class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                        <input type="text" id="edit_codigo" x-model="itemToEdit.codigo" maxlength="10" required @input="formEditEstadoTicket._touched.codigo = true" @blur="formEditEstadoTicket._touched.codigo = true"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                            :class="formEditEstadoTicket._touched && formEditEstadoTicket._touched.codigo && (itemToEdit.codigo === '' || (itemToEdit.codigo && itemToEdit.codigo.length >= 10)) ? 'border-red-500' : ''">
                        <small class="block mt-1 text-sm text-gray-500" :class="formEditEstadoTicket._touched && formEditEstadoTicket._touched.codigo && (itemToEdit.codigo === '' || (itemToEdit.codigo && itemToEdit.codigo.length >= 10)) ? 'text-red-500' : ''">Requerido. Máximo 10 caracteres.</small>
                    </div>
                    <div>
                        <label for="edit_nombre" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                        <input type="text" id="edit_nombre" x-model="itemToEdit.nombre" maxlength="150" required @input="formEditEstadoTicket._touched.nombre = true" @blur="formEditEstadoTicket._touched.nombre = true"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                            :class="formEditEstadoTicket._touched && formEditEstadoTicket._touched.nombre && !itemToEdit.nombre ? 'border-red-500' : (formEditEstadoTicket._touched && formEditEstadoTicket._touched.nombre && (itemToEdit.nombre && itemToEdit.nombre.length >= 150) ? 'border-red-500' : '')">
                        <small class="block mt-1 text-sm text-gray-500" :class="formEditEstadoTicket._touched && formEditEstadoTicket._touched.nombre && !itemToEdit.nombre ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                    </div>
                    <div class="col-span-2">
                        <label for="edit_descripcion"
                            class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                        <textarea id="edit_descripcion" x-model="itemToEdit.descripcion" maxlength="255" rows="2" @input="formEditEstadoTicket._touched.descripcion = true" @blur="formEditEstadoTicket._touched.descripcion = true"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                            :class="formEditEstadoTicket._touched && formEditEstadoTicket._touched.descripcion && (itemToEdit.descripcion === '' || (itemToEdit.descripcion && itemToEdit.descripcion.length >= 255)) ? 'border-red-500' : ''"></textarea>
                        <small class="block mt-1 text-sm text-gray-500" :class="formEditEstadoTicket._touched && formEditEstadoTicket._touched.descripcion && (itemToEdit.descripcion === '' || (itemToEdit.descripcion && itemToEdit.descripcion.length >= 255)) ? 'text-red-500' : ''">Máximo 255 caracteres.</small>
                    </div>
                    <div>
                        <label for="edit_es_final" class="block text-sm font-medium text-gray-700 nunito-bold">Es Final</label>
                        <input type="checkbox" id="edit_es_final" x-model="itemToEdit.es_final"
                            class="mt-1 block rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    </div>
                    <div>
                        <label for="edit_orden" class="block text-sm font-medium text-gray-700 nunito-bold">Orden</label>
                        <input type="number" id="edit_orden" x-model="itemToEdit.orden" required min="0" @input="formEditEstadoTicket._touched.orden = true" @blur="formEditEstadoTicket._touched.orden = true"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                            :class="formEditEstadoTicket._touched && formEditEstadoTicket._touched.orden && (itemToEdit.orden === '' || itemToEdit.orden < 0) ? 'border-red-500' : ''">
                        <small class="block mt-1 text-sm text-gray-500" :class="formEditEstadoTicket._touched && formEditEstadoTicket._touched.orden && (itemToEdit.orden === '' || itemToEdit.orden < 0) ? 'text-red-500' : ''">Requerido. Valor >= 0.</small>
                    </div>
                </div>
            </template>
        </x-admin.edit-modal>
        @endperm

        @perm(['Catálogo','Estados de Tickets','Estado de Ticket'], 'eliminacion')
        <x-admin.confirmation-modal class="nunito-regular" modalName="isEstadoTicketDeleteModalOpen" itemToDelete="itemToDelete"
            message="¿Estás seguro de que quieres eliminar este estado de ticket?" />
        @endperm
    </div>
</div>