<div x-data="{
    calificaciones: [
        { id_calificacion_servicio_pk: 1, nombre_calificacion: 'Excelente', descripcion_calificacion: 'Servicio excepcional, superó nuestras expectativas' },
        { id_calificacion_servicio_pk: 2, nombre_calificacion: 'Muy Bueno', descripcion_calificacion: 'Servicio de alta calidad, muy satisfecho' },
        { id_calificacion_servicio_pk: 3, nombre_calificacion: 'Bueno', descripcion_calificacion: 'Servicio adecuado, cumple con lo esperado' },
        { id_calificacion_servicio_pk: 4, nombre_calificacion: 'Regular', descripcion_calificacion: 'Servicio aceptable pero con área de mejora' },
        { id_calificacion_servicio_pk: 5, nombre_calificacion: 'Malo', descripcion_calificacion: 'Servicio deficiente, necesita mejoras urgentes' }
    ],
    isCalificacionModalOpen: false,
    isEditCalificacionModalOpen: false,
    isDeleteCalificacionModalOpen: false,
    calificacionToEdit: null,
    calificacionToDelete: null,
    nuevaCalificacion: { nombre_calificacion: '', descripcion_calificacion: '' },
    filtroCalificacion: ''
}">
    <div class="bg-white rounded-lg shadow p-6 mt-6 w-full">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full pb-4 mb-4 border-b">
            <h2 class="text-2xl text-gray-800 nunito-bold">Calificaciones de Servicio</h2>
            <div class="flex flex-col sm:flex-row gap-2 flex-1 md:ml-6 nunito-bold">
                <input type="text" x-model="filtroCalificacion" placeholder="Buscar calificación..."
                    class="border rounded px-3 py-2 text-sm w-full sm:w-56" />
            </div>
            <div class="flex gap-2">
                <button @click="isCalificacionModalOpen = true; nuevaCalificacion = {nombre_calificacion:'', descripcion_calificacion:''}"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap">
                    <i class="fas fa-plus mr-2"></i>Nueva Calificación
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm w-full">
                <thead class="bg-gray-100 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left">ID</th>
                        <th class="py-2 px-4 text-left">Nombre Calificación</th>
                        <th class="py-2 px-4 text-left">Descripción</th>
                        <th class="py-2 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="calificacion in calificaciones
                        .filter(c =>
                            (!filtroCalificacion || c.nombre_calificacion.toLowerCase().includes(filtroCalificacion.toLowerCase()) || c.descripcion_calificacion.toLowerCase().includes(filtroCalificacion.toLowerCase()))
                        )" :key="calificacion.id_calificacion_servicio_pk">
                        <tr class="border-b nunito-regular hover:bg-gray-50">
                            <td class="py-2 px-4" x-text="calificacion.id_calificacion_servicio_pk"></td>
                            <td class="py-2 px-4 font-medium" x-text="calificacion.nombre_calificacion"></td>
                            <td class="py-2 px-4 max-w-xs" x-text="calificacion.descripcion_calificacion" :title="calificacion.descripcion_calificacion"></td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#"
                                    @click.prevent="isEditCalificacionModalOpen = true; calificacionToEdit = Object.assign({}, calificacion)"
                                    class="text-blue-600 hover:text-blue-800" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#"
                                    @click.prevent="isDeleteCalificacionModalOpen = true; calificacionToDelete = Object.assign({}, calificacion)"
                                    class="text-red-600 hover:text-red-800" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="isCalificacionModalOpen" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
        <div class="bg-white p-6 rounded-lg shadow max-w-md w-full relative">
            <h2 class="text-xl font-bold mb-4">Agregar Calificación de Servicio</h2>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nombre de la Calificación</label>
                <input type="text" x-model="nuevaCalificacion.nombre_calificacion" class="w-full border rounded px-3 py-2"
                    placeholder="Ej: Excelente, Bueno, Regular...">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Descripción</label>
                <textarea x-model="nuevaCalificacion.descripcion_calificacion" class="w-full border rounded px-3 py-2"
                    placeholder="Descripción de la calificación..." rows="3"></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button @click="isCalificacionModalOpen = false; nuevaCalificacion = {nombre_calificacion:'', descripcion_calificacion:''}"
                    class="px-4 py-2 bg-gray-200 rounded">Cancelar</button>
                <button @click="
                    if(nuevaCalificacion.nombre_calificacion && nuevaCalificacion.descripcion_calificacion){
                        calificaciones.push({
                            id_calificacion_servicio_pk: calificaciones.length + 1,
                            nombre_calificacion: nuevaCalificacion.nombre_calificacion,
                            descripcion_calificacion: nuevaCalificacion.descripcion_calificacion
                        });
                        isCalificacionModalOpen = false;
                        nuevaCalificacion = {nombre_calificacion:'', descripcion_calificacion:''};
                    }
                " class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Guardar Calificación</button>
            </div>
            <button @click="isCalificacionModalOpen = false"
                class="absolute top-2 right-3 text-gray-500 hover:text-red-500 text-2xl leading-none">&times;</button>
        </div>
    </div>

    <div x-show="isEditCalificacionModalOpen"
        class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
        <div class="bg-white p-6 rounded-lg shadow max-w-md w-full relative">
            <h2 class="text-xl font-bold mb-4">Editar Calificación de Servicio</h2>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nombre de la Calificación</label>
                <input type="text" x-model="calificacionToEdit.nombre_calificacion" class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Descripción</label>
                <textarea x-model="calificacionToEdit.descripcion_calificacion" class="w-full border rounded px-3 py-2" rows="3"></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button @click="isEditCalificacionModalOpen = false" class="px-4 py-2 bg-gray-200 rounded">Cancelar</button>
                <button @click="
                    let i = calificaciones.findIndex(c => c.id_calificacion_servicio_pk === calificacionToEdit.id_calificacion_servicio_pk);
                    if(i !== -1){
                        calificaciones[i] = Object.assign({}, calificacionToEdit);
                    }
                    isEditCalificacionModalOpen = false;
                " class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Guardar Cambios</button>
            </div>
            <button @click="isEditCalificacionModalOpen = false"
                class="absolute top-2 right-3 text-gray-500 hover:text-red-500 text-2xl leading-none">&times;</button>
        </div>
    </div>

    <div x-show="isDeleteCalificacionModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
         @click.away="isDeleteCalificacionModalOpen = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md" @click.stop>
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="text-xl font-bold text-gray-700">Eliminar Calificación</h3>
                <button @click="isDeleteCalificacionModalOpen = false" class="text-gray-500 hover:text-gray-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mt-4">
                <p>¿Estás seguro de que deseas eliminar la calificación <strong x-text="calificacionToDelete ? calificacionToDelete.nombre_calificacion : ''"></strong>? Esta acción no se puede deshacer.</p>
            </div>
            <div class="flex justify-end pt-4">
                <button type="button" @click="isDeleteCalificacionModalOpen = false" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 mr-2">Cancelar</button>
                <button type="submit" @click="
                    if (calificacionToDelete) {
                        calificaciones = calificaciones.filter(c => c.id_calificacion_servicio_pk !== calificacionToDelete.id_calificacion_servicio_pk);
                        isDeleteCalificacionModalOpen = false;
                        calificacionToDelete = null;
                    }
                " class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Eliminar</button>
            </div>
        </div>
    </div>
</div>
