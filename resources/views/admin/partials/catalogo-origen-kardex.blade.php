<div x-data="{
    isModalOpen: false,
    isEditModalOpen: false,
    isDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    origenes: [],
    loading: false,
    nombre_origen: '',
    descripcion_origen: '',
    activo: true,
    // Campos locales para edición para evitar acceder a itemToEdit cuando es null
    edit_nombre_origen: '',
    edit_descripcion_origen: '',
    edit_activo: true,
    filtroOrigen: '',
    ordenarPor: 'nombre',
    async fetchItems(){ await window.origenKardexApiHandlers.fetchOrigenes(this); },
    async submit(){ await window.origenKardexApiHandlers.submitOrigen(this); },
    async update(){ await window.origenKardexApiHandlers.updateOrigen(this); },
    async remove(){ await window.origenKardexApiHandlers.deleteOrigen(this); },
    handleModalSubmit(e){
        if(e.detail.formId === 'formOrigen') this.submit();
        if(e.detail.formId === 'formEditOrigen') {
            // Sincronizar campos locales al objeto antes de enviar
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
    $watch('filtroOrigen', () => fetchItems());
    $watch('ordenarPor', () => fetchItems());
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
            <button @click="isModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm">
                Nuevo Origen
            </button>
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
                    <template x-for="item in origenes" :key="item.id_origen_pk">
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
                                <a href="#"
                                    @click.prevent="itemToEdit = {...item}; edit_nombre_origen = item.nombre_origen || ''; edit_descripcion_origen = item.descripcion_origen || ''; edit_activo = !!item.activo; isEditModalOpen=true"
                                    class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#"
                                    @click.prevent="isDeleteModalOpen=true; itemToDelete = {id_origen_pk: item.id_origen_pk, nombre_origen: item.nombre_origen}"
                                    class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
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
                <template x-for="item in origenes" :key="item.id_origen_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-3">
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
                            <button
                                @click.prevent="itemToEdit = {...item}; edit_nombre_origen = item.nombre_origen || ''; edit_descripcion_origen = item.descripcion_origen || ''; edit_activo = !!item.activo; isEditModalOpen = true"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button
                                @click.prevent="isDeleteModalOpen = true; itemToDelete = {id_origen_pk: item.id_origen_pk, nombre_origen: item.nombre_origen}"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </x-slot>
    </x-responsive-table>

    <!-- Modales -->
    <div>
        <x-admin.form-modal class="nunito-bold" modalName="isModalOpen" title="Nuevo Origen" submitLabel="Guardar"
            formId="formOrigen" maxWidth="max-w-md">
            <div class="space-y-4">
                <div>
                    <label for="nombre_origen" class="block text-sm font-medium">Nombre</label>
                    <input type="text" id="nombre_origen" x-model="nombre_origen"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                </div>
                <div>
                    <label for="descripcion_origen" class="block text-sm font-medium">Descripción</label>
                    <textarea id="descripcion_origen" x-model="descripcion_origen" rows="3"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="activo" x-model="activo" class="rounded border-gray-400">
                    <label for="activo" class="text-sm">Activo</label>
                </div>
            </div>
        </x-admin.form-modal>

        <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpen" title="Editar Origen"
            itemToEdit="itemToEdit" formId="formEditOrigen" maxWidth="max-w-md">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium">Nombre</label>
                    <input type="text" x-model="edit_nombre_origen"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium">Descripción</label>
                    <textarea x-model="edit_descripcion_origen" rows="3"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" x-model="edit_activo" class="rounded border-gray-400">
                    <label class="text-sm">Activo</label>
                </div>
            </div>
        </x-admin.edit-modal>

        <x-admin.confirmation-modal class="nunito-regular" modalName="isDeleteModalOpen" itemToDelete="itemToDelete"
            itemNameProperty="nombre_origen" message="¿Estás seguro de que quieres eliminar este origen?" />
    </div>
</div>