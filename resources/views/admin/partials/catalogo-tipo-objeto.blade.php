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
        <x-admin.form-modal modalName="isTipoModalOpen" title="Agregar Tipo de Objeto" submitLabel="Guardar Tipo" maxWidth="max-w-md">
            <div class="space-y-4">
                <div>
                    <label for="nombre_tipo" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" id="nombre_tipo" name="nombre_tipo" x-model="tipoToEdit.nombre"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent nunito-regular" placeholder="Ej: Analítica">
                </div>
                <div>
                    <label for="descripcion_tipo" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="descripcion_tipo" name="descripcion_tipo" x-model="tipoToEdit.descripcion" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent nunito-regular" placeholder="Describe el tipo..."></textarea>
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Tipo de Objeto -->
        <x-admin.edit-modal modalName="isTipoEditModalOpen" title="Editar Tipo de Objeto" itemToEdit="tipoToEdit" maxWidth="max-w-md">
            <div class="space-y-4">
                <div>
                    <label for="edit_nombre_tipo" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" id="edit_nombre_tipo" name="edit_nombre_tipo" x-model="tipoToEdit.nombre"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent nunito-regular">
                </div>
                <div>
                    <label for="edit_descripcion_tipo" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="edit_descripcion_tipo" name="edit_descripcion_tipo" x-model="tipoToEdit.descripcion" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent nunito-regular"></textarea>
                </div>
            </div>
        </x-admin.edit-modal>


    </div>
    <x-admin.confirmation-modal modalName="isTipoDeleteModalOpen" itemToDelete="tipoToDelete"
        message="¿Estás seguro de que deseas eliminar este tipo de objeto?" />
</div>