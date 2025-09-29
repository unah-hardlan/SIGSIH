<div x-data="{
    tab: 'tipos',
    tipos: [
        {nombre: 'General', descripcion: 'Pantallas de resumen y navegación principal'},
        {nombre: 'Operativa', descripcion: 'Pantallas para gestión de procesos y tickets'},
        {nombre: 'Analítica', descripcion: 'Pantallas para reportes y análisis de datos'},
        {nombre: 'Financiera', descripcion: 'Pantallas para facturación y pagos'}
    ],
    isTipoModalOpen: false,
    isTipoEditModalOpen: false,
    isTipoDeleteModalOpen: false,
    tipoToEdit: {nombre: '', descripcion: ''},
    tipoToDelete: {nombre: '', descripcion: ''}
    , searchTipos: '', ordenarPor: ''
}">
    <div x-show="tab === 'tipos'">
        <x-admin.tabla-mobile titulo="Tipos de Objetos" class="nunito-bold bg-white dark:bg-gray-900">
            <x-slot name="filtros">
                @include('partials.filtros-generales', [
                'searchModel' => 'searchTipos',
                'ordenarOptions' => [
                'nombre' => 'Nombre',
                'descripcion' => 'Descripción'
                ]
                ])
            </x-slot>
            <x-slot name="boton">
                <div class="w-full flex justify-center sm:justify-end">
                    <button @click="isTipoModalOpen = true"
                        class="w-11/12 sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center text-sm">
                        Agregar tipo
                    </button>
                </div>
            </x-slot>

            <table class="min-w-full text-sm bg-white dark:bg-gray-900">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                        <th class="py-2 px-4 text-left dark:text-white">Nombre</th>
                        <th class="py-2 px-4 text-left dark:text-white">Descripción</th>
                        <th class="py-2 px-4 text-left dark:text-white">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="tipo in tipos" :key="tipo.nombre">
                        <tr class="nunito-regular border-b dark:border-gray-700">
                            <td class="py-2 px-4 dark:text-white" x-text="tipo.nombre"></td>
                            <td class="py-2 px-4 dark:text-white" x-text="tipo.descripcion"></td>
                            <td class="py-2 px-4 flex gap-2 dark:text-white">
                                <a href="#" @click="isTipoEditModalOpen = true; tipoToEdit = tipo"
                                    class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#" @click="isTipoDeleteModalOpen = true; tipoToDelete = tipo"
                                    class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <x-slot name="mobileTemplate">
                <div class="space-y-4">
                    <template x-for="tipo in tipos" :key="tipo.nombre">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold"
                                        x-text="tipo.nombre"></h3>
                                </div>
                            </div>
                            <div class="space-y-1 text-sm">
                                <div><span
                                        class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Descripción:</span> <span
                                        class="text-gray-900 dark:text-gray-200 nunito-regular"
                                        x-text="tipo.descripcion"></span></div>
                            </div>
                            <div
                                class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <button @click="isTipoEditModalOpen = true; tipoToEdit = tipo"
                                    class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button @click="isTipoDeleteModalOpen = true; tipoToDelete = tipo"
                                    class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </x-slot>
        </x-admin.tabla-mobile>
        <!-- Modal Agregar Tipo de Objeto -->
        <div x-show="isTipoModalOpen"
            class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
            <div class="bg-white p-6 rounded-lg shadow max-w-xs xl:max-w-2xl 2xl:max-w-3xl min-h-[300px] xl:min-h-[600px] w-full relative">
                <h2 class="text-xl font-bold mb-4 nunito-bold">Agregar Tipo de Objeto</h2>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 nunito-bold">Nombre</label>
                    <input type="text" x-model="tipoToEdit.nombre" class="w-full border rounded px-3 py-2 nunito-regular"
                        placeholder="Ej: Analítica">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                    <textarea x-model="tipoToEdit.descripcion" class="w-full border rounded px-3 py-2 nunito-regular"
                        placeholder="Describe el tipo..."></textarea>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button @click="isTipoModalOpen = false; tipoToEdit = {nombre:'', descripcion:''}"
                        class="px-4 py-2 bg-gray-200 rounded">Cancelar</button>
                    <button @click="
                    if(tipoToEdit.nombre && tipoToEdit.descripcion){
                        tipos.push({nombre: tipoToEdit.nombre, descripcion: tipoToEdit.descripcion});
                        isTipoModalOpen = false;
                        tipoToEdit = {nombre:'', descripcion:''};
                    }
                " class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Guardar Tipo</button>
                </div>
                <button @click="isTipoModalOpen = false"
                    class="absolute top-2 right-3 text-gray-500 hover:text-red-500 text-2xl leading-none">&times;</button>
            </div>
        </div>

        <!-- Modal Editar Tipo de Objeto -->
        <div x-show="isTipoEditModalOpen"
            class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
            <div class="bg-white p-6 rounded-lg shadow max-w-xs xl:max-w-2xl 2xl:max-w-3xl min-h-[300px] xl:min-h-[600px] w-full relative">
                <h2 class="text-xl font-bold mb-4 nunito-bold">Editar Tipo de Objeto</h2>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 nunito-bold">Nombre</label>
                    <input type="text" x-model="tipoToEdit.nombre" class="w-full border rounded px-3 py-2 nunito-regular">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                    <textarea x-model="tipoToEdit.descripcion" class="w-full border rounded px-3 py-2 nunito-regular"></textarea>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button @click="isTipoEditModalOpen = false" class="px-4 py-2 bg-gray-200 rounded">Cancelar</button>
                    <button @click="
                    let i = tipos.findIndex(t => t.nombre === tipoToEdit.nombre);
                    if(i !== -1){
                        tipos[i].descripcion = tipoToEdit.descripcion;
                    }
                    isTipoEditModalOpen = false;
                " class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Guardar Cambios</button>
                </div>
                <button @click="isTipoEditModalOpen = false"
                    class="absolute top-2 right-3 text-gray-500 hover:text-red-500 text-2xl leading-none">&times;</button>
            </div>
        </div>


    </div>
    <x-admin.confirmation-modal class="nunito-regular" modalName="isTipoDeleteModalOpen" itemToDelete="tipoToDelete"
        message="¿Estás seguro de que deseas eliminar este tipo de objeto?" />
</div>