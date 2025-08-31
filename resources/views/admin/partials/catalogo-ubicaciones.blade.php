<div x-data="{ 
    isPaisModalOpen: false, 
    isDepartamentoModalOpen: false, 
    isCiudadModalOpen: false, 
    isDireccionModalOpen: false 
}">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 nunito-bold mb-2">Ubicaciones de Agencias</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Card Países -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
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
                <div class="mb-4">
                    <input type="text" placeholder="Buscar país..." 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent nunito-regular">
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 nunito-bold">
                            <tr>
                                <th class="px-4 py-3 text-left text-gray-700">ID</th>
                                <th class="px-4 py-3 text-left text-gray-700">Nombre</th>
                                <th class="px-4 py-3 text-left text-gray-700">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50 nunito-regular">
                                <td class="px-4 py-3 text-gray-900">1</td>
                                <td class="px-4 py-3 text-gray-900">Honduras</td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <button class="text-blue-600 hover:text-blue-800 p-1 rounded">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-800 p-1 rounded">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Card Departamentos -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
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
                <div class="mb-4">
                    <input type="text" placeholder="Buscar departamento..." 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent nunito-regular">
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 nunito-bold">
                            <tr>
                                <th class="px-4 py-3 text-left text-gray-700">ID</th>
                                <th class="px-4 py-3 text-left text-gray-700">Nombre</th>
                                <th class="px-4 py-3 text-left text-gray-700">País</th>
                                <th class="px-4 py-3 text-left text-gray-700">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50 nunito-regular">
                                <td class="px-4 py-3 text-gray-900">1</td>
                                <td class="px-4 py-3 text-gray-900">Francisco Morazán</td>
                                <td class="px-4 py-3 text-gray-600">Honduras</td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <button class="text-blue-600 hover:text-blue-800 p-1 rounded">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-800 p-1 rounded">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Card Ciudades -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
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
                <div class="mb-4">
                    <input type="text" placeholder="Buscar ciudad..." 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent nunito-regular">
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 nunito-bold">
                            <tr>
                                <th class="px-4 py-3 text-left text-gray-700">ID</th>
                                <th class="px-4 py-3 text-left text-gray-700">Nombre</th>
                                <th class="px-4 py-3 text-left text-gray-700">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50 nunito-regular">
                                <td class="px-4 py-3 text-gray-900">1</td>
                                <td class="px-4 py-3 text-gray-900">Tegucigalpa</td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <button class="text-blue-600 hover:text-blue-800 p-1 rounded">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-800 p-1 rounded">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Card Direcciones -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
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
                <div class="mb-4">
                    <input type="text" placeholder="Buscar dirección..." 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent nunito-regular">
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 nunito-bold">
                            <tr>
                                <th class="px-4 py-3 text-left text-gray-700">ID</th>
                                <th class="px-4 py-3 text-left text-gray-700">Nombre</th>
                                <th class="px-4 py-3 text-left text-gray-700">Ciudad</th>
                                <th class="px-4 py-3 text-left text-gray-700">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50 nunito-regular">
                                <td class="px-4 py-3 text-gray-900">1</td>
                                <td class="px-4 py-3 text-gray-900">Col. Centro</td>
                                <td class="px-4 py-3 text-gray-600">Tegucigalpa</td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <button class="text-blue-600 hover:text-blue-800 p-1 rounded">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-800 p-1 rounded">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
</div>
