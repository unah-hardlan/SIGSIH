<div x-data="{ isAgenciaModalOpen: false, isDeleteAgenciaModalOpen: false, agenciaToEdit: null, agenciaToDelete: null }">
  <div class="w-full">
    <div class="overflow-x-auto w-full">
      <div class="bg-white rounded-lg shadow p-6 mt-6 w-full">
        <div class="sticky top-0 z-10 bg-white pb-4 mb-4 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full">
          <h2 class="text-2xl text-gray-800 nunito-bold">Agencias</h2>
          <div class="flex flex-col sm:flex-row gap-2 flex-1 md:ml-6 nunito-bold">
            <input type="text" placeholder="Buscar agencia..." class="border rounded px-3 py-2 text-sm w-full sm:w-48" />
            <select class="border rounded px-1 py-2 text-sm w-full sm:w-40">
              <option class="nunito-bold" value="">Todas las ciudades</option>
              <option class="nunito-bold">Tegucigalpa</option>
              <option class="nunito-bold">San Pedro Sula</option>
            </select>
          </div>
          <button @click="isAgenciaModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap">Nueva agencia</button>
          <a href="/admin/reportes-header?modulo=Agencias&fecha={{ now()->format('d-M-Y') }}" target="_blank"
             class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap flex items-center gap-2">
              <i class="fas fa-file-alt"></i> Generar Reporte
          </a>
        </div>
        <table class="min-w-full text-sm w-full">
          <thead class="bg-gray-100 nunito-bold">
            <tr>
              <th class="py-2 px-4 text-left">Nombre</th>
              <th class="py-2 px-4 text-left">Horario</th>
              <th class="py-2 px-4 text-left">Dirección</th>
              <th class="py-2 px-4 text-left">Ciudad</th>
              <th class="py-2 px-4 text-left">Departamento</th>
              <th class="py-2 px-4 text-left">País</th>
              <th class="py-2 px-4 text-left">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr class="border-b nunito-regular">
              <td class="py-2 px-4">Agencia Central</td>
              <td class="py-2 px-4">Lunes a Viernes, 8am - 5pm</td>
              <td class="py-2 px-4">Col. Centro</td>
              <td class="py-2 px-4">Tegucigalpa</td>
              <td class="py-2 px-4">Francisco Morazán</td>
              <td class="py-2 px-4">Honduras</td>
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
    <x-admin.form-modal 
      modalName="isAgenciaModalOpen" 
      title="Nueva Agencia" 
      submitLabel="Guardar Agencia"
      maxWidth="max-w-2xl">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <div class="col-span-1">
          <label for="nombre_agencia" class="block text-sm font-medium text-gray-700">Nombre de la agencia</label>
          <input type="text" id="nombre_agencia" name="nombre_agencia" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
        <div>
          <label for="horario_agencia" class="block text-sm font-medium text-gray-700">Horario</label>
          <input type="text" id="horario_agencia" name="horario_agencia" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
        <div>
          <label for="pais_agencia" class="block text-sm font-medium text-gray-700">País</label>
          <input type="text" id="pais_agencia" name="pais_agencia" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
        <div>
          <label for="departamento_agencia" class="block text-sm font-medium text-gray-700">Departamento</label>
          <input type="text" id="departamento_agencia" name="departamento_agencia" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
        <div>
          <label for="ciudad_agencia" class="block text-sm font-medium text-gray-700">Ciudad</label>
          <input type="text" id="ciudad_agencia" name="ciudad_agencia" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
        <div>
          <label for="direccion_agencia" class="block text-sm font-medium text-gray-700">Dirección</label>
          <input type="text" id="direccion_agencia" name="direccion_agencia" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
      </div>
    </x-admin.form-modal>

    <!-- Modal Confirmar Eliminación Agencia -->
    <x-admin.confirmation-modal
      modalName="isDeleteAgenciaModalOpen"
      itemToDelete="agenciaToDelete"
      message="¿Estás seguro de que quieres eliminar la agencia?"
    />
  </div>
</div>
