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
    // Filtros/ordenamiento
    filtroOrigen: '',
    ordenarPor: 'nombre',
    async fetchItems(){ await window.origenKardexApiHandlers.fetchOrigenes(this); },
    async submit(){ await window.origenKardexApiHandlers.submitOrigen(this); },
    async update(){ await window.origenKardexApiHandlers.updateOrigen(this); },
    async remove(){ await window.origenKardexApiHandlers.deleteOrigen(this); },
    handleModalSubmit(e){
        if(e.detail.formId === 'formOrigen') this.submit();
        if(e.detail.formId === 'formEditOrigen') this.update();
    },
    handleDelete(){ this.remove(); }
}"
x-init="
    fetchItems();
    $watch('filtroOrigen', () => fetchItems());
    $watch('ordenarPor', () => fetchItems());
"
@keydown.escape.window="isModalOpen=false; isEditModalOpen=false; isDeleteModalOpen=false;"
@modal-submit.window="handleModalSubmit($event)"
@confirm-delete="handleDelete()">

    <x-admin.tabla-mobile titulo="Origen Kardex" class="nunito-bold bg-white dark:bg-gray-900">
        <x-slot name="filtros">
            @include('partials.filtros-generales', [
                'searchModel' => 'filtroOrigen',
                'ordenarOptions' => [
                    'nombre' => 'Nombre',
                    'activo' => 'Activo',
                    'id' => 'ID'
                ]
            ])
        </x-slot>
        <x-slot name="boton">
            <button @click="isModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo Origen
            </button>
        </x-slot>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left dark:text-gray-300">ID</th>
                        <th class="py-2 px-4 text-left dark:text-gray-300">Nombre</th>
                        <th class="py-2 px-4 text-left dark:text-gray-300">Descripción</th>
                        <th class="py-2 px-4 text-left dark:text-gray-300">Activo</th>
                        <th class="py-2 px-4 text-left dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in origenes" :key="item.id_origen_pk">
                        <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                            <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="item.id_origen_pk"></td>
                            <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="item.nombre_origen"></td>
                            <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="item.descripcion_origen"></td>
                            <td class="py-2 px-4 text-gray-900 dark:text-gray-200">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold" :class="item.activo ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-rose-50 text-rose-700 ring-1 ring-rose-200'">
                                    <i :class="item.activo ? 'fas fa-check' : 'fas fa-ban'"></i>
                                    <span x-text="item.activo ? 'Activo' : 'Inactivo'"></span>
                                </span>
                            </td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click.prevent="isEditModalOpen=true; itemToEdit = {...item}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#" @click.prevent="isDeleteModalOpen=true; itemToDelete = {id_origen_pk: item.id_origen_pk, nombre: item.nombre_origen}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <x-slot name="mobileTemplate">
            <div class="space-y-4">
                <template x-for="item in origenes" :key="item.id_origen_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="item.nombre_origen"></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular" x-text="'ID: ' + item.id_origen_pk"></p>
                            </div>
                        </div>
                        <div class="space-y-1 text-sm">
                            <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Descripción:</span> <span class="text-gray-900 dark:text-gray-200 nunito-regular" x-text="item.descripcion_origen"></span></div>
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Estado:</span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold" :class="item.activo ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-rose-50 text-rose-700 ring-1 ring-rose-200'">
                                    <i :class="item.activo ? 'fas fa-check' : 'fas fa-ban'"></i>
                                    <span x-text="item.activo ? 'Activo' : 'Inactivo'"></span>
                                </span>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="isEditModalOpen = true; itemToEdit = {...item}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="isDeleteModalOpen = true; itemToDelete = {id_origen_pk: item.id_origen_pk, nombre: item.nombre_origen}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </x-slot>
    </x-admin.tabla-mobile>

    <!-- Modales -->
    <div>
        <x-admin.form-modal class="nunito-bold" modalName="isModalOpen" title="Nuevo Origen"
            submitLabel="Guardar Origen" formId="formOrigen" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre_origen" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="nombre_origen" x-model="nombre_origen" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div class="col-span-2">
                    <label for="descripcion_origen" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="descripcion_origen" x-model="descripcion_origen" rows="2" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                </div>
                <div class="col-span-2 flex items-center gap-2">
                    <input type="checkbox" id="activo" x-model="activo" class="rounded border-gray-400">
                    <label for="activo" class="text-sm text-gray-700 nunito-bold">Activo</label>
                </div>
            </div>
        </x-admin.form-modal>

        <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpen" title="Editar Origen" itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditOrigen">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" x-model="itemToEdit.nombre_origen" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea x-model="itemToEdit.descripcion_origen" rows="2" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                </div>
                <div class="col-span-2 flex items-center gap-2">
                    <input type="checkbox" x-model="itemToEdit.activo" class="rounded border-gray-400">
                    <label class="text-sm text-gray-700 nunito-bold">Activo</label>
                </div>
            </div>
        </x-admin.edit-modal>

        <x-admin.confirmation-modal class="nunito-regular" modalName="isDeleteModalOpen" itemToDelete="itemToDelete" message="¿Estás seguro de que quieres eliminar este origen?" />
    </div>
</div>
