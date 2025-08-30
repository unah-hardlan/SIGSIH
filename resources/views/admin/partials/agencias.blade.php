<div x-data="{ isAgenciaModalOpen: false, isDeleteAgenciaModalOpen: false, agenciaToEdit: null, agenciaToDelete: null }">
  <div class="w-full">
    <div class="overflow-x-auto w-full">
      <div class="bg-white rounded-lg shadow p-6 mt-6 w-full">
        <div class="sticky top-0 z-10 bg-white pb-4 mb-4 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full">
          <h2 class="text-2xl text-gray-800 nunito-bold">Agencias</h2>
          <div class="flex flex-col sm:flex-row gap-2 flex-1 md:ml-6 nunito-bold">
            <input type="text" placeholder="Buscar agencia..." class="border rounded px-3 py-2 text-sm w-full sm:w-48 nunito-regular" />
            <select class="border rounded px-1 py-2 text-sm w-full sm:w-40 nunito-regular">
              <option class="nunito-regular" value="">Todas las ciudades</option>
              <option class="nunito-regular">Tegucigalpa</option>
              <option class="nunito-regular">San Pedro Sula</option>
            </select>
          </div>
          <button @click="isAgenciaModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nueva agencia</button>
          <a href="/admin/reportes-header?modulo=Agencias&fecha={{ now()->format('d-M-Y') }}" target="_blank"
             class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 text-sm">
              <i class="fas fa-file-alt"></i> Generar Reporte
          </a>
        </div>
        <table class="min-w-full text-sm w-full">
          <thead class="bg-gray-100 nunito-bold">
            <tr>
              <th class="py-2 px-4 text-left nunito-bold">Nombre</th>
              <th class="py-2 px-4 text-left nunito-bold">Horario</th>
              <th class="py-2 px-4 text-left nunito-bold">Dirección</th>
              <th class="py-2 px-4 text-left nunito-bold">Ciudad</th>
              <th class="py-2 px-4 text-left nunito-bold">Departamento</th>
              <th class="py-2 px-4 text-left nunito-bold">País</th>
              <th class="py-2 px-4 text-left nunito-bold">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr class="border-b nunito-regular">
              <td class="py-2 px-4 nunito-regular">Agencia Central</td>
              <td class="py-2 px-4 nunito-regular">Lunes a Viernes, 8am - 5pm</td>
              <td class="py-2 px-4 nunito-regular">Col. Centro</td>
              <td class="py-2 px-4 nunito-regular">Tegucigalpa</td>
              <td class="py-2 px-4 nunito-regular">Francisco Morazán</td>
              <td class="py-2 px-4 nunito-regular">Honduras</td>
              <td class="py-2 px-4 flex gap-2">
                <a href="#" class="text-blue-500 hover:text-blue-700"><i class="fas fa-eye"></i></a>
                <a href="#" @click="isDeleteAgenciaModalOpen = true; agenciaToDelete = {nombre: 'Agencia Central'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Nueva Agencia -->
    <x-admin.form-modal class="nunito-bold"
      modalName="isAgenciaModalOpen" 
      title="Nueva Agencia" 
      submitLabel="Guardar Agencia"
      maxWidth="max-w-2xl">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="nombre_agencia" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre de la agencia</label>
          <input type="text" id="nombre_agencia" name="nombre_agencia" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
        <div>
          <label for="horario_agencia" class="block text-sm font-medium text-gray-700 nunito-bold">Horario</label>
          <input type="text" id="horario_agencia" name="horario_agencia" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
        <div>
          <label for="pais_agencia" class="block text-sm font-medium text-gray-700 nunito-bold">País</label>
          <input type="text" id="pais_agencia" name="pais_agencia" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
        <div>
          <label for="departamento_agencia" class="block text-sm font-medium text-gray-700 nunito-bold">Departamento</label>
          <input type="text" id="departamento_agencia" name="departamento_agencia" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
        <div>
          <label for="ciudad_agencia" class="block text-sm font-medium text-gray-700 nunito-bold">Ciudad</label>
          <input type="text" id="ciudad_agencia" name="ciudad_agencia" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
        <div>
          <label for="direccion_agencia" class="block text-sm font-medium text-gray-700 nunito-bold">Dirección</label>
          <input type="text" id="direccion_agencia" name="direccion_agencia" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
      </div>
    </x-admin.form-modal>

    <!-- Modal Confirmar Eliminación Agencia -->
    <x-admin.confirmation-modal class="nunito-bold"
      modalName="isDeleteAgenciaModalOpen"
      itemToDelete="agenciaToDelete"
      message="¿Estás seguro de que quieres eliminar la agencia?"
    />
  </div>
</div>
