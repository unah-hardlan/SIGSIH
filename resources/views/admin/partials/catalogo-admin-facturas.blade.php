<div x-data="estadosFacturaCrud" @keydown.escape.window="
    isEstadoFacturaModalOpen = false;
    isEditEstadoFacturaModalOpen = false;
    isDeleteEstadoFacturaModalOpen = false;
" @modal-submit.window="handleModalSubmit($event)" @confirm-delete.window="handleDelete()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Estados de Factura</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
            'searchModel' => 'filtroEstadoFactura',
            'ordenarOptions' => [
            'codigo' => 'Código',
            'nombre' => 'Nombre',
            'orden' => 'Orden'
            ]
            ])
        </x-slot>

        <x-slot name="actions">
            <button @click="isEstadoFacturaModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo Estado
            </button>
        </x-slot>

        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Código</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Nombre Estado</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Descripción</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Orden</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Es Final</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loadingEstadosFactura">
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando estados de factura...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingEstadosFactura && filteredEstadosFactura.length === 0">
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 nunito-regular">
                                No hay estados de factura registrados
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingEstadosFactura && filteredEstadosFactura.length > 0">
                        <template x-for="(estadoFactura, index) in filteredEstadosFactura"
                            :key="estadoFactura.id_estado_factura_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === filteredEstadosFactura.length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="estadoFactura.codigo || '-'"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="estadoFactura.nombre_estado"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="estadoFactura.descripcion_estado_factura || '-'"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="estadoFactura.orden || '0'"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">
                                    <span x-show="estadoFactura.es_final"
                                        class="px-2 py-1 text-xs rounded bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 dark:ring-1 dark:ring-green-500/40">Sí</span>
                                    <span x-show="!estadoFactura.es_final"
                                        class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100 dark:ring-1 dark:ring-gray-500/40">No</span>
                                </td>
                                <td class="py-2 px-4 flex gap-2"
                                    :class="{ 'last:rounded-br-lg': index === filteredEstadosFactura.length - 1 }">
                                    <a href="#"
                                        @click.prevent="isEditEstadoFacturaModalOpen = true; itemToEdit = {id_estado_factura_pk: estadoFactura.id_estado_factura_pk, codigo: estadoFactura.codigo, nombre: estadoFactura.nombre_estado, descripcion: estadoFactura.descripcion_estado_factura, es_final: estadoFactura.es_final, orden: estadoFactura.orden}"
                                        class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    <a href="#"
                                        @click.prevent="isDeleteEstadoFacturaModalOpen = true; itemToDelete = {id_estado_factura_pk: estadoFactura.id_estado_factura_pk, nombre: estadoFactura.nombre_estado}"
                                        class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loadingEstadosFactura">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando estados de factura...
                </div>
            </template>
            <template x-if="!loadingEstadosFactura && filteredEstadosFactura.length === 0">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    No hay estados de factura registrados
                </div>
            </template>
            <template x-if="!loadingEstadosFactura && filteredEstadosFactura.length > 0">
                <template x-for="estadoFactura in filteredEstadosFactura" :key="estadoFactura.id_estado_factura_pk">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold"
                                x-text="estadoFactura.nombre_estado"></h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular"
                            x-text="estadoFactura.descripcion_estado_factura"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button
                                @click.prevent="isEditEstadoFacturaModalOpen = true; itemToEdit = {id_estado_factura_pk: estadoFactura.id_estado_factura_pk, codigo: estadoFactura.codigo, nombre: estadoFactura.nombre_estado, descripcion: estadoFactura.descripcion_estado_factura, es_final: estadoFactura.es_final, orden: estadoFactura.orden}"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button
                                @click.prevent="isDeleteEstadoFacturaModalOpen = true; itemToDelete = {id_estado_factura_pk: estadoFactura.id_estado_factura_pk, nombre: estadoFactura.nombre_estado}"
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
        <!-- Modal Nuevo Estado Factura -->
        <x-admin.form-modal class="nunito-bold" modalName="isEstadoFacturaModalOpen" title="Nuevo Estado de Factura"
            submitLabel="Guardar Estado" formId="formEstadoFactura" maxWidth="max-w-4xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="codigo" class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                    <input type="text" id="codigo" x-model="codigo" maxlength="10"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre
                        Estado</label>
                    <input type="text" id="nombre" x-model="nombre" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div class="col-span-2">
                    <label for="descripcion"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="descripcion" x-model="descripcion" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                </div>
                <div>
                    <label for="orden" class="block text-sm font-medium text-gray-700 nunito-bold">Orden</label>
                    <input type="number" id="orden" x-model="orden" min="0"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="es_final" x-model="es_final"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <label for="es_final" class="ml-2 block text-sm font-medium text-gray-700 nunito-bold">¿Es estado
                        final?</label>
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Estado Factura -->
        <x-admin.edit-modal class="nunito-bold" modalName="isEditEstadoFacturaModalOpen"
            title="Editar Estado de Factura" itemToEdit="itemToEdit" maxWidth="max-w-4xl"
            formId="formEditEstadoFactura">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_codigo" class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                    <input type="text" id="edit_codigo" x-model="itemToEdit.codigo" maxlength="10"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div>
                    <label for="edit_nombre" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre
                        Estado</label>
                    <input type="text" id="edit_nombre" x-model="itemToEdit.nombre" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div class="col-span-2">
                    <label for="edit_descripcion"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="edit_descripcion" x-model="itemToEdit.descripcion" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                </div>
                <div>
                    <label for="edit_orden" class="block text-sm font-medium text-gray-700 nunito-bold">Orden</label>
                    <input type="number" id="edit_orden" x-model="itemToEdit.orden" min="0"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="edit_es_final" x-model="itemToEdit.es_final"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <label for="edit_es_final" class="ml-2 block text-sm font-medium text-gray-700 nunito-bold">¿Es
                        estado final?</label>
                </div>
            </div>
        </x-admin.edit-modal>

        <!-- Modal Confirmar Eliminación -->
        <x-admin.confirmation-modal class="nunito-regular" modalName="isDeleteEstadoFacturaModalOpen"
            itemToDelete="itemToDelete" message="¿Estás seguro de que quieres eliminar este estado de factura?" />
    </div>
</div>