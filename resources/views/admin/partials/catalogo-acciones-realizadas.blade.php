<div x-data="{
    isAccionRealizadaModalOpen: false,
    isAccionRealizadaEditModalOpen: false,
    isAccionRealizadaDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    accionesRealizadas: [],
    loadingAccionesRealizadas: false,

    //  Variables de Paginación
    numbersAccionesRealizadas: [],
    currentPageAccionesRealizadas: 1,
    perPageAccionesRealizadas: 10,

    nombre: '',
    descripcion: '',
    filtroAccionRealizada: '',
    ordenarPor: 'nombre',

    //  Métodos de Paginación
    paginatedAccionesRealizadas() {
        return this.accionesRealizadas.slice(
            (this.currentPageAccionesRealizadas - 1) * this.perPageAccionesRealizadas, 
            this.currentPageAccionesRealizadas * this.perPageAccionesRealizadas
        );
    },
    totalPagesAccionesRealizadas() {
        return Math.ceil(this.accionesRealizadas.length / this.perPageAccionesRealizadas);
    },
    nextPageAccionesRealizadas() {
        if (this.currentPageAccionesRealizadas < this.totalPagesAccionesRealizadas()) {
            this.currentPageAccionesRealizadas++;
        }
    },
    prevPageAccionesRealizadas() {
        if (this.currentPageAccionesRealizadas > 1) {
            this.currentPageAccionesRealizadas--;
        }
    },

    //  Sincronizar Alias en cada operación CRUD
    async fetchAccionesRealizadas() {
        await window.accionesRealizadasApiHandlers.fetchAccionesRealizadas(this);
        this.numbersAccionesRealizadas = this.accionesRealizadas; // ← LÍNEA AGREGADA
    },
    async submitAccionRealizada() {
        await window.accionesRealizadasApiHandlers.submitAccionRealizada(this);
        this.fetchAccionesRealizadas(); // Refrescar datos
    },
    async updateAccionRealizada() {
        await window.accionesRealizadasApiHandlers.updateAccionRealizada(this);
        this.fetchAccionesRealizadas(); // Refrescar datos
    },
    async deleteAccionRealizada() {
        await window.accionesRealizadasApiHandlers.deleteAccionRealizada(this);
        this.fetchAccionesRealizadas(); // Refrescar datos
    },
    handleModalSubmit(event) {
        if(event.detail.formId === 'formAccionRealizada') this.submitAccionRealizada();
        if(event.detail.formId === 'formEditAccionRealizada') this.updateAccionRealizada();
    },
    handleDelete() {
        if (this.isAccionRealizadaDeleteModalOpen) {
            this.deleteAccionRealizada();
        }
    }
}"
x-init="fetchAccionesRealizadas()"
x-effect="
    // 4️⃣ Reset de página en filtros
    $watch('filtroAccionRealizada', () => { fetchAccionesRealizadas(); currentPageAccionesRealizadas = 1; });
    $watch('ordenarPor', () => { fetchAccionesRealizadas(); currentPageAccionesRealizadas = 1; });
"
@keydown.escape.window="
    isAccionRealizadaModalOpen = false;
    isAccionRealizadaEditModalOpen = false;
    isAccionRealizadaDeleteModalOpen = false;
"
@modal-submit.window="handleModalSubmit($event)"
@confirm-delete.window="handleDelete()">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Catálogo de Acciones Realizadas</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
                'searchModel' => 'filtroAccionRealizada',
                'ordenarModel' => 'ordenarPor',
                'ordenarOptions' => [
                    'nombre' => 'Nombre',
                    'id_accion_realizada_pk' => 'ID'
                ]
            ])
        </x-slot>

        <x-slot name="actions">
            <button
                @click="isAccionRealizadaModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nueva Acción
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
                    <template x-if="loadingAccionesRealizadas">
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando acciones...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingAccionesRealizadas && accionesRealizadas.length === 0">
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500 nunito-regular">
                                No hay acciones registradas
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingAccionesRealizadas && accionesRealizadas.length > 0">
                        <!--  Usar paginatedAccionesRealizadas() en el template -->
                        <template x-for="(accion, index) in paginatedAccionesRealizadas()" :key="accion.id_accion_realizada_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === paginatedAccionesRealizadas().length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="accion.nombre"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="accion.descripcion"></td>
                                <td class="py-2 px-4 flex gap-2" :class="{ 'last:rounded-br-lg': index === paginatedAccionesRealizadas().length - 1 }">
                                    <a href="#" @click.prevent="isAccionRealizadaEditModalOpen = true; itemToEdit = { ...accion }" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    <a href="#" @click.prevent="isAccionRealizadaDeleteModalOpen = true; itemToDelete = { id_accion_realizada_pk: accion.id_accion_realizada_pk, nombre: accion.nombre }" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loadingAccionesRealizadas">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando acciones...
                </div>
            </template>
            <template x-if="!loadingAccionesRealizadas && accionesRealizadas.length === 0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center text-gray-500 nunito-regular">
                    No hay acciones registradas
                </div>
            </template>
            <template x-if="!loadingAccionesRealizadas && accionesRealizadas.length > 0">
                <template x-for="accion in paginatedAccionesRealizadas()" :key="accion.id_accion_realizada_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-2">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="accion.nombre"></h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="accion.descripcion"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="isAccionRealizadaEditModalOpen = true; itemToEdit = { ...accion }" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="isAccionRealizadaDeleteModalOpen = true; itemToDelete = { id_accion_realizada_pk: accion.id_accion_realizada_pk, nombre: accion.nombre }" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <!--  Componente de Paginación -->
    <div x-show="accionesRealizadas.length > perPageAccionesRealizadas" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
        <!-- Mostrando (centered, supports light/dark) -->
        <div class="mb-2">
            <span class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                Mostrando
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="(currentPageAccionesRealizadas - 1) * perPageAccionesRealizadas + 1"></strong>
                a
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="Math.min(currentPageAccionesRealizadas * perPageAccionesRealizadas, accionesRealizadas.length)"></strong>
                de
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="accionesRealizadas.length"></strong>
                resultados
            </span>
        </div>

        <!-- Controls (light/dark) -->
        <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
            <button @click="prevPageAccionesRealizadas()" :disabled="currentPageAccionesRealizadas === 1"
                    class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                <span>Anterior</span>
            </button>

            <div class="flex items-center gap-1">
                <template x-for="page in Array.from({length: totalPagesAccionesRealizadas()}, (_, i) => i + 1).slice(Math.max(0, currentPageAccionesRealizadas - 3), currentPageAccionesRealizadas + 2)" :key="page">
                    <button @click="currentPageAccionesRealizadas = page"
                            class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                            :class="page === currentPageAccionesRealizadas ? 'bg-blue-600 text-white' : ''">
                        <span x-text="page"></span>
                    </button>
                </template>
            </div>

            <button @click="nextPageAccionesRealizadas()" :disabled="currentPageAccionesRealizadas === totalPagesAccionesRealizadas()"
                    class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                <span>Siguiente</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
    </div>

    <!-- Modales -->
    <div>
        <!-- Modal Nueva Acción -->
        <x-admin.form-modal class="nunito-bold" modalName="isAccionRealizadaModalOpen" title="Nueva Acción Realizada"
            submitLabel="Guardar Acción" formId="formAccionRealizada" maxWidth="max-w-md">
            <div class="space-y-4">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="nombre" x-model="nombre" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="descripcion" x-model="descripcion" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"></textarea>
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Acción -->
        <x-admin.edit-modal class="nunito-bold" modalName="isAccionRealizadaEditModalOpen" title="Editar Acción Realizada"
            itemToEdit="itemToEdit" maxWidth="max-w-md" formId="formEditAccionRealizada">
            <template x-if="itemToEdit">
            <div class="space-y-4">
                <div>
                    <label for="edit_nombre" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="edit_nombre" x-model="itemToEdit.nombre" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>
                    <label for="edit_descripcion" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="edit_descripcion" x-model="itemToEdit.descripcion" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"></textarea>
                </div>
            </div>
            </template>
        </x-admin.edit-modal>

        <!-- Modal Confirmar Eliminación -->
        <x-admin.confirmation-modal class="nunito-regular" modalName="isAccionRealizadaDeleteModalOpen" itemToDelete="itemToDelete"
            message="¿Estás seguro de que quieres eliminar esta acción?" />
    </div>
</div>