<div x-data="{
    isEstadoProyectoModalOpen: false,
    isEstadoProyectoEditModalOpen: false,
    isEstadoProyectoDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    estadosProyecto: [],
    categorias: [],
    numbers: [],
    loadingEstadosProyecto: false,
    codigo: '',
    nombre: '',
    descripcion: '',
    es_final: false,
    orden: '',
    formEstadoProyecto: { _touched: {} },
    formEditEstadoProyecto: { _touched: {} },
    currentPage: 1,
    perPage: 10,
    paginatedEstadosProyecto() {
        return this.estadosProyecto.slice((this.currentPage - 1) * this.perPage, this.currentPage * this.perPage);
    },
    totalPages() {
        return Math.max(1, Math.ceil((this.estadosProyecto || []).length / this.perPage));
    },
    nextPage() {
        if (this.currentPage < this.totalPages()) {
            this.currentPage++;
        }
    },
    prevPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
        }
    },
    goToPage(page) {
        this.currentPage = page;
    },
    filtroEstadoProyecto: '',
    ordenarPor: '',
    async fetchEstadosProyecto() {
        await window.estadosProyectoApiHandlers.fetchEstadosProyecto(this);
    this.categorias = this.estadosProyecto;
    this.numbers = this.estadosProyecto;
    },
    async submitEstadoProyecto() {
        await window.estadosProyectoApiHandlers.submitEstadoProyecto(this);
    this.categorias = this.estadosProyecto;
    this.numbers = this.estadosProyecto;
    },
    async updateEstadoProyecto() {
        await window.estadosProyectoApiHandlers.updateEstadoProyecto(this);
    this.categorias = this.estadosProyecto;
    this.numbers = this.estadosProyecto;
    },
    async deleteEstadoProyecto() {
        await window.estadosProyectoApiHandlers.deleteEstadoProyecto(this);
    this.categorias = this.estadosProyecto;
    this.numbers = this.estadosProyecto;
    },
    handleModalSubmit(event) {
        if(event.detail.formId === 'formEstadoProyecto') this.submitEstadoProyecto();
        if(event.detail.formId === 'formEditEstadoProyecto') this.updateEstadoProyecto();
    },
    handleDelete() {
        if (this.isEstadoProyectoDeleteModalOpen) {
            this.deleteEstadoProyecto();
        }
    }
}"
    x-init="fetchEstadosProyecto()"
    x-effect="
$watch('filtroEstadoProyecto', () => { fetchEstadosProyecto(); currentPage = 1; });
$watch('ordenarPor', () => { fetchEstadosProyecto(); currentPage = 1; });
"
    @keydown.escape.window="
    isEstadoProyectoModalOpen = false;
    isEstadoProyectoEditModalOpen = false;
    isEstadoProyectoDeleteModalOpen = false;
"
    @modal-submit.window="handleModalSubmit($event)"
    @confirm-delete.window="handleDelete()">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Catálogo de Estados de Proyecto</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
            'searchModel' => 'filtroEstadoProyecto',
            'ordenarModel' => 'ordenarPor',
            'ordenarOptions' => [
            'nombre' => 'Nombre',
            'id' => 'ID Estado'
            ]
            ])
        </x-slot>

        <x-slot name="actions">
            @perm(['Catálogo','Estados de Proyecto','Estado de Proyecto'], 'insercion')
            <button
                @click="formEstadoProyecto = { _touched: {} }; codigo=''; nombre=''; descripcion=''; es_final=false; orden=''; isEstadoProyectoModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo estado de proyecto
            </button>
            @else
            <button disabled title="No tiene permiso para crear Estados de Proyecto"
                class="bg-green-600 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm opacity-50 cursor-not-allowed">
                Nuevo estado de proyecto
            </button>
            @endperm
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
                    <template x-if="loadingEstadosProyecto">
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando estados de proyecto...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingEstadosProyecto && estadosProyecto.length === 0">
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 nunito-regular">
                                No hay estados de proyecto registrados
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingEstadosProyecto && estadosProyecto.length > 0">
                        <template x-for="(estadoProyecto, index) in paginatedEstadosProyecto()" :key="estadoProyecto.id_estado_proyecto_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === paginatedEstadosProyecto().length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="estadoProyecto.nombre"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="estadoProyecto.codigo"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="estadoProyecto.descripcion"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200">
                                    <span x-text="estadoProyecto.es_final ? 'Sí' : 'No'"></span>
                                </td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="estadoProyecto.orden"></td>
                                <td class="py-2 px-4 flex gap-2" :class="{ 'last:rounded-br-lg': index === estadosProyecto.length - 1 }">
                                    @perm(['Catálogo','Estados de Proyecto','Estado de Proyecto'], 'actualizacion')
                                    <a href="#" @click.prevent="formEditEstadoProyecto = { _touched: {} }; isEstadoProyectoEditModalOpen = true; itemToEdit = { ...estadoProyecto }" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    @else
                                    <span class="text-blue-300 cursor-not-allowed" title="No tiene permiso para editar Estados de Proyecto"><i class="fas fa-edit"></i></span>
                                    @endperm

                                    @perm(['Catálogo','Estados de Proyecto','Estado de Proyecto'], 'eliminacion')
                                    <a href="#" @click.prevent="isEstadoProyectoDeleteModalOpen = true; itemToDelete = { id_estado_proyecto_pk: estadoProyecto.id_estado_proyecto_pk, nombre: estadoProyecto.nombre }" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                    @else
                                    <span class="text-red-300 cursor-not-allowed" title="No tiene permiso para eliminar Estados de Proyecto"><i class="fas fa-trash"></i></span>
                                    @endperm
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loadingEstadosProyecto">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando estados de proyecto...
                </div>
            </template>
            <template x-if="!loadingEstadosProyecto && estadosProyecto.length === 0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    No hay estados de proyecto registrados
                </div>
            </template>
            <template x-if="!loadingEstadosProyecto && estadosProyecto.length > 0">
                <template x-for="estadoProyecto in paginatedEstadosProyecto()" :key="estadoProyecto.id_estado_proyecto_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="estadoProyecto.nombre"></h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'Código: ' + estadoProyecto.codigo"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="estadoProyecto.descripcion"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'Es Final: ' + (estadoProyecto.es_final ? 'Sí' : 'No')"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'Orden: ' + estadoProyecto.orden"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            @perm(['Catálogo','Estados de Proyecto','Estado de Proyecto'], 'actualizacion')
                            <button @click.prevent="formEditEstadoProyecto = { _touched: {} }; isEstadoProyectoEditModalOpen = true; itemToEdit = { ...estadoProyecto }" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @else
                            <button disabled title="No tiene permiso para editar Estados de Proyecto" class="px-3 py-1 text-xs bg-blue-600 text-white rounded opacity-50 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @endperm

                            @perm(['Catálogo','Estados de Proyecto','Estado de Proyecto'], 'eliminacion')
                            <button @click.prevent="isEstadoProyectoDeleteModalOpen = true; itemToDelete = { id_estado_proyecto_pk: estadoProyecto.id_estado_proyecto_pk, nombre: estadoProyecto.nombre }" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @else
                            <button disabled title="No tiene permiso para eliminar Estados de Proyecto" class="px-3 py-1 text-xs bg-red-600 text-white rounded opacity-50 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @endperm
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <x-pagination />

    <div>
        @perm(['Catálogo','Estados de Proyecto','Estado de Proyecto'], 'insercion')
        <x-admin.form-modal class="nunito-bold" modalName="isEstadoProyectoModalOpen" title="Nuevo Estado de Proyecto"
            submitLabel="Guardar Estado de Proyecto" formId="formEstadoProyecto" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="nombre" x-model="nombre" maxlength="150" required @input="formEstadoProyecto._touched.nombre = true" @blur="formEstadoProyecto._touched.nombre = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEstadoProyecto._touched && formEstadoProyecto._touched.nombre && !nombre ? 'border-red-500' : (formEstadoProyecto._touched && formEstadoProyecto._touched.nombre && (nombre && nombre.length >= 150) ? 'border-red-500' : '')">
                    <small class="block mt-1 text-sm text-gray-500" :class="formEstadoProyecto._touched && formEstadoProyecto._touched.nombre && !nombre ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                </div>
                <div>
                    <label for="codigo" class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                    <input type="text" id="codigo" x-model="codigo" maxlength="10" required @input="formEstadoProyecto._touched.codigo = true" @blur="formEstadoProyecto._touched.codigo = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEstadoProyecto._touched && formEstadoProyecto._touched.codigo && (codigo === '' || (codigo && codigo.length >= 10)) ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500" :class="formEstadoProyecto._touched && formEstadoProyecto._touched.codigo && (codigo === '' || (codigo && codigo.length >= 10)) ? 'text-red-500' : ''">Requerido. Máximo 10 caracteres.</small>
                </div>
                <div class="col-span-2">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="descripcion" x-model="descripcion" maxlength="255" rows="2" @input="formEstadoProyecto._touched.descripcion = true" @blur="formEstadoProyecto._touched.descripcion = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEstadoProyecto._touched && formEstadoProyecto._touched.descripcion && (descripcion === '' || (descripcion && descripcion.length >= 255)) ? 'border-red-500' : ''"></textarea>
                    <small class="block mt-1 text-sm text-gray-500" :class="formEstadoProyecto._touched && formEstadoProyecto._touched.descripcion && (descripcion === '' || (descripcion && descripcion.length >= 255)) ? 'text-red-500' : ''">Máximo 255 caracteres.</small>
                </div>
                <div>
                    <label for="orden" class="block text-sm font-medium text-gray-700 nunito-bold">Orden</label>
                    <input type="number" id="orden" x-model="orden" required min="0" @input="formEstadoProyecto._touched.orden = true" @blur="formEstadoProyecto._touched.orden = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEstadoProyecto._touched && formEstadoProyecto._touched.orden && (orden === '' || orden < 0) ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500" :class="formEstadoProyecto._touched && formEstadoProyecto._touched.orden && (orden === '' || orden < 0) ? 'text-red-500' : ''">Requerido. Valor >= 0.</small>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="es_final" x-model="es_final"
                        class="rounded border-gray-500 text-blue-600 shadow-sm focus:border-gray-500 ">
                    <label for="es_final" class="ml-2 block text-sm font-medium text-gray-700 nunito-bold">Es Final</label>
                </div>
            </div>
        </x-admin.form-modal>
        @endperm

        @perm(['Catálogo','Estados de Proyecto','Estado de Proyecto'], 'actualizacion')
        <x-admin.edit-modal class="nunito-bold" modalName="isEstadoProyectoEditModalOpen" title="Editar Estado de Proyecto" itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditEstadoProyecto">
            <template x-if="itemToEdit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit_nombre" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                        <input type="text" id="edit_nombre" x-model="itemToEdit.nombre" maxlength="150" required @input="formEditEstadoProyecto._touched.nombre = true" @blur="formEditEstadoProyecto._touched.nombre = true"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                            :class="formEditEstadoProyecto._touched && formEditEstadoProyecto._touched.nombre && !itemToEdit.nombre ? 'border-red-500' : (formEditEstadoProyecto._touched && formEditEstadoProyecto._touched.nombre && (itemToEdit.nombre && itemToEdit.nombre.length >= 150) ? 'border-red-500' : '')">
                        <small class="block mt-1 text-sm text-gray-500" :class="formEditEstadoProyecto._touched && formEditEstadoProyecto._touched.nombre && !itemToEdit.nombre ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                    </div>
                    <div>
                        <label for="edit_codigo" class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                        <input type="text" id="edit_codigo" x-model="itemToEdit.codigo" maxlength="10" required @input="formEditEstadoProyecto._touched.codigo = true" @blur="formEditEstadoProyecto._touched.codigo = true"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                            :class="formEditEstadoProyecto._touched && formEditEstadoProyecto._touched.codigo && (itemToEdit.codigo === '' || (itemToEdit.codigo && itemToEdit.codigo.length >= 10)) ? 'border-red-500' : ''">
                        <small class="block mt-1 text-sm text-gray-500" :class="formEditEstadoProyecto._touched && formEditEstadoProyecto._touched.codigo && (itemToEdit.codigo === '' || (itemToEdit.codigo && itemToEdit.codigo.length >= 10)) ? 'text-red-500' : ''">Requerido. Máximo 10 caracteres.</small>
                    </div>
                    <div class="col-span-2">
                        <label for="edit_descripcion" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                        <textarea id="edit_descripcion" x-model="itemToEdit.descripcion" maxlength="255" rows="2" @input="formEditEstadoProyecto._touched.descripcion = true" @blur="formEditEstadoProyecto._touched.descripcion = true"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                            :class="formEditEstadoProyecto._touched && formEditEstadoProyecto._touched.descripcion && (itemToEdit.descripcion === '' || (itemToEdit.descripcion && itemToEdit.descripcion.length >= 255)) ? 'border-red-500' : ''"></textarea>
                        <small class="block mt-1 text-sm text-gray-500" :class="formEditEstadoProyecto._touched && formEditEstadoProyecto._touched.descripcion && (itemToEdit.descripcion === '' || (itemToEdit.descripcion && itemToEdit.descripcion.length >= 255)) ? 'text-red-500' : ''">Máximo 255 caracteres.</small>
                    </div>
                    <div>
                        <label for="edit_orden" class="block text-sm font-medium text-gray-700 nunito-bold">Orden</label>
                        <input type="number" id="edit_orden" x-model="itemToEdit.orden" required min="0" @input="formEditEstadoProyecto._touched.orden = true" @blur="formEditEstadoProyecto._touched.orden = true"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                            :class="formEditEstadoProyecto._touched && formEditEstadoProyecto._touched.orden && (itemToEdit.orden === '' || itemToEdit.orden < 0) ? 'border-red-500' : ''">
                        <small class="block mt-1 text-sm text-gray-500" :class="formEditEstadoProyecto._touched && formEditEstadoProyecto._touched.orden && (itemToEdit.orden === '' || itemToEdit.orden < 0) ? 'text-red-500' : ''">Requerido. Valor >= 0.</small>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="edit_es_final" x-model="itemToEdit.es_final"
                            class="rounded border-gray-500 text-blue-600 shadow-sm focus:border-gray-500 ">
                        <label for="edit_es_final" class="ml-2 block text-sm font-medium text-gray-700 nunito-bold">Es Final</label>
                    </div>
                </div>
            </template>
        </x-admin.edit-modal>
        @endperm

        @perm(['Catálogo','Estados de Proyecto','Estado de Proyecto'], 'eliminacion')
        <x-admin.confirmation-modal class="nunito-regular" modalName="isEstadoProyectoDeleteModalOpen" itemToDelete="itemToDelete"
            message="¿Estás seguro de que quieres eliminar este estado de proyecto?" />
        @endperm
    </div>
</div>