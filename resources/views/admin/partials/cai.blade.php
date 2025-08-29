<div x-data="{ 
    tab: 'cai', 
    isModalOpen: false, 
    isEditModalOpen: false, 
    itemToEdit: {id: '', codigo: '', rango_inicio: '', rango_fin: '', fecha_limite: '', estado_cai: ''}, 
    isDeleteModalOpen: false, 
    itemToDelete: {id: ''}
}">

    <div x-show="tab==='cai'" class="overflow-x-auto">
        <x-admin.tabla-crud class="nunito-bold">
            <x-slot name="titulo">
                <h2 class="text-2xl text-gray-800 nunito-bold">CAI</h2>
            </x-slot>
            <x-slot name="filtros">
                <input type="text" placeholder="Buscar CAI..." class="border rounded px-3 py-2 text-sm w-full sm:w-48 nunito-regular" />
            </x-slot>
            <x-slot name="boton">
                <div class="flex gap-2 items-stretch">
                    <button @click="isModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap">Nuevo CAI</button>
                    <a href="/admin/reportes-header?modulo=CAI&fecha={{ now()->format('d-M-Y') }}" target="_blank"
                       class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap flex items-center gap-2">
                        <i class="fas fa-file-alt"></i> Generar Reporte
                    </a>
                </div>
            </x-slot>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 nunito-bold">
                        <tr>
                            <th class="py-2 px-4 text-left nunito-bold">ID</th>
                            <th class="py-2 px-4 text-left nunito-bold">Código</th>
                            <th class="py-2 px-4 text-left nunito-bold">Rango Inicio</th>
                            <th class="py-2 px-4 text-left nunito-bold">Rango Fin</th>
                            <th class="py-2 px-4 text-left nunito-bold">Fecha Límite</th>
                            <th class="py-2 px-4 text-left nunito-bold">Estado CAI</th>
                            <th class="py-2 px-4 text-left nunito-bold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b nunito-regular">
                            <td class="py-2 px-4 nunito-regular">1</td>
                            <td class="py-2 px-4 nunito-regular">ABC123</td>
                            <td class="py-2 px-4 nunito-regular"> 000-001-01-00000001</td>
                            <td class="py-2 px-4 nunito-regular">000-001-01-000000200</td>
                            <td class="py-2 px-4 nunito-regular">2025-12-31</td>
                            <td class="py-2 px-4 nunito-regular">Activo</td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click.prevent="isEditModalOpen = true; itemToEdit = {id: 1, codigo: 'ABC123456789', rango_inicio: '0001', rango_fin: '1000', fecha_limite: '2025-12-31', estado_cai: 'Activo'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#" @click.prevent="isDeleteModalOpen = true; itemToDelete = {id: 1}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-admin.tabla-crud>
    </div>

    {{-- Modales --}}
    <x-admin.form-modal class="nunito-bold"
        modalName="isModalOpen" 
        title="Nuevo CAI" 
        submitLabel="Guardar CAI"
        maxWidth="max-w-md">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="codigo" class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                <input type="text" id="codigo" name="codigo" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="rango_inicio" class="block text-sm font-medium text-gray-700 nunito-bold">Rango Inicio</label>
                <input type="text" id="rango_inicio" name="rango_inicio" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="rango_fin" class="block text-sm font-medium text-gray-700 nunito-bold">Rango Fin</label>
                <input type="text" id="rango_fin" name="rango_fin" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="fecha_limite" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha Límite</label>
                <input type="date" id="fecha_limite" name="fecha_limite" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="estado_cai" class="block text-sm font-medium text-gray-700 nunito-bold">Estado CAI</label>
                <select id="estado_cai" name="estado_cai" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="Activo" class="nunito-regular">Activo</option>
                    <option value="Inactivo" class="nunito-regular">Inactivo</option>
                </select>
            </div>
        </div>
    </x-admin.form-modal>

    <x-admin.edit-modal class="nunito-bold"
        modalName="isEditModalOpen" 
        title="Editar CAI" 
        itemToEdit="itemToEdit"
        maxWidth="max-w-md">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="edit_codigo" class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                <input type="text" id="edit_codigo" name="edit_codigo" :value="itemToEdit.codigo" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_rango_inicio" class="block text-sm font-medium text-gray-700 nunito-bold">Rango Inicio</label>
                <input type="text" id="edit_rango_inicio" name="edit_rango_inicio" :value="itemToEdit.rango_inicio" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_rango_fin" class="block text-sm font-medium text-gray-700 nunito-bold">Rango Fin</label>
                <input type="text" id="edit_rango_fin" name="edit_rango_fin" :value="itemToEdit.rango_fin" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_fecha_limite" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha Límite</label>
                <input type="date" id="edit_fecha_limite" name="edit_fecha_limite" :value="itemToEdit.fecha_limite" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_estado_cai" class="block text-sm font-medium text-gray-700 nunito-bold">Estado CAI</label>
                <select id="edit_estado_cai" name="edit_estado_cai" :value="itemToEdit.estado_cai" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="Activo" class="nunito-regular">Activo</option>
                    <option value="Inactivo" class="nunito-regular">Inactivo</option>
                </select>
            </div>
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-bold"
        modalName="isDeleteModalOpen"
        itemToDelete="itemToDelete"
        message="¿Estás seguro de que quieres eliminar este CAI?"
    />
</div>
