<div x-data="{ 
    isPaisModalOpen: false, 
    isDepartamentoModalOpen: false, 
    isCiudadModalOpen: false, 
    isDireccionModalOpen: false,
    isEditModalOpen: false,
    isDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    isCiudadEditModalOpen: false,
    isCiudadDeleteModalOpen: false,
    isDireccionEditModalOpen: false,
    isDireccionDeleteModalOpen: false,
    isPaisEditModalOpen: false,
    isPaisDeleteModalOpen: false,
    isDepartamentoEditModalOpen: false,
    isDepartamentoDeleteModalOpen: false,
}" @keydown.escape.window="isEditModalOpen = false; isDeleteModalOpen = false; isCiudadEditModalOpen = false; isCiudadDeleteModalOpen = false; isDireccionEditModalOpen = false; isDireccionDeleteModalOpen = false">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-2">Ubicaciones de Agencias</h1>
        <div class="flex flex-wrap gap-2 items-center mb-6">
            @include('partials.filtros-generales', [
                'searchModel' => 'filtroUbicaciones',
                'ordenarOptions' => [
                    'nombre' => 'Nombre',
                    'id' => 'ID'
                ]
            ])
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Card Países -->
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-900 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                            <i class="fas fa-globe text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white nunito-bold">Países</h2>
                            <p class="text-blue-100 text-sm nunito-regular">Gestiona los países disponibles</p>
                        </div>
                    </div>
                    <button @click="isPaisModalOpen = true" 
                        class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg nunito-bold transition flex items-center space-x-2">
                        <i class="fas fa-plus text-sm"></i>
                        <span class="text-sm">Nuevo</span>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <x-admin.tabla-mobile class="nunito-bold bg-white dark:bg-gray-900">
                        <x-slot name="filtros">
                        </x-slot>
                        <x-slot name="mobileTemplate">
                            <div class="space-y-4">
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold">Honduras</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular">ID: 1</p>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                        <button @click="isPaisEditModalOpen = true; itemToEdit = {id: 1, nombre: 'Honduras'}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <button @click="isPaisDeleteModalOpen = true; itemToDelete = {id: 1}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </x-slot>
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700 nunito-bold">
                                <tr>
                                    <th class="px-4 py-3 text-left text-gray-700 dark:text-white">ID</th>
                                    <th class="px-4 py-3 text-left text-gray-700 dark:text-white">Nombre</th>
                                    <th class="px-4 py-3 text-left text-gray-700 dark:text-white">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr class="nunito-regular">
                                    <td class="px-4 py-3 text-gray-900 dark:text-white">1</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-white">Honduras</td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <button @click="isPaisEditModalOpen = true; itemToEdit = {id: 1, nombre: 'Honduras'}" class="text-blue-500 hover:text-blue-700 p-1 rounded">
                                                <i class="fas fa-edit text-sm"></i>
                                            </button>
                                            <button @click="isPaisDeleteModalOpen = true; itemToDelete = {id: 1}" class="text-red-500 hover:text-red-700 p-1 rounded">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </x-admin.tabla-mobile>
                </div>
            </div>
        </div>

        <!-- Card Departamentos -->
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-green-700 to-green-900 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                            <i class="fas fa-map-marked-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white nunito-bold">Departamentos</h2>
                            <p class="text-green-100 text-sm nunito-regular">Gestiona los departamentos por país</p>
                        </div>
                    </div>
                    <button @click="isDepartamentoModalOpen = true" 
                        class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg nunito-bold transition flex items-center space-x-2">
                        <i class="fas fa-plus text-sm"></i>
                        <span class="text-sm">Nuevo</span>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <x-admin.tabla-mobile  class="nunito-bold bg-white dark:bg-gray-900">
                        <x-slot name="filtros">
                            <!-- Aquí puedes incluir filtros si son necesarios -->
                        </x-slot>
                        <x-slot name="mobileTemplate">
                            <div class="space-y-4">
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold">Francisco Morazán</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular">ID: 1</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular">País: Honduras</p>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                        <button @click="isDepartamentoEditModalOpen = true; itemToEdit = {id: 1, nombre: 'Francisco Morazán', pais: 'Honduras'}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <button @click="isDepartamentoDeleteModalOpen = true; itemToDelete = {id: 1}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </x-slot>
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700 nunito-bold">
                                <tr>
                                    <th class="px-4 py-3 text-left text-gray-700 dark:text-white">ID</th>
                                    <th class="px-4 py-3 text-left text-gray-700 dark:text-white">Nombre</th>
                                    <th class="px-4 py-3 text-left text-gray-700 dark:text-white">País</th>
                                    <th class="px-4 py-3 text-left text-gray-700 dark:text-white">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr class="nunito-regular">
                                    <td class="px-4 py-3 text-gray-900 dark:text-white">1</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-white">Francisco Morazán</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">Honduras</td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <button @click="isDepartamentoEditModalOpen = true; itemToEdit = {id: 1, nombre: 'Francisco Morazán', pais: 'Honduras'}" class="text-blue-500 hover:text-blue-700 p-1 rounded">
                                                <i class="fas fa-edit text-sm"></i>
                                            </button>
                                            <button @click="isDepartamentoDeleteModalOpen = true; itemToDelete = {id: 1}" class="text-red-500 hover:text-red-700 p-1 rounded">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </x-admin.tabla-mobile>
                </div>
            </div>
        </div>

        <!-- Card Ciudades -->
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-700 to-purple-900 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                            <i class="fas fa-city text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white nunito-bold">Ciudades</h2>
                            <p class="text-purple-100 text-sm nunito-regular">Gestiona las ciudades por departamento</p>
                        </div>
                    </div>
                    <button @click="isCiudadModalOpen = true" 
                        class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg nunito-bold transition flex items-center space-x-2">
                        <i class="fas fa-plus text-sm"></i>
                        <span class="text-sm">Nuevo</span>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <x-admin.tabla-mobile class="nunito-bold bg-white dark:bg-gray-900">
                        <x-slot name="filtros">
                            <!-- Aquí puedes incluir filtros si son necesarios -->
                        </x-slot>
                        <x-slot name="mobileTemplate">
                            <div class="space-y-4">
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold">Tegucigalpa</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular">ID: 1</p>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                        <button @click="isCiudadEditModalOpen = true; itemToEdit = {id: 1, nombre: 'Tegucigalpa'}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <button @click="isCiudadDeleteModalOpen = true; itemToDelete = {id: 1}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </x-slot>
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700 nunito-bold">
                                <tr>
                                    <th class="px-4 py-3 text-left text-gray-700 dark:text-white">ID</th>
                                    <th class="px-4 py-3 text-left text-gray-700 dark:text-white">Nombre</th>
                                    <th class="px-4 py-3 text-left text-gray-700 dark:text-white">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr class="nunito-regular">
                                    <td class="px-4 py-3 text-gray-900 dark:text-white">1</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-white">Tegucigalpa</td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <button class="text-blue-500 hover:text-blue-700 p-1 rounded">
                                                <i class="fas fa-edit text-sm"></i>
                                            </button>
                                            <button class="text-red-500 hover:text-red-700 p-1 rounded">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </x-admin.tabla-mobile>
                </div>
            </div>
        </div>

        <!-- Card Direcciones -->
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-orange-700 to-orange-900 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                            <i class="fas fa-map-marker-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white nunito-bold">Direcciones</h2>
                            <p class="text-orange-100 text-sm nunito-regular">Gestiona las direcciones por ciudad</p>
                        </div>
                    </div>
                    <button @click="isDireccionModalOpen = true" 
                        class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg nunito-bold transition flex items-center space-x-2">
                        <i class="fas fa-plus text-sm"></i>
                        <span class="text-sm">Nuevo</span>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <x-admin.tabla-mobile titulo="Direcciones" class="nunito-bold bg-white dark:bg-gray-900">
             <x-slot name="filtros">
                <!-- Aquí puedes incluir filtros si son necesarios -->
             </x-slot>
             <x-slot name="mobileTemplate">
               <div class="space-y-4">
                   <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                       <div class="flex justify-between items-start mb-2">
                           <div>
                               <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold">Col. Centro</h3>
                               <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular">ID: 1</p>
                               <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular">Ciudad: Tegucigalpa</p>
                           </div>
                       </div>
                       <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                          <button 
                                @click="isDireccionEditModalOpen = true; itemToEdit = {id: 1, nombre: 'Col. Centro', ciudad: 'Tegucigalpa'}" 
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                               <i class="fas fa-edit"></i> Editar
                           </button>
                           <button 
                                  @click="isDireccionDeleteModalOpen = true; itemToDelete = {id: 1}" 
                                  class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                  <i class="fas fa-trash"></i> Eliminar
                           </button>
                       </div>
                    </div>
                 </div>
               </x-slot>
                   <table class="w-full text-sm">
                       <thead class="bg-gray-50 dark:bg-gray-700 nunito-bold">
                           <tr>
                              <th class="px-4 py-3 text-left text-gray-700 dark:text-white">ID</th>
                              <th class="px-4 py-3 text-left text-gray-700 dark:text-white">Nombre</th>
                              <th class="px-4 py-3 text-left text-gray-700 dark:text-white">Ciudad</th>
                              <th class="px-4 py-3 text-left text-gray-700 dark:text-white">Acciones</th>
                           </tr>
                       </thead>
                       <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                          <tr class="nunito-regular">
                               <td class="px-4 py-3 text-gray-900 dark:text-white">1</td>
                               <td class="px-4 py-3 text-gray-900 dark:text-white">Col. Centro</td>
                               <td class="px-4 py-3 text-gray-600 dark:text-gray-300">Tegucigalpa</td>
                               <td class="px-4 py-3">
                                 <div class="flex justify-end gap-2">
                                   <button 
                                       class="text-blue-500 hover:text-blue-700 p-1 rounded">
                                       <i class="fas fa-edit text-sm"></i>
                                   </button>
                                   <button 
                                     class="text-red-500 hover:text-red-700 p-1 rounded">
                                     <i class="fas fa-trash text-sm"></i>
                                   </button>
                                </div>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                  </x-admin.tabla-mobile>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nuevo País -->
    <x-admin.form-modal class="nunito-bold"
        modalName="isPaisModalOpen" 
        title="Nuevo País" 
        submitLabel="Guardar País"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="nombre_pais" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre País</label>
                <input type="text" id="nombre_pais" name="nombre_pais" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Nuevo Departamento -->
    <x-admin.form-modal class="nunito-bold"
        modalName="isDepartamentoModalOpen" 
        title="Nuevo Departamento" 
        submitLabel="Guardar Departamento"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="nombre_departamento" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre Departamento</label>
                <input type="text" id="nombre_departamento" name="nombre_departamento" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="pais_departamento" class="block text-sm font-medium text-gray-700 nunito-bold">País</label>
                <input type="text" id="pais_departamento" name="pais_departamento" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Nueva Ciudad -->
        <!-- Modal Nueva Ciudad -->
    <x-admin.form-modal class="nunito-bold"
        modalName="isCiudadModalOpen" 
        title="Nueva Ciudad" 
        submitLabel="Guardar Ciudad"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="nombre_ciudad" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre Ciudad</label>
                <input type="text" id="nombre_ciudad" name="nombre_ciudad" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="departamento_ciudad" class="block text-sm font-medium text-gray-700 nunito-bold">Departamento</label>
                <input type="text" id="departamento_ciudad" name="departamento_ciudad" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Nueva Dirección -->
        <!-- Modal Nueva Dirección -->
    <x-admin.form-modal class="nunito-bold"
        modalName="isDireccionModalOpen" 
        title="Nueva Dirección" 
        submitLabel="Guardar Dirección"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="direccion" class="block text-sm font-medium text-gray-700 nunito-bold">Dirección</label>
                <input type="text" id="direccion" name="direccion" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="ciudad_direccion" class="block text-sm font-medium text-gray-700 nunito-bold">Ciudad</label>
                <input type="text" id="ciudad_direccion" name="ciudad_direccion" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modales -->
    <x-admin.form-modal modalName="isEditModalOpen" title="Editar País" submitLabel="Guardar Cambios">
        <div>
            <label for="nombre_pais" class="block text-sm font-medium text-gray-700">Nombre País</label>
            <input type="text" id="nombre_pais" x-model="itemToEdit.nombre" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        </div>
    </x-admin.form-modal>

    <x-admin.form-modal modalName="isDeleteModalOpen" title="Eliminar País" submitLabel="Confirmar Eliminación">
        <p>¿Estás seguro de que deseas eliminar el país <span x-text="itemToDelete.nombre"></span>?</p>
    </x-admin.form-modal>

    <!-- Modales Departamentos -->
    <x-admin.form-modal modalName="isEditModalOpen" title="Editar Departamento" submitLabel="Guardar Cambios">
        <div>
            <label for="nombre_departamento" class="block text-sm font-medium text-gray-700">Nombre Departamento</label>
            <input type="text" id="nombre_departamento" x-model="itemToEdit.nombre" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        </div>
    </x-admin.form-modal>

    <x-admin.form-modal modalName="isDeleteModalOpen" title="Eliminar Departamento" submitLabel="Confirmar Eliminación">
        <p>¿Estás seguro de que deseas eliminar el departamento <span x-text="itemToDelete.nombre"></span>?</p>
    </x-admin.form-modal>

    <!-- Modales Países -->
    <x-admin.edit-modal class="nunito-bold" modalName="isPaisEditModalOpen" title="Editar País" itemToEdit="itemToEdit" maxWidth="max-w-2xl">
        <div>
            <label for="edit_nombre_pais" class="block text-sm font-medium text-gray-700">Nombre País</label>
            <input type="text" id="edit_nombre_pais" name="edit_nombre_pais" :value="itemToEdit?.nombre" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-regular" modalName="isPaisDeleteModalOpen" itemToDelete="itemToDelete" message="¿Estás seguro de que quieres eliminar este país?" />

    <!-- Modales Departamentos -->
    <x-admin.edit-modal class="nunito-bold" modalName="isDepartamentoEditModalOpen" title="Editar Departamento" itemToEdit="itemToEdit" maxWidth="max-w-2xl">
        <div>
            <label for="edit_nombre_departamento" class="block text-sm font-medium text-gray-700">Nombre Departamento</label>
            <input type="text" id="edit_nombre_departamento" name="edit_nombre_departamento" :value="itemToEdit?.nombre" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-regular" modalName="isDepartamentoDeleteModalOpen" itemToDelete="itemToDelete" message="¿Estás seguro de que quieres eliminar este departamento?" />

    <!-- Modales Ciudades -->
    <x-admin.edit-modal class="nunito-bold" modalName="isCiudadEditModalOpen" title="Editar Ciudad" itemToEdit="itemToEdit" maxWidth="max-w-2xl">
        <div>
            <label for="edit_nombre_ciudad" class="block text-sm font-medium text-gray-700">Nombre Ciudad</label>
            <input type="text" id="edit_nombre_ciudad" name="edit_nombre_ciudad" :value="itemToEdit?.nombre" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-regular" modalName="isCiudadDeleteModalOpen" itemToDelete="itemToDelete" message="¿Estás seguro de que quieres eliminar esta ciudad?" />

    <!-- Modales Direcciones -->
    <x-admin.edit-modal class="nunito-bold" modalName="isDireccionEditModalOpen" title="Editar Dirección" itemToEdit="itemToEdit" maxWidth="max-w-2xl">
        <div>
            <label for="edit_direccion" class="block text-sm font-medium text-gray-700">Dirección</label>
            <input type="text" id="edit_direccion" name="edit_direccion" :value="itemToEdit?.nombre" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-regular" modalName="isDireccionDeleteModalOpen" itemToDelete="itemToDelete" message="¿Estás seguro de que quieres eliminar esta dirección?" />
</div>
