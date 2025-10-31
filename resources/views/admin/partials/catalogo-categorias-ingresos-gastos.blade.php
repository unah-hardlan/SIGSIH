<div x-data="{
    isCategoriaModalOpen: false,
    isCategoriaEditModalOpen: false,
    isCategoriaDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    categorias: [],
    // alias expected by the pagination component (component checks `numbers.length`)
    numbers: [],
    loadingCategorias: false,
    nombre_categoria: '',
    descripcion_categoria: '',
    tipo_categoria: '',
    formCategoria: { _touched: {} },
    formEditCategoria: { _touched: {} },
    filtroCategoria: '',
    ordenarPor: '',
    currentPage: 1,
    perPage: 10,
    async fetchCategorias() {
        // NOTA: El JS deberá ser actualizado para usar estos nuevos nombres de campo
        await window.categoriasApiHandlers.fetchCategorias(this);
        // keep the pagination component's alias in sync
        this.numbers = this.categorias;
    },
    async submitCategoria() {
        await window.categoriasApiHandlers.submitCategoria(this);
        // ensure numbers reflects the latest categorias after create
        this.numbers = this.categorias;
    },
    async updateCategoria() {
        await window.categoriasApiHandlers.updateCategoria(this);
        // ensure numbers reflects the latest categorias after update
        this.numbers = this.categorias;
    },
    async deleteCategoria() {
        await window.categoriasApiHandlers.deleteCategoria(this);
        // ensure numbers reflects the latest categorias after delete
        this.numbers = this.categorias;
    },
    handleModalSubmit(event) {
        if(event.detail.formId === 'formCategoria') this.submitCategoria();
        if(event.detail.formId === 'formEditCategoria') this.updateCategoria();
    },
    handleDelete() {
        if (this.isCategoriaDeleteModalOpen) {
            this.deleteCategoria();
        }
    },
    paginatedCategorias() {
        return this.categorias.slice((this.currentPage - 1) * this.perPage, this.currentPage * this.perPage);
    },
    totalPages() {
        return Math.ceil(this.categorias.length / this.perPage);
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
    }
}"
x-init="fetchCategorias()"
x-effect="
$watch('filtroCategoria', () => { fetchCategorias(); currentPage = 1; });
$watch('ordenarPor', () => { fetchCategorias(); currentPage = 1; });
"
@keydown.escape.window="
    isCategoriaModalOpen = false;
    isCategoriaEditModalOpen = false;
    isCategoriaDeleteModalOpen = false;
"
@modal-submit.window="handleModalSubmit($event)"
@confirm-delete.window="handleDelete()">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Catálogo de Categorías de ingresos y gastos</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
                'searchModel' => 'filtroCategoria',
                'ordenarModel' => 'ordenarPor',
                'ordenarOptions' => [
                    'nombre_categoria' => 'Nombre',
                    'id_categoria_pk' => 'ID'
                ]
            ])
        </x-slot>

        <x-slot name="actions">
            <button
                @click="formCategoria = { _touched: {} }; nombre_categoria = ''; descripcion_categoria = ''; tipo_categoria = ''; isCategoriaModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nueva Categoría
            </button>
        </x-slot>

        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Nombre</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Descripción</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300"> Tipo Categoria</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loadingCategorias">
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando categorías...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingCategorias && categorias.length === 0">
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-500 nunito-regular">
                                No hay categorías registradas
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingCategorias && categorias.length > 0">
                        <template x-for="(categoria, index) in paginatedCategorias()" :key="categoria.id_categoria_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === categorias.length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="categoria.nombre_categoria"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="categoria.descripcion_categoria"></td>
                                 <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="categoria.tipo_categoria"></td>
                                <td class="py-2 px-4 flex gap-2" :class="{ 'last:rounded-br-lg': index === categorias.length - 1 }">
                                        <a href="#" @click.prevent="itemToEdit = { ...categoria }; formEditCategoria = { _touched: {} }; isCategoriaEditModalOpen = true" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    <a href="#" @click.prevent="isCategoriaDeleteModalOpen = true; itemToDelete = { id_categoria_pk: categoria.id_categoria_pk, nombre: categoria.nombre_categoria }" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loadingCategorias">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando categorías...
                </div>
            </template>
            <template x-if="!loadingCategorias && categorias.length === 0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center text-gray-500 nunito-regular">
                    No hay categorías registradas
                </div>
            </template>
            <template x-if="!loadingCategorias && categorias.length > 0">
                <template x-for="categoria in paginatedCategorias()" :key="categoria.id_categoria_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-2 border dark:border-gray-800 border-black">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="categoria.nombre_categoria"></h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="categoria.descripcion_categoria"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="categoria.tipo_categoria"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="itemToEdit = { ...categoria }; formEditCategoria = { _touched: {} }; isCategoriaEditModalOpen = true" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="isCategoriaDeleteModalOpen = true; itemToDelete = { id_categoria_pk: categoria.id_categoria_pk, nombre: categoria.nombre_categoria }" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <x-pagination />

    <!-- Modales -->
    <div>
        <!-- Modal Nueva Categoría -->
        <x-admin.form-modal class="nunito-bold" modalName="isCategoriaModalOpen" title="Nueva Categoría"
            submitLabel="Guardar Categoría" formId="formCategoria" maxWidth="max-w-md">
            <div class="space-y-4">
                <div>
                    <label for="nombre_categoria" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="nombre_categoria" x-model="nombre_categoria" required maxlength="150"
                        @input="formCategoria = formCategoria || { _touched: {} }; formCategoria._touched.nombre_categoria = true"
                        @blur="formCategoria._touched.nombre_categoria = true"
                        :class="formCategoria && formCategoria._touched && formCategoria._touched.nombre_categoria && (nombre_categoria === '' || nombre_categoria.length > 150) ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <small :class="formCategoria && formCategoria._touched && formCategoria._touched.nombre_categoria && (nombre_categoria === '' || nombre_categoria.length > 150) ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                </div>
                <div>
                    <label for="descripcion_categoria" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="descripcion_categoria" x-model="descripcion_categoria" rows="3" maxlength="255"
                        @input="formCategoria = formCategoria || { _touched: {} }; formCategoria._touched.descripcion_categoria = true"
                        @blur="formCategoria._touched.descripcion_categoria = true"
                        :class="formCategoria && formCategoria._touched && formCategoria._touched.descripcion_categoria && descripcion_categoria.length > 255 ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"></textarea>
                    <small :class="formCategoria && formCategoria._touched && formCategoria._touched.descripcion_categoria && descripcion_categoria.length > 255 ? 'text-red-500' : ''">Máximo 255 caracteres.</small>
                </div>
                <div>
                    <label for="tipo_categoria" class="block text-sm font-medium text-gray-700 nunito-bold">Tipo de Categoría</label>
                    <select id="tipo_categoria" x-model="tipo_categoria" required
                        @change="formCategoria = formCategoria || { _touched: {} }; formCategoria._touched.tipo_categoria = true"
                        :class="formCategoria && formCategoria._touched && formCategoria._touched.tipo_categoria && tipo_categoria === '' ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                        <option value="">Seleccionar tipo</option>
                        <option value="ingreso">Ingreso</option>
                        <option value="gasto">Gasto</option>
                    </select>
                    <small :class="formCategoria && formCategoria._touched && formCategoria._touched.tipo_categoria && tipo_categoria === '' ? 'text-red-500' : ''">Selecciona tipo (Ingreso/Gasto).</small>
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Categoría -->
        <x-admin.edit-modal class="nunito-bold" modalName="isCategoriaEditModalOpen" title="Editar Categoría"
            itemToEdit="itemToEdit" maxWidth="max-w-md" formId="formEditCategoria">
            <template x-if="itemToEdit">
            <div class="space-y-4">
                <div>
                    <label for="edit_nombre_categoria" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="edit_nombre_categoria" x-model="itemToEdit.nombre_categoria" required maxlength="150"
                        @input="formEditCategoria = formEditCategoria || { _touched: {} }; formEditCategoria._touched.nombre_categoria = true"
                        @blur="formEditCategoria._touched.nombre_categoria = true"
                        :class="formEditCategoria && formEditCategoria._touched && formEditCategoria._touched.nombre_categoria && (itemToEdit.nombre_categoria === '' || itemToEdit.nombre_categoria.length > 150) ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <small :class="formEditCategoria && formEditCategoria._touched && formEditCategoria._touched.nombre_categoria && (itemToEdit.nombre_categoria === '' || itemToEdit.nombre_categoria.length > 150) ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                </div>
                <div>
                    <label for="edit_descripcion_categoria" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="edit_descripcion_categoria" x-model="itemToEdit.descripcion_categoria" rows="3" maxlength="255"
                        @input="formEditCategoria = formEditCategoria || { _touched: {} }; formEditCategoria._touched.descripcion_categoria = true"
                        @blur="formEditCategoria._touched.descripcion_categoria = true"
                        :class="formEditCategoria && formEditCategoria._touched && formEditCategoria._touched.descripcion_categoria && itemToEdit.descripcion_categoria.length > 255 ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"></textarea>
                    <small :class="formEditCategoria && formEditCategoria._touched && formEditCategoria._touched.descripcion_categoria && itemToEdit.descripcion_categoria.length > 255 ? 'text-red-500' : ''">Máximo 255 caracteres.</small>
                </div>
                <div>
                    <label for="edit_tipo_categoria" class="block text-sm font-medium text-gray-700 nunito-bold">Tipo de Categoría</label>
                    <select id="edit_tipo_categoria" x-model="itemToEdit.tipo_categoria" required
                        @change="formEditCategoria = formEditCategoria || { _touched: {} }; formEditCategoria._touched.tipo_categoria = true"
                        :class="formEditCategoria && formEditCategoria._touched && formEditCategoria._touched.tipo_categoria && itemToEdit.tipo_categoria === '' ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                        <option value="">Seleccionar tipo</option>
                        <option value="ingreso">Ingreso</option>
                        <option value="gasto">Gasto</option>
                    </select>
                    <small :class="formEditCategoria && formEditCategoria._touched && formEditCategoria._touched.tipo_categoria && itemToEdit.tipo_categoria === '' ? 'text-red-500' : ''">Selecciona tipo (Ingreso/Gasto).</small>
                </div>
            </div>
            </template>
        </x-admin.edit-modal>

        <!-- Modal Confirmar Eliminación -->
        <x-admin.confirmation-modal class="nunito-regular" modalName="isCategoriaDeleteModalOpen" itemToDelete="itemToDelete"
            message="¿Estás seguro de que quieres eliminar esta categoría?" />
    </div>
</div>