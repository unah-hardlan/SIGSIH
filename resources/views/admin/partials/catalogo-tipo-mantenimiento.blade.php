<div x-data="{
    isModalOpen: false,
    isEditModalOpen: false,
    isDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    items: [],
    loading: false,

    numbersItems: [],
    currentPageItems: 1,
    perPageItems: 10,

    tipo_mantenimiento: '',
    descripcion_mantenimiento: '',
    edit_tipo_mantenimiento: '',
    edit_descripcion_mantenimiento: '',
    formTipoMantenimiento: { _touched: {} },
    formEditTipoMantenimiento: { _touched: {} },
    filtroTipoMantenimiento: '',
    ordenarPor: 'nombre',

    paginatedItems() {
        return this.items.slice(
            (this.currentPageItems - 1) * this.perPageItems, 
            this.currentPageItems * this.perPageItems
        );
    },
    totalPagesItems() {
        return Math.ceil(this.items.length / this.perPageItems);
    },
    nextPageItems() {
        if (this.currentPageItems < this.totalPagesItems()) {
            this.currentPageItems++;
        }
    },
    prevPageItems() {
        if (this.currentPageItems > 1) {
            this.currentPageItems--;
        }
    },

    // 3️⃣ Sincronizar Alias en cada operación CRUD
    async fetchItems(){ 
        await window.tipoMantenimientoApiHandlers.fetchTipos(this); 
        this.numbersItems = this.items; 
    },
    async submit(){ 
        await window.tipoMantenimientoApiHandlers.submitTipo(this);
        this.fetchItems(); 
    },
    async update(){ 
        await window.tipoMantenimientoApiHandlers.updateTipo(this);
        this.fetchItems(); 
    },
    async remove(){ 
        await window.tipoMantenimientoApiHandlers.deleteTipo(this);
        this.fetchItems(); 
    },
    handleModalSubmit(e){
        if(e.detail.formId === 'formTipoMantenimiento') this.submit();
        if(e.detail.formId === 'formEditTipoMantenimiento') this.update();
    },
    handleDelete(){ this.remove(); }
}" x-init="
    fetchItems();
    $watch('filtroTipoMantenimiento', () => { fetchItems(); currentPageItems = 1; });
    $watch('ordenarPor', () => { fetchItems(); currentPageItems = 1; });
" @keydown.escape.window="isModalOpen=false; isEditModalOpen=false; isDeleteModalOpen=false;"
    @modal-submit.window="handleModalSubmit($event)" @confirm-delete.window="handleDelete()">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Tipo de Mantenimiento</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
            'searchModel' => 'filtroTipoMantenimiento',
            'ordenarOptions' => [
            'nombre' => 'Nombre',
            'id' => 'ID'
            ]
            ])
        </x-slot>

        <x-slot name="actions">
            @perm(['Tipo de Mantenimiento','Tipos de Mantenimiento','Tipo de mantenimiento','Tipos de mantenimiento'],
            'insercion')
            <button
                @click="formTipoMantenimiento = { _touched: {} }; tipo_mantenimiento = ''; descripcion_mantenimiento = ''; isModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm">
                Nuevo tipo de mantenimiento
            </button>
            @else
            <button disabled title="Sin permiso para crear"
                class="bg-green-600/60 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm opacity-60 cursor-not-allowed">
                Nuevo tipo de mantenimiento
            </button>
            @endperm
        </x-slot>

        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Nombre</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Descripción</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loading">
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500"><i
                                    class="fas fa-spinner fa-spin mr-2"></i>Cargando...</td>
                        </tr>
                    </template>
                    <template x-if="!loading && items.length === 0">
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500">Sin resultados</td>
                        </tr>
                    </template>
                    <template x-for="(item, index) in paginatedItems()" :key="item.id_tipo_mantenimiento_pk">
                        <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                            :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === paginatedItems().length - 1 }">
                            <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular"
                                x-text="item.tipo_mantenimiento"></td>
                            <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular"
                                x-text="item.descripcion_mantenimiento"></td>
                            <td class="py-2 px-4 flex gap-2"
                                :class="{ 'last:rounded-br-lg': index === paginatedItems().length - 1 }">
                                @perm(['Tipo de Mantenimiento','Tipos de Mantenimiento','Tipo de mantenimiento','Tipos
                                de mantenimiento'], 'actualizacion')
                                <a href="#"
                                    @click.prevent="itemToEdit = {...item}; edit_tipo_mantenimiento = item.tipo_mantenimiento || ''; edit_descripcion_mantenimiento = item.descripcion_mantenimiento || ''; formEditTipoMantenimiento = { _touched: {} }; isEditModalOpen=true"
                                    class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                @else
                                <span title="Sin permiso para editar" class="text-blue-300 cursor-not-allowed"><i
                                        class="fas fa-edit"></i></span>
                                @endperm
                                @perm(['Tipo de Mantenimiento','Tipos de Mantenimiento','Tipo de mantenimiento','Tipos
                                de mantenimiento'], 'eliminacion')
                                <a href="#"
                                    @click.prevent="isDeleteModalOpen=true; itemToDelete = {id: item.id_tipo_mantenimiento_pk, nombre: item.tipo_mantenimiento}"
                                    class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                @else
                                <span title="Sin permiso para eliminar" class="text-red-300 cursor-not-allowed"><i
                                        class="fas fa-trash"></i></span>
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
                <template x-if="!loading && items.length === 0">
                    <div class="p-4 text-center text-gray-500">Sin resultados</div>
                </template>
                <template x-for="item in paginatedItems()" :key="item.id_tipo_mantenimiento_pk">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-3 border border-black dark:border-gray-800">
                        <div class="flex justify-between items-start">
                            <h3 class="font-semibold text-gray-900 dark:text-white" x-text="item.tipo_mantenimiento">
                            </h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300" x-text="item.descripcion_mantenimiento"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t dark:border-gray-700">
                            @perm(['Tipo de Mantenimiento','Tipos de Mantenimiento','Tipo de mantenimiento','Tipos de
                            mantenimiento'], 'actualizacion')
                            <button
                                @click.prevent="itemToEdit = {...item}; edit_tipo_mantenimiento = item.tipo_mantenimiento || ''; edit_descripcion_mantenimiento = item.descripcion_mantenimiento || ''; formEditTipoMantenimiento = { _touched: {} }; isEditModalOpen = true"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @else
                            <button type="button" disabled title="Sin permiso para editar"
                                class="px-3 py-1 text-xs bg-blue-600/60 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @endperm
                            @perm(['Tipo de Mantenimiento','Tipos de Mantenimiento','Tipo de mantenimiento','Tipos de
                            mantenimiento'], 'eliminacion')
                            <button
                                @click.prevent="isDeleteModalOpen = true; itemToDelete = {id: item.id_tipo_mantenimiento_pk, nombre: item.tipo_mantenimiento}"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @else
                            <button type="button" disabled title="Sin permiso para eliminar"
                                class="px-3 py-1 text-xs bg-red-600/60 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @endperm
                        </div>
                    </div>
                </template>
            </div>
        </x-slot>
    </x-responsive-table>

    <div x-show="items.length > perPageItems"
        class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
        <div class="mb-2">
            <span
                class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                Mostrando
                <strong class="font-medium mx-1 text-gray-900 dark:text-white"
                    x-text="(currentPageItems - 1) * perPageItems + 1"></strong>
                a
                <strong class="font-medium mx-1 text-gray-900 dark:text-white"
                    x-text="Math.min(currentPageItems * perPageItems, items.length)"></strong>
                de
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="items.length"></strong>
                resultados
            </span>
        </div>

        <div
            class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
            <button @click="prevPageItems()" :disabled="currentPageItems === 1"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span>Anterior</span>
            </button>

            <div class="flex items-center gap-1">
                <template
                    x-for="page in Array.from({length: totalPagesItems()}, (_, i) => i + 1).slice(Math.max(0, currentPageItems - 3), currentPageItems + 2)"
                    :key="page">
                    <button @click="currentPageItems = page"
                        class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-blue-900 hover:text-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                        :class="page === currentPageItems ? 'bg-blue-600 text-white' : ''">
                        <span x-text="page"></span>
                    </button>
                </template>
            </div>

            <button @click="nextPageItems()" :disabled="currentPageItems === totalPagesItems()"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                <span>Siguiente</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <div>
        @perm(['Tipo de Mantenimiento','Tipos de Mantenimiento','Tipo de mantenimiento','Tipos de mantenimiento'],
        'insercion')
        <x-admin.form-modal class="nunito-bold" modalName="isModalOpen" title="Nuevo Tipo de Mantenimiento"
            submitLabel="Guardar" formId="formTipoMantenimiento" maxWidth="max-w-md">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="tipo_mantenimiento" class="block text-sm font-medium">Nombre</label>
                    <input type="text" id="tipo_mantenimiento" x-model="tipo_mantenimiento" required maxlength="150"
                        @input="formTipoMantenimiento = formTipoMantenimiento || { _touched: {} }; formTipoMantenimiento._touched.tipo_mantenimiento = true"
                        @blur="formTipoMantenimiento._touched.tipo_mantenimiento = true"
                        :class="formTipoMantenimiento && formTipoMantenimiento._touched && formTipoMantenimiento._touched.tipo_mantenimiento && (tipo_mantenimiento === '' || tipo_mantenimiento.length > 150) ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                    <small
                        :class="formTipoMantenimiento && formTipoMantenimiento._touched && formTipoMantenimiento._touched.tipo_mantenimiento && (tipo_mantenimiento === '' || tipo_mantenimiento.length > 150) ? 'text-red-500' : ''">Requerido.
                        Máximo 150 caracteres.</small>
                </div>
                <div class="md:col-span-2">
                    <label for="descripcion_mantenimiento" class="block text-sm font-medium">Descripción</label>
                    <textarea id="descripcion_mantenimiento" x-model="descripcion_mantenimiento" rows="3"
                        maxlength="255"
                        @input="formTipoMantenimiento = formTipoMantenimiento || { _touched: {} }; formTipoMantenimiento._touched.descripcion_mantenimiento = true"
                        @blur="formTipoMantenimiento._touched.descripcion_mantenimiento = true"
                        :class="formTipoMantenimiento && formTipoMantenimiento._touched && formTipoMantenimiento._touched.descripcion_mantenimiento && descripcion_mantenimiento.length > 255 ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></textarea>
                    <small
                        :class="formTipoMantenimiento && formTipoMantenimiento._touched && formTipoMantenimiento._touched.descripcion_mantenimiento && descripcion_mantenimiento.length > 255 ? 'text-red-500' : ''">Máximo
                        255 caracteres.</small>
                </div>
            </div>
        </x-admin.form-modal>
        @endperm

        @perm(['Tipo de Mantenimiento','Tipos de Mantenimiento','Tipo de mantenimiento','Tipos de mantenimiento'],
        'actualizacion')
        <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpen" title="Editar Tipo de Mantenimiento"
            itemToEdit="itemToEdit" formId="formEditTipoMantenimiento" maxWidth="max-w-md">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Nombre</label>
                    <input type="text" x-model="edit_tipo_mantenimiento" required maxlength="150"
                        @input="formEditTipoMantenimiento = formEditTipoMantenimiento || { _touched: {} }; formEditTipoMantenimiento._touched.edit_tipo_mantenimiento = true"
                        @blur="formEditTipoMantenimiento._touched.edit_tipo_mantenimiento = true"
                        :class="formEditTipoMantenimiento && formEditTipoMantenimiento._touched && formEditTipoMantenimiento._touched.edit_tipo_mantenimiento && (edit_tipo_mantenimiento === '' || edit_tipo_mantenimiento.length > 150) ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                    <small
                        :class="formEditTipoMantenimiento && formEditTipoMantenimiento._touched && formEditTipoMantenimiento._touched.edit_tipo_mantenimiento && (edit_tipo_mantenimiento === '' || edit_tipo_mantenimiento.length > 150) ? 'text-red-500' : ''">Requerido.
                        Máximo 150 caracteres.</small>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Descripción</label>
                    <textarea x-model="edit_descripcion_mantenimiento" rows="3" maxlength="255"
                        @input="formEditTipoMantenimiento = formEditTipoMantenimiento || { _touched: {} }; formEditTipoMantenimiento._touched.edit_descripcion_mantenimiento = true"
                        @blur="formEditTipoMantenimiento._touched.edit_descripcion_mantenimiento = true"
                        :class="formEditTipoMantenimiento && formEditTipoMantenimiento._touched && formEditTipoMantenimiento._touched.edit_descripcion_mantenimiento && edit_descripcion_mantenimiento.length > 255 ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></textarea>
                    <small
                        :class="formEditTipoMantenimiento && formEditTipoMantenimiento._touched && formEditTipoMantenimiento._touched.edit_descripcion_mantenimiento && edit_descripcion_mantenimiento.length > 255 ? 'text-red-500' : ''">Máximo
                        255 caracteres.</small>
                </div>
            </div>
        </x-admin.edit-modal>
        @endperm

        @perm(['Tipo de Mantenimiento','Tipos de Mantenimiento','Tipo de mantenimiento','Tipos de mantenimiento'],
        'eliminacion')
        <x-admin.confirmation-modal class="nunito-regular" modalName="isDeleteModalOpen" itemToDelete="itemToDelete"
            itemNameProperty="nombre" message="¿Estás seguro de que quieres eliminar este tipo de mantenimiento?" />
        @endperm
    </div>
</div>