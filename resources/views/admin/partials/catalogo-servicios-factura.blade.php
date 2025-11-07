<div x-data="serviciosCrud" @keydown.escape.window="
    isServicioModalOpen = false;
    isEditServicioModalOpen = false;
    isDeleteServicioModalOpen = false;
" @modal-submit.window="handleModalSubmit($event)" @confirm-delete.window="handleDelete()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Servicios Factura</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
            'searchModel' => 'filtroServicio',
            'ordenarOptions' => [
            'nombre_servicio' => 'Nombre',
            'tarifa' => 'Tarifa'
            ]
            ])
        </x-slot>

        <x-slot name="actions">
            @perm(['Catálogo','Servicios Factura','Servicio Factura','Servicios de Factura'], 'insercion')
            <button @click="formServicio = { _touched: {} }; nombre_servicio = ''; tarifa = ''; isServicioModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo Servicio
            </button>
            @else
            <button type="button" disabled title="Sin permiso para crear"
                class="bg-gray-400 text-white px-4 py-2 rounded-lg nunito-regular opacity-60 cursor-not-allowed whitespace-nowrap text-sm">
                Nuevo Servicio
            </button>
            @endperm
        </x-slot>

        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Nombre Servicio</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Tarifa</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loadingServicios">
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando servicios...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingServicios && filteredServicios.length === 0">
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500 nunito-regular">
                                No hay servicios registrados
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingServicios && filteredServicios.length > 0">
                        <template x-for="(servicio, index) in paginatedServicios()" :key="servicio.id_servicio_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === paginatedServicios().length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="servicio.nombre_servicio"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="'L ' + Number(servicio.tarifa ?? 0).toFixed(2)"></td>
                                <td class="py-2 px-4 flex gap-2"
                                    :class="{ 'last:rounded-br-lg': index === paginatedServicios().length - 1 }">
                                    @perm(['Catálogo','Servicios Factura','Servicio Factura','Servicios de Factura'], 'actualizacion')
                                    <a href="#"
                                        @click.prevent="formEditServicio = { _touched: {} }; isEditServicioModalOpen = true; itemToEdit = {id_servicio_pk: servicio.id_servicio_pk, nombre_servicio: servicio.nombre_servicio, tarifa: servicio.tarifa}"
                                        class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    @else
                                    <span class="text-gray-400 cursor-not-allowed" title="Sin permiso para editar"><i class="fas fa-edit"></i></span>
                                    @endperm
                                    @perm(['Catálogo','Servicios Factura','Servicio Factura','Servicios de Factura'], 'eliminacion')
                                    <a href="#"
                                        @click.prevent="isDeleteServicioModalOpen = true; itemToDelete = {id_servicio_pk: servicio.id_servicio_pk, nombre: servicio.nombre_servicio}"
                                        class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                    @else
                                    <span class="text-gray-400 cursor-not-allowed" title="Sin permiso para eliminar"><i class="fas fa-trash"></i></span>
                                    @endperm
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loadingServicios">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando servicios...
                </div>
            </template>
            <template x-if="!loadingServicios && filteredServicios.length === 0">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    No hay servicios registrados
                </div>
            </template>
            <template x-if="!loadingServicios && filteredServicios.length > 0">
                <template x-for="servicio in paginatedServicios()" :key="servicio.id_servicio_pk">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold"
                                x-text="servicio.nombre_servicio"></h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular">
                            <span class="font-medium">Tarifa:</span>
                            <span x-text="'L ' + Number(servicio.tarifa ?? 0).toFixed(2)"></span>
                        </p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            @perm(['Catálogo','Servicios Factura','Servicio Factura','Servicios de Factura'], 'actualizacion')
                            <button
                                @click.prevent="formEditServicio = { _touched: {} }; isEditServicioModalOpen = true; itemToEdit = {id_servicio_pk: servicio.id_servicio_pk, nombre_servicio: servicio.nombre_servicio, tarifa: servicio.tarifa}"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @else
                            <button type="button" disabled title="Sin permiso para editar"
                                class="px-3 py-1 text-xs bg-gray-400 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @endperm
                            @perm(['Catálogo','Servicios Factura','Servicio Factura','Servicios de Factura'], 'eliminacion')
                            <button
                                @click.prevent="isDeleteServicioModalOpen = true; itemToDelete = {id_servicio_pk: servicio.id_servicio_pk, nombre: servicio.nombre_servicio}"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @else
                            <button type="button" disabled title="Sin permiso para eliminar"
                                class="px-3 py-1 text-xs bg-gray-400 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @endperm
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <!-- Paginación del lado del cliente -->
    <x-pagination />

    <!-- Modales -->
    <div>
        <!-- Modal Nuevo Servicio -->
        @perm(['Catálogo','Servicios Factura','Servicio Factura','Servicios de Factura'], 'insercion')
        <x-admin.form-modal class="nunito-bold" modalName="isServicioModalOpen" title="Nuevo Servicio"
            submitLabel="Guardar Servicio" formId="formServicio" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre
                        Servicio</label>
                    <input type="text" id="nombre_servicio" x-model="nombre_servicio" required maxlength="150"
                        @input="formServicio = formServicio || { _touched: {} }; formServicio._touched.nombre_servicio = true"
                        @blur="formServicio = formServicio || { _touched: {} }; formServicio._touched.nombre_servicio = true"
                        :class="formServicio && formServicio._touched && formServicio._touched.nombre_servicio && (nombre_servicio === '' || (nombre_servicio && nombre_servicio.length > 150)) ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <small class="block text-xs nunito-regular mt-1" :class="formServicio && formServicio._touched && formServicio._touched.nombre_servicio && (nombre_servicio === '' || (nombre_servicio && nombre_servicio.length > 150)) ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                </div>
                <div>
                    <label for="tarifa" class="block text-sm font-medium text-gray-700 nunito-bold">Tarifa</label>
                    <input type="number" id="tarifa" x-model="tarifa" step="0.01" min="0" required
                        @input="formServicio = formServicio || { _touched: {} }; formServicio._touched.tarifa = true"
                        @blur="formServicio = formServicio || { _touched: {} }; formServicio._touched.tarifa = true"
                        :class="formServicio && formServicio._touched && formServicio._touched.tarifa && (tarifa === '' || tarifa < 0) ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <small class="block text-xs nunito-regular mt-1" :class="formServicio && formServicio._touched && formServicio._touched.tarifa && (tarifa === '' || tarifa < 0) ? 'text-red-500' : ''">Requerido. Mínimo 0.</small>
                </div>
            </div>
        </x-admin.form-modal>
        @endperm

        <!-- Modal Editar Servicio -->
        @perm(['Catálogo','Servicios Factura','Servicio Factura','Servicios de Factura'], 'actualizacion')
        <x-admin.edit-modal class="nunito-bold" modalName="isEditServicioModalOpen" title="Editar Servicio"
            itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditServicio">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_nombre_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre
                        Servicio</label>
                    <input type="text" id="edit_nombre_servicio" x-model="itemToEdit.nombre_servicio" required maxlength="150"
                        @input="formEditServicio = formEditServicio || { _touched: {} }; formEditServicio._touched.nombre_servicio = true"
                        @blur="formEditServicio = formEditServicio || { _touched: {} }; formEditServicio._touched.nombre_servicio = true"
                        :class="formEditServicio && formEditServicio._touched && formEditServicio._touched.nombre_servicio && (itemToEdit.nombre_servicio === '' || (itemToEdit.nombre_servicio && itemToEdit.nombre_servicio.length > 150)) ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <small class="block text-xs nunito-regular mt-1" :class="formEditServicio && formEditServicio._touched && formEditServicio._touched.nombre_servicio && (itemToEdit.nombre_servicio === '' || (itemToEdit.nombre_servicio && itemToEdit.nombre_servicio.length > 150)) ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                </div>
                <div>
                    <label for="edit_tarifa" class="block text-sm font-medium text-gray-700 nunito-bold">Tarifa</label>
                    <input type="number" id="edit_tarifa" x-model="itemToEdit.tarifa" step="0.01" min="0" required
                        @input="formEditServicio = formEditServicio || { _touched: {} }; formEditServicio._touched.tarifa = true"
                        @blur="formEditServicio = formEditServicio || { _touched: {} }; formEditServicio._touched.tarifa = true"
                        :class="formEditServicio && formEditServicio._touched && formEditServicio._touched.tarifa && (itemToEdit.tarifa === '' || itemToEdit.tarifa < 0) ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <small class="block text-xs nunito-regular mt-1" :class="formEditServicio && formEditServicio._touched && formEditServicio._touched.tarifa && (itemToEdit.tarifa === '' || itemToEdit.tarifa < 0) ? 'text-red-500' : ''">Requerido. Mínimo 0.</small>
                </div>
            </div>
        </x-admin.edit-modal>
        @endperm

        <!-- Modal Confirmar Eliminación -->
        @perm(['Catálogo','Servicios Factura','Servicio Factura','Servicios de Factura'], 'eliminacion')
        <x-admin.confirmation-modal class="nunito-regular" modalName="isDeleteServicioModalOpen"
            itemToDelete="itemToDelete" message="¿Estás seguro de que quieres eliminar este servicio?" />
        @endperm
    </div>
</div>