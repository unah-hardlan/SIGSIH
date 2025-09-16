<div x-data="{ 
  isModalOpen: false, 
  isEditModalOpen: false, 
  ticketToEdit: { id: '', cliente: '', fecha: '', estado: '' }, 
  isDeleteModalOpen: false, 
  ticketToDelete: null,
  search: '',
  estadoFiltro: '',
  ordenarPor: 'id'
}">
<div class="w-full">
    <div class="overflow-x-auto w-full">
  <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6 mt-2 w-full">
        <div class="sticky top-0 z-10 bg-white dark:bg-gray-900 pb-4 border-b dark:border-gray-600 flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full">
          <h2 class="text-2xl dark:bg-gray-900 text-gray-800 dark:text-white nunito-bold">Gestión de Tickets</h2>
          <div class="flex flex-col sm:flex-row gap-2 flex-1 md:ml-6 nunito-bold">
            @include('partials.filtros-generales', [
              'searchModel' => 'search',
              'filtrosSelect' => [
                'estadoFiltro' => [
                  'label' => 'Estados',
                  'options' => ['Pendiente', 'En proceso', 'Finalizado']
                ]
              ],
              'ordenarOptions' => [
                'id' => 'ID',
                'cliente' => 'Cliente',
                'fecha' => 'Fecha',
                'estado' => 'Estado'
              ]
            ])
          </div>
          <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <button @click="isModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nuevo ticket</button>
            <a href="/admin/reportes-header?modulo=Tickets&fecha={{ now()->format('d-M-Y') }}" target="_blank"
               class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 text-sm">
                <i class="fas fa-file-alt"></i> Generar Reporte
            </a>
          </div>
        </div>
        <table class="min-w-full text-sm w-full">
          <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
            <tr>
              <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">ID</th>
              <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Cliente</th>
              <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Fecha</th>
              <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Estado</th>
              <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr class="border-b dark:border-gray-600 nunito-regular">
              <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">1</td>
              <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">Empresa Ejemplo S.A.</td>
              <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">26/07/2025</td>
              <td class="py-2 px-4"><span class="bg-yellow-100 dark:bg-yellow-600 text-yellow-700 dark:text-yellow-100 px-2 py-1 rounded nunito-regular">Pendiente</span></td>
              <td class="py-2 px-4 flex gap-2">
                <a href="#" @click="isEditModalOpen = true; ticketToEdit = {id: 1, cliente: 'Empresa Ejemplo S.A.', fecha: '26/07/2025', estado: 'Pendiente'}" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"><i class="fas fa-edit"></i></a>
                <a href="#" @click="isDeleteModalOpen = true; ticketToDelete = {id: 1}" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"><i class="fas fa-trash"></i></a>
              </td>
            </tr>
            <tr class="border-b dark:border-gray-600 nunito-regular">
              <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">2</td>
              <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">Bac Credomatic</td>
              <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">27/07/2025</td>
              <td class="py-2 px-4"><span class="bg-green-100 dark:bg-green-600 text-green-700 dark:text-green-100 px-2 py-1 rounded nunito-regular">En proceso</span></td>
              <td class="py-2 px-4 flex gap-2">
                <a href="#" @click="isEditModalOpen = true; ticketToEdit = {id: 2, cliente: 'Bac Credomatic', fecha: '27/07/2025', estado: 'En proceso'}" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"><i class="fas fa-edit"></i></a>
                <a href="#" @click="isDeleteModalOpen = true; ticketToDelete = {id: 2}" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"><i class="fas fa-trash"></i></a>
              </td>
            </tr>
            <tr class="border-b dark:border-gray-600 nunito-regular">
              <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">3</td>
              <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">Ficohsa</td>
              <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">28/07/2025</td>
              <td class="py-2 px-4"><span class="bg-blue-100 dark:bg-blue-600 text-blue-700 dark:text-blue-100 px-2 py-1 rounded nunito-regular">Finalizado</span></td>
              <td class="py-2 px-4 flex gap-2">
                <a href="#" @click="isEditModalOpen = true; ticketToEdit = {id: 3, cliente: 'Ficohsa', fecha: '28/07/2025', estado: 'Finalizado'}" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"><i class="fas fa-edit"></i></a>
                <a href="#" @click="isDeleteModalOpen = true; ticketToDelete = {id: 3}" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"><i class="fas fa-trash"></i></a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Nuevo Ticket -->
    <x-admin.form-modal class="nunito-bold"
        modalName="isModalOpen" 
        title="Nuevo Ticket" 
        submitLabel="Guardar Ticket">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="id" class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold">ID</label>
                <input type="text" id="id" name="id" class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm border focus:border-gray-500 dark:focus:border-white focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="cliente" class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold">Cliente</label>
                <input type="text" id="cliente" name="cliente" class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm border focus:border-gray-500 dark:focus:border-white focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="fecha" class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold">Fecha</label>
                <input type="date" id="fecha" name="fecha" class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm border focus:border-gray-500 dark:focus:border-white focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold">Estado</label>
                <select id="estado" name="estado" class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm border focus:border-gray-500 dark:focus:border-white focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option class="nunito-regular dark:bg-gray-700">Pendiente</option>
                    <option class="nunito-regular dark:bg-gray-700">En proceso</option>
                    <option class="nunito-regular dark:bg-gray-700">Finalizado</option>
                </select>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Ticket -->
    <x-admin.edit-modal class="nunito-bold"
        modalName="isEditModalOpen" 
        title="Editar Ticket" 
        itemToEdit="ticketToEdit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="edit_id" class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold">ID</label>
                <input type="text" id="edit_id" name="edit_id" :value="ticketToEdit.id" class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm border focus:border-gray-500 dark:focus:border-white focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_cliente" class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold">Cliente</label>
                <input type="text" id="edit_cliente" name="edit_cliente" :value="ticketToEdit.cliente" class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm border focus:border-gray-500 dark:focus:border-white focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_fecha" class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold">Fecha</label>
                <input type="date" id="edit_fecha" name="edit_fecha" :value="ticketToEdit.fecha" class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm border focus:border-gray-500 dark:focus:border-white focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_estado" class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold">Estado</label>
                <select id="edit_estado" name="edit_estado" class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm border focus:border-gray-500 dark:focus:border-white focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option :selected="ticketToEdit.estado === 'Pendiente'" class="nunito-regular dark:bg-gray-700">Pendiente</option>
                    <option :selected="ticketToEdit.estado === 'En proceso'" class="nunito-regular dark:bg-gray-700">En proceso</option>
                    <option :selected="ticketToEdit.estado === 'Finalizado'" class="nunito-regular dark:bg-gray-700">Finalizado</option>
                </select>
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación Ticket -->
    <x-admin.confirmation-modal class="nunito-bold"
        modalName="isDeleteModalOpen"
        itemToDelete="ticketToDelete"
        message="¿Estás seguro de que quieres eliminar el ticket?"
    />
</div>
</div>
