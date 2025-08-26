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
        <x-admin.tabla-crud :titulo="'Tipos de Objetos'">
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
                        class="w-11/12 sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap flex items-center justify-center">
                        Agregar tipo
                    </button>
                </div>
            </x-slot>
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="py-2 px-4 text-left">Nombre</th>
                        <th class="py-2 px-4 text-left">Descripción</th>
                        <th class="py-2 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="tipo in tipos" :key="tipo.nombre">
                        <tr>
                            <td class="py-2 px-4" x-text="tipo.nombre"></td>
                            <td class="py-2 px-4" x-text="tipo.descripcion"></td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click="isTipoEditModalOpen = true; tipoToEdit = tipo"
                                    class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                                <a href="#" @click="isTipoDeleteModalOpen = true; tipoToDelete = tipo"
                                    class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </x-admin.tabla-crud>
        <!-- Modal Agregar Tipo de Objeto -->
        <div x-show="isTipoModalOpen"
            class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
            <div class="bg-white p-6 rounded-lg shadow max-w-md w-full relative">
                <h2 class="text-xl font-bold mb-4">Agregar Tipo de Objeto</h2>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Nombre</label>
                    <input type="text" x-model="tipoToEdit.nombre" class="w-full border rounded px-3 py-2"
                        placeholder="Ej: Analítica">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Descripción</label>
                    <textarea x-model="tipoToEdit.descripcion" class="w-full border rounded px-3 py-2"
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
            <div class="bg-white p-6 rounded-lg shadow max-w-md w-full relative">
                <h2 class="text-xl font-bold mb-4">Editar Tipo de Objeto</h2>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Nombre</label>
                    <input type="text" x-model="tipoToEdit.nombre" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Descripción</label>
                    <textarea x-model="tipoToEdit.descripcion" class="w-full border rounded px-3 py-2"></textarea>
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
    <x-admin.confirmation-modal modalName="isTipoDeleteModalOpen" itemToDelete="tipoToDelete"
        message="¿Estás seguro de que deseas eliminar este tipo de objeto?" />
</div>