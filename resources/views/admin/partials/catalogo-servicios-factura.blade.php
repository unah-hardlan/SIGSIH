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
            <button @click="isServicioModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo Servicio
            </button>
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
                        <template x-for="(servicio, index) in filteredServicios" :key="servicio.id_servicio_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === filteredServicios.length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="servicio.nombre_servicio"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="'L ' + Number(servicio.tarifa ?? 0).toFixed(2)"></td>
                                <td class="py-2 px-4 flex gap-2"
                                    :class="{ 'last:rounded-br-lg': index === filteredServicios.length - 1 }">
                                    <a href="#"
                                        @click.prevent="isEditServicioModalOpen = true; itemToEdit = {id_servicio_pk: servicio.id_servicio_pk, nombre_servicio: servicio.nombre_servicio, tarifa: servicio.tarifa}"
                                        class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    <a href="#"
                                        @click.prevent="isDeleteServicioModalOpen = true; itemToDelete = {id_servicio_pk: servicio.id_servicio_pk, nombre: servicio.nombre_servicio}"
                                        class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
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
                <template x-for="servicio in filteredServicios" :key="servicio.id_servicio_pk">
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
                            <button
                                @click.prevent="isEditServicioModalOpen = true; itemToEdit = {id_servicio_pk: servicio.id_servicio_pk, nombre_servicio: servicio.nombre_servicio, tarifa: servicio.tarifa}"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button
                                @click.prevent="isDeleteServicioModalOpen = true; itemToDelete = {id_servicio_pk: servicio.id_servicio_pk, nombre: servicio.nombre_servicio}"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <!-- Modales -->
    <div>
        <!-- Modal Nuevo Servicio -->
        <x-admin.form-modal class="nunito-bold" modalName="isServicioModalOpen" title="Nuevo Servicio"
            submitLabel="Guardar Servicio" formId="formServicio" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre
                        Servicio</label>
                    <input type="text" id="nombre_servicio" x-model="nombre_servicio" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div>
                    <label for="tarifa" class="block text-sm font-medium text-gray-700 nunito-bold">Tarifa</label>
                    <input type="number" id="tarifa" x-model="tarifa" step="0.01" min="0" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Servicio -->
        <x-admin.edit-modal class="nunito-bold" modalName="isEditServicioModalOpen" title="Editar Servicio"
            itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditServicio">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_nombre_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre
                        Servicio</label>
                    <input type="text" id="edit_nombre_servicio" x-model="itemToEdit.nombre_servicio" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div>
                    <label for="edit_tarifa" class="block text-sm font-medium text-gray-700 nunito-bold">Tarifa</label>
                    <input type="number" id="edit_tarifa" x-model="itemToEdit.tarifa" step="0.01" min="0" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
            </div>
        </x-admin.edit-modal>

        <!-- Modal Confirmar Eliminación -->
        <x-admin.confirmation-modal class="nunito-regular" modalName="isDeleteServicioModalOpen"
            itemToDelete="itemToDelete" message="¿Estás seguro de que quieres eliminar este servicio?" />
    </div>
</div>