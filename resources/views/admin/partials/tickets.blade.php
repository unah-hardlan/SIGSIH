<div x-data="{ isModalOpen: false, isEditModalOpen: false, ticketToEdit: { id: '', cliente: '', fecha: '', estado: '' }, isDeleteModalOpen: false, ticketToDelete: null }">
<div class="w-full">
    <div class="overflow-x-auto w-full">
      <div class="bg-white rounded-lg shadow p-6 mt-6 w-full">
        <div class="sticky top-0 z-10 bg-white pb-4 mb-4 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full">
          <h2 class="text-2xl text-gray-800 nunito-bold">Gestión de Tickets</h2>
          <div class="flex flex-col sm:flex-row gap-2 flex-1 md:ml-6 nunito-bold">
            <input type="text" placeholder="Buscar ticket..." class="border rounded px-3 py-2 text-sm w-full sm:w-48 nunito-regular" />
            <select class="border rounded px-1 py-2 text-sm w-full sm:w-40 nunito-regular">
              <option class="nunito-regular" value="">Todos los estados</option>
              <option class="nunito-regular">Pendiente</option>
              <option class="nunito-regular">En proceso</option>
              <option class="nunito-regular">Finalizado</option>
            </select>
          </div>
          <div class="flex flex-col gap-2 w-full sm:w-auto">
            <button @click="isModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nuevo ticket</button>
            <a href="/admin/reportes-header?modulo=Tickets&fecha={{ now()->format('d-M-Y') }}" target="_blank"
               class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 text-sm">
                <i class="fas fa-file-alt"></i> Generar Reporte
            </a>
          </div>
        </div>
        <table class="min-w-full text-sm w-full">
          <thead class="bg-gray-100 nunito-bold">
            <tr>
              <th class="py-2 px-4 text-left nunito-bold">ID</th>
              <th class="py-2 px-4 text-left nunito-bold">Cliente</th>
              <th class="py-2 px-4 text-left nunito-bold">Fecha</th>
              <th class="py-2 px-4 text-left nunito-bold">Estado</th>
              <th class="py-2 px-4 text-left nunito-bold">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr class="border-b nunito-regular">
              <td class="py-2 px-4 nunito-regular">1</td>
              <td class="py-2 px-4 nunito-regular">Empresa Ejemplo S.A.</td>
              <td class="py-2 px-4 nunito-regular">26/07/2025</td>
              <td class="py-2 px-4"><span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded nunito-regular">Pendiente</span></td>
              <td class="py-2 px-4 flex gap-2">
                <a href="#" @click="isEditModalOpen = true; ticketToEdit = {id: 1, cliente: 'Empresa Ejemplo S.A.', fecha: '26/07/2025', estado: 'Pendiente'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                <a href="#" @click="isDeleteModalOpen = true; ticketToDelete = {id: 1}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
              </td>
            </tr>
            <tr class="border-b nunito-regular">
              <td class="py-2 px-4 nunito-regular">2</td>
              <td class="py-2 px-4 nunito-regular">Bac Credomatic</td>
              <td class="py-2 px-4 nunito-regular">27/07/2025</td>
              <td class="py-2 px-4"><span class="bg-green-100 text-green-700 px-2 py-1 rounded nunito-regular">En proceso</span></td>
              <td class="py-2 px-4 flex gap-2">
                <a href="#" @click="isEditModalOpen = true; ticketToEdit = {id: 2, cliente: 'Bac Credomatic', fecha: '27/07/2025', estado: 'En proceso'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                <a href="#" @click="isDeleteModalOpen = true; ticketToDelete = {id: 2}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
              </td>
            </tr>
            <tr class="border-b nunito-regular">
              <td class="py-2 px-4 nunito-regular">3</td>
              <td class="py-2 px-4 nunito-regular">Ficohsa</td>
              <td class="py-2 px-4 nunito-regular">28/07/2025</td>
              <td class="py-2 px-4"><span class="bg-blue-100 text-blue-700 px-2 py-1 rounded nunito-regular">Finalizado</span></td>
              <td class="py-2 px-4 flex gap-2">
                <a href="#" @click="isEditModalOpen = true; ticketToEdit = {id: 3, cliente: 'Ficohsa', fecha: '28/07/2025', estado: 'Finalizado'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                <a href="#" @click="isDeleteModalOpen = true; ticketToDelete = {id: 3}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
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
                <label for="id" class="block text-sm font-medium text-gray-700 nunito-bold">ID</label>
                <input type="text" id="id" name="id" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="cliente" class="block text-sm font-medium text-gray-700 nunito-bold">Cliente</label>
                <input type="text" id="cliente" name="cliente" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="fecha" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                <input type="date" id="fecha" name="fecha" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 nunito-bold">Estado</label>
                <select id="estado" name="estado" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option class="nunito-regular">Pendiente</option>
                    <option class="nunito-regular">En proceso</option>
                    <option class="nunito-regular">Finalizado</option>
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
                <label for="edit_id" class="block text-sm font-medium text-gray-700 nunito-bold">ID</label>
                <input type="text" id="edit_id" name="edit_id" :value="ticketToEdit.id" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_cliente" class="block text-sm font-medium text-gray-700 nunito-bold">Cliente</label>
                <input type="text" id="edit_cliente" name="edit_cliente" :value="ticketToEdit.cliente" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_fecha" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                <input type="date" id="edit_fecha" name="edit_fecha" :value="ticketToEdit.fecha" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Estado</label>
                <select id="edit_estado" name="edit_estado" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option :selected="ticketToEdit.estado === 'Pendiente'" class="nunito-regular">Pendiente</option>
                    <option :selected="ticketToEdit.estado === 'En proceso'" class="nunito-regular">En proceso</option>
                    <option :selected="ticketToEdit.estado === 'Finalizado'" class="nunito-regular">Finalizado</option>
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
