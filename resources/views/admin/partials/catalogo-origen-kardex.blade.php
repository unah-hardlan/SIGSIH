<div x-data="{
    isModalOpen: false,
    isEditModalOpen: false,
    isDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    origenes: [],
    loading: false,

    numbersOrigenes: [],
    currentPageOrigenes: 1,
    perPageOrigenes: 10,

    nombre_origen: '',
    descripcion_origen: '',
    activo: true,
    edit_nombre_origen: '',
    edit_descripcion_origen: '',
    edit_activo: true,
    formOrigen: { _touched: {} },
    formEditOrigen: { _touched: {} },
    filtroOrigen: '',
    ordenarPor: 'nombre',

    paginatedOrigenes() {
        return this.origenes.slice(
            (this.currentPageOrigenes - 1) * this.perPageOrigenes, 
            this.currentPageOrigenes * this.perPageOrigenes
        );
    },
    totalPagesOrigenes() {
        return Math.ceil(this.origenes.length / this.perPageOrigenes);
    },
    nextPageOrigenes() {
        if (this.currentPageOrigenes < this.totalPagesOrigenes()) {
            this.currentPageOrigenes++;
        }
    },
    prevPageOrigenes() {
        if (this.currentPageOrigenes > 1) {
            this.currentPageOrigenes--;
        }
    },

    async fetchItems(){ 
        await window.origenKardexApiHandlers.fetchOrigenes(this); 
        this.numbersOrigenes = this.origenes; 
    },
    async submit(){ 
        await window.origenKardexApiHandlers.submitOrigen(this);
        this.fetchItems(); 
    },
    async update(){ 
        await window.origenKardexApiHandlers.updateOrigen(this);
        this.fetchItems(); 
    },
    async remove(){ 
        await window.origenKardexApiHandlers.deleteOrigen(this);
        this.fetchItems(); 
    },
    handleModalSubmit(e){
        if(e.detail.formId === 'formOrigen') this.submit();
        if(e.detail.formId === 'formEditOrigen') {
            if (this.itemToEdit) {
                this.itemToEdit.nombre_origen = this.edit_nombre_origen ?? '';
                this.itemToEdit.descripcion_origen = this.edit_descripcion_origen ?? '';
                this.itemToEdit.activo = !!this.edit_activo;
            }
            this.update();
        }
    },
    handleDelete(){ this.remove(); }
}" x-init="
    fetchItems();
    $watch('filtroOrigen', () => { fetchItems(); currentPageOrigenes = 1; });
    $watch('ordenarPor', () => { fetchItems(); currentPageOrigenes = 1; });
" @keydown.escape.window="isModalOpen=false; isEditModalOpen=false; isDeleteModalOpen=false;"
    @modal-submit.window="handleModalSubmit($event)" @confirm-delete.window="handleDelete()">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Gestión de Orígenes (Kardex)</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
            'searchModel' => 'filtroOrigen',
            'ordenarOptions' => [ 'nombre' => 'Nombre', 'activo' => 'Activo', 'id' => 'ID' ]
            ])
        </x-slot>

        <x-slot name="actions">
            @perm(['Catálogo','Origen Kardex','Orígenes Kardex','Origenes Kardex','Origen de Kardex'], 'insercion')
            <button @click="formOrigen = { _touched: {} }; nombre_origen = ''; descripcion_origen = ''; activo = true; isModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm">
                Nuevo Origen
            </button>
            @else
            <button type="button" disabled title="Sin permiso para crear"
                class="bg-gray-400 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm opacity-70 cursor-not-allowed">
                Nuevo Origen
            </button>
            @endperm
        </x-slot>

        <x-slot name="table">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left">Nombre</th>
                        <th class="py-2 px-4 text-left">Descripción</th>
                        <th class="py-2 px-4 text-left">Activo</th>
                        <th class="py-2 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loading">
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-500"><i
                                    class="fas fa-spinner fa-spin mr-2"></i>Cargando...</td>
                        </tr>
                    </template>
                    <template x-if="!loading && origenes.length === 0">
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-500">Sin resultados</td>
                        </tr>
                    </template>
                    <template x-for="item in paginatedOrigenes()" :key="item.id_origen_pk">
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-2 px-4" x-text="item.nombre_origen"></td>
                            <td class="py-2 px-4" x-text="item.descripcion_origen"></td>
                            <td class="py-2 px-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                    :class="item.activo ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'">
                                    <i :class="item.activo ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                                    <span x-text="item.activo ? 'Activo' : 'Inactivo'"></span>
                                </span>
                            </td>
                            <td class="py-2 px-4 flex gap-2">
                                @perm(['Catálogo','Origen Kardex','Orígenes Kardex','Origenes Kardex','Origen de Kardex'], 'actualizacion')
                                <a href="#"
                                    @click.prevent="formEditOrigen = { _touched: {} }; itemToEdit = {...item}; edit_nombre_origen = item.nombre_origen || ''; edit_descripcion_origen = item.descripcion_origen || ''; edit_activo = !!item.activo; isEditModalOpen=true"
                                    class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                @else
                                <span class="text-gray-400 cursor-not-allowed" title="Sin permiso para editar"><i class="fas fa-edit"></i></span>
                                @endperm
                                @perm(['Catálogo','Origen Kardex','Orígenes Kardex','Origenes Kardex','Origen de Kardex'], 'eliminacion')
                                <a href="#"
                                    @click.prevent="isDeleteModalOpen=true; itemToDelete = {id_origen_pk: item.id_origen_pk, nombre_origen: item.nombre_origen}"
                                    class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                @else
                                <span class="text-gray-400 cursor-not-allowed" title="Sin permiso para eliminar"><i class="fas fa-trash"></i></span>
                                @endperm
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <div class="space-y-4">
                <template x-if="loading">
                    <div class="p-4 text-center text-gray-500">Cargando...</div>
                </template>
                <template x-if="!loading && origenes.length === 0">
                    <div class="p-4 text-center text-gray-500">Sin resultados</div>
                </template>
                <template x-for="item in paginatedOrigenes()" :key="item.id_origen_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-3 border dark:border-gray-800 border-black">
                        <div class="flex justify-between items-start">
                            <h3 class="font-semibold text-gray-900 dark:text-white" x-text="item.nombre_origen"></h3>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                :class="item.activo ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'">
                                <i :class="item.activo ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                                <span x-text="item.activo ? 'Activo' : 'Inactivo'"></span>
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300" x-text="item.descripcion_origen"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t dark:border-gray-700">
                            @perm(['Catálogo','Origen Kardex','Orígenes Kardex','Origenes Kardex','Origen de Kardex'], 'actualizacion')
                            <button
                                @click.prevent="formEditOrigen = { _touched: {} }; itemToEdit = {...item}; edit_nombre_origen = item.nombre_origen || ''; edit_descripcion_origen = item.descripcion_origen || ''; edit_activo = !!item.activo; isEditModalOpen = true"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @else
                            <button type="button" disabled title="Sin permiso para editar"
                                class="px-3 py-1 text-xs bg-gray-400 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @endperm
                            @perm(['Catálogo','Origen Kardex','Orígenes Kardex','Origenes Kardex','Origen de Kardex'], 'eliminacion')
                            <button
                                @click.prevent="isDeleteModalOpen = true; itemToDelete = {id_origen_pk: item.id_origen_pk, nombre_origen: item.nombre_origen}"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @else
                            <button type="button" disabled title="Sin permiso para eliminar"
                                class="px-3 py-1 text-xs bg-gray-400 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @endperm
                        </div>
                    </div>
                </template>
            </div>
        </x-slot>
    </x-responsive-table>

    <div x-show="origenes.length > perPageOrigenes" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
        <div class="mb-2">
            <span class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                Mostrando
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="(currentPageOrigenes - 1) * perPageOrigenes + 1"></strong>
                a
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="Math.min(currentPageOrigenes * perPageOrigenes, origenes.length)"></strong>
                de
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="origenes.length"></strong>
                resultados
            </span>
        </div>

        <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
            <button @click="prevPageOrigenes()" :disabled="currentPageOrigenes === 1"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span>Anterior</span>
            </button>

            <div class="flex items-center gap-1">
                <template x-for="page in Array.from({length: totalPagesOrigenes()}, (_, i) => i + 1).slice(Math.max(0, currentPageOrigenes - 3), currentPageOrigenes + 2)" :key="page">
                    <button @click="currentPageOrigenes = page"
                        class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-blue-900 hover:text-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                        :class="page === currentPageOrigenes ? 'bg-blue-600 text-white' : ''">
                        <span x-text="page"></span>
                    </button>
                </template>
            </div>

            <button @click="nextPageOrigenes()" :disabled="currentPageOrigenes === totalPagesOrigenes()"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                <span>Siguiente</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <div>
        @perm(['Catálogo','Origen Kardex','Orígenes Kardex','Origenes Kardex','Origen de Kardex'], 'insercion')
        <x-admin.form-modal class="nunito-bold" modalName="isModalOpen" title="Nuevo Origen" submitLabel="Guardar"
            formId="formOrigen" maxWidth="max-w-md">
            <div class="space-y-4">
                <div>
                    <label for="nombre_origen" class="block text-sm font-medium">Nombre</label>
                    <input type="text" id="nombre_origen" x-model="nombre_origen" required maxlength="150"
                        @input="formOrigen = formOrigen || { _touched: {} }; formOrigen._touched.nombre_origen = true"
                        @blur="formOrigen._touched.nombre_origen = true"
                        :class="formOrigen && formOrigen._touched && formOrigen._touched.nombre_origen && (nombre_origen === '' || nombre_origen.length > 150) ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                    <small :class="formOrigen && formOrigen._touched && formOrigen._touched.nombre_origen && (nombre_origen === '' || nombre_origen.length > 150) ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                </div>
                <div>
                    <label for="descripcion_origen" class="block text-sm font-medium">Descripción</label>
                    <textarea id="descripcion_origen" x-model="descripcion_origen" rows="3" maxlength="255"
                        @input="formOrigen = formOrigen || { _touched: {} }; formOrigen._touched.descripcion_origen = true"
                        @blur="formOrigen._touched.descripcion_origen = true"
                        :class="formOrigen && formOrigen._touched && formOrigen._touched.descripcion_origen && descripcion_origen.length > 255 ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></textarea>
                    <small :class="formOrigen && formOrigen._touched && formOrigen._touched.descripcion_origen && descripcion_origen.length > 255 ? 'text-red-500' : ''">Máximo 255 caracteres.</small>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="activo" x-model="activo" class="rounded border-gray-400">
                    <label for="activo" class="text-sm">Activo</label>
                </div>
            </div>
        </x-admin.form-modal>
        @endperm

        @perm(['Catálogo','Origen Kardex','Orígenes Kardex','Origenes Kardex','Origen de Kardex'], 'actualizacion')
        <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpen" title="Editar Origen"
            itemToEdit="itemToEdit" formId="formEditOrigen" maxWidth="max-w-md">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium">Nombre</label>
                    <input type="text" x-model="edit_nombre_origen" required maxlength="150"
                        @input="formEditOrigen = formEditOrigen || { _touched: {} }; formEditOrigen._touched.nombre_origen = true"
                        @blur="formEditOrigen._touched.nombre_origen = true"
                        :class="formEditOrigen && formEditOrigen._touched && formEditOrigen._touched.nombre_origen && (edit_nombre_origen === '' || edit_nombre_origen.length > 150) ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                    <small :class="formEditOrigen && formEditOrigen._touched && formEditOrigen._touched.nombre_origen && (edit_nombre_origen === '' || edit_nombre_origen.length > 150) ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium">Descripción</label>
                    <textarea x-model="edit_descripcion_origen" rows="3" maxlength="255"
                        @input="formEditOrigen = formEditOrigen || { _touched: {} }; formEditOrigen._touched.descripcion_origen = true"
                        @blur="formEditOrigen._touched.descripcion_origen = true"
                        :class="formEditOrigen && formEditOrigen._touched && formEditOrigen._touched.descripcion_origen && edit_descripcion_origen.length > 255 ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></textarea>
                    <small :class="formEditOrigen && formEditOrigen._touched && formEditOrigen._touched.descripcion_origen && edit_descripcion_origen.length > 255 ? 'text-red-500' : ''">Máximo 255 caracteres.</small>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" x-model="edit_activo" class="rounded border-gray-400">
                    <label class="text-sm">Activo</label>
                </div>
            </div>
        </x-admin.edit-modal>
        @endperm

        @perm(['Catálogo','Origen Kardex','Orígenes Kardex','Origenes Kardex','Origen de Kardex'], 'eliminacion')
        <x-admin.confirmation-modal class="nunito-regular" modalName="isDeleteModalOpen" itemToDelete="itemToDelete"
            itemNameProperty="nombre_origen" message="¿Estás seguro de que quieres eliminar este origen?" />
        @endperm
    </div>
</div>