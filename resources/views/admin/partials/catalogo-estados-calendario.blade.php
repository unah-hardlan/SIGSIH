<div x-data="{
    isEstadoCalendarioModalOpen: false,
    isEstadoCalendarioEditModalOpen: false,
    isEstadoCalendarioDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    estadosCalendario: [],
    loadingEstadosCalendario: false,

    //  Variables de Paginación
    numbersEstadosCalendario: [],
    currentPageEstadosCalendario: 1,
    perPageEstadosCalendario: 10,

    codigo: '',
    nombre: '',
    descripcion: '',
    es_final: false,
    orden: '',
    formEstadoCalendario: { _touched: {} },
    formEditEstadoCalendario: { _touched: {} },
    filtroEstadoCalendario: '',
    ordenarPor: 'nombre',

    //  Métodos de Paginación
    paginatedEstadosCalendario() {
        return this.estadosCalendario.slice(
            (this.currentPageEstadosCalendario - 1) * this.perPageEstadosCalendario, 
            this.currentPageEstadosCalendario * this.perPageEstadosCalendario
        );
    },
    totalPagesEstadosCalendario() {
        return Math.max(1, Math.ceil((this.estadosCalendario || []).length / this.perPageEstadosCalendario));
    },
    nextPageEstadosCalendario() {
        if (this.currentPageEstadosCalendario < this.totalPagesEstadosCalendario()) {
            this.currentPageEstadosCalendario++;
        }
    },
    prevPageEstadosCalendario() {
        if (this.currentPageEstadosCalendario > 1) {
            this.currentPageEstadosCalendario--;
        }
    },

    //  Sincronizar Alias en cada operación CRUD
    async fetchEstadosCalendario() {
        await window.estadosCalendarioApiHandlers.fetchEstadosCalendario(this);
        this.numbersEstadosCalendario = this.estadosCalendario; // ← LÍNEA AGREGADA
    },
    async submitEstadoCalendario() {
        await window.estadosCalendarioApiHandlers.submitEstadoCalendario(this);
        this.fetchEstadosCalendario(); // Refrescar datos
    },
    async updateEstadoCalendario() {
        await window.estadosCalendarioApiHandlers.updateEstadoCalendario(this);
        this.fetchEstadosCalendario(); // Refrescar datos
    },
    async deleteEstadoCalendario() {
        await window.estadosCalendarioApiHandlers.deleteEstadoCalendario(this);
        this.fetchEstadosCalendario(); // Refrescar datos
    },
    handleModalSubmit(event) {
        if(event.detail.formId === 'formEstadoCalendario') this.submitEstadoCalendario();
        if(event.detail.formId === 'formEditEstadoCalendario') this.updateEstadoCalendario();
    },
    handleDelete() {
        if (this.isEstadoCalendarioDeleteModalOpen) {
            this.deleteEstadoCalendario();
        }
    }
}"
x-init="fetchEstadosCalendario()"
x-effect="
    // 4️⃣ Reset de página en filtros
    $watch('filtroEstadoCalendario', () => { fetchEstadosCalendario(); currentPageEstadosCalendario = 1; });
    $watch('ordenarPor', () => { fetchEstadosCalendario(); currentPageEstadosCalendario = 1; });
"
@keydown.escape.window="
    isEstadoCalendarioModalOpen = false;
    isEstadoCalendarioEditModalOpen = false;
    isEstadoCalendarioDeleteModalOpen = false;
"
@modal-submit.window="handleModalSubmit($event)"
@confirm-delete.window="handleDelete()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Catálogo de Estados de Calendario</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
                'searchModel' => 'filtroEstadoCalendario',
                'ordenarModel' => 'ordenarPor',
                'ordenarOptions' => [
                    'nombre' => 'Nombre',
                    'id' => 'ID Estado'
                ]
            ])
        </x-slot>

        <x-slot name="actions">
            <button
                @click="formEstadoCalendario = { _touched: {} }; codigo=''; nombre=''; descripcion=''; es_final=false; orden=''; isEstadoCalendarioModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo estado de calendario
            </button>
        </x-slot>

        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Nombre</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Código</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Descripción</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Es Final</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Orden</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loadingEstadosCalendario">
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando estados de calendario...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingEstadosCalendario && estadosCalendario.length === 0">
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 nunito-regular">
                                No hay estados de calendario registrados
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingEstadosCalendario && estadosCalendario.length > 0">
                        <!--  Usar paginatedEstadosCalendario() en el template -->
                        <template x-for="(estadoCalendario, index) in paginatedEstadosCalendario()" :key="estadoCalendario.id_estado_calendario_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === paginatedEstadosCalendario().length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estadoCalendario.nombre"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estadoCalendario.codigo"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estadoCalendario.descripcion"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">
                                    <span x-text="estadoCalendario.es_final ? 'Sí' : 'No'"></span>
                                </td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estadoCalendario.orden"></td>
                                <td class="py-2 px-4 flex gap-2" :class="{ 'last:rounded-br-lg': index === paginatedEstadosCalendario().length - 1 }">
                                    <a href="#" @click.prevent="formEditEstadoCalendario = { _touched: {} }; isEstadoCalendarioEditModalOpen = true; itemToEdit = { ...estadoCalendario }" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    <a href="#" @click.prevent="isEstadoCalendarioDeleteModalOpen = true; itemToDelete = {id_estado_calendario_pk: estadoCalendario.id_estado_calendario_pk, nombre: estadoCalendario.nombre}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loadingEstadosCalendario">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando estados de calendario...
                </div>
            </template>
            <template x-if="!loadingEstadosCalendario && estadosCalendario.length === 0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    No hay estados de calendario registrados
                </div>
            </template>
            <template x-if="!loadingEstadosCalendario && estadosCalendario.length > 0">
                <template x-for="estadoCalendario in paginatedEstadosCalendario()" :key="estadoCalendario.id_estado_calendario_pk">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="estadoCalendario.nombre"></h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'Código: ' + estadoCalendario.codigo"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="estadoCalendario.descripcion"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'Es Final: ' + (estadoCalendario.es_final ? 'Sí' : 'No')"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'Orden: ' + estadoCalendario.orden"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="formEditEstadoCalendario = { _touched: {} }; isEstadoCalendarioEditModalOpen = true; itemToEdit = {id_estado_calendario_pk: estadoCalendario.id_estado_calendario_pk, codigo: estadoCalendario.codigo, nombre: estadoCalendario.nombre, descripcion: estadoCalendario.descripcion, es_final: estadoCalendario.es_final, orden: estadoCalendario.orden}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="isEstadoCalendarioDeleteModalOpen = true; itemToDelete = {id_estado_calendario_pk: estadoCalendario.id_estado_calendario_pk, nombre: estadoCalendario.nombre}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <!--  Componente de Paginación -->
    <div x-show="estadosCalendario.length > perPageEstadosCalendario" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
        <!-- Mostrando (centered, supports light/dark) -->
        <div class="mb-2">
            <span class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                Mostrando
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="(currentPageEstadosCalendario - 1) * perPageEstadosCalendario + 1"></strong>
                a
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="Math.min(currentPageEstadosCalendario * perPageEstadosCalendario, estadosCalendario.length)"></strong>
                de
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="estadosCalendario.length"></strong>
                resultados
            </span>
        </div>

        <!-- Controls (light/dark) -->
        <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
            <button @click="prevPageEstadosCalendario()" :disabled="currentPageEstadosCalendario === 1"
                    class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                <span>Anterior</span>
            </button>

            <div class="flex items-center gap-1">
                <template x-for="page in Array.from({length: totalPagesEstadosCalendario()}, (_, i) => i + 1).slice(Math.max(0, currentPageEstadosCalendario - 3), currentPageEstadosCalendario + 2)" :key="page">
                    <button @click="currentPageEstadosCalendario = page"
                            class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                            :class="page === currentPageEstadosCalendario ? 'bg-blue-600 text-white' : ''">
                        <span x-text="page"></span>
                    </button>
                </template>
            </div>

            <button @click="nextPageEstadosCalendario()" :disabled="currentPageEstadosCalendario === totalPagesEstadosCalendario()"
                    class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                <span>Siguiente</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
    </div>

    <!-- Modales -->
    <div>
        <!-- Modal Nuevo Estado de Calendario -->
        <x-admin.form-modal class="nunito-bold" modalName="isEstadoCalendarioModalOpen" title="Nuevo Estado de Calendario"
            submitLabel="Guardar Estado de Calendario" formId="formEstadoCalendario" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="nombre" x-model="nombre" maxlength="150" required @input="formEstadoCalendario._touched.nombre = true" @blur="formEstadoCalendario._touched.nombre = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEstadoCalendario._touched && formEstadoCalendario._touched.nombre && !nombre ? 'border-red-500' : (formEstadoCalendario._touched && formEstadoCalendario._touched.nombre && (nombre && nombre.length >= 150) ? 'border-red-500' : '')">
                    <small class="block mt-1 text-sm text-gray-500" :class="formEstadoCalendario._touched && formEstadoCalendario._touched.nombre && !nombre ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                </div>
                <div>
                    <label for="codigo" class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                    <input type="text" id="codigo" x-model="codigo" maxlength="10" required @input="formEstadoCalendario._touched.codigo = true" @blur="formEstadoCalendario._touched.codigo = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEstadoCalendario._touched && formEstadoCalendario._touched.codigo && (codigo === '' || (codigo && codigo.length >= 10)) ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500" :class="formEstadoCalendario._touched && formEstadoCalendario._touched.codigo && (codigo === '' || (codigo && codigo.length >= 10)) ? 'text-red-500' : ''">Requerido. Máximo 10 caracteres.</small>
                </div>
                <div class="col-span-2">
                    <label for="descripcion"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="descripcion" x-model="descripcion" maxlength="255" rows="2" @input="formEstadoCalendario._touched.descripcion = true" @blur="formEstadoCalendario._touched.descripcion = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEstadoCalendario._touched && formEstadoCalendario._touched.descripcion && (descripcion === '' || (descripcion && descripcion.length >= 255)) ? 'border-red-500' : ''"></textarea>
                    <small class="block mt-1 text-sm text-gray-500" :class="formEstadoCalendario._touched && formEstadoCalendario._touched.descripcion && (descripcion === '' || (descripcion && descripcion.length >= 255)) ? 'text-red-500' : ''">Máximo 255 caracteres.</small>
                </div>
                <div>
                    <label for="orden" class="block text-sm font-medium text-gray-700 nunito-bold">Orden</label>
                    <input type="number" id="orden" x-model="orden" required min="0" @input="formEstadoCalendario._touched.orden = true" @blur="formEstadoCalendario._touched.orden = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEstadoCalendario._touched && formEstadoCalendario._touched.orden && (orden === '' || orden < 0) ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500" :class="formEstadoCalendario._touched && formEstadoCalendario._touched.orden && (orden === '' || orden < 0) ? 'text-red-500' : ''">Requerido. Valor >= 0.</small>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="es_final" x-model="es_final"
                        class="rounded border-gray-500 text-blue-600 shadow-sm focus:border-gray-500 ">
                    <label for="es_final" class="ml-2 block text-sm font-medium text-gray-700 nunito-bold">Es Final</label>
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Estado de Calendario -->
        <x-admin.edit-modal class="nunito-bold" modalName="isEstadoCalendarioEditModalOpen" title="Editar Estado de Calendario" itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditEstadoCalendario">
            <template x-if="itemToEdit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_nombre" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="edit_nombre" x-model="itemToEdit.nombre" maxlength="150" required @input="formEditEstadoCalendario._touched.nombre = true" @blur="formEditEstadoCalendario._touched.nombre = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEditEstadoCalendario._touched && formEditEstadoCalendario._touched.nombre && !itemToEdit.nombre ? 'border-red-500' : (formEditEstadoCalendario._touched && formEditEstadoCalendario._touched.nombre && (itemToEdit.nombre && itemToEdit.nombre.length >= 150) ? 'border-red-500' : '')">
                    <small class="block mt-1 text-sm text-gray-500" :class="formEditEstadoCalendario._touched && formEditEstadoCalendario._touched.nombre && !itemToEdit.nombre ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                </div>
                <div>
                    <label for="edit_codigo" class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                    <input type="text" id="edit_codigo" x-model="itemToEdit.codigo" maxlength="10" required @input="formEditEstadoCalendario._touched.codigo = true" @blur="formEditEstadoCalendario._touched.codigo = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEditEstadoCalendario._touched && formEditEstadoCalendario._touched.codigo && (itemToEdit.codigo === '' || (itemToEdit.codigo && itemToEdit.codigo.length >= 10)) ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500" :class="formEditEstadoCalendario._touched && formEditEstadoCalendario._touched.codigo && (itemToEdit.codigo === '' || (itemToEdit.codigo && itemToEdit.codigo.length >= 10)) ? 'text-red-500' : ''">Requerido. Máximo 10 caracteres.</small>
                </div>
                <div class="col-span-2">
                    <label for="edit_descripcion"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="edit_descripcion" x-model="itemToEdit.descripcion" maxlength="255" rows="2" @input="formEditEstadoCalendario._touched.descripcion = true" @blur="formEditEstadoCalendario._touched.descripcion = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEditEstadoCalendario._touched && formEditEstadoCalendario._touched.descripcion && (itemToEdit.descripcion === '' || (itemToEdit.descripcion && itemToEdit.descripcion.length >= 255)) ? 'border-red-500' : ''"></textarea>
                    <small class="block mt-1 text-sm text-gray-500" :class="formEditEstadoCalendario._touched && formEditEstadoCalendario._touched.descripcion && (itemToEdit.descripcion === '' || (itemToEdit.descripcion && itemToEdit.descripcion.length >= 255)) ? 'text-red-500' : ''">Máximo 255 caracteres.</small>
                </div>
                <div>
                    <label for="edit_orden" class="block text-sm font-medium text-gray-700 nunito-bold">Orden</label>
                    <input type="number" id="edit_orden" x-model="itemToEdit.orden" required min="0" @input="formEditEstadoCalendario._touched.orden = true" @blur="formEditEstadoCalendario._touched.orden = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEditEstadoCalendario._touched && formEditEstadoCalendario._touched.orden && (itemToEdit.orden === '' || itemToEdit.orden < 0) ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500" :class="formEditEstadoCalendario._touched && formEditEstadoCalendario._touched.orden && (itemToEdit.orden === '' || itemToEdit.orden < 0) ? 'text-red-500' : ''">Requerido. Valor >= 0.</small>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="edit_es_final" x-model="itemToEdit.es_final"
                        class="rounded border-gray-500 text-blue-600 shadow-sm focus:border-gray-500 ">
                    <label for="edit_es_final" class="ml-2 block text-sm font-medium text-gray-700 nunito-bold">Es Final</label>
                </div>
            </div>
            </template>
        </x-admin.edit-modal>

        <!-- Modal Confirmar Eliminación -->
        <x-admin.confirmation-modal class="nunito-regular" modalName="isEstadoCalendarioDeleteModalOpen" itemToDelete="itemToDelete"
            message="¿Estás seguro de que quieres eliminar este estado de calendario?" />
    </div>
</div>