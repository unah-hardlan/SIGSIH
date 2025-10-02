<div x-data="{ isAgenciaModalOpen: false, isDeleteAgenciaModalOpen: false, agenciaToEdit: null, agenciaToDelete: null }">
  <div class="w-full">
    <div class="overflow-x-auto w-full">
      <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6 mt-6 w-full">
        <div class="sticky top-0 z-10 bg-white dark:bg-gray-900 pb-4 mb-4 border-b dark:border-gray-600 flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full">
          <h2 class="text-2xl text-gray-800 dark:text-white nunito-bold">Agencias</h2>
          <div class="flex flex-col sm:flex-row gap-2 flex-1 md:ml-6 nunito-bold">
            @include('partials.filtros-generales', [
              'searchModel' => 'searchAgencia',
              'filtrosSelect' => [
                'ciudadFiltro' => [
                  'label' => 'Ciudades',
                  'options' => ['Tegucigalpa', 'San Pedro Sula']
                ]
              ],
              'ordenarOptions' => [
                'nombre' => 'Nombre',
                'ciudad' => 'Ciudad',
                'departamento' => 'Departamento',
                'pais' => 'País'
              ]
            ])
          </div>
          <button @click="isAgenciaModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nueva agencia</button>
          <a href="/admin/reportes-header?modulo=Agencias&fecha={{ now()->format('d-M-Y') }}" target="_blank"
             class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 text-sm">
              <i class="fas fa-file-alt"></i> Generar Reporte
          </a>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
            <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
              <tr class="border-0">
                <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 first:rounded-tl-lg border-0">Nombre</th>
                <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 border-0">Horario</th>
                <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 border-0">Dirección</th>
                <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 border-0">Ciudad</th>
                <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 border-0">Departamento</th>
                <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 border-0">País</th>
                <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 last:rounded-tr-lg border-0">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular last:border-b-0">
                <td class="py-2 px-4 nunito-regular first:rounded-bl-lg">Agencia Central</td>
                <td class="py-2 px-4 nunito-regular">Lunes a Viernes, 8am - 5pm</td>
                <td class="py-2 px-4 nunito-regular">Col. Centro</td>
                <td class="py-2 px-4 nunito-regular">Tegucigalpa</td>
                <td class="py-2 px-4 nunito-regular">Francisco Morazán</td>
                <td class="py-2 px-4 nunito-regular">Honduras</td>
                <td class="py-2 px-4 flex gap-2 last:rounded-br-lg">
                  <a href="#" class="text-blue-500 hover:text-blue-700"><i class="fas fa-eye"></i></a>
                  <a href="#" @click="isDeleteAgenciaModalOpen = true; agenciaToDelete = {nombre: 'Agencia Central'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
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
          <label for="nombre_agencia" class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold">Nombre de la agencia</label>
          <input type="text" id="nombre_agencia" name="nombre_agencia" class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm border focus:border-gray-500 dark:focus:border-white focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
        <div>
          <label for="horario_agencia" class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold">Horario</label>
          <input type="text" id="horario_agencia" name="horario_agencia" class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm border focus:border-gray-500 dark:focus:border-white focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
        <div>
          <label for="pais_agencia" class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold">País</label>
          <input type="text" id="pais_agencia" name="pais_agencia" class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm border focus:border-gray-500 dark:focus:border-white focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
        <div>
          <label for="departamento_agencia" class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold">Departamento</label>
          <input type="text" id="departamento_agencia" name="departamento_agencia" class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm border focus:border-gray-500 dark:focus:border-white focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
        <div>
          <label for="ciudad_agencia" class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold">Ciudad</label>
          <input type="text" id="ciudad_agencia" name="ciudad_agencia" class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm border focus:border-gray-500 dark:focus:border-white focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
        <div>
          <label for="direccion_agencia" class="block text-sm font-medium text-gray-700 dark:text-white nunito-bold">Dirección</label>
          <input type="text" id="direccion_agencia" name="direccion_agencia" class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm border focus:border-gray-500 dark:focus:border-white focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
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
