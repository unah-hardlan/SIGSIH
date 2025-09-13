<div x-data="{ 
    isEstadoFacturaModalOpen: false,
    isEditEstadoFacturaModalOpen: false,
    estadoFacturaToEdit: {id: '', nombre: '', descripcion: ''},
    isDeleteEstadoFacturaModalOpen: false,
    estadoFacturaToDelete: {id: ''},
    searchEstadoFactura: '',
    sortBy: 'nombre',
    sortDirection: 'asc'
}" class="overflow-x-auto w-full dark:bg-gray-900 min-h-screen">
    <x-admin.tabla-crud class="nunito-bold">
        <x-slot name="titulo">
            <h2 class="text-2xl text-gray-800 dark:text-white nunito-bold">Estados de Factura</h2>
        </x-slot>
        <x-slot name="filtros">
            @include('partials.filtros-generales', [
                'searchModel' => 'searchEstadoFactura',
                'ordenarOptions' => [
                    'nombre' => 'Nombre'
                ]
            ])
        </x-slot>
        <x-slot name="boton">
            <button @click="isEstadoFacturaModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nuevo Estado</button>
        </x-slot>
        <x-admin.tabla-mobile>
            <x-slot name="desktop">
                <div class="overflow-x-auto">
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
                            <template x-for="estado in [
                                {id: 1, nombre: 'Pagada', descripcion: 'Factura completamente pagada'},
                                {id: 2, nombre: 'Pendiente', descripcion: 'Factura pendiente de pago'},
                                {id: 3, nombre: 'Cancelada', descripcion: 'Factura cancelada'}
                            ].filter(estado => 
                                !searchEstadoFactura || 
                                estado.nombre.toLowerCase().includes(searchEstadoFactura.toLowerCase()) ||
                                estado.descripcion.toLowerCase().includes(searchEstadoFactura.toLowerCase())
                            ).sort((a, b) => {
                                const aValue = a[sortBy]?.toLowerCase() || '';
                                const bValue = b[sortBy]?.toLowerCase() || '';
                                if (sortDirection === 'asc') {
                                    return aValue.localeCompare(bValue);
                                } else {
                                    return bValue.localeCompare(aValue);
                                }
                            })" :key="estado.id">
                                <tr class="border-b dark:border-gray-700 nunito-regular">
                                    <td class="py-2 px-4 nunito-regular dark:text-white" x-text="estado.id"></td>
                                    <td class="py-2 px-4 nunito-regular dark:text-white" x-text="estado.nombre"></td>
                                    <td class="py-2 px-4 nunito-regular dark:text-white" x-text="estado.descripcion"></td>
                                    <td class="py-2 px-4 flex gap-2 nunito-regular dark:text-white">
                                        <button @click="isEditEstadoFacturaModalOpen = true; estadoFacturaToEdit = estado" class="text-blue-500 hover:text-blue-700" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button @click="isDeleteEstadoFacturaModalOpen = true; estadoFacturaToDelete = estado" class="text-red-500 hover:text-red-700" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </x-slot>
            <x-slot name="mobileTemplate">
                <template x-for="estado in [
                    {id: 1, nombre: 'Pagada', descripcion: 'Factura completamente pagada'},
                    {id: 2, nombre: 'Pendiente', descripcion: 'Factura pendiente de pago'},
                    {id: 3, nombre: 'Cancelada', descripcion: 'Factura cancelada'}
                ].filter(estado => 
                    !searchEstadoFactura || 
                    estado.nombre.toLowerCase().includes(searchEstadoFactura.toLowerCase()) ||
                    estado.descripcion.toLowerCase().includes(searchEstadoFactura.toLowerCase())
                ).sort((a, b) => {
                    const aValue = a[sortBy]?.toLowerCase() || '';
                    const bValue = b[sortBy]?.toLowerCase() || '';
                    if (sortDirection === 'asc') {
                        return aValue.localeCompare(bValue);
                    } else {
                        return bValue.localeCompare(aValue);
                    }
                })" :key="estado.id">
                    <div class="rounded-lg shadow mb-4 p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">ID</span>
                            <span class="text-sm nunito-bold text-gray-800 dark:text-white" x-text="estado.id"></span>
                        </div>
                        <div class="mb-2">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">Nombre Estado</span>
                            <div class="text-sm nunito-bold text-gray-800 dark:text-white" x-text="estado.nombre"></div>
                        </div>
                        <div class="mb-2">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">Descripción</span>
                            <div class="text-sm nunito-regular text-gray-800 dark:text-white" x-text="estado.descripcion"></div>
                        </div>
                        <div class="flex gap-3 mt-2">
                            <button @click="isEditEstadoFacturaModalOpen = true; estadoFacturaToEdit = estado" class="text-blue-500 hover:text-blue-700 dark:text-blue-300" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button @click="isDeleteEstadoFacturaModalOpen = true; estadoFacturaToDelete = estado" class="text-red-500 hover:text-red-700 dark:text-red-400" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </x-slot>
        </x-admin.tabla-mobile>
    </x-admin.tabla-crud>

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
