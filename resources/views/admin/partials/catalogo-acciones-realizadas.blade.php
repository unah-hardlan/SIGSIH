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
    <div class="bg-white rounded-lg shadow p-6 mt-6 w-full">
        <!-- HEADER Y FILTROS -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full pb-4 mb-4 border-b">
            <h2 class="text-2xl text-gray-800 nunito-bold">Acciones Realizadas</h2>
            <div class="flex flex-col sm:flex-row gap-2 flex-1 md:ml-6 nunito-bold">
                <input type="text" x-model="filtroAccion" placeholder="Buscar acción..."
                    class="border rounded px-3 py-2 text-sm w-full sm:w-48" />
                <select x-model="filtroTipo" class="border rounded px-1 py-2 text-sm w-full sm:w-40">
                    <option value="">Todas las acciones</option>
                    <option>Revisión</option>
                    <option>Mantenimiento</option>
                    <option>Capacitación</option>
                </select>
            </div>
            <button @click="isAccionModalOpen = true; nuevaAccion = {nombre:'', descripcion:''}"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap">
                <i class="fas fa-plus mr-2"></i>Nueva acción
            </button>
        </div>
        <!-- TABLA -->
        <table class="min-w-full text-sm w-full">
            <thead class="bg-gray-100 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left">ID Acción</th>
                    <th class="py-2 px-4 text-left">Nombre</th>
                    <th class="py-2 px-4 text-left">Descripción</th>
                    <th class="py-2 px-4 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="accion in acciones
                    .filter(a =>
                        (!filtroAccion || a.descripcion.toLowerCase().includes(filtroAccion.toLowerCase()))
                        && (!filtroTipo || a.nombre === filtroTipo)
                    )" :key="accion.id_accion">
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4" x-text="accion.id_accion"></td>
                        <td class="py-2 px-4" x-text="accion.nombre"></td>
                        <td class="py-2 px-4" x-text="accion.descripcion"></td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#"
                                @click.prevent="isEditAccionModalOpen = true; accionToEdit = Object.assign({}, accion)"
                                class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                            <a href="#"
                                @click.prevent="isDeleteAccionModalOpen = true; accionToDelete = Object.assign({}, accion)"
                                class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- MODAL AGREGAR ACCIÓN -->
    <div x-show="isAccionModalOpen" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
        <div class="bg-white p-6 rounded-lg shadow max-w-md w-full relative">
            <h2 class="text-xl font-bold mb-4">Agregar Acción Realizada</h2>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nombre</label>
                <input type="text" x-model="nuevaAccion.nombre" class="w-full border rounded px-3 py-2"
                    placeholder="Ej: Revisión">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Descripción</label>
                <textarea x-model="nuevaAccion.descripcion" class="w-full border rounded px-3 py-2"
                    placeholder="Describe la acción..."></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button @click="isAccionModalOpen = false; nuevaAccion = {nombre:'', descripcion:''}"
                    class="px-4 py-2 bg-gray-200 rounded">Cancelar</button>
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
                " class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Guardar Acción</button>
            </div>
            <button @click="isAccionModalOpen = false"
                class="absolute top-2 right-3 text-gray-500 hover:text-red-500 text-2xl leading-none">&times;</button>
        </div>
    </div>

    <!-- MODAL EDITAR ACCIÓN -->
    <div x-show="isEditAccionModalOpen"
        class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
        <div class="bg-white p-6 rounded-lg shadow max-w-md w-full relative">
            <h2 class="text-xl font-bold mb-4">Editar Acción Realizada</h2>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nombre</label>
                <input type="text" x-model="accionToEdit.nombre" class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Descripción</label>
                <textarea x-model="accionToEdit.descripcion" class="w-full border rounded px-3 py-2"></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button @click="isEditAccionModalOpen = false" class="px-4 py-2 bg-gray-200 rounded">Cancelar</button>
                <button @click="
                    let i = acciones.findIndex(a => a.id_accion === accionToEdit.id_accion);
                    if(i !== -1){
                        acciones[i].nombre = accionToEdit.nombre;
                        acciones[i].descripcion = accionToEdit.descripcion;
                    }
                    isEditAccionModalOpen = false;
                " class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Guardar Cambios</button>
            </div>
            <button @click="isEditAccionModalOpen = false"
                class="absolute top-2 right-3 text-gray-500 hover:text-red-500 text-2xl leading-none">&times;</button>
        </div>


    </div>
    <!-- MODAL ELIMINAR ACCIÓN REALIZADA -->
    <x-admin.confirmation-modal 
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