<div x-data="{ 
    isEstadoFacturaModalOpen: false,
    isEditEstadoFacturaModalOpen: false,
    estadoFacturaToEdit: {id: '', nombre: '', descripcion: ''},
    isDeleteEstadoFacturaModalOpen: false,
    estadoFacturaToDelete: {id: ''}
}" class="overflow-x-auto w-full dark:bg-gray-900 min-h-screen">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6 mt-6 w-full">
        <div class="sticky top-0 z-10 bg-white dark:bg-gray-900 pb-4 mb-4 border-b dark:border-gray-700 flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full">
            <h2 class="text-2xl text-gray-800 dark:text-white nunito-bold">Estados de Factura</h2>
            <button @click="isEstadoFacturaModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nuevo Estado</button>
        </div>
        <table class="min-w-full text-sm w-full">
            <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left nunito-bold">ID</th>
                    <th class="py-2 px-4 text-left nunito-bold">Nombre Estado</th>
                    <th class="py-2 px-4 text-left nunito-bold">Descripción</th>
                    <th class="py-2 px-4 text-left nunito-bold">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b dark:border-gray-700 nunito-regular">
                    <td class="py-2 px-4 nunito-regular dark:text-white">1</td>
                    <td class="py-2 px-4 nunito-regular dark:text-white">Pagada</td>
                    <td class="py-2 px-4 nunito-regular dark:text-white">Factura completamente pagada</td>
                    <td class="py-2 px-4 flex gap-2 nunito-regular dark:text-white">
                        <button @click="isEditEstadoFacturaModalOpen = true; estadoFacturaToEdit = {id: 1, nombre: 'Pagada', descripcion: 'Factura completamente pagada'}" class="text-blue-500 hover:text-blue-700" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button @click="isDeleteEstadoFacturaModalOpen = true; estadoFacturaToDelete = {id: 1}" class="text-red-500 hover:text-red-700" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <tr class="border-b dark:border-gray-700 nunito-regular">
                    <td class="py-2 px-4 nunito-regular dark:text-white">2</td>
                    <td class="py-2 px-4 nunito-regular dark:text-white">Pendiente</td>
                    <td class="py-2 px-4 nunito-regular dark:text-white">Factura pendiente de pago</td>
                    <td class="py-2 px-4 flex gap-2 nunito-regular dark:text-white">
                        <button @click="isEditEstadoFacturaModalOpen = true; estadoFacturaToEdit = {id: 2, nombre: 'Pendiente', descripcion: 'Factura pendiente de pago'}" class="text-blue-500 hover:text-blue-700" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button @click="isDeleteEstadoFacturaModalOpen = true; estadoFacturaToDelete = {id: 2}" class="text-red-500 hover:text-red-700" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <tr class="border-b dark:border-gray-700 nunito-regular">
                    <td class="py-2 px-4 nunito-regular dark:text-white">3</td>
                    <td class="py-2 px-4 nunito-regular dark:text-white">Cancelada</td>
                    <td class="py-2 px-4 nunito-regular dark:text-white">Factura cancelada</td>
                    <td class="py-2 px-4 flex gap-2 nunito-regular dark:text-white">
                        <button @click="isEditEstadoFacturaModalOpen = true; estadoFacturaToEdit = {id: 3, nombre: 'Cancelada', descripcion: 'Factura cancelada'}" class="text-blue-500 hover:text-blue-700" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button @click="isDeleteEstadoFacturaModalOpen = true; estadoFacturaToDelete = {id: 3}" class="text-red-500 hover:text-red-700" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Modal Nuevo Estado Factura -->
    <x-admin.form-modal class="nunito-bold dark:bg-gray-800" modalName="isEstadoFacturaModalOpen" title="Nuevo Estado Factura" submitLabel="Guardar Estado">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-bold">Nombre Estado</label>
                <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent nunito-regular dark:bg-gray-900 dark:text-white" placeholder="Ej: En Proceso">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-bold">Descripción</label>
                <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent nunito-regular dark:bg-gray-900 dark:text-white" rows="3" placeholder="Descripción del estado de la factura"></textarea>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Estado Factura -->
    <x-admin.edit-modal class="nunito-bold dark:bg-gray-800" modalName="isEditEstadoFacturaModalOpen" title="Editar Estado Factura" itemToEdit="estadoFacturaToEdit">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-bold">ID Estado</label>
                <input type="text" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-700 rounded-lg nunito-regular dark:text-white" x-bind:value="estadoFacturaToEdit?.id" readonly>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-bold">Nombre Estado</label>
                <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent nunito-regular dark:bg-gray-900 dark:text-white" x-bind:value="estadoFacturaToEdit?.nombre">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-bold">Descripción</label>
                <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent nunito-regular dark:bg-gray-900 dark:text-white" rows="3" x-text="estadoFacturaToEdit?.descripcion"></textarea>
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Eliminar Estado Factura -->
    <x-admin.confirmation-modal class="nunito-bold" modalName="isDeleteEstadoFacturaModalOpen" itemToDelete="estadoFacturaToDelete" message="¿Está seguro que desea eliminar este estado de factura? Esta acción no se puede deshacer." />
</div>
