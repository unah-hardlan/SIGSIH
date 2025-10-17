<div x-data="{
    isModalOpen: false,
    isEditModalOpen: false,
    isDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    items: [],
    loading: false,
    tipo_mantenimiento: '',
    descripcion_mantenimiento: '',
    // Campos locales para edición (evitan acceder a itemToEdit cuando es null)
    edit_tipo_mantenimiento: '',
    edit_descripcion_mantenimiento: '',
    filtroTipoMantenimiento: '',
    ordenarPor: 'nombre',
    async fetchItems(){ await window.tipoMantenimientoApiHandlers.fetchTipos(this); },
    async submit(){ await window.tipoMantenimientoApiHandlers.submitTipo(this); },
    async update(){ await window.tipoMantenimientoApiHandlers.updateTipo(this); },
    async remove(){ await window.tipoMantenimientoApiHandlers.deleteTipo(this); },
    handleModalSubmit(e){
        if(e.detail.formId === 'formTipoMantenimiento') this.submit();
        if(e.detail.formId === 'formEditTipoMantenimiento') this.update();
    },
    handleDelete(){ this.remove(); }
}" x-init="
    fetchItems();
    $watch('filtroTipoMantenimiento', () => fetchItems());
    $watch('ordenarPor', () => fetchItems());
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
            <button @click="isModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm">
                Nuevo tipo de mantenimiento
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
                    <template x-for="(item, index) in items" :key="item.id_tipo_mantenimiento_pk">
                        <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular" :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === items.length - 1 }">
                            <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="item.tipo_mantenimiento"></td>
                            <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="item.descripcion_mantenimiento"></td>
                            <td class="py-2 px-4 flex gap-2" :class="{ 'last:rounded-br-lg': index === items.length - 1 }">
                                <a href="#"
                                    @click.prevent="itemToEdit = {...item}; edit_tipo_mantenimiento = item.tipo_mantenimiento || ''; edit_descripcion_mantenimiento = item.descripcion_mantenimiento || ''; isEditModalOpen=true"
                                    class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#"
                                    @click.prevent="isDeleteModalOpen=true; itemToDelete = {id: item.id_tipo_mantenimiento_pk, nombre: item.tipo_mantenimiento}"
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
                <template x-if="!loading && items.length === 0">
                    <div class="p-4 text-center text-gray-500">Sin resultados</div>
                </template>
                <template x-for="item in items" :key="item.id_tipo_mantenimiento_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-3">
                        <div class="flex justify-between items-start">
                            <h3 class="font-semibold text-gray-900 dark:text-white" x-text="item.tipo_mantenimiento">
                            </h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300" x-text="item.descripcion_mantenimiento"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t dark:border-gray-700">
                            <button
                                @click.prevent="itemToEdit = {...item}; edit_tipo_mantenimiento = item.tipo_mantenimiento || ''; edit_descripcion_mantenimiento = item.descripcion_mantenimiento || ''; isEditModalOpen = true"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button
                                @click.prevent="isDeleteModalOpen = true; itemToDelete = {id: item.id_tipo_mantenimiento_pk, nombre: item.tipo_mantenimiento}"
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
        <x-admin.form-modal class="nunito-bold" modalName="isModalOpen" title="Nuevo Tipo de Mantenimiento"
            submitLabel="Guardar" formId="formTipoMantenimiento" maxWidth="max-w-md">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="tipo_mantenimiento" class="block text-sm font-medium">Nombre</label>
                    <input type="text" id="tipo_mantenimiento" x-model="tipo_mantenimiento"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                </div>
                <div class="md:col-span-2">
                    <label for="descripcion_mantenimiento" class="block text-sm font-medium">Descripción</label>
                    <textarea id="descripcion_mantenimiento" x-model="descripcion_mantenimiento" rows="3"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></textarea>
                </div>
            </div>
        </x-admin.form-modal>

        <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpen" title="Editar Tipo de Mantenimiento"
            itemToEdit="itemToEdit" formId="formEditTipoMantenimiento" maxWidth="max-w-md">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Nombre</label>
                    <input type="text" x-model="edit_tipo_mantenimiento"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Descripción</label>
                    <textarea x-model="edit_descripcion_mantenimiento" rows="3"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></textarea>
                </div>
            </div>
        </x-admin.edit-modal>

        <x-admin.confirmation-modal class="nunito-regular" modalName="isDeleteModalOpen" itemToDelete="itemToDelete"
            itemNameProperty="nombre" message="¿Estás seguro de que quieres eliminar este tipo de mantenimiento?" />
    </div>
</div>