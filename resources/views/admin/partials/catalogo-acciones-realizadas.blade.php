<div x-data="{
    acciones: [
        { id_accion: 'AC-001', nombre: 'Revisión', descripcion: 'Revisión de equipos de red' },
        { id_accion: 'AC-002', nombre: 'Mantenimiento', descripcion: 'Mantenimiento general de servidores' }
    ],
    isAccionModalOpen: false,
    isEditAccionModalOpen: false,
    isDeleteAccionModalOpen: false,
    accionToEdit: { id_accion: '', nombre: '', descripcion: '' },
    accionToDelete: { id_accion: '', nombre: '', descripcion: '' },
    nuevaAccion: { nombre: '', descripcion: '' },
    filtroAccion: '',
    filtroTipo: ''
}">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6 mt-6 w-full">
        <!-- HEADER Y FILTROS -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full pb-4 mb-4 border-b">
            <h2 class="text-2xl text-gray-800 dark:text-white nunito-bold">Acciones Realizadas</h2>
            <div class="flex-1 md:ml-6">
                @include('partials.filtros-generales', [
                    'searchModel' => 'searchAccion',
                    'ordenarOptions' => [
                        'nombre' => 'Nombre',
                        'descripcion' => 'Descripción'
                    ]
                ])
            </div>
            <button @click="isAccionModalOpen = true; nuevaAccion = {nombre:'', descripcion:''}"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                <i class="fas fa-plus mr-2"></i>Nueva acción
            </button>
        </div>
        <!-- TABLA -->
        <table class="min-w-full text-sm w-full">
            <thead class="bg-gray-100 dark:bg-gray-800 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">ID Acción</th>
                    <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Nombre</th>
                    <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Descripción</th>
                    <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="accion in acciones
                    .filter(a =>
                        (!filtroAccion || a.descripcion.toLowerCase().includes(filtroAccion.toLowerCase()))
                        && (!filtroTipo || a.nombre === filtroTipo)
                    )" :key="accion.id_accion">
                    <tr class="border-b nunito-regular bg-white dark:bg-gray-900">
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white" x-text="accion.id_accion"></td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white" x-text="accion.nombre"></td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white" x-text="accion.descripcion"></td>
                        <td class="py-2 px-4 flex gap-2 nunito-regular">
                            <a href="#"
                                @click.prevent="isEditAccionModalOpen = true; accionToEdit = Object.assign({}, accion)"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-300"><i class="fas fa-edit"></i></a>
                            <a href="#"
                                @click.prevent="isDeleteAccionModalOpen = true; accionToDelete = Object.assign({}, accion)"
                                class="text-red-600 hover:text-red-800 dark:text-red-400"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- MODAL AGREGAR ACCIÓN -->
    <div x-show="isAccionModalOpen" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
    <div class="bg-white p-6 rounded-lg shadow max-w-xs xl:max-w-2xl 2xl:max-w-3xl min-h-[300px] xl:min-h-[600px] w-full relative">
            <h2 class="text-xl font-bold mb-4 nunito-bold">Agregar Acción Realizada</h2>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Nombre</label>
                <input type="text" x-model="nuevaAccion.nombre" class="w-full border rounded px-3 py-2 nunito-regular"
                    placeholder="Ej: Revisión">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                <textarea x-model="nuevaAccion.descripcion" class="w-full border rounded px-3 py-2 nunito-regular"
                    placeholder="Describe la acción..."></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button @click="isAccionModalOpen = false; nuevaAccion = {nombre:'', descripcion:''}"
                    class="px-4 py-2 bg-gray-200 rounded nunito-regular">Cancelar</button>
                <button @click="
                    if(nuevaAccion.nombre && nuevaAccion.descripcion){
                        acciones.push({
                            id_accion: 'AC-' + String(acciones.length+1).padStart(3,'0'),
                            nombre: nuevaAccion.nombre,
                            descripcion: nuevaAccion.descripcion
                        });
                        isAccionModalOpen = false;
                        nuevaAccion = {nombre:'', descripcion:''};
                    }
                " class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 nunito-regular">Guardar Acción</button>
            </div>
            <button @click="isAccionModalOpen = false"
                class="absolute top-2 right-3 text-gray-500 hover:text-red-500 text-2xl leading-none">&times;</button>
        </div>
    </div>

    <!-- MODAL EDITAR ACCIÓN -->
    <div x-show="isEditAccionModalOpen"
        class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
    <div class="bg-white p-6 rounded-lg shadow max-w-xs xl:max-w-2xl 2xl:max-w-3xl min-h-[300px] xl:min-h-[600px] w-full relative">
            <h2 class="text-xl font-bold mb-4 nunito-bold">Editar Acción Realizada</h2>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Nombre</label>
                <input type="text" x-model="accionToEdit.nombre" class="w-full border rounded px-3 py-2 nunito-regular">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                <textarea x-model="accionToEdit.descripcion" class="w-full border rounded px-3 py-2 nunito-regular"></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button @click="isEditAccionModalOpen = false" class="px-4 py-2 bg-gray-200 rounded nunito-regular">Cancelar</button>
                <button @click="
                    let i = acciones.findIndex(a => a.id_accion === accionToEdit.id_accion);
                    if(i !== -1){
                        acciones[i].nombre = accionToEdit.nombre;
                        acciones[i].descripcion = accionToEdit.descripcion;
                    }
                    isEditAccionModalOpen = false;
                " class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 nunito-regular">Guardar Cambios</button>
            </div>
            <button @click="isEditAccionModalOpen = false"
                class="absolute top-2 right-3 text-gray-500 hover:text-red-500 text-2xl leading-none">&times;</button>
        </div>


    </div>
    <!-- MODAL ELIMINAR ACCIÓN REALIZADA -->
    <x-admin.confirmation-modal class="nunito-bold"
        modalName="isDeleteAccionModalOpen"
        title="Eliminar Acción Realizada"
        :itemToDelete="'accionToDelete'"
        itemNameProperty="nombre"
        message="¿Estás seguro de que deseas eliminar la acción realizada"
    />
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('accionesRealizadas', () => ({
                ...Alpine.rawData,
                deleteAccion() {
                    if (this.accionToDelete) {
                        this.acciones = this.acciones.filter(a => a.id_accion !== this.accionToDelete.id_accion);
                        this.isDeleteAccionModalOpen = false;
                        this.accionToDelete = { id_accion: '', nombre: '', descripcion: '' };
                    }
                }
            }));
        });
    </script>

</div>