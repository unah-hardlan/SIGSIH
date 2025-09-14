<div x-data="{ isAgenciaModalOpen: false, isDeleteAgenciaModalOpen: false, agenciaToEdit: null, agenciaToDelete: null }">
  <div class="w-full">
    <div class="overflow-x-auto w-full">
      <x-admin.tabla-mobile titulo="Agencias">
        <x-slot name="filtros">
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
        </x-slot>
        <x-slot name="boton">
          <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <button @click="isAgenciaModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nueva agencia</button>
            <a href="/admin/reportes-header?modulo=Agencias&fecha={{ now()->format('d-M-Y') }}" target="_blank"
               class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 text-sm">
                <i class="fas fa-file-alt"></i> Generar Reporte
            </a>
          </div>
        </x-slot>
        <table class="min-w-full text-sm w-full">
          <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
            <tr>
              <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Nombre</th>
              <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Horario</th>
              <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Dirección</th>
              <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Ciudad</th>
              <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Departamento</th>
              <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">País</th>
              <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr class="border-b dark:border-gray-600 nunito-regular">
              <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">Agencia Central</td>
              <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">Lunes a Viernes, 8am - 5pm</td>
              <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">Col. Centro</td>
              <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">Tegucigalpa</td>
              <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">Francisco Morazán</td>
              <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">Honduras</td>
              <td class="py-2 px-4 flex gap-2">
                <a href="#" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"><i class="fas fa-eye"></i></a>
                <a href="#" @click="isDeleteAgenciaModalOpen = true; agenciaToDelete = {nombre: 'Agencia Central'}" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"><i class="fas fa-trash"></i></a>
              </td>
            </tr>
          </tbody>
        </table>
        <x-slot name="mobileTemplate">
          <div class="space-y-4 max-w-sm mx-auto">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
              <div class="flex flex-col gap-2">
                <div class="text-base nunito-bold text-gray-800 dark:text-white">Agencia Central</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Lunes a Viernes, 8am - 5pm</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Col. Centro</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Tegucigalpa, Francisco Morazán, Honduras</div>
              </div>
              <div class="flex justify-end gap-3 mt-3 text-sm">
                <button class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"><i class="fas fa-eye"></i></button>
                <button @click="isDeleteAgenciaModalOpen = true; agenciaToDelete = {nombre: 'Agencia Central'}" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"><i class="fas fa-trash"></i></button>
              </div>
            </div>
          </div>
        </x-slot>
      </x-admin.tabla-mobile>
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
