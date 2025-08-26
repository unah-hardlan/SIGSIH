<div x-data="{ open: false, nombre: '', fecha_inicio: '', fecha_fin: '', fecha_estimada: '', descripcion: '', actividades: '', orden_servicio: '', estado: 'En Proceso' }">
    <button type="button" @click="open = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap">
        Nuevo proyecto
    </button>
    <div x-show="open" x-transition class="fixed inset-0 z-50 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 min-h-screen">
        <div @click.stop class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg mx-auto max-h-[90vh] overflow-auto">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="text-xl font-bold text-gray-700">Nuevo Proyecto</h3>
                <button @click="open = false" class="text-gray-500 hover:text-gray-800"><i class="fas fa-times"></i></button>
            </div>
            <form @submit.prevent="open = false" class="mt-4 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" x-model="nombre" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha Inicial del Proyecto</label>
                        <input type="date" x-model="fecha_inicio" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha Estimada de Finalización</label>
                        <input type="date" x-model="fecha_estimada" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha de Finalización</label>
                        <input type="date" x-model="fecha_fin" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea x-model="descripcion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required></textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Actividades</label>
                        <input type="text" x-model="actividades" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Orden de Servicio</label>
                        <input type="text" x-model="orden_servicio" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estado de Proyecto</label>
                    <select x-model="estado" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option>En Proceso</option>
                        <option>Finalizado</option>
                        <option>Pendiente</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" @click="open = false" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Cancelar</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Guardar Proyecto</button>
                </div>
            </form>
        </div>
    </div>
</div>
