<div x-data="{ 
    isEstadoModalOpen: false, 
    isEditEstadoModalOpen: false, 
    estadoToEdit: {id: '', nombre: '', descripcion: ''}, 
    isDeleteEstadoModalOpen: false, 
    estadoToDelete: {id: ''} 
}" class="overflow-x-auto w-full">
    <div class="bg-white rounded-lg shadow p-6 mt-6 w-full">
        <div class="sticky top-0 z-10 bg-white pb-4 mb-4 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full">
            <h2 class="text-2xl text-gray-800 nunito-bold">Estados CAI</h2>
            <button @click="isEstadoModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nuevo Estado CAI</button>
        </div>
        <table class="min-w-full text-sm w-full">
            <thead class="bg-gray-100 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left nunito-bold">ID</th>
                    <th class="py-2 px-4 text-left nunito-bold">Nombre Estado CAI</th>
                    <th class="py-2 px-4 text-left nunito-bold">Descripción Estado CAI</th>
                    <th class="py-2 px-4 text-left nunito-bold">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b nunito-regular">
                    <td class="py-2 px-4 nunito-regular">1</td>
                    <td class="py-2 px-4 nunito-regular">Activo</td>
                    <td class="py-2 px-4 nunito-regular">Estado activo para el CAI</td>
                    <td class="py-2 px-4 flex gap-2 nunito-regular">
                        <button @click="isEditEstadoModalOpen = true; estadoToEdit = {id: 1, nombre: 'Activo', descripcion: 'Estado activo para el CAI'}" class="text-blue-500 hover:text-blue-700" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button @click="isDeleteEstadoModalOpen = true; estadoToDelete = {id: 1}" class="text-red-500 hover:text-red-700" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <tr class="border-b nunito-regular">
                    <td class="py-2 px-4 nunito-regular">2</td>
                    <td class="py-2 px-4 nunito-regular">Inactivo</td>
                    <td class="py-2 px-4 nunito-regular">Estado inactivo para el CAI</td>
                    <td class="py-2 px-4 flex gap-2 nunito-regular">
                        <button @click="isEditEstadoModalOpen = true; estadoToEdit = {id: 2, nombre: 'Inactivo', descripcion: 'Estado inactivo para el CAI'}" class="text-blue-500 hover:text-blue-700" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button @click="isDeleteEstadoModalOpen = true; estadoToDelete = {id: 2}" class="text-red-500 hover:text-red-700" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <tr class="border-b nunito-regular">
                    <td class="py-2 px-4 nunito-regular">3</td>
                    <td class="py-2 px-4 nunito-regular">Por Vencer</td>
                    <td class="py-2 px-4 nunito-regular">CAI próximo a vencer</td>
                    <td class="py-2 px-4 flex gap-2 nunito-regular">
                        <button @click="isEditEstadoModalOpen = true; estadoToEdit = {id: 3, nombre: 'Por Vencer', descripcion: 'CAI próximo a vencer'}" class="text-blue-500 hover:text-blue-700" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button @click="isDeleteEstadoModalOpen = true; estadoToDelete = {id: 3}" class="text-red-500 hover:text-red-700" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Modal Nuevo Estado CAI -->
    <x-admin.form-modal class="nunito-bold" modalName="isEstadoModalOpen" title="Nuevo Estado CAI" submitLabel="Guardar Estado CAI">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 nunito-bold">Nombre Estado CAI</label>
                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent nunito-regular" placeholder="Ej: Vencido">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 nunito-bold">Descripción Estado CAI</label>
                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent nunito-regular" rows="3" placeholder="Descripción del estado del CAI"></textarea>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Estado CAI -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditEstadoModalOpen" title="Editar Estado CAI" itemToEdit="estadoToEdit">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 nunito-bold">ID Estado</label>
                <input type="text" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg nunito-regular" x-bind:value="estadoToEdit?.id" readonly>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 nunito-bold">Nombre Estado CAI</label>
                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent nunito-regular" x-bind:value="estadoToEdit?.nombre">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 nunito-bold">Descripción Estado CAI</label>
                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent nunito-regular" rows="3" x-text="estadoToEdit?.descripcion"></textarea>
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Eliminar Estado CAI -->
    <x-admin.confirmation-modal class="nunito-bold" modalName="isDeleteEstadoModalOpen" itemToDelete="estadoToDelete" message="¿Está seguro que desea eliminar este estado CAI? Esta acción no se puede deshacer." />
</div>
