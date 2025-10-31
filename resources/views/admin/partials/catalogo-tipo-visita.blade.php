<div x-data="{
    isTipoVisitaModalOpen: false,
    isTipoVisitaEditModalOpen: false,
    isTipoVisitaDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    tipoVisitas: [],
    loadingTipoVisitas: false,

    // 1️⃣ Variables de Paginación
    numbersTipoVisitas: [],
    currentPageTipoVisitas: 1,
    perPageTipoVisitas: 10,

    nombre_tipo_visita: '',
    descripcion_tipo_visita: '',
    edit_nombre_tipo_visita: '',
    edit_descripcion_tipo_visita: '',
    formTipoVisita: { _touched: {} },
    formEditTipoVisita: { _touched: {} },
    filtroTipoVisita: '',
    ordenarPor: 'nombre',

    // 2️⃣ Métodos de Paginación
    paginatedTipoVisitas() {
        return this.tipoVisitas.slice(
            (this.currentPageTipoVisitas - 1) * this.perPageTipoVisitas, 
            this.currentPageTipoVisitas * this.perPageTipoVisitas
        );
    },
    totalPagesTipoVisitas() {
        return Math.ceil(this.tipoVisitas.length / this.perPageTipoVisitas);
    },
    nextPageTipoVisitas() {
        if (this.currentPageTipoVisitas < this.totalPagesTipoVisitas()) {
            this.currentPageTipoVisitas++;
        }
    },
    prevPageTipoVisitas() {
        if (this.currentPageTipoVisitas > 1) {
            this.currentPageTipoVisitas--;
        }
    },

    // 3️⃣ Sincronizar Alias en cada operación CRUD
    async fetchTipoVisitas() {
        await window.tipoVisitasApiHandlers.fetchTipoVisitas(this);
        this.numbersTipoVisitas = this.tipoVisitas; // ← LÍNEA AGREGADA
    },
    async submitTipoVisita() {
        await window.tipoVisitasApiHandlers.submitTipoVisita(this);
        this.fetchTipoVisitas(); // Refrescar datos
    },
    async updateTipoVisita() {
        await window.tipoVisitasApiHandlers.updateTipoVisita(this);
        this.fetchTipoVisitas(); // Refrescar datos
    },
    async deleteTipoVisita() {
        await window.tipoVisitasApiHandlers.deleteTipoVisita(this);
        this.fetchTipoVisitas(); // Refrescar datos
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
x-effect="
    // 4️⃣ Reset de página en filtros
    $watch('filtroTipoVisita', () => { fetchTipoVisitas(); currentPageTipoVisitas = 1; });
    $watch('ordenarPor', () => { fetchTipoVisitas(); currentPageTipoVisitas = 1; });
"
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
                'ordenarModel' => 'ordenarPor',
                'ordenarOptions' => [
                    'nombre' => 'Nombre',
                    'id' => 'ID Tipo'
                ]
            ])
        </x-slot>

        <x-slot name="actions">
            <button
                @click="formTipoVisita = { _touched: {} }; nombre_tipo_visita = ''; descripcion_tipo_visita = ''; isTipoVisitaModalOpen = true"
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
                        <!-- 5️⃣ Usar paginatedTipoVisitas() en el template -->
                        <template x-for="(tipoVisita, index) in paginatedTipoVisitas()" :key="tipoVisita.id_tipo_visita_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === paginatedTipoVisitas().length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="tipoVisita.nombre_tipo_visita"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="tipoVisita.descripcion_tipo_visita"></td>
                                <td class="py-2 px-4 flex gap-2" :class="{ 'last:rounded-br-lg': index === paginatedTipoVisitas().length - 1 }">
                                    <a href="#" @click.prevent="itemToEdit = {id_tipo_visita_pk: tipoVisita.id_tipo_visita_pk, nombre_tipo_visita: tipoVisita.nombre_tipo_visita, descripcion_tipo_visita: tipoVisita.descripcion_tipo_visita}; edit_nombre_tipo_visita = tipoVisita.nombre_tipo_visita; edit_descripcion_tipo_visita = tipoVisita.descripcion_tipo_visita; formEditTipoVisita = { _touched: {} }; isTipoVisitaEditModalOpen = true" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
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
                <template x-for="tipoVisita in paginatedTipoVisitas()" :key="tipoVisita.id_tipo_visita_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="tipoVisita.nombre_tipo_visita"></h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="tipoVisita.descripcion_tipo_visita"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="itemToEdit = {id_tipo_visita_pk: tipoVisita.id_tipo_visita_pk, nombre_tipo_visita: tipoVisita.nombre_tipo_visita, descripcion_tipo_visita: tipoVisita.descripcion_tipo_visita}; edit_nombre_tipo_visita = tipoVisita.nombre_tipo_visita; edit_descripcion_tipo_visita = tipoVisita.descripcion_tipo_visita; formEditTipoVisita = { _touched: {} }; isTipoVisitaEditModalOpen = true" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
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

    <!-- 6️⃣ Componente de Paginación -->
    <div x-show="tipoVisitas.length > perPageTipoVisitas" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
        <!-- Mostrando (centered, supports light/dark) -->
        <div class="mb-2">
            <span class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                Mostrando
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="(currentPageTipoVisitas - 1) * perPageTipoVisitas + 1"></strong>
                a
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="Math.min(currentPageTipoVisitas * perPageTipoVisitas, tipoVisitas.length)"></strong>
                de
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="tipoVisitas.length"></strong>
                resultados
            </span>
        </div>

        <!-- Controls (light/dark) -->
        <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
            <button @click="prevPageTipoVisitas()" :disabled="currentPageTipoVisitas === 1"
                    class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                <span>Anterior</span>
            </button>

<div class="flex items-center gap-1">
                <template x-for="page in Array.from({length: totalPagesTipoVisitas()}, (_, i) => i + 1).slice(Math.max(0, currentPageTipoVisitas - 3), currentPageTipoVisitas + 2)" :key="page">
                    <button @click="currentPageTipoVisitas = page"
                            class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                            :class="page === currentPageTipoVisitas ? 'bg-blue-600 text-white' : ''">
                        <span x-text="page"></span>
                    </button>
                </template>
            </div>

            <button @click="nextPageTipoVisitas()" :disabled="currentPageTipoVisitas === totalPagesTipoVisitas()"
                    class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                <span>Siguiente</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
    </div>


    <!-- Modales -->
    <div>
        <!-- Modal Nuevo Tipo de Visita -->
        <x-admin.form-modal class="nunito-bold" modalName="isTipoVisitaModalOpen" title="Nuevo Tipo de Visita"
            submitLabel="Guardar Tipo de Visita" formId="formTipoVisita" maxWidth="max-w-2xl">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre_tipo_visita" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="nombre_tipo_visita" x-model="nombre_tipo_visita" required maxlength="150"
                        @input="formTipoVisita = formTipoVisita || { _touched: {} }; formTipoVisita._touched.nombre_tipo_visita = true"
                        @blur="formTipoVisita._touched.nombre_tipo_visita = true"
                        :class="formTipoVisita && formTipoVisita._touched && formTipoVisita._touched.nombre_tipo_visita && (nombre_tipo_visita === '' || nombre_tipo_visita.length > 150) ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <small :class="formTipoVisita && formTipoVisita._touched && formTipoVisita._touched.nombre_tipo_visita && (nombre_tipo_visita === '' || nombre_tipo_visita.length > 150) ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                </div>
                <div class="col-span-2">
                    <label for="descripcion_tipo_visita"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="descripcion_tipo_visita" x-model="descripcion_tipo_visita" rows="2" maxlength="255"
                        @input="formTipoVisita = formTipoVisita || { _touched: {} }; formTipoVisita._touched.descripcion_tipo_visita = true"
                        @blur="formTipoVisita._touched.descripcion_tipo_visita = true"
                        :class="formTipoVisita && formTipoVisita._touched && formTipoVisita._touched.descripcion_tipo_visita && descripcion_tipo_visita.length > 255 ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"></textarea>
                    <small :class="formTipoVisita && formTipoVisita._touched && formTipoVisita._touched.descripcion_tipo_visita && descripcion_tipo_visita.length > 255 ? 'text-red-500' : ''">Máximo 255 caracteres.</small>
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Tipo de Visita -->
        <x-admin.edit-modal class="nunito-bold" modalName="isTipoVisitaEditModalOpen" title="Editar Tipo de Visita" itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditTipoVisita">
            <template x-if="itemToEdit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_nombre_tipo_visita" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="edit_nombre_tipo_visita" x-model="edit_nombre_tipo_visita" required maxlength="150"
                        @input="formEditTipoVisita = formEditTipoVisita || { _touched: {} }; formEditTipoVisita._touched.edit_nombre_tipo_visita = true"
                        @blur="formEditTipoVisita._touched.edit_nombre_tipo_visita = true"
                        :class="formEditTipoVisita && formEditTipoVisita._touched && formEditTipoVisita._touched.edit_nombre_tipo_visita && (edit_nombre_tipo_visita === '' || edit_nombre_tipo_visita.length > 150) ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <small :class="formEditTipoVisita && formEditTipoVisita._touched && formEditTipoVisita._touched.edit_nombre_tipo_visita && (edit_nombre_tipo_visita === '' || edit_nombre_tipo_visita.length > 150) ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                </div>
                <div class="col-span-2">
                    <label for="edit_descripcion_tipo_visita"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="edit_descripcion_tipo_visita" x-model="edit_descripcion_tipo_visita" rows="2" maxlength="255"
                        @input="formEditTipoVisita = formEditTipoVisita || { _touched: {} }; formEditTipoVisita._touched.edit_descripcion_tipo_visita = true"
                        @blur="formEditTipoVisita._touched.edit_descripcion_tipo_visita = true"
                        :class="formEditTipoVisita && formEditTipoVisita._touched && formEditTipoVisita._touched.edit_descripcion_tipo_visita && edit_descripcion_tipo_visita.length > 255 ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"></textarea>
                    <small :class="formEditTipoVisita && formEditTipoVisita._touched && formEditTipoVisita._touched.edit_descripcion_tipo_visita && edit_descripcion_tipo_visita.length > 255 ? 'text-red-500' : ''">Máximo 255 caracteres.</small>
                </div>
            </div>
            </template>
        </x-admin.edit-modal>

<!-- Modal Confirmar Eliminación -->
        <x-admin.confirmation-modal class="nunito-regular" modalName="isTipoVisitaDeleteModalOpen" itemToDelete="itemToDelete"
            message="¿Estás seguro de que quieres eliminar este tipo de visita?" />
    </div>
</div>